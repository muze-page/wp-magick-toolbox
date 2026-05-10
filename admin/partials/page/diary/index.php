<?php
/**
 * 日记风格文章类型
 *
 * 创建自定义文章类型「日记」，模仿日记格式展示。
 */
if (!class_exists('MaBox_Diary_Post_Type')) {
    class MaBox_Diary_Post_Type {

        private static $post_type = 'mabox_diary';

        public static function run() {
            add_action('init', array(__CLASS__, 'register_post_type'));
            add_filter('single_template', array(__CLASS__, 'load_diary_template'));
        }

        public static function register_post_type() {
            register_post_type(self::$post_type, array(
                'labels' => array(
                    'name'          => '日记',
                    'singular_name' => '日记',
                    'add_new'       => '写日记',
                    'add_new_item'  => '写新日记',
                    'edit_item'     => '编辑日记',
                    'view_item'     => '查看日记',
                    'search_items'  => '搜索日记',
                    'not_found'     => '未找到日记',
                ),
                'public'             => true,
                'show_ui'            => true,
                'show_in_menu'       => 'edit.php',
                'capability_type'    => 'post',
                'supports'           => array('title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments'),
                'menu_icon'          => 'dashicons-book',
                'show_in_rest'       => true,
                'has_archive'        => true,
                'rewrite'            => array('slug' => 'diary'),
            ));

            register_taxonomy('mabox_diary_mood', self::$post_type, array(
                'labels' => array(
                    'name'          => '心情',
                    'singular_name' => '心情',
                    'search_items'  => '搜索心情',
                    'all_items'     => '所有心情',
                    'edit_item'     => '编辑心情',
                    'update_item'   => '更新心情',
                    'add_new_item'  => '添加心情',
                ),
                'hierarchical' => false,
                'show_ui'      => true,
                'show_in_rest' => true,
            ));

            $moods = array('开心', '难过', '平静', '激动', '疲惫', '感恩', '期待', '焦虑');
            foreach ($moods as $mood) {
                if (!term_exists($mood, 'mabox_diary_mood')) {
                    wp_insert_term($mood, 'mabox_diary_mood');
                }
            }
        }

        public static function load_diary_template($template) {
            if (is_singular(self::$post_type)) {
                $diary_template = plugin_dir_path(__FILE__) . 'diary/single-diary.php';
                if (file_exists($diary_template)) {
                    return $diary_template;
                }
            }
            return $template;
        }
    }
}
