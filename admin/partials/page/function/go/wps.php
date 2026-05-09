<?php
/*
 Go中间页跳转 - WPS
 */
include plugin_dir_path((__FILE__)) . 'index.php'; // 获取数据
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
    <link rel="stylesheet" type="text/css" href=<?php echo $url . "wps.css" ?>>
    <style>
    @media (max-width: 768px) {
        .component-modal-wrap { padding: 20px; }
        .modal-body { left: 50% !important; top: 50% !important; transform: translate(-50%, -50%); width: 90% !important; max-width: 400px; }
        .dialog-content { padding: 0 16px; }
        .dialog-foot { flex-wrap: wrap; gap: 8px; }
        .component-text-btn { flex: 1; min-width: 120px; text-align: center; }
    }
    </style>
</head>

<body>

    <!--
        来源：https://www.kdocs.cn/office/link?target=https%3A%2F%2F3.cn%2F2-4kooxV&fileId=303679889425
    -->
    <div class="component-modal-wrap">
        <div class="component-modal component-confirm link-page-modal" tabindex="1">
            <div class="modal-body" style="left: calc(50% - 225px); top: calc(50% - 78px);">
                <div class="dialog-header">
                    <div class="header-title">即将离开<?php echo $site_name ?></div>
                    <div class="component-icon-btn close-btn" role="button">
                        <i class="icons icons-16 icons-16-close"></i>
                    </div>
                </div>
                <div class="dialog-content">
                    <div class="wrap">
                        <p class="content">你即将通过访问链接离开<?php echo $site_name ?>，请注意你的账号和信息安全，建议谨慎访问。
                        </p>
                        <div class="link">
                            <div class="url">你访问的是：<?php echo esc_url($external_url); ?></div>
                        </div>
                    </div>
                </div>
                <div class="dialog-foot">
                    <div class="component-text-btn cancel-btn" role="button" onclick="window.close()">停止访问</div>
                    <div class="component-text-btn confirm-btn" role="button">
                        <a href="<?php echo esc_url($external_url); ?>" target="_self"> 继续访问</a>
                    </div>
                </div>
            </div>
        </div>
    </div>


</body>

</html>