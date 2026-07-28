# サクナビ 更新ログ（〜2026-07-10）

対象リポジトリ: `wp-content/themes/`（親テーマ `sakunavi-theme` ／ 子テーマ `sakunavi-child-migrated`）

---

## 1. 返済シミュレーター（本格版）

固定ページ「返済シミュレーター」（テンプレート: `sakunavi-theme/template-parts/page-simulator.php`）に、Claude Designのモックを土台にした新しいシミュレーターを実装。

### やったこと
- `[repayment_simulator]` ショートコードを全面刷新（会社選択・条件入力・結果表示・増額シミュレーション・スケジュール表・チェックリスト・保存共有ボタン）
- 会社データはハードコードせず、既存の**カードローン会社（`card_loan_company`）投稿タイプ**から取得する設計に統一（金利・無利息日数・限度額・注釈は会社ページのACFフィールドをそのまま再利用）
- ACFフィールドを新規追加（`sakunavi-child-migrated/inc/acf-simulator.php`）
  - 会社側: `sim_show`（シミュレーターに表示する）／`sim_featured`（おすすめ表示）
  - ページ側: `simulator_notice`（ページ下部の注釈・表示義務事項用）
- 会社を選ばずに手動入力できる「カスタム設定」オプションを追加
- 数値入力のカーソル位置が飛ぶ不具合を修正（`type="number"` → `type="text"` + `inputmode`、さらに条件設定欄と結果パネルのDOMを分離して再描画範囲を最小化）
- 「完済できません」エラーの必要最低返済額、「最低返済額との比較」の基準額を、固定値（12,000円）から**借入額・金利に応じた動的計算**に修正（あわせて、実際には完済に何百年もかかる金額を「完済可能」と誤判定していた`amortize()`のバグも修正）
- 増額返済シミュレーションのカードに、削減額だけでなく総利息額・総返済回数も控えめに併記
- 「使い方」ガイド（`<details>`要素、開閉式）を追加
- サイト全体の実際のブランドカラー（`#39B167`系グリーン、`#1a6fd4`系ブルー）に合わせて配色を統一（当初モックの黄色・ゴールド系配色から変更）

### 触ったファイル
- `sakunavi-theme/functions.php`（ショートコード本体）
- `sakunavi-theme/assets/js/simulator.js`
- `sakunavi-theme/assets/css/simulator.css`
- `sakunavi-theme/template-parts/page-simulator.php`
- `sakunavi-child-migrated/inc/acf-simulator.php`（新規）
- `sakunavi-child-migrated/functions.php`（上記読み込み追加）

---

## 2. 返済シミュレーター（簡易版・トップページ用）

ホームページの「返済シミュレーション」セクション（元々は動いていない古い簡易フォーム）を、実際に計算できる簡易版に置き換え。

### やったこと
- 新ショートコード `[repayment_simulator_mini]` を追加（借入希望額・毎月の返済希望額・借入利率の3項目→総返済額・返済期間・利息合計を即時表示）
- 本格版ページへの導線ボタン付き
- PC幅ではコンテンツ幅いっぱいの2カラムレイアウトに対応
- アイコンは絵文字ではなくSVGで自作（電卓アイコン→後にヘッダーからは削除）
- 配色は本格版と同じグリーン基調、CTAボタン以外は控えめなトーンに調整

### 触ったファイル
- `sakunavi-theme/functions.php`（ショートコード追加）
- `sakunavi-theme/assets/js/simulator-mini.js`（新規）
- `sakunavi-child-migrated/front-page.php`（旧セクションを差し替え）

---

## 3. コラムの「人気のキーワード」機能

コラム記事に新しい「キーワード」タクソノミー（`column_keyword`）を追加し、使用頻度順に自動表示する仕組みを実装。

### やったこと
- 新規タクソノミー `column_keyword`（WordPress標準のタグ機能と同じ使用感、`column`投稿タイプに紐付け）
- 使用頻度順トップ15件をピル表示する共通パーツを作成し、クリックでそのキーワードの記事一覧に絞り込み
- 表示場所: サイドバー（カテゴリー一覧の直上）／ トップページ（タブエリアの直下）

