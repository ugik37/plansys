<?php

class FormsController extends Controller {

    public $countRenderID = 1;
    public static $modelField = array();
    public static $modelFieldList = array(); // list of all fields in current model
    public static $relFieldList = array();

    public static function setModelFieldList($data, $type = "AR", $class = "") {
        if (count(FormsController::$modelFieldList) == 0) {
            if ($type == "AR") {
                FormsController::$modelFieldList = $data;
                $rel = isset($data['Relations']) ? $data['Relations'] : array();

                FormsController::$relFieldList = array_merge(array(
                    ''             => '-- None --',
                    '---'          => '---',
                    'currentModel' => 'Current Model',
                    '--'           => '---',
                        ), $rel);
            } else {
                foreach ($data as $name => $field) {
                    FormsController::$modelFieldList[$name] = $name;
                }
                unset(FormsController::$modelFieldList['type']);
            }
        }
    }

    public function renderPropertiesForm($field) {
        FormField::$inEditor = false;
        $fbp = FormBuilder::load($field['type']);
        return $fbp->render($field, array(
                    'wrapForm'          => false,
                    'FormFieldRenderID' => $this->countRenderID++
        ));
    }

    public function actionRenderProperties($class = null) {
        if ($class == null) {
            return true;
        }

        $a = new $class;
        $field = $a->attributes;
        $field['name'] = $class::$toolbarName;
        if (isset($array['label'])) {
            $field['label'] = $class::$toolbarName;
        }
        echo $this->renderPropertiesForm($field);
    }

    public function actionRenderTemplate($class = null) {
        if ($class == null) {
            return true;
        }

        echo $class::template();
    }

    public function actionRenderBuilder($class, $layout) {
        $postdata = file_get_contents("php://input");
        $post = CJSON::decode($postdata);
        if (isset($post['form'])) {
            $form = $post['form'];
        } else {
            $fb = FormBuilder::load($class);
            $form = $fb->form;
        }

        $builder = $this->renderPartial('form_builder', array(), true);
        $mainFormSection = Layout::getMainFormSection($form['layout']['data']);
        $data = $form['layout']['data'];
        if ($layout != $form['layout']['name']) {
            unset($data[$mainFormSection]);
            $mainFormSection = Layout::defaultSection($layout);
        }

        $data['editor'] = true;
        $data[$mainFormSection]['content'] = $builder;

        Layout::render($layout, $data);
    }

    public function actionRenderHiddenField() {
        $this->renderPartial('form_fields_hidden');
    }

    public function renderAllToolbar($formType) {
        FormField::$inEditor = false;

        $toolbarData = Yii::app()->cache->get('toolbarData');
        if (!$toolbarData) {
            $toolbarData = FormField::allSorted();
            Yii::app()->cache->set('toolbarData', $toolbarData, 0);
        }
        foreach ($toolbarData as $k => $f) {
            $ff = new $f['type'];
            $scripts = array_merge($ff->renderScript(), $ff->renderPropertiesScript());

            foreach ($scripts as $script) {
                $ext = Helper::explodeLast(".", $script);
                if ($ext == "js") {
                    Yii::app()->clientScript->registerScriptFile($script, CClientScript::POS_END);
                } else if ($ext == "css") {
                    Yii::app()->clientScript->registerCSSFile($script, CClientScript::POS_BEGIN);
                }
            }
        }

        FormField::$inEditor = true;

        return array(
            'data' => $toolbarData
        );
    }

    public function actionFormList($m = '') {
        $list = FormBuilder::listFile();
        $return = [];
        if ($m == '') {
            foreach ($list as $k => $l) {
                array_push($return, [
                    'module' => $l['module'],
                    'count'  => $l['count'],
                    'items'  => [
                        [
                            'name'  => 'Loading...',
                            'items' => []
                        ]
                    ]
                ]);
            }
        } else {
            foreach ($list as $k => $l) {
                if ($m == $l['module']) {
                    $return = $l['items'];
                }
            }
        }

        echo json_encode($return);
    }

    public function actionIndex() {
        $this->render('index', array(
            'forms' => array()
        ));
    }

