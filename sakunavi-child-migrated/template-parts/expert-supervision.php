<?php

/**
 * Template Part: Expert Supervision
 */
?>
<section class="expert-supervision">
  <h4 class="company_title">この記事を監修したのはこの方</h4>

  <div class="expert-supervision__wrap">
    <div class="expert-supervision__image">
      <img
        src="<?php echo esc_url(get_theme_file_uri('assets/img/natuki.jpg')); ?>"
        alt="監修者の写真"
        class="expert-supervision__profile-img">
    </div>

    <div class="expert-supervision__body">
      <p class="expert-supervision__badge">肩書</p>
      <p class="expert-supervision__name">藤井　夏樹</p>

      <?php
      $comments = [
        1 => "借入は金利だけでなく、自身の収入と支出のバランス・見直し、返済総額と毎月の返済額が無理なく続くかが重要です。\n契約前に条件を比較し、計画的に利用しましょう。返済に困った場合は、専門士業への相談を推奨します。",
        2 => "ローン選びは審査の有無より、返済計画とリスク管理が要となります。\n延滞は信用情報に影響するため、困ったら早めに担当金融機関や専門士業への相談を推奨します。",
        3 => "カードローンは便利な反面、借り過ぎを招きやすい商品です。\n目的と上限額を決め、返済シミュレーションで負担を確認してから利用しましょう。",
        4 => "借入条件（上限金利・返済方式・手数料・遅延損害金）を理解することが、トラブル予防の第一歩です。\n分からない点は必ず確認しましょう。返済に困った場合は、専門士業への相談を推奨します。",
        5 => "生活費の補填など短期の資金繰りは、まず家計の見直しが基本。\nローンを使う場合も総費用を把握し、返済可能性を最優先に判断しましょう。\n返済に困った場合は、専門士業への相談を推奨します。",
      ];

      $post_id = get_queried_object_id();
      if (!$post_id) $post_id = get_the_ID();

      // ACFの選択値（1〜5）を取得
      $selected = null;
      if (function_exists('get_field')) {
        $selected = get_field('supervision_comment_key', $post_id);
      } else {
        $selected = get_post_meta($post_id, 'supervision_comment_key', true);
      }
      $selected = (int) $selected;

      // 未選択なら記事ごと固定で自動割当（1〜5）
      if ($selected < 1 || $selected > 5) {
        $selected = ($post_id % 5) + 1;
      }

      $comment = $comments[$selected];
      ?>

      <div class="expert-supervision__balloon">
        <p><?php echo nl2br(esc_html($comment)); ?></p>
      </div>
      <!-- ▼ ここから事務所情報（追加） -->
      <div class="expert-supervision__office">
        <div class="expert-supervision__office-head">
          <h3 class="expert-supervision__office-name">東京大手法律事務所</h3>
          <p class="expert-supervision__office-meta">第一東京弁護士会所属（登録番号53149）</p>
        </div>

        <p class="expert-supervision__office-desc">
          借金・債務整理、労働・雇用、離婚・男女問題、など身近な法律問題から、<br>
          人事労務、顧問対応、規約・契約書作成等の企業法務まで幅広く取り扱う。
        </p>
      </div>
      <!-- ▲ ここまで事務所情報 -->
    </div>
  </div>
</section>