<?php
if ( ! defined( 'ABSPATH' ) ) exit;

function sakunavi_monologue_shortcode( $atts, $content = null ) {
	$characters = function_exists( 'sakunavi_get_characters' ) ? sakunavi_get_characters() : [];

	$atts = shortcode_atts( [
		'character' => 'common',
		'style'     => 'memo',
		'label'     => '',
		'show_label'=> 'yes',
		'show_icon' => 'yes',
		'spacing'   => 'normal',
		'emphasis'  => 'no',
	], $atts, 'sakunavi_monologue' );

	$character_key = sanitize_key( $atts['character'] );
	$style         = sanitize_key( $atts['style'] );
	$spacing       = sanitize_key( $atts['spacing'] );
	$show_label    = $atts['show_label'] === 'yes';
	$show_icon     = $atts['show_icon'] === 'yes';
	$emphasis      = $atts['emphasis'] === 'yes';

	$character = $characters[ $character_key ] ?? $characters['common'] ?? [
		'name'          => 'サクナビ編集部',
		'label_default' => 'ひとこと',
		'class'         => 'common',
		'icon'          => '',
	];

	$text = trim( do_shortcode( shortcode_unautop( $content ?? '' ) ) );
	if ( $text === '' ) {
		return '';
	}

	$label = trim( $atts['label'] ) !== '' ? $atts['label'] : $character['label_default'];

	$classes = [
		'sn-monologue',
		'sn-monologue--' . sanitize_html_class( $style ),
		'sn-monologue--' . sanitize_html_class( $character['class'] ),
		'sn-monologue--spacing-' . sanitize_html_class( $spacing ),
	];

	if ( $emphasis ) {
		$classes[] = 'is-emphasis';
	}

	ob_start();
	?>
	<div class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>">
		<?php if ( $show_icon && ! empty( $character['icon'] ) ) : ?>
			<div class="sn-monologue__icon">
				<img src="<?php echo esc_url( $character['icon'] ); ?>" alt="<?php echo esc_attr( $character['name'] ); ?>">
			</div>
		<?php endif; ?>

		<div class="sn-monologue__body">
			<?php if ( $show_label && $label !== '' ) : ?>
				<p class="sn-monologue__label"><?php echo esc_html( $label ); ?></p>
			<?php endif; ?>

			<p class="sn-monologue__text"><?php echo nl2br( esc_html( wp_strip_all_tags( $text ) ) ); ?></p>
		</div>
	</div>
	<?php
	return ob_get_clean();
}
add_shortcode( 'sakunavi_monologue', 'sakunavi_monologue_shortcode' );