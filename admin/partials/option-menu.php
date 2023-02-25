<?php
/**
 * 选项管理菜单
 */
if (!class_exists('Magick_Mixtrue_Option')) {
    class Magick_Mixtrue_Option
    {
        public function __construct()
        {

            /**
             * 添加顶级菜单
             */
            add_action('admin_menu', array(__CLASS__, 'add_option_menu'));
            /**
             * 添加设置
             */
            add_action('admin_init', array(__CLASS__, 'rudr_settings_fields'));
            /**
             * 保存信息
             */
            add_action('admin_notices', array(__CLASS__, 'rudr_notice'));

        }
        /**
         * 添加菜单
         */
        public static function add_option_menu()
        {
            add_menu_page(
                '魔法选项', // 此菜单对应页面上显示的标题
                '魔法合剂', // 要为此实际菜单项显示的文本
                'administrator', // 哪种类型的用户可以看到此菜单
                'magick_mixtrue_id', // 此菜单项的唯一ID
                array(__CLASS__, 'sandbox_menu_page_display'), // 呈现此页面的菜单时要调用的函数的名称
                'dashicons-palmtree', //图标
                '900' //顺序
            );
        }
        /**
         * 菜单展示的内容
         */
        public static function sandbox_menu_page_display()
        {

            ?>
            <div class="wrap">
                <h1><?php echo get_admin_page_title() ?></h1>

                     <!-- 在保存设置时调用WordPress函数以呈现错误. -->
            <?php settings_errors('rudr_slider_settings_errors');?>

            <?php

            if (isset($_GET['tab'])) {
                $active_tab = $_GET['tab'];
            } // end if

            //设置默认值
            $active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'display_options';
            ?>

		<h2 class="nav-tab-wrapper">
			<a href="?page=magick_mixtrue_id&tab=display_options" class="nav-tab <?php echo $active_tab == 'display_options' ? 'nav-tab-active' : ''; ?>">安全</a>
			<a href="?page=magick_mixtrue_id&tab=social_options" class="nav-tab <?php echo $active_tab == 'social_options' ? 'nav-tab-active' : ''; ?>">优化</a>
		</h2>

        <form method="post" action="options.php">
        <?php
//根据tab显示对应的内容
            if ($active_tab == 'display_options') {
                settings_fields('rudr_slider_settings'); // 设置组名称
                do_settings_sections('rudr_slider'); // just a page slug，只是一个页面 slug
            } else {
                settings_fields('rudr_slider_settings');
                do_settings_sections('ruders');
            } // end if/else

            submit_button();

            ?>


                </form>
            </div>
        <?php

        } // end sandbox_menu_page_display

        /**
         * 创建两个选项节
         */
        public static function rudr_settings_fields()
        {
            /**
             * 安全
             * -- 账户安全
             */
            /**
             * 优化
             * -- 评论优化
             * -- 后台增强
             */

            /**
             * 其他
             * -- 评论添加表情
             */
            $about_slug = 'about';
            $about_group = 'about_settings';

            // 我创建了变量以使事情更清楚
            $page_slug = 'rudr_slider'; //页面段
            $option_group = 'rudr_slider_settings'; //选项组

            // 1. 创建节
            add_settings_section(
                'rudr_section_id', // section ID
                '', // title (optional)
                '', // 显示节的回调函数（可选）
                $page_slug //显示的位置
            );

            // 2. 保存选项用字段
            register_setting(
                $option_group, //选项组
                'slider_on', //选项名称
                array(__CLASS__, 'rudr_sanitize_checkbox') //数据验证
            );

            register_setting(
                $option_group,
                'num_of_slides',
                array(__CLASS__, 'rudr_validate')
            );

            // 3. 添加字段
            add_settings_field(
                'slider_on',
                '显示幻灯片',
                array(__CLASS__, 'rudr_checkbox'), // 函数打印字段
                $page_slug,
                'rudr_section_id' // 节的 ID
            );

            add_settings_field(
                'num_of_slides',
                '幻灯片数量',
                array(__CLASS__, 'rudr_number'),
                $page_slug,
                'rudr_section_id',
                array(
                    'label_for' => 'num_of_slides',
                    'class' => 'hello', // for <tr> element
                    'name' => 'num_of_slides', // 传递任何自定义参数
                )
            );

            /**
             * 创建第二个节，用作选项展示
             */
            add_settings_section(
                'rudr_section_display', // section ID
                '展示效果', // title (optional)
                array(__CLASS__, 'rudr_section_callback'),
                'ruders'
            );

            /**
             * 表情包
             */
            //是否显示表情包
            register_setting(
                $option_group, //选项组
                'slider_ons', //选项名称
                array(__CLASS__, 'rudr_sanitize_checkbox') //数据验证
            );

            add_settings_field(
                'slider_ons',
                '显示表情包',
                array(__CLASS__, 'rudr_checkboxs'), // 函数打印字段
                $page_slug,
                'rudr_section_id' // 节的 ID
            );

        }

// 打印字段HTML的自定义回调函数
        public static function rudr_number($args)
        {
            printf(
                '<input type="number" id="%s" name="%s" value="%d" />',
                $args['name'],
                $args['name'],
                get_option($args['name'], 2) // 2 is the default number of slides
            );
        }
// 用于打印复选框字段HTML的自定义回调函数
        public static function rudr_checkbox($args)
        {
            $value = get_option('slider_on');
            ?>
		<label>
			<input type="checkbox" name="slider_on" <?php checked($value, 'yes')?> /> Yes
		</label>
	<?php
}

/**
 * 数据验证
 */
// 复选框字段的自定义清除功能
        public static function rudr_sanitize_checkbox($value)
        {
            return 'on' === $value ? 'yes' : 'no';
        }

//自定义的消毒功能
        public static function rudr_validate($input)
        {
            // 首先消毒
            $input = absint($input);

            if ($input < 2) { // 某些条件
                add_settings_error(
                    'rudr_slider_settings_errors',
                    'not-enough', // 错误消息ID的一部分 id="setting-error-not-enough"
                    '幻灯片的最小数量应至少为 2!',
                    'error' // success, warning, 信息
                );
                // 如果验证失败，则获取上一个字段值
                $input = get_option('num_of_slides');
            }

            return $input;
        }

//显示保存成功消息

        public static function rudr_notice()
        {

            $settings_errors = get_settings_errors('rudr_slider_settings_errors');
            // 如果有任何错误，请退出
            if (!empty($settings_errors)) {
                return;
            }

            if (
                isset($_GET['page'])
                && 'magick_mixtrue_id' == $_GET['page']
                && isset($_GET['settings-updated'])
                && true == $_GET['settings-updated']
            ) {
                ?>
			<div class="notice notice-success is-dismissible">
				<p>
					<strong>选项设置已保存</strong>
				</p>
			</div>
		<?php
}

        }

/**
 * 效果展示
 */
        public static function rudr_section_callback()
        {
//获取选项值
            $switch = get_option('slider_on');
            $num = get_option('num_of_slides');
            $switchs = get_option('slider_ons');
            ?>
您的开关状态：<?php echo $switch; ?>
<br />
您填写的数字是：<?php echo $num; ?>
<br />
您的开关状态：<?php echo $switchs; ?>
    <?php
}

// 用于打印复选框字段HTML的自定义回调函数
        public static function rudr_checkboxs($args)
        {
            $value = get_option('slider_ons');
            ?>
<label>
    <input type="checkbox" name="slider_ons" <?php checked($value, 'yes')?> /> 给评论框添加表情包功能
</label>
<?php
}

    } //end Magick_Mixtrue_Option
}
