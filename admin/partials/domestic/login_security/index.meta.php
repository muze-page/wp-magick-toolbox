<?php
defined('ABSPATH') || exit;

return array(
    'class'       => 'MaBox_Domestic_Login_Security',
    'file'        => 'domestic/login_security/index.php',
    'option_key'  => 'domestic.login_security.attempt_limit_enabled',
    'category'    => 'domestic',
    'scope'       => 'both',
    'config_path' => 'domestic.login_security',
    'risk_tags'   => array('推荐', '安全'),
    'label'       => '登录安全',
    'group'       => '登录安全',
    'feature_id'  => 'domestic-login_security',
    'depends_on'  => array(),
    'preset_tags' => array('security'),
);
