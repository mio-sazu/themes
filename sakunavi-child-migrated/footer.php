<footer>
  <div class="footer-note">
    <div class="wrapper">
      <?php
      // 優先: 固定ページ「注意事項」（slug: notices or タイトル: 注意事項）
      $page = get_page_by_path('notices');
      if (!$page) {
        $page = get_page_by_path('注意事項');
      }
      if ($page) : ?>
        <div class="footer-notice footer-notice--page">
          <?php echo apply_filters('the_content', $page->post_content); ?>
        </div>
        <?php else :
        // フォールバック: カスタマイザーの自由入力欄
        if ($notice = get_theme_mod('footer_notice_text')) : ?>
          <div class="footer-notice footer-notice--customizer">
            <?php echo apply_filters('the_content', $notice); ?>
          </div>
      <?php endif;
      endif; ?>
      <!-- 固定ページ（「注意事項」）を編集画面で作成しておくと自動で表示されます / カスタマイザー入力がある場合はフォールバックとして使用 -->
    </div>
  </div>

  <div class="footer-inner wrapper">
    <!-- ==== Footer widgets (3 columns inside .footer-inner.wrapper) ==== -->
    <div
      v class="footer-widgets">
      <div class="footer-columns">
        <?php if (is_active_sidebar('footer-1')) : ?>
          <?php dynamic_sidebar('footer-1'); ?>
        <?php else : ?>
          <div class="footer-col footer-col--brand">
            <div class="footer-brand">
              <?php if (function_exists('the_custom_logo') && has_custom_logo()) : ?>
                <?php the_custom_logo(); ?>
              <?php else : ?>
                <strong class="site-name"><?php bloginfo('name'); ?></strong>
              <?php endif; ?>
              <?php if (get_bloginfo('description')) : ?>
                <p class="site-tagline"><?php bloginfo('description'); ?></p>
              <?php endif; ?>
            </div>
          </div>
        <?php endif; ?>

        <?php if (is_active_sidebar('footer-2')) : ?>
          <?php dynamic_sidebar('footer-2'); ?>
        <?php else : ?>
          <div class="footer-col footer-col--menu">
            <nav class="footer-nav" aria-label="<?php esc_attr_e('Footer menu', 'sakunavi-child'); ?>">
              <?php
              wp_nav_menu([
                'theme_location' => 'footer',
                'container'      => false,
                'fallback_cb'    => false,
                'items_wrap'     => '<ul class="footer-menu">%3$s</ul>',
              ]);
              ?>
            </nav>
          </div>
        <?php endif; ?>

        <?php if (is_active_sidebar('footer-3')) : ?>
          <?php dynamic_sidebar('footer-3'); ?>
        <?php else : ?>
          <div class="footer-col footer-col--pages">
            <h2 class="widget-title">サイト情報</h2>

            <?php if (has_nav_menu('footer-legal')) : ?>
              <?php
              wp_nav_menu([
                'theme_location' => 'footer-legal',
                'container'      => false,
                'fallback_cb'    => false,
                'items_wrap'     => '<ul class="footer-pages">%3$s</ul>',
              ]);
              ?>
              <?php else :
              // よく使う固定ページのスラッグを自動検出
              $slugs = ['company', '会社情報', 'about', '会社概要', 'privacy-policy', 'プライバシーポリシー', 'terms', '利用規約', '特定商取引法', 'sitemap', 'サイトマップ'];
              $pages = [];
              foreach ($slugs as $slug) {
                $p = get_page_by_path($slug);
                if ($p) $pages[$p->ID] = get_permalink($p);
              }
              if (!empty($pages)) : ?>
                <ul class="footer-pages">
                  <?php foreach ($pages as $pid => $url) : ?>
                    <li><a href="<?php echo esc_url($url); ?>"><?php echo esc_html(get_the_title($pid)); ?></a></li>
                  <?php endforeach; ?>
                </ul>
              <?php else : ?>
                <p class="footer-pages__hint">「外観 → ウィジェット → Footer 3」で固定ページ一覧ブロックを追加、<br>または「外観 → メニュー → メニューの位置（footer-legal）」を設定してください。</p>
              <?php endif; ?>
            <?php endif; ?>
          </div>
        <?php endif; ?>
      </div>
    </div>

    <!--<div class="footer-container">
      <div class="footer-brand">
        <img src="<?php echo get_template_directory_uri(); ?>/images/index/index_logo.png" alt="<?php bloginfo('name'); ?>ロゴ">
      </div>
      <div class="footer-about">
        <p><?php bloginfo('description'); ?></p>
      </div>
    </div>-->
    <!--<div class="footer-nav">
      <?php wp_nav_menu([
        'theme_location' => 'footer',
        'container' => false,
        'items_wrap' => '<ul>%3$s</ul>'
      ]); ?>
    </div>-->
  </div>
  <p class="copyright">
    <?php echo esc_html(date_i18n('Y')); ?> <?php bloginfo('name'); ?> © All Rights Reserved.
  </p>
</footer>
<?php wp_footer(); ?>
</body>
<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-PVPSZSD5"
    height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->

</html>