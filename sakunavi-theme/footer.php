<footer>
  <div class="footer-note">
    <div class="wrapper">
      <?php if ($notice = get_theme_mod('footer_notice_text')) : ?>
        <div class="footer-notice">
          <?php echo apply_filters('the_content', $notice); ?>
        </div>
      <?php endif; ?>
      <!-- 固定ページ（「注意事項」）を編集画面で作成しておき、ここで呼び出す方法もあります -->
    </div>
  </div>

  <div class="footer-inner wrapper">
    <div class="footer-container">
      <div class="footer-brand">
        <img src="<?php echo get_template_directory_uri(); ?>/assets/images/index_logo.png" alt="<?php bloginfo('name'); ?>ロゴ">
      </div>
      <div class="footer-about">
        <p><?php bloginfo('description'); ?></p>
      </div>
    </div>
    <div class="footer-nav">
      <?php wp_nav_menu([
        'theme_location' => 'footer',
        'container' => false,
        'items_wrap' => '<ul>%3$s</ul>'
      ]); ?>
    </div>
  </div>
  <p class="copyright">
    <?php echo esc_html(date_i18n('Y')); ?> <?php bloginfo('name'); ?> © All Rights Reserved.
  </p>
</footer>
<?php wp_footer(); ?>
</body>

</html>