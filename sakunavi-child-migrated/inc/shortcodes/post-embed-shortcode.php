<?php
if (!defined('ABSPATH')) exit;

/**
 * [post_embed id="123"]
 * [post_embed id="123" label="あわせて読みたい"]
 * [post_embed id="123" label=""]   ← ラベル非表示
 *
 * column / ranking 投稿をインラインのリンクカードとして差し込む。
 */
add_shortcode('post_embed', function ($atts) {
    $atts = shortcode_atts([
        'id'    => 0,
        'label' => 'あわせて読みたい',
    ], $atts, 'post_embed');

    $post_id = (int) $atts['id'];
    if (!$post_id) return '';

    $post = get_post($post_id);
    if (!$post || $post->post_status !== 'publish') return '';

    $allowed = ['column', 'ranking'];
    if (!in_array($post->post_type, $allowed, true)) return '';

    ob_start();
    get_template_part('template-parts/post-embed-card', null, [
        'post'  => $post,
        'label' => (string) $atts['label'],
    ]);
    return ob_get_clean();
});
