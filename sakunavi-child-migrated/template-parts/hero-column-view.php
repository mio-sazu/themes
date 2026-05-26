<?php
/**
 * Template Part: ヒーロー コラムビュー
 * 最新コラムをスライダー（左）と新着TOP5（右）で表示
 * CSS: assets/css/hero-column.css
 * JS:  assets/js/hero-column.js
 *
 * 呼び出し方: get_template_part('template-parts/hero-column-view');
 * 切替:      外観 > カスタマイズ > ヒーロービュー設定
 */

// スライダー用：最新6件
$hero_columns = get_posts([
    'post_type'      => 'column',
    'posts_per_page' => 6,
    'post_status'    => 'publish',
    'orderby'        => 'date',
    'order'          => 'DESC',
]);

// 右サイド TOP5：最新5件
$top5_columns = get_posts([
    'post_type'      => 'column',
    'posts_per_page' => 5,
    'post_status'    => 'publish',
    'orderby'        => 'date',
    'order'          => 'DESC',
]);

if (empty($hero_columns)) return;

$column_archive_url = get_post_type_archive_link('column') ?: home_url('/column/');
$mascot_path        = get_stylesheet_directory() . '/assets/img/mascot.png';
$mascot_url         = file_exists($mascot_path)
    ? get_stylesheet_directory_uri() . '/assets/img/mascot.png'
    : '';
?>

