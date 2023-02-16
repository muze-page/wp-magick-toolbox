<?php
//文章统计菜单

//如何在当前页面加载js
if (!class_exists('Magick_Mixtrue_Census_Single')) {
    class Magick_Mixtrue_Census_Single extends Magick_Mixtrue
    {

        public function __construct()
        {
            self::init_actions();

        }

        public static function init_actions()
        {

            add_action('admin_init', array(__CLASS__, 'magick_plugin_options'));

        }

        //待渲染的内容
        public static function load_content()
        {
            ?>
            <!-- 在默认WordPress“包装”容器中创建标题 -->
	        <div class="wrap">
            <!--标题-->
		     <h2><?php echo esc_html(get_admin_page_title()); ?></h2>
		     <!--在保存设置时调用WordPress函数以呈现错误。 -->
		     <?php settings_errors();?>
		     <!-- 创建用于呈现选项的表单 -->
		     <form method="post" action="options.php">
		     	<?php settings_fields('sandbox_theme_display_options');?>
		     	<?php do_settings_sections('sandbox_theme_display_options');?>
		     	<?php submit_button();?>
		     </form>
             <?php self::render_page()?>
	             </div><!-- /.wrap -->
            <?php
}

        //开始判断，在文章统计页则加载
        //public static function current_page_hook($hook)
        //{
        //    if ('dashboard_page_magick-census-single' == $hook) {
        //        //是指定页
        //        return true;
        //    }
        //}

        //添加设置选项
        public function magick_plugin_options()
        {
            // 如果插件选项不存在，请创建它们。
            if (false == get_option('sandbox_theme_display_options')) {
                add_option('sandbox_theme_display_options');
            } // end if

            // 首先，我们注册一个部分。这是必要的，因为所有未来的选项都必须属于一个。
            add_settings_section(
                'sandbox_theme_display_option', // 用于标识此部分以及用于注册选项的ID
                '自定义设置', // 要在管理页面上显示的标题
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
        public function magick_plugin_options_callback()
        {
            //拿到选项的值
            $options = get_option('magick_plugin_config');
            if ($options) {
                echo "您选择的是：" . implode(',', $options['option_id']);
                return;
            } else {
                echo "您没有选择值";
                return;
            }
        } //结束magick_plugin_options_callback

        //选中框设置的回调
        public function magick_show_select_callback($args)
        {
            // 首先，我们拿到选项
            $options = get_option('magick_plugin_config');

            $uwcc_checkbox_field_1 = isset($options['option_id']) ? (array) $options['option_id'] : [];
            //name值很关键
            //开始循环
            $user = self::get_user();
            foreach ($user as $value) {
                $id = $value['id'];
                $name = $value['name'];
                //echo $id, $name;
                //Magick_Mixtrue_Tool::p($user);
                //注意，元素的ID和name属性应与add_settings_field调用中的ID属性相匹配
                ?>

                     <input type='checkbox' name='magick_plugin_config[option_id][]' <?php checked(in_array($id, $uwcc_checkbox_field_1), 1);?> value=<?php echo $id; ?>>
        <label><?php echo $name; ?></label>
                <?php
} //end foreach
            ?>

    <label for="option_id"> <?php echo $args[0]; ?></label>

    <?php

        } // end magick_show_select_callback

        /**
         * 产出管理员、编辑、作者和贡献用户的ID
         *
         * Array
         * (
         *     [0] => Array
         *         (
         *             [id] => 5
         *             [name] => FOUR
         *         )
         * )
         * */
        public static function get_user()
        {
            //获取基础数组
            $user = get_users(
                array(
                    //符合其中之一要求的人
                    'role__in' => $role = array('administrator', 'author', 'editor', 'contributor'),
                ));

            //存储数据
            $arr = array();
            foreach ($user as $key => $value) {
                $arr[$key]['id'] .= $value->id;
                $arr[$key]['name'] .= $value->display_name;
            }
            return $arr;
        }
        /**
         * 获取一批人的近7天发文数量
         */
        public static function get_user_release_arr()
        {
            $tool = new Magick_Mixtrue_Tool;
            //存储数组
            $arr = array();
            //拿到ID数组
            $options = get_option('magick_plugin_config');
            $a = $options['option_id'];

            foreach ($a as $key => $value) {
                $arr[$key] = $tool->get_count_user_week($value);
            }

            return $arr;

        }

        //统计页面基本框架
        public static function render_page()
        {
            $tool = new Magick_Mixtrue_Tool;
            //拿到表格用数据
            $chart = self::get_user_release_arr();

            /**
             * 表格数据准备
             */
            //拿到作者名
            $chart_user = array();
            foreach ($chart as $key => $value) {
                $id = $value['0']['user_id'];
                $chart_user[$key] = $tool->get_user_data($id, 'display_name'); //拿到名字
            }

            //拿到时间
            $chart_time = array();
            foreach ($chart['0'] as $key => $value) {
                $time = $value['time'];
                $chart_time[$key] = date("d", strtotime($time));
            }

            $chart_content = array();
            foreach ($chart as $a => $b) {

                foreach ($b as $key => $value) {

                    $c[$key] = $value['total'];
                }
                $id = $b['0']['user_id'];
                $chart_content[$a]['name'] = $tool->get_user_data($id, 'display_name'); //拿到名字
                $chart_content[$a]['type'] = "bar";
                $chart_content[$a]['data'] = $c;
            }

            //看看里面有啥
            //$tool->p($chart);
            //$tool->p($chart_user);
            //$tool->p($chart_time);
            //$tool->p($chart_content);

            /**
             * 基础数据准备
             */

            //今天发文
            $count_today = $tool->get_publish_count_today();
            //本周发文
            $count_week = $tool->get_publish_count_week();
            //本月发文
            $count_month = $tool->get_publish_count_month();
            //本年发文
            $count_year = $tool->get_publish_count_year();
            //累计发文
            $count_total = $tool->get_publish_count();

            ?>

            <div class="magick-single-census">
        <!--放统计图-->
        <div id="magick-seven-census" style="width:700px;height:400px;"></div>
        <!--放方框-->
        <div class="magick-right">
            <div class="bisection">
                <div class="census-total">
                    <span>今日发文</span>
                    <div class="census-child">
                        <p><span><?php echo $count_today; ?></span>篇</p>
                        <span class="dashicons dashicons-analytics"></span>
                    </div>
                </div>
                <div class="census-total">
                    <span>累计发文</span>
                    <div class="census-child">
                        <p><span><?php echo $count_total ?></span>篇</p>
                        <span class="dashicons dashicons-analytics"></span>
                    </div>
                </div>
            </div>

            <div class="bisection">
                <div class="census-week">
                    <span>本周发文</span>
                    <div class="census-child">
                        <p><span><?php echo $count_week; ?></span>篇</p>
                        <span class="dashicons dashicons-analytics"></span>
                    </div>
                </div>
                <div class="census-month">
                    <span>本月发文</span>
                    <div class="census-child">
                        <p><span><?php echo $count_month; ?></span>篇</p>
                        <span class="dashicons dashicons-analytics"></span>
                    </div>
                </div>
                <div class="census-month">
                    <span>本年发文</span>
                    <div class="census-child">
                        <p><span><?php echo $count_year; ?></span>篇</p>
                        <span class="dashicons dashicons-analytics"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        // 基于准备好的dom，初始化echarts实例
        var myChart = echarts.init(document.getElementById("magick-seven-census"));

        // 指定图表的配置项和数据
        var option = {
            title: {
                text: "一周发文统计",
            },
            tooltip: {},
            legend: {
                //data: ["作者一", "作者二", "作者三"],
                data: <?php echo json_encode($chart_user) ?>,
            },
            xAxis: {
                //data: ["周一", "周二", "周三", "周四", "周五", "周六", "周七"],
                data: <?php echo json_encode($chart_time) ?>,
            },
            yAxis: {},
            series:  <?php echo json_encode($chart_content) ?>,
            //series: [
            //    {
            //        name: "作者一",
            //        type: "bar",
            //        data: [5, 20, 36, 10, 10, 20, 22],
            //    },
//
            //    {
            //        name: "作者二",
            //        type: "bar",
            //        data: [55, 22, 16, 18, 30, 22, 26],
            //    },
//
            //    {
            //        name: "作者三",
            //        type: "bar",
            //        data: [26, 10, 16, 20, 30, 10, 28],
            //    },
            //],
        };

        // 使用刚指定的配置项和数据显示图表。
        myChart.setOption(option);
    </script>

            <?php
}

    } //end class
}

//加载echarts 用于图标绘制
// public static function load_block_js()
// {
//     wp_enqueue_style('插件名', plugin_dir_url(dirname(__FILE__)) . 'js/echarts_v5.4.0.js', array(), '版本号', 'all');

// }
//激活插件时运行
//add_action('plugins_loaded', array('Magick_Mixtrue_Census_Single', 'init_actions'));
//add_action('admin_enqueue_scripts', array('Magick_Mixtrue_Census_Single', 'load_block_js'));