### 触ったファイル
- `sakunavi-child-migrated/functions.php`（タクソノミー登録）
- `sakunavi-child-migrated/template-parts/popular-keywords.php`（新規）
- `sakunavi-child-migrated/taxonomy-column_keyword.php`（新規、絞り込み結果ページ）
- `sakunavi-child-migrated/sidebar.php`
- `sakunavi-child-migrated/front-page.php`
- `sakunavi-child-migrated/style.css`（ピルのデザイン）

**運用メモ**: コラム編集画面の「キーワード」欄にタグを入力していくと、使用回数が多い順に自動表示される。1件も登録がない間は該当エリアごと非表示。

---

## 4. コラム記事ページの表示崩れ修正

`https://stg.saku-okane-navi.com/column/3671/` を実際に確認しながら、複数の実害あるバグを特定・修正。

### 見つかった不具合と修正内容
- 「よくある質問」セクション（`.faq-section`）に上下マージンが一切なく、本文にくっついて表示されていた → 余白追加
- 手動入力FAQだけ配色がオレンジ・茶色系で浮いていた → サイトのグリーン基調に統一
- 本文中のリンク色が二重コメントアウトのミスで無効化されていた → 復活
- リンク色の修正が、記事内の申込ボタン（`.apply-btnblue`等）にも誤って適用されてしまう回帰バグ → `:not([class*="apply-btn"])` で除外して修正
- `<mark>`（黄色マーカー）が無スタイルでブラウザ標準の黄色のまま → サイト配色の薄緑ハイライトに変更
- `.loan-heading`クラスが矛盾する2つの定義（片方は常に無効な死んだコード）を持っていた → 不要な方を削除

### 触ったファイル
- `sakunavi-child-migrated/assets/css/column.css`

---

## 5. 親子テーマの重複・不要データの整理

親テーマ（`sakunavi-theme`）と子テーマ（`sakunavi-child-migrated`）の間で、CSS/JSの二重読み込みや、使われていないレガシー機能を洗い出して整理。

### 見つかった実害バグ（修正済み）
- **`style.css`の二重読み込み**: 親テーマの読み込み処理が `get_stylesheet_uri()` を使っていたため、子テーマ有効時に子テーマの`style.css`を誤って2回読み込んでいた
- **`sakunavi-company`ハンドルの衝突**: 親テーマ・子テーマ双方が同じハンドル名で別内容の`company.css`を読み込もうとしており、**子テーマ本来の623行版のCSSが一度も適用されていなかった**（先に登録された親テーマの213行版だけが有効になっていた）

→ どちらも「子テーマが有効な間は親テーマ側の読み込みをスキップする」形で解消（`is_child_theme()` によるガード）。

### 未使用と判断して削除したファイル（親テーマ）
過去の「カードローン記事（`cardloan`）」関連の旧機能一式。投稿データ自体（`cardloan`投稿タイプ・`purpose`/`condition`/`money_column`タクソノミーの登録）は現存する投稿があるため残し、表示用テンプレートのみ削除:
- `page-condition.php` / `page-purpose.php` / `page-money-column.php`（3つとも同名「お金コラム 固定ページ」で重複登録されていた・固定ページ側で未使用と確認済み）
- `page-cardloan-list.php`（存在しない投稿タイプ`card_loan`を検索していて常に空表示だった）
- `single-cardloan.php`（パンくずリンクが壊れていた）
- `taxonomy-money_column.php`
- `assets/css/column.css`（親テーマ側。子テーマの仕組みで既に強制無効化されていた）
- `Walker_Nav_Taxonomy_Posts`クラス（`functions.php`内。どのメニューにも設定されておらず未使用と確認）

### そのほか整理したもの
- `sakunavi-theme/js/`直下の未使用重複ファイル（`simulator.js`・`chat.js`・`support.js`）を削除、`main.js`は実際に使われているファイルとして`assets/js/`に一本化
- `sakunavi-theme/js/`フォルダ自体を削除（空になったため）

### 触ったファイル
- `sakunavi-theme/functions.php`

---

## 6. カードローン診断（トップページ「カードローンを探す」タブ）

トップページのタブ「カードローンを探す」内「あなたに合ったカードローンを探す」を、返済シミュレーターと同じ考え方で全面刷新。旧バージョンはCSSが一切当たっておらず生のフォーム要素のまま、判定ロジックも「4条件のうち1つでも当てはまれば表示」という緩い作りで、かつプロミス・レイクなど実在の会社名と提携リンクをコード内にハードコードしていた。

