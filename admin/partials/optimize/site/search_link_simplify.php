<?php

defined('ABSPATH') || exit;

/**
 * 效果：修改WordPress搜索结果的链接样式
 * 来源：https://www.huitheme.com/wordpress-search.html
 */
if (!class_exists('MaBox_Search_Link_Simplify')) {
    class MaBox_Search_Link_Simplify implements MaBox_Module_Interface
    {
        public static function run($config = array())
        {
            add_action('template_redirect', array(__CLASS__, 'redirect_search'));
        }
        
        //修改搜索结果的链接
        public static function redirect_search()
        {
            // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Public read-only search query; no state is changed.
            $search_term = isset($_GET['s']) && is_string($_GET['s']) ? sanitize_text_field(wp_unslash($_GET['s'])) : '';

            if (is_search() && '' !== $search_term) {
                wp_safe_redirect(home_url('/search/' . rawurlencode($search_term)));
                exit();
            }
        }
    }
}
