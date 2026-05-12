<?php
/* Template: taxonomy-ranking_year.php */
get_header(); ?>
<div class="wrapper">
    <article>
        <?php get_template_part('template-parts/breadcrumbs'); ?>
        <div class="layout">
            <?php get_sidebar(); ?>
            <main class="rank-archive">
                <header class="page-header">
                    <h1 class="page-title"><?php single_term_title(); ?></h1>
                    <?php if (term_description()) : ?>
                        <p class="lead"><?php echo esc_html(wp_strip_all_tags(term_description())); ?></p>
                    <?php endif; ?>
                </header>

                <?php if (have_posts()) : ?>
                    <div class="rank-grid">
                        <?php while (have_posts()) : the_post();
                            // 代表画像＆平均スコアは archive と同じ関数を再利用
                            if (!function_exists('sakunavi_ranking_card_image')) {
                                function sakunavi_ranking_card_image($post_id = 0)
                                {
                                    $post_id = $post_id ?: get_the_ID();
                                    $url = '';
                                    if (function_exists('get_field')) {
                                        $items = get_field('items', $post_id);
                                        if (is_array($items) && !empty($items[0])) {
                                            $promo = $items[0]['promo']['url'] ?? '';
                                            $logo = $items[0]['logo']['url'] ?? '';
                                            $url = $promo ?: $logo;
                                        }
                                        if (!$url) {
                                            $p1 = get_field('rank_1_promo', $post_id);
                                            $l1 = get_field('rank_1_logo', $post_id);
                                            $url = (is_array($p1) ? ($p1['url'] ?? '') : (is_string($p1) ? $p1 : '')) ?: (is_array($l1) ? ($l1['url'] ?? '') : (is_string($l1) ? $l1 : ''));
                                        }
                                    }
                                    if (!$url) $url = get_the_post_thumbnail_url($post_id, 'medium');
                                    return $url;
                                }
                            }
                            if (!function_exists('sakunavi_render_stars')) {
                                function sakunavi_render_stars($s)
                                {
                                    if ($s === null || $s === '') return '';
                                    $s = floatval($s);
                                    $o = '';
                                    for ($i = 1; $i <= 5; $i++) $o .= ($s >= $i ? '★' : ($s >= $i - 0.5 ? '☆' : '☆'));
                                    return '<span class="stars" aria-label="rating ' . esc_attr(number_format($s, 1)) . '">' . $o . '</span>';
                                }
                            }
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

                            $img  = sakunavi_ranking_card_image();
                            $lead = function_exists('get_field') ? (get_field('lead') ?: '') : '';
                            $avg  = sakunavi_ranking_avg_overall();
                            $cats = get_the_terms(get_the_ID(), 'ranking_category');
                        ?>
                            <article class="rank-card">
                                <?php if ($img): ?><img class="rank-card__logo" src="<?php echo esc_url($img); ?>" alt="<?php echo esc_attr(get_the_title()); ?>" loading="lazy"><?php endif; ?>
                                <div>
                                    <h2 class="rank-card__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                                    <div class="rank-card__meta">
                                        <?php if ($cats && !is_wp_error($cats)) echo 'カテゴリー: ' . esc_html($cats[0]->name); ?>
                                    </div>
                                    <?php if ($avg !== null): ?>
                                        <div class="rank-card__meta"><?php echo sakunavi_render_stars($avg); ?> <span><?php echo esc_html(number_format($avg, 1)); ?></span></div>
                                    <?php endif; ?>
                                    <?php if ($lead): ?><p class="rank-card__meta"><?php echo esc_html(wp_trim_words($lead, 32)); ?></p><?php endif; ?>
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
                    <p>この年のランキング記事はありません。</p>
                <?php endif; ?>
            </main>
        </div>
    </article>
</div>
<?php get_footer(); ?>