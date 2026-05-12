# サポートページ（Loan Support）WP化手順（template-parts 版）

## 配置
```
wp-content/themes/sakunavi-child-migrated/
  ├─ template-parts/
  │    └─ page-loan-support.php
  └─ assets/css/
       └─ support.css
```

## 使い方
1. 固定ページを新規作成 → 右側「テンプレート」で **サポートページ（Loan Support）** を選択し公開
2. 本文欄に `side/loan-support.html` の **<h1>〜フッター直前までの本文** を貼り付け（ヘッダー/フッターはWP側を使用）
3. 画像はメディアライブラリにアップし、`<img src="">` を差し替え

## 備考
- CSSはこのテンプレート使用時のみ `support.css` を読み込みます（他ページへ影響しません）。
- テンプレートはサブフォルダ `template-parts/` 内でも **Template Name** コメントにより選択可能です。
