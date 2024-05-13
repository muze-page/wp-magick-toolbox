<?php

/**
 * 页面 功能
 */

if (!class_exists('Npcink_Page_Function')) {
    class Npcink_Page_Function
    {
        public static function run($option)
        {
            //文章关键词自动添加内链链接代码
            $add_inks = MaMi_Admin::get_config($option, 'add_inks');
            if ($add_inks === true) {
                require_once plugin_dir_path(__FILE__) . 'single_keyword_add_link.php';
                Npcink_Single_Keyword_Add_Link::run();
            }
            //去除文章内的超链接，可复原
            $remove_single_link = MaMi_Admin::get_config($option, 'remove_single_link');
            if ($remove_single_link === true) {
                require_once plugin_dir_path(__FILE__) . 'single_remove_link.php';
                Npcink_Single_Remove_Link::run();
            }

            //圆角彩色背景标签云
            $color_tag = MaMi_Admin::get_config($option, 'color_tag');
            if ($color_tag === true) {
                require_once plugin_dir_path(__FILE__) . 'color_tags.php';
                Npcink_Page_Color_Tags::run();
            }

            //文章末尾添加最后更新时间
            $add_last_update = MaMi_Admin::get_config($option, 'add_last_update');
            if ($add_last_update === true) {
                require_once plugin_dir_path(__FILE__) . 'add_article_update_time.php';
                Npcink_Single_Add_Last_Updated_Date::run();
            }

            //跳转中间页
            $go_middle = MaMi_Admin::get_config($option, 'go_middle');
            if ($go_middle !== false) {
                require_once plugin_dir_path(__FILE__) . 'jump_middle_page.php';
                Npcink_Jump_Middle_Page::run($go_middle);
            }
        }
    }
}
