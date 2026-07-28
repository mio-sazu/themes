<?php
/**
 * template-parts/loan-diagnosis.php
 * タブ「カードローンを探す」用：条件を選ぶとマッチ度順にカードローン会社を診断表示する
 */

wp_enqueue_style('loan-diagnosis-css');
wp_enqueue_script('loan-diagnosis-js');

$diag_companies = function_exists('sakunavi_loan_diagnosis_companies') ? sakunavi_loan_diagnosis_companies() : [];

wp_localize_script('loan-diagnosis-js', 'ldxData', [
  'companies' => $diag_companies,
]);
?>
<section id="ldx" class="ldx">
  <div class="ldx-head">
    <span class="ldx-eyebrow">カードローン診断</span>
    <h2 class="ldx-title">あなたに合ったカードローンを探す</h2>
    <p class="ldx-lead">気になる条件を選ぶだけで、マッチ度が高い順にカードローン会社をご案内します。</p>
  </div>

  <div class="ldx-body">
    <div class="ldx-q">
      <p class="ldx-q__label">借り入れ予定額</p>
      <div class="ldx-pills" data-group="amount">
        <button type="button" class="ldx-pill" data-value="10">10万円以下</button>
        <button type="button" class="ldx-pill" data-value="30">30万円前後</button>
        <button type="button" class="ldx-pill" data-value="60">50万円以上</button>
      </div>
    </div>

    <div class="ldx-q">
      <p class="ldx-q__label">あなたの職業</p>
      <div class="ldx-pills" data-group="job">
        <button type="button" class="ldx-pill" data-value="student">学生</button>
        <button type="button" class="ldx-pill" data-value="parttime">パート・アルバイト</button>
        <button type="button" class="ldx-pill" data-value="employee">正社員・その他</button>
      </div>
    </div>

    <div class="ldx-q">
      <p class="ldx-q__label">こだわり条件<span class="ldx-q__hint">（複数選択可）</span></p>
      <div class="ldx-pills" data-group="conditions" data-multi="1">
        <button type="button" class="ldx-pill" data-value="sameday">即日融資希望</button>
        <button type="button" class="ldx-pill" data-value="nointerest">無利息期間を重視</button>
        <button type="button" class="ldx-pill" data-value="web">WEB完結希望</button>
        <button type="button" class="ldx-pill" data-value="noverify">在籍確認なしを希望</button>
        <button type="button" class="ldx-pill" data-value="lowrate">低金利重視</button>
        <button type="button" class="ldx-pill" data-value="highlimit">高額融資に対応</button>
        <button type="button" class="ldx-pill" data-value="housewife">専業主婦（夫）も利用可</button>
        <button type="button" class="ldx-pill" data-value="cardless">アプリ完結・カードレス対応</button>
        <button type="button" class="ldx-pill" data-value="refinance">おまとめ・借り換えに対応</button>
        <button type="button" class="ldx-pill" data-value="weekend">土日・夜間でも借入可能</button>
      </div>
    </div>

    <button type="button" id="ldxSubmit" class="ldx-submit">この条件で診断する<span class="ldx-submit__arrow">→</span></button>
  </div>

  <div id="ldxResult" class="ldx-result"></div>
</section>
