<?php
/**
 * Template Part: FAQ Accordion（ナレッジ投稿から生成）
 *
 * 呼び出し例:
 * get_template_part('template-parts/knowledge/accordion', null, [
 *     'knowledge_ids' => [123, 456],
 *     'title'         => 'よくある質問',
 * ]);
 *
 * @param array  $args['knowledge_ids']  knowledge投稿IDの配列
 * @param string $args['title']          セクション見出し（省略可）
 */

$knowledge_ids = $args['knowledge_ids'] ?? [];
$title         = $args['title']         ?? 'よくある質問';

if (empty($knowledge_ids) || !function_exists('get_field')) return;

$items = [];
foreach ($knowledge_ids as $kid) {
    $kp = get_post($kid);
    if (!$kp || $kp->post_status !== 'publish') continue;

    $answer = get_field('one_liner', $kid) ?: get_field('short_answer', $kid);
    $items[] = [
        'question' => $kp->post_title,
        'answer'   => $answer,
        'url'      => get_permalink($kid),
        'label'    => get_field('kn_cta_label', $kid) ?: '詳しく見る',
    ];
}

if (empty($items)) return;
?>
<section class="kn-accordion">
    <?php if ($title): ?>
    <h2 class="kn-section-title"><?php echo esc_html($title); ?></h2>
    <?php endif; ?>

    <div class="kn-faq-box">
        <?php foreach ($items as $item): ?>
        <div class="kn-faq-item">
            <button class="kn-faq-question faq-question" type="button" aria-expanded="false">
                <span class="kn-faq-question__text"><?php echo esc_html($item['question']); ?></span>
                <span class="faq-icon" aria-hidden="true">+</span>
            </button>
            <div class="kn-faq-answer faq-answer" hidden>
                <?php if ($item['answer']): ?>
                <p><?php echo esc_html($item['answer']); ?></p>
                <?php endif; ?>
                <p class="kn-faq-more">
                    <a href="<?php echo esc_url($item['url']); ?>"><?php echo esc_html($item['label']); ?></a>
                </p>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</section>
