<?php
/**
 * 仅登录可搜索
 * 未登录用户无法使用搜索功能
 */
if (!class_exists('Npcink_Page_Login_Search')) {
    class Npcink_Page_Login_Search
    {
        public static function run()
        {
            add_action('pre_get_posts', array(__CLASS__, 'check_login_search'));
        }

        public static function check_login_search($query)
        {
            if (!is_admin() && $query->is_search && $query->is_main_query()) {
                if (!MaBox_Helpers::is_logged_in()) {
                    wp_die('请先登录后再使用搜索功能。');
                }
            }
        }
    }
}
