<?php
/**
 * Template: 固定ページ（デフォルト）
 * 特定のテンプレートが割り当てられていない固定ページ（利用規約・サイトマップ・サンプルページ等）用の
 * 最低限のフォールバックです。このファイルが無いと、テーマにpage.php/singular.phpが存在しないため
 * 最終的に index.php（空のフォールバック）まで落ちてしまい、ページが真っ白になります。
 */
get_header(); ?>

<div class="wrapper">
    <article>
        <?php get_template_part('template-parts/breadcrumbs'); ?>
        <main>
            <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
                <article <?php post_class(); ?>>
                    <h1 class="page-title"><?php the_title(); ?></h1>
                    <div class="page-content">
                        <?php the_content(); ?>
                    </div>
                </article>
            <?php endwhile;
            else: ?>
                <p>ページが見つかりませんでした。</p>
            <?php endif; ?>
        </main>
    </article>
</div>

<?php get_footer(); ?>
