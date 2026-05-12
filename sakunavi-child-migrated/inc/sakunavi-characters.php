<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * サクナビ キャラクター共通データ
 */
function sakunavi_get_characters() {
	return [
		'hinano' => [
			'name'          => '結城ひなの',
			'label_default' => 'ひなのの本音',
			'class'         => 'hinano',
			'icon'          => get_stylesheet_directory_uri() . '/assets/img/characters/hinano.png',
		],
		'rio' => [
			'name'          => '柏木りお',
			'label_default' => 'その時の気持ち',
			'class'         => 'rio',
			'icon'          => get_stylesheet_directory_uri() . '/assets/img/characters/rio.png',
		],
		'satomi' => [
			'name'          => '宮下さとみ',
			'label_default' => '心の声',
			'class'         => 'satomi',
			'icon'          => get_stylesheet_directory_uri() . '/assets/img/characters/satomi.png',
		],
		'takase' => [
			'name'          => '高瀬恒一',
			'label_default' => 'メモ',
			'class'         => 'takase',
			'icon'          => get_stylesheet_directory_uri() . '/assets/img/characters/takase.png',
		],
		'shun' => [
			'name'          => '三浦しゅん',
			'label_default' => '正直なところ',
			'class'         => 'shun',
			'icon'          => get_stylesheet_directory_uri() . '/assets/img/characters/shun.png',
		],
		'common' => [
			'name'          => 'サクナビ編集部',
			'label_default' => 'ひとこと',
			'class'         => 'common',
			'icon'          => get_stylesheet_directory_uri() . '/assets/img/characters/common.png',
		],
	];
}