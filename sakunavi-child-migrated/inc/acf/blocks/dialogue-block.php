<?php
if ( ! defined( 'ABSPATH' ) ) exit;

add_action( 'acf/init', function() {
	if ( ! function_exists( 'acf_register_block_type' ) ) {
		return;
	}

	acf_register_block_type( [
		'name'            => 'sakunavi-dialogue',
		'title'           => 'サクナビ｜会話ボックス',
		'description'     => '複数人の掛け合いや座談会風の会話を表示します。',
		'render_template' => get_stylesheet_directory() . '/template-parts/blocks/dialogue.php',
		'category'        => 'formatting',
		'icon'            => 'format-chat',
		'keywords'        => [ '会話', '掛け合い', '座談会', '吹き出し', 'サクナビ' ],
		'mode'            => 'preview',
		'supports'        => [
			'align' => false,
			'jsx'   => true,
		],
	] );
} );