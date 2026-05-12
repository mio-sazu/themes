<?php
if ( ! defined( 'ABSPATH' ) ) exit;

// ============================
// ナレッジ（FAQ・用語）機能の読み込み
// ============================
require_once get_stylesheet_directory() . '/inc/cpt-knowledge.php';
require_once get_stylesheet_directory() . '/inc/acf-knowledge.php';
require_once get_stylesheet_directory() . '/inc/acf-column-knowledge.php';

/**
 * =====================================================
 * サクナビ 子テーマ functions.php（整理版）
 * =====================================================
 * 目的：
 * - セクションごとに役割を整理して見やすくする
 * - 既存機能をなるべく維持する
 * - 管理画面のお金コラム一覧に「コラムカテゴリ」列と絞り込みを追加
 *
 * メモ：
 * - 元ファイルで重複していた archives.css / breadcrumbs.css 読み込みは1箇所に整理
 * - 既存の処理名はなるべく維持
 */

// ============================
// 01. PVカウントとフィード作成
// ============================

// PVカウント用：メタに保存
function md_set_post_views($post_id)
{
  $count_key = 'md_post_views';
  $count     = get_post_meta($post_id, $count_key, true);

  if ($count === '') {
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

/**
 * SmartNews専用のフィードを登録
 */
function custom_smartnews_feed() {
    // add_feed( 'URLの末尾', '実行する関数名' );
    add_feed('smartnews', 'render_smartnews_xml');
}
add_action('init', 'custom_smartnews_feed');

/**
 * 実際のXML構造をロードする関数
 */
function render_smartnews_xml() {
    // テンプレートファイルの場所を指定（テーマフォルダ内に作成する）
    get_template_part('feed', 'smartnews');
}

// JSON-LD構造化
function add_json_ld_metadata() {
    // 投稿ページ、または固定ページの場合に実行
    if (is_singular()) { 
        global $post;
        
        // アイキャッチ画像のURLを取得
        $img_url = get_the_post_thumbnail_url($post->ID, 'full');
        
        // 画像がない場合の予備（サイトロゴなどがあればそのURL、なければ空）
        if (!$img_url) {
            $img_url = ""; 
        }

        $json_ld = [
            "@context" => "https://schema.org",
            "@type" => "Article",
            "headline" => get_the_title(), // 実際のタイトルを自動取得
            "image" => [ $img_url ],       // 実際の画像URLを自動取得
            "datePublished" => get_the_date('c'),
            "dateModified" => get_the_modified_date('c'),
            "author" => [
                "@type" => "Person",
                "name" => get_the_author(), // 実際の投稿者名を自動取得
                "url"  => get_author_posts_url(get_the_author_meta('ID')) // 著者ページURL
            ],
            "publisher" => [
                "@type" => "Organization",
                "name" => get_bloginfo('name'),
                "logo" => [
                    "@type" => "ImageObject",
                    "url" => "" // もしロゴがあればここにURLを入れる
                ]
            ]
        ];
        
        // 画像がある場合のみ出力（画像URLが無効だとエラーになるため）
        if (!empty($img_url)) {
            echo "\n" . '<script type="application/ld+json">' . json_encode($json_ld, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . '</script>' . "\n";
        }
    }
}
add_action('wp_head', 'add_json_ld_metadata');

// ============================
// 02. 基本設定・テーマセットアップ
// ============================

// 子テーマの翻訳ファイル読み込み
add_action('after_setup_theme', function () {
  load_child_theme_textdomain('sakunavi-child', get_stylesheet_directory() . '/languages');
});

// カスタムロゴ対応
add_action('after_setup_theme', function () {
  add_theme_support('custom-logo', [
    'height'      => 80,
    'width'       => 240,
    'flex-height' => true,
    'flex-width'  => true,
  ]);
});

// フッターの法務用メニュー位置を追加
add_action('after_setup_theme', function () {
  register_nav_menus([
    'footer-legal' => 'フッター（サイト情報/法務）',
  ]);
});

/*
 * サクナビ 独り言ボックス関連ファイル読み込み
 */





// ============================
// 03. カスタム投稿タイプ・タクソノミー
// ============================

add_action('init', function () {

  // ----------------------------
  // お金コラム
  // ----------------------------
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

  register_taxonomy('column_category', ['column'], [
    'label'        => 'コラムカテゴリ',
    'public'       => true,
    'hierarchical' => true,
    'show_in_rest' => true,
    'rewrite'      => ['slug' => 'column-category', 'with_front' => false],
  ]);

  // ----------------------------
  // ランキング
  // ----------------------------
  register_post_type('ranking', [
    'label'        => 'ランキング',
    'public'       => true,
    'has_archive'  => true,
    'rewrite'      => ['slug' => 'ranking-list', 'with_front' => false],
    'show_in_rest' => true,
    'supports'     => ['title', 'editor', 'thumbnail', 'excerpt', 'revisions', 'custom-fields'],
    'menu_icon'    => 'dashicons-awards',
  ]);

  register_taxonomy('ranking_year', ['ranking'], [
    'label'        => 'ランキング年',
    'public'       => true,
    'hierarchical' => false,
    'show_in_rest' => true,
    'rewrite'      => ['slug' => 'ranking_year', 'with_front' => false],
  ]);

  register_taxonomy('ranking_category', ['ranking'], [
    'label'        => 'ランキング種別',
    'public'       => true,
    'hierarchical' => true,
    'show_in_rest' => true,
    'rewrite'      => ['slug' => 'ranking_category', 'with_front' => false],
  ]);

  // 初回のみリライトルールをフラッシュ
  if (! get_option('sakunavi_migrated_flush_v1')) {
    flush_rewrite_rules();
    update_option('sakunavi_migrated_flush_v1', 1);
  }
}, 0);

// ランキング記事作成（子ページ用CPT）
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
    'labels'             => $labels,
    'public'             => false,
    'show_ui'            => true,
    'show_in_menu'       => false,
    'show_in_admin_bar'  => false,
    'show_in_nav_menus'  => false,
    'hierarchical'       => true,
    'supports'           => ['title', 'page-attributes'],
  ]);
});


// ============================
// 04. ショートコード
// ============================

// 固定ページ本文に [column_category_list] を入れると column_category 一覧を表示
function sakunavi_column_category_list_shortcode($atts)
{
  $terms = get_terms([
    'taxonomy'   => 'column_category',
    'hide_empty' => false,
    'orderby'    => 'name',
    'order'      => 'ASC',
  ]);

  if (is_wp_error($terms) || empty($terms)) {
    return '<p>コラムカテゴリがまだありません。</p>';
  }

  ob_start();
?>
  <section class="column-category-links">
    <h2 class="page-title">お金コラムのジャンル一覧</h2>
    <ul class="column-category-links__list">
      <?php foreach ($terms as $term) : ?>
        <li class="column-category-links__item">
          <a href="<?php echo esc_url(get_term_link($term)); ?>" class="column-category-links__link">
            <span class="column-category-links__name"><?php echo esc_html($term->name); ?></span>
            <?php if (! empty($term->description)) : ?>
              <span class="column-category-links__desc"><?php echo esc_html($term->description); ?></span>
            <?php endif; ?>
          </a>
        </li>
      <?php endforeach; ?>
    </ul>
  </section>
<?php
  return ob_get_clean();
}
add_shortcode('column_category_list', 'sakunavi_column_category_list_shortcode');

