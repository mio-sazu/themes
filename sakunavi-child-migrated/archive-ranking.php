<?php
/* Template: archive-ranking.php */
get_header();

// 便利関数：★表示
if (!function_exists('sakunavi_render_stars')) {
    function sakunavi_render_stars($score)
    {
        if ($score === null || $score === '') return '';
        $score = floatval($score);
        $out = '';
        for ($i = 1; $i <= 5; $i++) $out .= ($score >= $i ? '★' : ($score >= $i - 0.5 ? '☆' : '☆'));
        return '<span class="stars" aria-label="rating ' . esc_attr(number_format($score, 1)) . '">' . $out . '</span>';
    }
}

// 便利関数：アーカイブカード用 代表画像（ACF優先→アイキャッチ）
if (!function_exists('sakunavi_ranking_card_image')) {
    function sakunavi_ranking_card_image($post_id = 0)
    {
        $post_id = $post_id ?: get_the_ID();
        $url = '';
        if (function_exists('get_field')) {
            // ACF PRO: items[0].promo or logo
            $items = get_field('items', $post_id);
            if (is_array($items) && !empty($items[0])) {
                $promo = $items[0]['promo']['url'] ?? '';
                $logo  = $items[0]['logo']['url']  ?? '';
                $url = $promo ?: $logo;
            }
            // ACF Free 固定フィールド: rank_1_promo or rank_1_logo
            if (!$url) {
                $p1 = get_field('rank_1_promo', $post_id);
                $l1 = get_field('rank_1_logo',  $post_id);
                $url = (is_array($p1) ? ($p1['url'] ?? '') : (is_string($p1) ? $p1 : '')) ?: (is_array($l1) ? ($l1['url'] ?? '') : (is_string($l1) ? $l1 : ''));
            }
        }
        if (!$url) $url = get_the_post_thumbnail_url($post_id, 'medium');
        return $url;
    }
}

// 便利関数：平均スコア（早見値）。ACF PRO/Freeの「overall」を集計
if (!function_exists('sakunavi_ranking_avg_overall')) {
    function sakunavi_ranking_avg_overall($post_id = 0)
    {
        $post_id = $post_id ?: get_the_ID();
        if (!function_exists('get_field')) return null;

        $sum = 0;
        $n = 0;
        $items = get_field('items', $post_id);
        if (is_array($items) && !empty($items)) {
            foreach ($items as $it) {
                if (isset($it['overall']) && $it['overall'] !== '') {
                    $sum += floatval($it['overall']);
                    $n++;
                }
            }
        } else {
            // Free: rank_1_overall … rank_10_overall
            for ($i = 1; $i <= 10; $i++) {
                $v = get_field("rank_{$i}_overall", $post_id);
                if ($v !== '' && $v !== null) {
                    $sum += floatval($v);
                    $n++;
                }
            }
        }
        return $n ? $sum / $n : null;
    }
}
?>
<div class="wrapper">
    <article>
        <?php get_template_part('template-parts/breadcrumbs'); ?>
        <div class="layout">
            <?php get_sidebar(); ?>
            <main class="rank-archive">
                <header class="page-header">
                    <h1 class="page-title"><?php post_type_archive_title(); ?></h1>
                    <?php if (is_paged()) : ?>
                        <p class="lead">ページ <?php echo intval(get_query_var('paged')); ?></p>
                    <?php endif; ?>
                </header>

                <?php if (have_posts()) : ?>
                    <div class="rank-grid">
                        <?php while (have_posts()) : the_post();
                            $img   = sakunavi_ranking_card_image();
                            $lead  = function_exists('get_field') ? (get_field('lead') ?: '') : '';
                            $avg   = sakunavi_ranking_avg_overall();
                            $years = get_the_terms(get_the_ID(), 'ranking_year');
                            $cats  = get_the_terms(get_the_ID(), 'ranking_category');
                        ?>
                            <article class="rank-card">
                                <?php if ($img): ?>
                                    <img class="rank-card__logo" src="<?php echo esc_url($img); ?>" alt="<?php echo esc_attr(get_the_title()); ?>" loading="lazy">
                                <?php endif; ?>
                                <div>
                                    <h2 class="rank-card__title">
                                        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                    </h2>
                                    <div class="rank-card__meta">
                                        <?php if ($years && !is_wp_error($years)) echo '年: ' . esc_html($years[0]->name) . '　'; ?>
                                        <?php if ($cats && !is_wp_error($cats))  echo 'カテゴリー: ' . esc_html($cats[0]->name); ?>
                                    </div>
                                    <?php if ($avg !== null): ?>
                                        <div class="rank-card__meta"><?php echo sakunavi_render_stars($avg); ?> <span><?php echo esc_html(number_format($avg, 1)); ?></span></div>
                                    <?php endif; ?>
                                    <?php if ($lead): ?>
                                        <p class="rank-card__meta"><?php echo esc_html(wp_trim_words($lead, 32)); ?></p>
                                    <?php endif; ?>
                                    <p class="rank-card__more"><a href="<?php the_permalink(); ?>">詳細を見る</a></p>
                                </div>
                            </article>
                        <?php endwhile; ?>
                    </div>

                    <div class="pagination">
                        <?php echo paginate_links([
                            'total'   => $wp_query->max_num_pages,
                            'current' => max(1, get_query_var('paged')),
                            'mid_size' => 1,
                            'prev_text' => '« 前へ',
                            'next_text' => '次へ »',
                        ]); ?>
                    </div>
                <?php else: ?>
                    <p>ランキング記事がありません。</p>
                <?php endif; ?>
            </main>
        </div>
    </article>
</div>
<?php get_footer(); ?>