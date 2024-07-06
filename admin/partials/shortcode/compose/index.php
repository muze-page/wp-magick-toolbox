<?php

/**
 * 功能：短代码 板式
 */
if (!class_exists('MaBox_ShortCode_Compose')) {
    class MaBox_ShortCode_Compose
    {
        public static function run($option)
        {

            //文章列表
            $single_list = MaBox_Admin::get_config($option, 'single_list');
            if ($single_list === true) {
                require_once plugin_dir_path(__FILE__) . 'single_list/index.php';
                MaBox_ShortCode_Single_List::run();
            }
        }

       
    } //end
}
