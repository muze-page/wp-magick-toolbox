<?php

/**
 * 效果：简单SEO - 分类和标签TDK 
 * 来源：https://www.npc.ink/4596.html
 */
if (!class_exists('Npcink_Seo_Category')) {
    class Npcink_Seo_Category
    {
        public static function run()
        {
            add_action('wp_head', array(__CLASS__, 'add_hello_header'));
        }
        public static function add_hello_header()
        {
            echo '<div style="background-color: yellow; text-align: center;">你好</div>';
        }
    }
}
