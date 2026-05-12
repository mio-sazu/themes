<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * 子ショートコードの内容を一時保存
 */
$GLOBALS['sakunavi_dialogue_items'] = [];

/**
 * 発言ショートコード
 * [sakunavi_talk character="hinano" position="left" show_name="yes" highlight="no"]発言[/sakunavi_talk]
 */
function sakunavi_talk_shortcode( $atts, $content = null ) {
	$atts = shortcode_atts( [
		'character'  => 'common',
		'position'   => 'auto',
		'show_name'  => 'yes',
		'highlight'  => 'no',
	], $atts, 'sakunavi_talk' );

	$GLOBALS['sakunavi_dialogue_items'][] = [
		'character' => sanitize_key( $atts['character'] ),
		'position'  => sanitize_key( $atts['position'] ),
		'show_name' => $atts['show_name'] === 'yes',
		'highlight' => $atts['highlight'] === 'yes',
		'message'   => trim( do_shortcode( shortcode_unautop( $content ?? '' ) ) ),
	];

	return '';
}
add_shortcode( 'sakunavi_talk', 'sakunavi_talk_shortcode' );

/**
 * 親ショートコード
 * [sakunavi_dialogue title="編集部メモ" style="chat"]...[/sakunavi_dialogue]
 */
function sakunavi_dialogue_shortcode( $atts, $content = null ) {
	$characters = function_exists( 'sakunavi_get_characters' ) ? sakunavi_get_characters() : [];

	$atts = shortcode_atts( [
		'title'     => '',
		'style'     => 'chat',
		'spacing'   => 'normal',
		'emphasis'  => 'no',
	], $atts, 'sakunavi_dialogue' );

	$GLOBALS['sakunavi_dialogue_items'] = [];
	do_shortcode( $content ?? '' );
	$items = $GLOBALS['sakunavi_dialogue_items'];

	if ( empty( $items ) ) {
		return '';
	}

	$classes = [
		'sn-dialogue',
		'sn-dialogue--' . sanitize_html_class( $atts['style'] ),
		'sn-dialogue--spacing-' . sanitize_html_class( $atts['spacing'] ),
	];

	if ( $atts['emphasis'] === 'yes' ) {
		$classes[] = 'is-emphasis';
	}

	ob_start();
	?>
	<div class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>">
		<?php if ( trim( $atts['title'] ) !== '' ) : ?>
			<p class="sn-dialogue__title"><?php echo esc_html( $atts['title'] ); ?></p>
		<?php endif; ?>

		<div class="sn-dialogue__items">
			<?php foreach ( $items as $index => $item ) : ?>
				<?php
				$key = $item['character'];
				$message = $item['message'];
				if ( $message === '' ) {
					continue;
				}

				$character = $characters[ $key ] ?? $characters['common'];

				$position = $item['position'];
				if ( $position === 'auto' ) {
					$position = ( $index % 2 === 0 ) ? 'left' : 'right';
				}

				$item_classes = [
					'sn-dialogue__item',
					'sn-dialogue__item--' . sanitize_html_class( $character['class'] ),
					'sn-dialogue__item--' . sanitize_html_class( $position ),
				];

				if ( $item['highlight'] ) {
					$item_classes[] = 'is-highlight';
				}
				?>
				<div class="<?php echo esc_attr( implode( ' ', $item_classes ) ); ?>">
					<div class="sn-dialogue__icon">
						<?php if ( ! empty( $character['icon'] ) ) : ?>
							<img src="<?php echo esc_url( $character['icon'] ); ?>" alt="<?php echo esc_attr( $character['name'] ); ?>">
						<?php endif; ?>
					</div>

					<div class="sn-dialogue__bubble">
						<?php if ( $item['show_name'] ) : ?>
							<p class="sn-dialogue__name"><?php echo esc_html( $character['name'] ); ?></p>
						<?php endif; ?>
						<p class="sn-dialogue__message"><?php echo nl2br( esc_html( wp_strip_all_tags( $message ) ) ); ?></p>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
	<?php

	$GLOBALS['sakunavi_dialogue_items'] = [];
	return ob_get_clean();
}
add_shortcode( 'sakunavi_dialogue', 'sakunavi_dialogue_shortcode' );