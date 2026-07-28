<?php
/**
 * Template Part: 人気のキーワード（column_keyword タクソノミー）
 * 使用頻度順に上位キーワードをピル表示。クリックするとそのキーワードの
 * 記事だけに絞り込んだアーカイブ（taxonomy-column_keyword.php）へ遷移する。
 * サイドバー・トップページの両方から呼び出される想定。
 */

$popular_keyword_terms = get_terms([
  'taxonomy'   => 'column_keyword',
  'hide_empty' => true,
  'orderby'    => 'count',
  'order'      => 'DESC',
  'number'     => 15,
]);

if (empty($popular_keyword_terms) || is_wp_error($popular_keyword_terms)) {
  return;
}

$current_keyword_slug = (is_tax('column_keyword') && get_queried_object()) ? get_queried_object()->slug : '';
?>
<div class="popular-keywords">
  <h3 class="popular-keywords__title">人気のキーワード</h3>
  <ul class="popular-keywords__list">
    <?php foreach ($popular_keyword_terms as $kw_term) :
      $is_active = ($kw_term->slug === $current_keyword_slug);
    ?>
      <li>
        <a href="<?php echo esc_url(get_term_link($kw_term)); ?>" class="popular-keywords__pill<?php echo $is_active ? ' is-active' : ''; ?>">
          <?php echo esc_html($kw_term->name); ?>
        </a>
      </li>
    <?php endforeach; ?>
  </ul>
</div>
