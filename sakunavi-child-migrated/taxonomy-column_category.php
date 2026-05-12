<?php
get_header();

// 安全な抜粋（ブロックでも必ず拾える）
if (!function_exists('sakunavi_safe_excerpt')) {
    function sakunavi_safe_excerpt($len = 30)
    {
        $raw = get_the_content(null, false);
        $text = wp_strip_all_tags(do_shortcode($raw));
        return wp_trim_words($text, $len, '…');
    }
}
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
                                <a href="<?php the_permalink(); ?>">
                                    <?php if (has_post_thumbnail()) the_post_thumbnail('medium'); ?>
                                    <h2 class="post-title"><?php the_title(); ?></h2>
                                    <time datetime="<?php echo esc_attr(get_the_date('c')); ?>">
                                        <?php echo esc_html(get_the_date()); ?>
                                    </time>
                                    <div class="post-excerpt"><?php echo esc_html(sakunavi_safe_excerpt()); ?></div>
                                </a>
                            </li>
                        <?php endwhile; ?>
                    </ul>

                    <div class="pagination">
                        <?php
                        global $wp_query;
                        echo paginate_links([
                            'total'     => $wp_query->max_num_pages,
                            'current'   => max(1, get_query_var('paged', 1)),
                            'mid_size'  => 1,
                            'prev_text' => '« 前へ',
                            'next_text' => '次へ »',
                        ]);
                        ?>
                    </div>

                <?php else : ?>
                    <p>記事がありません。</p>
                <?php endif; ?>
            </main>
        </div>
    </article>
</div>
<?php get_footer();
