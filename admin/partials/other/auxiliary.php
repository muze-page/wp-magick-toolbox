<?php

/**
 * 权限 辅助
 */
if (!class_exists('MaMi_Auxiliary_Index')) {
    class MaMi_Auxiliary_Index
    {

        private static $auxiliary; //辅助功能
        //加载
        //加载文件
        public static function load()
        {
            //文章统计页面
            require_once plugin_dir_path(__FILE__) . '/block/census-single.php';

            //登录验证码
            require_once plugin_dir_path(__FILE__) . '/block/login_verify.php';
        }
        public static function run($auxiliary)
        {
            //加载文件
            self::load();
            //获取选项 - 辅助功能
            // $auxiliary =  MaMi_Admin::get_config($config, 'auxiliary');
            self::$auxiliary = $auxiliary;

            //加载文章统计
            $single_count = MaMi_Admin::get_config($auxiliary, 'single_count');
            if ($single_count) {

                Magick_Mixtrue_Census_Single::run();
            }



            //屏蔽恶意关键词搜索
            $no_malice_key = MaMi_Admin::get_config($auxiliary, 'no_malice_key');
            if ($no_malice_key) {
                add_action('template_redirect', array(__CLASS__, 'ytkah_search_ban'));
            }

            //登录验证码
            $login_code = MaMi_Admin::get_config($auxiliary, 'login_code');
            if ($login_code !== "false") {
                MaMi_Login_Verify::run($login_code);
            }
        }
        //屏蔽恶意关键词搜索
        public static function ytkah_search_ban()
        {
            $malice_keu_content = MaMi_Admin::get_config(self::$auxiliary, 'malice_keu_content');

            if (is_search()) {
                global $wp_query;
                //拿到输入的值
                $ytkah_search_key = $malice_keu_content;
                if ($ytkah_search_key) {
                    $ytkah_search_key = str_replace("\n", "|", $ytkah_search_key);
                    $BanKey = explode('|', $ytkah_search_key);
                    $S_Key = $wp_query->query_vars;
                    foreach ($BanKey as $Key) {
                        if (stristr($S_Key['s'], $Key) != false) {
                            $message = '搜索内容包含敏感词，请换个方式搜索';
                            $message = $message . MaMi_Admin::blank_button();
                            wp_die($message);
                        }
                    }
                }
            }
        }
    }
}
