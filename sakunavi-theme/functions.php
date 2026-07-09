<?php
// テーマセットアップ
function sakunavi_setup()
{
  // メニュー登録
  register_nav_menus([
    'primary' => 'グローバルナビ',
    'footer'  => 'フッターメニュー',
  ]);
}
add_action('after_setup_theme', 'sakunavi_setup');


// 1. Walker_Nav_Primary を定義
class Walker_Nav_Primary extends Walker_Nav_Menu
{
  // サブメニュー開始タグ
  public function start_lvl(&$output, $depth = 0, $args = [])
  {
    $indent = str_repeat("\t", $depth);
    $output .= "\n{$indent}<ul class=\"sub-menu\">\n";
  }

  // サブメニュー終了タグ
  public function end_lvl(&$output, $depth = 0, $args = [])
  {
    $indent = str_repeat("\t", $depth);
    $output .= "{$indent}</ul>\n";
  }

  // 各メニューアイテム開始タグ
  public function start_el(&$output, $item, $depth = 0, $args = [], $id = 0)
  {
    $indent = ($depth) ? str_repeat("\t", $depth) : '';
    $classes = empty($item->classes) ? [] : (array) $item->classes;
    $class_names = join(' ', array_filter($classes));
    $output .= "{$indent}<li class=\"menu-item {$class_names}\">";
    $output .= '<a href="' . esc_url($item->url) . '">'
      . esc_html($item->title)
      . '</a>';
  }

  // 各メニューアイテム終了タグ
  public function end_el(&$output, $item, $depth = 0, $args = [])
  {
    $output .= "</li>\n";
  }
}


// CSS / JS の読み込み
function sakunavi_scripts()
{
  $theme_uri = get_template_directory_uri();

  // 共通CSS
  wp_enqueue_style(
    'sakunavi-reset',
    $theme_uri . '/assets/css/reset.css',
    [],
    '1.0'
  );

  wp_enqueue_style(
    'sakunavi-style',
    $theme_uri . '/assets/css/style.css',
    ['sakunavi-reset'],
    '1.0'
  );

  // トップ以外の補助CSS
  if (!is_front_page()) {
    wp_enqueue_style(
      'sakunavi-support',
      $theme_uri . '/assets/css/support.css',
      ['sakunavi-style'],
      '1.0'
    );
  }

  // お金コラム系
  if (
    is_page_template('page-money-column.php') ||
    is_tax('money_column')
  ) {
    wp_enqueue_style(
      'sakunavi-column',
      $theme_uri . '/assets/css/column.css',
      ['sakunavi-style'],
      '1.0'
    );
  }

  // 記事一覧ページ
  if (is_page_template('page-post-list.php')) {
    wp_enqueue_style(
      'sakunavi-post-archive',
      $theme_uri . '/assets/css/post-archive.css',
      ['sakunavi-style'],
      '1.0'
    );
  }

  // カードローン会社一覧・詳細
  if (
    is_post_type_archive('card_loan_company') ||
    is_singular('card_loan_company')
  ) {
    wp_enqueue_style(
      'sakunavi-company',
      $theme_uri . '/assets/css/company.css',
      ['sakunavi-style'],
      '1.0'
    );
  }

  // ガイド系・カードローン記事系
  if (
    is_page_template('page-condition.php') ||
    is_page_template('page-purpose.php') ||
    is_page_template('page-cardloan-list.php') ||
    is_singular('cardloan') ||
    is_post_type_archive('cardloan') ||
    is_tax(['purpose', 'condition'])
  ) {
    wp_enqueue_style(
      'sakunavi-loan-guide',
      $theme_uri . '/assets/css/loan-guide.css',
      ['sakunavi-style'],
      '1.0'
    );
  }

  // テーマ直下の style.css は1回だけ
  wp_enqueue_style(
    'theme-style',
    get_stylesheet_uri(),
    ['sakunavi-style'],
    '1.0'
  );

  // JS
  wp_enqueue_script(
    'sakunavi-main',
    $theme_uri . '/assets/js/main.js',
    ['jquery'],
    '1.0',
    true
  );
}
add_action('wp_enqueue_scripts', 'sakunavi_scripts');






// 適度なサムネサイズがなければ用意（任意）
add_action('after_setup_theme', function () {
  add_image_size('card-m', 640, 360, true);
});


// スライダーカスタム投稿タイプ（任意）
function sakunavi_cpt_slider()
{
  register_post_type('slider_banner', [
    'labels' => [
      'name' => 'スライダーバナー',
      'singular_name' => 'バナー'
    ],
    'public' => true,
    'has_archive' => false,
    'show_in_rest' => true,
    'supports' => ['title', 'thumbnail', 'page-attributes'],
    'menu_icon'   => 'dashicons-images-alt2',
  ]);
}
add_action('init', 'sakunavi_cpt_slider');

