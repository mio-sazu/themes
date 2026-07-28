<?php
/* Template: taxonomy-money_column.php */
get_header();

// 現在のターム情報を取得（パンくずやクエリで使うので最初に一度だけ）
$term = get_queried_object();
?>

<div class="wrapper">
    <article>
        <?php get_template_part('template-parts/breadcrumbs'); ?>

        <div class="layout">
            <?php get_sidebar(); ?>

            <main class="column-main">

                <!-- ターム見出し -->
                <header class="page-header">
                    <h1 class="page-title">
                        <?php echo esc_html(single_term_title('', false)); ?>
                    </h1>
                    <?php if (term_description()) : ?>
                        <p class="lead">
                            <?php echo esc_html(wp_strip_all_tags(term_description())); ?>
                        </p>
                    <?php endif; ?>
                </header>

                <!-- おすすめコラム -->
                <?php
                $recommended = new WP_Query([
                    'post_type'      => 'cardloan',
                    'tax_query'      => [[
                        'taxonomy' => 'money_column',
                        'field'    => 'term_id',
                        'terms'    => $term->term_id,
                    ]],
                    'meta_key'       => 'is_recommended', // 任意のメタキー
                    'meta_value'     => '1',
                    'posts_per_page' => 3,
                ]);
                if ($recommended->have_posts()) : ?>
                    <section class="column-section">
                        <h2 class="section-title">おすすめコラム</h2>
                        <div class="column-grid">
                            <?php while ($recommended->have_posts()) : $recommended->the_post(); ?>
                                <div class="column-card">
                                    <a href="<?php the_permalink(); ?>">
                                        <?php if (has_post_thumbnail()) the_post_thumbnail(); ?>
                                    </a>
                                    <div class="card-content">
                                        <h4><?php the_title(); ?></h4>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    </section>
                    <?php wp_reset_postdata(); ?>
                <?php endif; ?>

                <!-- 新着コラム -->
                <section class="column-section">
                    <h2 class="section-title">新着コラム</h2>
                    <div class="column-grid">
                        <?php if (have_posts()) : ?>
                            <?php while (have_posts()) : the_post(); ?>
                                <div class="column-card">
                                    <a href="<?php the_permalink(); ?>">
                                        <?php if (has_post_thumbnail()) the_post_thumbnail(); ?>
                                    </a>
                                    <div class="card-content">
                                        <h4><?php the_title(); ?></h4>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        <?php else : ?>
                            <p>まだコラム記事がありません。</p>
                        <?php endif; ?>
                    </div>
                </section>

            </main>
        </div>
    </article>
</div>

<?php get_footer(); ?>