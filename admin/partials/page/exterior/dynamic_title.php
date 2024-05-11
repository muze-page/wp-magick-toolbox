<?php

/**
 * 效果：动态标题
 * 来源：
 */
if (!class_exists('Npcink_Page_Dynamic_Title')) {
    class Npcink_Page_Dynamic_Title
    {
        public static $option; //配置
        public static function run($config)
        {
            self::$option = $config;
            add_action('wp_footer', array(__CLASS__, 'tag_title'));
        }
        //动态标题
        public static function tag_title()
        {
            $title_front = MaMi_Admin::get_config(self::$option, 'title_front', "(/≧▽≦/)你又回来啦！");
            $title_after = MaMi_Admin::get_config(self::$option, 'title_after', "你别走吖 Σ(っ °Д °;)っ");
            echo '
    <script>
    //网站动态标题开始 
var OriginTitile = document.title,
titleTime;
document.addEventListener("visibilitychange",
function() {
    if (document.hidden) {
        document.title = "' . $title_after . '";
        clearTimeout(titleTime)
    } else {
        document.title = "' . $title_front . '" ;
        titleTime = setTimeout(function() {
            document.title = OriginTitile
        },
        2000)
    }
});
//网站动态标题结束
    </script>
    ';
        }
    }
}
