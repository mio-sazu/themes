<?php
get_header(); ?>
<main id="primary" class="site-main archive-card-company">
  <header class="page-header">
    <h1 class="page-title"><?php post_type_archive_title(); ?></h1>
    <?php if (get_the_archive_description()) : ?>
      <div class="archive-description"><?php echo wp_kses_post(wpautop(get_the_archive_description())); ?></div>
    <?php endif; ?>
  </header>
  <?php if (have_posts()) : ?>
    <div class="cards-grid">
      <?php while (have_posts()) : the_post(); get_template_part('template-parts/loop', 'card'); endwhile; ?>
    </div>
    <nav class="pagination">
      <?php the_posts_pagination(['mid_size'=>2,'prev_text'=>'前へ','next_text'=>'次へ']); ?>
    </nav>
  <?php else : ?>
    <p>該当する会社がありません。</p>
  <?php endif; ?>
</main>
<?php get_footer(); ?>
