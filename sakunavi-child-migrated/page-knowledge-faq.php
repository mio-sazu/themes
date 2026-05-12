<?php
/**
 * Template Name: ナレッジ FAQ一覧
 * Description: よくある質問 トップ10アコーディオン + 全FAQ一覧 + キーワード検索
 */
get_header();

// ── Top10クエリ（kn_display_order が数値で設定されている投稿を昇順最大10件）
$top10_query = new WP_Query([
    'post_type'      => 'knowledge',
    'posts_per_page' => 10,
    'orderby'        => 'meta_value_num',
    'order'          => 'ASC',
    'meta_key'       => 'kn_display_order',
    'meta_query'     => [
        'relation' => 'AND',
        [
            'key'     => 'kn_display_order',
            'value'   => 0,
            'compare' => '>=',
            'type'    => 'NUMERIC',
        ],
        [
            'relation' => 'OR',
            [
                'key'     => 'kn_show_faq_list',
                'value'   => '1',
            ],
            [
                'key'     => 'kn_show_faq_list',
                'compare' => 'NOT EXISTS',
            ],
        ],
    ],
]);

// ── 全FAQクエリ（kn_show_faq_list = 1 またはメタ未設定、タイトル昇順）
$all_query = new WP_Query([
    'post_type'      => 'knowledge',
    'posts_per_page' => -1,
    'orderby'        => 'title',
    'order'          => 'ASC',
    'meta_query'     => [
        'relation' => 'OR',
        [
            'key'     => 'kn_show_faq_list',
            'value'   => '1',
        ],
        [
            'key'     => 'kn_show_faq_list',
            'compare' => 'NOT EXISTS',
        ],
    ],
]);
?>

