<?php
/*
Template Name: 固定ページ（サイドバーあり）
*/
get_header();
?>

<div class="wrapper">
    <article>
        <?php get_template_part('template-parts/breadcrumbs'); ?>
        <div class="layout">
            <?php get_sidebar(); ?>

            <main>
                <?php the_content(); ?>

            </main>

        </div>
    </article>
</div>

<?php get_footer();
