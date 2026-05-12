<?php
/**
 * Template Name: サポートページ（Loan Support）
 * Description: side/loan-support.html をWP化したページテンプレート。本文HTMLを貼り付けて使います。
 */

$p = get_stylesheet_directory() . '/assets/css/support.css';
wp_enqueue_style(
  'sakunavi-support',
  get_stylesheet_directory_uri() . '/assets/css/support.css',
  [], file_exists($p) ? filemtime($p) : null
);

get_header(); ?>
<div class="page-support">
  <div class="wrapper">
    <?php while ( have_posts() ) : the_post(); ?>
      <article class="entry-content">
        <?php the_content(); ?>
      </article>
    <?php endwhile; ?>
  </div>
</div>
<?php get_footer(); ?>
