<?php

class DevUserIndex extends User {
    public function getFields() {
        return array (
            array (
                'linkBar' => array (
                    array (
                        'label' => 'New User',
                        'url' => '/dev/user/new',
                        'buttonType' => 'success',
                        'icon' => 'plus',
                        'options' => array (
                            'href' => 'url:/dev/user/new',
                        ),
                        'type' => 'LinkButton',
                    ),
                ),
                'showSectionTab' => 'No',
                'type' => 'ActionBar',
            ),
            array (
                'name' => 'dataFilter1',
                'datasource' => 'dataSource1',
                'filters' => array (
                    array (
                        'name' => 'id',
                        'label' => 'id',
                        'listExpr' => '',
                        'filterType' => 'number',
                        'show' => false,
                    ),
                    array (
                        'name' => 'nip',
                        'label' => 'nip',
                        'listExpr' => '',
                        'filterType' => 'string',
                        'show' => false,
                    ),
                    array (
                        'name' => 'fullname',
                        'label' => 'fullname',
                        'listExpr' => '',
                        'filterType' => 'string',
                        'show' => false,
                    ),
                    array (
                        'name' => 'email',
                        'label' => 'email',
                        'listExpr' => '',
                        'filterType' => 'string',
                        'show' => false,
                    ),
                    array (
                        'name' => 'phone',
                        'label' => 'phone',
                        'listExpr' => '',
                        'filterType' => 'string',
                        'show' => false,
                    ),
                    array (
                        'name' => 'username',
                        'label' => 'username',
                        'listExpr' => '',
                        'filterType' => 'string',
                        'show' => false,
                    ),
                    array (
                        'name' => 'last_login',
                        'label' => 'last login',
                        'listExpr' => '',
                        'filterType' => 'date',
                        'show' => false,
                    ),
                    array (
                        'name' => 'role',
                        'label' => 'role',
                        'listExpr' => '',
                        'filterType' => 'string',
                        'show' => false,
                    ),
                ),
                'type' => 'DataFilter',
            ),
            array (
                'name' => 'dataSource1',
                'sql' => 'select u.*,r.role_description as role from p_user u
 left outer join 
   p_user_role p on u.id = p.user_id 
   and p.is_default_role = \'Yes\' 
 left outer join 
   p_role r on r.id = p.role_id 
 {where [where]} group by u.id {[order]} {[paging]}',
                'params' => array (
                    'where' => 'dataFilter1',
                    'order' => 'dataGrid1',
                    'paging' => 'dataGrid1',
                ),
                'enablePaging' => 'Yes',
                'pagingSQL' => 'select count(1) as role from p_user u
 left outer join 
   p_user_role p on u.id = p.user_id 
   and p.is_default_role = \'Yes\' 
 left outer join 
   p_role r on r.id = p.role_id 
    {where [where]}',
                'type' => 'DataSource',
            ),
            array (
                'name' => 'dataGrid1',
                'datasource' => 'dataSource1',
                'columns' => array (
                    array (
                        'name' => 'id',
                        'label' => 'id',
                        'options' => array (),
                        'buttonCollapsed' => 'Yes',
                        'buttons' => array (
                            array (
                                '',
                                'label' => '',
                            ),
                        ),
                        'listType' => 'php',
                        'listExpr' => '',
                        'listMustChoose' => 'No',
                        'relCondition' => '',
                        'relModelClass' => '',
                        'relIdField' => '',
                        'relLabelField' => '',
                        'columnType' => 'string',
                        'show' => true,
                    ),
                    array (
                        'name' => 'nip',
                        'label' => 'nip',
                        'options' => array (),
                        'buttonCollapsed' => 'Yes',
                        'buttons' => array (
                            array (
                                '',
                                'label' => '',
                            ),
                        ),
                        'listType' => 'php',
                        'listExpr' => '',
                        'listMustChoose' => 'No',
                        'relCondition' => '',
                        'relModelClass' => '',
                        'relIdField' => '',
                        'relLabelField' => '',
                        'columnType' => 'string',
                        'show' => false,
                    ),
                    array (
                        'name' => 'fullname',
                        'label' => 'fullname',
                        'options' => array (),
                        'buttonCollapsed' => 'Yes',
                        'buttons' => array (
                            array (
                                '',
                                'label' => '',
                            ),
                        ),
                        'listType' => 'php',
                        'listExpr' => '',
                        'listMustChoose' => 'No',
                        'relCondition' => '',
                        'relModelClass' => '',
                        'relIdField' => '',
                        'relLabelField' => '',
                        'columnType' => 'string',
                        'show' => false,
                    ),
                    array (
                        'name' => 'email',
                        'label' => 'email',
                        'options' => array (),
                        'buttonCollapsed' => 'Yes',
                        'buttons' => array (
                            array (
                                '',
                                'label' => '',
                            ),
                        ),
                        'listType' => 'php',
                        'listExpr' => '',
                        'listMustChoose' => 'No',
                        'relCondition' => '',
                        'relModelClass' => '',
                        'relIdField' => '',
                        'relLabelField' => '',
                        'columnType' => 'string',
                        'show' => false,
                    ),
                    array (
                        'name' => 'phone',
                        'label' => 'phone',
                        'options' => array (),
                        'buttonCollapsed' => 'Yes',
                        'buttons' => array (
                            array (
                                '',
                                'label' => '',
                            ),
                        ),
                        'listType' => 'php',
                        'listExpr' => '',
                        'listMustChoose' => 'No',
                        'relCondition' => '',
                        'relModelClass' => '',
                        'relIdField' => '',
                        'relLabelField' => '',
                        'columnType' => 'string',
                        'show' => false,
                    ),
                    array (
                        'name' => 'username',
                        'label' => 'username',
                        'options' => array (),
                        'buttonCollapsed' => 'Yes',
                        'buttons' => array (
                            array (
                                '',
                                'label' => '',
                            ),
                        ),
                        'listType' => 'php',
                        'listExpr' => '',
                        'listMustChoose' => 'No',
                        'relCondition' => '',
                        'relModelClass' => '',
                        'relIdField' => '',
                        'relLabelField' => '',
                        'columnType' => 'string',
                        'show' => false,
                    ),
                    array (
                        'name' => 'password',
                        'label' => 'password',
                        'options' => array (),
                        'buttonCollapsed' => 'Yes',
                        'buttons' => array (
                            array (
                                '',
                                'label' => '',
                            ),
                        ),
                        'listType' => 'php',
                        'listExpr' => '',
                        'listMustChoose' => 'No',
                        'relCondition' => '',
                        'relModelClass' => '',
                        'relIdField' => '',
                        'relLabelField' => '',
                        'columnType' => 'string',
                        'show' => false,
                    ),
                    array (
                        'name' => 'last_login',
                        'label' => 'last_login',
                        'options' => array (),
                        'buttonCollapsed' => 'Yes',
                        'buttons' => array (
                            array (
                                '',
                                'label' => '',
                            ),
                        ),
                        'listType' => 'php',
                        'listExpr' => '',
                        'listMustChoose' => 'No',
                        'relCondition' => '',
                        'relModelClass' => '',
                        'relIdField' => '',
                        'relLabelField' => '',
                        'columnType' => 'string',
                        'show' => false,
                    ),
                    array (
                        'name' => 'role',
                        'label' => 'role',
                        'options' => array (),
                        'buttonCollapsed' => 'Yes',
                        'buttons' => array (
                            array (
                                '',
                                'label' => '',
                            ),
                        ),
                        'listType' => 'php',
                        'listExpr' => '',
                        'listMustChoose' => 'No',
                        'relCondition' => '',
                        'relModelClass' => '',
                        'relIdField' => '',
                        'relLabelField' => '',
                        'columnType' => 'string',
                        'show' => false,
                    ),
                ),
                'gridOptions' => array (
                    'useExternalSorting' => 'true',
                    'enablePaging' => 'true',
                    'afterSelectionChange' => 'url:/dev/user/update?id={id}',
                    'enableColumnResize' => 'true',
                ),
                'type' => 'DataGrid',
            ),
            array (
                'name' => 'dataSource2',
                'sql' => 'select 20 as \\\'Series 1\\\', 30 as \\\'Series 2\\\', 50 as \\\'Series 3\\\' from dual',
                'type' => 'DataSource',
            ),
            array (
                'name' => 'pieChart1',
                'datasource' => 'dataSource2',
                'chartTitle' => 'Judul Chart',
                'series' => array (
                    array (
                        'label' => 'Series 1',
                        'value' => '20',
                        'color' => '#ff0000',
                        'columnOptions' => array (),
                        'show' => true,
                    ),
                    array (
                        'label' => 'Series 2',
                        'value' => '30',
                        'color' => '#4de826',
                        'columnOptions' => array (),
                        'show' => false,
                    ),
                    array (
                        'label' => 'Series 3',
                        'value' => '50',
                        'color' => '#cacaca',
                        'columnOptions' => array (),
                        'show' => false,
                    ),
                ),
                'type' => 'ChartPie',
            ),
        );
    }
    
    public function getForm() {
        return array (
            'title' => 'User List',
            'layout' => array (
                'name' => 'full-width',
                'data' => array (
                    'col1' => array (
                        'type' => 'mainform',
                        'size' => '100',
                    ),
                ),
            ),
            'inlineJS' => 'js/index.js',
        );
    }
    
}