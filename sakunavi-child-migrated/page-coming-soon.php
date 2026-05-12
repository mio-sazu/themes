<?php
/*
Template Name: Coming Soon Page
*/
get_header();
?>

<div class="wrapper"><!-- ここはテーマの構造に合わせて -->
    <?php
    // ★パンくず：他テンプレからコピペ
    // 例）get_template_part('template-parts/breadcrumb');
    // 例）if ( function_exists( 'bcn_display' ) ) { bcn_display(); }
    ?>
    <article>
        <?php get_template_part('template-parts/breadcrumbs'); ?>
        <div class="layout">
            <?php get_sidebar(); ?>
        <div class="l-main">
        <main class="content-area">
            <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

                <article <?php post_class('coming-soon-page'); ?>>
                    <h1 class="page-title"><?php the_title(); ?></h1>

                    <div class="page-content">
                        <?php the_content(); ?>
                        <!-- ここに「ただいま準備中です」などの説明を書いてOK -->
                    </div>

                    <!-- ▼ 人気の記事（ランダム表示） -->
                    <section class="coming-soon-section popular-posts">
                        <h2 class="section-title">人気の記事はこちら</h2>

                        <ul class="post-list">
                        <?php
                        $popular_query = new WP_Query( array(
                            'post_type'      => 'post',   // 通常の投稿
                            'posts_per_page' => 4,
                            'meta_key'       => 'md_post_views',   // PVを保存しているmetaキー
                            'orderby'        => 'meta_value_num',  // 数値としてソート
                            'order'          => 'DESC',            // 多い順
                            'no_found_rows'  => true,
                            'meta_query'     => array(
                                'relation' => 'AND',
                                // PVが存在するものだけ（ゼロ件の変な並びを避ける）
                                array(
                                    'key'     => 'md_post_views',
                                    'compare' => 'EXISTS',
                                ),
                                // カミングスーン中の記事は除外したい場合
                                array(
                                    'key'     => '_md_coming_soon',
                                    'value'   => '1',
                                    'compare' => '!=',
                                ),
                            ),
                        ) );


                        if ( $popular_query->have_posts() ) :
                            while ( $popular_query->have_posts() ) : $popular_query->the_post(); ?>
                                <li class="post-list-item">
                                    <a href="<?php the_permalink(); ?>">
                                        <?php if ( has_post_thumbnail() ) : ?>
                                            <div class="thumb">
                                                <?php the_post_thumbnail( 'thumbnail' ); ?>
                                            </div>
                                        <?php endif; ?>
                                        <span class="post-title"><?php the_title(); ?></span>
                                    </a>
                                </li>
                            <?php endwhile;
                            wp_reset_postdata();
                        else : ?>
                            <li>表示できる記事がありません。</li>
                        <?php endif; ?>
                        </ul>
                    </section>

                    <!-- ▼ 最新のコラム（新着順） -->
                    <section class="coming-soon-section latest-columns">
                        <h2 class="section-title">最新のコラム</h2>

                        <ul class="post-list">
                        <?php
                        $column_query = new WP_Query( array(
                            'post_type'      => 'column', // ★コラム用カスタム投稿タイプ
                            'posts_per_page' => 4,
                            'orderby'        => 'date',   // 新着順
                            'order'          => 'DESC',
                            'no_found_rows'  => true,
                        ) );

                        if ( $column_query->have_posts() ) :
                            while ( $column_query->have_posts() ) : $column_query->the_post(); ?>
                                <li class="post-list-item">
                                    <a href="<?php the_permalink(); ?>">
                                        <?php if ( has_post_thumbnail() ) : ?>
                                            <div class="thumb">
                                                <?php the_post_thumbnail( 'thumbnail' ); ?>
                                            </div>
                                        <?php endif; ?>
                                        <span class="post-title"><?php the_title(); ?></span>
                                    </a>
                                </li>
                            <?php endwhile;
                            wp_reset_postdata();
                        else : ?>
                            <li>表示できるコラムがありません。</li>
                        <?php endif; ?>
                        </ul>
                    </section>

                </article>

            <?php endwhile; endif; ?>
        </main>

        
        </div><!-- /.l-main -->
    </div><!-- /.layout -->
    </article>
</div><!-- /.wrapper -->

<?php get_footer(); ?>
