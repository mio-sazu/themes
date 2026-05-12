<?php

/**
 * ======================================
 * CPT/TAXONOMY REGISTRATION (CENTRALIZED)
 * ======================================
 * mu-pluginとの競合を避けるため、ここに一本化
 */

// ============================
// PVカウント用：メタに保存
// ============================
function md_set_post_views($post_id)
{

  // ログインユーザーをカウントしたくなければここで return
  // if ( is_user_logged_in() ) return;

  $count_key = 'md_post_views';
  $count     = get_post_meta($post_id, $count_key, true);

  if ($count === '') {
    // 初回アクセス
    $count = 1;
    add_post_meta($post_id, $count_key, $count, true);
  } else {
    $count = (int) $count + 1;
    update_post_meta($post_id, $count_key, $count);
  }
}

// シングルページ表示時にPVカウント
function md_count_post_views()
{
  if (! is_singular()) {
    return;
  }

  global $post;

  if (! $post || ! isset($post->ID)) {
    return;
  }

  md_set_post_views($post->ID);
}
add_action('wp', 'md_count_post_views');

add_action('init', function () {

  // ============================
  // カスタム投稿タイプ用設定
  // ============================
  // --- お金コラム ---
  register_post_type('column', [
    'label'              => 'お金コラム',
    'public'             => true,
    'publicly_queryable' => true,
    'has_archive'        => 'column',
    'rewrite'            => ['slug' => 'column', 'with_front' => false],
    'show_in_rest'       => true,
    'supports'           => ['title', 'editor', 'thumbnail', 'excerpt', 'revisions'],
    'menu_icon'          => 'dashicons-media-text',
  ]);

  // コラムカテゴリ（親子構造）
  register_taxonomy('column_category', ['column'], [
    'label'        => 'コラムカテゴリ',
    'public'       => true,
    'hierarchical' => true,
    'show_in_rest' => true,
    'rewrite'      => ['slug' => 'column/%year%/%monthnum%/%day%', 'with_front' => false],
  ]);


  // --- ランキング ---
  register_post_type('ranking', [
    'label' => 'ランキング',
    'public' => true,
    'has_archive' => true,
    'rewrite' => ['slug' => 'ranking-list', 'with_front' => false], // ★ 'ranking-list' に変更    
    'show_in_rest' => true,
    // ACFを使うため 'custom-fields' を追加
    'supports' => ['title', 'editor', 'thumbnail', 'excerpt', 'revisions', 'custom-fields'],
    'menu_icon'   => 'dashicons-awards',
    'menu_position' => 7,
  ]);

  register_taxonomy('ranking_year', ['ranking'], [
    'label' => 'ランキング年',
    'public' => true,
    'hierarchical' => false, // 年は親子関係なし
    'show_in_rest' => true,
    'rewrite' => ['slug' => 'ranking_year', 'with_front' => false],
  ]);

  register_taxonomy('ranking_category', ['ranking'], [
    'label' => 'ランキング種別',
    'public' => true,
    'hierarchical' => true, // 種別は親子関係あり
    'show_in_rest' => true,
    'rewrite' => ['slug' => 'ranking_category', 'with_front' => false],
  ]);

  // 念のため、初回起動時にリライトルールを強制フラッシュ
  if (! get_option('sakunavi_migrated_flush_v1')) {
    flush_rewrite_rules();
    update_option('sakunavi_migrated_flush_v1', 1);
  }
}, 0); // 優先度を0にして早めに実行


/**
 * ======================================
 * CHILD THEME FUNCTIONS (Original)
 * ======================================
 */

/**
 * Enqueue assets & replace parent's main.js
 */
add_action('wp_enqueue_scripts', function () {
  // Parent style
  $parent_handle    = 'parent-style';
  $parent_style_uri = get_template_directory_uri() . '/style.css';
  $parent_ver       = wp_get_theme(get_template())->get('Version');
  wp_enqueue_style($parent_handle, $parent_style_uri, [], $parent_ver);

  // Child style
  $child_ver = wp_get_theme()->get('Version');
  wp_enqueue_style('sakunavi-child-style', get_stylesheet_uri(), [$parent_handle], $child_ver);

  // Replace parent's main handle if it exists
  $parent_handle_js = 'sakunavi-main';
  if (wp_script_is($parent_handle_js, 'enqueued') || wp_script_is($parent_handle_js, 'registered')) {
    wp_dequeue_script($parent_handle_js);
    wp_deregister_script($parent_handle_js);
  }

  // Enqueue child's main.js using the same handle to keep dependencies intact
  $child_js_file = get_stylesheet_directory() . '/assets/js/main.js';
  $child_js_uri  = get_stylesheet_directory_uri() . '/assets/js/main.js';
  $ver = file_exists($child_js_file) ? filemtime($child_js_file) : null;
  wp_enqueue_script($parent_handle_js, $child_js_uri, [], $ver, true);
}, 20);

/**
 * Optimize queries for card_company archives & loan_genre taxonomy
 */
add_action('pre_get_posts', function ($q) {
  if (is_admin() || !$q->is_main_query()) return;
  if ($q->is_post_type_archive('card_company') || $q->is_tax('loan_genre')) {
    $q->set('meta_key', 'daily_rank');
    $q->set('orderby', 'meta_value_num');
    $q->set('order', 'ASC');
    if (!$q->get('posts_per_page')) $q->set('posts_per_page', 12);
  }
});

/**
 * Render page content by slug (e.g., 'notices')
 */
function sakunavi_child_render_page_content_by_path($slug)
{
  if (!$slug) return '';
  $p = get_page_by_path($slug);
  if (!$p) $p = get_page_by_path('注意事項');
  if (!$p) return '<p>（注意事項のページが見つかりません）</p>';
  return apply_filters('the_content', $p->post_content);
}

/**
 * Shortcode: [page_content slug="notices"]
 */
add_shortcode('page_content', function ($atts) {
  $atts = shortcode_atts(['slug' => ''], $atts);
  return sakunavi_child_render_page_content_by_path($atts['slug']);
});

/**
 * Load child textdomain
 */
