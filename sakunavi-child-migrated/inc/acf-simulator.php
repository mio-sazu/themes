<?php
/**
 * 返済シミュレーター用 ACF フィールド
 *
 * - card_loan_company: シミュレーターの会社選択に出すかどうか（表示ON/OFFとおすすめ表示）
 *   金利・無利息日数・限度額・注釈そのものは、会社ページで既に管理している
 *   rate_min / rate_max / no_interest_days / no_interest_note_text /
 *   limit_amount_min / limit_amount_max / cta_url をそのまま再利用する。
 * - 返済シミュレーターページ（page-simulator.php）: ページ下部に出す注釈（表示義務事項・免責事項など）
 */

add_action('acf/init', function () {
  if (! function_exists('acf_add_local_field_group')) return;

  // カードローン会社：シミュレーター表示設定
  acf_add_local_field_group([
    'key'      => 'group_sakunavi_simulator_company',
    'title'    => '返済シミュレーター表示設定',
    'position' => 'side',
    'style'    => 'default',
    'location' => [
      [
        ['param' => 'post_type', 'operator' => '==', 'value' => 'card_loan_company'],
      ],
    ],
    'fields'   => [
      [
        'key'           => 'field_sim_show',
        'label'         => '返済シミュレーターに表示する',
        'name'          => 'sim_show',
        'type'          => 'true_false',
        'ui'            => 1,
        'ui_on_text'    => '表示する',
        'ui_off_text'   => '表示しない',
        'default_value' => 0,
        'instructions'  => '「表示する」側（緑色）にすると返済シミュレーターの会社選択に登場します。金利・無利息日数・限度額・注釈は上部の基本情報タブの値がそのまま使われます。',
      ],
      [
        'key'               => 'field_sim_featured',
        'label'             => 'おすすめ表示にする',
        'name'              => 'sim_featured',
        'type'              => 'true_false',
        'ui'                => 1,
        'ui_on_text'        => 'おすすめにする',
        'ui_off_text'       => '通常表示',
        'default_value'     => 0,
        'instructions'      => '「おすすめにする」側（緑色）にするとシミュレーター上部に大きなカードで表示されます。「通常表示」側の会社は「その他の会社から選ぶ」の検索リストに入ります。並び順は「表示設定」タブの並べ替え（menu_order）に従います。',
        'conditional_logic' => [
          [
            ['field' => 'field_sim_show', 'operator' => '==', 'value' => '1'],
          ],
        ],
      ],
    ],
  ]);

  // 返済シミュレーターページ：注釈（表示義務事項・免責事項など）
  acf_add_local_field_group([
    'key'      => 'group_sakunavi_simulator_notice',
    'title'    => '返済シミュレーター 注釈',
    'position' => 'normal',
    'style'    => 'default',
    'location' => [
      [
        ['param' => 'page_template', 'operator' => '==', 'value' => 'template-parts/page-simulator.php'],
      ],
    ],
    'fields'   => [
      [
        'key'     => 'field_simulator_notice_help',
        'label'   => '入力ルール',
        'name'    => '',
        'type'    => 'message',
        'message' => 'シミュレーション結果の直後・お申し込みボタンの直前に表示されます。「本シミュレーションは目安であり、実際の審査結果によりご希望に沿えない場合があります」等、貸金業法に基づく表示義務事項や免責事項をここに入力してください。空欄の場合は何も表示されません。',
      ],
      [
        'key'          => 'field_simulator_notice',
        'label'        => 'ページ注釈',
        'name'         => 'simulator_notice',
        'type'         => 'wysiwyg',
        'tabs'         => 'visual',
        'toolbar'      => 'basic',
        'media_upload' => 0,
        'rows'         => 6,
      ],
    ],
  ]);
});
