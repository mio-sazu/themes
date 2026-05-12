<?php
if ( ! defined( 'ABSPATH' ) ) exit;

$characters = function_exists( 'sakunavi_get_characters' ) ? sakunavi_get_characters() : [];
$style      = get_field( 'dialogue_style' ) ?: 'chat';
$title      = trim( (string) get_field( 'dialogue_title' ) );
$items      = get_field( 'dialogue_items' );
$spacing    = get_field( 'dialogue_spacing' ) ?: 'normal';
$emphasis   = (bool) get_field( 'dialogue_emphasis' );

if ( empty( $items ) || ! is_array( $items ) ) {
	if ( is_admin() ) {
		echo '<div style="padding:12px;border:1px dashed #ccc;background:#fff;">発言を追加してください。</div>';
	}
	return;
}

$classes = [
	'sn-dialogue',
	'sn-dialogue--' . sanitize_html_class( $style ),
	'sn-dialogue--spacing-' . sanitize_html_class( $spacing ),
];

if ( $emphasis ) {
	$classes[] = 'is-emphasis';
}
?>
<div class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>">
	<?php if ( $title !== '' ) : ?>
		<p class="sn-dialogue__title"><?php echo esc_html( $title ); ?></p>
	<?php endif; ?>

	<div class="sn-dialogue__items">
		<?php foreach ( $items as $index => $item ) : ?>
			<?php
			$key       = $item['speaker'] ?? 'common';
			$message   = trim( (string) ( $item['message'] ?? '' ) );
			$show_name = ! empty( $item['show_name'] );
			$position  = $item['position'] ?? 'auto';
			$highlight = ! empty( $item['highlight'] );

			if ( $message === '' ) {
				continue;
			}

			$character = $characters[ $key ] ?? $characters['common'];

			if ( $position === 'auto' ) {
				$position = ( $index % 2 === 0 ) ? 'left' : 'right';
			}

			$item_classes = [
				'sn-dialogue__item',
				'sn-dialogue__item--' . sanitize_html_class( $character['class'] ),
				'sn-dialogue__item--' . sanitize_html_class( $position ),
			];

			if ( $highlight ) {
				$item_classes[] = 'is-highlight';
			}
			?>
			<div class="<?php echo esc_attr( implode( ' ', $item_classes ) ); ?>">
				<div class="sn-dialogue__icon">
					<img src="<?php echo esc_url( $character['icon'] ); ?>" alt="<?php echo esc_attr( $character['name'] ); ?>">
				</div>

				<div class="sn-dialogue__bubble">
					<?php if ( $show_name ) : ?>
						<p class="sn-dialogue__name"><?php echo esc_html( $character['name'] ); ?></p>
					<?php endif; ?>
					<p class="sn-dialogue__message"><?php echo nl2br( esc_html( $message ) ); ?></p>
				</div>
			</div>
		<?php endforeach; ?>
	</div>
</div>