<?php

/**
 * 效果：插件设置选项
 */
if (!class_exists('MaBox_Config')) {
    class MaBox_Config
    {
        public static function run($option)
        {
            //删除插件的同时删除选项 - 见根目录下的uninstall.php文件
            //require_once plugin_dir_path(__FILE__) . 'remove_config.php';
            //$remove_config = MaBox_Admin::get_config($option, 'remove_config');
            //if ($remove_config === true) {
            //    require_once plugin_dir_path(__FILE__) . 'remove_config.php'; //载入文件
            //    MaBox_Config_Remove_Config::run($remove_config);
            //}
        }
    }
}
