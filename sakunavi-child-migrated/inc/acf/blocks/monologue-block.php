<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * ACFブロック登録
 */
add_action( 'acf/init', function() {
	if ( ! function_exists( 'acf_register_block_type' ) ) {
		return;
	}

	acf_register_block_type( [
		'name'            => 'sakunavi-monologue',
		'title'           => 'サクナビ｜独り言ボックス',
		'description'     => '記事中に差し込める独り言・心の声ボックスです。',
		'render_template' => get_stylesheet_directory() . '/template-parts/blocks/monologue.php',
		'category'        => 'formatting',
		'icon'            => 'format-quote',
		'keywords'        => [ '独り言', '心の声', '吹き出し', 'メモ', 'サクナビ' ],
		'mode'            => 'preview',
		'supports'        => [
			'align' => false,
			'jsx'   => true,
		],
	] );
} );
