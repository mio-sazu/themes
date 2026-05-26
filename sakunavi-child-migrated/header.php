<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
  <meta charset="<?php bloginfo('charset'); ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>サクっとお金ナビ｜生活に役立つお金の情報サイト</title>
  <!-- キーワードは複数形×content -->
  <meta name="keywords" content="お金,カードローン,比較,金融">
  <!-- 欠落しがちなディスクリプションも追加 -->
  <meta name="description" content="金融のプロが厳選したカードローンを比 較・ランキング形式でご紹介するウェブサービスです">
  <!-- Google Tag Manager 
  <script>
    (function(w, d, s, l, i) {
      w[l] = w[l] || [];
      w[l].push({
        'gtm.start': new Date().getTime(),
        event: 'gtm.js'
      });
      var f = d.getElementsByTagName(s)[0],
        j = d.createElement(s),
        dl = l != 'dataLayer' ? '&l=' + l : '';
      j.async = true;
      j.src =
        'https://www.googletagmanager.com/gtm.js?id=' + i + dl;
      f.parentNode.insertBefore(j, f);
    })(window, document, 'script', 'dataLayer', 'GTM-PVPSZSD5');
  </script>-->
  <!-- End Google Tag Manager -->
  <script type="application/ld+json">
      {
        "@context": "https://schema.org",
        "@type": "Article",
        "headline": "記事のタイトル",
        "image": [
          "アイキャッチ画像のURL"
        ],
        "author": {
          "@type": "Person",
          "name": "著者名"
        }
      }
  </script>
  
  <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>

  <header class="site-header">
    <div class="header-inner">

      <div class="header-inner">
        <div class="header-logo-menu">
          <div class="logo">
            <a href="<?php echo esc_url(home_url('/')); ?>">
              <img src="<?php echo get_template_directory_uri(); ?>/images/index/logo.png" alt="<?php bloginfo('name'); ?> ロゴ">
            </a>
          </div>

          <!-- PC用メニュー -->
          <div class="menu-bar">
            <?php
            wp_nav_menu([
              'theme_location' => 'header-menu',
              'container'      => false,
              'menu_class'     => 'main-menu',
              'fallback_cb'    => false,
            ]);
            ?>
          </div>

          <!-- ハンバーガー用 -->
          <input id="nav-input" type="checkbox" class="nav-unshown">
          <label id="nav-open" for="nav-input"><span></span></label>
          <label id="nav-close" for="nav-input" class="nav-unshown"></label>

          <nav id="nav-content">
            <?php
            wp_nav_menu([
              'theme_location' => 'primary',
              'container'      => false,
              'menu_class'     => 'hamburger-menu',
              'fallback_cb'    => false,
            ]);
            ?>

            <?php get_template_part('template-parts/mobile-drawer-links'); ?>
          </nav>
          </div>

          <nav class="mobile-bottom-nav">
            <?php
            wp_nav_menu([
              'theme_location' => 'mobile-bottom',
              'container'      => false,
              'menu_class'     => '',
              'items_wrap'     => '<ul>%3$s</ul>',
              'fallback_cb'    => false,
            ]);
            ?>
          </nav>

          <nav class="global-nav">
            <?php
            wp_nav_menu([
              'theme_location' => 'primary',
              'container'      => false,
              'menu_class'     => 'main-menu'
            ]);
            ?>
          </nav>

      </div>
  </header>

  <!-- モバイル下部ナビ -->
  <nav class="mobile-bottom-nav">
    <?php
    wp_nav_menu([
      'theme_location' => 'mobile-bottom',
      'container'      => false,
      'menu_class'     => 'mobile-bottom-menu',
      'fallback_cb'    => false,
    ]);
    ?>
  </nav>
  <?php if (! is_front_page()) : ?>
    <?php get_template_part('template-parts/hero-global'); ?>
  <?php endif; ?>

  <?php
// ここで <main> や他のテンプレートパーツが始まり
// get_header() を書いたテンプレートファイル(front-page.php など)の先頭に
