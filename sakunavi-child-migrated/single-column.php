<?php
/**
 * Template: 個別コラム記事
 * CPT: column
 * CSS: style.css（グローバル）, assets/css/support.css, assets/css/column.css
 */
get_header(); ?>

<div class="wrapper">
    <article>
        <?php get_template_part('template-parts/breadcrumbs'); ?>
        <div class="layout">
            <?php get_sidebar(); ?>

            <main>
                <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
                    <?php
                    // ============================
                    // コラムカテゴリ取得
                    // ============================
                    $terms = get_the_terms(get_the_ID(), 'column_category');
                    $term_ids = (!empty($terms) && !is_wp_error($terms))
                        ? wp_list_pluck($terms, 'term_id')
                        : [];

                    // ============================
                    // FAQ取得（既存：手動入力）
                    // ============================
                    $faq_items = function_exists('sakunavi_get_column_faq_items')
                        ? sakunavi_get_column_faq_items(get_the_ID())
                        : [];

                    // ============================
                    // ナレッジ連携フィールド取得
                    // ============================
                    $col_id                 = get_the_ID();
                    $kn_intro_faq           = function_exists('get_field') ? get_field('col_intro_faq',           $col_id) : [];
                    $kn_article_faq         = function_exists('get_field') ? get_field('col_article_faq',         $col_id) : [];
                    $kn_related_faq_bottom  = function_exists('get_field') ? get_field('col_related_faq_bottom',  $col_id) : [];
                    $kn_glossary_bottom     = function_exists('get_field') ? get_field('col_glossary_bottom',     $col_id) : [];

                    // ============================
                    // 本文内ショートコード [column_faq] の有無
                    // ============================
                    $has_faq_shortcode = has_shortcode(get_post_field('post_content', get_the_ID()), 'column_faq');

                    // ============================
                    // 関連記事（同カテゴリ優先 → なければ最新記事）
                    // ============================
                    $related_args = [
                        'post_type'      => 'column',
                        'posts_per_page' => 4,
                        'post__not_in'   => [get_the_ID()],
                        'orderby'        => 'date',
                        'order'          => 'DESC',
                    ];

                    if (!empty($term_ids)) {
                        $related_args['tax_query'] = [[
                            'taxonomy'         => 'column_category',
                            'field'            => 'term_id',
                            'terms'            => $term_ids,
                            'include_children' => true,
                        ]];
                    }

                    $rel = new WP_Query($related_args);

                    if (!$rel->have_posts()) {
                        $related_args = [
                            'post_type'      => 'column',
                            'posts_per_page' => 4,
                            'post__not_in'   => [get_the_ID()],
                            'orderby'        => 'date',
                            'order'          => 'DESC',
                        ];
                        $rel = new WP_Query($related_args);
                    }
                    ?>

                    <article <?php post_class('entry'); ?>>
                        <header class="entry-header">
                            <h1 class="entry-title"><?php the_title(); ?></h1>

                            <?php if ($kn_intro_faq && is_array($kn_intro_faq)): ?>
                            <?php get_template_part('template-parts/knowledge/related-links', null, [
                                'knowledge_ids' => $kn_intro_faq,
                                'title'         => 'この記事に関連するFAQ',
                            ]); ?>
                            <?php endif; ?>

                            <div class="entry-meta">
                                <span class="entry-meta__updated">
                                    <span class="entry-meta__label">更新</span>
                                    <time datetime="<?php echo esc_attr(get_the_modified_date('c')); ?>">
                                        <?php echo esc_html(get_the_modified_date()); ?>
                                    </time>
                                </span>
                            </div>

                            <?php if (has_post_thumbnail()) : ?>
                                <figure class="entry-thumb">
                                    <?php the_post_thumbnail('large', ['loading' => 'eager']); ?>
                                </figure>
                            <?php endif; ?>
                        </header>

                        <div class="entry-content">
                            <?php the_content(); ?>
                        </div>

                        <?php if (!empty($faq_items) && !$has_faq_shortcode) : ?>
                            <section class="faq-section faq-section--column">
                                <h2>よくある質問</h2>
                                <div class="faq-box">
                                    <?php foreach ($faq_items as $item) : ?>
                                        <div class="faq-item">
                                            <button class="faq-question" type="button" aria-expanded="false">
                                                <span class="faq-question__text">
                                                    <?php echo esc_html($item['question']); ?>
                                                </span>
                                                <span class="faq-icon" aria-hidden="true">+</span>
                                            </button>
                                            <div class="faq-answer" hidden>
                                                <p><?php echo nl2br(esc_html($item['answer'])); ?></p>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </section>
                        <?php endif; ?>

                        <?php if ($kn_article_faq && is_array($kn_article_faq)): ?>
                        <?php get_template_part('template-parts/knowledge/accordion', null, [
                            'knowledge_ids' => $kn_article_faq,
                            'title'         => 'よくある質問',
                        ]); ?>
                        <?php endif; ?>

                        <?php if ($rel->have_posts()) : ?>
                            <section class="related">
                                <h2>あわせて読みたい記事</h2>
                                <ul class="post-list -related">
                                    <?php while ($rel->have_posts()) : $rel->the_post(); ?>
                                        <li>
                                            <a href="<?php the_permalink(); ?>">
                                                <?php if (has_post_thumbnail()) : ?>
                                                    <figure class="related-thumb">
                                                        <?php the_post_thumbnail('medium'); ?>
                                                    </figure>
                                                <?php endif; ?>

                                                <div class="related-body">
                                                    <time class="related-date" datetime="<?php echo esc_attr(get_the_date('c')); ?>">
                                                        <?php echo esc_html(get_the_date()); ?>
                                                    </time>

                                                    <span class="related-title">
                                                        <?php the_title(); ?>
                                                    </span>
                                                </div>
                                            </a>
                                        </li>
                                    <?php endwhile; ?>
                                </ul>
                            </section>
                            <?php wp_reset_postdata(); ?>
                        <?php endif; ?>

                        <?php if ($kn_related_faq_bottom && is_array($kn_related_faq_bottom)): ?>
                        <?php get_template_part('template-parts/knowledge/related-links', null, [
                            'knowledge_ids' => $kn_related_faq_bottom,
                            'title'         => '関連FAQ',
                        ]); ?>
                        <?php endif; ?>

                        <?php if ($kn_glossary_bottom && is_array($kn_glossary_bottom)): ?>
                        <div class="kn-glossary-chips">
                            <p class="kn-glossary-chips__label">この記事でよく出る用語</p>
                            <ul class="kn-glossary-chips__list">
                                <?php foreach ($kn_glossary_bottom as $gid):
                                    $gp = get_post($gid);
                                    if (!$gp || $gp->post_status !== 'publish') continue;
                                ?>
                                <li class="kn-glossary-chips__item">
                                    <a href="<?php echo esc_url(get_permalink($gid)); ?>"><?php echo esc_html($gp->post_title); ?></a>
                                </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                        <?php endif; ?>

                    </article>

                <?php endwhile;
                else: ?>
                    <p>記事がありません。</p>
                <?php endif; ?>
            </main>

        </div>
    </article>
</div>

<?php get_footer(); ?>