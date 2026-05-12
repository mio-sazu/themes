<?php get_header(); ?>

<article class="cardloan-detail">
    <h1><?php the_title(); ?></h1>
    <div class="cardloan-meta">
        <p>金利：<?php echo esc_html(get_field('loan_rate')); ?>％</p>
        <p>限度額：<?php echo esc_html(get_field('limit_amount')); ?></p>
        <p>即日融資：<?php echo get_field('same_day') ? '可能' : '不可'; ?></p>
        <p>種類：<?php echo esc_html(get_field('loan_type')); ?></p>
        <p>評価：<?php echo esc_html(get_field('evaluation_score')); ?> / 5</p>
    </div>

    <div class="cardloan-content">
        <?php the_content(); ?>
    </div>

    <div class="cardloan-apply">
        <a href="<?php echo esc_url(get_field('affiliate_link')); ?>" class="btn-apply" target="_blank">
            公式サイトから申し込む
        </a>
    </div>
</article>

<?php get_footer(); ?>