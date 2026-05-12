<?php
// template-parts/tab-loop.php

// 固定ページ ID
$settings_page_id = 42;

$title_cf     = get_field('tab_cf_title',    $settings_page_id);
$content_cf   = get_field('tab_cf_content',  $settings_page_id);
$title_rank   = get_field('tab_rank_title',  $settings_page_id);
$content_rank = get_field('tab_rank_content', $settings_page_id);
$title_card   = get_field('tab_card_title',  $settings_page_id);
$content_card = get_field('tab_card_content', $settings_page_id);
?>

<section id="tab">
  <div class="container">
    <ul class="tab">
      <li><a href="#" class="active" data-id="cf"><?php echo esc_html($title_cf); ?></a></li>
      <li><a href="#" data-id="rank"><?php echo esc_html($title_rank); ?></a></li>
      <li><a href="#" data-id="card" class="li_last"><?php echo esc_html($title_card); ?></a></li>
    </ul>

    <?php echo apply_filters('the_content', $content_cf); ?>
    <?php echo apply_filters('the_content', $content_rank); ?>
    <?php echo apply_filters('the_content', $content_card); ?>

  </div>
</section>