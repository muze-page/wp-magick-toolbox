<?php
//提供各种数据接口
class MaMi_Interface
{
    public static function run()
    {
        // 注册动作钩子
        add_action('wp_ajax_get_all_table_names', array(__CLASS__, 'get_all_table_names'));
        add_action('wp_ajax_nopriv_get_all_table_names', array(__CLASS__, 'get_all_table_names'));
    }

    //获取所有的数据库表名
    public static function get_all_table_names()
    {
        global $wpdb;

        $table_names = $wpdb->tables();

        // 处理请求，并生成响应数据
        $response = array(
            'data' =>  $table_names,
        );

      

        // 返回响应数据
        wp_send_json($response);
    }
}
