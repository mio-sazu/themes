<?php

/**
 * template-parts/page-hero.php
 * 各ページのヒーロービュー
 */

// 投稿・固定ページのアイキャッチURLを取得。なければデフォルト画像を指定
if (has_post_thumbnail()) {
    $bg = get_the_post_thumbnail_url(get_the_ID(), 'full');
} else {
    $bg = get_template_directory_uri() . '/assets/images/default-hero.jpg';
}
?>
<section class="page-hero" style="background-image: url('<?php echo esc_url($bg); ?>');">
    <div class="page-hero-overlay">
        <h1 class="page-hero-title"><?php echo esc_html(get_the_title()); ?></h1>
    </div>
</section>