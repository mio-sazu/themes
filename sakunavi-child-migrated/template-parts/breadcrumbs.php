<?php
// トップでは非表示（必要なら消してください）
if (is_front_page()) return;

echo '<nav class="breadcrumbs wrapper" aria-label="パンくず">';

// 2) ここからフォールバック（プラグイン無しでも出る簡易パンくず）
$crumbs = [];
$push = function ($label, $url = null) use (&$crumbs) {
  $crumbs[] = ['label' => $label, 'url' => $url];
};

// ホーム
$push('ホーム', home_url('/'));

// コラム
if (is_post_type_archive('column')) {
  $push('コラム');
} elseif (is_tax('column_category')) {
  $t = get_queried_object();
  $push('コラム', get_post_type_archive_link('column'));
  if ($t && $t->parent) {
    foreach (array_reverse(get_ancestors($t->term_id, 'column_category')) as $pid) {
      $p = get_term($pid, 'column_category');
      if (!is_wp_error($p)) $push($p->name, get_term_link($p));
    }
  }
  $push($t->name);
} elseif (is_singular('column')) {
  $push('コラム', get_post_type_archive_link('column'));
  $terms = wp_get_post_terms(get_the_ID(), 'column_category');
  if ($terms && !is_wp_error($terms)) {
    $t = $terms[0];
    if ($t->parent) {
      foreach (array_reverse(get_ancestors($t->term_id, 'column_category')) as $pid) {
        $p = get_term($pid, 'column_category');
        if (!is_wp_error($p)) $push($p->name, get_term_link($p));
      }
    }
    $push($t->name, get_term_link($t));
  }
  $push(get_the_title());

  // ランキング
} elseif (is_post_type_archive('ranking')) {
  $push('ランキング');
} elseif (is_tax(['ranking_year', 'ranking_category'])) {
  $push('ランキング', get_post_type_archive_link('ranking'));
  $push(single_term_title('', false));
} elseif (is_singular('ranking')) {
  $push('ランキング', get_post_type_archive_link('ranking'));
  $y = wp_get_post_terms(get_the_ID(), 'ranking_year');
  if ($y && !is_wp_error($y)) $push($y[0]->name, get_term_link($y[0]));
  $push(get_the_title());

  // 会社系
} elseif (is_post_type_archive('card_loan_company')) {
  $push('カードローン会社');
} elseif (is_singular('card_loan_company')) {
  $push('カードローン会社', get_post_type_archive_link('card_loan_company'));
  $push(get_the_title());
} elseif (is_tax('loan_genre')) {
  $push('カードローン会社', get_post_type_archive_link('card_loan_company'));
  $push(single_term_title('', false));

  // ナレッジ（FAQ・用語）
} elseif (is_post_type_archive('knowledge') || is_tax(['knowledge_type', 'knowledge_category', 'knowledge_intent', 'knowledge_level'])) {
  $faq_pages = get_pages(['meta_key' => '_wp_page_template', 'meta_value' => 'page-knowledge-faq.php', 'number' => 1]);
  $faq_url   = $faq_pages ? get_permalink($faq_pages[0]->ID) : get_post_type_archive_link('knowledge');
  $faq_label = $faq_pages ? get_the_title($faq_pages[0]->ID) : 'よくある質問';
  $push($faq_label, $faq_url);
  if (is_tax()) $push(single_term_title('', false));
} elseif (is_singular('knowledge')) {
  $faq_pages = get_pages(['meta_key' => '_wp_page_template', 'meta_value' => 'page-knowledge-faq.php', 'number' => 1]);
  $faq_url   = $faq_pages ? get_permalink($faq_pages[0]->ID) : get_post_type_archive_link('knowledge');
  $faq_label = $faq_pages ? get_the_title($faq_pages[0]->ID) : 'よくある質問';
  $push($faq_label, $faq_url);
  $cats = get_the_terms(get_the_ID(), 'knowledge_category');
  if ($cats && !is_wp_error($cats)) $push($cats[0]->name);
  $push(get_the_title());

  // その他
} elseif (is_search()) {
  $push('検索結果');
} elseif (is_404()) {
  $push('404');

  // ▼ お金コラム（money_column タクソノミー）
} elseif (is_tax('money_column')) {

  // お金コラムの「一覧ページ」へのリンク
  // /okane-column/ が別のスラッグならここだけ変更してください
  $push('お金コラム', home_url('/column/'));
  // お金コラムのターム（例：カード会社の選び方）
  $term = get_queried_object();
  if ($term && ! is_wp_error($term)) {
    $push($term->name);
  }
} elseif (is_archive()) {
  // お金コラムのアーカイブ（カテゴリ／タグ／年月など）はそのまま1階層
  $push(wp_strip_all_tags(get_the_archive_title()));
} elseif (is_singular()) {
  $pt = get_post_type_object(get_post_type());
  if ($pt && $pt->has_archive) {
    $push($pt->labels->name, get_post_type_archive_link($pt->name));
  }
  $push(get_the_title());
}



// ページ送り
if ($p = get_query_var('paged')) $push('ページ ' . intval($p));

// 出力
echo '<ol itemscope itemtype="https://schema.org/BreadcrumbList">';
foreach ($crumbs as $i => $c) {
  $pos = $i + 1;
  echo '<li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">';
  if (!empty($c['url']) && $pos < count($crumbs)) {
    echo '<a itemprop="item" href="' . esc_url($c['url']) . '"><span itemprop="name">' . esc_html($c['label']) . '</span></a>';
  } else {
    echo '<span itemprop="name">' . esc_html($c['label']) . '</span>';
  }
  echo '<meta itemprop="position" content="' . $pos . '"></li>';
}
echo '</ol></nav>';
