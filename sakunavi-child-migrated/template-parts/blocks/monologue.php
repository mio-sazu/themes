<?php
if ( ! defined( 'ABSPATH' ) ) exit;

$characters     = function_exists( 'sakunavi_get_characters' ) ? sakunavi_get_characters() : [];
$text           = trim( (string) get_field( 'monologue_text' ) );
$character_key  = get_field( 'monologue_character' ) ?: 'common';
$style          = get_field( 'monologue_style' ) ?: 'memo';
$show_label     = (bool) get_field( 'monologue_show_label' );
$custom_label   = trim( (string) get_field( 'monologue_label' ) );
$show_icon      = (bool) get_field( 'monologue_show_icon' );
$spacing        = get_field( 'monologue_spacing' ) ?: 'normal';
$emphasis       = (bool) get_field( 'monologue_emphasis' );

if ( empty( $text ) ) {
	if ( is_admin() ) {
		echo '<div style="padding:12px;border:1px dashed #ccc;background:#fff;">独り言本文を入力してください。</div>';
	}
	return;
}

$character = $characters[ $character_key ] ?? $characters['common'] ?? [
	'name'          => 'サクナビ編集部',
	'label_default' => 'ひとこと',
	'class'         => 'common',
	'icon'          => '',
];

$label = $custom_label !== '' ? $custom_label : $character['label_default'];

$classes = [
	'sn-monologue',
	'sn-monologue--' . sanitize_html_class( $style ),
	'sn-monologue--' . sanitize_html_class( $character['class'] ),
	'sn-monologue--spacing-' . sanitize_html_class( $spacing ),
];

if ( $emphasis ) {
	$classes[] = 'is-emphasis';
}
?>
<div class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>">
	<?php if ( $show_icon && ! empty( $character['icon'] ) ) : ?>
		<div class="sn-monologue__icon">
			<img src="<?php echo esc_url( $character['icon'] ); ?>" alt="<?php echo esc_attr( $character['name'] ); ?>">
		</div>
	<?php endif; ?>

	<div class="sn-monologue__body">
		<?php if ( $show_label && ! empty( $label ) ) : ?>
			<p class="sn-monologue__label"><?php echo esc_html( $label ); ?></p>
		<?php endif; ?>

		<p class="sn-monologue__text"><?php echo nl2br( esc_html( $text ) ); ?></p>
	</div>
</div>