### やったこと
- データソースを、返済シミュレーターと同じ**カードローン会社（`card_loan_company`）投稿タイプ**に統一（実質年率・限度額・無利息日数・審査時間・WEB完結・おすすめ度は既存フィールドを再利用）
- ACFフィールドを新規追加（`sakunavi-child-migrated/inc/acf-loan-diagnosis.php`）
  - `diag_show`（診断結果に表示する）
  - `diag_student_ok` / `diag_parttime_ok` / `diag_sameday_ok` / `diag_no_verify`（学生・パート・即日融資・在籍確認なしの対応可否）
- 質問を4→3項目に整理（「ご希望目的」は会社データ上ほぼ差別化要因にならないため削除）
  1. 借り入れ予定額（単一選択）
  2. 職業（単一選択）
  3. こだわり条件：即日融資／無利息期間／WEB完結／在籍確認なし（複数選択可）
- 判定を「1つでも当てはまれば表示」から**マッチ度スコア方式**に変更（選んだ条件のうち何割に合致するかを「マッチ度◯%」として算出し、高い順に最大5社をカード表示）
- UIをボタン選択式（ピル）に刷新し、返済シミュレーターと同じブランドカラーで統一
- `diag_show` は当初デフォルトOFF（オプトイン）で作ったが、既存の会社を1件ずつ手動でONにする手間を避けるため、**デフォルトON・明示的にOFFにした会社だけ除外**という「オプトアウト」方式に変更（`diag_student_ok`等の個別対応可否フィールドは、実態と異なると誤マッチになるため引き続きデフォルトOFFのまま）

### 触ったファイル
- `sakunavi-child-migrated/inc/acf-loan-diagnosis.php`（新規）
- `sakunavi-child-migrated/functions.php`（上記読み込み追加）
- `sakunavi-theme/functions.php`（会社データ取得・アセット登録）
- `sakunavi-theme/template-parts/loan-diagnosis.php`（全面書き換え）
- `sakunavi-theme/assets/js/loan-diagnosis.js`（新規）
- `sakunavi-theme/assets/css/loan-diagnosis.css`（新規）
- `sakunavi-theme/template-parts/loan-diagnosis copy.php`（未使用コピーを削除）

**運用メモ**: `rate_max`（実質年率上限）が入力済みの会社は、`diag_show`を明示的にOFFにしない限り自動的に診断結果の対象になる。「学生OK」「即日融資OK」などは会社ごとに正しい状態を入力しないと、その項目のマッチ度には反映されない。

---

## アップロード対象ファイル一覧（今回のセッション分）

**新規作成**
- `sakunavi-child-migrated/inc/acf-simulator.php`
- `sakunavi-child-migrated/inc/acf-loan-diagnosis.php`
- `sakunavi-child-migrated/template-parts/popular-keywords.php`
- `sakunavi-child-migrated/taxonomy-column_keyword.php`
- `sakunavi-theme/assets/js/simulator-mini.js`
- `sakunavi-theme/assets/js/loan-diagnosis.js`
- `sakunavi-theme/assets/css/loan-diagnosis.css`

**編集**
- `sakunavi-theme/functions.php`
- `sakunavi-theme/assets/js/simulator.js`
- `sakunavi-theme/assets/css/simulator.css`
- `sakunavi-theme/template-parts/page-simulator.php`
- `sakunavi-theme/template-parts/loan-diagnosis.php`
- `sakunavi-child-migrated/functions.php`
- `sakunavi-child-migrated/front-page.php`
- `sakunavi-child-migrated/sidebar.php`
- `sakunavi-child-migrated/style.css`
- `sakunavi-child-migrated/assets/css/column.css`

**削除（サーバー上でも手動削除が必要）**
- `sakunavi-theme/page-condition.php`
- `sakunavi-theme/page-purpose.php`
- `sakunavi-theme/page-money-column.php`
- `sakunavi-theme/page-cardloan-list.php`
- `sakunavi-theme/single-cardloan.php`
- `sakunavi-theme/taxonomy-money_column.php`
- `sakunavi-theme/assets/css/column.css`
- `sakunavi-theme/js/`（フォルダごと）
- `sakunavi-theme/template-parts/loan-diagnosis copy.php`
