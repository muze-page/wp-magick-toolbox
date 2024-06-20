<?php

/**
 * 效果：背景特效
 * 来源：
 */
if (!class_exists('Npcink_Page_Background_Effect')) {
    class Npcink_Page_Background_Effect
    {
        public static function run($config)
        {


            switch ($config) {
                case 'star': //底部飘星星
                    require_once plugin_dir_path(__FILE__) . 'footer-star/index.php';
                    Npcink_Page_Footer_Star::run();
                    break;
                case 'sakura': //飘落樱花
                    require_once plugin_dir_path(__FILE__) . 'sakura_drops/index.php';
                    Npcink_Page_Sakura_Drops::run();
                    break;
                case 'coupling': //细线联结
                    require_once plugin_dir_path(__FILE__) . 'convergence_line/index.php';
                    Npcink_Page_Add_Convergence_Line::run();
                    break;
                default:
                    echo "i is not equal to 0, 1 or 2";
            }
        }
    }
}
