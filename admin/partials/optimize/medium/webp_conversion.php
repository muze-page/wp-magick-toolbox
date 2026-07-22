<?php

defined('ABSPATH') || exit;

/**
 * 将 WordPress 为 JPEG 生成的媒体输出保存为 WebP。
 *
 * 原始上传文件始终保留；格式支持、文件命名、元数据和失败回退均由
 * WordPress 图片编辑器负责。
 */
if (!class_exists('Npcink_Toolbox_Medium_Webp_Conversion')) {
    class Npcink_Toolbox_Medium_Webp_Conversion implements Npcink_Toolbox_Module_Interface
    {
        /**
         * @param array $config optimize.medium 配置。
         */
        public static function run($config = array())
        {
            if (empty($config['webp_conversion']) || !self::is_supported()) {
                return;
            }

            add_filter(
                'image_editor_output_format',
                array(__CLASS__, 'map_jpeg_to_webp'),
                10,
                3
            );
        }

        /**
         * 只映射 JPEG，保留 WordPress 与其他插件已有的格式映射。
         *
         * @param array $formats 现有输入 MIME 到输出 MIME 的映射。
         * @param string $filename 当前文件名。
         * @param string $mime_type 当前输入 MIME。
         * @return array
         */
        public static function map_jpeg_to_webp($formats, $filename = '', $mime_type = '')
        {
            if (!is_array($formats)) {
                $formats = array();
            }

            $formats['image/jpeg'] = 'image/webp';
            return $formats;
        }

        /**
         * 当前 WordPress 图片编辑器是否可以读写 WebP。
         */
        public static function is_supported()
        {
            return function_exists('wp_image_editor_supports')
                && wp_image_editor_supports(array('mime_type' => 'image/webp'));
        }
    }
}
