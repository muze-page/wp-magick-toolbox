<?php
/*
 Go中间页跳转 - 腾讯
 */
//拿到的链接：
$external_url = isset($_GET['url']) ? $_GET['url'] : '暂无';
//网站名：
$site_name = get_bloginfo('name') ;


//ico图标
$favicon_url = get_site_icon_url();
?>
<html lang="zh-CN">

<head>
    <meta charset="utf-8">
    <meta name="robots" content="noindex">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="renderer" content="webkit">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    
    <title><?php echo $site_name ?> - 安全中心</title>
    <link rel="shortcut icon" href="<?php echo $favicon_url ?>" type="image/x-icon">
<style>
    .cdc-external-link-page {
  position: relative;
  width: 100%;
  min-height: 100vh;
  background: linear-gradient(0deg, #f5f7fa, #f5f7fa), #fff;
  box-sizing: border-box;
  padding: 0 10px;
}
.cdc-external-link-page .mod-external-link {
  position: absolute;
  top: 320px;
  left: 50%;
  -webkit-transform: translateX(-50%);
  transform: translateX(-50%);
  width: 580px;
}
.cdc-external-link-page .mod-external-link-logo {
  width: 208px;
  height: 26px;
  background-image: url(img/sprite.ExternalLinkTips-202307171530.svg);
  background-position: 0% 0%;
  background-size: 100% auto;
}
.cdc-external-link-page .mod-external-link-content {
  margin-top: 10px;
  background: #fff;
  box-shadow: 0 8px 12px rgba(55, 79, 120, 0.08);
  border-radius: 4px;
  box-sizing: border-box;
  padding: 20px;
}
.cdc-external-link-page .mod-external-link-title {
  font-size: 16px;
  line-height: 20px;
  font-weight: 500;
  color: #000;
  margin-bottom: 10px;
}
.cdc-external-link-page .mod-external-link-address {
  font-weight: 400;
  font-size: 14px;
  line-height: 22px;
  color: #97a3b7;
  word-break: break-all;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
  margin-bottom: 20px;
}
.cdc-external-link-page .mod-external-link-btn {
  box-sizing: border-box;
  padding-top: 20px;
  text-align: right;
  cursor: pointer;
  border-top: 1px solid #d6dbe3;
}
.cdc-external-link-page .mod-external-link-btn a {
  display: inline-block;
  min-width: 140px;
  height: 36px;
  line-height: 34px;
  text-align: center;
  background: #0052d9;
  font-size: 14px;
  font-weight: 400;
  color: #fff;
}
@media screen and (max-width: 768px) {
  .cdc-external-link-page .mod-external-link {
    top: 50%;
    width: calc(100% - 40px);
    -webkit-transform: translate(-50%, -50%);
    transform: translate(-50%, -50%);
  }
}
body {
  margin: 0;
  padding: 0;
}
a,
a:hover {
  text-decoration: none;
}
    </style>
    </head>

<body>
<div class="cdc-external-link-page">
        <div class="mod-external-link">
            <div class="mod-external-link-logo"></div>
            <div class="mod-external-link-content">
                <div class="mod-external-link-main">
                    <div class="mod-external-link-title">您即将离开<?php echo $site_name ?>，请注意您的账号财产安全</div>
                    <div class="mod-external-link-address"><?php echo esc_url($external_url); ?></div>
                </div>
                <div class="mod-external-link-btn"><a href="<?php echo esc_url($external_url); ?>">继续访问</a></div>
            </div>
        </div>
    </div>

