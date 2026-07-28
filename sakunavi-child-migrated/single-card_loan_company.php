<?php

/**
 * Template: Single - card_loan_company
 * Location: wp-content/themes/sakunavi-child-migrated/single-card_loan_company.php
 */

// --- Load page-specific CSS (before get_header) ---
$css_path = get_stylesheet_directory() . '/assets/css/company.css';
wp_enqueue_style(
  'sakunavi-company',
  get_stylesheet_directory_uri() . '/assets/css/company.css',
  [],
  file_exists($css_path) ? filemtime($css_path) : null
);

get_header();

// --- Fields (ACF) ---
// --- Hero 画像の決定（優先: ACF > トップ固定画像 > 投稿アイキャッチ > プレースホルダ） ---
// $hero_url = '';

// 1) ACFで手動差し替えがあれば最優先（任意）
if (function_exists('get_field')) {
  $hero_img = get_field('hero_image'); // 画像フィールド（IDでも配列でもOK）
  if ($hero_img) {
    $hero_url = is_array($hero_img) ? wp_get_attachment_image_url($hero_img['ID'], 'full')
      : wp_get_attachment_image_url(intval($hero_img), 'full');
  }
}

// 2) トップの固定ヒーロー画像（親テーマの assets を直参照）
//if (!$hero_url) {
//$parent_dir = get_template_directory();      // 親テーマの絶対パス
//$parent_uri = get_template_directory_uri();  // 親テーマのURL
//$top_bg_rel = '/images/index/index_img_03.png';

//if (file_exists($parent_dir . $top_bg_rel)) {
//$hero_url = $parent_uri . $top_bg_rel;
// }
//}
// 2) トップの固定ヒーロー画像 →header.phpで一括制御

// 3) 投稿アイキャッチ（保険）
//if (!$hero_url) {
//$thumb_id = get_post_thumbnail_id();
//if ($thumb_id) $hero_url = wp_get_attachment_image_url($thumb_id, 'full');
//}

// 4) プレースホルダ
if (!$hero_url) {
  $hero_url = get_theme_file_uri('assets/img/company/hero-placeholder.jpg');
}


// （本文内のサムネ用は別で使うので保持）
$inline_image_id = get_post_thumbnail_id();


$rate_prefix = function_exists('get_field') ? get_field('rate_prefix') : get_post_meta(get_the_ID(), 'rate_prefix', true);
$rate_min  = function_exists('get_field') ? get_field('rate_min') : get_post_meta(get_the_ID(), 'rate_min', true);
$rate_max  = function_exists('get_field') ? get_field('rate_max') : get_post_meta(get_the_ID(), 'rate_max', true);
$rate_has_note  = function_exists('get_field') ? get_field('rate_has_note') : '';
$rate_note_text = function_exists('get_field') ? get_field('rate_note_text') : '';

$limit_min = function_exists('get_field') ? get_field('limit_amount_min') : get_post_meta(get_the_ID(), 'limit_amount_min', true);
$limit_max = function_exists('get_field') ? get_field('limit_amount_max') : get_post_meta(get_the_ID(), 'limit_amount_max', true);
$exam_fast = function_exists('get_field') ? get_field('exam_fast') : get_post_meta(get_the_ID(), 'exam_fast', true);
$no_interest_days = function_exists('get_field') ? get_field('no_interest_days') : get_post_meta(get_the_ID(), 'no_interest_days', true);
$no_interest_label = function_exists('get_field') ? get_field('no_interest_label') : get_post_meta(get_the_ID(), 'no_interest_label', true);
$web_only  = function_exists('get_field') ? get_field('web_only') : get_post_meta(get_the_ID(), 'web_only', true);

$cta_label = function_exists('get_field') ? get_field('cta_label') : get_post_meta(get_the_ID(), 'cta_label', true);
$cta_url   = function_exists('get_field') ? get_field('cta_url')   : get_post_meta(get_the_ID(), 'cta_url', true);
if (!$cta_label) $cta_label = '申し込む';

$rank_score = floatval(function_exists('get_field') ? get_field('rank_score') : get_post_meta(get_the_ID(), 'rank_score', true));

