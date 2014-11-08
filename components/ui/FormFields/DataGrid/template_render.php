<div ps-data-grid id="<?= $this->name ?>" class="data-grid">

    <data name="datasource" class="hide"><?= $this->datasource; ?></data>
    <data name="name" class="hide"><?= $this->name; ?></data>
    <data name="columns" class="hide"><?= json_encode($this->columns); ?></data>
    <data name="model_class" class="hide"><?= Helper::getAlias($model) ?></data>
    <data name="grid_options" class="hide"><?= json_encode($this->gridOptions); ?></data>

    <div ng-if="!loaded" class="list-view-loading">
        <i class="fa fa-link"></i>
        Loading DataGrid...
    </div>
    <div ng-if="!datasource && loaded" class="list-view-loading">
        <i class="fa fa-warning"></i>
        {{name}}: Please choose Data Source in Form Builder
    </div>
    <div class="data-grid-container" ng-if="loaded">
        <script type="text/ng-template" id="category_header"><?php include('category_header.php'); ?></script>
        <div ng-if="datasource.data.length != 0 || gridOptions.enableExcelMode" class="data-grid-paging-shadow" style="height:50px;display:none;"></div>
        <div class="data-grid-paging" ng-if="gridOptions.enablePaging || gridOptions.enableExcelMode || gridOptions.enableCellEdit">
            <div class="data-grid-pageinfo pull-right" ng-if="gridOptions.enablePaging">
                <div class="btn-group pull-right" style="padding-top:2px;margin-left:5px;" >
                    <button ng-click="datasource.query()"
                            type="button" class="btn btn-default">
                        <i class="fa fa-refresh"></i> Refresh
                    </button>
                    <button ng-click="reset()"
                            type="button" class="btn btn-default">
                        <i class="fa fa-repeat"></i> Reset
                    </button>
                </div>

                <div class="btn-group pull-right" style="padding-top:2px;" dropdown>
                    <button type="button" class="btn btn-default dropdown-toggle">

                        <span class="caret pull-right" 
                              style="margin:7px 0px 0px 5px;"></span>
                        {{gridOptions.pagingOptions.pageSize}}
                    </button>
                    <ul class="dropdown-menu" role="menu">
                        <li class="dropdown-toggle"
                            ng-click="gridOptions.pagingOptions.pageSize = page"
                            ng-repeat="page in gridOptions.pagingOptions.pageSizes">
                            <a href="#">{{page}}</a>
                        </li>
                    </ul>
                </div>

                <div class="pull-right" style="margin:5px;">
                    View Per Page:
                </div>
            </div>
            <div ng-if="gridOptions.enableExcelMode"
            <?php if (@$this->gridOptions['enablePaging'] == 'true'): ?>
                     style="float:right;border-right:1px solid #ccc;padding-right:10px;margin-right:5px;"
                 <?php else: ?>
                     style="float:left;margin-left:-5px;"
                 <?php endif; ?>
                 class="data-grid-pageinfo">
                <div class="btn-group pull-right" style="padding-top:2px;margin-left:5px;" >
                    <button ng-click="addRow(excelModeSelectedRow)" type="button" class="btn btn-default">
                        <i class="fa fa-plus"></i> Add
                    </button>
                    <button ng-click="removeRow(excelModeSelectedRow)"
                            ng-if="grid.selectedItems.length > 0"
                            type="button" class="btn btn-default">
                        <i class="fa fa-times"></i> Remove
                    </button>
                </div>

                <div class="pull-right" style="margin:5px;">
                    Row:
                </div>
            </div>
            <div class="data-grid-pagination" ng-if="gridOptions.enablePaging">
                <div class="pull-left" style="margin:5px;">Page:</div>
                <div class="pull-left">
                    <div class="data-grid-page-selector">
                        <div class="input-group input-group-sm">
                            <span class="input-group-btn">
                                <button class="btn btn-default" ng-click="grid.pageBackward()" type="button">
                                    <i class="fa fa-chevron-left"></i>
                                </button>
                            </span>
                            <input type="text" class="text-center form-control"
                                   ng-keypress="pagingKeypress($event)"
                                   ng-model="gridOptions.pagingOptions.currentPage">
                            <span class="input-group-btn">
                                <button class="btn btn-default"  ng-click="grid.pageForward()" type="button">
                                    <i class="fa fa-chevron-right"></i>
                                </button>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="pull-left" style="margin:5px">
                    of {{ Math.ceil(datasource.totalItems / gridOptions.pagingOptions.pageSize)}} 
                </div>
                <div class="pull-left" 
                     style="border-left:1px solid #ccc;margin:2px 5px;padding:3px 8px;">
                    Total of {{ datasource.totalItems}} Record{{ datasource.totalItems >1 ? 's' :'' }}
                </div>
            </div>
            <div class="clearfix"></div>
        </div>

        <div ng-show="datasource.data.length != 0 || gridOptions.enableExcelMode" class="data-grid-category" category-header="gridOptions"></div>
        <div ng-show="datasource.data.length != 0 || gridOptions.enableExcelMode" class="data-grid-table" ng-init="initGrid()" ng-grid="gridOptions"></div>

        <div ng-if="datasource.data.length == 0 && !gridOptions.enableExcelMode" 
             style="text-align:center;padding:20px;color:#ccc;font-size:25px;">
            &mdash; Data Empty &mdash;
        </div>
    </div>
</div>