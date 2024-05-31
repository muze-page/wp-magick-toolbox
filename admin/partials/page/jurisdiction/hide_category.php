<?php

/**
 * 未登录隐藏指定分类下的文章
 */

if (!class_exists('Npcink_Page_Hide_Category')) {
    class Npcink_Page_Hide_Category
    {
        private static $id_array; //配置
        public static function run($array)
        {
            self::$id_array = $array;
            add_action('pre_get_posts', array(__CLASS__, 'exclude_category_from_query'));
        }

        public static  function exclude_category_from_query($query)
        {
            if (!is_admin() && !is_user_logged_in() && $query->is_main_query()) {
                $excluded_category_ids = self::$id_array; // 要隐藏的分类ID数组

                // 检查是否在分类页
                if ($query->is_category($excluded_category_ids)) {
                    $query->set('post__not_in', array()); // 清空当前查询中的post__not_in参数，以确保不会排除任何文章

                    foreach ($excluded_category_ids as $category_id) {
                        $query->set('cat', '-' . $category_id); // 排除特定分类
                    }
                }
            }
        }
    }
}
