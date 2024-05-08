<?php
//本月每天销售统计（一年内）
/**
 * 一年内的时间
 * 每天的销售额
 */
if (!class_exists('Npcink_B2_Shop_Day')) {
    class Npcink_B2_Shop_Day
    {
        public static function run()
        {
            add_action('wp_footer', array(__CLASS__, 'get_datas'));
        }
        public static function get_datas()
        {
            echo '<h2>66668</h2>';
        }
        public static function get_data()
        {
            global $wpdb;
            //拿到数据表
            $table_name = $wpdb->prefix . 'zrz_order';

            // 获取最近6个月内的订单数据
            $six_months_ago = date('Y-m-d H:i:s', strtotime('-1 months'));
            $query = $wpdb->prepare("
                SELECT DATE(post_date) AS order_date, SUM(meta_value) AS total
                FROM  $table_name AS posts
                INNER JOIN {$wpdb->postmeta} AS meta ON posts.ID = meta.post_id
                WHERE posts.post_type = 'order'
                AND posts.post_status = 'publish'
                AND meta.meta_key = 'order_total'
                AND meta.meta_value IS NOT NULL
                AND meta.meta_value != ''
                AND posts.post_date >= %s
                GROUP BY DATE(post_date)
            ", $six_months_ago);
            $results = $wpdb->get_results($query);

            // 构建对象数组
            $sales_data = array();
            foreach ($results as $result) {
                $sales_data[] = array(
                    'time' => $result->order_date,
                    'total' => (float) $result->total
                );
            }

            return $sales_data;
        }
    }
}
