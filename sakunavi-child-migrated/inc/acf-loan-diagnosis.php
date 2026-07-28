<?php
/**
 * 「あなたに合ったカードローンを探す」診断機能用 ACF フィールド
 *
 * card_loan_company の既存フィールド（rate_min/rate_max/limit_amount_min/max/
 * no_interest_days/web_only/cta_url など）はそのまま再利用し、診断でしか
 * 使わない項目だけをここで追加する。
 */

add_action('acf/init', function () {
  if (! function_exists('acf_add_local_field_group')) return;

  acf_add_local_field_group([
    'key'      => 'group_sakunavi_loan_diagnosis',
    'title'    => 'カードローン診断 表示設定',
    'position' => 'side',
    'style'    => 'default',
    'location' => [
      [
        ['param' => 'post_type', 'operator' => '==', 'value' => 'card_loan_company'],
      ],
    ],
    'fields'   => [
      [
        'key'           => 'field_diag_show',
        'label'         => '診断結果に表示する',
        'name'          => 'diag_show',
        'type'          => 'true_false',
        'ui'            => 1,
        'ui_on_text'    => '表示する',
        'ui_off_text'   => '表示しない',
        'default_value' => 1,
        'instructions'  => '「あなたに合ったカードローンを探す」（トップページ）の診断結果候補から外したい場合のみOFFにしてください（未設定の会社は表示する扱いになります）。',
      ],
      [
        'key'               => 'field_diag_student_ok',
        'label'             => '学生の利用',
        'name'              => 'diag_student_ok',
        'type'              => 'true_false',
        'ui'                => 1,
        'ui_on_text'        => '可',
        'ui_off_text'       => '不可',
        'default_value'     => 0,
        'conditional_logic' => [[['field' => 'field_diag_show', 'operator' => '==', 'value' => '1']]],
      ],
      [
        'key'               => 'field_diag_parttime_ok',
        'label'             => 'パート・アルバイトの利用',
        'name'              => 'diag_parttime_ok',
        'type'              => 'true_false',
        'ui'                => 1,
        'ui_on_text'        => '可',
        'ui_off_text'       => '不可',
        'default_value'     => 0,
        'conditional_logic' => [[['field' => 'field_diag_show', 'operator' => '==', 'value' => '1']]],
      ],
      [
        'key'               => 'field_diag_sameday_ok',
        'label'             => '即日融資',
        'name'              => 'diag_sameday_ok',
        'type'              => 'true_false',
        'ui'                => 1,
        'ui_on_text'        => '対応',
        'ui_off_text'       => '非対応',
        'default_value'     => 0,
        'conditional_logic' => [[['field' => 'field_diag_show', 'operator' => '==', 'value' => '1']]],
      ],
      [
        'key'               => 'field_diag_no_verify',
        'label'             => '原則、在籍確認なし',
        'name'              => 'diag_no_verify',
        'type'              => 'true_false',
        'ui'                => 1,
        'ui_on_text'        => 'あり',
        'ui_off_text'       => 'なし',
        'default_value'     => 0,
        'conditional_logic' => [[['field' => 'field_diag_show', 'operator' => '==', 'value' => '1']]],
        'instructions'      => '在籍確認なしでの利用を原則案内できる場合のみONにしてください。',
      ],
      [
        'key'               => 'field_diag_housewife_ok',
        'label'             => '専業主婦（夫）の利用',
        'name'              => 'diag_housewife_ok',
        'type'              => 'true_false',
        'ui'                => 1,
        'ui_on_text'        => '可',
        'ui_off_text'       => '不可',
        'default_value'     => 0,
        'conditional_logic' => [[['field' => 'field_diag_show', 'operator' => '==', 'value' => '1']]],
        'instructions'      => '本人に収入がない配偶者（配偶者貸付など）でも申込可能な場合のみONにしてください。',
      ],
      [
        'key'               => 'field_diag_cardless_ok',
        'label'             => 'アプリ完結・カードレス対応',
        'name'              => 'diag_cardless_ok',
        'type'              => 'true_false',
        'ui'                => 1,
        'ui_on_text'        => '対応',
        'ui_off_text'       => '非対応',
        'default_value'     => 0,
        'conditional_logic' => [[['field' => 'field_diag_show', 'operator' => '==', 'value' => '1']]],
      ],
      [
        'key'               => 'field_diag_refinance_ok',
        'label'             => 'おまとめ・借り換え対応',
        'name'              => 'diag_refinance_ok',
        'type'              => 'true_false',
        'ui'                => 1,
        'ui_on_text'        => '対応',
        'ui_off_text'       => '非対応',
        'default_value'     => 0,
        'conditional_logic' => [[['field' => 'field_diag_show', 'operator' => '==', 'value' => '1']]],
      ],
      [
        'key'               => 'field_diag_weekend_ok',
        'label'             => '土日・夜間の借入対応',
        'name'              => 'diag_weekend_ok',
        'type'              => 'true_false',
        'ui'                => 1,
        'ui_on_text'        => '対応',
        'ui_off_text'       => '非対応',
        'default_value'     => 0,
        'conditional_logic' => [[['field' => 'field_diag_show', 'operator' => '==', 'value' => '1']]],
        'instructions'      => '土日・夜間でも振込やATM等での借入に対応できる場合のみONにしてください。',
      ],
    ],
  ]);
});
