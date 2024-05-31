<?php

/**
 * 页面 权限
 */

if (!class_exists('Npcink_Page_Jurisdiction')) {
    class Npcink_Page_Jurisdiction
    {
        public static function run($option)
        {


            $category_id = MaBox_Admin::get_config($option, 'category_id');
            $tag_id = MaBox_Admin::get_config($option, 'tag_id');
            //分类数组或标签数组是非空数组才开启接口
            if (!empty($category_id) || !empty($tag_id)) {
                //添加分类数据接口
                require_once plugin_dir_path(__FILE__) . 'interface_category_data.php';
                Npcink_Interface_Category_Data::run();
            }

            //隐藏指定分类
            if (!empty($category_id)) {
                require_once plugin_dir_path(__FILE__) . 'hide_category.php';
                Npcink_Page_Hide_Category::run($category_id);
            }
        }
    }
}
