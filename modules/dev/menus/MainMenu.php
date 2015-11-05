<?php 

## AUTOGENERATED OPTIONS - DO NOT EDIT
$options = array (
    'mode' => 'normal',
    'layout' => array (
        'size' => '200',
        'sizetype' => 'px',
        'type' => 'menu',
        'name' => 'col1',
        'file' => 'application.modules.dev.menus.MainMenu',
        'title' => '',
        'icon' => '',
        'inlineJS' => '',
    ),
);
## END OF AUTOGENERATED OPTIONS

return array (
    array (
        'label' => 'Builder',
        'items' => array (
            array (
                'label' => 'Form Builder',
                'icon' => 'fa-gavel',
                'url' => '/dev/forms/index',
                'formattedUrl' => '/egrc/index.php?r=dev/forms/index',
            ),
            array (
                'label' => '---',
                'icon' => '',
                'url' => '#',
            ),
            array (
                'label' => 'Email Builder',
                'icon' => 'fa-envelope',
                'url' => '/dev/email/index',
                'formattedUrl' => '/egrc/index.php?r=email/index',
            ),
            array (
                'label' => '---',
                'icon' => '',
                'url' => '#',
            ),
            array (
                'label' => 'MenuTree Editor',
                'icon' => 'fa-sitemap',
                'url' => '/dev/genMenu/index',
                'formattedUrl' => '/egrc/index.php?r=dev/genMenu/index',
            ),
            array (
                'label' => '---',
                'icon' => '',
                'url' => '#',
            ),
            array (
                'label' => 'Module Generator',
                'icon' => 'fa-empire',
                'url' => '/dev/genModule/index',
                'formattedUrl' => '/egrc/index.php?r=dev/genModule/index',
            ),
            array (
                'label' => 'Model Generator',
                'icon' => 'fa-cube',
                'url' => '/dev/genModel/index',
                'formattedUrl' => '/egrc/index.php?r=dev/genModel/index',
            ),
            array (
                'label' => 'Controller Generator',
                'icon' => 'fa-slack',
                'url' => '/dev/genCtrl/index',
                'formattedUrl' => '/etis/index.php?r=dev/genCtrl/index',
            ),
            array (
                'label' => '---',
                'icon' => '',
                'url' => '#',
            ),
            array (
                'label' => 'Service Manager',
                'icon' => 'fa-asterisk',
                'url' => '/dev/service/index',
                'formattedUrl' => '/etis/index.php?r=dev/processManager/index',
            ),
        ),
        'state' => 'collapsed',
        'icon' => 'fa-gavel',
    ),
    array (
        'label' => 'Users',
        'icon' => 'fa-user',
        'url' => '#',
        'items' => array (
            array (
                'label' => 'User List',
                'icon' => 'fa-user',
                'url' => '/dev/user/index',
                'formattedUrl' => '/egrc/index.php?r=dev/user/index',
            ),
            array (
                'label' => 'Role Manager',
                'icon' => 'fa-graduation-cap',
                'url' => '/dev/user/roles',
                'formattedUrl' => '/egrc/index.php?r=dev/user/roles',
            ),
        ),
        'state' => 'collapsed',
    ),
    array (
        'label' => 'Settings',
        'icon' => 'fa-sliders',
        'url' => '/dev/setting/app',
        'formattedUrl' => '/egrc/index.php?r=dev/setting/app',
    ),
    array (
        'label' => 'Repository',
        'icon' => 'fa-folder-open',
        'url' => '/repo/index',
        'formattedUrl' => '/egrc/index.php?r=repo/index',
    ),
);