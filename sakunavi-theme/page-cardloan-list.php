<?php

/**
 * Template Name: カードローン会社一覧
 */

get_header();
get_template_part('template-parts/page', 'hero');
?>

<main id="primary" class="site-main">
    <h1 class="page-title"><?php the_title(); ?></h1>

    <?php
    // カードローン投稿を全件取得
    $args = [
        'post_type'      => 'card_loan',
        'posts_per_page' => -1,
        'orderby'        => 'menu_order', // Intuitive CPOで並び替え
        'order'          => 'ASC',
    ];
    $loop = new WP_Query($args);

    if ($loop->have_posts()): ?>
        <ul class="cardloan-list">
            <?php while ($loop->have_posts()): $loop->the_post(); ?>
                <li class="cardloan-item">
                    <a href="<?php the_permalink(); ?>">
                        <?php if (has_post_thumbnail()): ?>
                            <div class="cardloan-thumb">
                                <?php the_post_thumbnail('medium'); ?>
                            </div>
                        <?php endif; ?>
                        <div class="cardloan-info">
                            <h2 class="company-name"><?php the_title(); ?></h2>
                            <p class="loan-rate">金利：<?php echo esc_html(get_field('loan_rate')); ?>％</p>
                            <p class="limit-amount">限度額：<?php echo esc_html(get_field('limit_amount')); ?></p>
                        </div>
                    </a>
                </li>
            <?php endwhile; ?>
        </ul>
    <?php else: ?>
        <p>現在、カードローン情報は登録されていません。</p>
    <?php endif;
    wp_reset_postdata();
    ?>

</main>

<?php
get_footer();
