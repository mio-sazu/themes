<?php
/**
 * カードローン比較早見表 CPT & ACF
 * 管理画面: 投稿タイトル = 社名 / ACF で URL・限度額・審査時間を管理
 * 表示順:   編集画面「順序」欄（menu_order）を昇順で読む
 */
if ( ! defined( 'ABSPATH' ) ) exit;

// ============================
// CPT 登録
// ============================
add_action( 'init', function () {
    register_post_type( 'loan_comparison', [
        'label'         => 'カードローン比較',
        'labels'        => [
            'name'               => 'カードローン比較',
            'singular_name'      => 'カードローン比較',
            'menu_name'          => 'カードローン比較',
            'add_new'            => '新規追加',
            'add_new_item'       => '比較項目を追加',
            'edit_item'          => '比較項目を編集',
            'all_items'          => '一覧',
            'not_found'          => '見つかりませんでした',
            'not_found_in_trash' => 'ゴミ箱に見つかりません',
        ],
        'public'             => true,
        'publicly_queryable' => false, // フロントエンドのアーカイブ・パーマリンクは無効
        'show_ui'            => true,
        'show_in_menu'       => true,
        'show_in_rest'       => true,
        'show_in_nav_menus'  => false,
        'has_archive'        => false,
        'menu_icon'          => 'dashicons-list-view',
        'menu_position'      => 26,
        'supports'           => [ 'title', 'page-attributes' ], // page-attributes = 順序（menu_order）
    ] );
} );

// ============================
// 管理画面: 一覧に「順序」列を追加
// ============================
add_filter( 'manage_loan_comparison_posts_columns', function ( $columns ) {
    $new = [];
    foreach ( $columns as $k => $v ) {
        if ( $k === 'title' ) {
            $new['lc_order'] = '順序';
        }
        $new[$k] = $v;
    }
    return $new;
} );

add_action( 'manage_loan_comparison_posts_custom_column', function ( $column, $post_id ) {
    if ( $column === 'lc_order' ) {
        echo (int) get_post_field( 'menu_order', $post_id );
    }
}, 10, 2 );

add_filter( 'manage_edit-loan_comparison_sortable_columns', function ( $columns ) {
    $columns['lc_order'] = 'menu_order';
    return $columns;
} );

// ============================
// ACF フィールドグループ
// ============================
add_action( 'acf/init', function () {
    if ( ! function_exists( 'acf_add_local_field_group' ) ) return;

    acf_add_local_field_group( [
        'key'             => 'group_loan_comparison',
        'title'           => 'カードローン比較情報',
        'position'        => 'acf_after_title',
        'label_placement' => 'top',
        'location'        => [ [ [ 'param' => 'post_type', 'operator' => '==', 'value' => 'loan_comparison' ] ] ],
        'fields'          => [
            [
                'key'           => 'field_lc_thumbnail',
                'label'         => 'サムネイル（ロゴ画像）',
                'name'          => 'lc_thumbnail',
                'type'          => 'image',
                'return_format' => 'array',
                'preview_size'  => 'thumbnail',
                'library'       => 'all',
                'instructions'  => '会社ロゴや商品画像を設定してください。',
            ],
            [
                'key'         => 'field_lc_url',
                'label'       => '申込リンクURL',
                'name'        => 'lc_url',
                'type'        => 'url',
                'instructions' => '社名リンク・詳細ボタン共通で使用します。',
                'placeholder' => 'https://example.com/apply/',
            ],
            [
                'key'         => 'field_lc_limit',
                'label'       => '限度額',
                'name'        => 'lc_limit',
                'type'        => 'text',
                'instructions' => '注釈番号も含めて入力してください。例: 最大800万円まで※1',
                'placeholder' => '最大800万円まで※1',
            ],
            [
                'key'          => 'field_lc_review_time',
                'label'        => '最短審査',
                'name'         => 'lc_review_time',
                'type'         => 'textarea',
                'rows'         => 2,
                'instructions' => '改行すると表でも改行されます。例: 審査は最短15分\n10秒で簡易審査※3',
                'placeholder'  => '審査は最短15分',
            ],
            [
                'key'           => 'field_lc_btn_label',
                'label'         => 'ボタン文言',
                'name'          => 'lc_btn_label',
                'type'          => 'text',
                'default_value' => '今すぐ詳細を見る',
            ],
            [
                'key'          => 'field_lc_footnote',
                'label'        => '注釈',
                'name'         => 'lc_footnote',
                'type'         => 'textarea',
                'rows'         => 3,
                'instructions' => '※番号と注釈文をセットで入力。複数ある場合は改行して1行ずつ入力してください。同じ内容は自動でまとめられます。',
                'placeholder'  => "※1借入限度額は審査によって決定致します。\n※3申込の曜日、時間帯によっては翌日以降の取扱となる場合があります。",
            ],
        ],
    ] );
} );