add_action('after_setup_theme', function () {
  load_child_theme_textdomain('sakunavi-child', get_stylesheet_directory() . '/languages');
});

// --- Footer 3 columns widget areas ---
add_action('after_setup_theme', function () {
  // 念のためカスタムロゴに対応（未対応テーマでも使えるように）
  add_theme_support('custom-logo', [
    'height' => 80,
    'width' => 240,
    'flex-height' => true,
    'flex-width' => true,
  ]);
});

add_action('widgets_init', function () {
  register_sidebar([
    'name' => 'Footer 1：ブランド',
    'id' => 'footer-1',
    'description' => 'ロゴとキャッチフレーズ向け',
    'before_widget' => '<div class="footer-col footer-col--brand %2$s">',
    'after_widget' => '</div>',
    'before_title'  => '<h2 class="widget-title">',
    'after_title'  => '</h2>',
  ]);
  register_sidebar([
    'name' => 'Footer 2：メニュー',
    'id' => 'footer-2',
    'description' => 'ナビゲーションメニュー向け',
    'before_widget' => '<div class="footer-col footer-col--menu %2$s">',
    'after_widget' => '</div>',
    'before_title'  => '<h2 class="widget-title">',
    'after_title'  => '</h2>',
  ]);
  register_sidebar([
    'name' => 'Footer 3：会社情報',
    'id' => 'footer-3',
    'description' => '会社情報（住所や連絡先など）',
    'before_widget' => '<div class="footer-col footer-col--company %2$s">',
    'after_widget' => '</div>',
    'before_title'  => '<h2 class="widget-title">',
    'after_title'  => '</h2>',
  ]);
});
// フッターの法務用メニュー位置を追加
add_action('after_setup_theme', function () {
  register_nav_menus([
    'footer-legal' => 'フッター（サイト情報/法務）',
  ]);
});

//single-card_company.phpのみにcompany.cssの読み込みをさせる
add_action('wp_enqueue_scripts', function () {
  if (is_singular('card_loan_company')) {
    $path = get_stylesheet_directory() . '/assets/css/company.css';
    wp_enqueue_style(
      'sakunavi-company',
      get_stylesheet_directory_uri() . '/assets/css/company.css',
      ['sakunavi-child-style'],
      file_exists($path) ? filemtime($path) : null
    );
  }
}, 30);

//FAQ用js
// 子テーマ想定。親テーマなら get_template_directory_uri() / get_template_directory() に変更
add_action('wp_enqueue_scripts', function () {
  // コラム記事のみで読み込み（必要に応じて条件を足す）
  if (is_singular('column')) {
    $path = get_stylesheet_directory() . '/assets/js/faq.js';
    wp_enqueue_script(
      'sakunavi-faq',
      get_stylesheet_directory_uri() . '/assets/js/faq.js',
      [],                            // 依存なし（バニラJS）
      file_exists($path) ? filemtime($path) : null,  // キャッシュバスター
      true                           // フッター読み込み
    );
  }
});


////// カードローン会社情報投稿ページ専用のACF
add_action('acf/init', function () {
  if (!function_exists('acf_add_local_field_group')) return;
  acf_add_local_field_group([
    'key' => 'group_card_company',
    'title' => '会社情報',
    'position' => 'acf_after_title',
    'label_placement' => 'top',
    'location' => [[['param' => 'post_type', 'operator' => '==', 'value' => 'card_company']]],
    'fields' => [
      ['key' => 'field_logo', 'label' => 'ロゴ', 'name' => 'logo', 'type' => 'image', 'return_format' => 'array', 'preview_size' => 'medium', 'library' => 'all'],
      ['key' => 'field_rate_min', 'label' => '金利（最小）', 'name' => 'rate_min', 'type' => 'number', 'append' => '%', 'step' => '0.01'],
      ['key' => 'field_rate_max', 'label' => '金利（最大）', 'name' => 'rate_max', 'type' => 'number', 'append' => '%', 'step' => '0.01'],
      ['key' => 'field_limit_amount_min', 'label' => '融資限度額（最小・万円）', 'name' => 'limit_amount_min', 'type' => 'number', 'append' => '万円', 'step' => '1'],
      ['key' => 'field_limit_amount_max', 'label' => '融資限度額（最大・万円）', 'name' => 'limit_amount_max', 'type' => 'number', 'append' => '万円', 'step' => '1'],
      ['key' => 'field_exam_fast', 'label' => '最短審査時間', 'name' => 'exam_fast', 'type' => 'text', 'placeholder' => '最短30分 など'],
      ['key' => 'field_no_interest_days', 'label' => '無利息期間（日）', 'name' => 'no_interest_days', 'type' => 'number', 'append' => '日', 'step' => '1'],
      ['key' => 'field_web_only', 'label' => 'Web完結', 'name' => 'web_only', 'type' => 'true_false', 'ui' => 1, 'ui_on_text' => '対応', 'ui_off_text' => '―'],
      ['key' => 'field_rank_score', 'label' => 'おすすめ度（0〜5）', 'name' => 'rank_score', 'type' => 'number', 'min' => '0', 'max' => '5', 'step' => '0.5'],
      ['key' => 'field_cta_label', 'label' => '申込ボタン文言', 'name' => 'cta_label', 'type' => 'text', 'placeholder' => '申し込む'],
      ['key' => 'field_cta_url', 'label' => '申込リンクURL', 'name' => 'cta_url', 'type' => 'url', 'placeholder' => 'https://example.com/'],
    ],
  ]);
});


////// カードローン会社情報投稿ページのFAQ用js
add_action('wp_enqueue_scripts', function () {
  if (is_singular('card_loan_company') || is_post_type_archive('card_loan_company')) {
    $p = get_stylesheet_directory() . '/assets/js/faq.js';
    wp_enqueue_script(
      'sakunavi-faq',
      get_stylesheet_directory_uri() . '/assets/js/faq.js',
      [],
      file_exists($p) ? filemtime($p) : null,
      true
    );
  }
}, 30);

