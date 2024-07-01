<?php

/**
 * 效果：移除插件设置选项内容
 */
if (!class_exists('MaBox_Config_Remove_Config')) {
    class MaBox_Config_Remove_Config
    {
        //卸载插件时执行
        public static function run()
        {
            $function =  self::get_seting('function');
            $config = self::get_config($function, 'config');
            $remove_config =  self::get_config($config, 'remove_config');


            if ($remove_config === true) {
                
                //$default_value = MAGICK_MIXTURE_OPTION;
               // printf('<script>console.log(%s)</script>', json_encode($default_value));
            }
        }
        /**
         * 提供选项
         */
        public static function get_seting($option)
        {
            //拿到选项值
            $config = get_option(MAGICK_MIXTURE_OPTION);
            $value =  self::get_config($config, $option);
            return $value;
        }
        /**
         * 从对象中获取属性值
         *
         * @param object $config 对象
         * @param string $property 从对象中获取的属性名
         * @param string $defaultValue 默认值（可选）
         * @return mixed 属性值或默认值
         */
        public static function get_config($config, $property, $defaultValue = false)
        {
            /**
             * 是否是对象
             * 对象中是否有此键名
             * 在对象中的此值是否为空
             */
            if (is_object($config) && property_exists($config, $property) && !empty($config->$property)) {
                return $config->$property;
            } else {
                //不存在则输出默认值
                return $defaultValue;
            }
        }
    }
}
