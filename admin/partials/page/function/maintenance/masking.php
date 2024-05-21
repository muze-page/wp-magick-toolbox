<?php
/*
 暂停页模版 - 背景遮罩
 */

include plugin_dir_path((__FILE__)) . 'index.php'; // 获取数据
?>
<!doctype html>
<html <?php language_attributes(); ?>>

<head>
    <meta charset="<?php bloginfo('charset'); ?>" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <?php wp_head(); ?>
</head>

<body class="maintenance-page" <?php body_class(); ?>>
    <?php wp_body_open(); ?>



    <link href="<?php echo $url_css . "masking.css" ?>" rel="stylesheet" type="text/css" />
    <script type="text/javascript" src="<?php echo $url_js . "Countdown.js" ?>"></script>
    <script>
        // 目标日期和时间
        var targetDate = new Date("<?php echo $countdown ?>"); //规定以T分隔日期和时间
    </script>
    <picture class="pending-bg-img">
        <img src="<?php echo $countdown_image ?>" alt="<?php echo $countdown_title ?>">
    </picture>
    <section class="site-main-container">
        <div class="site-carry">
            <div class="site-content">
                <div>
                    <main>
                        <article>
                            <div>
                                <section class="pending-caption ">
                                    <h1><?php echo $countdown_title ?></h1>
                                    <h2><?php echo $countdown_content ?></h2>
                                </section>
                                <section class="countdown-container">
                                    <h3 class="countdown-desc">倒计时结束后即可正常访问</h3>
                                    <div id="countdown"></div>

                                </section>
                            </div>
                        </article>
                    </main>
                </div>
            </div>
        </div>
    </section>


</body>

</html>