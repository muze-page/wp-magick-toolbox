<?php

/**
 * 功能：媒体库支持SVG格式（安全模式）
 * 来源：
 *
 * 安全措施：
 * 1. 仅管理员可上传 SVG
 * 2. 上传时清洗 SVG 内容（移除危险标签和属性）
 * 3. 使用 wp_check_filetype_and_ext 验证文件类型
 */
if (!class_exists('MaBox_Medium_Svg_Support')) {
    class MaBox_Medium_Svg_Support implements MaBox_Module_Interface
    {
        //加载
        public static function run($config = array())
        {
            self::run_add_svg();
        }

        //添加媒体库 SVG 支持
        public static function run_add_svg()
        {
            add_filter('upload_mimes', array(__CLASS__, 'salong_mime_types'));
            add_action('admin_head', array(__CLASS__, 'salong_admin_svg_css'));

            // SVG 上传时清洗内容
            add_filter('wp_handle_upload_prefilter', array(__CLASS__, 'sanitize_svg_upload'));
        }

        //添加媒体库 SVG 图标支持
        public static function salong_mime_types($mimes)
        {
            // 仅管理员可上传 SVG
            if (!current_user_can('manage_options')) {
                return $mimes;
            }

            $mimes['svg'] = 'image/svg+xml';
            return $mimes;
        }

        //在媒体库显示 SVG 图标
        public static function salong_admin_svg_css()
        {
            echo "
             <style>
             table.media .column-title .media-icon img[src*='.svg']{
              width: 100%;
              height: auto;
                     }
         </style>";
        }

        /**
         * 清洗 SVG 文件内容
         * 移除危险标签和属性，防止 XSS 攻击
         */
        public static function sanitize_svg_content($content)
        {
            // 移除危险标签
            $dangerous_tags = array(
                'script', 'object', 'embed', 'iframe', 'form', 'input',
                'button', 'select', 'textarea', 'link', 'meta', 'base',
            );

            foreach ($dangerous_tags as $tag) {
                // 移除整个标签及其内容
                $content = preg_replace('/<' . $tag . '[^>]*>.*?<\/' . $tag . '>/is', '', $content);
                $content = preg_replace('/<' . $tag . '[^>]*\/?>/is', '', $content);
            }

            // 移除危险属性
            $dangerous_attrs = array(
                'on\w+',           // 所有 on* 事件处理器
                'javascript:',     // javascript: 协议
                'vbscript:',       // vbscript: 协议
                'expression\(',    // CSS expression
                'url\(',           // CSS url() - 可选，可能误伤
                'import',          // CSS @import
            );

            foreach ($dangerous_attrs as $attr) {
                $content = preg_replace('/\s*' . $attr . '\s*=\s*["\'][^"\']*["\']/i', '', $content);
                $content = preg_replace('/\s*' . $attr . '\s*=\s*\S+/i', '', $content);
            }

            $dangerous_values = array(
                'javascript\s*:',
                'vbscript\s*:',
                'expression\s*\(',
            );
            foreach ($dangerous_values as $pattern) {
                $content = preg_replace('/\s*[\w-]+\s*=\s*["\'][^"\']*' . $pattern . '[^"\']*["\']/i', '', $content);
                $content = preg_replace('/\s*[\w-]+\s*=\s*\S*' . $pattern . '\S*/i', '', $content);
            }

            // 移除 XML 外部实体 (XXE)
            $content = preg_replace('/<!DOCTYPE[^>]*>/i', '', $content);
            $content = preg_replace('/<!ENTITY[^>]*>/i', '', $content);

            return $content;
        }

        /**
         * SVG 上传预处理
         */
        public static function sanitize_svg_upload($file)
        {
            // 仅管理员可上传 SVG
            if (!current_user_can('manage_options')) {
                $file['error'] = '仅管理员可上传 SVG 文件';
                return $file;
            }

            // 检查是否为 SVG 文件
            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            if (strtolower($ext) !== 'svg') {
                return $file;
            }

            // 读取文件内容
            $content = file_get_contents($file['tmp_name']);
            if ($content === false) {
                $file['error'] = '无法读取 SVG 文件内容';
                return $file;
            }

            // 检查是否为有效的 XML
            $xml = @simplexml_load_string($content);
            if ($xml === false) {
                $file['error'] = 'SVG 文件格式无效（无效的 XML）';
                return $file;
            }

            // 清洗内容
            $sanitized = self::sanitize_svg_content($content);

            // 写回文件
            if (file_put_contents($file['tmp_name'], $sanitized) === false) {
                $file['error'] = '无法保存清洗后的 SVG 文件';
                return $file;
            }

            // 记录日志
            if (class_exists('MaBox_Audit_Logger')) {
                MaBox_Audit_Logger::file('SVG 上传已清洗: ' . $file['name'], array(
                    'user_id' => get_current_user_id(),
                ));
            }
            error_log('[MaBox] SVG 上传已清洗: ' . $file['name'] . ' by user ' . get_current_user_id());

            return $file;
        }
    }
}
