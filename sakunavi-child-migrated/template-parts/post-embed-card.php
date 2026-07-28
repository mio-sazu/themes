<?php
/**
 * Template Part: 記事内埋め込みカード
 *
 * 呼び出し例（shortcode経由で自動呼び出し）:
 * get_template_part('template-parts/post-embed-card', null, [
 *     'post'  => $post_object,
 *     'label' => 'あわせて読みたい',  // 空文字でラベル非表示
 * ]);
 */

$post  = $args['post']  ?? null;
$label = $args['label'] ?? 'あわせて読みたい';

if (!$post) return;

$title    = get_the_title($post);
$url      = get_permalink($post);
$thumb_id = get_post_thumbnail_id($post->ID);
$excerpt  = $post->post_excerpt
    ?: wp_trim_words(strip_shortcodes(strip_tags($post->post_content)), 40, '…');

// 投稿タイプに応じたカテゴリータクソノミーを判定
$category_taxonomy = $post->post_type === 'ranking' ? 'ranking_category' : 'column_category';
$terms = get_the_terms($post->ID, $category_taxonomy);
$category_name = (!empty($terms) && !is_wp_error($terms)) ? $terms[0]->name : '';

$updated_date = get_the_modified_date('Y.m.d', $post);
?>
<div class="post-embed-card">
    <?php if ($label !== ''): ?>
    <p class="post-embed-card__label"><?php echo esc_html($label); ?></p>
    <?php endif; ?>
    <a href="<?php echo esc_url($url); ?>" class="post-embed-card__link">
        <?php if ($thumb_id): ?>
        <figure class="post-embed-card__thumb">
            <?php echo wp_get_attachment_image($thumb_id, 'medium', false, ['loading' => 'lazy', 'alt' => esc_attr($title)]); ?>
        </figure>
        <?php endif; ?>
        <div class="post-embed-card__body">
            <?php if ($category_name || $updated_date): ?>
            <div class="post-embed-card__meta">
                <?php if ($category_name): ?>
                <span class="post-embed-card__cat"><?php echo esc_html($category_name); ?></span>
                <?php endif; ?>
                <?php if ($updated_date): ?>
                <time class="post-embed-card__date" datetime="<?php echo esc_attr(get_the_modified_date('c', $post)); ?>"><?php echo esc_html($updated_date); ?></time>
                <?php endif; ?>
            </div>
            <?php endif; ?>
            <span class="post-embed-card__title"><?php echo esc_html($title); ?></span>
            <?php if ($excerpt): ?>
            <span class="post-embed-card__excerpt"><?php echo esc_html($excerpt); ?></span>
            <?php endif; ?>
        </div>
    </a>
</div>
