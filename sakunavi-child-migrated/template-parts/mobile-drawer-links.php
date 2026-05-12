<?php
if (!defined('ABSPATH')) exit;
?>

<div class="drawer-side-links">
    <div class="drawer-box">
        <div class="drawer-box__title">カテゴリー</div>
        <ul class="drawer-box__list">
        <?php
        $terms = get_terms([
            'taxonomy'   => 'column_category',
            'hide_empty' => true,
        ]);

        if (!is_wp_error($terms) && !empty($terms)) :
            foreach ($terms as $term) : ?>
            <li>
                <a href="<?php echo esc_url(get_term_link($term)); ?>">
                <?php echo esc_html($term->name); ?>
                </a>
            </li>
            <?php endforeach;
        endif;
        ?>
        </ul>
    </div>

    <div class="drawer-box">
        <div class="drawer-box__title">人気の記事</div>
        <ul class="drawer-box__list">
        <?php
        $popular = new WP_Query([
            'post_type'      => 'column',
            'posts_per_page' => 5,
            'meta_key'       => 'md_post_views',
            'orderby'        => 'meta_value_num',
            'order'          => 'DESC',
        ]);

        if ($popular->have_posts()) :
            while ($popular->have_posts()) : $popular->the_post(); ?>
            <li>
                <a href="<?php the_permalink(); ?>">
                <?php the_title(); ?>
                </a>
            </li>
            <?php endwhile;
            wp_reset_postdata();
        endif;
        ?>
        </ul>
    </div>

    <div class="drawer-box drawer-box--banners">
        <div class="drawer-box__title">おすすめ</div>
        <div class="drawer-banners">
        <a href="#">
            <img src="<?php echo esc_url(get_theme_file_uri('assets/img/common/banner1.jpg')); ?>" alt="バナー1">
        </a>
        <a href="#">
            <img src="<?php echo esc_url(get_theme_file_uri('assets/img/common/banner2.jpg')); ?>" alt="バナー2">
        </a>
        </div>
    </div>
</div>