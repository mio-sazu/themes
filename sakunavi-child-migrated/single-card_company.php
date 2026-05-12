<?php

/**
 * Template: Single - card_company
 * Location: wp-content/themes/sakunavi-child-migrated/single-card_company.php
 */
get_header();

// --- Fields ---
$hero_id   = get_post_thumbnail_id();
$hero_url  = $hero_id ? wp_get_attachment_image_url($hero_id, 'full') : get_theme_file_uri('assets/img/company/hero-placeholder.jpg');

$rate_min  = get_field('rate_min');
$rate_max  = get_field('rate_max');
$limit_amt = get_field('limit_amount');
$exam_fast = get_field('exam_fast');
$no_interest_days = get_field('no_interest_days');
$web_only  = get_field('web_only');

$cta_label = get_field('cta_label') ?: '申し込む';
$cta_url   = get_field('cta_url') ?: '#';

$rank_score = floatval(get_field('rank_score'));

// helper
function sakunavi_star($score)
{
  $score = max(0, min(5, floatval($score)));
  $full = floor($score);
  $half = ($score - $full) >= 0.5 ? 1 : 0;
  $empty = 5 - $full - $half;
  return str_repeat('★', $full) . ($half ? '☆' : '') . str_repeat('☆', $empty);
}
?>
<div class="company-hero-banner" style="background-image:url('<?php echo esc_url($hero_url); ?>');">
  <div class="company-hero-overlay"></div>
  <div class="wrapper company-hero-inner">
    <h1 class="loan-title"><?php the_title(); ?></h1>
  </div>
</div>

<div class="wrapper company-layout">
  <?php get_sidebar(); ?>
  <main class="company-content">
    <figure class="loan-image">
      <?php echo $hero_id ? wp_get_attachment_image($hero_id, 'large') : ''; ?>
    </figure>

    <section class="basic-info">
      <h2 class="section-title">基本情報</h2>
      <table class="info-table">
        <tbody>
          <?php if ($exam_fast): ?><tr>
              <th>審査時間</th>
              <td><?php echo esc_html($exam_fast); ?></td>
            </tr><?php endif; ?>
          <?php if ($rate_min || $rate_max): ?>
            <tr>
              <th>金利</th>
              <td><?php
                  $min = $rate_min !== '' ? floatval($rate_min) : null;
                  $max = $rate_max !== '' ? floatval($rate_max) : null;
                  if (!is_null($min) && !is_null($max))      echo esc_html($min) . '% ～ ' . esc_html($max) . '%';
                  elseif (!is_null($min) && is_null($max))   echo esc_html($min) . '%';
                  elseif (is_null($min) && !is_null($max))   echo '～ ' . esc_html($max) . '%';
                  ?></td>
            </tr>
          <?php endif; ?>
          <?php if ($limit_amt !== ''): ?><tr>
              <th>融資限度額</th>
              <td><?php echo esc_html(number_format($limit_amt)); ?>万円</td>
            </tr><?php endif; ?>
          <?php if ($no_interest_days !== ''): ?><tr>
              <th>無利息期間</th>
              <td><?php echo esc_html($no_interest_days); ?>日</td>
            </tr><?php endif; ?>
          <tr>
            <th>Web完結</th>
            <td><?php echo $web_only ? '対応' : '―'; ?></td>
          </tr>
        </tbody>
      </table>
    </section>

    <section class="rating-and-cta">
      <div class="rating-box">
        <h3>おすすめ度</h3>
        <div class="rating-stars" aria-label="おすすめ度"><?php echo sakunavi_star($rank_score); ?></div>
        <?php if ($rank_score): ?><div class="rating-note"><?php echo esc_html($rank_score); ?> / 5</div><?php endif; ?>
      </div>
      <div class="apply-box">
        <a class="btn btn--primary apply-btn" href="<?php echo esc_url($cta_url); ?>"><?php echo esc_html($cta_label); ?></a>
      </div>
    </section>

    <section class="company-points">
      <h2 class="section-title">ここがポイント</h2>
      <div class="entry-content">
        <?php the_content(); ?>
      </div>
    </section>
  </main>
</div>

<?php get_footer(); ?>