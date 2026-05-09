<?php

/**
 * 效果：生成微信小程序跳转链接和页面模版
 */
if (!class_exists('MaBox_Function_Wx_Xcx_Link')) {
    class MaBox_Function_Wx_Xcx_Link
    {
        //选项值
        private static $option;
        public static function run($wx_xcx)
        {
            self::$option = $wx_xcx;
            $active = MaBox_Admin::get_config(self::$option, 'active'); //状态
            if ($active) {
                //展示链接到首页顶部
                //add_action('wp_head', array(__CLASS__, 'add_hello_header'));

                //注册页面模版
                add_filter('theme_page_templates', array(__CLASS__, 'add_custom_page_template'));

                //置顶模版路径
                add_filter('template_include', array(__CLASS__, 'get_custom_template'));

                //添加接口
                add_action('rest_api_init', array(__CLASS__, 'mytheme_register_rest_endpoints'));
            }
        }

        public static function add_hello_header()
        {
            $appid = MaBox_Admin::get_config(self::$option, 'appid');
            $secret = MaBox_Admin::get_config(self::$option, 'secret');
            $path = MaBox_Admin::get_config(self::$option, 'path');
            $query = MaBox_Admin::get_config(self::$option, 'query');

            $token = self::wx_json_token_cached($appid, $secret);

            // 检查 token 是否获取成功
            if (is_wp_error($token)) {
                return $token;
            }

            $link = self::get_link($token, $path, $query);
            return $link;
        }

        public static function wx_json_token_cached($appid, $secret)
        {
            $cached = get_transient('mabox_wx_token');
            if ($cached) {
                return $cached;
            }

            $token = self::wx_json_token($appid, $secret);
            if (!is_wp_error($token) && !empty($token)) {
                set_transient('mabox_wx_token', $token, 7000);
            }
            return $token;
        }
        /**
         * 构造获取token的链接
         */
        public static  function wx_json_token($appid, $secret)
        {
            //构造链接
            $link = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=" . $appid . "&secret=" . $secret;

            if (!function_exists('curl_init') || !function_exists('curl_exec')) {
                return new \WP_Error('curl_missing', '服务器未安装 cURL 扩展，无法获取 Token');
            }

            $curl = curl_init();
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
            ]);


            // 抓取URL并把它传递给浏览器
            $response = curl_exec($curl);
            $err = curl_error($curl);

            // 关闭cURL资源，并且释放系统资源
            curl_close($curl);

            // 检查 cURL 错误
            if ($err) {
                return new \WP_Error('curl_error', '获取 Token 时发生网络错误: ' . $err);
            }

            // 检查响应是否为空
            if (empty($response)) {
                return new \WP_Error('empty_response', '微信 API 返回空响应');
            }

            //返回函数生成的内容
            //获取token文件
            //将数组变为变量
            $json_token = json_decode($response, true);

            // 检查 JSON 解析是否成功
            if (!is_array($json_token)) {
                return new \WP_Error('invalid_json', '微信 API 返回无效 JSON: ' . substr($response, 0, 200));
            }

            // 检查微信 API 是否返回错误
            if (isset($json_token['errcode']) && $json_token['errcode'] != 0) {
                $errmsg = isset($json_token['errmsg']) ? $json_token['errmsg'] : '未知错误';
                return new \WP_Error('wx_api_error', '微信 API 错误 [' . $json_token['errcode'] . ']: ' . $errmsg);
            }

            //拿到需要的值  expires_in为有效期2小时
            if (!isset($json_token['access_token'])) {
                return new \WP_Error('missing_token', '微信 API 响应中缺少 access_token');
            }
            $wx_token =  $json_token['access_token'];


            return $wx_token;
        }

        /**
         * 获取跳转小程序的链接
         */
        public static function get_link($token, $path, $query)
        {
            //获取小程序链接
            $xcx_url = 'https://api.weixin.qq.com/wxa/generatescheme?access_token=' . $token;


            $params = array(
                "jump_wxa" => array(
                    "path" => $path,
                    "query" => $query,
                    //"env_version"=>''
                ),

            );

            $data = json_encode($params);

            if (!function_exists('curl_init') || !function_exists('curl_exec')) {
                return new \WP_Error('curl_missing', '服务器未安装 cURL 扩展，无法生成跳转链接');
            }

            $curl = curl_init();

            curl_setopt_array($curl, [
                CURLOPT_URL => $xcx_url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => $data,
            ]);

            $response = curl_exec($curl);
            $err = curl_error($curl);

            curl_close($curl);

            // 检查 cURL 错误
            if ($err) {
                return new \WP_Error('curl_error', '生成小程序链接时发生网络错误: ' . $err);
            }

            // 检查响应是否为空
            if (empty($response)) {
                return new \WP_Error('empty_response', '微信 API 返回空响应');
            }

            //拿到小程序链接相关信息
            $json_url = json_decode($response, true);

            // 检查 JSON 解析是否成功
            if (!is_array($json_url)) {
                return new \WP_Error('invalid_json', '微信 API 返回无效 JSON: ' . substr($response, 0, 200));
            }

            // 检查微信 API 是否返回错误
            if (isset($json_url['errcode']) && $json_url['errcode'] != 0) {
                $errmsg = isset($json_url['errmsg']) ? $json_url['errmsg'] : '未知错误';
                return new \WP_Error('wx_api_error', '微信 API 错误 [' . $json_url['errcode'] . ']: ' . $errmsg);
            }

            //拿到需要的值
            if (!isset($json_url['openlink'])) {
                return new \WP_Error('missing_link', '微信 API 响应中缺少 openlink');
            }
            $wx_url =  $json_url['openlink'];
            return $wx_url;
        }
        /**
         * 添加单页
         */
        // 注册自定义页面模板
        public static function add_custom_page_template($templates)
        {
            $templates['custom-template.php'] = '微信小程序引导页';
            return $templates;
        }

        // 指定自定义页面模板的路径
        public static  function get_custom_template($template)
        {
            if (!is_singular() || !$template) {
                return $template;
            }

            $custom_template = get_post_meta(get_queried_object_id(), '_wp_page_template', true);
            if ('custom-template.php' === basename($custom_template)) {
                $template = plugin_dir_path(__FILE__) . 'custom-template.php';
            }

            return $template;
        }

        /**
         * 接口
         */
        public static function mytheme_register_rest_endpoints()
        {
            register_rest_route('wx_xcx/v1', 'qy', array(
                'methods' => 'GET',
                'callback' => array(__CLASS__, 'get_h5_options'),
                'permission_callback' => function () {
                    return current_user_can('manage_options');
                },
            ));
        }
        public static function get_h5_options()
        {
            $link = self::add_hello_header();

            // 检查是否返回错误
            if (is_wp_error($link)) {
                return new \WP_REST_Response(
                    array(
                        'success' => false,
                        'message' => $link->get_error_message(),
                    ),
                    500
                );
            }

            $data = array(
                "data" => $link,
            );

            return $data;
        }

        //传递网址选项
        public static function get_h5_options_site()
        {
            $site = MaBox_Admin::get_config(self::$option, 'site');
            return $site;
        }
    } //end
}
