<?php
if ( ! defined( 'ABSPATH' ) ) exit;

add_action( 'init', function() {

	if ( ! function_exists( 'register_block_pattern' ) ) {
		return;
	}

	if ( function_exists( 'register_block_pattern_category' ) ) {
		register_block_pattern_category(
			'sakunavi',
			[
				'label' => 'サクナビ',
			]
		);
	}

	register_block_pattern(
		'sakunavi/debug-text',
		[
			'title'      => 'サクナビ｜動作確認テキスト',
			'categories' => [ 'sakunavi' ],
			'content'    => '<!-- wp:paragraph --><p>サクナビのパターン動作確認です。</p><!-- /wp:paragraph -->',
		]
	);

	register_block_pattern(
		'sakunavi/column-template-basic',
		[
			'title'       => 'サクナビ｜体験コラム基本テンプレ',
			'categories'  => [ 'sakunavi' ],
			'description' => '体験コラム用の基本テンプレート',
			'content'     =>
				'<!-- wp:heading {"level":2} --><h2>当時の私の生活状況</h2><!-- /wp:heading -->' .
				'<!-- wp:paragraph --><p>ここに生活背景を書きます。</p><!-- /wp:paragraph -->' .
				'<!-- wp:heading {"level":2} --><h2>実際に何が起こったのか</h2><!-- /wp:heading -->' .
				'<!-- wp:paragraph --><p>ここに困った出来事を書きます。</p><!-- /wp:paragraph -->' .
				'<!-- wp:paragraph --><p>※必要な位置に「サクナビ｜独り言ボックス」を手動で追加してください。</p><!-- /wp:paragraph -->' .
				'<!-- wp:heading {"level":2} --><h2>焦って調べたこと</h2><!-- /wp:heading -->' .
				'<!-- wp:paragraph --><p>ここに調べた内容を書きます。</p><!-- /wp:paragraph -->' .
				'<!-- wp:heading {"level":2} --><h2>知ってよかったこと</h2><!-- /wp:heading -->' .
				'<!-- wp:paragraph --><p>ここに気づきや学びを書きます。</p><!-- /wp:paragraph -->' .
				'<!-- wp:heading {"level":2} --><h2>まとめ</h2><!-- /wp:heading -->' .
				'<!-- wp:paragraph --><p>ここにまとめを書きます。</p><!-- /wp:paragraph -->',
		]
	);

} );