<?php
// single-card_loan.php
get_header();
if (have_posts()) : while (have_posts()) : the_post();
?>

    <?php
    // ―― ヒーロービュー ――
    // アイキャッチまたは ACF フィールド 'hero_image' を背景に
    if (has_post_thumbnail()) {
      $hero_url = get_the_post_thumbnail_url(get_the_ID(), 'full');
    } elseif ($hero = get_field('hero_image')) {
      $hero_url = wp_get_attachment_image_url($hero, 'full');
    } else {
      $hero_url = get_template_directory_uri() . '/assets/images/default-hero.jpg';
    }
    ?>
    <section class="page-hero" style="background-image:url('<?php echo esc_url($hero_url); ?>');">
      <div class="page-hero-overlay">
        <h1 class="page-hero-title"><?php the_title(); ?></h1>
      </div>
    </section>

    <div class="wrapper layout">

      <!-- サイドバー -->
      <?php get_sidebar(); ?>

      <!-- メイン -->
      <main class="content">
        <!-- パンくず -->
        <ul class="breadcrumb">
          <li><a href="<?php echo esc_url(home_url('/')); ?>">HOME</a></li>
          <li><a href="<?php echo esc_url(get_post_type_archive_link('card_loan')); ?>">カードローン会社一覧</a></li>
          <li><?php the_title(); ?></li>
        </ul>

        <article class="column-main">
          <!-- 基本情報 -->
          <section class="basic-info">
            <h3><?php the_title(); ?> 基本情報</h3>
            <table class="info-table">
              <tr>
                <th>審査時間</th>
                <td><?php echo esc_html(get_field('loan_time')); ?></td>
              </tr>
              <tr>
                <th>金利</th>
                <td><?php echo esc_html(get_field('loan_rate')); ?>％</td>
              </tr>
              <tr>
                <th>融資金額</th>
                <td><?php echo esc_html(get_field('limit_amount')); ?></td>
              </tr>
              <tr>
                <th>在籍確認</th>
                <td><?php echo get_field('verify_required') ? 'あり' : 'なし'; ?></td>
              </tr>
            </table>
          </section>

          <!-- おすすめ度 & ボタン -->
          <div class="rating-and-btn">
            <h4>おすすめ度</h4>
            <div class="rating">
              <?php
              $score = floatval(get_field('evaluation_score'));
              echo str_repeat('★', floor($score))
                . str_repeat('☆', 5 - floor($score));
              ?>
            </div>
          </div>
          <div class="btn">
            <?php if ($link = get_field('affiliate_link')): ?>
              <a href="<?php echo esc_url($link); ?>" class="apply-btn" target="_blank" rel="noopener">
                <?php the_title(); ?>に申し込む
              </a>
            <?php endif; ?>
          </div>

          <!-- ポイント紹介（リピーターフィールド） -->
          <?php if (have_rows('points')): ?>
            <section>
              <h2><?php the_title(); ?>のポイント</h2>
              <div class="point">
                <?php $i = 1;
                while (have_rows('points')) : the_row(); ?>
                  <div class="radius"><span><?php echo $i++; ?></span></div>
                  <ul>
                    <li>
                      <?php if ($icon = get_sub_field('icon')): ?>
                        <figure>
                          <?php echo wp_get_attachment_image($icon, 'thumbnail'); ?>
                          <figcaption>
                            <p><?php echo esc_html(get_sub_field('label_top')); ?></p>
                            <p><?php echo esc_html(get_sub_field('label_bottom')); ?></p>
                          </figcaption>
                        </figure>
                      <?php endif; ?>
                    </li>
                  </ul>
                <?php endwhile; ?>
              </div>
            </section>
          <?php endif; ?>

          <!-- 以下、必要に応じて本文(エディタ)を出力 -->
          <section class="company-content">
            <?php the_content(); ?>
          </section>

        </article>
      </main>
    </div>

<?php
  endwhile;
endif;
get_footer();
