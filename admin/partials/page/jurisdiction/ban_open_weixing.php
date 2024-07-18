<?php

/**
 * 效果：禁止在微信中打开网页
 * 来源：
 */

if (!class_exists('Npcink_Page_Ban_Open_WeiXing')) {
    class Npcink_Page_Ban_Open_WeiXing
    {
        public static function run()
        {
            add_action('wp_footer', array(__CLASS__, 'add_js'));
        }

        //添加jS
        public static function add_js()
        {
?>
            <script>
                function is_weixn_qq() {
                    var ua = navigator.userAgent.toLowerCase();
                    if (ua.match(/MicroMessenger/i) == "micromessenger") {
                        alert('微信中打开');
                    } else {
                        console.log('非微信中打开');
                    }
                }
                is_weixn_qq();
            </script>
<?php
        }
    }
}
