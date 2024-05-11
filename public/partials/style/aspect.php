<?php
//外观特效
if (!class_exists('MaMi_Style_Aspect')) {
    class MaMi_Style_Aspect
    {
        //选项值
        private static $option;
        //加载
        public static function run($config)
        {
            //获取选项
            $option =  MaMi_Admin::get_config($config, 'aspect');

            //传值
            self::$option = $option;


         



          
           
                
            

        


           



           

            /**
             * 添加樱花
             */
            $sakura =  MaMi_Admin::get_config($option, 'sakura');
            if ($sakura) {
                add_action('wp_enqueue_scripts', array(__CLASS__, 'sakura'));
            }
        }

       

        

       



      


       

       

        /**
         * 添加樱花
         */
        public static function sakura()
        {
            wp_enqueue_script(
                MAGICK_MIXTURE_NAME . '_sakura',
                plugin_dir_url(dirname(__DIR__)) . 'js/sakuraPlus.js',
                array(),
                MAGICK_MIXTURE_VERSION,
                true
            );
        }
    }
}