// カードローン会社の金利表示（1 → 1.0 / 14.5 → 14.5） helper
function sakunavi_the_rate_range($post_id = null)
{
  $post_id = $post_id ?: get_the_ID();
  if (! $post_id) return;

  // ACFの金利フィールド
  $rate_min = get_field('rate_min', $post_id); // 金利（最小）
  $rate_max = get_field('rate_max', $post_id); // 金利（最大）

  // 何も入ってなければ何も出さない
  if ($rate_min === '' && $rate_max === '') {
    return;
  }

  // 小数第1位までで表示（1 → 1.0、18.5 → 18.5）
  $show_min = $rate_min !== '' && $rate_min !== null
    ? sprintf('%.1f', (float) $rate_min)
    : '';

  $show_max = $rate_max !== '' && $rate_max !== null
    ? sprintf('%.1f', (float) $rate_max)
    : '';

  // 表示のパターン
  if ($show_min !== '' && $show_max !== '') {
    // 1.0%〜18.0% 形式
    echo esc_html($show_min . '〜' . $show_max) . '%';
  } elseif ($show_min !== '') {
    // 最小だけ
    echo esc_html($show_min) . '%';
  } elseif ($show_max !== '') {
    // 最大だけ
    echo esc_html($show_max) . '%';
  }
}

// 融資限度額の表示（最小～最大・万円） helper
function sakunavi_the_limit_range($post_id = null)
{
  $post_id = $post_id ?: get_the_ID();
  if (! $post_id) return;

  // ACF の融資限度額フィールド
  $limit_min = get_field('limit_amount_min', $post_id);
  $limit_max = get_field('limit_amount_max', $post_id);

  // 何も入ってなければ出さない
  if ($limit_min === '' && $limit_max === '') {
    return;
  }

  // 数値をフォーマット（3桁カンマ区切り）
  $show_min = ($limit_min !== '' && $limit_min !== null)
    ? number_format((float) $limit_min)
    : '';

  $show_max = ($limit_max !== '' && $limit_max !== null)
    ? number_format((float) $limit_max)
    : '';

  // 表示パターン
  if ($show_min !== '' && $show_max !== '') {
    // 10万円 ～ 800万円
    echo esc_html($show_min . '万円 ～ ' . $show_max . '万円');
  } elseif ($show_min !== '') {
    // 下限だけ
    echo esc_html($show_min . '万円');
  } elseif ($show_max !== '') {
    // 上限だけ
    echo esc_html('～ ' . $show_max . '万円');
  }
}


/** サイドバー（コラム用） */
add_action('widgets_init', function () {
  register_sidebar([
    'name'          => 'コラム用サイドバー',
    'id'            => 'column-sidebar',
    'description'   => 'コラム記事の右サイドに表示されるウィジェットエリアです。',
    'before_widget' => '<div class="toc-box %2$s">',
    'after_widget'  => '</div>',
    'before_title'  => '<div class="toc-title">',
    'after_title'   => '</div>',
  ]);
});

// コラム系ページで CSS/JS を読み込む（フロント）
if (!function_exists('sakunavi_enqueue_column_assets')) {
  function sakunavi_enqueue_column_assets()
  {
    if (is_singular('column') || is_post_type_archive('column') || is_tax(array('column_category', 'column_persona'))) {

      $css = get_stylesheet_directory() . '/assets/css/support.css';
      if (file_exists($css)) {
        wp_enqueue_style(
          'sakunavi-column-css',
          get_stylesheet_directory_uri() . '/assets/css/support.css',
          array('sakunavi-child-style'), // 子テーマの後に
          filemtime($css)
        );
      }
      // 'column.css' を記事ページ(single-column.php)でのみ追加で読み込む
      if (is_singular('column')) {
        $column_css_path = get_stylesheet_directory() . '/assets/css/column.css';
        if (file_exists($column_css_path)) {
          wp_enqueue_style(
            'sakunavi-single-column-css', // 新しいハンドル名
            get_stylesheet_directory_uri() . '/assets/css/column.css',
            array('sakunavi-column-css'), // support.css の後に読み込む
            filemtime($column_css_path)
          );
        }
      }

      $js = get_stylesheet_directory() . '/assets/js/support.js';
      if (file_exists($js)) {
        wp_enqueue_script(
          'sakunavi-column-js',
          get_stylesheet_directory_uri() . '/assets/js/support.js',
          array('jquery'),
          filemtime($js),
          true
        );
      }
    }
  }
  add_action('wp_enqueue_scripts', 'sakunavi_enqueue_column_assets', 30); // 親/子の後
}

// ブロックエディタ（投稿編集画面）でも必要なら読み込む
add_action('enqueue_block_editor_assets', function () {
  $js_file = get_stylesheet_directory() . '/assets/js/support.js';
  if (file_exists($js_file)) {
    wp_enqueue_script(
      'column-support-editor',
      get_stylesheet_directory_uri() . '/assets/js/support.js',
      ['wp-element', 'wp-dom-ready'], // 依存は用途に合わせて
      filemtime($js_file),
      true
    );
  }
});

// ============================
// テンプレート用：ランキングページ生成
// ============================

// ランキング記事（single-ranking.php）のACFコード登録