// テーマ全体でアイキャッチを使う宣言
add_theme_support('post-thumbnails');

// + ---メニュー--- +

// header nav ドロップダウンの設定
class Walker_Nav_Taxonomy_Posts extends Walker_Nav_Menu
{
  // 親<li> に子ありクラスを付与しつつ出力
  public function start_el(&$output, $item, $depth = 0, $args = [], $id = 0)
  {
    // taxonomy リンクなら .menu-item-has-children を追加
    if ($depth === 0 && $item->object === 'taxonomy') {
      $item->classes[] = 'menu-item-has-children';
    }

    // まずは通常の<li><a>…を出力
    parent::start_el($output, $item, $depth, $args, $id);

    // taxonomy の場合だけ sub-menu を自前で出力
    if ($depth === 0 && $item->object === 'taxonomy') {
      // 開始タグ
      $output .= "\n<ul class=\"sub-menu\">\n";

      $term = get_term($item->object_id, $item->object);
      $posts = get_posts([
        'post_type'      => 'cardloan',
        'tax_query'      => [[
          'taxonomy' => $term->taxonomy,
          'field'    => 'term_id',
          'terms'    => $term->term_id,
        ]],
        'posts_per_page' => 5,
      ]);

      foreach ($posts as $p) {
        $output .= '<li class="menu-item-sub">';
        $output .= '<a href="' . get_permalink($p->ID) . '">'
          . esc_html(get_the_title($p->ID))
          . '</a>';
        $output .= "</li>\n";
      }

      // 終了タグ
      $output .= "</ul>\n";
    }
  }

  // 親の end_el で </li> を閉じてもらう
  public function end_el(&$output, $item, $depth = 0, $args = [])
  {
    parent::end_el($output, $item, $depth, $args);
  }

  // start_lvl / end_lvl は不要なのでオミットしてOK
}

add_action('init', function () {
  register_post_type('cardloan', [
    'labels' => [
      'name'                  => 'カードローン記事',
      'singular_name'         => 'カードローン記事',
      'menu_name'             => 'カードローン記事',
      'name_admin_bar'        => 'カードローン記事',
      'add_new'               => '新規追加',
      'add_new_item'          => '新規カードローン記事を追加',
      'new_item'              => '新規カードローン記事',
      'edit_item'             => 'カードローン記事を編集',
      'view_item'             => 'カードローン記事を表示',
      'all_items'             => 'すべてのカードローン記事',
      'search_items'          => 'カードローン記事を検索',
      'not_found'             => 'カードローン記事が見つかりません',
      'not_found_in_trash'    => 'ゴミ箱にカードローン記事はありません',
    ],
    'public'       => true,
    'has_archive'  => true,
    'menu_icon'    => 'dashicons-money-alt',
    'supports'     => ['title', 'editor', 'thumbnail', 'excerpt', 'page-attributes'],
    'taxonomies'   => ['money_column', 'purpose', 'condition'],
    'rewrite'      => ['slug' => 'cardloan', 'with_front' => false],
    'show_in_rest' => true,
  ]);

  // 「お金コラム」タクソノミー
  register_taxonomy('money_column', 'cardloan', [
    'labels'            => [
      'name'              => 'お金コラム',
      'singular_name'     => 'お金コラム',
      'search_items'      => 'お金コラムを検索',
      'all_items'         => 'すべてのお金コラム',
      'edit_item'         => 'お金コラムを編集',
      'update_item'       => 'お金コラムを更新',
      'add_new_item'      => '新規お金コラムを追加',
      'new_item_name'     => '新規お金コラム名',
    ],
    'hierarchical'      => true,
    'show_in_rest'      => true,
    'show_admin_column' => true,
    'rewrite'           => ['slug' => 'okane-column', 'with_front' => false],
  ]);

  // 「目的別ランキング」タクソノミー
  register_taxonomy('purpose', 'cardloan', [
    'labels'            => [
      'name'              => '目的別ランキング',
      'singular_name'     => '目的別ランキング',
      'search_items'      => '目的別ランキングを検索',
      'all_items'         => 'すべての目的別ランキング',
      'edit_item'         => '目的別ランキングを編集',
      'update_item'       => '目的別ランキングを更新',
      'add_new_item'      => '新規目的別ランキングを追加',
      'new_item_name'     => '新規ランキング名',
    ],
    'hierarchical'      => true,
    'show_in_rest'      => true,
    'show_admin_column' => true,
    'rewrite'           => ['slug' => 'purpose', 'with_front' => false],
  ]);

  // 「条件」タクソノミー
  register_taxonomy('condition', 'cardloan', [
    'labels'            => [
      'name'              => '条件',
      'singular_name'     => '条件',
      'search_items'      => '条件を検索',
      'all_items'         => 'すべての条件',
      'edit_item'         => '条件を編集',
      'update_item'       => '条件を更新',
      'add_new_item'      => '新規条件を追加',
      'new_item_name'     => '新規条件名',
    ],
    'hierarchical'      => true,
    'show_in_rest'      => true,
    'show_admin_column' => true,
    'rewrite'           => ['slug' => 'condition', 'with_front' => false],
  ]);
});