    public function actionEmpty() {
        $this->layout = "//layouts/blank";
        $this->render('empty');
    }

    public function actionDashboard($f) {
        $postdata = file_get_contents("php://input");
        $post = CJSON::decode($postdata);
        FormField::$inEditor = true;

        $classPath = FormBuilder::classPath($f);
        $fb = FormBuilder::load($classPath);
        if (isset($post['save'])) {
            switch ($post['save']) {
                case "portlet":
                    $fields = $fb->updateField(['name' => $post['name']], $post['data']);
                    $fb->fields = $fields;
                    break;
            }

            return;
        }

        $this->renderForm($f, [
            'classPath' => $classPath,
            'fields'    => $fb->fields
        ]);
    }

    public function actionSave($class, $timestamp) {
        FormField::$inEditor = true;

        $class = FormBuilder::classPath($class);
        $session = Yii::app()->session['FormBuilder_' . $class];
        $file = file(Yii::getPathOfAlias($class) . ".php", FILE_IGNORE_NEW_LINES);

        $changed = false;

        if ($timestamp != @$session['timestamp']) {
            $changed = true;
        }

        if (!$changed) {
            foreach ($file as $k => $f) {
                if (trim($file[$k]) != trim(@$session['file'][$k])) {
                    $changed = true;
                }
            }
        }

        if (!$changed) {
            $postdata = file_get_contents("php://input");
            $post = CJSON::decode($postdata);
            $session = Yii::app()->session['FormBuilder_' . $class];
            $fb = FormBuilder::load($class);

            if (isset($post['fields'])) {
                if (is_subclass_of($fb->model, 'FormField')) {
                    Yii::app()->cache->delete('toolbarData');
                    Yii::app()->cache->delete('toolbarHtml');
                }

                //save posted fields
                if (!$fb->setFields($post['fields'])) {
                    echo "FAILED: PERMISSION DENIED";
                }
            } else if (isset($post['form'])) {
                if (is_array($post['form']['layout']['data'])) {

                    ## save menutree to menutree file
                    foreach ($post['form']['layout']['data'] as $d) {
                        if (@$d['type'] == "menu" && !!@$d['file']) {
                            $menuOptions = MenuTree::getOptions($d['file']);
                            $menuOptions['layout'] = $d;
                            MenuTree::writeOptions($d['file'], $menuOptions);
                        }
                    }
                }

                //save posted form
                if (!$fb->setForm($post['form'])) {
                    echo "FAILED: PERMISSION DENIED";
                }
            }
        } else {
            echo "FAILED";
        }
    }

    public function actionUpdate($class) {
        FormField::$inEditor = true;
        $class = FormBuilder::classPath($class);
        $this->layout = "//layouts/blank";

        ## reset form builder session
        FormBuilder::resetSession($class);

        ## load form builder class and session
        $fb = FormBuilder::load($class);
        $fb->resetTimestamp();

        $classPath = $class;
        $class = Helper::explodeLast(".", $class);

        if (is_subclass_of($fb->model, 'ActiveRecord')) {
            $formType = "ActiveRecord";
            FormsController::setModelFieldList($class::model()->attributesList, "AR", $class);
        } else if (is_subclass_of($fb->model, 'FormField')) {
            $formType = "FormField";
            $mf = new $class;
            FormsController::setModelFieldList($mf->attributes, "FF");
        } else if (is_subclass_of($fb->model, 'Form')) {
            $formType = "Form";
            $mf = new $class;
            FormsController::setModelFieldList($mf->attributes, "FF");
        }

        $fieldData = $fb->fields;
        FormsController::$modelField = $fieldData;
        $toolbar = $this->renderAllToolbar($formType);
        Yii::import('application.modules.' . $fb->module . '.controllers.*');


        echo $this->render('form', array(
            'fb'          => $fb,
            'class'       => $class,
            'classPath'   => $classPath,
            'formType'    => $formType,
            'toolbarData' => @$toolbar['data'],
            'fieldData'   => $fieldData,
                ), true);
    }

}