//過去のACFなので削除予定
/*add_action('acf/include_fields', function () {
  if (!function_exists('acf_add_local_field_group')) return;

  $fields = [
    ['key' => 'field_lead', 'label' => '概要', 'name' => 'lead', 'type' => 'textarea', 'rows' => 3],
  ];

  for ($i = 1; $i <= 10; $i++) {
    $idx = str_pad($i, 2, '0', STR_PAD_LEFT);
    // 視覚区切り（Message フィールド）
    $fields[] = [
      'key' => "field_r{$idx}_sep",
      'label' => "— ランク {$i} —",
      'name' => '',
      'type' => 'message',
      'message' => "<strong>Rank {$i}</strong>",
    ];
    $fields[] = ['key' => "field_r{$idx}_title",     'label' => "{$i}位 名称",        'name' => "rank_{$i}_title",     'type' => 'text'];
    $fields[] = ['key' => "field_r{$idx}_logo",      'label' => "{$i}位 ロゴ",        'name' => "rank_{$i}_logo",      'type' => 'image', 'return_format' => 'array', 'preview_size' => 'medium'];
    $fields[] = ['key' => "field_r{$idx}_one",       'label' => "{$i}位 一言ポイント",  'name' => "rank_{$i}_one",       'type' => 'text'];
    $fields[] = ['key' => "field_r{$idx}_overall",   'label' => "{$i}位 総合評価(0-5)", 'name' => "rank_{$i}_overall",   'type' => 'number', 'min' => 0, 'max' => 5, 'step' => 0.5];

    $fields[] = ['key' => "field_r{$idx}_rate",      'label' => "{$i}位 金利",        'name' => "rank_{$i}_rate",      'type' => 'text'];
    $fields[] = ['key' => "field_r{$idx}_speed",     'label' => "{$i}位 スピード",    'name' => "rank_{$i}_speed",     'type' => 'text'];
    $fields[] = ['key' => "field_r{$idx}_ease",      'label' => "{$i}位 使いやすさ",  'name' => "rank_{$i}_ease",      'type' => 'text'];

    $fields[] = ['key' => "field_r{$idx}_reason",    'label' => "{$i}位 ここがポイント", 'name' => "rank_{$i}_reason",    'type' => 'wysiwyg'];
    $fields[] = ['key' => "field_r{$idx}_cta_label", 'label' => "{$i}位 CTAラベル",     'name' => "rank_{$i}_cta_label", 'type' => 'text', 'default_value' => '申し込む'];
    $fields[] = ['key' => "field_r{$idx}_cta_url",   'label' => "{$i}位 CTA URL",      'name' => "rank_{$i}_cta_url",   'type' => 'url'];

    // 口コミ 2件分（必要なら数を増やせます）
    for ($r = 1; $r <= 2; $r++) {
      $fields[] = ['key' => "field_r{$idx}_rev{$r}_persona", 'label' => "{$i}位 口コミ{$r} ペルソナ", 'name' => "rank_{$i}_review{$r}_persona", 'type' => 'text'];
      $fields[] = ['key' => "field_r{$idx}_rev{$r}_stars",   'label' => "{$i}位 口コミ{$r} 星(1-5)", 'name' => "rank_{$i}_review{$r}_stars",   'type' => 'number', 'min' => 1, 'max' => 5, 'step' => 1];
      $fields[] = ['key' => "field_r{$idx}_rev{$r}_text",    'label' => "{$i}位 口コミ{$r} 本文",     'name' => "rank_{$i}_review{$r}_text",    'type' => 'textarea', 'rows' => 3];
      // ★追加：アバター（画像）
      $fields[] = ['key' => "field_r{$idx}_rev{$r}_avatar",  'label' => "{$i}位 口コミ{$r} アイコン", 'name' => "rank_{$i}_review{$r}_avatar",  'type' => 'image', 'return_format' => 'array', 'preview_size' => 'thumbnail'];
    }
  }

  acf_add_local_field_group([
    'key' => 'group_ranking_page_simple',
    'title' => 'Ranking Page',
    'fields' => $fields,
    'location' => [[['param' => 'post_type', 'operator' => '==', 'value' => 'ranking']]],
    'position' => 'normal',
    'style' => 'default',
  ]);
});
*/

//ランキング記事（single-ranking.php）のACFコード登録
// Ranking用フィールド配列を作る関数（ranking/variant共通）
function sakunavi_ranking_fields_base()
{
  $fields = [
    ['key' => 'field_lead', 'label' => '概要', 'name' => 'lead', 'type' => 'textarea', 'rows' => 3],
  ];

  for ($i = 1; $i <= 10; $i++) {
    $idx = str_pad($i, 2, '0', STR_PAD_LEFT);
    $fields[] = [
      'key' => "field_r{$idx}_sep",
      'label' => "— ランク {$i} —",
      'name' => '',
      'type' => 'message',
      'message' => "<strong>Rank {$i}</strong>",
    ];
    $fields[] = ['key' => "field_r{$idx}_title",     'label' => "{$i}位 名称",          'name' => "rank_{$i}_title",     'type' => 'text'];
    $fields[] = ['key' => "field_r{$idx}_logo",      'label' => "{$i}位 ロゴ",          'name' => "rank_{$i}_logo",      'type' => 'image', 'return_format' => 'array', 'preview_size' => 'medium'];
    $fields[] = ['key' => "field_r{$idx}_one",       'label' => "{$i}位 一言ポイント",  'name' => "rank_{$i}_one",       'type' => 'text'];
    $fields[] = ['key' => "field_r{$idx}_overall",   'label' => "{$i}位 総合評価(0-5)", 'name' => "rank_{$i}_overall",   'type' => 'number', 'min' => 0, 'max' => 5, 'step' => 0.5];

    $fields[] = ['key' => "field_r{$idx}_rate",      'label' => "{$i}位 金利",          'name' => "rank_{$i}_rate",      'type' => 'text'];
    $fields[] = ['key' => "field_r{$idx}_speed",     'label' => "{$i}位 スピード",      'name' => "rank_{$i}_speed",     'type' => 'text'];
    $fields[] = ['key' => "field_r{$idx}_ease",      'label' => "{$i}位 使いやすさ",    'name' => "rank_{$i}_ease",      'type' => 'text'];

    $fields[] = ['key' => "field_r{$idx}_reason",    'label' => "{$i}位 ここがポイント", 'name' => "rank_{$i}_reason",    'type' => 'wysiwyg'];
    $fields[] = ['key' => "field_r{$idx}_cta_label", 'label' => "{$i}位 CTAラベル",      'name' => "rank_{$i}_cta_label", 'type' => 'text', 'default_value' => '申し込む'];
    $fields[] = ['key' => "field_r{$idx}_cta_url",   'label' => "{$i}位 CTA URL",        'name' => "rank_{$i}_cta_url",   'type' => 'url'];

    for ($r = 1; $r <= 2; $r++) {
      $fields[] = ['key' => "field_r{$idx}_rev{$r}_persona", 'label' => "{$i}位 口コミ{$r} ペルソナ", 'name' => "rank_{$i}_review{$r}_persona", 'type' => 'text'];
      $fields[] = ['key' => "field_r{$idx}_rev{$r}_stars",   'label' => "{$i}位 口コミ{$r} 星(1-5)",   'name' => "rank_{$i}_review{$r}_stars",   'type' => 'number', 'min' => 1, 'max' => 5, 'step' => 1];
      $fields[] = ['key' => "field_r{$idx}_rev{$r}_text",    'label' => "{$i}位 口コミ{$r} 本文",       'name' => "rank_{$i}_review{$r}_text",    'type' => 'textarea', 'rows' => 3];
      $fields[] = ['key' => "field_r{$idx}_rev{$r}_avatar",  'label' => "{$i}位 口コミ{$r} アイコン",   'name' => "rank_{$i}_review{$r}_avatar",  'type' => 'image', 'return_format' => 'array', 'preview_size' => 'thumbnail'];
    }
  }

  return $fields;
}

