<?php

defined('ABSPATH') || exit;

/**
 * 接口 - 提供分类和标签数据接口
 */

if (!class_exists('MaBox_Interface_Category_Data')) {
    class MaBox_Interface_Category_Data implements MaBox_Module_Interface
    {
        public static function run($config = array())
        {
        }

        /**
         * Return category, tag, and page options for the admin settings UI.
         *
         * @param WP_REST_Request|null $_request REST request, unused.
         * @return WP_REST_Response|WP_Error
         */
        public static function get_all_category_names(?\WP_REST_Request $_request = null)
        {
            $categories = get_terms(array(
                'taxonomy'   => 'category',
                'hide_empty' => false,
            ));

            if (is_wp_error($categories)) {
                return self::unavailable_response();
            }

            $category_array = array();

            foreach ($categories as $category) {
                $category_array[] = array(
                    'label' => (string) $category->name,
                    'value' => (int) $category->term_id,
                );
            }

            $tags = get_terms(array(
                'taxonomy'   => 'post_tag',
                'hide_empty' => false,
            ));

            if (is_wp_error($tags)) {
                return self::unavailable_response();
            }

            $tag_array = array();

            foreach ($tags as $tag) {
                $tag_array[] = array(
                    'label' => (string) $tag->name,
                    'value' => (int) $tag->term_id,
                );
            }

            $pages = get_pages();
            if (!is_array($pages)) {
                return self::unavailable_response();
            }

            $page_array = array();

            foreach ($pages as $page) {
                $page_array[] = array(
                    'label' => (string) $page->post_title,
                    'value' => (int) $page->ID,
                );
            }

            return rest_ensure_response(array(
                'success' => true,
                'data'    => array(
                    'categorys' => $category_array,
                    'tags'      => $tag_array,
                    'pages'     => $page_array,
                ),
            ));
        }

        private static function unavailable_response()
        {
            return new WP_Error(
                'mabox_category_data_unavailable',
                __('无法获取分类、标签和页面数据。', 'magick-toolbox'),
                array('status' => 500)
            );
        }
    }
}
