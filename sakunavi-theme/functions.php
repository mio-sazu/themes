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

  wp_enqueue_style(
    'sakunavi-simulator',
    $theme_uri . '/assets/css/simulator.css',
    ['sakunavi-style'],
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
    $theme_uri . '/js/main.js',
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
  // chat.js は後述の JavaScript
  wp_enqueue_script(
    'chat-js',
    get_template_directory_uri() . '/assets/js/chat.js',
    [], // 依存なし
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


// 返済シミュレーション
add_action('wp_enqueue_scripts', 'simulator_enqueue_assets');
function simulator_enqueue_assets()
{
  // 計算用JS
  wp_register_script(
    'simulator-js',
    get_template_directory_uri() . '/assets/js/simulator.js',
    [], // 依存なし
    null,
    true
  );
  // オプションでCSS
  wp_register_style(
    'simulator-css',
    get_template_directory_uri() . '/assets/css/simulator.css',
    [],
    null
  );
}

add_shortcode('repayment_simulator', 'simulator_shortcode');
function simulator_shortcode($atts)
{
  // ショートコードが呼ばれたら資産をキューに積む
  wp_enqueue_script('simulator-js');
  wp_enqueue_style('simulator-css');

  // フォームの HTML を返す
  return <<<HTML
<div class="simulator-wrapper">
  <table class="sim-table">
    <tr>
      <th>借り入れ予定額</th>
      <td>
        <select id="sim-amount">
          <option value="">選択してください</option>
          <option value="100000">10万円</option>
          <option value="300000">30万円</option>
          <option value="500000">50万円</option>
          <option value="1000000">100万円</option>
          <option value="2000000">200万円</option>
        </select>
      </td>
    </tr>
    <tr>
      <th>希望返済期間</th>
      <td>
        <select id="sim-term">
          <option value="">選択してください</option>
          <option value="3">3ヶ月</option>
          <option value="6">6ヶ月</option>
          <option value="12">1年</option>
          <option value="24">2年</option>
          <option value="36">3年</option>
          <option value="60">5年</option>
        </select>
      </td>
    </tr>
    <tr>
      <th>金利</th>
      <td>
        <select id="sim-rate">
          <option value="">選択してください</option>
          <option value="3">3%</option>
          <option value="10">10%</option>
          <option value="15">15%</option>
          <option value="18">18%</option>
        </select>
      </td>
    </tr>
    <tr>
      <th>目的</th>
      <td>
        <select id="sim-purpose">
          <option value="">選択してください</option>
          <option value="生活費">生活費</option>
          <option value="学費">学費</option>
          <option value="旅行・趣味">旅行・趣味</option>
          <option value="その他">その他</option>
        </select>
      </td>
    </tr>
  </table>
  <div class="sim-button">
    <button id="calculateBtn" type="button">シミュレーションする</button>
  </div>
  <div id="simResult" class="sim-result" style="display:none;"></div>
</div>
HTML;
}
// 2. Settings API の登録
add_action('admin_init', 'simulator_settings_init');
function simulator_settings_init()
{
  // 設定項目を登録
  register_setting('simulator_settings_group', 'simulator_amounts');
  register_setting('simulator_settings_group', 'simulator_terms');
  register_setting('simulator_settings_group', 'simulator_rates');
  register_setting('simulator_settings_group', 'simulator_purposes');

  // ■ アロー関数を通常の無名関数に書き換え
  add_settings_section(
    'simulator_main_section',
    '返済シミュレーターの項目（カンマ区切り）',
    function () {
      echo '各リストを「値,値,値」のようにカンマ区切りで入力してください。';
    },
    'simulator-settings'
  );

  // フィールド追加はそのまま…
  add_settings_field(
    'field_sim_amounts',
    '借入額リスト（円）',
    'simulator_field_amounts_render',
    'simulator-settings',
    'simulator_main_section'
  );
  add_settings_field(
    'field_sim_terms',
    '返済期間リスト（月）',
    'simulator_field_terms_render',
    'simulator-settings',
    'simulator_main_section'
  );
  add_settings_field(
    'field_sim_rates',
    '金利リスト（%）',
    'simulator_field_rates_render',
    'simulator-settings',
    'simulator_main_section'
  );
  add_settings_field(
    'field_sim_purposes',
    '目的リスト',
    'simulator_field_purposes_render',
    'simulator-settings',
    'simulator_main_section'
  );
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