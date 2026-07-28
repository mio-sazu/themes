<?php
/**
 * Template: コラム カテゴリーアーカイブ（column_category タクソノミー）
 * CSS: style.css（グローバル）, assets/css/support.css, assets/css/column.css
 */
get_header();
?>
<div class="wrapper">
    <article>
        <!--パンくず-->
        <?php get_template_part('template-parts/breadcrumbs'); ?>
        <div class="layout">
            <?php get_sidebar(); ?>

            <main>
                <header class="page-header">
                    <h1 class="page-title"><?php echo esc_html(single_term_title('', false)); ?></h1>
                    <?php if (term_description()) : ?>
                        <div class="term-desc"><?php echo wp_kses_post(term_description()); ?></div>
                    <?php endif; ?>
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

                                <?php
                                $kw_terms = get_the_terms(get_the_ID(), 'column_keyword');
                                if ($kw_terms && !is_wp_error($kw_terms)) : ?>
                                    <ul class="post-keyword-list">
                                        <?php foreach (array_slice($kw_terms, 0, 4) as $kw) : ?>
                                            <li>
                                                <a href="<?php echo esc_url(get_term_link($kw)); ?>">
                                                    #<?php echo esc_html($kw->name); ?>
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
<?php get_footer();