// 指定固定ページの本文を出すショートコード [page_content slug="notices"]
function sakunavi_child_render_page_content_by_path($slug)
{
  if (! $slug) return '';
  $p = get_page_by_path($slug);
  if (! $p) $p = get_page_by_path('注意事項');
  if (! $p) return '<p>（注意事項のページが見つかりません）</p>';
  return apply_filters('the_content', $p->post_content);
}

add_shortcode('page_content', function ($atts) {
  $atts = shortcode_atts(['slug' => ''], $atts);
  return sakunavi_child_render_page_content_by_path($atts['slug']);
});


// ============================
// ショートコード：コラムFAQ表示
// 本文内に [column_faq] を入れた位置にFAQを表示
// ============================
function sakunavi_column_faq_shortcode($atts)
{
  if (! is_singular('column')) {
    return '';
  }

  if (! function_exists('sakunavi_get_column_faq_items')) {
    return '';
  }

  $faq_items = sakunavi_get_column_faq_items(get_the_ID());

  if (empty($faq_items)) {
    return '';
  }

  ob_start();
?>
  <section class="faq-section faq-section--column">
    <h2>よくある質問</h2>

    <div class="faq-box">
      <?php foreach ($faq_items as $item) : ?>
        <div class="faq-item">
          <button class="faq-question" type="button" aria-expanded="false">
            <span class="faq-question__text">
              <?php echo esc_html($item['question']); ?>
            </span>
            <span class="faq-icon" aria-hidden="true">+</span>
          </button>
          <div class="faq-answer" hidden>
            <p><?php echo nl2br(esc_html($item['answer'])); ?></p>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </section>
<?php
  return ob_get_clean();
}
add_shortcode('column_faq', 'sakunavi_column_faq_shortcode');


// ============================
// 05. サイドバー・ウィジェット・メニュー
// ============================

// フッター 3カラム
add_action('widgets_init', function () {
  register_sidebar([
    'name'          => 'Footer 1：ブランド',
    'id'            => 'footer-1',
    'description'   => 'ロゴとキャッチフレーズ向け',
    'before_widget' => '<div class="footer-col footer-col--brand %2$s">',
    'after_widget'  => '</div>',
    'before_title'  => '<h2 class="widget-title">',
    'after_title'   => '</h2>',
  ]);

  register_sidebar([
    'name'          => 'Footer 2：メニュー',
    'id'            => 'footer-2',
    'description'   => 'ナビゲーションメニュー向け',
    'before_widget' => '<div class="footer-col footer-col--menu %2$s">',
    'after_widget'  => '</div>',
    'before_title'  => '<h2 class="widget-title">',
    'after_title'   => '</h2>',
  ]);

  register_sidebar([
    'name'          => 'Footer 3：会社情報',
    'id'            => 'footer-3',
    'description'   => '会社情報（住所や連絡先など）',
    'before_widget' => '<div class="footer-col footer-col--company %2$s">',
    'after_widget'  => '</div>',
    'before_title'  => '<h2 class="widget-title">',
    'after_title'   => '</h2>',
  ]);
});

// コラム用サイドバー
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


// ============================
// 06. フロント用 CSS / JS 読み込み
// ============================

// 親style + 子style + 子main.js
add_action('wp_enqueue_scripts', function () {
  $parent_handle    = 'parent-style';
  $parent_style_uri = get_template_directory_uri() . '/style.css';
  $parent_ver       = wp_get_theme(get_template())->get('Version');
  wp_enqueue_style($parent_handle, $parent_style_uri, [], $parent_ver);

  $child_ver = wp_get_theme()->get('Version');
  wp_enqueue_style('sakunavi-child-style', get_stylesheet_uri(), [$parent_handle], $child_ver);

  $parent_handle_js = 'sakunavi-main';
  if (wp_script_is($parent_handle_js, 'enqueued') || wp_script_is($parent_handle_js, 'registered')) {
    wp_dequeue_script($parent_handle_js);
    wp_deregister_script($parent_handle_js);
  }

  $child_js_file = get_stylesheet_directory() . '/assets/js/main.js';
  $child_js_uri  = get_stylesheet_directory_uri() . '/assets/js/main.js';
  $ver = file_exists($child_js_file) ? filemtime($child_js_file) : null;
  wp_enqueue_script($parent_handle_js, $child_js_uri, [], $ver, true);
}, 20);

// カードローン会社詳細ページ用 CSS
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

// FAQ用JS（コラム）
add_action('wp_enqueue_scripts', function () {
  if (is_singular('column')) {
    $path = get_stylesheet_directory() . '/assets/js/faq.js';
    wp_enqueue_script(
      'sakunavi-faq',
      get_stylesheet_directory_uri() . '/assets/js/faq.js',
      [],
      file_exists($path) ? filemtime($path) : null,
      true
    );
  }
});

// FAQ用JS（カードローン会社）
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

// コラム系ページ用 CSS / JS
// 共通レイアウト + コラム系ページ用 CSS / JS
// 共通レイアウト + コラム系ページ用 CSS / JS
if (! function_exists('sakunavi_enqueue_shared_and_column_assets')) {
  function sakunavi_enqueue_shared_and_column_assets()
  {
    $needs_support =
      is_singular('column') ||
      is_post_type_archive('column') ||
      is_tax(['column_category', 'column_persona']) ||
      is_singular('ranking') ||
      is_post_type_archive('ranking') ||
      is_tax(['ranking_year', 'ranking_category']) ||
      is_page_template('page-with-sidebar.php');

    if ($needs_support) {
      $css = get_stylesheet_directory() . '/assets/css/support.css';
      if (file_exists($css)) {
        wp_enqueue_style(
          'sakunavi-support-css',
          get_stylesheet_directory_uri() . '/assets/css/support.css',
          ['sakunavi-child-style'],
          filemtime($css)
        );
      }

      $js = get_stylesheet_directory() . '/assets/js/support.js';
      if (file_exists($js)) {
        wp_enqueue_script(
          'sakunavi-support-js',
          get_stylesheet_directory_uri() . '/assets/js/support.js',
          ['jquery'],
          filemtime($js),
          true
        );
      }
    }

    if (is_singular('column')) {
      $column_css_path = get_stylesheet_directory() . '/assets/css/column.css';
      if (file_exists($column_css_path)) {
        wp_enqueue_style(
          'sakunavi-single-column-css',
          get_stylesheet_directory_uri() . '/assets/css/column.css',
          ['sakunavi-support-css'],
          filemtime($column_css_path)
        );
      }
    }
  }
  add_action('wp_enqueue_scripts', 'sakunavi_enqueue_shared_and_column_assets', 30);
}

// ブロックエディタ用 JS
add_action('enqueue_block_editor_assets', function () {
  $js_file = get_stylesheet_directory() . '/assets/js/support.js';
  if (file_exists($js_file)) {
    wp_enqueue_script(
      'column-support-editor',
      get_stylesheet_directory_uri() . '/assets/js/support.js',
      ['wp-element', 'wp-dom-ready'],
      filemtime($js_file),
      true
    );
  }
});

