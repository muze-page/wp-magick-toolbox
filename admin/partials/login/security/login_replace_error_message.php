
<?php
/**
 * 作用：覆盖默认登录错误提示信息
 * 来源：https://rudrastyh.com/wordpress/11-security-steps.html
 */
if (!class_exists('Npcink_Login_Replace_Error_Message')) {
    class Npcink_Login_Replace_Error_Message
    {
        public static function run()
        {

            add_filter('login_errors', array(__CLASS__, 'remove_default_login_errors'));
        }


        public static function remove_default_login_errors()
        {
            return '<span class="dashicons dashicons-info-outline" style="
            color: #d63638;
            margin: 0 6px;
        "></span><strong>错误</strong>：您输入的信息不正确，请检查后输入';
        }
    }
}
