<?php
if (!defined('ABSPATH')) exit;

add_action('init', function () {

    // knowledge CPT
    register_post_type('knowledge', [
        'labels' => [
            'name'               => 'ナレッジ（FAQ・用語）',
            'singular_name'      => 'ナレッジ',
            'menu_name'          => 'ナレッジ',
            'add_new'            => '新規追加',
            'add_new_item'       => 'ナレッジを追加',
            'edit_item'          => 'ナレッジを編集',
            'all_items'          => '一覧',
            'search_items'       => 'ナレッジを検索',
            'not_found'          => '見つかりませんでした',
            'not_found_in_trash' => 'ゴミ箱に見つかりませんでした',
        ],
        'public'             => true,
        'publicly_queryable' => true,
        'has_archive'        => 'knowledge',
        'rewrite'            => ['slug' => 'knowledge', 'with_front' => false],
        'show_in_rest'       => true,
        'supports'           => ['title', 'editor', 'thumbnail', 'excerpt', 'revisions', 'custom-fields'],
        'menu_icon'          => 'dashicons-lightbulb',
        'menu_position'      => 6,
    ]);

    // カテゴリ（階層あり）
    register_taxonomy('knowledge_category', ['knowledge'], [
        'label'        => 'ナレッジカテゴリ',
        'public'       => true,
        'hierarchical' => true,
        'show_in_rest' => true,
        'rewrite'      => ['slug' => 'knowledge-category', 'with_front' => false],
    ]);

    // 種別（FAQ・用語解説・注意喚起・手続きガイド）
    register_taxonomy('knowledge_type', ['knowledge'], [
        'label'        => '種別',
        'public'       => true,
        'hierarchical' => false,
        'show_in_rest' => true,
        'rewrite'      => ['slug' => 'knowledge-type', 'with_front' => false],
    ]);

    // 検索意図（取引開始・比較検討・問題解決・学習）
    register_taxonomy('knowledge_intent', ['knowledge'], [
        'label'        => '検索意図',
        'public'       => true,
        'hierarchical' => false,
        'show_in_rest' => true,
        'rewrite'      => ['slug' => 'knowledge-intent', 'with_front' => false],
    ]);

    // 難易度（初心者向け・中級者向け）
    register_taxonomy('knowledge_level', ['knowledge'], [
        'label'        => '難易度',
        'public'       => true,
        'hierarchical' => false,
        'show_in_rest' => true,
        'rewrite'      => ['slug' => 'knowledge-level', 'with_front' => false],
    ]);
});

// 管理画面：knowledge一覧にカテゴリ・種別列を追加
add_filter('manage_knowledge_posts_columns', function ($columns) {
    $new = [];
    foreach ($columns as $k => $v) {
        $new[$k] = $v;
        if ($k === 'title') {
            $new['kn_type']     = '種別';
            $new['kn_category'] = 'カテゴリ';
        }
    }
    return $new;
});

add_action('manage_knowledge_posts_custom_column', function ($col, $post_id) {
    if ($col === 'kn_type') {
        $terms = get_the_terms($post_id, 'knowledge_type');
        echo (!$terms || is_wp_error($terms)) ? '—' : esc_html($terms[0]->name);
    }
    if ($col === 'kn_category') {
        $terms = get_the_terms($post_id, 'knowledge_category');
        echo (!$terms || is_wp_error($terms)) ? '—' : esc_html(implode(', ', wp_list_pluck($terms, 'name')));
    }
}, 10, 2);

// 管理画面：種別で絞り込み
add_action('restrict_manage_posts', function () {
    global $typenow;
    if ($typenow !== 'knowledge') return;

    $selected = $_GET['knowledge_type'] ?? '';
    wp_dropdown_categories([
        'show_option_all' => 'すべての種別',
        'taxonomy'        => 'knowledge_type',
        'name'            => 'knowledge_type',
        'selected'        => $selected,
        'show_count'      => true,
        'hide_empty'      => false,
        'value_field'     => 'slug',
    ]);
});
