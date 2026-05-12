<?php
if (!defined('ABSPATH')) exit;

add_action('acf/include_fields', function () {
    if (!function_exists('acf_add_local_field_group')) return;

    // セクション補足フィールド（3セット分）を生成
    $section_fields = [];
    for ($s = 1; $s <= 3; $s++) {
        $section_fields[] = [
            'key'     => "field_col_kn_sec{$s}_sep",
            'label'   => "── セクション {$s} ──",
            'name'    => '',
            'type'    => 'message',
            'message' => "本文中のセクション{$s}の下に差し込む補足カード・関連FAQです。",
        ];
        $section_fields[] = [
            'key'          => "field_col_kn_sec{$s}_name",
            'label'        => "セクション{$s} 管理用メモ",
            'name'         => "col_sec{$s}_name",
            'type'         => 'text',
            'instructions' => 'どのセクションに対応するか管理用にメモしてください（表示はされません）。',
        ];
        $section_fields[] = [
            'key'           => "field_col_kn_sec{$s}_faq",
            'label'         => "セクション{$s} 関連FAQ",
            'name'          => "col_sec{$s}_faq",
            'type'          => 'relationship',
            'post_type'     => ['knowledge'],
            'filters'       => ['search', 'taxonomy'],
            'max'           => 3,
            'return_format' => 'id',
            'instructions'  => 'このセクション内に表示する関連FAQリンクを最大3件選択してください。',
        ];
        $section_fields[] = [
            'key'          => "field_col_kn_sec{$s}_sup_heading",
            'label'        => "セクション{$s} 補足カード 見出し",
            'name'         => "col_sec{$s}_sup_heading",
            'type'         => 'text',
        ];
        $section_fields[] = [
            'key'          => "field_col_kn_sec{$s}_sup_body",
            'label'        => "セクション{$s} 補足カード 本文",
            'name'         => "col_sec{$s}_sup_body",
            'type'         => 'textarea',
            'rows'         => 3,
            'instructions' => '2〜4行程度の補足説明を入力してください。',
        ];
        $section_fields[] = [
            'key'          => "field_col_kn_sec{$s}_sup_url",
            'label'        => "セクション{$s} 補足カード リンクURL",
            'name'         => "col_sec{$s}_sup_url",
            'type'         => 'url',
        ];
        $section_fields[] = [
            'key'           => "field_col_kn_sec{$s}_sup_label",
            'label'         => "セクション{$s} 補足カード リンク文言",
            'name'          => "col_sec{$s}_sup_label",
            'type'          => 'text',
            'default_value' => '詳しく見る',
        ];
    }

    acf_add_local_field_group([
        'key'      => 'group_col_knowledge',
        'title'    => 'ナレッジ連携（FAQ・用語導線）',
        'position' => 'normal',
        'style'    => 'default',
        'location' => [[['param' => 'post_type', 'operator' => '==', 'value' => 'column']]],
        'fields'   => array_merge(
            [
                [
                    'key'     => 'field_col_kn_usage_note',
                    'label'   => '使い方',
                    'name'    => '',
                    'type'    => 'message',
                    'message' => '<p>ここで選択したナレッジ（FAQ・用語）は記事の各位置に自動で表示されます。</p>
<ul>
<li><strong>導入下FAQ</strong>：リード文の直下にリンク形式で表示（最大3件）</li>
<li><strong>記事下FAQアコーディオン</strong>：本文の後にアコーディオン形式で表示（最大5件）</li>
<li><strong>記事末 関連FAQ</strong>：記事の一番下にリンク形式で表示（最大3件）</li>
<li><strong>記事末 用語導線</strong>：記事の一番下に用語チップ形式で表示（最大6件）</li>
</ul>
<p>セクション補足は本文内の <code>[kn_section_1]</code> 〜 <code>[kn_section_3]</code> ショートコードで差し込めます。</p>',
                ],

                // ── 記事全体向け ──
                [
                    'key'           => 'field_col_kn_intro_faq',
                    'label'         => '導入下の関連FAQ（リード文直下）',
                    'name'          => 'col_intro_faq',
                    'type'          => 'relationship',
                    'post_type'     => ['knowledge'],
                    'filters'       => ['search', 'taxonomy'],
                    'max'           => 3,
                    'return_format' => 'id',
                    'instructions'  => 'リード文の直下にリンク一覧として表示されます。最大3件。',
                ],
                [
                    'key'           => 'field_col_kn_article_faq',
                    'label'         => '記事下 FAQアコーディオン',
                    'name'          => 'col_article_faq',
                    'type'          => 'relationship',
                    'post_type'     => ['knowledge'],
                    'filters'       => ['search', 'taxonomy'],
                    'max'           => 5,
                    'return_format' => 'id',
                    'instructions'  => '記事本文の後にアコーディオン形式で表示されます。最大5件。',
                ],
                [
                    'key'           => 'field_col_kn_related_faq_bottom',
                    'label'         => '記事末 関連FAQリンク',
                    'name'          => 'col_related_faq_bottom',
                    'type'          => 'relationship',
                    'post_type'     => ['knowledge'],
                    'filters'       => ['search', 'taxonomy'],
                    'max'           => 3,
                    'return_format' => 'id',
                    'instructions'  => '記事の最後に関連FAQリンクとして表示されます。最大3件。',
                ],
                [
                    'key'           => 'field_col_kn_glossary_bottom',
                    'label'         => '記事末 用語導線チップ',
                    'name'          => 'col_glossary_bottom',
                    'type'          => 'relationship',
                    'post_type'     => ['knowledge'],
                    'filters'       => ['search', 'taxonomy'],
                    'max'           => 6,
                    'return_format' => 'id',
                    'instructions'  => '記事の最後に用語チップとして表示されます。種別「用語解説」のナレッジを選ぶと整合性が取れます。最大6件。',
                ],
            ],
            $section_fields
        ),
    ]);
});

// ショートコード [kn_section_1] 〜 [kn_section_3]
// コラム本文内の任意の位置に補足カード＋関連FAQを差し込む
add_action('init', function () {
    for ($s = 1; $s <= 3; $s++) {
        add_shortcode("kn_section_{$s}", function ($atts, $content = null, $tag = '') use ($s) {
            if (!is_singular('column')) return '';
            $post_id = get_the_ID();
            if (!$post_id || !function_exists('get_field')) return '';

            ob_start();

            // 補足カード
            $heading = get_field("col_sec{$s}_sup_heading", $post_id);
            $body    = get_field("col_sec{$s}_sup_body",    $post_id);
            $url     = get_field("col_sec{$s}_sup_url",     $post_id);
            $label   = get_field("col_sec{$s}_sup_label",   $post_id) ?: '詳しく見る';

            if ($heading || $body) {
                get_template_part('template-parts/knowledge/supplement-card', null, compact('heading', 'body', 'url', 'label'));
            }

            // 関連FAQリンク
            $faq_ids = get_field("col_sec{$s}_faq", $post_id);
            if ($faq_ids && is_array($faq_ids)) {
                get_template_part('template-parts/knowledge/related-links', null, [
                    'knowledge_ids' => $faq_ids,
                    'title'         => '関連FAQ',
                ]);
            }

            return ob_get_clean();
        });
    }
});