// カスタム投稿タイプ: card_loan_company を登録
function register_cardloan_company_cpt()
{
  $labels = [
    'name'                  => 'カードローン会社',
    'singular_name'         => 'カードローン会社',
    'menu_name'             => 'カードローン会社',
    'name_admin_bar'        => 'カードローン会社',
    'add_new'               => '新規追加',
    'add_new_item'          => 'カードローン会社を追加',
    'new_item'              => '新規カードローン会社',
    'edit_item'             => 'カードローン会社を編集',
    'view_item'             => 'カードローン会社を表示',
    'all_items'             => 'すべてのカードローン会社',
    'search_items'          => 'カードローン会社を検索',
    'not_found'             => 'カードローン会社が見つかりません',
    'not_found_in_trash'    => 'ゴミ箱にカードローン会社はありません',
  ];
  $args = [
    'label'                 => 'card_loan_company',
    'labels'                => $labels,
    'public'                => true,
    'has_archive'           => true,
    'show_in_rest'          => true,
    'menu_icon'             => 'dashicons-money',
    'supports'              => ['title', 'editor', 'thumbnail', 'excerpt', 'page-attributes'],
    'taxonomies'            => ['purpose', 'condition'],
    'rewrite'               => ['slug' => 'card-loan-company', 'with_front' => false],
  ];
  register_post_type('card_loan_company', $args);
}

add_action('init', 'register_cardloan_company_cpt');

// sideber用
function sakunavi_widgets_init()
{
  register_sidebar([
    'name'          => 'サイドバー (メイン)',
    'id'            => 'sidebar-main',
    'before_widget' => '<div class="sidebar-box %2$s">',
    'after_widget'  => '</div>',
    'before_title'  => '<h3>',
    'after_title'   => '</h3>',
  ]);
}
add_action('widgets_init', 'sakunavi_widgets_init');

// テーマサポートにナビメニュー機能を追加
add_action('after_setup_theme', function () {
  register_nav_menus([
    'header-menu'      => 'ヘッダーメニュー',     // メインナビ用
    'mobile-bottom'    => 'モバイル底部ナビ',     // スマホ下部固定メニュー用
    'footer-menu-1'    => 'フッター左',          // フッターの各カラム用
    'footer-menu-2'    => 'フッター中',
    'footer-menu-3'    => 'フッター右',
  ]);
});

// カスタム投稿タイプ「タブ コンテンツ」を登録
function my_register_tab_content_cpt()
{
  $labels = [
    'name'               => 'タブ コンテンツ',
    'singular_name'      => 'タブ コンテンツ',
    'add_new'            => '新規追加',
    'add_new_item'       => 'タブ コンテンツを追加',
    'edit_item'          => 'タブ コンテンツを編集',
    'new_item'           => '新しいタブ コンテンツ',
    'view_item'          => 'タブ コンテンツを表示',
    'search_items'       => 'タブ コンテンツを検索',
    'not_found'          => 'タブ コンテンツは見つかりませんでした',
    'not_found_in_trash' => 'ゴミ箱にタブ コンテンツはありません',
    'menu_name'          => 'タブ コンテンツ',
  ];
  $args = [
    'labels'       => $labels,
    'public'       => false,
    'show_ui'      => true,
    'show_in_menu' => true,
    'menu_icon'    => 'dashicons-editor-ul',
    'supports'     => ['title', 'editor', 'page-attributes'],
  ];
  register_post_type('tab_content', $args);
}
add_action('init', 'my_register_tab_content_cpt');

// タクソノミー「タブカテゴリ」を登録
function my_register_tab_category_taxonomy()
{
  $labels = [
    'name'          => 'タブカテゴリ',
    'singular_name' => 'タブカテゴリ',
    'menu_name'     => 'タブカテゴリ',
    'all_items'     => 'すべてのタブカテゴリ',
    'edit_item'     => 'タブカテゴリを編集',
    'add_new_item'  => 'タブカテゴリを追加',
  ];
  register_taxonomy('tab_category', 'tab_content', [
    'labels'       => $labels,
    'hierarchical' => false,
    'show_ui'      => true,
  ]);
}
add_action('init', 'my_register_tab_category_taxonomy');

