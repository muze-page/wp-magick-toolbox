<?php

/**
 * 效果：插件设置选项
 */
if (!class_exists('MaBox_Config')) {
    class MaBox_Config implements MaBox_Module_Interface
    {
        public static function run($config = array())
        {
            // 删除插件时清理数据 — 见 uninstall.php
        }
    }
}
