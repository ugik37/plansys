<?php
if (count(@$renderParams['errors']) > 0) {
    if (isset($data['errors'])) {
        $data['errors'] = array_merge($data['errors'], $renderParams['errors']);
    } else {
        $data['errors'] = $renderParams['errors'];
    }
}
ob_start();
?>

<script type="text/javascript">
<?php ob_start(); ?>
    app.controller("<?= $modelClass ?>Controller", function ($scope, $parse, $timeout, $http, $localStorage) {
        $scope.form = <?php echo json_encode($this->form); ?>;
        $scope.model = <?php echo @json_encode($data['data']); ?>;
        $scope.errors = <?php echo @json_encode($data['errors']); ?>;
        $scope.params = <?php echo @json_encode($renderParams); ?>;
        $scope.pageUrl = "<?php echo @Yii::app()->request->url; ?>";
        $scope.pageInfo = <?php echo json_encode(AuditTrail::getPathInfo()) ?>;
        $scope.formClass = "<?php echo $modelClass; ?>";
        $scope.modelBaseClass = "<?php echo ActiveRecord::baseClass($this->model); ?>";

        // initialize pageSetting
        $timeout(function () {
            var $storage = $localStorage;
            $storage.pageSetting = {} || $storage.pageSetting;
            $storage.pageSetting[$scope.pageInfo.pathinfo] = {} || $storage.pageSetting[$scope.pageInfo.pathinfo];
            $scope.pageSetting = $storage.pageSetting[$scope.pageInfo.pathinfo];
        });

        // audit trail tracker
        $timeout(function () {
            // send current page title with id to tracker
            $scope.pageInfo['description'] = $scope.form.title
            $scope.pageInfo['form_class'] = $scope.formClass;
            $scope.pageInfo['model_class'] = $scope.modelBaseClass;
            if ($("[ps-action-bar] .action-bar .title").text() != '') {
                $scope.pageInfo['description'] = $("[ps-action-bar] .action-bar .title").text();

                if (!!$scope.model && !!$scope.model.id) {
                    $scope.pageInfo['description'] += " [#" + $scope.model.id + "]";
                }
            }
            // track create or update in audit trail 
            $http.post(Yii.app.createUrl('/sys/auditTrail/track'), $scope.pageInfo);
        }, 1000);

        $scope.rel = {};

<?php if (!Yii::app()->user->isGuest): ?>
            $scope.user = <?php echo @json_encode(Yii::app()->user->info); ?>;
            if ($scope.user != null) {
                $scope.user.role = [];
                for (i in $scope.user.roles) {
                    $scope.user.role.push($scope.user.roles[i]['role_name']);
                }

                $scope.user.isRole = function (role) {
                    for (i in $scope.user.role) {
                        var r = $scope.user.role[i];
                        if (r.indexOf(role + ".") == 0) {
                            return true;
                        }
                    }

                    for (i in $scope.user.role) {
                        var r = $scope.user.role[i];
                        if (r.indexOf(role + ".") == 0) {
                            return true;
                        }
                    }

                    return $scope.user.role.indexOf(role) >= 0;
                }
            }
<?php endif; ?>

<?php if (is_object(Yii::app()->controller) && is_object(Yii::app()->controller->module)): ?>
            $scope.module = '<?= Yii::app()->controller->module->id ?>';
<?php endif; ?>

<?php if (Yii::app()->user->hasFlash('info')): ?>
            $scope.flash = '<?= Yii::app()->user->getFlash('info'); ?>';
<?php endif; ?>

<?php if (isset($data['validators'])): ?>
            $scope.validators = <?php echo @json_encode($data['validators']); ?>;
<?php endif; ?>

<?php if (is_subclass_of($this->model, 'ActiveRecord') && isset($data['isNewRecord'])): ?>
            $scope.isNewRecord = <?php echo $data['isNewRecord'] ? "true" : "false" ?>;
<?php endif; ?>

        document.title = $scope.form.title;
        $scope.$watch('form.title', function () {
            document.title = $scope.form.title;
        });
        $scope.formSubmitting = false;
        $scope.startLoading = function () {

            $scope.formSubmitting = true;
        }
        $scope.form.submit = function (button) {
            function submit() {
                if (typeof button != "undefined") {
                    var baseurl = button.url;
                    if (typeof button.url != 'string' || button.url.trim() == '' || button.url.trim() == '#') {
                        baseurl = '<?= Yii::app()->urlManager->parseUrl(Yii::app()->request) ?>';
                    }

                    var parseParams = $parse(button.urlparams);
                    var urlParams = angular.extend($scope.params, parseParams($scope));
                    var url = Yii.app.createUrl(baseurl, urlParams);
                    $("div[ng-controller=<?= $modelClass ?>Controller] form").attr('action', url).submit();
                }
            }

            if (!!$scope.model) {
                // track create or update in audit trail 
                $scope.formSubmitting = true;
                if (!!$scope.model) {
                    $scope.pageInfo['data'] = JSON.stringify($scope.model);
                    $scope.pageInfo['form_class'] = $scope.formClass;
                    $scope.pageInfo['model_class'] = $scope.modelBaseClass;
                    $scope.pageInfo['model_id'] = $scope.model.id;
                }

                // send tracking information
                $http.post(Yii.app.createUrl('/sys/auditTrail/track', {
                    t: $scope.isNewRecord ? 'create' : 'update'
                }), $scope.pageInfo).success(function () {
                    submit();
                });
            } else {
                submit();
            }
        };
        $("div[ng-controller=<?= $modelClass ?>Controller] form").submit(function (e) {
            if ($scope.uploading.length > 0) {
                alert("Mohon tunggu sampai proses file upload selesai.");
                e.preventDefault();
                e.stopPropagation();
                return false;
            }

            $scope.form.submit();
        });

        $scope.form.canGoBack = function () {
            return (document.referrer == "" || window.history.length > 1);
        }

        $scope.form.goBack = function () {
            window.history.back();
        }

        $scope.uploading = [];
        $scope.dataGrids = {length: 0};
        $("[ng-controller=<?= $modelClass ?>Controller] .data-grid-loader").each(function () {
            $scope.dataGrids[$(this).attr('name')] = false;
            $scope.dataGrids.length++;
        });
        function inlineJS() {
            $("div[ng-controller=<?= $modelClass ?>Controller]").css('opacity', 1);
<?= $inlineJS; ?>
        }

        // execute inline JS
        $timeout(function () {
            // make sure datagrids is loaded before executing inlinejs
            if ($scope.dataGrids.length > 0) {
                var dgWatch = $scope.$watch('dataGrids.length', function (n) {
                    if (n == 0) {
                        dgWatch();
                        inlineJS();
                    }
                }, true);
            } else {
                inlineJS();
            }
        }, 0);
    });
<?php $script = ob_get_clean(); ?>
</script>
<?php
ob_get_clean();

return $script;