add_action('acf/include_fields', function () {
  if (!function_exists('acf_add_local_field_group')) return;

  // 親ranking用（今のまま）
  acf_add_local_field_group([
    'key' => 'group_ranking_page_simple',
    'title' => 'Ranking Page',
    'fields' => sakunavi_ranking_fields_base(),
    'location' => [[['param' => 'post_type', 'operator' => '==', 'value' => 'ranking']]],
    'position' => 'normal',
    'style' => 'default',
  ]);

  // 子variant用（共通fields + variant専用）
  $variant_fields = array_merge(
    [
      ['key' => 'field_variant_enable', 'label' => 'このパターンを有効にする', 'name' => 'variant_enable', 'type' => 'true_false', 'ui' => 1, 'default_value' => 1],
      ['key' => 'field_variant_weight', 'label' => '配信比率(weight)', 'name' => 'weight', 'type' => 'number', 'min' => 1, 'step' => 1, 'default_value' => 10],
    ],
    sakunavi_ranking_fields_base()
  );

  acf_add_local_field_group([
    'key' => 'group_ranking_variant_simple',
    'title' => 'Ranking Variant',
    'fields' => $variant_fields,
    'location' => [[['param' => 'post_type', 'operator' => '==', 'value' => 'ranking_variant']]],
    'position' => 'normal',
    'style' => 'default',
  ]);
});



// ランキング記事（single-ranking.php）のCSS
// --- Ranking 用アセット（親・子の後）---
if (!function_exists('sakunavi_enqueue_ranking_assets')) {
  function sakunavi_enqueue_ranking_assets()
  {
    if (is_singular('ranking') || is_post_type_archive('ranking') || is_tax(array('ranking_year', 'ranking_category'))) {

      $css = get_stylesheet_directory() . '/assets/css/ranking.css';
      if (file_exists($css)) {
        wp_enqueue_style(
          'sakunavi-ranking-css',
          get_stylesheet_directory_uri() . '/assets/css/ranking.css',
          array('sakunavi-child-style'),
          filemtime($css)
        );
      }

      // （任意）assets/js/ranking.js がある場合のみ
      $js = get_stylesheet_directory() . '/assets/js/ranking.js';
      if (file_exists($js)) {
        wp_enqueue_script(
          'sakunavi-ranking-js',
          get_stylesheet_directory_uri() . '/assets/js/ranking.js',
          array('jquery'),
          filemtime($js),
          true
        );
      }
    }
  }
  add_action('wp_enqueue_scripts', 'sakunavi_enqueue_ranking_assets', 30);
}

// ============================
// 管理画面：ランキング子ページUI
// ============================

add_action('admin_menu', function () {
  add_submenu_page(
    'edit.php?post_type=ranking',          // 親（ランキング）のメニューURL
    'ランキング記事作成',                  // ページタイトル
    'ランキング記事作成',                  // メニュー表示名
    'edit_posts',                          // 権限（必要ならedit_rankingsにする）
    'edit.php?post_type=ranking_variant'   // 遷移先（一覧）
  );
}, 999);

add_action('init', function () {
  $labels = [
    'name'               => 'ランキング記事作成',
    'singular_name'      => 'ランキング記事作成',
    'menu_name'          => 'ランキング記事作成',
    'add_new'            => '新規追加',
    'add_new_item'       => 'ランキング記事作成を追加',
    'edit_item'          => 'ランキング記事作成を編集',
    'new_item'           => '新規ランキング記事作成',
    'view_item'          => '表示',
    'search_items'       => 'ランキング記事作成を検索',
    'not_found'          => '見つかりませんでした',
    'not_found_in_trash' => 'ゴミ箱に見つかりませんでした',
    'all_items'          => '一覧',
  ];

  register_post_type('ranking_variant', [
    'labels'        => $labels,
    'public'        => false,  // フロント公開しない（管理用）
    'show_ui'       => true,
    // ★ここがポイント：親CPT「ranking」の下に入れる
    'show_in_menu'  => 'false',

    'hierarchical'  => true,   // 親（post_parent）を持てる
    'supports'      => ['title', 'page-attributes'], // 親設定/並び順(menu_order)

    // menu_position / menu_icon は「親メニュー配下」だと基本不要
    // 'menu_icon' => 'dashicons-randomize',
  ]);
});

/**
 * 1) column のタクソノミー/アーカイブを“必ず” column 投稿に固定
 * さらにページネーション・並びもここで統一
 */
add_action('pre_get_posts', function ($q) {
  if (is_admin() || !$q->is_main_query()) return;

  // /column-category/xxx/ など
  if ($q->is_tax('column_category')) {
    $q->set('post_type', 'column');
    $q->set('posts_per_page', 10);
    $q->set('orderby', 'date');
    $q->set('order', 'DESC');
  }

  // /column/ アーカイブ
  if ($q->is_post_type_archive('column')) {
    $q->set('posts_per_page', 10);
    $q->set('orderby', 'date');
    $q->set('order', 'DESC');
  }

  // ランキングのアーカイブ（ある場合）
  if ($q->is_post_type_archive('ranking') || is_tax('ranking_year') || is_tax('ranking_category')) {
    // タクソノミーアーカイブの場合は、必ず'ranking'投稿タイプを対象にする
    if (is_tax()) $q->set('post_type', 'ranking');
    $q->set('posts_per_page', 10);
    // ランキングは特に並び順を指定しない場合、管理画面での順番や日付順になる
  }
});


