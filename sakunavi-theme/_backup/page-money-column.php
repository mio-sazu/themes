<?php
/* Template Name: お金コラム 固定ページ */
get_header();

// 1. 現在のページのスラッグを取得
$slug = get_post_field('post_name', get_queried_object_id());

// 2. WP_Query で money_column タクソノミーを絞り込み
$query = new WP_Query([
    'post_type'      => 'cardloan',     // あなたの CPT スラッグ
    'tax_query'      => [[
        'taxonomy' => 'money_column',
        'field'    => 'slug',
        'terms'    => $slug,
    ]],
    'posts_per_page' => 10,
    'paged'          => get_query_var('paged', 1),
]);

// 3. ループ開始
if ($query->have_posts()) : ?>
    <section class="column-list">
        <h1><?php the_title(); ?></h1>
        <?php while ($query->have_posts()): $query->the_post(); ?>
            <article class="column-item">
                <?php if (has_post_thumbnail()): ?>
                    <a href="<?php the_permalink(); ?>">
                        <?php the_post_thumbnail('medium'); ?>
                    </a>
                <?php endif; ?>
                <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                <?php the_excerpt(); ?>
            </article>
        <?php endwhile; ?>

        <?php
        // ページネーション
        the_posts_pagination([
            'prev_text' => '‹ 前へ',
            'next_text' => '次へ ›',
        ]);
        ?>
    </section>
<?php
else: ?>
    <p>まだ記事がありません。</p>
<?php
endif;
wp_reset_postdata();

get_footer();
