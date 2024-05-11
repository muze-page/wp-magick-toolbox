<?php

/**
 * 效果：灯笼
 * 来源：
 */
if (!class_exists('Npcink_Page_Lantern')) {
    class Npcink_Page_Lantern
    {
        public static $option; //配置
        public static function run($config)
        {
            self::$option = $config;
            //移动端不展示
            if (!wp_is_mobile()) {
                add_action('wp_enqueue_scripts', array(__CLASS__, 'lantern_css'));
                add_action('wp_footer', array(__CLASS__, 'lantern'));
            }
        }
        /**
         * 添加灯笼css
         */
        public static function lantern_css()
        {
            wp_enqueue_style(
                MAGICK_MIXTURE_NAME . '_lantern',
                plugin_dir_url(__FILE__) . 'css/lantern.css',
                array(),
                MAGICK_MIXTURE_VERSION,
                false
            );
        }
        /**
         * 添加灯笼节点
         */
        public static function lantern()
        {
            $lantern_left =  MaMi_Admin::get_config(self::$option, 'lantern_left', "春");
            $lantern_right =  MaMi_Admin::get_config(self::$option, 'lantern_right', "节");
            echo '
<div id="lantern">
<div class="deng-box">
<div class="deng">
    <div class="xian"></div>
    <div class="deng-a">
        <div class="deng-b"><div class="deng-t">' . $lantern_right . '</div></div>
    </div>
    <div class="shui shui-a"><div class="shui-c"></div><div class="shui-b"></div></div>
</div>
</div>

<!-- 灯笼2 -->
<div class="deng-box1">
<div class="deng">
    <div class="xian"></div>
    <div class="deng-a">
        <div class="deng-b"><div class="deng-t">' . $lantern_left . '</div></div>
    </div>
    <div class="shui shui-a"><div class="shui-c"></div><div class="shui-b"></div></div>
</div>
</div>
</div>
<!--结束包裹我-->';
        }
    }
}
