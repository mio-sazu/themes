<?php
if ( ! defined( 'ABSPATH' ) ) exit;

add_action( 'acf/init', function() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	acf_add_local_field_group( [
		'key'    => 'group_sakunavi_dialogue',
		'title'  => 'サクナビ 会話ボックス',
		'fields' => [
			[
				'key'           => 'field_dialogue_style',
				'label'         => '表示タイプ',
				'name'          => 'dialogue_style',
				'type'          => 'button_group',
				'choices'       => [
					'chat'   => '会話風',
					'note'   => 'メモ対話風',
					'simple' => 'シンプル',
				],
				'default_value' => 'chat',
				'return_format' => 'value',
			],
			[
				'key'          => 'field_dialogue_title',
				'label'        => '会話タイトル',
				'name'         => 'dialogue_title',
				'type'         => 'text',
				'instructions' => '任意。未入力なら表示しません。',
			],
			[
				'key'          => 'field_dialogue_items',
				'label'        => '発言リスト',
				'name'         => 'dialogue_items',
				'type'         => 'repeater',
				'required'     => 1,
				'layout'       => 'block',
				'button_label' => '発言を追加',
				'sub_fields'   => [
					[
						'key'           => 'field_dialogue_speaker',
						'label'         => 'キャラクター',
						'name'          => 'speaker',
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
						'key'          => 'field_dialogue_message',
						'label'        => '発言本文',
						'name'         => 'message',
						'type'         => 'textarea',
						'required'     => 1,
						'rows'         => 2,
						'new_lines'    => 'br',
					],
					[
						'key'           => 'field_dialogue_show_name',
						'label'         => '名前を表示する',
						'name'          => 'show_name',
						'type'          => 'true_false',
						'default_value' => 1,
						'ui'            => 1,
					],
					[
						'key'           => 'field_dialogue_position',
						'label'         => '左右位置',
						'name'          => 'position',
						'type'          => 'button_group',
						'choices'       => [
							'auto'  => '自動',
							'left'  => '左',
							'right' => '右',
						],
						'default_value' => 'auto',
						'return_format' => 'value',
					],
					[
						'key'           => 'field_dialogue_highlight',
						'label'         => '強調する',
						'name'          => 'highlight',
						'type'          => 'true_false',
						'default_value' => 0,
						'ui'            => 1,
					],
					[
						'key'   => 'field_dialogue_note',
						'label' => '管理メモ',
						'name'  => 'note',
						'type'  => 'text',
					],
				],
			],
			[
				'key'           => 'field_dialogue_spacing',
				'label'         => '余白サイズ',
				'name'          => 'dialogue_spacing',
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
				'key'           => 'field_dialogue_emphasis',
				'label'         => '背景強調',
				'name'          => 'dialogue_emphasis',
				'type'          => 'true_false',
				'default_value' => 0,
				'ui'            => 1,
			],
		],
		'location' => [
			[
				[
					'param'    => 'block',
					'operator' => '==',
					'value'    => 'acf/sakunavi-dialogue',
				],
			],
		],
	] );
} );