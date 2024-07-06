<?php

/**
 * 功能：文章列表
 * 来源：https://www.evan.xin/1934/
 */
if (!class_exists('MaBox_ShortCode_Single_List')) {
    class MaBox_ShortCode_Single_List
    {
        public static function run()
        {
             //加载前端资源
             add_action('wp_enqueue_scripts', array(__CLASS__, 'load_js'));
             //添加短代码
            add_shortcode('past_posts_display', array(__CLASS__, 'past_posts_display_shortcode'));

            
        }

       
        
        

        public static function past_posts_display_shortcode($atts)
        {
            // Shortcode attributes with defaults
            $atts = shortcode_atts(array(
                'ids'   => '', // Comma-separated list of post IDs
                'links' => '', // Comma-separated list of post links
                'limit' => 5,   // Number of posts to display, default is 5
            ), $atts, 'past_posts_display');

            // Convert input to an array and filter out empty values
            $ids = array_filter(array_map('trim', explode(',', $atts['ids'])));
            $links = array_filter(array_map('trim', explode(',', $atts['links'])));

            // Convert links to post IDs
            $ids = array_merge($ids, array_map(function ($link) {
                return url_to_postid($link);
            }, $links));

            // Remove duplicates and get posts
            $post_ids = array_unique($ids);
            $args = array(
                'post__in' => $post_ids,
                'posts_per_page' => $atts['limit'],
                'post__not_in' => get_option('sticky_posts'), // Exclude sticky posts
                'ignore_sticky_posts' => 1,
                'orderby' => 'post__in', // Order by the post IDs sequence
            );
            $query = new WP_Query($args);

            // Prepare output
            $output = '<div class="past-posts-listing">';
            if ($query->have_posts()) {
                while ($query->have_posts()) {
                    $query->the_post();
                    $post_title = get_the_title();
                    $post_link = get_permalink();
                    $post_date = get_the_date('F j, Y');
                    $output .= '<div class="past-post-item">';
                    $output .= '<div class="past-post-content"><a href="' . esc_url($post_link) . '" target="_blank">' . esc_html($post_title) . '</a></div>';
                    $output .= '<div class="past-post-timestamp">' . esc_html($post_date) . '</div>';
                    $output .= '</div>';
                }
                wp_reset_postdata();
            } else {
                $output .= '<p>No posts found.</p>';
            }
            $output .= '</div>';

            return $output;
        }

        public static function load_js()
        {
            //判断下，是否在前端页中
            if (is_admin()) {
                return;
            }

            //准备数据
            $build_js =  plugin_dir_url(__DIR__) . 'single_list/script.js';
            $build_css =  plugin_dir_url(__DIR__) . 'single_list/style.css';

            wp_enqueue_script(
                MAGICK_MIXTURE_NAME . '_public_single_list_js',
                $build_js,
                array('jquery'),
                MAGICK_MIXTURE_VERSION,
                true
            );

            wp_enqueue_style(
                MAGICK_MIXTURE_NAME . '_public_single_list_css',
                $build_css,
                array(),
                MAGICK_MIXTURE_VERSION,
                false
            );
        }
    }
}
