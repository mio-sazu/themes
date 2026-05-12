<?php
/**
 * Template Part: 関連FAQリンク（コンパクトなリンク一覧）
 *
 * 呼び出し例:
 * get_template_part('template-parts/knowledge/related-links', null, [
 *     'knowledge_ids' => [123, 456],
 *     'title'         => '関連FAQ',
 * ]);
 *
 * @param array  $args['knowledge_ids']  knowledge投稿IDの配列
 * @param string $args['title']          ラベル見出し（省略可）
 */

$knowledge_ids = $args['knowledge_ids'] ?? [];
$title         = $args['title']         ?? '関連FAQ';

if (empty($knowledge_ids)) return;

$items = [];
foreach ($knowledge_ids as $kid) {
    $kp = get_post($kid);
    if (!$kp || $kp->post_status !== 'publish') continue;
    $items[] = [
        'title' => $kp->post_title,
        'url'   => get_permalink($kid),
    ];
}

if (empty($items)) return;
?>
<div class="kn-related-links">
    <?php if ($title): ?>
    <p class="kn-related-links__label"><?php echo esc_html($title); ?></p>
    <?php endif; ?>
    <ul class="kn-related-links__list">
        <?php foreach ($items as $item): ?>
        <li class="kn-related-links__item">
            <a href="<?php echo esc_url($item['url']); ?>" class="kn-related-links__link">
                <?php echo esc_html($item['title']); ?>
            </a>
        </li>
        <?php endforeach; ?>
    </ul>
</div>