// LINE風カスタム投稿
add_action('init', 'chat_register_message_cpt');
function chat_register_message_cpt()
{
  register_post_type('message', [
    'labels'             => [
      'name'          => 'Messages',
      'singular_name' => 'Message',
    ],
    'public'             => false,
    'show_ui'            => false,
    'has_archive'        => false,
    'show_in_rest'       => false,   // 独自エンドポイントで扱うので REST はオフ
    'supports'           => ['editor'],
  ]);
}
// LINE風カスタム投稿
add_action('rest_api_init', 'chat_register_endpoints');
function chat_register_endpoints()
{
  //　LINE風カスタム投稿 ■ GET /wp-json/chat/v1/messages?after=YYYY-MM-DDTHH:MM:SS
  register_rest_route('chat/v1', '/messages', [
    'methods'             => 'GET',
    'callback'            => 'chat_get_messages',
    'permission_callback' => '__return_true',
    'args'                => [
      'after'  => [
        'validate_callback' => function ($v) {
          return !$v || (bool) strtotime($v);
        },
      ],
    ],
  ]);

  //　LINE風カスタム投稿 ■ POST /wp-json/chat/v1/messages
  register_rest_route('chat/v1', '/messages', [
    'methods'             => 'POST',
    'callback'            => 'chat_create_message',
    // ログインユーザーのみ許可
    'permission_callback' => function () {
      return is_user_logged_in();
    },
    'args'                => [
      'content' => ['required' => true],
    ],
  ]);
}

//　LINE風カスタム投稿 GET のコールバック
function chat_get_messages(WP_REST_Request $req)
{
  $after = $req->get_param('after');
  $args = [
    'post_type'      => 'message',
    'posts_per_page' => -1,
    'orderby'        => 'date',
    'order'          => 'ASC',
  ];
  if ($after) {
    $args['date_query'] = [['after' => $after]];
  }
  $posts = get_posts($args);
  $data = array_map(function ($p) {
    return [
      'id'      => $p->ID,
      'content' => $p->post_content,
      'author'  => get_the_author_meta('display_name', $p->post_author),
      'date'    => get_gmt_from_date($p->post_date, 'c'),
    ];
  }, $posts);
  return rest_ensure_response($data);
}

//　LINE風カスタム投稿 POST のコールバック
function chat_create_message(WP_REST_Request $req)
{
  $user    = wp_get_current_user();
  $content = sanitize_textarea_field($req->get_param('content'));
  $id = wp_insert_post([
    'post_type'    => 'message',
    'post_status'  => 'publish',
    'post_content' => $content,
    'post_author'  => $user->ID,
  ]);
  if (is_wp_error($id)) {
    return new WP_Error('chat_insert_failed', 'メッセージの保存に失敗しました', ['status' => 500]);
  }
  return rest_ensure_response([
    'id'      => $id,
    'content' => $content,
    'author'  => $user->display_name,
    'date'    => get_gmt_from_date(get_post($id)->post_date, 'c'),
  ]);
}

// LINE風カスタム投稿
add_action('wp_enqueue_scripts', 'chat_enqueue_scripts');
function chat_enqueue_scripts()
{
  if ( ! is_front_page() ) {
    return;
  }
  wp_enqueue_script(
    'chat-js',
    get_template_directory_uri() . '/assets/js/chat.js',
    [],
    null,
    true
  );
  wp_localize_script('chat-js', 'chatSettings', [
    'restRoot'    => esc_url_raw(rest_url('chat/v1/')),
    'nonce'       => wp_create_nonce('wp_rest'),
    'currentUser' => is_user_logged_in()
      ? wp_get_current_user()->display_name
      : '',
  ]);
}


// ============================
// 返済シミュレーション
// ============================

// card_loan_company の中から、シミュレーターに出す会社データを組み立てる
// （金利・無利息日数・限度額・注釈は会社ページの既存ACFフィールドをそのまま再利用）
function sakunavi_simulator_companies()
{
  $query = new WP_Query([
    'post_type'      => 'card_loan_company',
    'post_status'    => 'publish',
    'posts_per_page' => -1,
    'orderby'        => ['menu_order' => 'ASC', 'title' => 'ASC'],
    'meta_query'     => [
      [
        'key'     => 'sim_show',
        'value'   => '1',
        'compare' => '=',
      ],
    ],
  ]);

  $companies = [];
  foreach ($query->posts as $post) {
    $id = $post->ID;

    $rate_min  = function_exists('get_field') ? get_field('rate_min', $id) : get_post_meta($id, 'rate_min', true);
    $rate_max  = function_exists('get_field') ? get_field('rate_max', $id) : get_post_meta($id, 'rate_max', true);
    $free_days = function_exists('get_field') ? get_field('no_interest_days', $id) : get_post_meta($id, 'no_interest_days', true);
    $free_note = function_exists('get_field') ? get_field('no_interest_note_text', $id) : get_post_meta($id, 'no_interest_note_text', true);
    $limit_min = function_exists('get_field') ? get_field('limit_amount_min', $id) : get_post_meta($id, 'limit_amount_min', true);
    $limit_max = function_exists('get_field') ? get_field('limit_amount_max', $id) : get_post_meta($id, 'limit_amount_max', true);
    $cta_url   = function_exists('get_field') ? get_field('cta_url', $id) : get_post_meta($id, 'cta_url', true);
    $featured  = function_exists('get_field') ? (bool) get_field('sim_featured', $id) : false;

    $rate_min  = ($rate_min !== '' && $rate_min !== null) ? floatval($rate_min) : null;
    $rate_max  = ($rate_max !== '' && $rate_max !== null) ? floatval($rate_max) : null;
    $free_days = ($free_days !== '' && $free_days !== null) ? intval($free_days) : 0;

    // 実質年率の上限が未入力の会社は返済計算ができないため一覧から除外
    if ($rate_max === null) continue;

    $companies[] = [
      'id'       => (string) $id,
      'name'     => get_the_title($id),
      'minRate'  => $rate_min !== null ? $rate_min : $rate_max,
      'maxRate'  => $rate_max,
      'freeDays' => $free_days,
      'freeNote' => $free_note ? wp_strip_all_tags($free_note) : '無利息期間の適用には各社所定の条件があります。詳細は公式サイトでご確認ください。',
      'limit'    => sakunavi_simulator_limit_label($limit_min, $limit_max),
      'featured' => $featured,
      'ctaUrl'   => $cta_url ? esc_url_raw($cta_url) : get_permalink($id),
    ];
  }

  return $companies;
}