/**
 * 2) テンプレート解決を子テーマのファイルに強制（安全対策）
 * 置き場所・ファイル名が正しくても、環境/パーマリンクで外れる事があるため保険として。
 */
add_filter('taxonomy_template', function ($template) {
  $path = get_stylesheet_directory();
  if (is_tax('column_category')) {
    $child = $path . '/taxonomy-column_category.php';
    if (file_exists($child)) return $child;
  }
  if (is_tax('ranking_category')) {
    $child = $path . '/taxonomy-ranking_category.php';
    if (file_exists($child)) return $child;
  }
  if (is_tax('ranking_year')) {
    $child = $path . '/taxonomy-ranking_year.php';
    if (file_exists($child)) return $child;
  }
  return $template;
}, 99);

add_filter('archive_template', function ($template) {
  $path = get_stylesheet_directory();
  if (is_post_type_archive('column')) {
    $child = $path . '/archive-column.php';
    if (file_exists($child)) return $child;
  }
  if (is_post_type_archive('ranking')) {
    $child = $path . '/archive-ranking.php';
    if (file_exists($child)) return $child;
  }
  return $template;
}, 99);

/**
 * 2b) 個別投稿 (single) テンプレート解決を子テーマのファイルに強制
 * ------------------------------- */
add_filter('single_template', function ($template) {
  global $post;
  if (!$post) return $template; // 投稿データが無い場合は何もしない

  $path = get_stylesheet_directory();

  // ランキング記事
  if ($post->post_type === 'ranking') {
    $child_template = $path . '/single-ranking.php';
    if (file_exists($child_template)) {
      return $child_template;
    }
  }

  // コラム記事
  if ($post->post_type === 'column') {
    $child_template = $path . '/single-column.php';
    if (file_exists($child_template)) {
      return $child_template;
    }
  }

  return $template;
}, 99);


/**
 * 3) 必要CSSの後勝ちロード（サポートCSSより後）
 * 依存ハンドルを子テーマCSSに統一して競合を防止
 */
add_action('wp_enqueue_scripts', function () {
  $base = 'sakunavi-child-style';

  // 一覧用
  $arch = get_stylesheet_directory() . '/assets/css/archives.css';
  if ((is_post_type_archive('column') || is_tax('column_category') ||
    is_post_type_archive('ranking') || is_tax('ranking_year') || is_tax('ranking_category')) && file_exists($arch)) { // 【★条件追加★】
    wp_enqueue_style(
      'sakunavi-archives-css',
      get_stylesheet_directory_uri() . '/assets/css/archives.css',
      array($base),
      filemtime($arch)
    );
  }

  // パンくずは常に後勝ち
  $bc = get_stylesheet_directory() . '/assets/css/breadcrumbs.css';
  if (file_exists($bc)) {
    wp_enqueue_style(
      'sakunavi-breadcrumbs-css',
      get_stylesheet_directory_uri() . '/assets/css/breadcrumbs.css',
      array($base,),
      filemtime($bc)
    );
  }
}, 40);


/**
 * 4) 画面下に“どのテンプレが使われたか”をコメント出力（トラブル時の目視確認用）
 * 問題解決後は消してOK
 */
add_action('wp_footer', function () {
  if (current_user_can('manage_options')) {
    global $template, $post;
    // デバッグコメントを追加
    echo "\n\n";
    if ($post) {
      echo "\n";
    } else {
      echo "\n";
    }
    echo "\n";
  }
}, 999);

/**
 * -----------------------------------------------
 * 「コラム」と「ランキング」の一覧にスラッグ列を追加する
 * -----------------------------------------------
 */

// 1. 列のヘッダーを追加する
function add_slug_column_to_cpt_list($columns)
{
  $new_columns = [];
  foreach ($columns as $key => $title) {
    $new_columns[$key] = $title;
    if ($key === 'title') { // 'title' (タイトル) の直後に追加
      $new_columns['slug'] = 'スラッグ';
    }
  }
  // 'title' が見つからなかった場合のフォールバック
  if (!isset($new_columns['slug'])) {
    $new_columns['slug'] = 'スラッグ';
  }
  return $new_columns;
}
add_filter('manage_column_posts_columns', 'add_slug_column_to_cpt_list'); // コラム
add_filter('manage_ranking_posts_columns', 'add_slug_column_to_cpt_list'); // ランキング


// 2. 列に内容（スラッグ本体）を表示する
function display_cpt_slug_column($column_name, $post_id)
{
  if ($column_name === 'slug') {
    $post = get_post($post_id);
    echo esc_html($post->post_name);
  }
}
add_action('manage_column_posts_custom_column', 'display_cpt_slug_column', 10, 2); // コラム
add_action('manage_ranking_posts_custom_column', 'display_cpt_slug_column', 10, 2); // ランキング

/**
 * -----------------------------------------------
 * 「コラム」のパーマリンクを /column/YYYY/MM/DD/slug/ 形式に変更
 * （ステップ1の rewrite ルールと連動）
 * -----------------------------------------------
 */
function sakunavi_column_permalink_structure($link, $post)
{
  if ($post->post_type === 'column') {
    // パーマリンク設定が '基本' (?p=123) 以外の場合のみ
    if (get_option('permalink_structure')) {
      $post_date = strtotime($post->post_date);
      $year  = date('Y', $post_date);
      $month = date('m', $post_date);
      $day   = date('d', $post_date);

      // rewrite タグを日付に置き換え
      $link = str_replace('%year%', $year, $link);
      $link = str_replace('%monthnum%', $month, $link);
      $link = str_replace('%day%', $day, $link);
    }
  }
  return $link;
}
add_filter('post_type_link', 'sakunavi_column_permalink_structure', 10, 2);

/**
 * -----------------------------------------------
 * 「コラム」のスラッグを自動で「投稿ID」に設定
 * （スラッグを英字で考えなくて済むようにする）
 * -----------------------------------------------
 */