<section class="hero-column-view">
    <div class="hero-column-view__inner wrapper">

        <!-- お知らせバー -->
        <div class="hcv-bar">
            <span class="hcv-bar__icon">
                <?php if ($mascot_url): ?>
                    <img src="<?php echo esc_url($mascot_url); ?>" alt="" width="40" height="40">
                <?php else: ?>
                    <span class="hcv-bar__icon-emoji" aria-hidden="true">🔔</span>
                <?php endif; ?>
            </span>
            <span class="hcv-bar__text">今週のおすすめコラム、サクッとチェック！</span>
            <span class="hcv-bar__badge">● いま読まれてます</span>
            <a href="<?php echo esc_url($column_archive_url); ?>" class="hcv-bar__more">コラム一覧 →</a>
        </div>

        <!-- メインエリア -->
        <div class="hcv-main">

            <!-- 左：スライダー -->
            <div class="hcv-slider" data-total="<?php echo (int) count($hero_columns); ?>">

                <?php foreach ($hero_columns as $idx => $col):
                    $terms    = get_the_terms($col->ID, 'column_category');
                    $cat_name = ($terms && ! is_wp_error($terms)) ? $terms[0]->name : '';
                    $cat_url  = ($terms && ! is_wp_error($terms)) ? get_term_link($terms[0]) : '#';
                    $thumb    = get_the_post_thumbnail_url($col->ID, 'large');
                    $excerpt  = wp_trim_words(wp_strip_all_tags(do_shortcode($col->post_content)), 30, '…');
                    $date     = get_the_date('Y.m.d', $col->ID);
                    $is_new   = (time() - strtotime($col->post_date)) < (7 * DAY_IN_SECONDS);
                ?>
                <div class="hcv-slide<?php echo $idx === 0 ? ' is-active' : ''; ?>" data-index="<?php echo (int) $idx; ?>">
                <div class="hcv-slide__content">

                    <div class="hcv-slide__img-wrap">
                        <?php if ($thumb): ?>
                            <img
                                src="<?php echo esc_url($thumb); ?>"
                                alt="<?php echo esc_attr($col->post_title); ?>"
                                loading="<?php echo $idx === 0 ? 'eager' : 'lazy'; ?>">
                        <?php else: ?>
                            <div class="hcv-slide__img-placeholder"></div>
                        <?php endif; ?>
                        <?php if ($is_new): ?>
                            <span class="hcv-slide__new-badge">✦<br>NEW</span>
                        <?php endif; ?>
                    </div>

                    <div class="hcv-slide__body">
                        <div class="hcv-slide__meta">
                            <?php if ($cat_name): ?>
                                <a href="<?php echo esc_url($cat_url); ?>" class="hcv-slide__cat">#<?php echo esc_html($cat_name); ?></a>
                            <?php endif; ?>
                            <span class="hcv-slide__date"><?php echo esc_html($date); ?></span>
                            <span class="hcv-slide__read-min">3分でわかる</span>
                        </div>

                        <h2 class="hcv-slide__title">
                            <a href="<?php echo esc_url(get_permalink($col->ID)); ?>"><?php echo esc_html($col->post_title); ?></a>
                        </h2>

                        <p class="hcv-slide__excerpt"><?php echo esc_html($excerpt); ?></p>

                        <div class="hcv-slide__actions">
                            <a href="<?php echo esc_url(get_permalink($col->ID)); ?>" class="hcv-slide__btn">サクッと読む →</a>
                            <?php if ($cat_name && ! is_wp_error($cat_url)): ?>
                                <a href="<?php echo esc_url($cat_url); ?>" class="hcv-slide__cat-link">このカテゴリの一覧</a>
                            <?php endif; ?>
                        </div>
                    </div>

                </div>
                </div>
                <?php endforeach; ?>

                <!-- ナビゲーション（2件以上のときだけ表示） -->
                <?php if (count($hero_columns) > 1): ?>
                <button class="hcv-slider__prev" aria-label="前へ">‹</button>
                <button class="hcv-slider__next" aria-label="次へ">›</button>
                <div class="hcv-slider__dots" aria-label="スライダーページ">
                    <?php for ($d = 0; $d < count($hero_columns); $d++): ?>
                        <button
                            class="hcv-dot<?php echo $d === 0 ? ' is-active' : ''; ?>"
                            data-dot="<?php echo (int) $d; ?>"
                            aria-label="<?php echo ($d + 1) . '枚目'; ?>">
                            <span class="dot-progress"></span>
                        </button>
                    <?php endfor; ?>
                </div>
                <?php endif; ?>

            </div><!-- /.hcv-slider -->

            <!-- 右：新着 TOP5 -->
            <?php if (! empty($top5_columns)): ?>
            <aside class="hcv-top5">
                <div class="hcv-top5__head">
                    <span class="hcv-top5__badge">新着 TOP5</span>
                    <span class="hcv-top5__label">サクッと読める3分コラム</span>
                </div>
                <ol class="hcv-top5__list">
                    <?php foreach ($top5_columns as $rank => $col5):
                        $terms5    = get_the_terms($col5->ID, 'column_category');
                        $cat5_name = ($terms5 && ! is_wp_error($terms5)) ? $terms5[0]->name : '';
                        $cat5_url  = ($terms5 && ! is_wp_error($terms5)) ? get_term_link($terms5[0]) : '#';
                        $thumb5    = get_the_post_thumbnail_url($col5->ID, 'thumbnail');
                        $date5     = get_the_date('Y.m.d', $col5->ID);
                        $rank_num  = $rank + 1;
                    ?>
                    <li class="hcv-top5__item">
                        <span class="hcv-top5__rank hcv-top5__rank--<?php echo (int) $rank_num; ?>"><?php echo (int) $rank_num; ?></span>
                        <?php if ($thumb5): ?>
                            <a href="<?php echo esc_url(get_permalink($col5->ID)); ?>" class="hcv-top5__thumb" tabindex="-1" aria-hidden="true">
                                <img src="<?php echo esc_url($thumb5); ?>" alt="" loading="lazy">
                            </a>
                        <?php else: ?>
                            <span class="hcv-top5__thumb hcv-top5__thumb--no-img"></span>
                        <?php endif; ?>
                        <div class="hcv-top5__info">
                            <div class="hcv-top5__meta">
                                <?php if ($cat5_name): ?>
                                    <a href="<?php echo esc_url($cat5_url); ?>" class="hcv-top5__cat">#<?php echo esc_html($cat5_name); ?></a>
                                <?php endif; ?>
                                <span class="hcv-top5__date"><?php echo esc_html($date5); ?></span>
                            </div>
                            <a href="<?php echo esc_url(get_permalink($col5->ID)); ?>" class="hcv-top5__title"><?php echo esc_html($col5->post_title); ?></a>
                        </div>
                    </li>
                    <?php endforeach; ?>
                </ol>
                <a href="<?php echo esc_url($column_archive_url); ?>" class="hcv-top5__more">もっと見る →</a>
            </aside>
            <?php endif; ?>

        </div><!-- /.hcv-main -->
    </div><!-- /.hero-column-view__inner -->
</section><!-- /.hero-column-view -->
