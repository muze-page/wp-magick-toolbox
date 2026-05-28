<?php

defined('ABSPATH') || exit;

if (!class_exists('MaBox_ShortCode_Merc_Map')) {
    class MaBox_ShortCode_Merc_Map implements MaBox_Module_Interface
    {
        public static $location;
        private static $assets_url;

        public static function run($merc_location = array())
        {
            self::$location = $merc_location;
            self::$assets_url = plugin_dir_url(__FILE__) . 'assets/';
            add_shortcode('mabox_cn_map', array(__CLASS__, 'mabox_cn_map_shortcode'));
            add_action('wp', array(__CLASS__, 'check_for_mabox_cn_map_shortcode'));
        }

        public static function check_for_mabox_cn_map_shortcode()
        {
            global $post;
            if (!is_singular() || !has_shortcode($post->post_content, 'mabox_cn_map')) {
                return;
            }
            add_action('wp_footer', array(__CLASS__, 'add_map_script'));
            add_action('wp_enqueue_scripts', array(__CLASS__, 'load_echarts'));
        }

        public static function mabox_cn_map_shortcode($atts, $content = null)
        {
            $atts = shortcode_atts(array(
                'height' => '550px',
                'background' => '#f4f4f4',
            ), $atts);
            $uid = 'mabox-cn-map-' . uniqid();
            ob_start();
            ?>
            <div id="<?php echo esc_attr($uid); ?>" class="mabox-cn-map" style="width:100%;height:<?php echo esc_attr($atts['height']); ?>;background:<?php echo esc_attr($atts['background']); ?>;" data-locations="<?php echo esc_attr(wp_json_encode(self::$location)); ?>"></div>
            <?php
            return ob_get_clean();
        }

        public static function add_map_script()
        {
            $geo_url = esc_url(self::$assets_url . 'china.json');
            ?>
            <script>
            document.addEventListener('DOMContentLoaded', function() {
                var geoUrl = <?php echo wp_json_encode($geo_url); ?>;
                fetch(geoUrl)
                    .then(function(res) { return res.json(); })
                    .then(function(geoJson) {
                        echarts.registerMap('china', geoJson);
                        document.querySelectorAll('.mabox-cn-map').forEach(function(el) {
                            var locations = JSON.parse(el.getAttribute('data-locations') || '[]');
                            var scatterData = locations.map(function(loc) {
                                if (Array.isArray(loc.latLng) && loc.latLng.length >= 2) {
                                    var lat = parseFloat(loc.latLng[0]);
                                    var lng = parseFloat(loc.latLng[1]);
                                    if (!isNaN(lat) && !isNaN(lng) && lat >= -90 && lat <= 90 && lng >= -180 && lng <= 180) {
                                        return { name: loc.name || '', value: [lng, lat, 1] };
                                    }
                                }
                                return null;
                            }).filter(Boolean);
                            var chart = echarts.init(el);
                            var option = {
                                tooltip: { trigger: 'item', formatter: function(p) { return p.name; } },
                                geo: {
                                    map: 'china',
                                    roam: true,
                                    zoom: 1.2,
                                    scaleLimit: { min: 0.9, max: 5 },
                                    center: [104.5, 36],
                                    itemStyle: {
                                        areaColor: '#e5e5e5',
                                        borderColor: '#ccc'
                                    },
                                    emphasis: {
                                        itemStyle: { areaColor: '#ddd' }
                                    }
                                },
                                series: [{
                                    type: 'scatter',
                                    coordinateSystem: 'geo',
                                    data: scatterData,
                                    symbolSize: 12,
                                    itemStyle: { color: '#fd8888', borderColor: '#fff', borderWidth: 1 },
                                    emphasis: { itemStyle: { color: '#fd2020' } }
                                }]
                            };
                            chart.setOption(option);
                            window.addEventListener('resize', function() { chart.resize(); });
                        });
                    });
            });
            </script>
            <?php
        }

        public static function load_echarts()
        {
            if (is_admin()) {
                return;
            }
            wp_enqueue_script(
                MAGICK_MIXTURE_NAME . '_echarts',
                self::$assets_url . 'echarts.min.js',
                array(),
                MAGICK_MIXTURE_VERSION,
                true
            );
        }
    }
}