<?php
/**
 * Template Part: 補足カード
 *
 * 呼び出し例:
 * get_template_part('template-parts/knowledge/supplement-card', null, [
 *     'heading' => '無利息期間について',
 *     'body'    => '初回借入から30日間は利息が0円になるサービスです。...',
 *     'url'     => 'https://example.com/no-interest/',
 *     'label'   => '詳しく見る',
 * ]);
 *
 * @param string $args['heading']  カード見出し
 * @param string $args['body']     カード本文（2〜4行程度）
 * @param string $args['url']      リンクURL（省略可）
 * @param string $args['label']    リンク文言（省略時「詳しく見る」）
 */

$heading = $args['heading'] ?? '';
$body    = $args['body']    ?? '';
$url     = $args['url']     ?? '';
$label   = $args['label']   ?? '詳しく見る';

if (!$heading && !$body) return;
?>
<div class="kn-supplement-card">
    <?php if ($heading): ?>
    <p class="kn-supplement-card__heading"><?php echo esc_html($heading); ?></p>
    <?php endif; ?>
    <?php if ($body): ?>
    <p class="kn-supplement-card__body"><?php echo nl2br(esc_html($body)); ?></p>
    <?php endif; ?>
    <?php if ($url): ?>
    <p class="kn-supplement-card__cta">
        <a href="<?php echo esc_url($url); ?>" class="kn-supplement-card__link"><?php echo esc_html($label); ?></a>
    </p>
    <?php endif; ?>
</div>