// 融資限度額の表示ラベルを組み立てる（シミュレーター専用の簡易フォーマッタ）
function sakunavi_simulator_limit_label($min, $max)
{
  $min = ($min !== '' && $min !== null) ? floatval($min) : null;
  $max = ($max !== '' && $max !== null) ? floatval($max) : null;

  if ($min !== null && $max !== null) {
    return number_format($min) . '万円〜' . number_format($max) . '万円';
  }
  if ($max !== null) {
    return '最大' . number_format($max) . '万円';
  }
  if ($min !== null) {
    return number_format($min) . '万円〜';
  }
  return '要確認';
}

add_action('wp_enqueue_scripts', 'simulator_enqueue_assets');
function simulator_enqueue_assets()
{
  $js_path  = get_template_directory() . '/assets/js/simulator.js';
  $css_path = get_template_directory() . '/assets/css/simulator.css';

  // 計算用JS
  wp_register_script(
    'simulator-js',
    get_template_directory_uri() . '/assets/js/simulator.js',
    [], // 依存なし
    file_exists($js_path) ? filemtime($js_path) : null,
    true
  );
  wp_register_style(
    'simulator-css',
    get_template_directory_uri() . '/assets/css/simulator.css',
    [],
    file_exists($css_path) ? filemtime($css_path) : null
  );
  // デザイン用フォント（ショートコードが呼ばれたページのみ読み込む）
  wp_register_style(
    'simulator-fonts',
    'https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Work+Sans:wght@400;500;600;700&family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20..48,100..700,0..1,0..200&display=swap',
    [],
    null
  );
}

