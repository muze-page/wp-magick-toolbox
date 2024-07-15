<?php

/**
 * 功能：足迹地图
 * 来源：https://github.com/HelloWuJiaYi/jVectorMap-Footprint
 */
if (!class_exists('MaBox_ShortCode_Merc_Map')) {
    class MaBox_ShortCode_Merc_Map
    {
        public static function run()
        {
            //添加短代码
            // add_shortcode('past_posts_display', array(__CLASS__, 'past_posts_display_shortcode'));

            // 判断当前页面是否有 mabox_copy_btn 短代码，如果有则加载 加载前端资源
            //add_action('wp_enqueue_scripts', function () {
            //    global $post;
            //    if (has_shortcode($post->post_content, 'past_posts_display')) {
            //        self::load_js();
            //    }
            //});
            add_action('wp_footer', array(__CLASS__, 'add_map_node'));
            add_action('wp_enqueue_scripts', array(__CLASS__, 'load_js'));
        }
        public static function add_map_node()
        {
?>
            <!--background-color: 地图背景颜色-->
            <div id="map" style="background-color:#f4f4f4"></div>
            <script>
                jQuery(document).ready(function($) {
                    $('#map').vectorMap({

                        // 此处更改地图
                        map: 'cn_merc_en', // 中国地图
                        //map: 'us_aea',     // 美国地图
                        //map: 'world_mill', // 世界地图


                        backgroundColor: 'transparent',
                        zoomMin: 0.9, // 鼠标缩放时的最小比例
                        zoomMax: 2.4, // 鼠标缩放时的最大比例
                        focusOn: {
                            x: 0.55,
                            y: 2,
                            scale: 0.9
                        },
                        regionStyle: {
                            initial: {
                                fill: '#e5e5e5', // 地图颜色
                                "fill-opacity": 1, // 省份（州）是否隐藏，鼠标滑动时显示; 1：显示，2：隐藏。
                                stroke: 'none',
                                "stroke-width": 0,
                                "stroke-opacity": 1
                            },
                            hover: {
                                fill: '#ccc', // 鼠标滑动至某省份的高亮颜色。
                                "fill-opacity": 0.8
                            },
                            selected: {
                                fill: 'yellow'
                            },
                            selectedHover: {}
                        },
                        markerStyle: {
                            initial: {
                                fill: '#fd8888', // 足迹位置的填充颜色
                                stroke: '#fff' // 足迹位置的描边颜色
                            },
                            hover: {
                                fill: '#fd2020', // 鼠标滑动至足迹位置后的填充颜色
                                stroke: '#fff', // 鼠标滑动至足迹位置后的描边颜色
                                "fill-opacity": 0.8
                            },
                        },
                        markers: [ // 足迹位置

                            // {latLng: [经度（保留两位小数）, 纬度（保留两位小数）], name: '城市名称'},
                            // 推荐查询经纬度网站：http://www.gpsspg.com/maps.htm
                            {
                                latLng: [31.40, 121.48],
                                name: '上海'
                            },
                            {
                                latLng: [39.09, 117.20],
                                name: '天津'
                            },
                            {
                                latLng: [22.54, 114.06],
                                name: '深圳'
                            },
                            // 河南
                            {
                                latLng: [34.75, 113.66],
                                name: '郑州'
                            },
                            {
                                latLng: [34.80, 114.31],
                                name: '开封'
                            },
                            // 山东
                            {
                                latLng: [37.46, 121.45],
                                name: '烟台'
                            },
                            {
                                latLng: [37.51, 122.12],
                                name: '威海'
                            },
                            // 特区
                            {
                                latLng: [22.32, 114.17],
                                name: '香港'
                            },
                            {
                                latLng: [22.19, 113.54],
                                name: '澳门'
                            },
                            //台湾
                            {
                                latLng: [25.04, 121.51],
                                name: '台北'
                            },
                            {
                                latLng: [24.94, 121.16],
                                name: '桃园'
                            },
                            {
                                latLng: [25.00, 121.37],
                                name: '新北'
                            },
                            {
                                latLng: [22.99, 120.20],
                                name: '台南'
                            },
                            {
                                latLng: [24.44, 118.36],
                                name: '金门'
                            },
                            // 福建
                            {
                                latLng: [24.52, 117.65],
                                name: '漳州'
                            },
                            {
                                latLng: [24.47, 118.08],
                                name: '厦门'
                            },
                            {
                                latLng: [24.44, 118.06],
                                name: '鼓浪屿'
                            },
                            {
                                latLng: [24.56, 118.33],
                                name: '大嶝岛'
                            },
                            {
                                latLng: [24.56, 118.39],
                                name: '小嶝岛'
                            },
                            // 河北
                            {
                                latLng: [38.04, 114.51],
                                name: '石家庄'
                            },
                            {
                                latLng: [37.06, 114.50],
                                name: '邢台'
                            },
                            {
                                latLng: [36.63, 114.54],
                                name: '邯郸'
                            },
                            {
                                latLng: [39.85, 116.38],
                                name: '衡水'
                            },
                            {
                                latLng: [38.87, 115.46],
                                name: '保定'
                            },
                            {
                                latLng: [39.54, 116.68],
                                name: '廊坊'
                            },
                            {
                                latLng: [40.77, 114.89],
                                name: '张家口'
                            },
                            {
                                latLng: [39.63, 118.18],
                                name: '唐山'
                            },
                            // 浙江
                            {
                                latLng: [30.20, 120.21],
                                name: '杭州'
                            },
                            // 辽宁
                            {
                                latLng: [38.91, 121.61],
                                name: '大连'
                            },
                            //宁夏
                            {
                                latLng: [38.49, 106.23],
                                name: '银川'
                            },
                            {
                                latLng: [37.50, 105.20],
                                name: '中卫'
                            },
                            // 四川
                            {
                                latLng: [30.65, 104.10],
                                name: '成都'
                            },
                            {
                                latLng: [29.55, 103.77],
                                name: '乐山'
                            },
                            {
                                latLng: [29.55, 103.34],
                                name: '峨眉山'
                            },
                            //重庆
                            {
                                latLng: [29.56, 106.55],
                                name: '重庆'
                            },
                            // 安徽
                            {
                                latLng: [30.67, 117.50],
                                name: '池州'
                            },
                            {
                                latLng: [30.27, 118.14],
                                name: '黄山'
                            },
                            {
                                latLng: [30.47, 117.83],
                                name: '九华山'
                            },
                            // 北京
                            {
                                latLng: [40.22, 116.23],
                                name: '北京'
                            },
                            // 广东
                            {
                                latLng: [23.15, 113.27],
                                name: '广州'
                            },
                            {
                                latLng: [34.52, 109.47],
                                name: '渭南'
                            },



                        ]
                    });
                });
            </script>
<?php
        }

        //加载JS
        public static function load_js()
        {
            //判断下，是否在前端页中
            if (is_admin()) {
                return;
            }

            //准备css
            $build_css =  plugin_dir_url(__DIR__) . 'merc_map/jquery-jvectormap-1.2.2.css';
            wp_enqueue_style(
                MAGICK_MIXTURE_NAME . '_public_merc_map_css',
                $build_css,
                array(),
                MAGICK_MIXTURE_VERSION,
                false
            );
            //准备js 
            $build_js =  plugin_dir_url(__DIR__) . 'merc_map/jquery-jvectormap-1.2.2.min.js';
            wp_enqueue_script(
                MAGICK_MIXTURE_NAME . '_public_jvectormap_js',
                $build_js,
                array('jquery'),
                MAGICK_MIXTURE_VERSION,
                false
            );
            //准备js 
            $merc_js =  plugin_dir_url(__DIR__) . 'merc_map/jquery-jvectormap-cn-merc-en.js';
            wp_enqueue_script(
                MAGICK_MIXTURE_NAME . '_public_cn-merc_js',
                $merc_js,
                array('jquery'),
                MAGICK_MIXTURE_VERSION,
                false
            );
        }
    }
}
