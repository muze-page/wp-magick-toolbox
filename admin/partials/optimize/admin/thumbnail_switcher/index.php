<?php

/**
 * 效果：文章列表添加缩略图展示、添加和删除
 * 来源：https://wordpress.org/plugins/easy-thumbnail-switcher/
 */
if (!class_exists('Npcink_Admin_Single_Thumbnail_Switcher')) {
    class Npcink_Admin_Single_Thumbnail_Switcher
    {
        //加载
        public static function run()
        {
            // 加载 test.php 文件
            require_once 'easy-thumbnail-switcher.php';
        }
    }
}
