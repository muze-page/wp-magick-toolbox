<?php

/**
 * 未登录隐藏指定标签下的文章
 */

if (!class_exists('Npcink_Page_Hide_Tag')) {
    class Npcink_Page_Hide_Tag
    {
        private static $id_array; //配置
        public static function run($array)
        {
            self::$id_array = $array;
            add_action('pre_get_posts', array(__CLASS__, 'exclude_posts_by_tag'));//隐藏标签下的文章
            add_action( 'pre_get_posts', array(__CLASS__,'exclude_posts_and_add_login_hint') );//提示
        }

        //隐藏指定标签下的文章
        public static function exclude_posts_by_tag($query)
        {
            if (!is_admin() && !is_user_logged_in() && $query->is_main_query()) {
                $excluded_tag_ids = self::$id_array; // 要隐藏的标签ID数组

                // 检查是否在标签页
                if ($query->is_tag($excluded_tag_ids)) {
                    $query->set('tag__not_in', $excluded_tag_ids); // 排除特定标签
                }
            }
        }

        //添加提示
        public static function exclude_posts_and_add_login_hint($query)
        {
            if (!is_admin() && !is_user_logged_in() && $query->is_main_query()) {
                // 检查是否在标签页
                if ($query->is_tag()) {
                    // 获取当前标签
                    $current_tag = $query->get_queried_object();

                    // 检查是否为受限标签
                    if ($current_tag && in_array($current_tag->term_id, self::$id_array)) {
                        // 排除受限标签下的文章
                        $query->set('tag__not_in', array($current_tag->term_id));

                        // 添加提示消息
                        add_action('wp_footer', function () {
                            echo '<div class="login-hint">抱歉，您没有权限访问此文章，请<a href="' . wp_login_url(get_permalink()) . '">登录</a>后访问。</div>';
                        });
                    }
                }
            }
        }
    }
}