// helper
if (!function_exists('sakunavi_star')) {
  function sakunavi_star($score)
  {
    $score = max(0, min(5, floatval($score)));
    $full = floor($score);
    $half = ($score - $full) >= 0.5 ? 1 : 0;
    $empty = 5 - $full - $half;
    return str_repeat('★', $full) . ($half ? '☆' : '') . str_repeat('☆', $empty);
  }
}
?>
<!-- <div class="company-hero-banner" style="background-image:url('<?php echo esc_url($hero_url); ?>');">
  <div class="company-hero-overlay"></div>
  <div class="wrapper company-hero-inner">
    <h1 class="loan-title"><?php the_title(); ?></h1>
  </div>
</div> -->


<div class="wrapper company-layout">
  <?php get_template_part('template-parts/breadcrumbs'); ?>
  <div class="layout">
    <?php get_sidebar(); ?>
    <main class="company-content">
      <article class="column-main">
        <figure class="loan-image">
          <?php echo $inline_image_id ? wp_get_attachment_image($inline_image_id, 'large') : ''; ?>
        </figure>

        <section class="basic-info">
          <h2 class="section-title">基本情報</h2>
          <?php
            // 既存
            $rate_prefix = function_exists('get_field') ? get_field('rate_prefix') : get_post_meta(get_the_ID(), 'rate_prefix', true);
            $rate_min  = function_exists('get_field') ? get_field('rate_min') : get_post_meta(get_the_ID(), 'rate_min', true);
            $rate_max  = function_exists('get_field') ? get_field('rate_max') : get_post_meta(get_the_ID(), 'rate_max', true);
            $exam_fast = function_exists('get_field') ? get_field('exam_fast') : get_post_meta(get_the_ID(), 'exam_fast', true);
            $no_interest_days = function_exists('get_field') ? get_field('no_interest_days') : get_post_meta(get_the_ID(), 'no_interest_days', true);
            $no_interest_label = function_exists('get_field') ? get_field('no_interest_label') : get_post_meta(get_the_ID(), 'no_interest_label', true);
            $web_only  = function_exists('get_field') ? get_field('web_only') : get_post_meta(get_the_ID(), 'web_only', true);
            // ... 他の既存変数たち

            // ▼ 各行の注釈テキスト（ACF）を取得
            $exam_note        = function_exists('get_field') ? get_field('exam_note_text')        : '';
            $rate_note        = function_exists('get_field') ? get_field('rate_note_text')        : '';
            $limit_note       = function_exists('get_field') ? get_field('limit_note_text')       : '';
            $no_interest_note = function_exists('get_field') ? get_field('no_interest_note_text') : '';
            $web_only_note    = function_exists('get_field') ? get_field('web_only_note_text')    : '';

            // ▼ 注釈のある行だけ集める（空ならスキップ）
            $notes_raw = [
                'exam'        => $exam_note,
                'rate'        => $rate_note,
                'limit'       => $limit_note,
                'no_interest' => $no_interest_note,
                'web_only'    => $web_only_note,
            ];

            $notes = [];
            foreach ($notes_raw as $key => $text) {
                if (is_string($text) && trim($text) !== '') {
                    $notes[$key] = $text;
                }
            }

            $total_notes = count($notes);

            // ▼ 「何番目の注釈か」をマップ化（rate → 1, limit → 2 など）
            $note_index = [];
            $idx = 1;
            foreach ($notes as $key => $text) {
                $note_index[$key] = $idx;
                $idx++;
            }

            // ▼ 各行で使う：こめ印マークを返すヘルパー
            if (!function_exists('sakunavi_note_mark')) {
                /**
                 * @param string $row_key  行のキー（'rate', 'limit' など）
                 * @param array  $notes    有効な注釈の配列
                 * @param array  $note_index キーごとの番号
                 * @param int    $total_notes 注釈の総数
                 */
                function sakunavi_note_mark($row_key, $notes, $note_index, $total_notes)
                {
                    if (!isset($notes[$row_key])) {
                        return ''; // この行に注釈なし
                    }

                    // 1つしか注釈がない場合 → ※のみ
                    if ($total_notes <= 1) {
                        return '<sup class="rate-asterisk">※</sup>';
                    }

                    // 複数ある場合 → ※1, ※2, ...
                    $n = isset($note_index[$row_key]) ? $note_index[$row_key] : null;
                    if (!$n) return '<sup class="rate-asterisk">※</sup>';

                    return '<sup class="rate-asterisk">※' . esc_html($n) . '</sup>';
                }
            }
          ?>
          <table class="info-table">
            <tbody>
              <?php if ($exam_fast): ?>
                <tr>
                  <th>審査時間</th>
                  <td>
                    <?php echo esc_html($exam_fast); ?>
                    <?php echo sakunavi_note_mark('exam', $notes, $note_index, $total_notes); ?>
                  </td>
                </tr>
              <?php endif; ?>

              <?php if ($rate_min !== '' || $rate_max !== ''): ?>
                <tr>
                  <th>金利</th>
                  <td>
                    <?php
                    $min = ($rate_min !== '' && $rate_min !== null) ? $rate_min : '';
                    $max = ($rate_max !== '' && $rate_max !== null) ? $rate_max : '';

                    $rate_label = '';
                    $pfx = $rate_prefix ? esc_html($rate_prefix) : '';

                    if ($min !== '' && $max !== '') {
                      $rate_label = $pfx . $min . '% ～ ' . $pfx . $max . '%';
                    } elseif ($min !== '') {
                      $rate_label = $pfx . $min . '%';
                    } elseif ($max !== '') {
                      $rate_label = '～ ' . $pfx . $max . '%';
                    }

                    if ($rate_label !== '') {
                      echo esc_html($rate_label);
                      // ★ 金利のこめ印（※ or ※1, ※2...）
                      echo sakunavi_note_mark('rate', $notes, $note_index, $total_notes);
                    }
                    ?>
                  </td>
                </tr>
              <?php endif; ?>

              <?php if (get_field('limit_amount_min') !== '' || get_field('limit_amount_max') !== ''): ?>
                <tr>
                  <th>融資限度額</th>
                  <td>
                    <?php sakunavi_the_limit_range(); ?>
                    <?php echo sakunavi_note_mark('limit', $notes, $note_index, $total_notes); ?>
                  </td>
                </tr>
              <?php endif; ?>

              <?php if ($no_interest_days !== '' || $no_interest_label): ?>
                <tr>
                  <th>無利息期間</th>
                  <td>
                    <?php echo $no_interest_label ? esc_html($no_interest_label) : (esc_html($no_interest_days) . '日間'); ?>
                    <?php echo sakunavi_note_mark('no_interest', $notes, $note_index, $total_notes); ?>
                  </td>
                </tr>
              <?php endif; ?>

              <tr>
                <th>Web完結</th>
                <td>
                  <?php echo $web_only ? '対応' : '―'; ?>
                  <?php echo sakunavi_note_mark('web_only', $notes, $note_index, $total_notes); ?>
                </td>
              </tr>
            </tbody>
          </table>

          <?php if ($total_notes === 1): ?>
            <?php $only_text = reset($notes); ?>
            <p class="rate-note">
              ※<?php echo nl2br(esc_html($only_text)); ?>
            </p>
          <?php elseif ($total_notes > 1): ?>
            <ul class="rate-notes">
              <?php foreach ($notes as $key => $text): ?>
                <li>
                  <span class="note-label">※<?php echo esc_html($note_index[$key]); ?></span>
                  <?php echo nl2br(esc_html($text)); ?>
                </li>
              <?php endforeach; ?>
            </ul>
          <?php endif; ?>
        </section>

        <section class="rating-and-cta">
          <div class="rating-box">
            <h3>おすすめ度</h3>
            <div class="rating-stars" aria-label="おすすめ度"><?php echo sakunavi_star($rank_score); ?></div>
            <?php if ($rank_score): ?><div class="rating-note"><?php echo esc_html($rank_score); ?> / 5</div><?php endif; ?>
          </div>
          <?php if ($cta_url): ?>
          <div class="apply-box">
            <a class="btn btn--primary apply-btn" href="<?php echo esc_url($cta_url); ?>"><?php echo esc_html($cta_label); ?></a>
          </div>
          <?php endif; ?>
        </section>

        <section class="company-points">
          <!--<h2 class="section-title">ここがポイント</h2>-->
          <div class="entry-content">
            <?php the_content(); ?>
          </div>
        </section>
        <?php get_template_part('template-parts/expert-supervision'); ?>
      </article>
    </main>
  </div>
</div>

<?php get_footer(); ?>