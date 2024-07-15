<?php

/**
 * 功能：足迹地图
 * 来源：https://github.com/HelloWuJiaYi/jVectorMap-Footprint
 */
if (!class_exists('MaBox_ShortCode_Merc_Map')) {
    class MaBox_ShortCode_Merc_Map
    {
        public static function run()
        {
            //添加短代码
            // add_shortcode('past_posts_display', array(__CLASS__, 'past_posts_display_shortcode'));

            // 判断当前页面是否有 mabox_copy_btn 短代码，如果有则加载 加载前端资源
            //add_action('wp_enqueue_scripts', function () {
            //    global $post;
            //    if (has_shortcode($post->post_content, 'past_posts_display')) {
            //        self::load_js();
            //    }
            //});
            add_action('wp_head', array(__CLASS__, 'add_hello_header'));
        }
        public static function add_hello_header()
        {
            echo '<div style="background-color: yellow; text-align: center;">你好</div>';
        }
    }
}
