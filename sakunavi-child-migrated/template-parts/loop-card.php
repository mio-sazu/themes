<?php
/**
 * Card component for card_company
 */
$post_id = get_the_ID();
$title   = get_the_title();
$perma   = get_permalink();

$logo            = function_exists('get_field') ? get_field('logo', $post_id) : null;
$rate_min        = function_exists('get_field') ? get_field('rate_min', $post_id) : '';
$rate_max        = function_exists('get_field') ? get_field('rate_max', $post_id) : '';
$limit_amount    = function_exists('get_field') ? get_field('limit_amount', $post_id) : '';
$exam_fast       = function_exists('get_field') ? get_field('exam_fast', $post_id) : '';
$no_interest     = function_exists('get_field') ? get_field('no_interest_days', $post_id) : '';
$web_only        = function_exists('get_field') ? get_field('web_only', $post_id) : 0;
$cta_label       = function_exists('get_field') ? get_field('cta_label', $post_id) : '';
$cta_url         = function_exists('get_field') ? get_field('cta_url', $post_id) : '';

$logo_url = '';
if (is_array($logo)) {
  $logo_url = isset($logo['sizes']['medium']) ? $logo['sizes']['medium'] : (isset($logo['url']) ? $logo['url'] : '');
} elseif (is_numeric($logo)) {
  $src = wp_get_attachment_image_src($logo, 'medium');
  $logo_url = $src ? $src[0] : '';
}

$rate_txt = '';
if ($rate_min !== '' || $rate_max !== '') {
  if ($rate_min !== '' && $rate_max !== '') {
    $rate_txt = esc_html($rate_min) . '% – ' . esc_html($rate_max) . '%';
  } else {
    $rate_txt = esc_html($rate_min ?: $rate_max) . '%';
  }
}
$limit_txt = $limit_amount !== '' ? esc_html(number_format_i18n((float)$limit_amount)) . '万円' : '';
?>
<article class="loan-card" data-id="<?php echo esc_attr($post_id); ?>">
  <a class="loan-card__body" href="<?php echo esc_url($perma); ?>" aria-label="<?php echo esc_attr($title); ?>">
    <div class="loan-card__media">
      <?php if ($logo_url): ?>
        <img src="<?php echo esc_url($logo_url); ?>" alt="<?php echo esc_attr($title); ?>" loading="lazy" />
      <?php else: ?>
        <div class="loan-card__placeholder" aria-hidden="true"><?php echo esc_html(mb_substr($title, 0, 2)); ?></div>
      <?php endif; ?>
    </div>
    <div class="loan-card__content">
      <h3 class="loan-card__title"><?php echo esc_html($title); ?></h3>
      <ul class="loan-card__spec">
        <?php if ($rate_txt): ?><li><span>金利</span><strong><?php echo $rate_txt; ?></strong></li><?php endif; ?>
        <?php if ($limit_txt): ?><li><span>限度額</span><strong><?php echo $limit_txt; ?></strong></li><?php endif; ?>
        <?php if ($exam_fast): ?><li><span>審査</span><strong><?php echo esc_html($exam_fast); ?></strong></li><?php endif; ?>
        <?php if ($no_interest !== ''): ?><li><span>無利息</span><strong><?php echo esc_html($no_interest); ?>日</strong></li><?php endif; ?>
      </ul>
      <div class="loan-card__badges">
        <?php if ($web_only): ?><span class="badge">Web完結</span><?php endif; ?>
        <?php $terms = get_the_terms($post_id, 'loan_genre'); if ($terms && !is_wp_error($terms)): foreach ($terms as $t): ?>
          <span class="badge badge--soft"><?php echo esc_html($t->name); ?></span>
        <?php endforeach; endif; ?>
      </div>
    </div>
  </a>
  <div class="loan-card__cta">
    <?php if ($cta_url): ?>
      <a class="btn btn--primary" target="_blank" rel="nofollow noopener" href="<?php echo esc_url($cta_url); ?>">
        <?php echo esc_html($cta_label ?: '公式サイトへ'); ?>
      </a>
    <?php else: ?>
      <a class="btn" href="<?php echo esc_url($perma); ?>">詳細を見る</a>
    <?php endif; ?>
  </div>
</article>