// ナレッジ系ページ用 CSS / JS（コラム記事でも読み込む）
if (! function_exists('sakunavi_enqueue_knowledge_assets')) {
  function sakunavi_enqueue_knowledge_assets()
  {
    $needs = is_singular('knowledge')
      || is_post_type_archive('knowledge')
      || is_tax(['knowledge_type', 'knowledge_category', 'knowledge_intent', 'knowledge_level'])
      || is_singular('column')
      || is_front_page();

    if ($needs) {
      $css = get_stylesheet_directory() . '/assets/css/knowledge.css';
      if (file_exists($css)) {
        wp_enqueue_style(
          'sakunavi-knowledge-css',
          get_stylesheet_directory_uri() . '/assets/css/knowledge.css',
          ['sakunavi-child-style'],
          filemtime($css)
        );
      }
      // アコーディオン（knowledge-faq.js）
      $js = get_stylesheet_directory() . '/assets/js/knowledge-faq.js';
      if (file_exists($js)) {
        wp_enqueue_script(
          'sakunavi-knowledge-faq',
          get_stylesheet_directory_uri() . '/assets/js/knowledge-faq.js',
          [],
          filemtime($js),
          true
        );
      }
    }
  }
  add_action('wp_enqueue_scripts', 'sakunavi_enqueue_knowledge_assets', 30);
}

// ランキング系ページ用 CSS / JS
if (! function_exists('sakunavi_enqueue_ranking_assets')) {
  function sakunavi_enqueue_ranking_assets()
  {
    if (is_singular('ranking') || is_post_type_archive('ranking') || is_tax(['ranking_year', 'ranking_category'])) {
      $css = get_stylesheet_directory() . '/assets/css/ranking.css';
      if (file_exists($css)) {
        wp_enqueue_style(
          'sakunavi-ranking-css',
          get_stylesheet_directory_uri() . '/assets/css/ranking.css',
          ['sakunavi-child-style'],
          filemtime($css)
        );
      }

      $js = get_stylesheet_directory() . '/assets/js/ranking.js';
      if (file_exists($js)) {
        wp_enqueue_script(
          'sakunavi-ranking-js',
          get_stylesheet_directory_uri() . '/assets/js/ranking.js',
          ['jquery'],
          filemtime($js),
          true
        );
      }
    }
  }
  add_action('wp_enqueue_scripts', 'sakunavi_enqueue_ranking_assets', 30);
}

// フロントページ専用 CSS
add_action('wp_enqueue_scripts', function () {
  if (! is_front_page()) return;
  $path = get_stylesheet_directory() . '/assets/css/front-page.css';
  if (file_exists($path)) {
    wp_enqueue_style(
      'sakunavi-front-page-css',
      get_stylesheet_directory_uri() . '/assets/css/front-page.css',
      ['sakunavi-child-style'],
      filemtime($path)
    );
  }
}, 32);

// Archives / Breadcrumbs は最後に読み込む
if (! function_exists('sakunavi_enqueue_archives_and_breadcrumbs')) {
  function sakunavi_enqueue_archives_and_breadcrumbs()
  {
    $base = 'sakunavi-child-style';

    $is_archive_like =
      is_post_type_archive('column') || is_tax(['column_category', 'column_persona']) ||
      is_post_type_archive('ranking') || is_tax(['ranking_year', 'ranking_category']);

    $arch = get_stylesheet_directory() . '/assets/css/archives.css';
    if ($is_archive_like && file_exists($arch)) {
      wp_enqueue_style(
        'sakunavi-archives-css',
        get_stylesheet_directory_uri() . '/assets/css/archives.css',
        [$base],
        filemtime($arch)
      );
    }

    $bc = get_stylesheet_directory() . '/assets/css/breadcrumbs.css';
    if (file_exists($bc)) {
      wp_enqueue_style(
        'sakunavi-breadcrumbs-css',
        get_stylesheet_directory_uri() . '/assets/css/breadcrumbs.css',
        [$base],
        filemtime($bc)
      );
    }
  }
  add_action('wp_enqueue_scripts', 'sakunavi_enqueue_archives_and_breadcrumbs', 40);
}


// ============================
// 07. ACF フィールド定義
// ============================

