<?php
// トップには出さない
if (is_front_page()) return;

// 子テーマ画像ディレクトリ（URI と 実パス）
$child_img_uri = trailingslashit(get_stylesheet_directory_uri()) . 'assets/img/';
$child_img_dir = trailingslashit(get_stylesheet_directory())   . 'assets/img/';

// 便利ヘルパ（子テーマに画像があればそれ、無ければ親テーマの既定にフォールバック）
function sakunavi_child_img($rel, $fallback = null)
{
    $child_img_dir = trailingslashit(get_stylesheet_directory()) . 'assets/img/';
    $child_img_uri = trailingslashit(get_stylesheet_directory_uri()) . 'assets/img/';
    $path = $child_img_dir . ltrim($rel, '/');
    if (file_exists($path)) return $child_img_uri . ltrim($rel, '/');
    return $fallback ?: get_theme_file_uri('images/index/index_img_01.png');
}
// デフォルト（あなたの画像）
$bg_default = sakunavi_child_img('hero_img.png');

// コンテキスト別（子テーマ内に用意できるなら置いておく）
$bg_ranking  = sakunavi_child_img('ranking/hero_ranking.jpg', $bg_default);
$bg_column   = sakunavi_child_img('column/hero_column.jpg',   $bg_default);
$bg_company  = sakunavi_child_img('company/hero_company.jpg', $bg_default);
$bg_company2 = sakunavi_child_img('company/hero_card_company.jpg', $bg_company);
$bg_taxonomy = sakunavi_child_img('misc/hero_taxonomy.jpg',   $bg_default);

$bg = $bg_default;

// ランキング（single はアイキャッチ最優先）
if (is_singular('ranking')) {
    $thumb = get_the_post_thumbnail_url(null, 'full');
    $bg = $thumb ?: $bg_ranking;
} elseif (is_post_type_archive('ranking') || is_tax(['ranking_category', 'ranking_year'])) {
    $bg = $bg_ranking;

    // 年別 hero_YYYY.jpg が子テーマにあれば採用
    if (is_tax('ranking_year')) {
        $term = get_queried_object();
        $maybe_rel = 'ranking/hero_' . $term->slug . '.jpg';
        $maybe_path = $child_img_dir . $maybe_rel;
        if (file_exists($maybe_path)) {
            $bg = $child_img_uri . $maybe_rel;
        }
    }
    // ACFターム画像があれば最優先
    if (is_tax() && function_exists('get_field')) {
        $acf = get_field('hero_bg', get_queried_object());
        if (!empty($acf)) $bg = is_array($acf) ? ($acf['url'] ?? $bg) : $acf;
    }
} elseif (is_singular('column') || is_post_type_archive('column') || is_tax(['column_category', 'column_persona'])) {
    $bg = $bg_column;
} elseif (is_singular('card_loan_company') || is_post_type_archive('card_loan_company') || is_tax('loan_genre')) {
    $bg = $bg_company;
} elseif (is_singular('card_company') || is_post_type_archive('card_company')) {
    $bg = $bg_company2;
} elseif (is_tax()) {
    $bg = $bg_taxonomy;
}
// 見出しテキスト（あなたの既存関数のままでOK）
if (!function_exists('sakunavi_get_section_title')) {
    function sakunavi_get_section_title()
    {
        if (is_post_type_archive()) {
            $pt  = is_array(get_query_var('post_type')) ? reset(get_query_var('post_type')) : get_query_var('post_type');
            $obj = $pt ? get_post_type_object($pt) : null;
            return $obj && !empty($obj->labels->name) ? $obj->labels->name : get_the_archive_title();
        }
        if (is_tax() || is_category() || is_tag()) return single_term_title('', false);
        if (is_home()) {
            $blog = get_option('page_for_posts');
            return $blog ? get_the_title($blog) : 'ブログ';
        }
        if (is_search()) return '検索結果';
        if (is_404())   return 'ページが見つかりません';
        if (is_singular()) {
            $pt = get_post_type();
            if ($pt && !in_array($pt, ['post', 'page'], true)) {
                $obj = get_post_type_object($pt);
                if ($obj && !empty($obj->labels->name)) return $obj->labels->name; // single はセクション名
            }
            return get_the_title();
        }
        return get_the_archive_title() ?: get_bloginfo('name');
    }
}
$title = sakunavi_get_section_title();
?>

<div class="hero-global" style="--hero-bg:url('<?php echo esc_url($bg); ?>');">
    <div class="hero-global__inner wrapper">
        <?php if (is_archive() || is_home() || is_search()) : ?>
            <h1 class="hero-global__title"><?php echo esc_html($title); ?></h1>
        <?php else : ?>
            <?php
            // single は本文側で H1 を出すので、ここではセクション名を小さく任意表示
            $pt = get_post_type();
            if ($pt && !in_array($pt, ['post', 'page'], true)) {
                $obj = get_post_type_object($pt);
                if ($obj && !empty($obj->labels->name)) {
                    echo '<p class="hero-global__section">' . esc_html($obj->labels->name) . '</p>';
                }
            }
            ?>
        <?php endif; ?>
    </div>
</div>