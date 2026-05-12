<?php

/*
 * Template Name: ランキング用テンプレート
 */

get_header();
the_post();

$ranking_id = get_the_ID();
$cookie_key = 'rank_var_' . $ranking_id;

$variant_id = !empty($_COOKIE[$cookie_key]) ? intval($_COOKIE[$cookie_key]) : 0;

// Cookieが指してるvariantが、このrankingの子かチェック
if ($variant_id) {
    $v = get_post($variant_id);
    if (!$v || $v->post_type !== 'ranking_variant' || intval($v->post_parent) !== $ranking_id) {
        $variant_id = 0;
    }
}

if (!$variant_id) {
    $variants = get_posts([
        'post_type'      => 'ranking_variant',
        'posts_per_page' => -1,
        'post_parent'    => $ranking_id,
        'post_status'    => 'publish',
        'orderby'        => 'menu_order',
        'order'          => 'ASC',
    ]);

    $pool = [];
    $total = 0;

    foreach ($variants as $v) {
        $enabled = (bool) get_field('variant_enable', $v->ID);
        if (!$enabled) continue;

        $w = (int) get_field('weight', $v->ID);
        if ($w <= 0) $w = 1;

        $total += $w;
        $pool[] = ['id' => $v->ID, 'w' => $w];
    }

    if ($total > 0) {
        $r = mt_rand(1, $total);
        $acc = 0;
        foreach ($pool as $p) {
            $acc += $p['w'];
            if ($r <= $acc) {
                $variant_id = $p['id'];
                break;
            }
        }
    }

    if ($variant_id) {
        setcookie($cookie_key, (string)$variant_id, time() + 60 * 60 * 24 * 30, COOKIEPATH, COOKIE_DOMAIN);
        $_COOKIE[$cookie_key] = (string)$variant_id;
    }
}

// ここが重要：以降の get_field は全部このIDから読む
$source_id = $variant_id ?: $ranking_id;