function sakunavi_set_column_slug_to_id($post_id, $post, $update)
{
  // 自動保存、リビジョン、または 'auto-draft' 状態のときは何もしない
  if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
  if (wp_is_post_revision($post_id)) return;
  if ($post->post_status === 'auto-draft') return;

  // 'column' 投稿タイプの場合
  if ($post->post_type === 'column') {

    // スラッグが既に投稿IDになっていれば、何もしない（無限ループ防止）
    if ($post->post_name == (string) $post_id) return;

    // 無限ループを避けるために、このフックを一時的に解除
    remove_action('save_post_column', 'sakunavi_set_column_slug_to_id', 10, 3);

    // 投稿のスラッグ(post_name)を投稿IDで上書き更新
    wp_update_post([
      'ID'        => $post_id,
      'post_name' => (string) $post_id,
    ]);

    // フックを再度登録
    add_action('save_post_column', 'sakunavi_set_column_slug_to_id', 10, 3);
  }
}
// save_post_{post_type} フックで、コラム保存時のみ実行
add_action('save_post_column', 'sakunavi_set_column_slug_to_id', 10, 3);

// --- Archives と Breadcrumbs は一番最後に ---
if (!function_exists('sakunavi_enqueue_archives_and_breadcrumbs')) {
  function sakunavi_enqueue_archives_and_breadcrumbs()
  {
    $base = 'sakunavi-child-style'; // 既に読み込んでいる子テーマのCSS

    $is_archive_like =
      is_post_type_archive('column') || is_tax(array('column_category', 'column_persona')) ||
      is_post_type_archive('ranking') || is_tax(array('ranking_year', 'ranking_category'));

    // archives.css（一覧系ページのみ）
    $arch = get_stylesheet_directory() . '/assets/css/archives.css';
    if ($is_archive_like && file_exists($arch)) {
      wp_enqueue_style(
        'sakunavi-archives-css',
        get_stylesheet_directory_uri() . '/assets/css/archives.css',
        array($base),                                   // ← 依存を子テーマのメインに
        filemtime($arch)
      );
    }

    // パンくずは全ページでOK（最後に）
    $bc = get_stylesheet_directory() . '/assets/css/breadcrumbs.css';
    if (file_exists($bc)) {
      wp_enqueue_style(
        'sakunavi-breadcrumbs-css',
        get_stylesheet_directory_uri() . '/assets/css/breadcrumbs.css',
        array($base, 'sakunavi-archives-css', 'sakunavi-column-css', 'sakunavi-ranking-css'), // ハンドル名を修正 // あれば更に後勝ち
        filemtime($bc)
      );
    }
  }
  add_action('wp_enqueue_scripts', 'sakunavi_enqueue_archives_and_breadcrumbs', 40);
}

/**
 * -----------------------------------------------
 * カミングスーンページ生成　未編集の物はComing soon固定ページ表示
 * （ログインしてるとカミングスーンではなく編集プレビュー）
 * -----------------------------------------------
 */
/**
 * 投稿ごとの「カミングスーン」機能一式
 * - チェックボックス
 * - 公開予定日時
 * - メモ欄
 * - リダイレクト
 * - 管理画面カラム＆絞り込み
 * - バッジ用ヘルパー
 */

// ============================
// メタボックス追加
// ============================
function md_add_coming_soon_metabox()
{
  // カミングスーンを使いたい投稿タイプを自動で全部取得（投稿・固定ページ・CPT）
  $screens = get_post_types(
    array(
      'public'  => true,
      'show_ui' => true,
    ),
    'names'
  );

  foreach ($screens as $screen) {
    add_meta_box(
      'md_coming_soon',
      'カミングスーン設定',
      'md_coming_soon_metabox_html',
      $screen,
      'side',
      'high'
    );
  }
}
add_action('add_meta_boxes', 'md_add_coming_soon_metabox');


function md_coming_soon_metabox_html($post)
{

  $is_coming_soon = get_post_meta($post->ID, '_md_coming_soon', true);
  $until          = get_post_meta($post->ID, '_md_coming_soon_until', true); // 文字列
  $note           = get_post_meta($post->ID, '_md_coming_soon_note', true);

  // datetime-local 用に整形
  $until_value = '';
  if (! empty($until)) {
    $ts = strtotime($until);
    if ($ts) {
      $until_value = date('Y-m-d\TH:i', $ts);
    }
  }

  wp_nonce_field('md_coming_soon_nonce', 'md_coming_soon_nonce_field');
?>
  <p>
    <label>
      <input type="checkbox" name="md_coming_soon" value="1" <?php checked($is_coming_soon, '1'); ?> />
      この投稿をカミングスーンにする
    </label>
  </p>

  <p>
    <label for="md_coming_soon_until"><strong>公開予定日時（任意）</strong></label><br>
    <input
      type="datetime-local"
      id="md_coming_soon_until"
      name="md_coming_soon_until"
      value="<?php echo esc_attr($until_value); ?>"
      style="width:100%;" />
    <small>この日時まではカミングスーン扱いになります（未設定なら手動で解除）。</small>
  </p>

  <p>
    <label for="md_coming_soon_note"><strong>メモ（任意・自分用）</strong></label><br>
    <textarea
      id="md_coming_soon_note"
      name="md_coming_soon_note"
      rows="3"
      style="width:100%;"><?php echo esc_textarea($note); ?></textarea>
    <small>なぜカミングスーンにしているか、TODOなどメモにどうぞ。</small>
  </p>
<?php
}

