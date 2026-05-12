<?php
/**
 * Template: コラム アーカイブ
 * CPT: column
 * CSS: style.css（グローバル）, assets/css/support.css, assets/css/column.css
 */
get_header();
?>
<div class="wrapper">
    <article>
        <?php get_template_part('template-parts/breadcrumbs'); ?>

        <div class="layout">
            <?php get_sidebar(); ?>

            <main>
                <header class="page-header">
                    <h1 class="page-title">コラム</h1>
                    <p class="lead">お金に関する基礎知識から実践まで、金融ジャンル別に解説。</p>
                </header>

                <?php if (have_posts()) : ?>
                    <ul class="post-list">
                        <?php while (have_posts()) : the_post(); ?>
                            <li <?php post_class('post-item'); ?>>
                                <?php md_the_coming_soon_badge(); ?>

                                <?php if (has_post_thumbnail()) : ?>
                                    <a href="<?php the_permalink(); ?>" class="post-thumb-link">
                                        <?php the_post_thumbnail('medium'); ?>
                                    </a>
                                <?php endif; ?>

                                <h2 class="post-title">
                                    <a href="<?php the_permalink(); ?>" class="post-title-link">
                                        <?php the_title(); ?>
                                    </a>
                                </h2>

                                <time datetime="<?php echo esc_attr(get_the_date('c')); ?>">
                                    <?php echo esc_html(get_the_date()); ?>
                                </time>

                                <div class="post-excerpt"><?php echo esc_html(sakunavi_safe_excerpt()); ?></div>

                                <?php
                                $terms = get_the_terms(get_the_ID(), 'column_category');
                                if ($terms && !is_wp_error($terms)) : ?>
                                    <ul class="post-cat-list post-cat-list--bottom">
                                        <?php foreach ($terms as $t) : ?>
                                            <li>
                                                <a href="<?php echo esc_url(get_term_link($t)); ?>">
                                                    <?php echo esc_html($t->name); ?>
                                                </a>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php endif; ?>
                            </li>
                        <?php endwhile; ?>
                    </ul>

                    <?php get_template_part('template-parts/pagination'); ?>
                <?php else : ?>
                    <p>記事がありません。</p>
                <?php endif; ?>
            </main>
        </div>
    </article>
</div>
<?php get_footer(); ?>