<?php
/**
 * Template Part: ページネーション
 *
 * 使い方:
 *   get_template_part('template-parts/pagination');
 */
global $wp_query;
$links = paginate_links([
    'total'     => $wp_query->max_num_pages,
    'current'   => max(1, get_query_var('paged', 1)),
    'mid_size'  => 1,
    'prev_text' => '« 前へ',
    'next_text' => '次へ »',
]);
if ($links) : ?>
<div class="pagination">
    <?php echo $links; ?>
</div>
<?php endif;
