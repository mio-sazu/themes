<?php get_header(); ?>

<?php
// 絞り込み条件を組み立て
$tax_query = ['relation' => 'AND'];
if (!empty($_GET['purpose'])) {
    $tax_query[] = [
        'taxonomy' => 'purpose',
        'field'    => 'term_id',
        'terms'    => intval($_GET['purpose']),
    ];
}
if (!empty($_GET['condition'])) {
    $tax_query[] = [
        'taxonomy' => 'condition',
        'field'    => 'term_id',
        'terms'    => intval($_GET['condition']),
    ];
}

// カスタムクエリ
$args = array_merge(
    ['post_type' => 'card_loan', 'paged' => get_query_var('paged', 1)],
    $tax_query ? ['tax_query' => $tax_query] : []
);
$query = new WP_Query($args);
?>

<section class="cardloan-archive">
    <?php if ($query->have_posts()): ?>
        <?php while ($query->have_posts()): $query->the_post(); ?>
            <article class="loan-card">
                <h3><?php the_title(); ?></h3>
                <?php the_post_thumbnail('medium'); ?>
                <div class="loan-info"><?php the_excerpt(); ?></div>
                <a href="<?php the_permalink(); ?>" class="apply-btnblue">詳細を見る</a>
            </article>
        <?php endwhile; ?>

        <?php
        // ページネーション
        the_posts_pagination([
            'mid_size'  => 2,
            'prev_text' => '前へ',
            'next_text' => '次へ',
        ]);
        ?>

    <?php else: ?>
        <p>該当するカードローンは見つかりませんでした。</p>
    <?php endif;
    wp_reset_postdata(); ?>
</section>

<?php get_footer(); ?>