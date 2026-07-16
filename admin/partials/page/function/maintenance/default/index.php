<?php

defined('ABSPATH') || exit;

//默认带图
include plugin_dir_path((__FILE__)) . '../index.php'; // 获取数据

$logo = $file_url . 'default/tips.svg';
wp_die('<div style="text-align:center">

    <img src="' . $logo . '" alt="' . self::$blogname . '" /><br /><br />' . $countdown_content . '</div>', $page_title, array('response' => '503'));
