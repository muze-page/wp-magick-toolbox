<?php

/**
 * 功能
 */
if (!class_exists('MaBox_ShortCode')) {
    class MaBox_ShortCode
    {
        public static function run()
        {

            //获取设置选项值
            $config = MaBox_Admin::get_seting('shortcode');

            /**
             * 短代码 - 板式
             */
            require_once plugin_dir_path(__FILE__) . '/compose/index.php';
            $compose =  MaBox_Admin::get_config($config, 'compose');
            MaBox_ShortCode_Compose::run($compose);

            
        }
       
    } //end
}
