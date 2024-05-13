<?php
//优化 媒体
if (!class_exists('MaMi_Optimize_Medium')) {
    class MaMi_Optimize_Medium
    {
        //加载
        public static function run($config)
        {
            //获取选项
            $option =  MaMi_Admin::get_config($config, 'medium');

            //自动给图片添加Alt标签
            $img_add_tag = MaMi_Admin::get_config($option, 'img_add_tag');
            if ($img_add_tag === true) {
                require_once plugin_dir_path(__FILE__) . 'image_add_tag.php';
                Npcink_Image_Add_Tag::run();
            }

            // 禁用自动生成的图片尺寸
            $no_auto_size = MaMi_Admin::get_config($option, 'no_auto_size');
            if ($no_auto_size) {
                self::run_ban_auto_size();
            }

            //添加媒体库 SVG 图标支持
            $medium_add_svg = MaMi_Admin::get_config($option, 'medium_add_svg');
            if ($medium_add_svg) {
                self::run_add_svg();
            }

            //媒体文件重命名
            $upload_auto_name = MaMi_Admin::get_config($option, 'upload_auto_name');
            switch ($upload_auto_name) {
                    //时间
                case 'math':
                    add_filter('wp_handle_upload_prefilter', array(__CLASS__, 'custom_upload_filter_time'));
                    break;
                    //md5重命名
                case 'md5':
                    add_filter('wp_handle_upload_prefilter', array(__CLASS__, 'custom_upload_filter_md5'));
                    break;
                    //默认值
                default:
                    return;
            }
        }

       

        // 禁用自动生成的图片尺寸
        public static function run_ban_auto_size()
        {

            // 禁用自动生成的图片尺寸
            add_action('intermediate_image_sizes_advanced', array(__CLASS__, 'shapeSpace_disable_image_sizes'));
            // 禁用缩放尺寸
            add_filter('big_image_size_threshold', '__return_false');
            // 禁用其他图片尺寸
            add_action('init', array(__CLASS__, 'shapeSpace_disable_other_image_sizes'));
        }

        // 禁用自动生成的图片尺寸
        public static function shapeSpace_disable_image_sizes($sizes)
        {
            unset($sizes['thumbnail']); // disable thumbnail size
            unset($sizes['medium']); // disable medium size
            unset($sizes['large']); // disable large size
            unset($sizes['medium_large']); // disable medium-large size
            unset($sizes['1536x1536']); // disable 2x medium-large size
            unset($sizes['2048x2048']); // disable 2x large size return $sizes;
        }

        // 禁用其他图片尺寸
        public static function shapeSpace_disable_other_image_sizes()
        {
            remove_image_size('post-thumbnail');
            // 禁用通过 set_post_thumbnail_size()  添加的图像
            remove_image_size('another-size');
            // 禁用任何其他添加的图像大小
        }

        //添加媒体库 SVG 图标支持
        public static function run_add_svg()
        {
            add_filter('upload_mimes', array(__CLASS__, 'salong_mime_types'));
            add_action('admin_head', array(__CLASS__, 'salong_admin_svg_css'));
        }

        //添加媒体库 SVG 图标支持
        public static function salong_mime_types($mimes)
        {
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
         * 重命名
         */

        /*图片按时间自动重命名*/
        public static function custom_upload_filter_time($file)
        {
            $info = pathinfo($file['name']);
            $ext = $info['extension'];
            $filedate = date('YmdHis') . rand(10, 99); //为了避免时间重复，再加一段2位的随机数
            $file['name'] = $filedate . '.' . $ext;
            return $file;
        }

        /*使用md5转码重命名媒体文件名*/
        public static function custom_upload_filter_md5($file)
        {
            $info = pathinfo($file['name']);
            $ext = '.' . $info['extension'];
            $md5 = md5($file['name']);
            $file['name'] = $md5 . $ext;
            return $file;
        }
    } //end
}
