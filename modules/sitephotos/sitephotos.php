<?php
defined('BASEPATH') or exit('No direct script access allowed');

/*
 * Perfex CRM - Site Photos module
 */

define('SITEPHOTOS_MODULE_NAME', 'sitephotos');
define('SITEPHOTOS_TIMELINE_UPLOAD_PATH', FCPATH . 'modules/sitephotos/uploads/timeline/');
define('SITEPHOTOS_TIMELINE_URL_PATH', site_url('modules/sitephotos/uploads/timeline/'));
define('SITEPHOTO_REVISION', rand(100000, 999999));

hooks()->add_action('admin_init', 'sitephoto_module_init_menu_items');

register_activation_hook(SITEPHOTOS_MODULE_NAME, 'sitephotos_module_activation');

function sitephotos_module_activation()
{
    $CI = &get_instance();
    require_once(__DIR__ . '/install.php');
}

function sitephoto_module_init_menu_items()
{
    $CI = &get_instance();
    $CI->app_menu->add_sidebar_menu_item('sitephotos', [
        'name'     => _l('Site photos'),
        'href'     => admin_url('sitephotos/photos'),
        'icon'     => 'fa-regular fa-images',
        'position' => 15,
        'badge'    => [],
    ]);
}

?>