<div class="wrapper">
    <article>
        <?php get_template_part('template-parts/breadcrumbs'); ?>
        <div class="layout">
            <?php get_sidebar(); ?>
            <main class="kn-faq-page">

                <header class="page-header">
                    <h1 class="page-title"><?php the_title(); ?></h1>
                    <?php
                    $page_content = get_the_content();
                    if ($page_content):
                        echo '<div class="page-lead">';
                        the_content();
                        echo '</div>';
                    else:
                    ?>
                    <p class="page-lead">カードローンや借入に関するよくある質問をまとめました。気になる疑問をキーワードで探すか、カテゴリから選んでください。</p>
                    <?php endif; ?>
                </header>

                <!-- ══════════════════════════════
                     検索ボックス
                ══════════════════════════════ -->
                <div class="kn-faq-search" role="search">
                    <label for="kn-faq-search-input" class="kn-faq-search__label">キーワードで探す</label>
                    <div class="kn-faq-search__wrap">
                        <input
                            type="search"
                            id="kn-faq-search-input"
                            class="kn-faq-search__input"
                            placeholder="例：審査、金利、返済、在籍確認..."
                            autocomplete="off"
                        >
                        <span class="kn-faq-search__icon" aria-hidden="true">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                        </span>
                    </div>
                    <p class="kn-faq-search__result" id="kn-faq-search-result" aria-live="polite" hidden></p>
                </div>

                <!-- ══════════════════════════════
                     よくある質問 トップ10
                ══════════════════════════════ -->
                <?php if ($top10_query->have_posts()): ?>
                <section class="kn-faq-top-section" id="kn-faq-top">
                    <h2 class="kn-faq-section-title">よくある質問 トップ10</h2>
                    <div class="kn-faq-box">
                        <?php
                        $rank = 0;
                        while ($top10_query->have_posts()): $top10_query->the_post();
                            $rank++;
                            $pid       = get_the_ID();
                            $one_liner = function_exists('get_field') ? get_field('one_liner',    $pid) : '';
                            $short_ans = function_exists('get_field') ? get_field('short_answer', $pid) : '';
                            $cta_label = function_exists('get_field') ? get_field('kn_cta_label', $pid) : '';
                            $cta_label = $cta_label ?: '詳しく見る';
                            $kn_types  = get_the_terms($pid, 'knowledge_type');
                            $type_slug = ($kn_types && !is_wp_error($kn_types)) ? $kn_types[0]->slug : '';
                            $type_name = ($kn_types && !is_wp_error($kn_types)) ? $kn_types[0]->name : '';
                        ?>
                        <div class="kn-faq-item"
                             data-search-title="<?php echo esc_attr(mb_strtolower(get_the_title())); ?>"
                             data-search-body="<?php echo esc_attr(mb_strtolower($one_liner . ' ' . $short_ans)); ?>">
                            <button class="kn-faq-question" type="button" aria-expanded="false">
                                <span class="kn-faq-question__rank"><?php echo esc_html($rank); ?></span>
                                <span class="kn-faq-question__text">
                                    <?php the_title(); ?>
                                    <?php if ($type_slug): ?>
                                    <span class="kn-type-badge kn-type-badge--<?php echo esc_attr($type_slug); ?>"><?php echo esc_html($type_name); ?></span>
                                    <?php endif; ?>
                                </span>
                                <span class="kn-faq-icon" aria-hidden="true">+</span>
                            </button>
                            <div class="kn-faq-answer-box" hidden>
                                <?php if ($one_liner): ?>
                                <p><?php echo esc_html($one_liner); ?></p>
                                <?php endif; ?>
                                <?php if ($short_ans): ?>
                                <div><?php echo nl2br(esc_html($short_ans)); ?></div>
                                <?php endif; ?>
                                <p class="kn-faq-more">
                                    <a href="<?php the_permalink(); ?>"><?php echo esc_html($cta_label); ?></a>
                                </p>
                            </div>
                        </div>
                        <?php endwhile; wp_reset_postdata(); ?>
                    </div>
                </section>
                <?php endif; ?>

                <!-- ══════════════════════════════
                     FAQ 全一覧
                ══════════════════════════════ -->
                <?php if ($all_query->have_posts()):

                    // カテゴリ別に整理
                    $cat_groups   = [];
                    $no_cat_items = [];

                    while ($all_query->have_posts()): $all_query->the_post();
                        $pid        = get_the_ID();
                        $one_liner  = function_exists('get_field') ? get_field('one_liner', $pid) : '';
                        $kn_types   = get_the_terms($pid, 'knowledge_type');
                        $type_slug  = ($kn_types && !is_wp_error($kn_types)) ? $kn_types[0]->slug : '';
                        $type_name  = ($kn_types && !is_wp_error($kn_types)) ? $kn_types[0]->name : '';
                        $cats       = get_the_terms($pid, 'knowledge_category');

                        $item_data = [
                            'title'     => get_the_title(),
                            'permalink' => get_permalink(),
                            'one_liner' => $one_liner,
                            'type_slug' => $type_slug,
                            'type_name' => $type_name,
                        ];

                        if ($cats && !is_wp_error($cats)) {
                            $cat = $cats[0];
                            if (!isset($cat_groups[$cat->term_id])) {
                                $cat_groups[$cat->term_id] = ['name' => $cat->name, 'items' => []];
                            }
                            $cat_groups[$cat->term_id]['items'][] = $item_data;
                        } else {
                            $no_cat_items[] = $item_data;
                        }
                    endwhile;
                    wp_reset_postdata();
                ?>
                <section class="kn-faq-list-section" id="kn-faq-list">
                    <h2 class="kn-faq-section-title">FAQ一覧</h2>

                    <p class="kn-faq-no-results" id="kn-faq-no-results" hidden>
                        該当するFAQは見つかりませんでした。キーワードを変えてお試しください。
                    </p>

                    <?php foreach ($cat_groups as $tid => $group): ?>
                    <div class="kn-faq-cat-group" data-cat-name="<?php echo esc_attr($group['name']); ?>">
                        <h3 class="kn-faq-cat-heading"><?php echo esc_html($group['name']); ?></h3>
                        <ul class="kn-faq-all-list">
                            <?php foreach ($group['items'] as $item): ?>
                            <li class="kn-faq-all-item"
                                data-search-title="<?php echo esc_attr(mb_strtolower($item['title'])); ?>"
                                data-search-body="<?php echo esc_attr(mb_strtolower($item['one_liner'])); ?>">
                                <a href="<?php echo esc_url($item['permalink']); ?>" class="kn-faq-all-item__link">
                                    <span class="kn-faq-all-item__inner">
                                        <?php if ($item['type_slug']): ?>
                                        <span class="kn-type-badge kn-type-badge--<?php echo esc_attr($item['type_slug']); ?>"><?php echo esc_html($item['type_name']); ?></span>
                                        <?php endif; ?>
                                        <span class="kn-faq-all-item__q"><?php echo esc_html($item['title']); ?></span>
                                    </span>
                                    <?php if ($item['one_liner']): ?>
                                    <span class="kn-faq-all-item__a"><?php echo esc_html(mb_strimwidth($item['one_liner'], 0, 80, '…')); ?></span>
                                    <?php endif; ?>
                                </a>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <?php endforeach; ?>

                    <?php if ($no_cat_items): ?>
                    <div class="kn-faq-cat-group kn-faq-cat-group--other">
                        <ul class="kn-faq-all-list">
                            <?php foreach ($no_cat_items as $item): ?>
                            <li class="kn-faq-all-item"
                                data-search-title="<?php echo esc_attr(mb_strtolower($item['title'])); ?>"
                                data-search-body="<?php echo esc_attr(mb_strtolower($item['one_liner'])); ?>">
                                <a href="<?php echo esc_url($item['permalink']); ?>" class="kn-faq-all-item__link">
                                    <span class="kn-faq-all-item__inner">
                                        <?php if ($item['type_slug']): ?>
                                        <span class="kn-type-badge kn-type-badge--<?php echo esc_attr($item['type_slug']); ?>"><?php echo esc_html($item['type_name']); ?></span>
                                        <?php endif; ?>
                                        <span class="kn-faq-all-item__q"><?php echo esc_html($item['title']); ?></span>
                                    </span>
                                    <?php if ($item['one_liner']): ?>
                                    <span class="kn-faq-all-item__a"><?php echo esc_html(mb_strimwidth($item['one_liner'], 0, 80, '…')); ?></span>
                                    <?php endif; ?>
                                </a>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <?php endif; ?>
                </section>

                <?php else: ?>
                <p class="kn-faq-empty">よくある質問はまだ登録されていません。</p>
                <?php endif; ?>

            </main>
        </div>
    </article>
</div>

<?php get_footer(); ?>
