<?php
//优化 后台
if (!class_exists('MaBox_Optimize_Admin')) {
    class MaBox_Optimize_Admin
    {
        //加载
        public static function run($config)
        {
            //获取选项
            $option =  MaBox_Admin::get_config($config, 'admin');

            //文章管理添加作者筛选
            $add_user = MaBox_Admin::get_config($option, 'add_user');
            if ($add_user === true) {
                require_once plugin_dir_path(__FILE__) . 'single_add_user_screen.php';
                Npcink_Admin_Single_Add_User_Screen::run();
            };

            //文章和媒体添加日期筛选
            $add_time = MaBox_Admin::get_config($option, 'add_time');
            if ($add_time === true) {
                require_once plugin_dir_path(__FILE__) . 'add_time_screen.php';
                Npcink_Admin_Add_Time_Screen::run();
            };

            //各个列表显示ID
            $show_id = MaBox_Admin::get_config($option, 'show_id');
            if ($show_id === true) {
                require_once plugin_dir_path(__FILE__) . 'single_show_id.php';
                Npcink_Admin_Single_Show_ID::run();
            }

             //缩略图切换
             $thumbnail_switcher = MaBox_Admin::get_config($option, 'thumbnail_switcher');
             if ($thumbnail_switcher === true) {
                 require_once plugin_dir_path(__FILE__) . 'thumbnail_switcher/index.php';
                 Npcink_Admin_Single_Thumbnail_Switcher::run();
             }
        }
    } //end
}