// ============================
// メタデータ保存
// ============================
function md_save_coming_soon_meta($post_id)
{

  // 自動保存時は何もしない
  if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
    return;
  }

  // nonce チェック
  if (
    ! isset($_POST['md_coming_soon_nonce_field']) ||
    ! wp_verify_nonce($_POST['md_coming_soon_nonce_field'], 'md_coming_soon_nonce')
  ) {
    return;
  }

  // 権限チェック
  if (! current_user_can('edit_post', $post_id)) {
    return;
  }

  // チェックボックス
  if (isset($_POST['md_coming_soon']) && $_POST['md_coming_soon'] === '1') {
    update_post_meta($post_id, '_md_coming_soon', '1');
  } else {
    delete_post_meta($post_id, '_md_coming_soon');
  }

  // 公開予定日時（datetime-local）
  if (! empty($_POST['md_coming_soon_until'])) {
    $raw = sanitize_text_field($_POST['md_coming_soon_until']); // 例: 2025-11-24T14:00
    update_post_meta($post_id, '_md_coming_soon_until', $raw);
  } else {
    delete_post_meta($post_id, '_md_coming_soon_until');
  }

  // メモ
  if (isset($_POST['md_coming_soon_note']) && $_POST['md_coming_soon_note'] !== '') {
    update_post_meta(
      $post_id,
      '_md_coming_soon_note',
      wp_kses_post($_POST['md_coming_soon_note'])
    );
  } else {
    delete_post_meta($post_id, '_md_coming_soon_note');
  }
}
add_action('save_post', 'md_save_coming_soon_meta');

// ============================
// カミングスーン投稿のリダイレクト
// ============================
function md_coming_soon_single_redirect()
{

  // 管理画面は対象外
  if (is_admin()) {
    return;
  }

  // すべてのシングルページを対象（投稿・固定・CPT全部）
  if (! is_singular()) {
    return;
  }


  $post_id = get_queried_object_id();
  if (! $post_id) {
    return;
  }

  // この投稿がカミングスーン指定か？
  $is_coming_soon = get_post_meta($post_id, '_md_coming_soon', true);

  if ($is_coming_soon !== '1') {
    return;
  }

  // 公開予定日時（設定されていれば、それまではカミングスーン）
  $until_raw = get_post_meta($post_id, '_md_coming_soon_until', true);
  if (! empty($until_raw)) {
    $until_ts = strtotime($until_raw);            // 例: 2025-11-24T14:00
    $now_ts   = current_time('timestamp');        // WPのタイムゾーン基準

    if ($until_ts && $now_ts >= $until_ts) {
      // 予定日時を過ぎたら、カミングスーン解除扱い
      // 自動でメタを消したい場合はコメントアウト外す
      // delete_post_meta( $post_id, '_md_coming_soon' );
      return;
    }
  }

  // 編集権限のあるログインユーザーは中身を見られる
  if (is_user_logged_in() && current_user_can('edit_post', $post_id)) {
    return;
  }

  // すでにカミングスーンページ表示中なら何もしない
  if (is_page('coming-soon')) { // スラッグが coming-soon の場合
    return;
  }

  // カミングスーン用の固定ページにリダイレクト
  // 必要に応じてスラッグを変更
  wp_redirect(home_url('/coming-soon/'), 302);
  exit;
}
add_action('template_redirect', 'md_coming_soon_single_redirect');

// ============================
// 管理画面：投稿一覧のカラム追加
// ============================
function md_coming_soon_columns($columns)
{
  $columns['md_coming_soon']       = 'CS';
  $columns['md_coming_soon_until'] = '公開予定';
  return $columns;
}
add_filter('manage_post_posts_columns', 'md_coming_soon_columns');

function md_coming_soon_columns_content($column, $post_id)
{
  if ('md_coming_soon' === $column) {
    $is_coming_soon = get_post_meta($post_id, '_md_coming_soon', true);
    if ($is_coming_soon === '1') {
      echo '✅';
    }
  }

  if ('md_coming_soon_until' === $column) {
    $until_raw = get_post_meta($post_id, '_md_coming_soon_until', true);
    if (! empty($until_raw)) {
      $ts = strtotime($until_raw);
      if ($ts) {
        echo esc_html(date_i18n('Y/m/d H:i', $ts));
      }
    }
  }
}
add_action('manage_post_posts_custom_column', 'md_coming_soon_columns_content', 10, 2);

// ============================
// 管理画面：カミングスーン絞り込みドロップダウン
// ============================
function md_coming_soon_filter_dropdown()
{
  global $typenow;

  if ($typenow !== 'post') {
    return;
  }

  $selected = isset($_GET['md_coming_soon_filter']) ? $_GET['md_coming_soon_filter'] : '';
?>
  <select name="md_coming_soon_filter">
    <option value="">カミングスーン絞り込み</option>
    <option value="1" <?php selected($selected, '1'); ?>>カミングスーンのみ</option>
    <option value="0" <?php selected($selected, '0'); ?>>通常のみ</option>
  </select>
<?php
}
add_action('restrict_manage_posts', 'md_coming_soon_filter_dropdown');

function md_coming_soon_filter_query($query)
{
  global $pagenow;

  if ($pagenow !== 'edit.php' || ! $query->is_main_query()) {
    return;
  }

  $post_type = isset($_GET['post_type']) ? $_GET['post_type'] : 'post';
  if ($post_type !== 'post') {
    return;
  }

  if (! isset($_GET['md_coming_soon_filter']) || $_GET['md_coming_soon_filter'] === '') {
    return;
  }

  $val        = $_GET['md_coming_soon_filter'];
  $meta_query = (array) $query->get('meta_query');

  if ($val === '1') {
    // カミングスーンのみ
    $meta_query[] = array(
      'key'     => '_md_coming_soon',
      'value'   => '1',
      'compare' => '=',
    );
  } elseif ($val === '0') {
    // 通常のみ
    $meta_query[] = array(
      'relation' => 'OR',
      array(
        'key'     => '_md_coming_soon',
        'compare' => 'NOT EXISTS',
      ),
      array(
        'key'     => '_md_coming_soon',
        'value'   => '1',
        'compare' => '!=',
      ),
    );
  }

  $query->set('meta_query', $meta_query);
}
add_action('pre_get_posts', 'md_coming_soon_filter_query');

// ============================
// テンプレート用：COMING SOON バッジ表示ヘルパー
// ============================
function md_the_coming_soon_badge($post_id = null)
{
  $post_id = $post_id ?: get_the_ID();
  if (! $post_id) {
    return;
  }

  $is_coming_soon = get_post_meta($post_id, '_md_coming_soon', true);
  if ($is_coming_soon !== '1') {
    return;
  }

  echo '<span class="coming-soon-badge">COMING SOON</span>';
}