add_shortcode('repayment_simulator', 'simulator_shortcode');
function simulator_shortcode($atts)
{
  // ショートコードが呼ばれたら資産をキューに積む
  wp_enqueue_style('simulator-fonts');
  wp_enqueue_style('simulator-css');
  wp_enqueue_script('simulator-js');

  $companies = sakunavi_simulator_companies();

  wp_localize_script('simulator-js', 'snvSimulatorData', [
    'companies' => $companies,
  ]);

  // ページ下部の注釈（貸金業法上の表示義務事項・免責事項など、管理画面で編集）
  $notice = '';
  if (function_exists('get_field')) {
    $page_id = get_queried_object_id() ?: get_the_ID();
    $notice  = get_field('simulator_notice', $page_id);
  }

  ob_start();
?>
  <div id="snv">
    <div style="display:flex; flex-wrap:wrap; align-items:flex-end; justify-content:space-between; gap:16px; margin-bottom:20px;">
      <div>
        <div style="display:flex; align-items:center; gap:8px; margin-bottom:8px;">
          <span style="font-size:12px; font-weight:700; letter-spacing:0.08em; color:#39B167; background:rgba(57,177,103,0.1); padding:4px 10px; border-radius:999px;">返済シミュレーター</span>
        </div>
        <h1 id="snv-company" style="font-weight:800; font-size:34px; line-height:1.15; letter-spacing:-0.02em; margin:0 0 6px;">カードローン返済シミュレーション</h1>
        <p style="margin:0; color:#5b6459; font-size:15px;">申し込む前に、あなたの返済プランを正確に見積もりましょう。</p>
      </div>
      <div style="display:flex; gap:8px;">
        <span id="snv-maxrate" style="display:none; background:#fff; border:1px solid #E5E9EF; color:#1e7a42; padding:8px 14px; border-radius:999px; font-size:13px; font-weight:700;"></span>
        <span id="snv-freedays" style="display:none; background:#EDFAF2; border:1px solid #C5E8D0; color:#1a5e34; padding:8px 14px; border-radius:999px; font-size:13px; font-weight:700;"></span>
      </div>
    </div>

    <details class="snv-noprint" style="background:#fff; border:1px solid #E5E9EF; border-radius:16px; margin-bottom:24px; padding:0 20px;">
      <summary style="cursor:pointer; padding:16px 0; font-weight:700; font-size:14px; color:#39B167; display:flex; align-items:center; gap:8px;"><span class="msr" style="font-size:20px;">help</span>使い方（初めての方はこちら）</summary>
      <div style="padding:0 0 20px; font-size:13px; line-height:1.9; color:#5b6459;">
        <ul style="margin:0; padding:0; list-style:none; display:flex; flex-direction:column; gap:8px;">
          <li><b style="color:#3f4a3e;">① 会社を選ぶ</b>：気になるカードローン会社をタップすると、その会社の金利・無利息期間が自動で入力されます。会社を決めていない場合は「会社を選ばずに入力」から自分で数値を入力できます。</li>
          <li><b style="color:#3f4a3e;">② 条件を入力する</b>：借入希望額・毎月の返済希望額・借入利率を入力してください。</li>
          <li><b style="color:#3f4a3e;">③ 結果を確認する</b>：総返済額・利息・返済期間が自動で計算されます。返済額を増やした場合の節約額や、無利息期間のメリットもあわせて確認できます。</li>
          <li><b style="color:#3f4a3e;">④ 保存・お申し込み</b>：結果はPDF・CSV・LINEなどで保存・共有でき、納得できたら公式サイトからお申し込みいただけます。</li>
        </ul>
      </div>
    </details>

    <div id="snv-picker" style="margin-bottom:24px;">
      <div style="display:flex; align-items:center; gap:8px; margin-bottom:12px; flex-wrap:wrap;">
        <span class="msr" style="color:#39B167; font-size:20px;">account_balance</span>
        <span style="font-size:14px; font-weight:700; color:#3f4a3e;">カードローン会社を選択</span>
        <span style="font-size:12px; color:#8a9187;">→ 金利・無利息期間が自動でセットされます</span>
      </div>
      <div id="snv-companies"></div>
    </div>

    <div id="snv-main"><!-- 動的に描画 --></div>

    <?php if ($notice) : ?>
      <div class="snv-noprint" style="margin-top:24px; background:#fff; border:1px solid #E5E9EF; border-radius:16px; padding:20px 24px; font-size:12px; line-height:1.9; color:#6b6b60;">
        <?php echo wp_kses_post($notice); ?>
      </div>
    <?php endif; ?>

    <section class="snv-noprint" style="margin-top:40px; background:linear-gradient(135deg,#39B167,#1e7a42); border-radius:28px; padding:56px 32px; text-align:center; color:#fff; box-shadow:0 12px 40px rgba(30,122,66,0.25);">
      <h2 class="snv-h-white" style="font-weight:800; font-size:28px; margin:0 0 16px;">シミュレーション結果はいかがでしたか？</h2>
      <p style="margin:0 auto 32px; max-width:520px; font-size:16px; line-height:1.7; opacity:.92;">返済計画が固まったら、公式サイトからお申し込みいただけます。審査は最短20分、当日中のお借入れも可能です。</p>
      <div style="display:flex; flex-wrap:wrap; gap:14px; justify-content:center;">
        <a id="snv-cta-apply" href="#" target="_blank" rel="noopener sponsored" style="background:#fff; color:#1e7a42; padding:18px 36px; border-radius:14px; font-weight:800; font-size:17px; text-decoration:none; box-shadow:0 6px 18px rgba(0,0,0,0.18); display:inline-flex; align-items:center; gap:8px;">公式サイトでお申し込み<span class="msr" style="font-size:20px;">open_in_new</span></a>
        <a href="<?php echo esc_url(get_post_type_archive_link('card_loan_company') ?: home_url('/')); ?>" style="border:1.5px solid rgba(255,255,255,0.6); color:#fff; padding:18px 32px; border-radius:14px; font-weight:700; font-size:16px; text-decoration:none;">他のプランを見る</a>
      </div>
      <p style="margin:28px 0 0; font-size:12px; opacity:.7; font-style:italic;">※お申し込みには審査があります。結果によりご希望に沿わない場合があります。</p>
    </section>
  </div>
<?php
  return ob_get_clean();
}

// ============================
// 返済シミュレーション（簡易版・トップページ用）
// ============================

// 本格版（返済シミュレーターページ）のURLを取得
function sakunavi_simulator_page_url()
{
  $pages = get_posts([
    'post_type'      => 'page',
    'posts_per_page' => 1,
    'post_status'    => 'publish',
    'fields'         => 'ids',
    'meta_key'       => '_wp_page_template',
    'meta_value'     => 'template-parts/page-simulator.php',
  ]);
  return $pages ? get_permalink($pages[0]) : home_url('/');
}

