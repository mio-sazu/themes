<?php
if (!defined('ABSPATH')) exit;

add_action('acf/include_fields', function () {
    if (!function_exists('acf_add_local_field_group')) return;

    acf_add_local_field_group([
        'key'      => 'group_kn_fields',
        'title'    => 'ナレッジ情報',
        'position' => 'acf_after_title',
        'style'    => 'default',
        'location' => [[['param' => 'post_type', 'operator' => '==', 'value' => 'knowledge']]],
        'fields'   => [

            // ──── 回答 ────
            [
                'key'          => 'field_kn_one_liner',
                'label'        => 'ひとこと回答',
                'name'         => 'one_liner',
                'type'         => 'text',
                'instructions' => '1文で結論を書きます。アコーディオンの回答文・一覧でも意味が通るようにしてください。',
                'required'     => 1,
                'placeholder'  => '例：在籍確認とは、申込者が申告した職場に勤務しているかを確認する手続きです。',
            ],
            [
                'key'          => 'field_kn_short_answer',
                'label'        => '短文回答（2〜4文）',
                'name'         => 'short_answer',
                'type'         => 'textarea',
                'rows'         => 4,
                'instructions' => 'ひとこと回答を少し掘り下げた補足です。アコーディオン本文に使います。',
            ],
            [
                'key'          => 'field_kn_detail',
                'label'        => '詳細解説',
                'name'         => 'detail',
                'type'         => 'wysiwyg',
                'toolbar'      => 'basic',
                'media_upload' => 0,
                'instructions' => '300〜800字目安。結論 → 補足 → 注意点 の順で書きます。法令・審査・条件は断定しすぎないように。',
            ],
            [
                'key'          => 'field_kn_example',
                'label'        => '実例・補足',
                'name'         => 'kn_example',
                'type'         => 'textarea',
                'rows'         => 3,
                'instructions' => '具体的な例や補足情報があれば入力してください。',
            ],
            [
                'key'          => 'field_kn_caution',
                'label'        => '注意書き',
                'name'         => 'kn_caution',
                'type'         => 'textarea',
                'rows'         => 3,
                'instructions' => '読者が誤解しやすい点や注意が必要なことを入力してください。',
            ],
            [
                'key'          => 'field_kn_misconception',
                'label'        => 'よくある誤解',
                'name'         => 'kn_misconception',
                'type'         => 'textarea',
                'rows'         => 3,
                'instructions' => '「〜と思いがちだが、実際は〜」という形で書くと効果的です。',
            ],

            // ──── 表示設定 ────
            [
                'key'     => 'field_kn_sep_display',
                'label'   => '━ 表示設定 ━',
                'name'    => '',
                'type'    => 'message',
                'message' => '一覧ページや記事内への表示を制御します。',
            ],
            [
                'key'           => 'field_kn_cta_label',
                'label'         => 'ボタン文言',
                'name'          => 'kn_cta_label',
                'type'          => 'text',
                'default_value' => '詳しく見る',
                'instructions'  => '関連FAQリンクのボタン文言。空欄なら「詳しく見る」。',
            ],
            [
                'key'          => 'field_kn_display_order',
                'label'        => '優先表示順',
                'name'         => 'kn_display_order',
                'type'         => 'number',
                'min'          => 0,
                'step'         => 1,
                'instructions' => '数値が小さいほど一覧で上に表示されます。未入力は最後尾。',
            ],
            [
                'key'           => 'field_kn_show_faq_list',
                'label'         => 'FAQ一覧ページに掲載する',
                'name'          => 'kn_show_faq_list',
                'type'          => 'true_false',
                'ui'            => 1,
                'default_value' => 1,
            ],
            [
                'key'           => 'field_kn_show_glossary',
                'label'         => '用語一覧ページに掲載する',
                'name'          => 'kn_show_glossary',
                'type'          => 'true_false',
                'ui'            => 1,
                'default_value' => 0,
            ],
            [
                'key'           => 'field_kn_show_inline',
                'label'         => '記事内の候補として使用可能にする',
                'name'          => 'kn_show_inline',
                'type'          => 'true_false',
                'ui'            => 1,
                'default_value' => 1,
                'instructions'  => 'ONにするとコラム記事側で関連FAQとして選択できます。',
            ],
            [
                'key'          => 'field_kn_noindex',
                'label'        => 'noindex候補（非公開SEOページ）',
                'name'         => 'kn_noindex',
                'type'         => 'true_false',
                'ui'           => 1,
                'default_value' => 0,
                'instructions'  => 'ONにするとこのページにnoindexが付きます。個別ページを検索インデックスに入れたくない場合のみONにしてください。',
            ],
            [
                'key'          => 'field_kn_link_type',
                'label'        => '推奨内部リンク先タイプ',
                'name'         => 'kn_link_type',
                'type'         => 'select',
                'choices'      => [
                    ''             => '指定なし',
                    'ranking'      => 'ランキング',
                    'comparison'   => '比較ページ',
                    'column'       => 'コラム',
                    'company'      => '会社詳細ページ',
                    'other'        => 'その他',
                ],
                'default_value' => '',
                'allow_null'    => 1,
                'instructions'  => 'この項目と一緒に読者に案内したいページの種類を選択してください。',
            ],

            // ──── 関連付け ────
            [
                'key'     => 'field_kn_sep_related',
                'label'   => '━ 関連コンテンツ ━',
                'name'    => '',
                'type'    => 'message',
                'message' => '関連するコンテンツを紐付けることで、個別ページに「あわせて見る」リンクが表示されます。',
            ],
            [
                'key'           => 'field_kn_related_faq',
                'label'         => '関連FAQ・用語',
                'name'          => 'kn_related_faq',
                'type'          => 'relationship',
                'post_type'     => ['knowledge'],
                'filters'       => ['search', 'taxonomy'],
                'taxonomy'      => [],
                'max'           => 5,
                'return_format' => 'id',
                'instructions'  => 'このFAQ・用語と関連するナレッジを選択してください。',
            ],
            [
                'key'           => 'field_kn_related_columns',
                'label'         => '関連コラム',
                'name'          => 'kn_related_columns',
                'type'          => 'relationship',
                'post_type'     => ['column'],
                'filters'       => ['search'],
                'max'           => 4,
                'return_format' => 'id',
                'instructions'  => '関連するお金コラムを選択してください。',
            ],
            [
                'key'           => 'field_kn_related_company',
                'label'         => '関連会社ページ',
                'name'          => 'kn_related_company',
                'type'          => 'relationship',
                'post_type'     => ['card_loan_company'],
                'filters'       => ['search'],
                'max'           => 3,
                'return_format' => 'id',
                'instructions'  => '関連するカードローン会社を選択してください。',
            ],
            [
                'key'           => 'field_kn_related_page',
                'label'         => '関連固定ページ',
                'name'          => 'kn_related_page',
                'type'          => 'relationship',
                'post_type'     => ['page'],
                'filters'       => ['search'],
                'max'           => 3,
                'return_format' => 'id',
                'instructions'  => '関連する固定ページを選択してください。',
            ],
            [
                'key'          => 'field_kn_related_keywords',
                'label'        => '関連キーワード（カンマ区切り）',
                'name'         => 'kn_related_keywords',
                'type'         => 'text',
                'instructions' => '例：在籍確認, 審査, 申込',
            ],
        ],
    ]);
});

// SEO：knowledge ページに noindex 出力
add_action('wp_head', function () {
    if (!is_singular('knowledge')) return;
    $post_id = get_queried_object_id();
    if (!function_exists('get_field')) return;
    if (get_field('kn_noindex', $post_id)) {
        echo '<meta name="robots" content="noindex, follow">' . "\n";
    }
}, 5);
