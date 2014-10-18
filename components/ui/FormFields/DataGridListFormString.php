<?php

class DataGridListFormString extends Form {

    public $inputMask = '';

    public function getForm() {
        return array(
            'title' => 'DataGridListFormString',
            'layout' => array(
                'name' => 'full-width',
                'data' => array(
                    'col1' => array(
                        'type' => 'mainform',
                    ),
                ),
            ),
        );
    }

    public function getFields() {
        return array (
            array (
                'label' => 'Input Mask',
                'name' => 'inputMask',
                'options' => array (
                    'ng-change' => 'updateListView();',
                    'ng-model' => 'item.inputMask',
                ),
                'labelOptions' => array (
                    'style' => 'text-align:left;',
                ),
                'list' => array (
                    '' => '-- NONE --',
                    '--' => '---',
                    '99/99/9999 99:99' => 'Date Time',
                    '99/99/9999' => 'Date',
                    '99:99' => 'Time',
                ),
                'labelWidth' => '3',
                'fieldWidth' => '9',
                'otherLabel' => 'Custom',
                'type' => 'DropDownList',
            ),
        );
    }

}