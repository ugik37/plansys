
app.directive('uploadFile', function ($timeout, $upload, $http) {
    return {
        require: '?ngModel',
        scope: true,
        compile: function (element, attrs, transclude) {
            if (attrs.ngModel && !attrs.ngDelay) {
                attrs.$set('ngModel', '$parent.' + attrs.ngModel, false);
            }

            return function ($scope, $el, attrs, ctrl) {
                $scope.file = null;
                $scope.loading = false;
                $scope.progress = -1;
                $scope.errors = [];
                $scope.json;

                //default value
                $scope.name = $el.find("data[name=name]").html().trim();
                $scope.classAlias = $el.find("data[name=class_alias]").html().trim();
                $scope.value = $el.find("data[name=value]").html().trim();
                $scope.mode = $el.find("data[name=mode]").html().trim();
                $scope.allowDelete = $el.find("data[name=allow_delete]").html().trim();
                $scope.allowOverwrite = $el.find("data[name=allow_overwrite]").html().trim();
                $scope.fileType = $el.find("data[name=file_type]").html().trim();

                $scope.afterUpload = function () {
                };

                // when ng-model is changed from outside directive
                if (typeof ctrl != 'undefined') {
                    ctrl.$render = function () {
                        if (typeof ctrl.$viewValue != "undefined") {
                            if (ctrl.$viewValue != null && ctrl.$viewValue != '') {

                                var data = ctrl.$viewValue;
                                var fileName = data.split('/').pop();
                                $scope.file = {
                                    'name': fileName
                                };
                            }
                        }
                    };
                }

                //Saving file description to JSON
                $scope.saveDesc = function (desc) {
                    $scope.fileDescLoadText = '...';
                    $http({
                        'method': 'post',
                        'url': Yii.app.createUrl('/formfield/UploadFile.description'),
                        'data': {
                            'desc': desc,
                            'name': $scope.file.name
//                            'path': $scope.encode($scope.fileDir)
                        }
                    }).success(function () {
                        $scope.fileDescLoadText = '';
                    });
                };

                //Upload Funcs start
                $scope.onFileSelect = function ($files) {
                    for (var i = 0; i < $files.length; i++) {
                        var file = $files[i];
                        var type = null;
                        var ext = $scope.ext(file);
                        if ($scope.fileType === "" || $scope.fileType === null) {
                            $scope.upload(file);
                        } else {
                            type = $scope.fileType.split(',');
                            for (var i = 0; i < type.length; i++)
                                type[i] = type[i].trim();

                            if ($.inArray(ext, type) > -1) {
                                $scope.upload(file);
                            } else {
                                $scope.errors.push("Tipe file tidak diijinkan, File yang diijinkan adalah " + $scope.fileType);
                            }
                        }
                    }
                };

                $scope.getFile = function (callback) {
                    if (typeof callback == "function") {
                        var url =  Yii.app.createUrl('/formfield/UploadFile.download', {
                            f: $scope.file.downloadPath,
                            n: $scope.file.name,
                            d: 'direct'
                        });
                        $http.get(url).success(function(data) {
                            callback(data);
                        });
                    }
                }

                $scope.upload = function (file) {
                    $scope.errors = [];
                    $scope.loading = true;
                    $scope.progress = 0;
                    $scope.$parent.uploading.push($scope.name);

                    $upload.upload({
                        url: Yii.app.createUrl('/formfield/UploadFile.upload', {
                            class: $scope.classAlias,
                            name: $scope.name
                        }),
                        file: file
                    }).progress(function (evt) {
                        $scope.progress = parseInt(100.0 * evt.loaded / evt.total);
                    }).success(function (data, html) {
                        $scope.progress = 101;

                        if (data.success == 'Yes') {
                            $scope.value = data.path;
                            $scope.file = {
                                'name': data.name,
                                'downloadPath': data.downloadPath
                            };

                            $scope.icon($scope.file);
                        } else {
                            alert("Error Uploading File. \n");
                        }

                        $scope.loading = false;
                        $scope.progress = -1;

                        var index = $scope.$parent.uploading.indexOf($scope.name);
                        if (index > -1) {
                            $scope.$parent.uploading.splice(index, 1);
                        }

                        if (data.success == 'Yes') {
                            $timeout(function () {
                                $scope.afterUpload($scope);
                            }, 100);
                        }

                    }).error(function (data) {
                        $scope.progress = -1;
                        $scope.loading = false;
                        var index = $scope.$parent.uploading.indexOf($scope.name);
                        if (index > -1) {
                            $scope.$parent.uploading.splice(index, 1);
                        }
                        alert(data);

                    });
                };
                //Upload Funcs end

                //Remove Func
                $scope.remove = function (file) {
                    $scope.loading = true;
                    $scope.errors = [];
                    var request = $http({
                        method: "post",
                        url: Yii.app.createUrl('/formfield/UploadFile.remove'),
                        data: {file: file}
                    });
                    request.success(function (html) {
                        $scope.file = null;
                        $scope.loading = false;
                    });

                };

                //Get the file extension
                $scope.ext = function (file) {
                    var type = file.name.split('.');
                    if (type.length === 1 || (type[0] === "" && type.length === 2)) {
                        return "";
                    }
                    return type.pop();
                }

                //Create icon based on extension 
                $scope.icon = function (file) {
                    var type = $scope.ext(file);

                    var code = ['php', 'js', 'html', 'json'];
                    var archive = ['zip'];
                    var image = ['jpg', 'jpeg', 'png', 'bmp'];
                    var audio = ['mp3'];
                    var video = ['avi'];
                    var word = ['doc', 'docx'];
                    var text = ['txt'];
                    var excel = ['xls', 'xlsx'];
                    var ppt = ['ppt', 'pptx'];
                    var pdf = ['pdf'];

                    if ($.inArray(type, image) > -1) {
                        $scope.file.type = "fa-file-image-o";
                    } else if ($.inArray(type, code) > -1) {
                        $scope.file.type = "fa-file-code-o";
                    } else if ($.inArray(type, archive) > -1) {
                        $scope.file.type = "fa-file-archive-o";
                    } else if ($.inArray(type, audio) > -1) {
                        $scope.file.type = "fa-file-audio-o";
                    } else if ($.inArray(type, video) > -1) {
                        $scope.file.type = "fa-file-movie-o";
                    } else if ($.inArray(type, word) > -1) {
                        $scope.file.type = "fa-file-word-o";
                    } else if ($.inArray(type, text) > -1) {
                        $scope.file.type = "fa-file-text-o";
                    } else if ($.inArray(type, excel) > -1) {
                        $scope.file.type = "fa-file-excel-o";
                    } else if ($.inArray(type, ppt) > -1) {
                        $scope.file.type = "fa-file-powerpoint-o";
                    } else if ($.inArray(type, pdf) > -1) {
                        $scope.file.type = "fa-file-pdf-o";
                    } else {
                        $scope.file.type = "fa-file-o";
                    }
                };

                //check if file is defined from outside
                if ($scope.value != "") {
                    var request = $http({
                        method: "post",
                        url: Yii.app.createUrl('/formfield/UploadFile.checkFile'),
                        data: {
                            file: $scope.value
                        }
                    });
                    request.success(function (result) {
                        if (result.status === 'exist') {
                            $scope.file.downloadPath = result.downloadPath;
                            $scope.icon($scope.file);
                        } else {
                            $scope.file = null;
                        }
                    }
                    );
                } else {
                    $scope.file = null;
                }

                $scope.$parent[$scope.name] = $scope;
            };
        }
    };
});