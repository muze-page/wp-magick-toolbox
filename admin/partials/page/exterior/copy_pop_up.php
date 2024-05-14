<?php

/**
 * 效果：复制时进行弹窗提示
 * 来源1：https://www.liitk.com/1098.html
 * 来源2：https://www.dqzboy.com/4672.html
 */
if (!class_exists('Npcink_Page_Copy_Pop_Up')) {
    class Npcink_Page_Copy_Pop_Up
    {

        public static function run($config)
        {
            //原生
            if ($config === "concise") {
                add_action('wp_footer', array(__CLASS__, 'concise'), 100);
            }
            //通用圆角
            if ($config === "sweetalert") {
                add_action('wp_enqueue_scripts', array(__CLASS__, 'add_page_sweetalert'));
                add_action('wp_footer', array(__CLASS__, 'jiub'), 100);
            }
        }

        //原生
        public static function concise()
        {
?>

            <script type="text/javascript">
                document.body.oncopy = function() {
                    alert('复制成功！若要转载请务必保留原文链接，谢谢合作！');
                }
            </script>

<?php
        }
        //复制成功提醒  
        public static function jiub()
        {
            echo '<script>document.body.oncopy = function() { swal("复制成功！", "转载请务必保留原文链接，申明来源，谢谢合作！！","success");};</script>';
        }

        //加载资源
        public static function add_page_sweetalert()
        {
            wp_enqueue_style(
                MAGICK_MIXTURE_NAME . '_sweetalert',
                plugin_dir_url(__FILE__) . 'project/sweetalert/sweetalert.min.css',
                array(),
                MAGICK_MIXTURE_VERSION,
                false
            );

            wp_enqueue_script(
                MAGICK_MIXTURE_NAME . '_sweetalert',
                plugin_dir_url(__FILE__) . 'project/sweetalert/sweetalert.min.js',
                array(),
                MAGICK_MIXTURE_VERSION,
                false
            );
        }
    }
}
