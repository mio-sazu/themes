<?php
/**
 * Template Part: カードローン比較早見表
 * CPT: loan_comparison / ACF: lc_url, lc_limit, lc_review_time, lc_btn_label
 * 表示順: menu_order 昇順（編集画面「順序」欄）
 */

$loan_items = get_posts( [
    'post_type'      => 'loan_comparison',
    'posts_per_page' => -1,
    'post_status'    => 'publish',
    'orderby'        => 'menu_order',
    'order'          => 'ASC',
] );

if ( empty( $loan_items ) ) return;

// 全投稿の注釈を収集・重複排除（※番号順でソート）
$footnotes = [];
foreach ( $loan_items as $item ) {
    $raw = function_exists( 'get_field' ) ? get_field( 'lc_footnote', $item->ID ) : '';
    if ( ! $raw ) continue;
    foreach ( array_filter( array_map( 'trim', explode( "\n", $raw ) ) ) as $line ) {
        if ( ! in_array( $line, $footnotes, true ) ) {
            $footnotes[] = $line;
        }
    }
}
// ※番号の昇順に並べ替え
usort( $footnotes, function ( $a, $b ) {
    preg_match( '/※(\d+)/', $a, $ma );
    preg_match( '/※(\d+)/', $b, $mb );
    return (int) ( $ma[1] ?? 99 ) - (int) ( $mb[1] ?? 99 );
} );
?>

<section class="loan-table">
    <h2>カードローン<br class="sp-only">比較早見表</h2>

    <div class="table-scroll-wrap js-scroll-hint">
        <p class="table-scroll-note" aria-hidden="true">
            <span class="table-scroll-note__text">横にスクロールできます</span>
            <span class="table-scroll-note__arrow">→</span>
        </p>

        <div class="table-scroll-area">
            <table>
                <thead>
                    <tr>
                        <th>カード</th>
                        <th>限度額</th>
                        <th>最短審査</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ( $loan_items as $item ) :
                        $thumb       = function_exists( 'get_field' ) ? get_field( 'lc_thumbnail',   $item->ID ) : null;
                        $url         = function_exists( 'get_field' ) ? get_field( 'lc_url',         $item->ID ) : '';
                        $limit       = function_exists( 'get_field' ) ? get_field( 'lc_limit',       $item->ID ) : '';
                        $review_time = function_exists( 'get_field' ) ? get_field( 'lc_review_time', $item->ID ) : '';
                        $btn_label   = function_exists( 'get_field' ) ? get_field( 'lc_btn_label',   $item->ID ) : '';
                        $btn_label   = $btn_label ?: '今すぐ詳細を見る';
                        $url         = $url ?: '#';
                    ?>
                    <tr>
                        <td class="lc-card-cell">
                            <?php if ( $thumb ) : ?>
                                <a href="<?php echo esc_url( $url ); ?>" class="lc-card-cell__thumb" tabindex="-1" aria-hidden="true">
                                    <img src="<?php echo esc_url( $thumb['url'] ); ?>" alt="<?php echo esc_attr( $thumb['alt'] ?: $item->post_title ); ?>" loading="lazy" width="80" height="40">
                                </a>
                            <?php endif; ?>
                            <a href="<?php echo esc_url( $url ); ?>" class="lc-card-cell__name"><?php echo esc_html( $item->post_title ); ?></a>
                        </td>
                        <td><?php echo esc_html( $limit ); ?></td>
                        <td><?php echo nl2br( esc_html( $review_time ) ); ?></td>
                        <td>
                            <a href="<?php echo esc_url( $url ); ?>" class="apply-btnred"><?php echo esc_html( $btn_label ); ?></a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php if ( ! empty( $footnotes ) ) : ?>
    <div class="Annotation">
        <?php foreach ( $footnotes as $note ) : ?>
            <p><?php echo esc_html( $note ); ?></p>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</section><!-- /.loan-table -->
