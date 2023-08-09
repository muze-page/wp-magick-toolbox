<?php

/**
 * 权限 辅助
 */
if (!class_exists('MaMi_Auxiliary')) {
    class MaMi_Auxiliary
    {
        //加载
        public static function run($config)
        {
            //获取选项 - 禁用
            $option =  MaMi_Admin::get_config($config, 'disable');

            //禁用更新
            $renew = MaMi_Admin::get_config($option, 'renew');
            if ($renew) {
                self::run_ban_update();
            }
            //未登录模糊文章内图片
            $no_login_img = MaMi_Admin::get_config($option, 'no_login_img');
            if ($no_login_img) {
                //判断，没有登录
                if (!is_user_logged_in()) {
                    add_action('wp_footer', array(__CLASS__, 'n_yingcang_css'));
                }
            }

            //获取选项 - 功能
            $auxiliary =  MaMi_Admin::get_config($config, 'auxiliary');
        }



        /**
         * 效果：禁用更新
         * 来源：https://www.npc.ink/15932.html
         */
        public static function run_ban_update()
        {
            remove_action('init', 'wp_schedule_update_checks'); // 关闭更新检查定时作业
            wp_clear_scheduled_hook('wp_version_check'); // 移除已有的版本检查定时作业
            wp_clear_scheduled_hook('wp_update_plugins'); // 移除已有的插件更新定时作业
            wp_clear_scheduled_hook('wp_update_themes'); // 移除已有的主题更新定时作业
            wp_clear_scheduled_hook('wp_maybe_auto_update'); // 移除已有的自动更新定时作业
            add_filter('automatic_updater_disabled', '__return_true'); // 彻底关闭自动更新
            remove_action('admin_init', '_maybe_update_core'); // 移除后台内核更新检查
            remove_action('load-plugins.php', 'wp_update_plugins'); // 移除后台插件更新检查
            remove_action('load-update.php', 'wp_update_plugins');
            remove_action('load-update-core.php', 'wp_update_plugins');
            remove_action('admin_init', '_maybe_update_plugins');
            remove_action('load-themes.php', 'wp_update_themes'); // 移除后台主题更新检查
            remove_action('load-update.php', 'wp_update_themes');
            remove_action('load-update-core.php', 'wp_update_themes');
            remove_action('admin_init', '_maybe_update_themes');
        }



        /**
         * 未登录模糊文章内图片
         */
        public static function n_yingcang_css()
        {
            echo '<style>
            /*仅模糊文章内图片*/
            .entry-content img {
            -webkit-filter: blur(10px)!important;
              -moz-filter: blur(10px)!important;
              -ms-filter: blur(10px)!important;
              filter: blur(6px)!important;}
              .entry-content img:before{
                content:"登录可见";
              }
              </style>';
        }
    } //end
}