// カードローン会社情報用 ACF
add_action('acf/init', function () {
  if (! function_exists('acf_add_local_field_group')) return;

  acf_add_local_field_group([
    'key'             => 'group_card_company',
    'title'           => '会社情報',
    'position'        => 'acf_after_title',
    'label_placement' => 'top',
    'location'        => [[['param' => 'post_type', 'operator' => '==', 'value' => 'card_loan_company']]],
    'fields'          => [
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

// Ranking用 共通フィールド定義
function sakunavi_ranking_fields_base()
{
  $fields = [
    ['key' => 'field_lead', 'label' => '概要（短いリード文）', 'name' => 'lead', 'type' => 'textarea', 'rows' => 3, 'instructions' => 'タイトル直下に表示される短い説明文（1〜2文程度）。'],
    [
      'key'          => 'field_page_overview',
      'label'        => 'ページ概要セクション',
      'name'         => 'page_overview',
      'type'         => 'wysiwyg',
      'toolbar'      => 'basic',
      'media_upload' => 0,
      'instructions' => 'タイトル下に表示される詳しい説明エリアです。このランキングの目的・選定基準・読者へのメッセージなどを入力してください。',
    ],
  ];

  for ($i = 1; $i <= 10; $i++) {
    $idx = str_pad($i, 2, '0', STR_PAD_LEFT);

    $fields[] = [
      'key'     => "field_r{$idx}_sep",
      'label'   => "— ランク {$i} —",
      'name'    => '',
      'type'    => 'message',
      'message' => "<strong>Rank {$i}</strong>",
    ];

    $fields[] = ['key' => "field_r{$idx}_title",     'label' => "{$i}位 名称",            'name' => "rank_{$i}_title",     'type' => 'text'];
    $fields[] = ['key' => "field_r{$idx}_logo",      'label' => "{$i}位 ロゴ",            'name' => "rank_{$i}_logo",      'type' => 'image', 'return_format' => 'array', 'preview_size' => 'medium'];
    $fields[] = ['key' => "field_r{$idx}_one",       'label' => "{$i}位 一言ポイント",    'name' => "rank_{$i}_one",       'type' => 'text'];
    $fields[] = ['key' => "field_r{$idx}_overall",   'label' => "{$i}位 総合評価(0-5)",   'name' => "rank_{$i}_overall",   'type' => 'number', 'min' => 0, 'max' => 5, 'step' => 0.5];
    $fields[] = ['key' => "field_r{$idx}_rate",      'label' => "{$i}位 金利",            'name' => "rank_{$i}_rate",      'type' => 'text'];
    $fields[] = ['key' => "field_r{$idx}_speed",     'label' => "{$i}位 スピード",        'name' => "rank_{$i}_speed",     'type' => 'text'];
    $fields[] = ['key' => "field_r{$idx}_ease",      'label' => "{$i}位 使いやすさ",      'name' => "rank_{$i}_ease",      'type' => 'text'];
    $fields[] = ['key' => "field_r{$idx}_reason",    'label' => "{$i}位 ここがポイント",  'name' => "rank_{$i}_reason",    'type' => 'wysiwyg'];
    $fields[] = ['key' => "field_r{$idx}_cta_label", 'label' => "{$i}位 CTAラベル",       'name' => "rank_{$i}_cta_label", 'type' => 'text', 'default_value' => '申し込む'];
    $fields[] = ['key' => "field_r{$idx}_cta_url",   'label' => "{$i}位 CTA URL",         'name' => "rank_{$i}_cta_url",   'type' => 'url'];

    for ($r = 1; $r <= 2; $r++) {
      $fields[] = ['key' => "field_r{$idx}_rev{$r}_persona", 'label' => "{$i}位 口コミ{$r} ペルソナ", 'name' => "rank_{$i}_review{$r}_persona", 'type' => 'text'];
      $fields[] = ['key' => "field_r{$idx}_rev{$r}_stars",   'label' => "{$i}位 口コミ{$r} 星(1-5)",   'name' => "rank_{$i}_review{$r}_stars",   'type' => 'number', 'min' => 1, 'max' => 5, 'step' => 1];
      $fields[] = ['key' => "field_r{$idx}_rev{$r}_text",    'label' => "{$i}位 口コミ{$r} 本文",       'name' => "rank_{$i}_review{$r}_text",    'type' => 'textarea', 'rows' => 3];
      $fields[] = ['key' => "field_r{$idx}_rev{$r}_avatar",  'label' => "{$i}位 口コミ{$r} アイコン",   'name' => "rank_{$i}_review{$r}_avatar",  'type' => 'image', 'return_format' => 'array', 'preview_size' => 'thumbnail'];
    }
  }

  // ────────────────────────────────────────────────────────────
  // ランキング後コンテンツ（ランキングエリアの下に表示）
  // ────────────────────────────────────────────────────────────
  $fields[] = [
    'key'     => 'field_sep_post_ranking',
    'label'   => '━ ランキング後のコンテンツ ━',
    'name'    => '',
    'type'    => 'message',
    'message' => '<p>以下のセクションはランキングカードの<strong>下</strong>に順番に表示されます。</p><ul><li>「解決できること」→ まとめ → 関連コラム → 関連キーワード の順です</li><li>入力しなかったセクションは表示されません</li></ul>',
  ];

  $fields[] = [
    'key'          => 'field_result_title',
    'label'        => '「解決できること」見出し',
    'name'         => 'result_title',
    'type'         => 'text',
    'default_value' => 'このランキングで解決できること',
    'instructions' => '空欄の場合「このランキングで解決できること」と表示されます。',
  ];
  $fields[] = [
    'key'          => 'field_result_body',
    'label'        => '「解決できること」本文',
    'name'         => 'result_body',
    'type'         => 'wysiwyg',
    'toolbar'      => 'basic',
    'media_upload' => 0,
    'instructions' => 'このランキングを読むことでユーザーが解決できる悩みや得られる情報を入力してください。',
  ];

  $fields[] = [
    'key'          => 'field_summary_title',
    'label'        => 'まとめ 見出し',
    'name'         => 'summary_title',
    'type'         => 'text',
    'default_value' => 'まとめ',
    'instructions' => '空欄の場合「まとめ」と表示されます。',
  ];
  $fields[] = [
    'key'          => 'field_summary_body',
    'label'        => 'まとめ 本文',
    'name'         => 'summary_body',
    'type'         => 'wysiwyg',
    'toolbar'      => 'basic',
    'media_upload' => 0,
    'instructions' => 'ランキング全体のまとめ・総評を入力してください。読者へのアドバイスや次のアクションを添えると効果的です。',
  ];

  $fields[] = [
    'key'           => 'field_related_columns',
    'label'         => '関連コラム',
    'name'          => 'related_columns',
    'type'          => 'relationship',
    'post_type'     => ['column'],
    'filters'       => ['search', 'taxonomy'],
    'taxonomy'      => [],
    'max'           => 6,
    'return_format' => 'id',
    'instructions'  => '関連するお金コラムを最大6件まで選択できます。検索ボックスでキーワード検索してから選択してください。',
  ];

  $fields[] = [
    'key'          => 'field_related_keywords',
    'label'        => '関連キーワード',
    'name'         => 'related_keywords',
    'type'         => 'textarea',
    'rows'         => 2,
    'instructions' => 'カンマ（,）区切りで入力してください。例：カードローン, 即日融資, 審査なし, 学生向け',
  ];

  return $fields;
}

add_action('acf/include_fields', function () {
  if (! function_exists('acf_add_local_field_group')) return;

  acf_add_local_field_group([
    'key'      => 'group_ranking_page_simple',
    'title'    => 'Ranking Page',
    'fields'   => sakunavi_ranking_fields_base(),
    'location' => [[['param' => 'post_type', 'operator' => '==', 'value' => 'ranking']]],
    'position' => 'normal',
    'style'    => 'default',
  ]);

  $variant_fields = array_merge(
    [
      ['key' => 'field_variant_enable', 'label' => 'このパターンを有効にする', 'name' => 'variant_enable', 'type' => 'true_false', 'ui' => 1, 'default_value' => 1],
      ['key' => 'field_variant_weight', 'label' => '配信比率(weight)', 'name' => 'weight', 'type' => 'number', 'min' => 1, 'step' => 1, 'default_value' => 10],
    ],
    sakunavi_ranking_fields_base()
  );

  acf_add_local_field_group([
    'key'      => 'group_ranking_variant_simple',
    'title'    => 'Ranking Variant',
    'fields'   => $variant_fields,
    'location' => [[['param' => 'post_type', 'operator' => '==', 'value' => 'ranking_variant']]],
    'position' => 'normal',
    'style'    => 'default',
  ]);
});



// ============================
// ACF：SEO設定
// 投稿・固定ページ・カスタム投稿で
// SEOタイトル / キーワード / ディスクリプションを設定
// ============================
add_action('acf/init', function () {
  if (! function_exists('acf_add_local_field_group')) {
    return;
  }

  acf_add_local_field_group([
    'key' => 'group_sakunavi_seo_settings',
    'title' => 'SEO設定',
    'fields' => [
      [
        'key' => 'field_sakunavi_seo_title',
        'label' => 'SEOタイトル',
        'name' => 'seo_title',
        'type' => 'text',
        'instructions' => '未入力の場合は「記事タイトル｜サクッとお金ナビ（サクナビ）」を自動で使用します。',
      ],
      [
        'key' => 'field_sakunavi_seo_keywords',
        'label' => 'SEOキーワード',
        'name' => 'seo_keywords',
        'type' => 'text',
        'instructions' => '例：カードローン, 即日融資, 学生向け',
      ],
      [
        'key' => 'field_sakunavi_seo_description',
        'label' => 'SEOディスクリプション',
        'name' => 'seo_description',
        'type' => 'textarea',
        'rows' => 4,
        'new_lines' => '',
        'instructions' => '検索結果向けの要約文です。ページ内容に即した固有の説明を書いてください。',
      ],
      [
        'key'           => 'field_sakunavi_noindex',
        'label'         => 'noindex（検索エンジンに表示しない）',
        'name'          => 'seo_noindex',
        'type'          => 'true_false',
        'ui'            => 1,
        'ui_on_text'    => 'noindex ON',
        'ui_off_text'   => 'インデックスする',
        'default_value' => 0,
        'instructions'  => 'ONにするとGoogleなどの検索結果にこのページが表示されなくなります。スライダーバナー・比較用の補助ページなど、検索流入が不要なページに使います。',
      ],
    ],
    'location' => [
      [['param' => 'post_type', 'operator' => '==', 'value' => 'post']],
      [['param' => 'post_type', 'operator' => '==', 'value' => 'page']],
      [['param' => 'post_type', 'operator' => '==', 'value' => 'column']],
      [['param' => 'post_type', 'operator' => '==', 'value' => 'card_loan_company']],
      [['param' => 'post_type', 'operator' => '==', 'value' => 'ranking']],
      [['param' => 'post_type', 'operator' => '==', 'value' => 'slider_banner']],
    ],
    'position' => 'normal',
    'style' => 'default',
    'active' => true,
  ]);
});

// ============================
// ACF：コラム記事 FAQ設定
// column 投稿に FAQ を最大5件まで設定
// ============================
add_action('acf/init', function () {
  if (! function_exists('acf_add_local_field_group')) {
    return;
  }

  acf_add_local_field_group([
    'key' => 'group_sakunavi_column_faq',
    'title' => 'コラムFAQ設定',
    'fields' => [
      [
        'key' => 'field_sakunavi_faq_note',
        'label' => '使い方',
        'name' => '',
        'type' => 'message',
        'message' => 'ここに入力したFAQは記事内または記事下に表示され、FAQ構造化データにも使われます。本文内の表示したい位置にショートコード <code>[column_faq]</code> を入れると、その位置にFAQが表示されます。ショートコードがない場合は記事下に自動表示されます。FAQ未入力時は表示されません。',
      ],

      [
        'key' => 'field_sakunavi_faq_1_q',
        'label' => 'FAQ1 質問',
        'name' => 'faq_1_question',
        'type' => 'text',
      ],
      [
        'key' => 'field_sakunavi_faq_1_a',
        'label' => 'FAQ1 回答',
        'name' => 'faq_1_answer',
        'type' => 'textarea',
        'rows' => 4,
      ],

      [
        'key' => 'field_sakunavi_faq_2_q',
        'label' => 'FAQ2 質問',
        'name' => 'faq_2_question',
        'type' => 'text',
      ],
      [
        'key' => 'field_sakunavi_faq_2_a',
        'label' => 'FAQ2 回答',
        'name' => 'faq_2_answer',
        'type' => 'textarea',
        'rows' => 4,
      ],

      [
        'key' => 'field_sakunavi_faq_3_q',
        'label' => 'FAQ3 質問',
        'name' => 'faq_3_question',
        'type' => 'text',
      ],
      [
        'key' => 'field_sakunavi_faq_3_a',
        'label' => 'FAQ3 回答',
        'name' => 'faq_3_answer',
        'type' => 'textarea',
        'rows' => 4,
      ],

      [
        'key' => 'field_sakunavi_faq_4_q',
        'label' => 'FAQ4 質問',
        'name' => 'faq_4_question',
        'type' => 'text',
      ],
      [
        'key' => 'field_sakunavi_faq_4_a',
        'label' => 'FAQ4 回答',
        'name' => 'faq_4_answer',
        'type' => 'textarea',
        'rows' => 4,
      ],

      [
        'key' => 'field_sakunavi_faq_5_q',
        'label' => 'FAQ5 質問',
        'name' => 'faq_5_question',
        'type' => 'text',
      ],
      [
        'key' => 'field_sakunavi_faq_5_a',
        'label' => 'FAQ5 回答',
        'name' => 'faq_5_answer',
        'type' => 'textarea',
        'rows' => 4,
      ],
    ],
    'location' => [
      [
        [
          'param' => 'post_type',
          'operator' => '==',
          'value' => 'column',
        ],
      ],
    ],
    'position' => 'normal',
    'style' => 'default',
    'active' => true,
  ]);
});



// ============================
// 08. SEO / meta / title
// ============================

// ============================
// SEO：title タグ制御
// ============================
function sakunavi_filter_document_title($title)
{
  if (is_admin()) {
    return $title;
  }

  $site_brand = 'サクッとお金ナビ（サクナビ）';

  // 単一ページ（投稿・固定ページ・カスタム投稿）
  if (is_singular()) {
    $post_id = get_queried_object_id();

    $seo_title = function_exists('get_field') ? get_field('seo_title', $post_id) : '';

    if (! empty($seo_title)) {
      return $seo_title;
    }

    return get_the_title($post_id) . '｜' . $site_brand;
  }

  // コラムカテゴリ一覧
  if (is_tax('column_category')) {
    $term = get_queried_object();
    if ($term && ! is_wp_error($term)) {
      return $term->name . '｜' . $site_brand;
    }
  }

  // コラム一覧
  if (is_post_type_archive('column')) {
    return 'お金コラム一覧｜' . $site_brand;
  }

  // カードローン会社一覧
  if (is_post_type_archive('card_loan_company')) {
    return 'カードローン会社一覧｜' . $site_brand;
  }

  // ランキング一覧
  if (is_post_type_archive('ranking')) {
    return 'ランキング一覧｜' . $site_brand;
  }

  // トップページ
  if (is_front_page() || is_home()) {
    return $site_brand;
  }

  return $title . '｜' . $site_brand;
}
add_filter('pre_get_document_title', 'sakunavi_filter_document_title');

// ============================
// SEO：meta description / keywords 出力
// ============================
function sakunavi_output_meta_tags()
{
  if (is_admin()) {
    return;
  }

  $description = '';
  $keywords    = '';

  // ----------------------------
  // 単一ページ
  // ----------------------------
  if (is_singular()) {
    $post_id = get_queried_object_id();

    $description = function_exists('get_field') ? get_field('seo_description', $post_id) : '';
    $keywords    = function_exists('get_field') ? get_field('seo_keywords', $post_id) : '';

    // description 未入力時は本文から自動生成
    if (empty($description)) {
      $description = wp_strip_all_tags(get_the_excerpt($post_id));

      if (empty($description)) {
        $post = get_post($post_id);
        if ($post) {
          $description = wp_strip_all_tags(strip_shortcodes($post->post_content));
        }
      }

      $description = preg_replace('/\s+/', ' ', $description);
      $description = trim($description);
      $description = mb_substr($description, 0, 120);
    }
  }

  // ----------------------------
  // コラムカテゴリ一覧
  // ----------------------------
  elseif (is_tax('column_category')) {
    $term = get_queried_object();

    if ($term && ! is_wp_error($term)) {
      $description = ! empty($term->description)
        ? wp_strip_all_tags($term->description)
        : $term->name . 'に関するお金コラム一覧ページです。';
    }
  }

  // ----------------------------
  // アーカイブ
  // ----------------------------
  elseif (is_post_type_archive('column')) {
    $description = 'お金やカードローンに関する基礎知識、選び方、比較ポイントをわかりやすく解説しています。';
  } elseif (is_post_type_archive('card_loan_company')) {
    $description = 'カードローン会社の特徴や金利、限度額、選び方を比較しやすくまとめています。';
  } elseif (is_post_type_archive('ranking')) {
    $description = '目的別・条件別にカードローンを比較しやすいランキング一覧ページです。';
  }

  if (! empty($description)) {
    echo '<meta name="description" content="' . esc_attr($description) . '">' . "\n";
  }

  if (! empty($keywords)) {
    echo '<meta name="keywords" content="' . esc_attr($keywords) . '">' . "\n";
  }

  // noindex 出力（ACF seo_noindex フィールドが ON のとき）
  if (is_singular() && function_exists('get_field')) {
    $noindex = get_field('seo_noindex', get_queried_object_id());
    if ($noindex) {
      echo '<meta name="robots" content="noindex, follow">' . "\n";
    }
  }
}
add_action('wp_head', 'sakunavi_output_meta_tags', 1);


// ============================
// SEO：Article 構造化データ出力
// column記事ページ用
// ============================
function sakunavi_output_article_structured_data()
{
  if (is_admin() || ! is_singular('column')) {
    return;
  }

  $post_id = get_queried_object_id();

  if (! $post_id) {
    return;
  }

  $headline = get_the_title($post_id);
  $url      = get_permalink($post_id);
  $image    = get_the_post_thumbnail_url($post_id, 'full');

  $data = [
    '@context' => 'https://schema.org',
    '@type'    => 'Article',
    'headline' => wp_strip_all_tags($headline),
    'mainEntityOfPage' => [
      '@type' => 'WebPage',
      '@id'   => $url,
    ],
    'datePublished' => get_the_date('c', $post_id),
    'dateModified'  => get_the_modified_date('c', $post_id),
    'author' => [
      '@type' => 'Organization',
      'name'  => 'サクナビ編集部',
    ],
    'publisher' => [
      '@type' => 'Organization',
      'name'  => 'サクッとお金ナビ（サクナビ）',
    ],
  ];

  if ($image) {
    $data['image'] = [$image];
  }

  $site_logo_url = '';
  $custom_logo_id = get_theme_mod('custom_logo');
  if ($custom_logo_id) {
    $site_logo_url = wp_get_attachment_image_url($custom_logo_id, 'full');
  }

  if ($site_logo_url) {
    $data['publisher']['logo'] = [
      '@type' => 'ImageObject',
      'url'   => $site_logo_url,
    ];
  }

  echo '<script type="application/ld+json">' . wp_json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . '</script>' . "\n";
}
add_action('wp_head', 'sakunavi_output_article_structured_data', 20);

// ============================
// SEO：FAQ 構造化データ出力
// column記事ページでFAQがある場合のみ
// ============================
function sakunavi_output_faq_structured_data()
{
  if (is_admin() || ! is_singular('column')) {
    return;
  }

  if (! function_exists('sakunavi_get_column_faq_items')) {
    return;
  }

  $faq_items = sakunavi_get_column_faq_items(get_queried_object_id());

  if (empty($faq_items)) {
    return;
  }

  $entities = [];

  foreach ($faq_items as $item) {
    $entities[] = [
      '@type' => 'Question',
      'name'  => wp_strip_all_tags($item['question']),
      'acceptedAnswer' => [
        '@type' => 'Answer',
        'text'  => wp_strip_all_tags($item['answer']),
      ],
    ];
  }

  $data = [
    '@context'   => 'https://schema.org',
    '@type'      => 'FAQPage',
    'mainEntity' => $entities,
  ];

  echo '<script type="application/ld+json">' . wp_json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . '</script>' . "\n";
}
add_action('wp_head', 'sakunavi_output_faq_structured_data', 21);


// ============================
// 09. 表示用ヘルパー関数
// ============================

// カードローン会社の金利表示 helper
function sakunavi_the_rate_range($post_id = null)
{
  $post_id = $post_id ?: get_the_ID();
  if (! $post_id) return;

  $rate_min = get_field('rate_min', $post_id);
  $rate_max = get_field('rate_max', $post_id);

  if ($rate_min === '' && $rate_max === '') {
    return;
  }

  $show_min = $rate_min !== '' && $rate_min !== null ? sprintf('%.1f', (float) $rate_min) : '';
  $show_max = $rate_max !== '' && $rate_max !== null ? sprintf('%.1f', (float) $rate_max) : '';

  if ($show_min !== '' && $show_max !== '') {
    echo esc_html($show_min . '〜' . $show_max) . '%';
  } elseif ($show_min !== '') {
    echo esc_html($show_min) . '%';
  } elseif ($show_max !== '') {
    echo esc_html($show_max) . '%';
  }
}

// 融資限度額の表示 helper
function sakunavi_the_limit_range($post_id = null)
{
  $post_id = $post_id ?: get_the_ID();
  if (! $post_id) return;

  $limit_min = get_field('limit_amount_min', $post_id);
  $limit_max = get_field('limit_amount_max', $post_id);

  if ($limit_min === '' && $limit_max === '') {
    return;
  }

  $show_min = ($limit_min !== '' && $limit_min !== null) ? number_format((float) $limit_min) : '';
  $show_max = ($limit_max !== '' && $limit_max !== null) ? number_format((float) $limit_max) : '';

  if ($show_min !== '' && $show_max !== '') {
    echo esc_html($show_min . '万円 ～ ' . $show_max . '万円');
  } elseif ($show_min !== '') {
    echo esc_html($show_min . '万円');
  } elseif ($show_max !== '') {
    echo esc_html('～ ' . $show_max . '万円');
  }
}

// ============================
// コラムFAQ取得ヘルパー
// ACFで入力したFAQを配列で返す
// ============================
function sakunavi_get_column_faq_items($post_id = null)
{
  $post_id = $post_id ?: get_the_ID();

  if (! $post_id || ! function_exists('get_field')) {
    return [];
  }

  $items = [];

  for ($i = 1; $i <= 5; $i++) {
    $question = trim((string) get_field("faq_{$i}_question", $post_id));
    $answer   = trim((string) get_field("faq_{$i}_answer", $post_id));

    if ($question !== '' && $answer !== '') {
      $items[] = [
        'question' => $question,
        'answer'   => $answer,
      ];
    }
  }

  return $items;
}

// ============================
// 10. 管理画面カスタマイズ
// ============================

// ランキング子ページUI
add_action('admin_menu', function () {
  add_submenu_page(
    'edit.php?post_type=ranking',
    'ランキング記事作成',
    'ランキング記事作成',
    'edit_posts',
    'edit.php?post_type=ranking_variant'
  );
}, 999);

// コラム・ランキング一覧にスラッグ列を追加
function add_slug_column_to_cpt_list($columns)
{
  $new_columns = [];
  foreach ($columns as $key => $title) {
    $new_columns[$key] = $title;
    if ($key === 'title') {
      $new_columns['slug'] = 'スラッグ';
    }
  }
  if (! isset($new_columns['slug'])) {
    $new_columns['slug'] = 'スラッグ';
  }
  return $new_columns;
}
add_filter('manage_column_posts_columns', 'add_slug_column_to_cpt_list');
add_filter('manage_ranking_posts_columns', 'add_slug_column_to_cpt_list');

function display_cpt_slug_column($column_name, $post_id)
{
  if ($column_name === 'slug') {
    $post = get_post($post_id);
    echo esc_html($post->post_name);
  }
}
add_action('manage_column_posts_custom_column', 'display_cpt_slug_column', 10, 2);
add_action('manage_ranking_posts_custom_column', 'display_cpt_slug_column', 10, 2);

// 管理画面：お金コラム一覧にカテゴリ列を追加
function sakunavi_add_column_category_admin_column($columns)
{
  $new_columns = [];

  foreach ($columns as $key => $value) {
    $new_columns[$key] = $value;

    if ($key === 'title') {
      $new_columns['column_category_terms'] = 'コラムカテゴリ';
    }
  }

  return $new_columns;
}
add_filter('manage_column_posts_columns', 'sakunavi_add_column_category_admin_column');

// 追加したカテゴリ列の中身を表示
function sakunavi_show_column_category_admin_column($column, $post_id)
{
  if ($column !== 'column_category_terms') {
    return;
  }

  $terms = get_the_terms($post_id, 'column_category');

  if (empty($terms) || is_wp_error($terms)) {
    echo '—';
    return;
  }

  $links = [];

  foreach ($terms as $term) {
    $url = admin_url('edit.php?post_type=column&column_category=' . $term->slug);
    $links[] = '<a href="' . esc_url($url) . '">' . esc_html($term->name) . '</a>';
  }

  echo implode(' / ', $links);
}
add_action('manage_column_posts_custom_column', 'sakunavi_show_column_category_admin_column', 10, 2);

// 管理画面：お金コラム一覧にカテゴリ絞り込みを追加
function sakunavi_add_column_category_filter()
{
  global $typenow;

  if ($typenow !== 'column') {
    return;
  }

  $taxonomy = 'column_category';
  $selected = isset($_GET[$taxonomy]) ? $_GET[$taxonomy] : '';

  wp_dropdown_categories([
    'show_option_all' => 'すべてのコラムカテゴリ',
    'taxonomy'        => $taxonomy,
    'name'            => $taxonomy,
    'orderby'         => 'name',
    'selected'        => $selected,
    'show_count'      => true,
    'hide_empty'      => false,
    'value_field'     => 'slug',
  ]);
}
add_action('restrict_manage_posts', 'sakunavi_add_column_category_filter');

/**
 * 管理画面の一覧に「更新日」カラムを追加
 */

/* ----------------------------
 * 1) カラム追加
 * ---------------------------- */
function md_add_modified_date_column($columns)
{
  $new_columns = array();

  foreach ($columns as $key => $value) {
    $new_columns[$key] = $value;

    // 「日付」の後ろに「更新日」を追加
    if ($key === 'date') {
      $new_columns['modified_date'] = '更新日';
    }
  }

  // 万が一「date」カラムが無い場合の保険
  if (!isset($new_columns['modified_date'])) {
    $new_columns['modified_date'] = '更新日';
  }

  return $new_columns;
}
add_filter('manage_posts_columns', 'md_add_modified_date_column');
add_filter('manage_pages_columns', 'md_add_modified_date_column');


/* ----------------------------
 * 2) 更新日の中身を表示
 * ---------------------------- */
function md_show_modified_date_column($column, $post_id)
{
  if ($column === 'modified_date') {
    $modified = get_the_modified_time('Y/m/d H:i', $post_id);
    $published = get_the_time('Y/m/d H:i', $post_id);

    if ($modified && $modified !== $published) {
      echo esc_html($modified);
    } else {
      echo '—';
    }
  }
}
add_action('manage_posts_custom_column', 'md_show_modified_date_column', 10, 2);
add_action('manage_pages_custom_column', 'md_show_modified_date_column', 10, 2);


/* ----------------------------
 * 3) ソート可能にする
 * ---------------------------- */
function md_sortable_modified_date_column($columns)
{
  $columns['modified_date'] = 'modified';
  return $columns;
}
add_filter('manage_edit-post_sortable_columns', 'md_sortable_modified_date_column');
add_filter('manage_edit-page_sortable_columns', 'md_sortable_modified_date_column');


// ============================
// 11. クエリ調整・パーマリンク
// ============================

// card_company アーカイブ / loan_genre タクソノミーの並び調整
add_action('pre_get_posts', function ($q) {
  if (is_admin() || ! $q->is_main_query()) return;

  if ($q->is_post_type_archive('card_company') || $q->is_tax('loan_genre')) {
    $q->set('meta_key', 'daily_rank');
    $q->set('orderby', 'meta_value_num');
    $q->set('order', 'ASC');
    if (! $q->get('posts_per_page')) {
      $q->set('posts_per_page', 12);
    }
  }
});

// column / ranking の一覧・タクソノミーを固定
add_action('pre_get_posts', function ($q) {
  if (is_admin() || ! $q->is_main_query()) return;

  if ($q->is_tax('column_category')) {
    $q->set('post_type', 'column');
    $q->set('posts_per_page', 10);
    $q->set('orderby', 'date');
    $q->set('order', 'DESC');
  }

  if ($q->is_post_type_archive('column')) {
    $q->set('posts_per_page', 10);
    $q->set('orderby', 'date');
    $q->set('order', 'DESC');
  }

  if ($q->is_post_type_archive('ranking') || $q->is_tax('ranking_year') || $q->is_tax('ranking_category')) {
    if ($q->is_tax('ranking_year') || $q->is_tax('ranking_category')) {
      $q->set('post_type', 'ranking');
    }
    $q->set('posts_per_page', 10);
  }
});

// コラムのパーマリンクを /column/YYYY/MM/DD/slug/ 形式に変更
function sakunavi_column_permalink_structure($link, $post)
{
  if ($post->post_type === 'column' && get_option('permalink_structure')) {
    $post_date = strtotime($post->post_date);
    $year  = date('Y', $post_date);
    $month = date('m', $post_date);
    $day   = date('d', $post_date);

    $link = str_replace('%year%', $year, $link);
    $link = str_replace('%monthnum%', $month, $link);
    $link = str_replace('%day%', $day, $link);
  }
  return $link;
}
add_filter('post_type_link', 'sakunavi_column_permalink_structure', 10, 2);

// コラムのスラッグを自動で投稿IDに設定
function sakunavi_set_column_slug_to_id($post_id, $post, $update)
{
  if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
  if (wp_is_post_revision($post_id)) return;
  if ($post->post_status === 'auto-draft') return;

  if ($post->post_type === 'column') {
    if ($post->post_name == (string) $post_id) return;

    remove_action('save_post_column', 'sakunavi_set_column_slug_to_id', 10, 3);

    wp_update_post([
      'ID'        => $post_id,
      'post_name' => (string) $post_id,
    ]);

    add_action('save_post_column', 'sakunavi_set_column_slug_to_id', 10, 3);
  }
}
add_action('save_post_column', 'sakunavi_set_column_slug_to_id', 10, 3);

// ============================
// 管理画面：お金コラム一覧にSEO入力状況列を追加
// ============================
function sakunavi_add_column_seo_status_admin_column($columns)
{
  $new_columns = [];

  foreach ($columns as $key => $value) {
    $new_columns[$key] = $value;

    if ($key === 'column_category_terms') {
      $new_columns['seo_status'] = 'SEO設定';
    }
  }

  return $new_columns;
}
add_filter('manage_column_posts_columns', 'sakunavi_add_column_seo_status_admin_column');

function sakunavi_show_column_seo_status_admin_column($column, $post_id)
{
  if ($column !== 'seo_status') {
    return;
  }

  $seo_title       = function_exists('get_field') ? get_field('seo_title', $post_id) : '';
  $seo_description = function_exists('get_field') ? get_field('seo_description', $post_id) : '';
  $seo_keywords    = function_exists('get_field') ? get_field('seo_keywords', $post_id) : '';

  $items = [];

  $items[] = ! empty($seo_title) ? 'タイトル○' : 'タイトル-';
  $items[] = ! empty($seo_description) ? '説明○' : '説明-';
  $items[] = ! empty($seo_keywords) ? 'KW○' : 'KW-';

  echo esc_html(implode(' / ', $items));
}
add_action('manage_column_posts_custom_column', 'sakunavi_show_column_seo_status_admin_column', 10, 2);


// ============================
// 12. テンプレート制御
// ============================

// taxonomy テンプレートを子テーマに固定
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

// archive テンプレートを子テーマに固定
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

  if (is_post_type_archive('knowledge')) {
    $child = $path . '/archive-knowledge.php';
    if (file_exists($child)) return $child;
  }

  return $template;
}, 99);

// single テンプレートを子テーマに固定
add_filter('single_template', function ($template) {
  global $post;
  if (! $post) return $template;

  $path = get_stylesheet_directory();

  if ($post->post_type === 'ranking') {
    $child_template = $path . '/single-ranking.php';
    if (file_exists($child_template)) {
      return $child_template;
    }
  }

  if ($post->post_type === 'column') {
    $child_template = $path . '/single-column.php';
    if (file_exists($child_template)) {
      return $child_template;
    }
  }

  if ($post->post_type === 'knowledge') {
    $child_template = $path . '/single-knowledge.php';
    if (file_exists($child_template)) {
      return $child_template;
    }
  }

  return $template;
}, 99);

// taxonomy テンプレート（knowledge）も子テーマに固定
add_filter('taxonomy_template', function ($template) {
  $path = get_stylesheet_directory();

  foreach (['knowledge_type', 'knowledge_category', 'knowledge_intent', 'knowledge_level'] as $tax) {
    if (is_tax($tax)) {
      $child = $path . '/archive-knowledge.php';
      if (file_exists($child)) return $child;
    }
  }

  return $template;
}, 100);

// テンプレートのデバッグコメント（管理者のみ）
add_action('wp_footer', function () {
  if (current_user_can('manage_options')) {
    global $template;
    echo "\n<!-- Current Template: " . esc_html(basename($template)) . " -->\n";
  }
}, 999);


// ============================
// 13. カミングスーン機能
// ============================

// メタボックス追加
function md_add_coming_soon_metabox()
{
  $screens = get_post_types([
    'public'  => true,
    'show_ui' => true,
  ], 'names');

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
  $until          = get_post_meta($post->ID, '_md_coming_soon_until', true);
  $note           = get_post_meta($post->ID, '_md_coming_soon_note', true);

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
    <input type="datetime-local" id="md_coming_soon_until" name="md_coming_soon_until" value="<?php echo esc_attr($until_value); ?>" style="width:100%;" />
    <small>この日時まではカミングスーン扱いになります（未設定なら手動で解除）。</small>
  </p>

  <p>
    <label for="md_coming_soon_note"><strong>メモ（任意・自分用）</strong></label><br>
    <textarea id="md_coming_soon_note" name="md_coming_soon_note" rows="3" style="width:100%;"><?php echo esc_textarea($note); ?></textarea>
    <small>なぜカミングスーンにしているか、TODOなどメモにどうぞ。</small>
  </p>
<?php
}

// メタ保存
function md_save_coming_soon_meta($post_id)
{
  if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
    return;
  }

  if (! isset($_POST['md_coming_soon_nonce_field']) || ! wp_verify_nonce($_POST['md_coming_soon_nonce_field'], 'md_coming_soon_nonce')) {
    return;
  }

  if (! current_user_can('edit_post', $post_id)) {
    return;
  }

  if (isset($_POST['md_coming_soon']) && $_POST['md_coming_soon'] === '1') {
    update_post_meta($post_id, '_md_coming_soon', '1');
  } else {
    delete_post_meta($post_id, '_md_coming_soon');
  }

  if (! empty($_POST['md_coming_soon_until'])) {
    $raw = sanitize_text_field($_POST['md_coming_soon_until']);
    update_post_meta($post_id, '_md_coming_soon_until', $raw);
  } else {
    delete_post_meta($post_id, '_md_coming_soon_until');
  }

  if (isset($_POST['md_coming_soon_note']) && $_POST['md_coming_soon_note'] !== '') {
    update_post_meta($post_id, '_md_coming_soon_note', wp_kses_post($_POST['md_coming_soon_note']));
  } else {
    delete_post_meta($post_id, '_md_coming_soon_note');
  }
}
add_action('save_post', 'md_save_coming_soon_meta');

// カミングスーン投稿のリダイレクト
function md_coming_soon_single_redirect()
{
  if (is_admin() || ! is_singular()) {
    return;
  }

  $post_id = get_queried_object_id();
  if (! $post_id) {
    return;
  }

  $is_coming_soon = get_post_meta($post_id, '_md_coming_soon', true);
  if ($is_coming_soon !== '1') {
    return;
  }

  $until_raw = get_post_meta($post_id, '_md_coming_soon_until', true);
  if (! empty($until_raw)) {
    $until_ts = strtotime($until_raw);
    $now_ts   = current_time('timestamp');

    if ($until_ts && $now_ts >= $until_ts) {
      return;
    }
  }

  if (is_user_logged_in() && current_user_can('edit_post', $post_id)) {
    return;
  }

  if (is_page('coming-soon')) {
    return;
  }

  wp_redirect(home_url('/coming-soon/'), 302);
  exit;
}
add_action('template_redirect', 'md_coming_soon_single_redirect');

// 投稿一覧にカミングスーン列を追加（通常投稿）
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

// カミングスーン絞り込み（通常投稿）
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
    $meta_query[] = [
      'key'     => '_md_coming_soon',
      'value'   => '1',
      'compare' => '=',
    ];
  } elseif ($val === '0') {
    $meta_query[] = [
      'relation' => 'OR',
      [
        'key'     => '_md_coming_soon',
        'compare' => 'NOT EXISTS',
      ],
      [
        'key'     => '_md_coming_soon',
        'value'   => '1',
        'compare' => '!=',
      ],
    ];
  }

  $query->set('meta_query', $meta_query);
}
add_action('pre_get_posts', 'md_coming_soon_filter_query');

