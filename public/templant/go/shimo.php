<?php
/*
 Go中间页跳转 - 石墨文档
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
    <style>
        html,
        body {
            background: #f7f7f7;
        }

        .gtGAjh {
            text-align: center;
        }

        .gtGAjh .logo {
            margin: 100px 0px 50px;
        }

        .gtGAjh .logo svg {
            width: 120px;
        }

        .gtGAjh .modal {
            display: inline-block;
            background: #ffffff;
            width: 800px;
            padding: 80px 0px;
        }

        .gtGAjh .modal .title {
            font-style: normal;
            font-weight: bold;
            font-size: 30px;
            line-height: 30px;
            color: #41464b;
            margin-bottom: 20px;
        }

        .gtGAjh .modal .subtitle {
            font-style: normal;
            font-weight: normal;
            font-size: 15px;
            line-height: 30px;
            color: #666666;
        }

        .gtGAjh .modal .link {
            display: inline-block;
            width: 400px;
            background: #f7f7f7;
            font-style: normal;
            font-weight: normal;
            font-size: 12px;
            padding: 12px;
            color: #41464b;
            margin: 30px 0px;
            word-break: break-all;
        }

        .gtGAjh .modal .button {
            background: linear-gradient(360deg, #2c3033 -0.09%, #54585d 100%);
            border: 1px solid #2c3033;
            border-radius: 2px;
            line-height: 24px;
            padding: 4px 24px;
            color: #ffffff;
        }

        .gKgaxE {
            box-sizing: border-box;
            display: inline-block;
            border-radius: 2px;
            border-style: solid;
            background-color: var(--sm-color-gray0);
            text-align: center;
            text-decoration: none;
            cursor: pointer;
            transition: opacity 0.3s ease 0s, border-color 0.3s ease 0s;
            color: #666666;
            border-width: 0px;
            background-image: linear-gradient(#fdfdfd, #f8f8f8);
            box-shadow: rgba(0, 0, 0, 0.11) 0px 1px 1px 0px;
            position: relative;
            font-size: 14px;
            font-weight: bold;
            min-width: 100px;
            height: 34px;
            line-height: 32px;
            padding: 0px 15px;
        }
    </style>
</head>

<body>
    <div id="root">

        <div class=" gtGAjh">
            <p class="logo"></p>
            <div class="modal">
                <h2 class="title">你即将离开<?php echo $site_name ?>，跳转到外部链接</h2>
                <p class="subtitle">请谨慎评估风险并注意保护你的隐私及财产安全</p>
                <p class="link"><?php echo esc_url($external_url); ?></p><br>
                <a href="<?php echo esc_url($external_url); ?>" target="_blank">
                    <button class=" gKgaxE  button" type="default">继续访问</button>
                </a>

            </div>
        </div>
    </div>
</body>

</html>