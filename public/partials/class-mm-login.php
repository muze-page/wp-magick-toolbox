<?php
/**
 * 效果：美化Wordpress登录页
 * 原文地址：https://www.iowen.cn/chundaimameihuawordpressmorendengluye/
 */
if (!class_exists('Magick_Mixtrue_Login')) {
    class Magick_Mixtrue_Login
    {
        public function __construct()
        {

        }
        public static function run()
        {
            add_action('init', array(__CLASS__, 'run_iowen'));
        }

        public static function run_iowen()
        {
            if (carbon_get_theme_option('cmma_abt_style_login')) {

                add_action('login_header', array(__CLASS__, 'io_login_header'));
                add_action('login_footer', array(__CLASS__, 'io_login_footer'));
                add_action('login_head', array(__CLASS__, 'custom_login_style'));
                //加载css
                add_action('login_enqueue_scripts', array(__CLASS__, 'load_css'));
            }

        }
        /**
         * 加载css
         */
        public static function load_css()
        {
            wp_enqueue_style(
                MAGICK_MIXTURE_NAME . '_style-login-css',
                plugin_dir_url(\dirname(__FILE__)) . 'css/style-login.css',
                array(),
                MAGICK_MIXTURE_VERSION,
                'all'
            );
        }
        public static function io_login_header()
        {
            echo '<div class="login-container">
    <div class="login-body">
        <div class="login-img shadow-lg position-relative flex-fill">
            <div class="img-bg position-absolute">
                <div class="login-info">
                    <h2>' . get_bloginfo('name') . '</h2>
                    <p>' . get_bloginfo('description') . '</p>
                </div>
            </div>
        </div>';
        }
        public static function io_login_footer()
        {
            echo '</div><!--login-body END-->
    </div><!--login-container END-->
    <div class="footer-copyright position-absolute">
            <span>Copyright © <a href="' . esc_url(home_url()) . '" class="text-white-50" title="' . get_bloginfo('name') . '" rel="home">' . get_bloginfo('name') . '</a></span>
    </div>';
        }

        public static function custom_login_style()
        {
            //左下背景色
            $bg_left = carbon_get_theme_option('cmma_opt_login_bgcolor_left');
            //右上背景色
            $bg_right = carbon_get_theme_option('cmma_opt_login_bgcolor_right');
            //LOGO
            $logo_url = carbon_get_theme_option('cmma_opt_login_logo');
            //尺寸
            $logo_size = carbon_get_theme_option('cmma_opt_login_logo_size');
            //左边文字背景图
            $bg_img_left = carbon_get_theme_option('cmma_opt_login_bg_left');
            echo '<style type="text/css">
    body{
        background:-o-linear-gradient(45deg,' . $bg_left . ',' . $bg_right . ');
        background:linear-gradient(45deg,' . $bg_left . ',' . $bg_right . ');
        height:100vh;
    }
    .login h1 a{
        background-image:url(' . $logo_url . ' );
        width:180px;
        background-position:center center;
        background-size:' . $logo_size . 'px;
    }
    .img-bg{
        color: #fff;
        padding: 2rem;
        bottom: -2rem;
        left: 0;
        top: -2rem;
        right: 0;
        border-radius: 10px;
        background-repeat: no-repeat;
        background-position: center center;
        background-size: cover;
        background-image:url(' . $bg_img_left . ');
       }

</style>';
        }
    }
}
