<?php

/**
 * 效果：WordPress外链新窗口打开并使用php页面go跳转
 * 来源：https://www.dujin.org/12762.html
 */

if (!class_exists('Npcink_Jump_Middle_Page')) {
    class Npcink_Jump_Middle_Page
    {

        /**
         * 传来的页面类型
         */
        public static function run($page_type)
        {
            //改造文章中的链接
            add_filter('the_content', array(__CLASS__, 'the_content_nofollowss'), 999);

            //改造评论中的链接
            add_filter('get_comment_text', array(__CLASS__, 'the_content_nofollowss'), 999);

            //添加重定向
            register_activation_hook(__FILE__, array(__CLASS__, 'go_new_link'));
            add_action('init', array(__CLASS__, 'go_new_link'));

            //行动
            add_action('template_redirect', function () use ($page_type) {
                self::go_new_link_move($page_type);
            });
        }
        public static function jump_middle_page()
        {
        }
        /**
         * 跳转中间页
         */
        /**
         * WordPress外链新窗口打开并使用php页面go跳转 - 替换文章中的链接内容
         * https://www.dujin.org/12762.html
         */
        public static function the_content_nofollowss($content)
        {
            $pattern = '/<a(.*?)href="(.*?)"(.*?)>(.*?)<\/a>/';
            $content = preg_replace_callback($pattern, function ($matches) {
                $url = $matches[2];
                if (strpos($url, '://') !== false && strpos($url, home_url()) === false && !preg_match('/\.(jpg|jpeg|png|ico|bmp|gif|tiff)/i', $url)) {
                    $new_link = home_url('/go_to/?url=' . urlencode($url));
                    $replacement = '<a' . $matches[1] . 'href="' . $new_link . '"' . $matches[3] . ' rel="external nofollow" target="_blank">' . $matches[4] . '</a>';
                    return $replacement;
                }
                return $matches[0];
            }, $content);

            // TODO:处理纯链接内容 - 有问题
            //$content = preg_replace_callback('/(https?:\/\/[^\s]+)/i', function ($matches) {
            //    $url = $matches[1];
            //    if (strpos($url, home_url()) === false && !preg_match('/\.(jpg|jpeg|png|ico|bmp|gif|tiff)/i', $url)) {
            //        $new_link = home_url('/go_to/?url=' . urlencode($url));
            //        $replacement = '<a href="' . $new_link . '" rel="external nofollow" target="_blank">' . $url . '</a>';
            //        return $replacement;
            //    }
            //    return $matches[0];
            //}, $content);

            // 替换评论者填写了网站地址的链接TODO:未完成


            return $content;
        }
        //注册

        public static function go_new_link()
        {
            add_rewrite_rule(
                'go_to', // 设置你的链接格式，例如 /too/
                '', // 空字符串表示不指定自定义模板文件的路径
                'top'
            );
            //刷新规则
            flush_rewrite_rules();
        }
        /**
         * 传入中间页类型
         */
        public static  function go_new_link_move($page_type)
        {
            global $wp;


            if ($wp->request === 'go_to') {
                $path = plugin_dir_path(dirname(dirname(dirname(__FILE__))));

                switch ($page_type) {
                    case 'zhihu':
                        include $path . 'public/templant/go/zhihu.php'; // 知乎
                        break;
                    case 'tencent':
                        include $path . 'public/templant/go/tencent.php'; // 腾讯
                        break;
                    case 'shimo':
                        include $path . 'public/templant/go/shimo.php'; // 石墨文档
                        break;
                    case 'jianshu':
                        include $path . 'public/templant/go/jianshu.php'; // 简书
                        break;
                    case 'csdn':
                        include $path . 'public/templant/go/csdn.php'; // CSDN
                        break;
                    case 'wx_community':
                        include $path . 'public/templant/go/wx_community.php'; // 微信公众号社群
                        break;
                    default:
                        // 默认操作（如果 $page_type 的值不匹配上述任意一种情况）
                        include $path . 'public/templant/go/demo.php'; // 微信公众号社群
                }



                exit();
            }
        }
    }
}
