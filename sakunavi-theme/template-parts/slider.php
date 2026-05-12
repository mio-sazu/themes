<div class="slider-wrapper">
  <div class="slider">
    <?php
    // 投稿タイプ slider_banner を2回ループしてシームレスに
    for ($i = 0; $i < 2; $i++) {
      $banners = new WP_Query([
        'post_type' => 'slider_banner',
        'posts_per_page' => -1,
        'orderby' => 'menu_order',
        'order' => 'ASC'
      ]);
      if ($banners->have_posts()):
        echo '<ul class="slider-list">';
        while ($banners->have_posts()): $banners->the_post();
          $url = get_the_post_thumbnail_url(get_the_ID(), 'full');
          $link = get_field('リンク先URL');
          $alt  = get_post_meta(get_post_thumbnail_id(), '_wp_attachment_image_alt', true);
          echo '<li class="slider-item">';
          echo    '<a href="' . esc_url($link) . '">';
          echo      '<img src="' . esc_url($url) . '" alt="' . esc_attr($alt) . '">';
          echo    '</a>';
          echo '</li>';
        endwhile;
        echo '</ul>';
      endif;
      wp_reset_postdata();
    }
    ?>
  </div>
</div>