<?php

/**
 * 文章统计菜单
 */

if (!class_exists('Magick_Mixtrue_Census_Single')) {
    class Magick_Mixtrue_Census_Single
    {

        public static function run()
        {
            //add_action('wp_loaded', array(__CLASS__, 'load'));
            //添加发文统计菜单
            add_action('admin_menu', array(__CLASS__, 'add_menu_single'));
            //添加设置选项
            add_action('admin_init', array(__CLASS__, 'magick_plugin_options'));
            //加载图标用js
            add_action('admin_enqueue_scripts', array(__CLASS__, 'load_enqueue_admin_script'));
        }



        /**
         * 添加发文统计菜单
         */
        public static function add_menu_single()
        {

            add_submenu_page(
                'index.php',
                __('发文统计'),
                __('发文统计'),
                'administrator',
                'magick-census-single',
                array(__CLASS__, 'load_content')
            );
        }

        //页面加载图标用css和js
        public static function load_enqueue_admin_script($hook)
        {
            //判断下，是否在文章统计页中
            if ('dashboard_page_magick-census-single' != $hook) {
                return;
            }

            //准备打包后的数据
            $build_css = plugin_dir_url(dirname(__DIR__)) . 'count/dist/index.css';
            $build_css = str_replace('/admin/partials/', '/vite/',  $build_css);

            $build_js = plugin_dir_url(dirname(__DIR__)) . 'count/dist/index.js';
            $build_js = str_replace('/admin/partials/', '/vite/',  $build_js);
            wp_enqueue_style(
                MAGICK_MIXTURE_NAME . '_index_css',
                $build_css,
                array(),
                MAGICK_MIXTURE_VERSION,
                'all'
            );
            wp_enqueue_script(
                MAGICK_MIXTURE_NAME . '_index_js',
                $build_js,
                array(),
                MAGICK_MIXTURE_VERSION,
                false
            );

            //传输数据给JS
            $mami_array = array(
                'countData' => self::deliver_data(), //统计的数据信息
            );

            wp_localize_script(MAGICK_MIXTURE_NAME . '_index_js', 'dataLocal', $mami_array); //传给vite项目
        }

        /**
         * 准备传递的数据
         */
        public static function deliver_data()
        {
            //准备对象
            $array = array(
                'single' => array(
                    'count' => self::get_today_data(), //今天的统计数据
                    'today' => self::get_today_release(), //今天文章发布数据
                )
            );
            return $array;
        }

        //待渲染的内容
        public static function load_content()
        {
?>
            <!-- 在默认WordPress“包装”容器中创建标题 -->
            <div class="wrap magick_section">

                <!--标题-->
                <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
                <div id="mami_b2_shop_count"></div>
                <!--展示图表内容-->
                <?php self::render_page() ?>
                <!--在保存设置时调用WordPress函数以呈现错误。 -->
                <?php settings_errors(); ?>
                <!-- 创建用于呈现选项的表单 -->
                <form method="post" action="options.php">
                    <?php settings_fields('sandbox_theme_display_options'); ?>
                    <?php do_settings_sections('sandbox_theme_display_options'); ?>
                    <?php submit_button(); ?>
                </form>

                <?php
                echo "<h3>原始数据</h3>";
                $user_release_arr = self::get_user_release_arr();
                if (!empty($user_release_arr)) {
                    echo '<pre>' . print_r($user_release_arr, true) . '</pre>';
                } else {
                    echo '<pre>暂无对象值</pre>';
                }
                ?>

            </div><!-- /.wrap -->
            <?php
        }

        /**
         * 今日文章信息
         */
        public static function get_today_data()
        {
            //今天的数据
            $tool = new Magick_Mixtrue_Tool;
            $option = $tool->get_site_census_data();

            $array = array(
                array(
                    'title' => "已发布",
                    'num' => (int)$option['today']['single'],
                    'unit' => "篇",
                    'icon' => "dashicons dashicons-universal-access",
                ),
                array(
                    'title' => "已评论",
                    'num' => (int)$option['today']['comments'],
                    'unit' => "条",
                    'icon' => "dashicons dashicons-format-status",
                ),
                array(
                    'title' => "已注册",
                    'num' => (int)$option['today']['register'],
                    'unit' => "位",
                    'icon' => "dashicons dashicons-database-add",
                )

            );
            return $array;
        }

        /**
         * 今日发文信息
         */
        public static function get_today_release()
        {
            //准备日期

            $array = array(
                "title" => "统计",
                "dataset" => self::get_user_release_arr()["week_sum"],
            );
            return $array;
        }

        //添加设置选项
        public static function magick_plugin_options()
        {
            // 如果插件选项不存在，请创建它们。
            if (false == get_option('sandbox_theme_display_options')) {
                add_option('sandbox_theme_display_options');
            } // end if

            // 首先，我们注册一个部分。这是必要的，因为所有未来的选项都必须属于一个。
            add_settings_section(
                'sandbox_theme_display_option', // 用于标识此部分以及用于注册选项的ID
                '已统计人员ID', // 要在管理页面上显示的标题
                //'magick_plugin_options_callback', // 用于呈现节描述的回调
                array(__CLASS__, 'magick_plugin_options_callback'),
                'sandbox_theme_display_options' // 添加此部分选项的页面
            );

            //添加一个对钩选项
            add_settings_field(
                'option_id', // 用于标识整个主题中的字段的ID
                '待统计人员', // 选项接口元素左侧的标签
                //'magick_show_select_callback', // 负责呈现选项界面的函数的名称
                array(__CLASS__, 'magick_show_select_callback'),
                'sandbox_theme_display_options', // 将显示此选项的页面
                'sandbox_theme_display_option', // 此字段所属的节的名称
                array( // 要传递给回调的参数数组。在这种情况下，只是一个描述。
                    '选择需要监控的用户（排除订阅者）',
                )
            );

            //注册这个设置
            register_setting(
                'sandbox_theme_display_options', //选项组
                'magick_plugin_config', //选项名称
            );
        } //结束magick_plugin_options

        /**
         * 选择结果
         */
        public static function magick_plugin_options_callback()
        {
            //拿到选项的值
            $options = get_option('magick_plugin_config');
            if ($options) {
                echo "您选择的是人员ID是：" . implode(',', $options['option_id']);
                return;
            } else {
                echo "您没有选择值";
                return;
            }
        } //结束magick_plugin_options_callback

        /**
         * 选中框设置的回调
         */
        public static function magick_show_select_callback($args)
        {
            // 首先，我们拿到选项
            $options = get_option('magick_plugin_config');
            $uwcc_checkbox_field_1 = isset($options['option_id']) ? (array) $options['option_id'] : [];
            //name值很关键

            //拿到用户数据
            $user_data = get_users(
                array(
                    //符合其中之一要求的人
                    'role__in' => $role = array('administrator', 'author', 'editor', 'contributor'),
                )
            );

            //将选项循环出来
            foreach ($user_data as $key => $value) {
                $id = $value->ID;
                $name = $value->display_name;
            ?>

                <input type='checkbox' name='magick_plugin_config[option_id][]' <?php checked(in_array($id, $uwcc_checkbox_field_1), 1); ?> value=<?php echo $id; ?>>
                <label class="magick-user-label"><?php echo $name; ?></label>
                &nbsp;&nbsp;|&nbsp;&nbsp;


            <?php
            } //end foreach
            ?>
            <!--描述-->
            <hr /><label for="option_id"> <?php echo $args[0]; ?></label>

        <?php

        } // end magick_show_select_callback

        /**
         * 统计页面基本框架
         */
        public static function render_page()
        {
            $tool = new Magick_Mixtrue_Tool;

            /**
             * 表格数据准备 - 周
             */
            $chart_data_week = self::get_user_release_arr()['week'];

            /**
             * 表格数据准备 - 月
             */
            $chart_data_month = self::get_user_release_arr()['month'];

            //看看里面有啥
            //$tool->p($chart);
            //$tool->p($chart_user);
            //$tool->p($chart_time);
            //$tool->p($chart_content);

            /**
             * 基础数据准备
             */
            $arr_data = $tool->get_site_census_data();

            //今天发文
            $count_today = $arr_data['today']['single'];
            //今天发评论
            $today_comments = $arr_data['today']['comments'];
            //今天注册
            $count_register = $arr_data['today']['register'];
            //总发文
            $total_single = $arr_data['total']['single'];
            //总用户
            $total_user = $arr_data['total']['register'];

        ?>

            <section class="magick_section">
                <div class="single-mixtrue">
                    <!--放统计图-->
                    <div id="magick-seven-census" style="width:700px;height:400px;"></div>
                    <!--放方框-->
                    <div class="magick-right">
                        <div class="magick-per">
                            <div class="per-content">
                                <div class="black-data-box-mix">
                                    <span>今日发文</span>
                                    <div class="child">
                                        <p><span><?php echo $count_today; ?></span>篇</p>
                                        <span class="dashicons dashicons-text-page"></span>
                                    </div>
                                </div>
                            </div>

                            <div class="per-content">
                                <div class="black-data-box-mix">
                                    <span>今日评论</span>
                                    <div class="child">
                                        <p><span><?php echo $today_comments ?></span>篇</p>
                                        <span class="dashicons dashicons-format-status"></span>
                                    </div>
                                </div>
                            </div>

                            <div class="per-content">
                                <div class="black-data-box-mix">
                                    <span>今日注册</span>
                                    <div class="child">
                                        <p><span><?php echo $count_register; ?></span>次</p>
                                        <span class="dashicons dashicons-universal-access"></span>
                                    </div>
                                </div>
                            </div>


                        </div>

                        <div class="magick-per">
                            <div class="per-content">
                                <div class="black-data-box-mix">
                                    <span>总计发文</span>
                                    <div class="child">
                                        <p><span><?php echo $total_single; ?></span>篇</p>
                                        <span class="dashicons dashicons-clipboard"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="per-content ">
                                <div class="black-data-box-mix">
                                    <span>总计用户</span>
                                    <div class="child">
                                        <p><span><?php echo $total_user; ?></span>位</p>
                                        <span class="dashicons dashicons-universal-access-alt"></span>
                                    </div>
                                </div>
                            </div>



                        </div>
                    </div>
                </div>

            </section>
            <!--月度统计-->
            <section class="magick-census-single-month">
                <div id="magick-month-census" style="width:1200px;height:400px;"></div>
            </section>

            <script type="text/javascript">
                // 基于准备好的dom，初始化echarts实例
                let myChart_week = echarts.init(document.getElementById("magick-seven-census"));
                let myChart_month = echarts.init(document.getElementById("magick-month-census"));

                // 指定图表的配置项和数据
                let option_week = {
                    title: {
                        text: "一周发文统计",
                    },
                    tooltip: {},
                    legend: {
                        data: <?php echo $chart_data_week['user'] ?>,
                    },
                    xAxis: {
                        data: <?php echo $chart_data_week['time'] ?>,
                    },
                    yAxis: {},
                    series: <?php echo $chart_data_week['content'] ?>,
                };
                // 指定图表的配置项和数据
                let option_month = {
                    title: {
                        text: "月度发文统计",
                    },
                    tooltip: {},
                    legend: {
                        data: <?php echo $chart_data_month['user'] ?>,
                    },
                    xAxis: {
                        data: <?php echo $chart_data_month['time'] ?>,
                    },
                    yAxis: {},
                    series: <?php echo $chart_data_month['content'] ?>,
                };

                // 使用刚指定的配置项和数据显示图表。
                myChart_week.setOption(option_week);
                myChart_month.setOption(option_month);
            </script>

<?php
        }


        /**
         * 临时处理
         */
        public static function get_article_counts($data, $id)
        {
            $result = array();

            foreach ($data as $date) {
                $current_date = DateTime::createFromFormat('Y-m-d', $date);
                $current_day = $current_date->format('d');
                $current_time = $current_date->format('H');

                $counts = array($current_day); // 第一个元素是当前日期的天数

                // 初始化用户发文数量为0
                foreach ($id as $userId) {
                    $counts[] = 0;
                }

                // 查询对应日期的文章
                $args = array(
                    'post_type' => 'post',
                    'post_status' => 'publish',
                    'date_query' => array(
                        array(
                            'year'  => $current_date->format('Y'),
                            'month' => $current_date->format('m'),
                            'day'   => $current_date->format('d'),
                        ),
                    ),
                );
                $query = new WP_Query($args);

                // 统计各个作者的发文数量
                if ($query->have_posts()) {
                    while ($query->have_posts()) {
                        $query->the_post();
                        $author_id = get_the_author_meta('ID');

                        if (in_array($author_id, $id)) {
                            $index = array_search($author_id, $id);
                            $counts[$index + 1]++;
                        }
                    }
                }

                wp_reset_postdata();

                $result[] = $counts;
            }

            return $result;
        }

        /**
         * 整理用户名
         * 输入用户ID数组
         */
        public static function format_dates($ID)
        {
            $result = array();

            foreach ($ID as $id) {
                $user = get_user_by('ID', $id);
                if ($user) {
                    $nickname = $user->display_name;
                    $result[] = $nickname;
                }
            }

            return $result;
        }


        /**
         * 结合
         */
        public static function handle_data(){
            
        }

        /**
         * 获取一批人的发文数量,一周，一个月
         */
        public static function get_user_release_arr()
        {
            //工具函数
            $tool = new Magick_Mixtrue_Tool;
            //存储数组
            $arr = array();
            //拿到ID数组
            $options = get_option('magick_plugin_config');

            //默认查阅ID为1的人的发文数据
            $id = isset($options['option_id']) ? $options['option_id'] : [1];

            //拿到时间数组 - 最近一周
            $t_week = $tool->get_time()['a'];
            //拿到时间数组 - 本月
            $t_month = $tool->get_time_long("this_month");


            //将数据处理后存入数组


            $week_time = self::format_dates($id); //整理昵称数据
            array_unshift($week_time, "user"); //添加标识头
            $week_time = array($week_time); //存进数组

            $week_data = array_reverse(self::get_article_counts($t_week, $id)); //获取数据并反序

            $arr['week_sum'] = array_merge($week_time, $week_data); //整理为所需格式


            //$arr['months'] = self::get_article_counts($t_month, $id);

            return $arr;
        }

        /**
         * 输入人员ID，返回最近7天，本月发文数量
         * 输出：数组(array)
         */
        public static function get_count_release($id = '1')
        {
            $tool = new Magick_Mixtrue_Tool;
            //存储数据
            $arr = array();
            /**
             * 最近一周发文
             */
            //拿到时间数组
            $t_week = $tool->get_time()['a'];
            //表格需要，反转下时间
            $t_week = array_reverse($t_week);
            //开始循环
            for ($i = 0; $i < count((array) $t_week); $i++) {
                //拿到日期
                $time = $t_week[$i];
                $arr['week'][$i] = $tool->get_count_user($id, $time, 'publish');
            }
            /**
             * 本月发文数量
             */
            //拿到时间数组
            $t_month = $tool->get_time_long("this_month");
            //循环
            for ($i = 0; $i < count((array) $t_month); $i++) {
                //拿到日期
                $time = $t_month[$i];
                $arr['month'][$i] = $tool->get_count_user($id, $time, 'publish');
            }
            return $arr;
        }
    } //end class
}
