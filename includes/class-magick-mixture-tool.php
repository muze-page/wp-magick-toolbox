<?php
// 如果直接访问此文件，请中止。
defined('ABSPATH') || exit;

/**
 * 一些公共函数
 */
if (!class_exists('MaBox_Tool')) {
    class MaBox_Tool
    {
        /**
         * 获取 WordPress 站点本地日期时间。
         *
         * WordPress 返回的本地时间字符串是事实源；DateTimeImmutable 仅负责
         * 后续日历运算，避免把 PHP 默认时区或固定偏移混入站点日期。
         */
        protected static function current_site_datetime()
        {
            $current_time = current_time('mysql');
            $date_time = DateTimeImmutable::createFromFormat('!Y-m-d H:i:s', $current_time);

            if ($date_time instanceof DateTimeImmutable) {
                return $date_time;
            }

            return new DateTimeImmutable($current_time);
        }

        /**
         * 将日期输入转换为不可变日期对象。
         */
        private static function parse_date($value)
        {
            try {
                return new DateTimeImmutable(trim((string) $value));
            } catch (Exception $exception) {
                return false;
            }
        }

        /**
         * 判断指定主题是否启用，若使用了该主题则返回true
         * 期待传入主题名  'Twenty Twenty'
         */
        public static function theme_active($theme_name)
        {
            $theme = wp_get_theme(); // 获取当前主题
            if ($theme_name == $theme->name || $theme_name == $theme->parent_theme) {
                //启用该主题
                return true;
            } else {
                //没有启用该主题
                return false;
            }
        }

        /**
         * 判断指定插件是否启用，若该插件启用则返回true
         * 期待传入插件目录，例如'advanced-custom-fields-pro/acf.php'
         */
        public static function plugin_active($plugin_position)
        {
            include_once ABSPATH . 'wp-admin/includes/plugin.php';
            if (is_plugin_active($plugin_position)) {
                //已启用
                return true;
            } else {
                //没有启用该插件
                return false;
            }
        }

        /**
         * 调试用，打印各种数据
         *
         */
        public static function p($data)
        {
            echo '<pre>';
            print_r($data);
            echo '</pre>';
        }

        /**
         * 调试用，打印当前页面的$hook参数
         */
        public static function run_page_hook()
        {
            add_action('admin_enqueue_scripts', array(__CLASS__, 'display_page_hook'));
        }
        public static function display_page_hook($hook)
        {

            echo '<h1 style="color: crimson;text-align: center;">' . esc_html($hook) . '</h1>';
        }

        /**
         * 创建一个方法，在后台顶部显示一个通知
         */
        public static function magick_admin_notice_acfs($content)
        {
?>
            <div class='notice notice-error '>
                <p><?php echo esc_html($content); ?></p>
            </div>
<?php
        }

        /**
         * 时间很重要
         */
        public static function get_time()
        {
            $today = static::current_site_datetime()->setTime(0, 0, 0);
            $dates = array();

            for ($days_ago = 0; $days_ago < 7; $days_ago++) {
                $dates[] = $today->modify('-' . $days_ago . ' days')->format('Y-m-d');
            }

            return array(
                'a' => $dates,
            );
        }
        /**
         * 输入一个日期，输出第一秒时间和最后一秒
         */
        public static function export_handle_time($type = 'start', $time = '2023-03-31')
        {
            $handle_time = '';
            $date_time = self::parse_date($time);

            if (!$date_time) {
                return $handle_time;
            }

            if ($type === 'start') {
                $handle_time = $date_time->setTime(0, 0, 0)->format('Y-m-d H:i:s');
            }
            if ($type === 'end') {
                $handle_time = $date_time->setTime(23, 59, 59)->format('Y-m-d H:i:s');
            }
            return $handle_time;
        }

        /**
         * 处理时间用
         */
        public static function getDateFromRange($startdate, $enddate)
        {
            $start = self::parse_date($startdate);
            $end = self::parse_date($enddate);

            if (!$start || !$end) {
                return array();
            }

            $current = $start->setTime(0, 0, 0);
            $last = $end->setTime(0, 0, 0);
            $dates = array();

            while ($current <= $last) {
                $dates[] = $current->format('Y-m-d');
                $current = $current->modify('+1 day');
            }

            return $dates;
        }
        /**
         * 输出本周、上周、本月、上月时间数组
         */
        public static function get_time_long($type = "this_week")
        {
            $today = static::current_site_datetime()->setTime(0, 0, 0);

            if ($type === 'this_week' || $type === 'last_week') {
                $days_since_monday = (int) $today->format('N') - 1;
                $start = $today->modify('-' . $days_since_monday . ' days');

                if ($type === 'last_week') {
                    $start = $start->modify('-7 days');
                }

                $end = $start->modify('+6 days');
                return self::getDateFromRange($start->format('Y-m-d'), $end->format('Y-m-d'));
            }

            if ($type === 'this_month' || $type === 'last_month') {
                $start = $type === 'this_month'
                    ? $today->modify('first day of this month')
                    : $today->modify('first day of last month');
                $end = $start->modify('last day of this month');

                return self::getDateFromRange($start->format('Y-m-d'), $end->format('Y-m-d'));
            }

            $msg = "参数错误！";
            return $msg;
        }

        /**
         * 输出今天、本周、本月、本年和累计发文数量
         * 可选获取时间、类型（page.post）、状态
         * 描述：考虑到性能问题，这里写成if判断，
         * 注意：全部统计有问题，有时间再解决
         */
        public static function get_total_release_amount($time = 'today', $type = 'post', $status = 'publish')
        {

            /**
             * 作用本日发文数量
             * 查询：https://developer.wordpress.org/reference/classes/wp_query/#date-parameters
             * 描述：仅统计已发布的公开内容和密码保护内容
             */
            if ($time == 'today') {

                $today = static::current_site_datetime();
                $today_args = array(
                    'post_type' => $type, //类型
                    'post_status' => $status, //状态
                    'post__not_in' => get_option('sticky_posts'), //排除置顶文章
                    'date_query' => array( //时间
                        array(
                            'year' => (int) $today->format('Y'),
                            'month' => (int) $today->format('n'),
                            'day' => (int) $today->format('j'),
                        ),
                    ),
                );
                $today_query = new WP_Query($today_args);
                return $today_query->post_count;
            }

            /**
             *功能：本周发文数量
             *来源：https://www.166yc.cn/195.html
             *参考：https://developer.wordpress.org/reference/classes/wp_query/#date-parameters
             */
            if ($time == 'week') {
                $today = static::current_site_datetime();
                $time_week = array(
                    'year' => (int) $today->format('Y'),
                    'week' => (int) $today->format('W'),
                );
                $args_week = array(
                    'post_type' => $type,
                    'post_status' => $status,
                    'date_query' => $time_week,
                    'no_found_rows' => true,
                    'suppress_filters' => true,
                    'fields' => 'ids',
                    'posts_per_page' => 10000,
                );
                $query_week = new WP_Query($args_week);
                return $query_week->found_posts;
            }

            /**
             * 用途：获取本月发文数量
             * 描述：仅统计本月已发布文章数量
             */
            if ($time == 'month') {
                $args = array(
                    'post_type' => $type,
                    'post_status' => $status,
                    'post__not_in' => get_option('sticky_posts'),
                    'date_query' => array(
                        array(
                            'after' => '1 month ago',
                        ),
                    ),
                    'no_found_rows' => true,
                    'fields' => 'ids',
                    'posts_per_page' => 10000,
                );
                $query = new WP_Query($args);
                return $query->found_posts;
            }

            /**
             * 用途：获取本年发文数量
             */

            if ($time == 'year') {
                $args = array(
                    'post_type' => $type,
                    'post_status' => $status,
                    'post__not_in' => get_option('sticky_posts'),
                    'date_query' => array(
                        array(
                            'after' => '1 year ago',
                        ),
                    ),
                    'no_found_rows' => true,
                    'fields' => 'ids',
                    'posts_per_page' => 10000,
                );
                $query = new WP_Query($args);
                return $query->found_posts;
            }

            /**
             * 用途：获取所有已发布文章数量
             * 来源：https://developer.wordpress.org/reference/functions/wp_count_posts/
             */
            if ($time == 'total') {
                $count_posts = wp_count_posts();

                if ($count_posts) {
                    $published_posts = $count_posts->publish;
                }
                return $published_posts;
            }

            $msg = "参数错误！";
            return $msg;
        }

        /**
         * 输出今天的相关统计数据
         * ['today']
         * 发文数量 - ['single']
         * 评论数量 - ['comments']
         * 注册人数 - ['register']
         * ['total']
         * 总发文数量 - ['single']
         * 总用户数量 - ['register']
         */
        public static function get_site_census_data()
        {
            //存储数据
            $arr = array();
            //拿到今天的时间
            $today = static::current_site_datetime();

            /**
             * 今天
             */
            //今天发文数量
            $today_args = array(
                'post_type' => 'post', //类型
                'post_status' => 'publish', //状态
                'post__not_in' => get_option('sticky_posts'), //排除置顶文章
                'date_query' => array( //时间
                    array(
                        'year' => (int) $today->format('Y'),
                        'month' => (int) $today->format('n'),
                        'day' => (int) $today->format('j'),
                        //after’=>’1 day ago’
                    ),

                ),
            );
            $today_query = new WP_Query($today_args);
            $today_single = $today_query->post_count;
            $arr['today']['single'] = $today_single;

            //获取今天的评论数量

            $args = array(
                'date_query' => array(
                    array(
                        'year' => (int) $today->format('Y'),
                        'month' => (int) $today->format('n'),
                        'day' => (int) $today->format('j'),
                    ),

                ),
            );

            $today_comments = count(get_comments($args));
            $arr['today']['comments'] = $today_comments;

            //获取今天的注册数量
            global $wpdb;
            $todate = $today->format('Y-m-d');
            $sql = $wpdb->prepare(
                "SELECT COUNT(*) AS num FROM `{$wpdb->prefix}users` WHERE SUBSTRING(`user_registered`,1,10) = %s",
                $todate
            );
            $results = $wpdb->get_results($sql);
            $totay_register = $results[0]->num;
            $arr['today']['register'] = $totay_register;

            /**
             * 总计
             */
            //总计发文数量
            $count_posts = wp_count_posts();
            $total_single = $count_posts->publish;
            $arr['total']['single'] = $total_single;

            //总用户
            //网站注册用户总数
            $total_users = $wpdb->get_var($wpdb->prepare("SELECT COUNT(ID) FROM {$wpdb->users}"));
            $arr['total']['register'] = $total_users;

            return $arr;
        }

        /**
         * 输入人员ID和时间，输出发文数量，可选文章状态
         * 时间：2022-12-09
         */
        public static function get_count_user($id = '1', $time = '2023-02-16', $type = 'publish')
        {
            /**
             * $id:待查询人员的ID
             * $time:时间：2022-12-09
             * $type:文章状态类型，默认publish(已发布)
             */
            $arr = array();
            $args = array(
                'date_query' => array(
                    array(
                        'after' => $time,
                        'before' => $time,
                        //'after'     => '2022-12-09',
                        //'before'    => '2022-12-09',
                        'inclusive' => true,
                    ),
                ),
                'posts_per_page' => -1, //全显示
                'post_status' => $type, //已发布的文章 - 非待审、草稿、私密
                'author' => $id, //指定用户的ID
            );
            $query = new WP_Query($args);
            $arr['user_id'] = $id;
            $arr['time'] = $time;
            $arr['post_status'] = $type;
            $arr['total'] = $query->post_count;
            return $arr;
        }

        /**
         * 根据给出的ID返回对应属性值
         * [ID] => 1
         *[user_login] => test
         *[user_pass] => $P$Bm9497CNcPxNS8DJMMCMqgXR.jKTeQ.
         *[user_nicename] => test
         *[user_email] => test@test.cc
         *[user_url] => http://magick.plugin
         *[user_registered] => 2023-02-01 08:40:27
         *[user_activation_key] =>
         *[user_status] => 0
         *[display_name] => test  推荐用这个获取名字
         */
        public static function get_user_data($id = '1', $type = 'ID')
        {
            $user = new WP_User($id);
            return $user->data->$type;
        }
    } //end
}
