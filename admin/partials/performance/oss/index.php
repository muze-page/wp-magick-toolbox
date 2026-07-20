<?php
defined('ABSPATH') || exit;
if (!class_exists('Npcink_Toolbox_Performance_Oss')) {
    class Npcink_Toolbox_Performance_Oss implements Npcink_Toolbox_Module_Interface {
        private const OFFLOADED_META = '_npcink_site_toolbox_oss_offloaded';

        private static $config;

        public static function run($config = array()) {
            self::$config = $config;
            if (empty($config['enabled'])) return;
            add_filter('wp_generate_attachment_metadata', array(__CLASS__, 'sync_attachment_to_oss'), 20, 3);
            add_filter('wp_get_attachment_url', array(__CLASS__, 'replace_attachment_url'), 10, 2);
            add_filter('wp_calculate_image_srcset', array(__CLASS__, 'replace_srcset_urls'), 10, 5);
        }

        public static function sync_attachment_to_oss($metadata, $attachment_id, $context = '') {
            delete_post_meta($attachment_id, self::OFFLOADED_META);

            $files = self::collect_attachment_files($attachment_id, $metadata);
            if (empty($files)) {
                return $metadata;
            }

            $provider = !empty(self::$config['provider']) ? self::$config['provider'] : 'aliyun';
            foreach ($files as $file) {
                $result = self::do_upload($file, $provider);
                if (!$result || is_wp_error($result)) {
                    return $metadata;
                }
            }

            update_post_meta($attachment_id, self::OFFLOADED_META, self::target_fingerprint());
            return $metadata;
        }

        public static function replace_attachment_url($url, $post_id) {
            $domain = !empty(self::$config['domain']) ? self::$config['domain'] : '';
            if (empty($domain) || empty($post_id) || !self::is_offloaded_to_current_target($post_id)) {
                return $url;
            }

            $upload_dir = wp_upload_dir();
            $baseurl = $upload_dir['baseurl'];
            if (strpos($url, $baseurl) === 0) {
                $path = substr($url, strlen($baseurl));
                return rtrim($domain, '/') . $path;
            }
            return $url;
        }

        public static function replace_srcset_urls($sources, $size_array, $image_src, $image_meta, $attachment_id) {
            $domain = !empty(self::$config['domain']) ? self::$config['domain'] : '';
            if (empty($domain) || !is_array($sources) || !self::is_offloaded_to_current_target($attachment_id)) {
                return $sources;
            }

            foreach ($sources as &$source) {
                if (!empty($source['url'])) {
                    $source['url'] = self::replace_attachment_url($source['url'], $attachment_id);
                }
            }
            unset($source);

            return $sources;
        }

        private static function is_offloaded_to_current_target($attachment_id) {
            $stored_fingerprint = get_post_meta($attachment_id, self::OFFLOADED_META, true);
            return is_string($stored_fingerprint)
                && $stored_fingerprint !== ''
                && hash_equals(self::target_fingerprint(), $stored_fingerprint);
        }

        private static function target_fingerprint() {
            $provider = !empty(self::$config['provider']) ? self::$config['provider'] : 'aliyun';
            $bucket = !empty(self::$config['bucket']) ? self::$config['bucket'] : '';
            $region = !empty(self::$config['region']) ? self::$config['region'] : '';
            $domain = !empty(self::$config['domain']) ? rtrim(self::$config['domain'], '/') : '';

            return hash('sha256', implode("\n", array($provider, $bucket, $region, $domain)));
        }

        private static function collect_attachment_files($attachment_id, $metadata) {
            $main_file = get_attached_file($attachment_id);
            if (!is_string($main_file) || $main_file === '') {
                return array();
            }

            $files = array($main_file);
            $attachment_dir = dirname($main_file);

            if (is_array($metadata)) {
                if (!empty($metadata['original_image']) && is_string($metadata['original_image'])) {
                    $files[] = $attachment_dir . '/' . $metadata['original_image'];
                }

                if (!empty($metadata['sizes']) && is_array($metadata['sizes'])) {
                    foreach ($metadata['sizes'] as $size) {
                        if (is_array($size) && !empty($size['file']) && is_string($size['file'])) {
                            $files[] = $attachment_dir . '/' . $size['file'];
                        }
                    }
                }
            }

            $upload_dir = wp_upload_dir();
            $upload_root = realpath($upload_dir['basedir']);
            if ($upload_root === false) {
                return array();
            }

            $upload_root = trailingslashit(wp_normalize_path($upload_root));
            $validated_files = array();
            foreach (array_unique($files) as $file) {
                $real_file = realpath($file);
                if ($real_file === false || !is_file($real_file) || !is_readable($real_file)) {
                    return array();
                }

                $normalized_file = wp_normalize_path($real_file);
                if (strpos($normalized_file, $upload_root) !== 0) {
                    return array();
                }

                $validated_files[] = $real_file;
            }

            return $validated_files;
        }

        private static function do_upload($file, $provider) {
            $domain = !empty(self::$config['domain']) ? self::$config['domain'] : '';
            $bucket = !empty(self::$config['bucket']) ? self::$config['bucket'] : '';
            $region = !empty(self::$config['region']) ? self::$config['region'] : '';
            $access_key = !empty(self::$config['access_key']) ? self::$config['access_key'] : '';
            $secret_key = !empty(self::$config['secret_key']) ? self::$config['secret_key'] : '';
            if (empty($domain) || empty($bucket) || empty($access_key) || empty($secret_key)) {
                return false;
            }

            $upload_dir = wp_upload_dir();
            $upload_root = trailingslashit(wp_normalize_path($upload_dir['basedir']));
            $normalized_file = wp_normalize_path($file);
            if (strpos($normalized_file, $upload_root) !== 0) {
                return false;
            }

            $object_key = ltrim(substr($normalized_file, strlen($upload_root)), '/');
            if ($object_key === '') {
                return false;
            }

            $file_content = file_get_contents($file);
            if ($file_content === false) return false;

            if ($provider === 'aliyun') {
                return self::upload_aliyun($file_content, $object_key, $access_key, $secret_key, $bucket, $region, $domain);
            } elseif ($provider === 'tencent') {
                return self::upload_tencent($file_content, $object_key, $access_key, $secret_key, $bucket, $region, $domain);
            } elseif ($provider === 'qiniu') {
                return self::upload_qiniu($file_content, $object_key, $access_key, $secret_key, $bucket, $domain);
            }
            return false;
        }
        private static function upload_aliyun($content, $key, $ak, $sk, $bucket, $region, $domain) {
            $host = $bucket . '.oss-' . $region . '.aliyuncs.com';
            $date = gmdate('D, d M Y H:i:s T');
            $sign_str = "PUT\n\napplication/octet-stream\n" . $date . "\n/" . $bucket . "/" . $key;
            $signature = base64_encode(hash_hmac('sha1', $sign_str, $sk, true));
            $auth = 'OSS ' . $ak . ':' . $signature;
            $response = wp_remote_request('https://' . $host . '/' . $key, array(
                'method'  => 'PUT',
                'body'    => $content,
                'headers' => array(
                    'Host'           => $host,
                    'Date'           => $date,
                    'Authorization'  => $auth,
                    'Content-Type'   => 'application/octet-stream',
                ),
                'timeout' => 60,
            ));
            if (!is_wp_error($response) && wp_remote_retrieve_response_code($response) === 200) {
                return rtrim($domain, '/') . '/' . $key;
            }
            return false;
        }
        private static function upload_tencent($content, $key, $ak, $sk, $bucket, $region, $domain) {
            $host = $bucket . '.cos.' . $region . '.myqcloud.com';
            $request_path = self::encode_object_key($key);
            $start_time = time();
            $end_time = $start_time + 3600;
            $auth = self::build_tencent_authorization(
                'PUT',
                $request_path,
                $host,
                $ak,
                $sk,
                $start_time,
                $end_time
            );
            $response = wp_remote_request('https://' . $host . $request_path, array(
                'method'  => 'PUT',
                'body'    => $content,
                'headers' => array(
                    'Host'           => $host,
                    'Authorization'  => $auth,
                    'Content-Type'   => 'application/octet-stream',
                ),
                'timeout' => 60,
            ));
            if (!is_wp_error($response) && wp_remote_retrieve_response_code($response) === 200) {
                return rtrim($domain, '/') . '/' . $key;
            }
            return false;
        }

        private static function build_tencent_authorization($method, $path, $host, $ak, $sk, $start_time, $end_time) {
            $key_time = $start_time . ';' . $end_time;
            $http_headers = 'host=' . rawurlencode(strtolower($host));
            $http_string = strtolower($method) . "\n" . urldecode($path) . "\n\n" . $http_headers . "\n";
            $sign_key = hash_hmac('sha1', $key_time, $sk);
            $string_to_sign = "sha1\n" . $key_time . "\n" . sha1($http_string) . "\n";
            $signature = hash_hmac('sha1', $string_to_sign, $sign_key);

            return 'q-sign-algorithm=sha1'
                . '&q-ak=' . rawurlencode($ak)
                . '&q-sign-time=' . $key_time
                . '&q-key-time=' . $key_time
                . '&q-header-list=host'
                . '&q-url-param-list='
                . '&q-signature=' . $signature;
        }

        private static function encode_object_key($key) {
            $segments = explode('/', ltrim($key, '/'));
            return '/' . implode('/', array_map('rawurlencode', $segments));
        }

        private static function upload_qiniu($content, $key, $ak, $sk, $bucket, $domain) {
            $upload_url = 'https://up.qiniup.com/';
            $token = self::qiniu_token($bucket, $ak, $sk);
            $boundary = wp_generate_password(24, false);
            $body = "--" . $boundary . "\r\n";
            $body .= "Content-Disposition: form-data; name=\"token\"\r\n\r\n" . $token . "\r\n";
            $body .= "--" . $boundary . "\r\n";
            $body .= "Content-Disposition: form-data; name=\"key\"\r\n\r\n" . $key . "\r\n";
            $body .= "--" . $boundary . "\r\n";
            $body .= "Content-Disposition: form-data; name=\"file\"; filename=\"" . basename($key) . "\"\r\n";
            $body .= "Content-Type: application/octet-stream\r\n\r\n" . $content . "\r\n";
            $body .= "--" . $boundary . "--";
            $response = wp_remote_post($upload_url, array(
                'body'    => $body,
                'headers' => array('Content-Type' => 'multipart/form-data; boundary=' . $boundary),
                'timeout' => 60,
            ));
            if (!is_wp_error($response)) {
                $code = wp_remote_retrieve_response_code($response);
                if ($code === 200) {
                    return rtrim($domain, '/') . '/' . $key;
                }
            }
            return false;
        }
        private static function qiniu_token($bucket, $ak, $sk) {
            $policy = json_encode(array('scope' => $bucket, 'deadline' => time() + 3600));
            $encoded_policy = self::qiniu_base64_url_safe($policy);
            $sign = hash_hmac('sha1', $encoded_policy, $sk, true);
            $encoded_sign = self::qiniu_base64_url_safe($sign);
            return $ak . ':' . $encoded_sign . ':' . $encoded_policy;
        }
        private static function qiniu_base64_url_safe($data) {
            $encoded = base64_encode($data);
            return str_replace(array('+', '/'), array('-', '_'), $encoded);
        }
    }
}