add_action('wp_enqueue_scripts', 'simulator_mini_enqueue_assets');
function simulator_mini_enqueue_assets()
{
  $js_path = get_template_directory() . '/assets/js/simulator-mini.js';
  wp_register_script(
    'simulator-mini-js',
    get_template_directory_uri() . '/assets/js/simulator-mini.js',
    [],
    file_exists($js_path) ? filemtime($js_path) : null,
    true
  );
}

add_shortcode('repayment_simulator_mini', 'simulator_mini_shortcode');
function simulator_mini_shortcode($atts)
{
  wp_enqueue_script('simulator-mini-js');

  $simulator_url = sakunavi_simulator_page_url();

  ob_start();
?>
  <style>
    #snvm-card { position: relative; width: 100%; box-sizing: border-box; background: linear-gradient(165deg,#ffffff,#f3faf6); border: 1px solid #dde9e2; border-radius: 24px; padding: 28px 26px; box-shadow: 0 10px 32px rgba(30,122,66,0.10); overflow: hidden; }
    #snvm-card::before { content:""; position:absolute; top:-60px; right:-60px; width:160px; height:160px; border-radius:50%; background:radial-gradient(circle, rgba(57,177,103,0.14), rgba(57,177,103,0) 70%); }
    #snvm-card .snvm-input:focus { border-color:#39B167 !important; box-shadow:0 0 0 3px rgba(57,177,103,0.15); }
    #snvm-card .snvm-input::placeholder { color:#a3ada6; font-weight:500; }
    #snvm-cta:hover { transform: translateY(-1px); box-shadow: 0 10px 24px rgba(30,122,66,0.30) !important; }
    #snvm-card input[type=number]::-webkit-inner-spin-button { -webkit-appearance:none; margin:0; }
    #snvm-grid { display:grid; grid-template-columns: 1fr; gap: 22px; }
    #snvm-outputs { display:flex; flex-direction:column; }
    @media (min-width: 680px) {
      #snvm-grid { grid-template-columns: 1fr 1fr; align-items:stretch; }
      #snvm-outputs { justify-content:center; }
    }
  </style>
  <div id="snvm" style="width:100%;">
  <div id="snvm-card">
    <div style="margin-bottom:20px;">
      <p style="margin:0; font-weight:800; font-size:18px; color:#20291f; letter-spacing:-0.01em;">返済シミュレーション</p>
      <p style="margin:4px 0 0; font-size:12px; color:#8a9187;">3つ入力するだけで、今すぐ目安が分かります</p>
    </div>

    <div id="snvm-grid">
      <div style="display:flex; flex-direction:column; gap:12px;">
        <div>
          <label style="display:block; font-size:12px; font-weight:700; color:#5b6459; margin-bottom:5px;">借入希望額</label>
          <div style="position:relative;">
            <input id="snvm-principal" class="snvm-input" type="text" inputmode="numeric" pattern="[0-9]*" placeholder="借入希望額を入力" style="width:100%; box-sizing:border-box; border:1.5px solid #dde9e2; border-radius:12px; padding:13px 44px 13px 14px; font-size:16px; font-weight:700; outline:none; background:#fff; transition:box-shadow .15s, border-color .15s;">
            <span style="position:absolute; right:14px; top:50%; transform:translateY(-50%); color:#8a9187; font-size:13px; font-weight:600;">円</span>
          </div>
        </div>
        <div>
          <label style="display:block; font-size:12px; font-weight:700; color:#5b6459; margin-bottom:5px;">毎月の返済希望額</label>
          <div style="position:relative;">
            <input id="snvm-monthly" class="snvm-input" type="text" inputmode="numeric" pattern="[0-9]*" placeholder="毎月の返済希望額を入力" style="width:100%; box-sizing:border-box; border:1.5px solid #dde9e2; border-radius:12px; padding:13px 44px 13px 14px; font-size:16px; font-weight:700; outline:none; background:#fff; transition:box-shadow .15s, border-color .15s;">
            <span style="position:absolute; right:14px; top:50%; transform:translateY(-50%); color:#8a9187; font-size:13px; font-weight:600;">円</span>
          </div>
        </div>
        <div>
          <label style="display:block; font-size:12px; font-weight:700; color:#5b6459; margin-bottom:5px;">借入利率（実質年率）</label>
          <div style="position:relative;">
            <input id="snvm-rate" class="snvm-input" type="text" inputmode="decimal" value="18.0" style="width:100%; box-sizing:border-box; border:1.5px solid #dde9e2; border-radius:12px; padding:13px 44px 13px 14px; font-size:16px; font-weight:700; outline:none; background:#fff; transition:box-shadow .15s, border-color .15s;">
            <span style="position:absolute; right:14px; top:50%; transform:translateY(-50%); color:#8a9187; font-size:13px; font-weight:600;">％</span>
          </div>
        </div>
      </div>

      <div id="snvm-outputs">
        <div id="snvm-result"></div>

        <a id="snvm-cta" href="<?php echo esc_url($simulator_url); ?>" style="margin-top:16px; display:flex; align-items:center; justify-content:center; gap:8px; width:100%; box-sizing:border-box; background:linear-gradient(135deg,#39B167,#1e7a42); color:#fff; padding:15px 24px; border-radius:14px; font-weight:800; font-size:15px; text-decoration:none; box-shadow:0 6px 16px rgba(30,122,66,0.22); transition:transform .15s, box-shadow .15s;">条件を詳しく設定してシミュレーション<span style="font-size:16px;">→</span></a>
        <p style="margin:10px 0 0; font-size:11px; color:#a3ada6; text-align:center;">会社を選ぶだけで金利も自動セット。入力は1分もかかりません。</p>
      </div>
    </div>
  </div>
  </div>
<?php
  return ob_get_clean();
}

