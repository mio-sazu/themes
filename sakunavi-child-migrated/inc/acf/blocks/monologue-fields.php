<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * ACFフィールド登録
 */
add_action( 'acf/init', function() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	acf_add_local_field_group( [
		'key'    => 'group_sakunavi_monologue',
		'title'  => 'サクナビ 独り言ボックス',
		'fields' => [
			[
				'key'               => 'field_monologue_text',
				'label'             => '独り言本文',
				'name'              => 'monologue_text',
				'type'              => 'textarea',
				'required'          => 1,
				'rows'              => 3,
				'new_lines'         => 'br',
				'instructions'      => '20〜80字目安。1〜2文で入力してください。',
				'placeholder'       => '“今月だけ”のつもりが、ちゃんと積み上がっていました。',
			],
			[
				'key'           => 'field_monologue_character',
				'label'         => 'キャラクター',
				'name'          => 'monologue_character',
				'type'          => 'select',
				'required'      => 1,
				'choices'       => [
					'hinano' => '結城ひなの',
					'rio'    => '柏木りお',
					'satomi' => '宮下さとみ',
					'takase' => '高瀬恒一',
					'shun'   => '三浦しゅん',
					'common' => '共通',
				],
				'default_value' => 'common',
				'ui'            => 1,
				'return_format' => 'value',
			],
			[
				'key'           => 'field_monologue_style',
				'label'         => 'デザインタイプ',
				'name'          => 'monologue_style',
				'type'          => 'button_group',
				'choices'       => [
					'memo'   => 'メモ風',
					'bubble' => '吹き出し風',
					'simple' => 'シンプル',
				],
				'default_value' => 'memo',
				'layout'        => 'horizontal',
				'return_format' => 'value',
			],
			[
				'key'           => 'field_monologue_show_label',
				'label'         => 'ラベルを表示する',
				'name'          => 'monologue_show_label',
				'type'          => 'true_false',
				'default_value' => 1,
				'ui'            => 1,
			],
			[
				'key'               => 'field_monologue_label',
				'label'             => 'ラベル文言',
				'name'              => 'monologue_label',
				'type'              => 'text',
				'instructions'      => '未入力ならキャラごとの初期ラベルを使います。',
				'conditional_logic' => [
					[
						[
							'field'    => 'field_monologue_show_label',
							'operator' => '==',
							'value'    => '1',
						],
					],
				],
			],
			[
				'key'           => 'field_monologue_show_icon',
				'label'         => 'アイコンを表示する',
				'name'          => 'monologue_show_icon',
				'type'          => 'true_false',
				'default_value' => 1,
				'ui'            => 1,
			],
			[
				'key'           => 'field_monologue_spacing',
				'label'         => '余白サイズ',
				'name'          => 'monologue_spacing',
				'type'          => 'select',
				'choices'       => [
					'tight'  => '狭め',
					'normal' => '通常',
					'wide'   => '広め',
				],
				'default_value' => 'normal',
				'ui'            => 1,
				'return_format' => 'value',
			],
			[
				'key'           => 'field_monologue_emphasis',
				'label'         => '強調表示',
				'name'          => 'monologue_emphasis',
				'type'          => 'true_false',
				'default_value' => 0,
				'ui'            => 1,
			],
			[
				'key'          => 'field_monologue_note',
				'label'        => '管理メモ',
				'name'         => 'monologue_note',
				'type'         => 'text',
				'instructions' => '任意。表示には使いません。',
			],
		],
		'location' => [
			[
				[
					'param'    => 'block',
					'operator' => '==',
					'value'    => 'acf/sakunavi-monologue',
				],
			],
		],
	] );
} );
