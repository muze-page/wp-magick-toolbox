<?php
//简单SEO - 文章SEO
/**
 * title：文章标题
 * description：文章描述，拿不到就拿文章开头120字
 * keywords：文章标签
 */
if (!class_exists('Npcink_Seo_Single')) {
    class Npcink_Seo_Single
    {
        private static $description;
        private static $keywords;
        public static function run()
        {
            add_action('wp', array(__CLASS__, 'single_seo'));
           
        }

       
        public static function single_seo()
        {
            if (is_singular()) {
                //文章ID
                //拿到文章的描述，关键词
                $description = get_the_excerpt();
                if (empty($description)) {
                    $description = get_post_meta(get_the_ID(), 'custom_description_field', true);
                }
                //拿到文章的关键词
                $tags = get_the_tags();
                $keywords = '';
                if ($tags) {
                    foreach ($tags as $tag) {
                        $keywords .= $tag->name . ', ';
                    }
                    $keywords = rtrim($keywords, ', '); // 去除最后一个逗号和空格
                }

                self::$keywords = $keywords;
                self::$description = $description;
                
                require_once plugin_dir_path(__FILE__) . 'site_keywords.php'; // 确保文件正确加载
                Npcink_Seo_Site_Keywords::run($keywords);

                echo self::$keywords;

              
            }
        }

       
    }
}
