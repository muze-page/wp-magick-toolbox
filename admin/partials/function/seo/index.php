<?php
//简单SEO
/**
 * 效果：
 * TODO: 检查，是否已存在相关标签，存在的则不添加
 * TODO: 分类、标签、文章、页面、等，添加TDK
 */
if (!class_exists('Npcink_Easy_Seo')) {
    class Npcink_Easy_Seo
    {

        public static function run($option)
        {

            //首页SEO
            require_once plugin_dir_path(__FILE__) . 'seo_home.php'; //载入文件
            Npcink_Seo_Home::run($option);

            //文章SEO
            $single = MaBox_Admin::get_config($option, 'seo_single');
            if ($single === true) {
                require_once plugin_dir_path(__FILE__) . 'seo_single.php'; //载入文件
                Npcink_Seo_Single::run();
            }

            //分类和标签SEO
            $category = MaBox_Admin::get_config($option, 'seo_category');
            if ($category === true) {
                //添加输入框
                require_once plugin_dir_path(__FILE__) . 'seo_category_add_meat.php'; //载入文件
                Npcink_Seo_Category_Add_Meat::run();
                //分类添加TDK
                require_once plugin_dir_path(__FILE__) . 'seo_category.php'; //载入文件
                Npcink_Seo_Category::run();

                //标签
                require_once plugin_dir_path(__FILE__) . 'seo_tag.php';
                Npcink_Seo_Tag::run();
            }
        }







        //运行 - 检查head标签中是否存在指定meta
        //add_action('wp', array(__CLASS__, 'head_meta'));
        // 添加一个标志变量来确保代码只运行一次
        /* private static  $head_content_captured = false;
        public static function head_meta()
        {

            // 检查标志变量，确保代码只运行一次
            if (self::$head_content_captured) {
                return;
            }

            // 设置标志变量为 true，表示代码即将运行
            self::$head_content_captured = true;

            ob_start(); // 开始输出缓存
            do_action('wp_head');
            $head_content = ob_get_clean(); // 获取缓存内容并清空缓存

            // 在这里可以对 $head_content 进行进一步处理
            $default_value = strpos($head_content, '<meta name="description"') !== false;
            printf('<script>console.log(%s)</script>', json_encode($default_value));
        }
        */
        /*
          <meta name='description' content='SEO 描述' />
          <meta name='keywords' content='1,2222,3，5' />
          elseif(is_tag() || is_category() || is_tax()){
			if(get_query_var('paged') < 2){
				if(self::get_setting('individual')){
					$value	= get_term_meta(get_queried_object_id(), 'seo_'.$type, true);
				}

				if(empty($value) && $type == 'description'){
					$value	= term_description();
				}
			}
         */
    }
}
