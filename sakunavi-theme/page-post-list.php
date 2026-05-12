<?php

/**
 * Template Name: 記事一覧（新着順）
 */
get_header();

// 小さめヒーロー：アイキャッチ or デフォルトグラデ
if (has_post_thumbnail()) {
    $hero = get_the_post_thumbnail_url(get_the_ID(), 'full');
} else {
    $hero = '';
}
?>
<section class="page-hero is-compact" style="<?php echo $hero ? "background-image:url('" . esc_url($hero) . "');" : ''; ?>">
    <?php if (! $hero): ?>
        <div class="page-hero-gradient"></div>
    <?php endif; ?>
    <div class="page-hero-overlay">
        <h1 class="page-hero-title"><?php the_title(); ?></h1>
    </div>
</section>

<main class="post-archive wrapper">
    <header class="archive-head">
        <p class="archive-sub">最新の記事を新着順でお届けします</p>
    </header>

    <?php
    // ページネーション
    $paged = get_query_var('paged') ? (int) get_query_var('paged') : 1;

    // 新着順で取得
    $args = [
        'post_type'      => 'post',
        'post_status'    => 'publish',
        'posts_per_page' => 12,
        'orderby'        => 'date',
        'order'          => 'DESC',
        'paged'          => $paged,
    ];
    $query = new WP_Query($args);
    ?>

    <?php if ($query->have_posts()) : ?>
        <ul class="post-grid">
            <?php while ($query->have_posts()) : $query->the_post(); ?>
                <li class="post-card">
                    <a class="post-link" href="<?php the_permalink(); ?>" aria-label="<?php the_title_attribute(); ?>">
                        <div class="post-thumb">
                            <?php if (has_post_thumbnail()) : ?>
                                <?php the_post_thumbnail('medium_large', ['loading' => 'lazy']); ?>
                            <?php else: ?>
                                <div class="thumb-ph">No Image</div>
                            <?php endif; ?>
                            <?php
                            // 公開から7日以内なら NEW
                            $days = 7;
                            $new = (time() - get_the_time('U')) < (60 * 60 * 24 * $days);
                            if ($new) echo '<span class="badge-new">NEW</span>';
                            ?>
                        </div>

                        <div class="post-body">
                            <div class="post-meta">
                                <?php
                                $cats = get_the_category();
                                if (!empty($cats)) :
                                    $cat = $cats[0];
                                ?>
                                    <span class="cat-badge"><?php echo esc_html($cat->name); ?></span>
                                <?php endif; ?>
                                <time class="post-date" datetime="<?php echo esc_attr(get_the_date('c')); ?>">
                                    <?php echo esc_html(get_the_date('Y.m.d')); ?>
                                </time>
                            </div>

                            <h2 class="post-title"><?php the_title(); ?></h2>
                            <p class="post-excerpt">
                                <?php echo esc_html(wp_trim_words(get_the_excerpt(), 42, '…')); ?>
                            </p>
                        </div>
                    </a>
                </li>
            <?php endwhile; ?>
        </ul>

        <nav class="pagination" aria-label="記事ページネーション">
            <?php
            echo paginate_links([
                'total'        => $query->max_num_pages,
                'current'      => $paged,
                'mid_size'     => 2,
                'prev_text'    => '← 前へ',
                'next_text'    => '次へ →',
            ]);
            ?>
        </nav>

    <?php else: ?>
        <p class="no-posts">まだ記事がありません。</p>
    <?php endif;
    wp_reset_postdata(); ?>
</main>

<?php get_footer(); ?>