function sakunavi_customize_register($wp_customize)
{
  // 1) 「注意事項」用セクションを追加
  $wp_customize->add_section('footer_notice_section', [
    'title'    => 'フッター：注意事項',
    'priority' => 160,
  ]);

  // 2) 設定項目を追加
  $wp_customize->add_setting('footer_notice_text', [
    'default'           => '',
    'sanitize_callback' => 'wp_kses_post',  // HTML を許可する場合
  ]);

  // 3) コントロール（入力欄）を追加
  $wp_customize->add_control('footer_notice_text_control', [
    'label'    => '注意事項テキスト',
    'section'  => 'footer_notice_section',
    'settings' => 'footer_notice_text',
    'type'     => 'textarea',
  ]);
}
add_action('customize_register', 'sakunavi_customize_register');

// 固定ページ一覧：テンプレート列 追加
add_filter('manage_pages_columns', function ($cols) {
  $cols['slug'] = 'スラッグ';
  $cols['tpl']  = 'テンプレート';
  return $cols;
});
add_action('manage_pages_custom_column', function ($col, $post_id) {
  if ($col === 'slug') {
    $post = get_post($post_id);
    echo esc_html($post->post_name ?: '（未設定）');
  }
  if ($col === 'tpl') {
    $tpl = get_page_template_slug($post_id);
    echo $tpl ? esc_html($tpl) : '（デフォルト）';
  }
}, 10, 2);

add_action('admin_menu', function () {
  add_management_page('テンプレート診断', 'テンプレート診断', 'edit_pages', 'tpl-audit', function () {
    echo '<div class="wrap"><h1>テンプレート診断</h1>';

    // 固定ページ
    $pages = get_pages(['post_status' => 'publish,private']);
    echo '<h2>固定ページ</h2><table class="widefat"><thead><tr><th>タイトル</th><th>URL</th><th>スラッグ</th><th>テンプレート</th></tr></thead><tbody>';
    foreach ($pages as $p) {
      printf(
        '<tr><td>%s</td><td><a href="%s" target="_blank">%s</a></td><td>%s</td><td>%s</td></tr>',
        esc_html($p->post_title),
        esc_url(get_permalink($p->ID)),
        esc_html(get_permalink($p->ID)),
        esc_html($p->post_name),
        esc_html(get_page_template_slug($p->ID) ?: '（デフォルト）')
      );
    }
    echo '</tbody></table>';

    // カードローン（必要なら）
    $q = new WP_Query(['post_type' => 'card_loan', 'posts_per_page' => -1, 'post_status' => 'publish']);
    echo '<h2>カードローン（single / archive の確認）</h2><table class="widefat"><thead><tr><th>タイトル</th><th>URL</th><th>スラッグ</th></tr></thead><tbody>';
    while ($q->have_posts()) {
      $q->the_post();
      printf(
        '<tr><td>%s</td><td><a href="%s" target="_blank">%s</a></td><td>%s</td></tr>',
        esc_html(get_the_title()),
        esc_url(get_permalink()),
        esc_html(get_permalink()),
        esc_html(get_post_field('post_name', get_the_ID()))
      );
    }
    wp_reset_postdata();
    echo '</tbody></table>';

    echo '<p>※ アーカイブURL：<a href="'
      . esc_url(get_post_type_archive_link('card_loan'))
      . '" target="_blank">'
      . esc_html(get_post_type_archive_link('card_loan'))
      . '</a></p>';
  });
});



// テスト
// add_action('init', 'cpt_test');
// function cpt_test()
// {
//     register_post_meta('post', 'test', array(
//         'type'         => 'string',
//         'single'       => true,
//         'show_in_rest' => true,
//     ));
// }