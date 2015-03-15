
<script>
    app.controller("<?= $class ?>MenuTree", ["$scope", "$compile", "$http", "$location", "$timeout", "$templateCache", function ($scope, $compile, $http, $location, $timeout, $templateCache) {
            $scope.list = <?= json_encode($list); ?>;
            $scope.active = null;
            $scope.sections = <?= json_encode($sections); ?>;
            $scope.selecting = false;
            $scope.targetSection = null;
            $scope.targetHTML = '';

            /******************* CONTEXT MENU SECTION ********************/
            $scope.contextMenu = [];
            $scope.contextMenuActive = null;
            $scope.contextMenuDisabled = false;
            $scope.contextMenuVisibleCount = 0;
            $scope.originalContextMenu = null;

            $scope.executeMenu = function (func, item, e) {
                if (typeof func == "function") {
                    $timeout(function () {
                        func($scope.contextMenuActive, e);
                    });
                }
            }
            $scope.processContextMenu = function (item, menu, orig, menuParent) {
                if (typeof orig == "object") {
                    menu.$parent = parent;

                    switch (typeof orig.label) {
                        case "string":
                            menu.label = orig.label;
                            break;
                        case "function":
                            menu.label = orig.label(item, orig);
                            break;
                    }

                    switch (typeof orig.visible) {
                        case "boolean":
                            menu.visible = orig.visible;
                            break;
                        case "function":
                            menu.visible = orig.visible(item, orig);
                            break;
                        default:
                            menu.visible = true;
                            break;
                    }
                }
                return false;
            };

            $scope.recurseContextMenu = function (item, menus, orig) {
                for (i in menus) {
                    $scope.processContextMenu(item, menus[i], orig[i], menus);

                    if (menus[i].visible) {
                        $scope.contextMenuVisibleCount++;
                    }
                }
            };

            $scope.UpdateQueryString = function (key, value, url) {
                if (!url)
                    url = window.location.href;
                var re = new RegExp("([?&])" + key + "=.*?(&|#|$)(.*)", "gi"),
                        hash;

                if (re.test(url)) {
                    if (typeof value !== 'undefined' && value !== null)
                        return url.replace(re, '$1' + key + "=" + value + '$2$3');
                    else {
                        hash = url.split('#');
                        url = hash[0].replace(re, '$1$3').replace(/(&|\?)$/, '');
                        if (typeof hash[1] !== 'undefined' && hash[1] !== null)
                            url += '#' + hash[1];
                        return url;
                    }
                }
                else {
                    if (typeof value !== 'undefined' && value !== null) {
                        var separator = url.indexOf('?') !== -1 ? '&' : '?';
                        hash = url.split('#');
                        url = hash[0] + separator + key + '=' + value;
                        if (typeof hash[1] !== 'undefined' && hash[1] !== null)
                            url += '#' + hash[1];
                        return url;
                    }
                    else
                        return url;
                }
            };

            $scope.select = function (item, e) {
                this.toggle();
                item.state = '';
                $scope.selecting = true;
                $scope.active = item;

                if (!!$scope.sections[item.target] && !!e) {
                    e.preventDefault();
                    e.stopPropagation();
                    if (!!item.url && !!item.target) {
                        var url = $scope.UpdateQueryString('render_section', item.target, item.url);
                        $http.get(url).success(function (data) {
                            var html = $(data).find('#' + item.target + ':eq(0)').html();
                            var controller = angular.element("#" + item.target + ':eq(0) [ng-controller]:eq(0)');
                            var scope = controller.scope();
                            angular.element("#" + item.target + ':eq(0)').html(html);
                            $compile("#" + item.target + ':eq(0)  > div')(scope);
//                            
                            history.pushState(null,'',item.url);
                        });
                    }
                }
            };
            $scope.openContextMenu = function (item, e, itemTree) {
                if ($scope.originalContextMenu == null) {
                    $scope.originalContextMenu = angular.copy($scope.contextMenu);
                }

                item.$tree = itemTree;
                if (itemTree.$parentNodeScope != null) {
                    item.$parent = itemTree.$parentNodeScope.$modelValue;
                }

                // mark visible menu
                $scope.contextMenuVisibleCount = 0;
                $scope.recurseContextMenu(item, $scope.contextMenu, $scope.originalContextMenu);
                $scope.contextMenuDisabled = ($scope.contextMenuVisibleCount == 0);

                // reset item state, collapsed or expanded ('' means expanded)
                item.state = '';

                // set menu as active
                $(".menu-sel").removeClass("active").removeClass(".menu-sel");
                $(e.target).parent().addClass("menu-sel active");
                $scope.contextMenuActive = item;
            };

            /******************* MENU TREE SECTION ********************/
            $scope.getUrl = function (item) {
                return item.url || '#';
            };
            $scope.getTarget = function (item) {
                if (!!$scope.sections[item.target]) {
                    return '_self';
                }
                return item.target || '_self';
            };
            $scope.iconAvailable = function (item) {
                if (typeof item.icon == "undefined")
                    return false;
                else
                    return (item.icon != '');
            };
            $scope.isSelected = function (item) {
                return angular.equals(item, $scope.active) ? 'active' : '';
            };


            /******************* INLINEJS SECTION ********************/
<?= $inlineJS ?>


        }
    ]);
</script>