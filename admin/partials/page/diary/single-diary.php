<?php
/**
 * 日记单页模板
 *
 * 模仿日记格式展示文章内容。
 */
get_header();

while (have_posts()) : the_post();
    $date = get_the_date('Y年m月d日');
    $weekday = get_the_date('l');
    $weather = get_post_meta(get_the_ID(), 'mabox_diary_weather', true);
    $moods = get_the_terms(get_the_ID(), 'mabox_diary_mood');
    $mood_text = '';
    if ($moods && !is_wp_error($moods)) {
        $mood_text = $moods[0]->name;
    }

    $weekday_cn = array(
        'Monday' => '星期一',
        'Tuesday' => '星期二',
        'Wednesday' => '星期三',
        'Thursday' => '星期四',
        'Friday' => '星期五',
        'Saturday' => '星期六',
        'Sunday' => '星期日',
    );
    $weekday_display = isset($weekday_cn[$weekday]) ? $weekday_cn[$weekday] : $weekday;
?>
<article id="post-<?php the_ID(); ?>" <?php post_class('mabox-diary-entry'); ?>>
    <div class="mabox-diary-container">
        <div class="mabox-diary-header">
            <div class="mabox-diary-date">
                <span class="date"><?php echo esc_html($date); ?></span>
                <span class="weekday"><?php echo esc_html($weekday_display); ?></span>
            </div>
            <?php if ($mood_text): ?>
            <div class="mabox-diary-mood">
                <span class="mood-label">心情：</span>
                <span class="mood-value"><?php echo esc_html($mood_text); ?></span>
            </div>
            <?php endif; ?>
            <?php if ($weather): ?>
            <div class="mabox-diary-weather">
                <span class="weather-label">天气：</span>
                <span class="weather-value"><?php echo esc_html($weather); ?></span>
            </div>
            <?php endif; ?>
        </div>

        <div class="mabox-diary-content">
            <h1 class="mabox-diary-title"><?php the_title(); ?></h1>
            <div class="mabox-diary-body">
                <?php the_content(); ?>
            </div>
        </div>

        <div class="mabox-diary-footer">
            <div class="mabox-diary-meta">
                <span class="author">作者：<?php the_author(); ?></span>
                <span class="time">发布于：<?php echo get_the_time('H:i'); ?></span>
            </div>
            <?php if (comments_open() || get_comments_number()): ?>
            <div class="mabox-diary-comments">
                <?php comments_template(); ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
</article>

<style>
.mabox-diary-entry { max-width: 700px; margin: 40px auto; padding: 0 20px; }
.mabox-diary-container { background: #fff; border: 1px solid #e8e8e8; border-radius: 8px; padding: 30px; box-shadow: 0 2px 12px rgba(0,0,0,0.05); }
.mabox-diary-header { display: flex; justify-content: space-between; align-items: center; padding-bottom: 15px; border-bottom: 1px dashed #ddd; margin-bottom: 20px; flex-wrap: wrap; gap: 10px; }
.mabox-diary-date { font-size: 18px; color: #333; }
.mabox-diary-date .date { font-weight: bold; }
.mabox-diary-date .weekday { color: #999; margin-left: 10px; font-size: 14px; }
.mabox-diary-mood, .mabox-diary-weather { font-size: 14px; color: #666; }
.mabox-diary-mood .mood-value, .mabox-diary-weather .weather-value { color: #1677ff; font-weight: bold; }
.mabox-diary-title { font-size: 24px; margin: 0 0 20px; color: #333; font-weight: normal; }
.mabox-diary-body { line-height: 1.8; font-size: 16px; color: #444; }
.mabox-diary-body p { margin-bottom: 16px; text-indent: 2em; }
.mabox-diary-footer { margin-top: 30px; padding-top: 15px; border-top: 1px dashed #ddd; }
.mabox-diary-meta { font-size: 13px; color: #999; }
.mabox-diary-meta span { margin-right: 20px; }
@media (max-width: 768px) {
    .mabox-diary-entry { margin: 20px auto; padding: 0 10px; }
    .mabox-diary-container { padding: 20px; }
    .mabox-diary-title { font-size: 20px; }
    .mabox-diary-body { font-size: 15px; }
    .mabox-diary-header { flex-direction: column; align-items: flex-start; }
}
</style>

<?php endwhile; ?>

<?php get_footer(); ?>