function star($n)
{
    $n = floatval($n);
    $o = '';
    for ($i = 1; $i <= 5; $i++) $o .= ($n >= $i ? '★' : ($n >= $i - 0.5 ? '☆' : '☆'));
    return '<span class="stars">' . $o . '</span>';
}
// ページレベルのフィールドは常にメイン投稿から読む
$page_overview    = get_field('page_overview',    $ranking_id);
$result_title     = get_field('result_title',     $ranking_id) ?: 'このランキングで解決できること';
$result_body      = get_field('result_body',      $ranking_id);
$summary_title    = get_field('summary_title',    $ranking_id) ?: 'まとめ';
$summary_body     = get_field('summary_body',     $ranking_id);
$related_col_ids  = get_field('related_columns',  $ranking_id);
$keywords_raw     = get_field('related_keywords', $ranking_id);
?>
<div class="wrapper">
    <article>
        <div class="layout">
            <?php get_sidebar(); ?>
            <main>
                <h1 class="entry-title"><?php the_title(); ?></h1>

                <?php if ($lead = get_field('lead', $source_id)): ?>
                    <p class="entry-lead"><?php echo esc_html($lead); ?></p>
                <?php endif; ?>

                <?php if ($page_overview): ?>
                <section class="ranking-overview">
                    <div class="ranking-overview__body">
                        <?php echo wp_kses_post($page_overview); ?>
                    </div>
                </section>
                <?php endif; ?>

                <?php for ($i = 1; $i <= 10; $i++):
                    $t   = get_field("rank_{$i}_title");
                    if (!$t) continue; // 未入力は飛ばす
                    $logo   = get_field("rank_{$i}_logo", $source_id);
                    $score  = get_field("rank_{$i}_overall", $source_id);
                    $rate   = get_field("rank_{$i}_rate", $source_id);
                    $speed  = get_field("rank_{$i}_speed", $source_id);
                    $ease   = get_field("rank_{$i}_ease", $source_id);
                    $reason = get_field("rank_{$i}_reason", $source_id);
                    $cta_lb = get_field("rank_{$i}_cta_label", $source_id) ?: '申し込む';
                    $cta    = get_field("rank_{$i}_cta_url", $source_id);
                ?>
                    <section id="rank<?php echo $i; ?>" class="rank-item">
                        <header class="rank-item__header">
                            <span class="rank-item__no"><?php echo $i; ?></span>
                            <h2 class="rank-item__title"><?php echo esc_html($t); ?></h2>
                            <div class="rank-item__rating"><?php echo star($score); ?></div>
                        </header>

                        <div class="rank-item__body">
                            <div class="rank-item__left">
                                <?php if ($logo) echo wp_get_attachment_image($logo['ID'], 'medium', false, ['class' => 'rank-item__logo']); ?>
                                <table class="rank-item__scores">
                                    <tbody>
                                        <?php if ($rate)  echo '<tr><th>金利</th><td>' . esc_html($rate) . '</td></tr>'; ?>
                                        <?php if ($speed) echo '<tr><th>スピード</th><td>' . esc_html($speed) . '</td></tr>'; ?>
                                        <?php if ($ease)  echo '<tr><th>使いやすさ</th><td>' . esc_html($ease) . '</td></tr>'; ?>
                                    </tbody>
                                </table>
                                <?php if ($cta) echo '<p><a class="apply-btnred" href="' . esc_url($cta) . '">' . esc_html($cta_lb) . '</a></p>'; ?>
                            </div>

                            <div class="rank-item__right">
                                <h3>ここがポイント</h3>
                                <div class="rank-item__reason"><?php echo wp_kses_post($reason); ?></div>
                            </div>
                        </div>

                        <?php for ($r = 1; $r <= 2; $r++):
                            $persona = get_field("rank_{$i}_review{$r}_persona", $source_id);
                            $stars   = get_field("rank_{$i}_review{$r}_stars", $source_id);
                            $text    = get_field("rank_{$i}_review{$r}_text", $source_id);
                            $avatar  = get_field("rank_{$i}_review{$r}_avatar", $source_id);
                            if (!$persona && !$text) continue;
                        ?>
                            <div class="review">
                                <div class="review__avatar">
                                    <?php
                                    if ($avatar) echo wp_get_attachment_image($avatar['ID'], 'thumbnail', false, ['class' => 'review__avatar-img']);
                                    else echo '<span class="review__avatar-fallback" aria-hidden="true"></span>';
                                    ?>
                                </div>
                                <div class="review__body">
                                    <div class="review__meta">
                                        <span class="review__persona"><?php echo esc_html($persona); ?></span>
                                        <span class="review__stars"><?php echo star($stars); ?></span>
                                    </div>
                                    <p class="review__text"><?php echo esc_html($text); ?></p>
                                </div>
                            </div>
                        <?php endfor; ?>
                    </section>
                <?php endfor; ?>

                <?php if ($result_body): ?>
                <section class="ranking-result">
                    <h2 class="ranking-section-title"><?php echo esc_html($result_title); ?></h2>
                    <div class="ranking-result__body">
                        <?php echo wp_kses_post($result_body); ?>
                    </div>
                </section>
                <?php endif; ?>

                <?php if ($summary_body): ?>
                <section class="ranking-summary">
                    <h2 class="ranking-section-title"><?php echo esc_html($summary_title); ?></h2>
                    <div class="ranking-summary__body">
                        <?php echo wp_kses_post($summary_body); ?>
                    </div>
                </section>
                <?php endif; ?>

                <?php if ($related_col_ids && is_array($related_col_ids)): ?>
                <section class="ranking-related-cols">
                    <h2 class="ranking-section-title">関連コラム</h2>
                    <div class="ranking-related-cols__grid">
                        <?php foreach ($related_col_ids as $col_id):
                            $col = get_post($col_id);
                            if (!$col || $col->post_status !== 'publish') continue;
                            $thumb = get_the_post_thumbnail_url($col_id, 'medium');
                        ?>
                        <article class="ranking-col-card">
                            <?php if ($thumb): ?>
                            <a href="<?php echo esc_url(get_permalink($col_id)); ?>" class="ranking-col-card__thumb-wrap" tabindex="-1" aria-hidden="true">
                                <img src="<?php echo esc_url($thumb); ?>" alt="" loading="lazy" class="ranking-col-card__thumb">
                            </a>
                            <?php endif; ?>
                            <div class="ranking-col-card__body">
                                <h3 class="ranking-col-card__title">
                                    <a href="<?php echo esc_url(get_permalink($col_id)); ?>"><?php echo esc_html($col->post_title); ?></a>
                                </h3>
                            </div>
                        </article>
                        <?php endforeach; ?>
                    </div>
                </section>
                <?php endif; ?>

                <?php
                if ($keywords_raw):
                    $kw_list = array_filter(array_map('trim', explode(',', $keywords_raw)));
                    if ($kw_list):
                ?>
                <section class="ranking-keywords">
                    <h2 class="ranking-section-title">関連キーワード</h2>
                    <ul class="ranking-keywords__list">
                        <?php foreach ($kw_list as $kw): ?>
                        <li class="ranking-keywords__item"><?php echo esc_html($kw); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </section>
                <?php
                    endif;
                endif;
                ?>

            </main>
        </div>
    </article>
</div>
<?php get_footer();
