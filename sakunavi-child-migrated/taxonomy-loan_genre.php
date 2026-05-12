<?php
/**
 * Template: ローンジャンル タクソノミーアーカイブ（loan_genre）
 * CSS: style.css（グローバル）, assets/css/company.css
 */
get_header(); ?>
<main id="primary" class="site-main taxonomy-loan-genre">
  <header class="page-header">
    <h1 class="page-title"><?php single_term_title(); ?></h1>
    <?php $desc = term_description(); if ($desc): ?>
      <div class="taxonomy-description"><?php echo wp_kses_post($desc); ?></div>
    <?php endif; ?>
  </header>
  <?php if (have_posts()) : ?>
    <div class="cards-grid">
      <?php while (have_posts()) : the_post(); get_template_part('template-parts/loop', 'card'); endwhile; ?>
    </div>
    <nav class="pagination"><?php the_posts_pagination(['mid_size'=>2,'prev_text'=>'前へ','next_text'=>'次へ']); ?></nav>
  <?php else : ?>
    <p>該当する会社がありません。</p>
  <?php endif; ?>
</main>
<?php get_footer(); ?>