// テンプレート用：COMING SOON バッジ表示ヘルパー
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

// ============================
// FAQ一覧ページ（page-knowledge-faq.php）用 CSS / JS
// ============================
add_action('wp_enqueue_scripts', function () {
  if (! is_page_template('page-knowledge-faq.php')) return;

  // knowledge.css（アコーディオン・カードスタイル）
  $css = get_stylesheet_directory() . '/assets/css/knowledge.css';
  if (file_exists($css) && ! wp_style_is('sakunavi-knowledge-css', 'enqueued')) {
    wp_enqueue_style(
      'sakunavi-knowledge-css',
      get_stylesheet_directory_uri() . '/assets/css/knowledge.css',
      ['sakunavi-child-style'],
      filemtime($css)
    );
  }

  // knowledge-faq.js（アコーディオン開閉 + キーワード検索）
  $js = get_stylesheet_directory() . '/assets/js/knowledge-faq.js';
  if (file_exists($js)) {
    wp_enqueue_script(
      'sakunavi-knowledge-faq',
      get_stylesheet_directory_uri() . '/assets/js/knowledge-faq.js',
      [],
      filemtime($js),
      true
    );
  }
}, 31);

/**
 * 親テーマの競合CSSを停止
 */
add_action('wp_enqueue_scripts', function () {

  // まずはよくありそうなハンドル名を止める
  $targets = [
    'sakunavi-support',
    'support',
    'parent-support',
    'theme-support',

    'sakunavi-column',
    'column',
    'parent-column',
    'theme-column',

    'sakunavi-loan-guide',
    'loan-guide',
    'parent-loan-guide',

    // 必要なら style も候補
    // 'sakunavi-style',
    // 'parent-style',
  ];

  foreach ($targets as $handle) {
    wp_dequeue_style($handle);
    wp_deregister_style($handle);
  }
}, 999);
