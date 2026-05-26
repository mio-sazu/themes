# フロントエンド・CSS命名ルール書（2026年最新版）

本ドキュメントは、保守性、再利用性、およびチーム開発における一貫性を担保するための、HTML / CSSのクラスおよびIDの命名規則を定義します。

特にWordPressテーマ・子テーマ・プラグイン開発では、外部CSSや既存テーマとの衝突を防ぐため、プロジェクトごとに固有のベンダープレフィックスを設定し、命名ルールを統一します。

---

## 1. スタイリングの基本原則

CSS設計では、スタイルの影響範囲を明確にし、保守しやすい状態を保つことが重要です。

---

### 1.1 クラス主体のスタイリング

CSSのスタイリングには、原則として **クラス（class）のみ** を使用します。

#### 基本ルール

- スタイリングはクラスで行う
- HTMLタグ名に直接スタイルを当てすぎない
- IDセレクタでスタイルを指定しない
- 影響範囲が読み取りやすい命名にする

#### 推奨例

```html
<button class="snv-c-btn snv-c-btn--primary">
  送信する
</button>
```

```css
.snv-c-btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
}
```

---

### 1.2 ID属性の限定的な用途

HTMLのID属性はページ内で一意である必要があり、再利用性が低いため、スタイリングには使用しません。

ID属性は、以下の目的に限定して使用します。

| 用途 | 例 |
|---|---|
| JavaScriptで特定の要素を取得する | `id="contact-form"` |
| フォームのラベル紐付け | `<label for="email">` |
| ページ内リンクのアンカー | `id="section-price"` |

#### NG例

```css
#header {
  background: #fff;
}
```

#### OK例

```css
.snv-l-header {
  background: #fff;
}
```

---

### 1.3 ケースの統一

クラス名およびID名は、すべて小文字とハイフンで構成される **ケバブケース（kebab-case）** を使用します。

#### OK例

```html
<div class="snv-c-primary-button"></div>
<div class="snv-c-service-card"></div>
<section id="section-price"></section>
```

#### NG例

```html
<div class="primaryButton"></div>
<div class="ServiceCard"></div>
<div class="service_card"></div>
```

---

## 2. 基本命名ルールは全体で統一する

BEMの記法やUtilityクラスの考え方など、基本となる命名ルールは、組織・チーム全体で統一します。

プロジェクトごとに命名ルールそのものを変えると、開発者が別プロジェクトに移動した際の学習コストが上がり、保守性も下がります。

---

### 2.1 統一すべきルール

以下の考え方は、プロジェクトが変わっても共通ルールとして扱います。

- BEM記法を使用する
- クラス名はkebab-caseで統一する
- スタイリングはclass主体で行う
- IDはスタイリングに使用しない
- JavaScript用クラスには `js-` を付ける
- 状態管理には `is-` / `has-` を使う
- 見た目ではなく、役割や意味で命名する
- 再利用するUIはコンポーネントとして定義する

---

### 2.2 プロジェクトごとに変えるもの

プロジェクトごとに変更するのは、先頭につける **ベンダープレフィックス** のみです。

#### 例

```css
/* 共通ルールのみの場合 */
.c-card
.c-card__title
.c-btn--primary

/* サクナビの場合 */
.snv-c-card
.snv-c-card__title
.snv-c-btn--primary

```

---

## 3. ベンダープレフィックス運用

CSS命名では、BEMやUtilityクラスなどの基本ルールはチーム・組織全体で統一しつつ、  
プロジェクトやWordPressテーマごとに **固有のベンダープレフィックス（接頭辞）** を付与して、クラス名の衝突を防ぎます。

---

### 3.1 基本方針

結論として、以下のハイブリッド運用を標準とします。

> **命名ルール自体は共通化し、先頭につけるプロジェクト固有のプレフィックスだけを案件ごとに変更する。**

#### 基本構造

```txt
[project-prefix]-[role-prefix]-[block]__[element]--[modifier]
```

#### 例

```css
.snv-c-card
.snv-c-card__title
.snv-c-card--featured
.snv-c-btn--primary
.snv-l-container
.snv-u-text-center
.snv-js-menu-toggle
.snv-is-active
```

---

### 3.2 なぜベンダープレフィックスが必要なのか

WebサイトやWordPressでは、以下のように複数のCSSが同時に読み込まれることがあります。

- WordPressテーマのCSS
- 子テーマのCSS
- プラグインのCSS
- 外部ライブラリのCSS
- JavaScriptライブラリに付属するCSS
- 管理画面用CSS
- ブロックエディタ用CSS
- 後から追加されるLP用CSS

そのため、単純に `.c-card` や `.is-active` のような汎用的な名前を使うと、他のテーマ・プラグイン・ライブラリとクラス名が衝突する可能性があります。

#### 衝突しやすいクラス名の例

```css
.c-card
.c-button
.card
.btn
.active
.is-active
.container
.grid
.title
```

---

### 3.3 WordPressで特に重要な理由

WordPressは、世界中のさまざまなテーマ・プラグインが同時に動く環境です。

そのため、汎用的なクラス名を使うと、以下のような問題が起きる可能性があります。

- プラグインのCSSと衝突する
- 親テーマと子テーマのCSSが競合する
- ブロックエディタのCSSと干渉する
- 外部ライブラリの `.container` や `.btn` とぶつかる
- 管理画面やショートコード出力のCSSと干渉する
- 後から追加したLPやCTAパーツの見た目が崩れる

そのため、WordPressテーマやプラグインでは、プロジェクト名・テーマ名由来の固有プレフィックスを必ず設定します。

---

## 4. プロジェクト別プレフィックスの決め方

プロジェクトごとに使用するベンダープレフィックスは、プロジェクト名・サイト名・テーマ名・ブランド名から英字3文字で作成します。

---

### 4.1 プレフィックス作成ルール

#### 基本ルール

- 英字3文字を基本とする
- すべて小文字にする
- 最後にハイフンを付ける
- 数字や記号は原則使わない
- 他プロジェクトと重複しないものにする
- 読みやすく、プロジェクト名を連想しやすいものにする
- WordPressテーマやプラグインと衝突しにくいものにする

#### 形式

```txt
xxx-
```

#### 例

```txt
snv-
mdi-
ani-
aym-
ysg-
```

---

### 4.2 プレフィックス生成パターン

| 生成方法 | 説明 | 例 |
|---|---|---|
| 頭文字型 | プロジェクト名の主要単語の頭文字を取る | Saku Navi → `snv-` |
| テーマ名型 | WordPressテーマ名から作る | sakunavi-theme → `snv-` |

---

### 4.3 プレフィックス決定時の注意点

#### 避けた方がよいもの

```txt
wp-
css-
js-
web-
new-
top-
main-
```

これらは一般的すぎるため、他テーマ・プラグイン・ライブラリと意味が衝突しやすくなります。

#### 推奨されるもの

```txt
snv-
ani-
aym-
ysg-
```

プロジェクト名やブランド名を連想しやすく、他と被りにくいものを選びます。

---

## 5. 役割別プレフィックスルール

クラスが持つ役割を明確にし、影響範囲をひと目で予測できるように、以下の役割プレフィックスを使用します。

ベンダープレフィックスと組み合わせて使用します。

---

### 5.1 プレフィックス一覧

| 役割プレフィックス | 意味 | 用途 | サクナビでの例 |
|---|---|---|---|
| `c-` | Component | 独立して再利用可能なUIコンポーネント | `snv-c-card`, `snv-c-btn` |
| `l-` | Layout | ページ全体のレイアウトや外枠 | `snv-l-container`, `snv-l-grid` |
| `u-` | Utility | 単一目的の微調整用クラス | `snv-u-text-center`, `snv-u-mb-8` |
| `js-` | JavaScript | JavaScript処理のフック用 | `snv-js-modal-trigger` |
| `is-` | State | 要素自身の状態 | `snv-is-active`, `snv-is-hidden` |
| `has-` | State | 何かを持っている状態 | `snv-has-error`, `snv-has-icon` |

---

### 5.2 `c-` Component

`c-` は、独立して再利用可能なUIコンポーネントに使用します。

#### 用途

- ボタン
- カード
- モーダル
- タブ
- アコーディオン
- バッジ
- フォームパーツ
- CTAブロック

#### 例

```html
<div class="snv-c-card">
  <h2 class="snv-c-card__title">カードタイトル</h2>
</div>

<button class="snv-c-btn snv-c-btn--primary">
  詳しく見る
</button>
```

---

### 5.3 `l-` Layout

`l-` は、ページ全体のレイアウトやコンポーネントを配置するための外枠に使用します。

#### 用途

- コンテナ
- グリッド
- ヘッダー
- フッター
- メインカラム
- サイドバー
- セクション外枠

#### 例

```html
<div class="snv-l-container">
  <div class="snv-l-grid">
    <main class="snv-l-main"></main>
    <aside class="snv-l-sidebar"></aside>
  </div>
</div>
```

---

### 5.4 `u-` Utility

`u-` は、1つの機能やスタイリングのみを持つ単一目的の微調整用クラスです。

#### 用途

- テキスト中央揃え
- 余白調整
- 表示 / 非表示
- Flex指定
- 色や幅などの補助指定

#### 例

```html
<p class="snv-u-text-center">
  中央揃えのテキスト
</p>

<div class="snv-u-mb-8">
  下に余白を持つ要素
</div>
```

#### 注意点

`u-` クラスは便利ですが、多用しすぎるとHTMLが読みにくくなるため、再利用性の高いUIは `c-` コンポーネントとして定義します。

---

### 5.5 `js-` JavaScript

`js-` は、JavaScriptの処理をフックするための目印として使用します。

このクラスに対して、CSSのスタイルを直接当ててはいけません。

#### OK例

```html
<button class="snv-c-btn snv-c-btn--primary snv-js-modal-trigger">
  モーダルを開く
</button>
```

```js
const trigger = document.querySelector('.snv-js-modal-trigger');
```

#### NG例

```css
.snv-js-modal-trigger {
  background: #000;
  color: #fff;
}
```

---

### 5.6 `is-` / `has-` State

`is-` や `has-` は、要素の現在の状態を表すクラスです。  
JavaScriptによって動的に付け外しされることを想定します。

#### `is-` の例

```html
<button class="snv-c-tab snv-is-active">
  選択中のタブ
</button>
```

```html
<div class="snv-c-modal snv-is-hidden">
  モーダル内容
</div>
```

#### `has-` の例

```html
<div class="snv-c-form-field snv-has-error">
  <p class="snv-c-form-field__error">入力内容を確認してください。</p>
</div>
```

#### 使い分け

| クラス | 意味 | 例 |
|---|---|---|
| `is-` | 要素自身の状態 | `snv-is-active`, `snv-is-hidden`, `snv-is-open` |
| `has-` | 何かを持っている状態 | `snv-has-error`, `snv-has-icon`, `snv-has-image` |

---

## 6. コンポーネントの構造化（BEMアーキテクチャ）

`c-` プレフィックスを持つコンポーネントの内部構造を命名する際は、  
**BEM（Block, Element, Modifier）** の記法を採用し、スタイルの競合を防ぎます。

---

### 6.1 BEMの基本構造

| 種類 | 意味 | 書き方 | サクナビでの例 |
|---|---|---|---|
| Block | コンポーネントの親となる独立した塊 | `.xxx-c-block` | `.snv-c-card` |
| Element | Blockを構成する子要素 | `.xxx-c-block__element` | `.snv-c-card__title` |
| Modifier | BlockやElementのバリエーション | `.xxx-c-block--modifier` | `.snv-c-btn--primary` |

---

### 6.2 Block（ブロック）

Blockは、コンポーネントの親となる独立した塊です。

#### 代表例

```css
.snv-c-card
.snv-c-btn
.snv-c-modal
.snv-c-tab
.snv-c-form
```

---

### 6.3 Element（エレメント）

Elementは、Blockを構成する子要素です。  
Block名にアンダースコア2つ `__` を繋いで命名します。

#### 例

```html
<article class="snv-c-card">
  <h2 class="snv-c-card__title">記事のタイトル</h2>
  <p class="snv-c-card__text">説明文が入ります。</p>
</article>
```

---

### 6.4 Modifier（モディファイア）

Modifierは、BlockやElementのバリエーションを表します。  
ハイフン2つ `--` を繋いで命名します。

#### 例

```html
<button class="snv-c-btn snv-c-btn--primary">
  送信する
</button>

<button class="snv-c-btn snv-c-btn--large">
  大きいボタン
</button>
```

---

### 6.5 HTML構造の実装例

```html
<article class="snv-c-card snv-c-card--featured">
  <h2 class="snv-c-card__title">記事のタイトル</h2>

  <div class="snv-c-card__content">
    <p>コンテンツテキスト...</p>

    <button class="snv-c-btn snv-c-btn--primary snv-js-submit-btn">
      送信する
    </button>
  </div>
</article>
```

---

### 6.6 BEM使用時の注意点

#### 子孫を深くしすぎない

NG例：

```css
.snv-c-card .snv-c-card__content p span {
  color: red;
}
```

OK例：

```css
.snv-c-card__note {
  color: var(--snv-color-text-muted);
}
```

#### ElementをさらにElement化しすぎない

NG例：

```css
.snv-c-card__content__title
```

OK例：

```css
.snv-c-card__title
.snv-c-card__content-title
```

---

## 7. セマンティック命名（機能・役割ベースの命名）

クラス名は、視覚的な「見た目」ではなく、その要素が持つ **機能や意図** に基づいて命名します。

---

### 7.1 見た目による命名は避ける

#### NG例

```css
.red-button
.text-blue
.small-text
.big-title
```

---

### 7.2 役割による命名を行う

#### OK例

```css
.snv-c-btn--danger
.snv-u-text-primary
.snv-u-text-caption
.snv-c-alert--warning
```

| 命名 | 意味 |
|---|---|
| `snv-c-btn--danger` | 危険な操作用ボタン |
| `snv-u-text-primary` | 主要テキスト |
| `snv-u-text-caption` | 注釈テキスト |
| `snv-c-alert--warning` | 警告メッセージ |

---

### 7.3 セマンティック命名のメリット

このルールを徹底することで、将来的な変更に強い設計になります。

#### 例

- 警告ボタンの色を赤からオレンジに変更する
- ダークモードに対応する
- ブランドカラーを変更する
- サイト全体のトーンを変更する

このような場合でも、HTML側のクラス名を変更せず、CSSのトークンや変数を書き換えるだけで安全に対応できます。

---

## 8. WordPressテーマ・プラグインでの厳格ルール

WordPressテーマやプラグインでは、テーマ名またはプロジェクト名に由来する英字3文字のプレフィックスを必ず設定します。

---

### 8.1 推奨ルール

```txt
[テーマ・プロジェクト略称3文字]-[役割プレフィックス]-[名前]
```

#### 例：サクナビの場合

| 項目 | 内容 |
|---|---|
| プロジェクト名 | サクッとお金ナビ |
| 略称 | サクナビ |
| WordPressテーマ名 | sakunavi-theme |
| 推奨プレフィックス | `snv-` |
| Component例 | `.snv-c-card` |
| Layout例 | `.snv-l-container` |
| Utility例 | `.snv-u-text-center` |
| JS Hook例 | `.snv-js-menu-toggle` |
| State例 | `.snv-is-active` |

---

### 8.2 WordPressで避けるべきクラス名

#### NG例

```css
.card
.button
.btn
.container
.main
.sidebar
.active
.hidden
```

#### OK例

```css
.snv-c-card
.snv-c-btn
.snv-l-container
.snv-l-main
.snv-l-sidebar
.snv-is-active
.snv-is-hidden
```

---

### 8.3 リリース後のクラス名変更は禁止

一度公開したテーマやサイトでは、クラス名の変更は慎重に行います。

特にWordPressでは、ユーザーや運用担当者が以下のように独自CSSで上書きしている可能性があります。

```css
.snv-c-card__title {
  font-size: 20px;
}
```

そのため、リリース後に `.snv-c-card__title` を `.snv-c-article-title` などへ変更すると、既存のカスタマイズが効かなくなり、デザイン崩れの原因になります。

#### 原則

- クラス名は初期設計時に確定する
- 公開後は安易に変更しない
- 変更する場合は旧クラスも一定期間残す
- 大規模変更時は移行表を作成する

---

## 9. モダン開発におけるハイブリッド運用

Tailwind CSSなどのユーティリティファーストなCSSフレームワークを導入しているプロジェクトでは、以下の役割分担でハイブリッドな命名を適用します。

---

### 9.1 Tailwindユーティリティを使う範囲

レイアウトや微小な余白調整には、Tailwind標準のユーティリティクラスを使用します。

#### 使用例

```html
<div class="flex items-center gap-4 mb-4">
  <img src="icon.png" alt="" class="w-6 h-6">
  <p class="text-sm">説明テキスト</p>
</div>
```

---

### 9.2 独自コンポーネントクラスを使う範囲

複数箇所で使い回す複雑なUIは、BEMに基づいた独自のコンポーネントクラスとして定義します。

#### 例

```css
.snv-c-btn {
  @apply inline-flex items-center justify-center rounded-md font-bold transition;
}

.snv-c-btn--primary {
  @apply bg-green-600 text-white hover:bg-green-700;
}

.snv-c-btn--large {
  @apply h-12 px-8 text-base;
}
```

---

### 9.3 ハイブリッド運用の考え方

| 対象 | 推奨方法 |
|---|---|
| 一度しか使わない微調整 | Tailwindユーティリティ |
| 複数ページで使うUI | `xxx-c-` コンポーネント |
| ページ全体の枠組み | `xxx-l-` レイアウト |
| JSの処理対象 | `xxx-js-` クラス |
| 状態管理 | `xxx-is-` / `xxx-has-` クラス |

---

## 10. モバイル・レスポンシブ対応の命名ルール

モバイル対応では、画面幅に応じた表示切り替えや、スマートフォン専用のナビゲーション、ドロワー、表示制御クラスなどが必要になります。

ただし、モバイル用だからといって `.sp-only` や `.pc-only` のような汎用名をそのまま使うと、他テーマ・プラグイン・外部CSSと衝突する可能性があります。

そのため、モバイル関連のクラスにも必ずプロジェクト固有のベンダープレフィックスを付けます。

---

### 10.1 基本方針

モバイル用クラスも、通常の命名ルールと同じく以下の構造で管理します。

```txt
[project-prefix]-[role-prefix]-[name]
```

#### 例

```css
.snv-u-sp-only
.snv-u-pc-only
.snv-u-hidden-sp
.snv-u-hidden-pc
.snv-js-mobile-nav
.snv-js-menu-toggle
.snv-is-open
```

---

### 10.2 モバイル専用クラスを乱立させない

モバイル対応は、原則としてメディアクエリで制御します。

#### 推奨

```css
.snv-c-card {
  padding: 16px;
}

@media (min-width: 768px) {
  .snv-c-card {
    padding: 24px;
  }
}
```

#### 非推奨

```html
<div class="snv-c-card snv-c-card-sp">
```

```css
.snv-c-card-sp {
  padding: 16px;
}
```

#### 理由

`sp` や `pc` をコンポーネント名に直接入れすぎると、以下の問題が起きやすくなります。

- クラス名が増えすぎる
- どれが本体のクラスか分かりにくくなる
- レスポンシブ条件がCSS内ではなくHTML側に散らばる
- 後からブレイクポイントを変更しにくくなる

---

### 10.3 表示切り替え用Utilityクラス

スマートフォンのみ表示、PCのみ表示などの表示制御が必要な場合は、Utilityクラスとして定義します。

#### 命名ルール

```txt
[project-prefix]-u-[device]-only
[project-prefix]-u-hidden-[device]
```

#### 例

```css
.snv-u-sp-only
.snv-u-tab-only
.snv-u-pc-only

.snv-u-hidden-sp
.snv-u-hidden-tab
.snv-u-hidden-pc
```

---

### 10.4 表示切り替えクラス一覧

| クラス名 | 用途 |
|---|---|
| `.snv-u-sp-only` | スマートフォンのみ表示 |
| `.snv-u-tab-only` | タブレットのみ表示 |
| `.snv-u-pc-only` | PCのみ表示 |
| `.snv-u-hidden-sp` | スマートフォンで非表示 |
| `.snv-u-hidden-tab` | タブレットで非表示 |
| `.snv-u-hidden-pc` | PCで非表示 |

---

### 10.5 表示切り替えUtilityのCSS例

```css
/* SPのみ表示 */
.snv-u-sp-only {
  display: block;
}

@media (min-width: 768px) {
  .snv-u-sp-only {
    display: none;
  }
}

/* PCのみ表示 */
.snv-u-pc-only {
  display: none;
}

@media (min-width: 1024px) {
  .snv-u-pc-only {
    display: block;
  }
}

/* SPで非表示 */
@media (max-width: 767px) {
  .snv-u-hidden-sp {
    display: none !important;
  }
}

/* PCで非表示 */
@media (min-width: 1024px) {
  .snv-u-hidden-pc {
    display: none !important;
  }
}
```

#### 注意点

- `display: none !important;` はUtilityクラスに限定する
- コンポーネント本体のCSSでは安易に `!important` を使わない
- 表示切り替えが増えすぎる場合は、HTML構造やコンポーネント設計を見直す

---

### 10.6 モバイルナビゲーションの命名ルール

ハンバーガーメニュー、ドロワーメニュー、モバイルナビなどは、PCナビと役割を分けて命名します。

#### Component

```css
.snv-c-mobile-nav
.snv-c-mobile-nav__inner
.snv-c-mobile-nav__list
.snv-c-mobile-nav__item
.snv-c-mobile-nav__link
```

#### Button

```css
.snv-c-menu-button
.snv-c-menu-button__line
```

#### JavaScript Hook

```css
.snv-js-menu-toggle
.snv-js-mobile-nav
.snv-js-menu-overlay
```

#### State

```css
.snv-is-open
.snv-is-locked
.snv-is-active
```

---

### 10.7 モバイルナビゲーションのHTML例

```html
<button class="snv-c-menu-button snv-js-menu-toggle" aria-label="メニューを開く">
  <span class="snv-c-menu-button__line"></span>
  <span class="snv-c-menu-button__line"></span>
  <span class="snv-c-menu-button__line"></span>
</button>

<nav class="snv-c-mobile-nav snv-js-mobile-nav" aria-label="スマートフォン用メニュー">
  <div class="snv-c-mobile-nav__inner">
    <ul class="snv-c-mobile-nav__list">
      <li class="snv-c-mobile-nav__item">
        <a href="/" class="snv-c-mobile-nav__link">ホーム</a>
      </li>
      <li class="snv-c-mobile-nav__item">
        <a href="/column/" class="snv-c-mobile-nav__link">コラム</a>
      </li>
    </ul>
  </div>
</nav>
```

---

### 10.8 モバイルナビゲーションのCSS例

```css
.snv-c-menu-button {
  width: 48px;
  height: 48px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
}

.snv-c-mobile-nav {
  display: none;
}

.snv-c-mobile-nav.snv-is-open {
  display: block;
}

@media (min-width: 1024px) {
  .snv-c-menu-button,
  .snv-c-mobile-nav {
    display: none;
  }
}
```

---

### 10.9 レスポンシブModifierの考え方

基本的には、画面幅の違いだけを表すModifierは多用しません。

#### 非推奨

```css
.snv-c-card--sp
.snv-c-card--pc
.snv-c-btn--mobile
```

#### 推奨

```css
.snv-c-card
.snv-c-card--featured
.snv-c-btn
.snv-c-btn--primary
```

レスポンシブの違いは、クラス名ではなくメディアクエリ内で調整します。

```css
.snv-c-card {
  padding: 16px;
}

@media (min-width: 768px) {
  .snv-c-card {
    padding: 24px;
  }
}
```

---

### 10.10 例外として許可するレスポンシブModifier

以下のように、単なる画面幅ではなく、UIの役割や表示形式が明確に変わる場合はModifierを許可します。

#### OK例

```css
.snv-c-ranking-list--compact
.snv-c-card-list--carousel
.snv-c-nav--drawer
.snv-c-search-form--modal
```

| クラス名 | 意味 |
|---|---|
| `.snv-c-ranking-list--compact` | 情報量を絞ったコンパクト表示 |
| `.snv-c-card-list--carousel` | カード一覧をカルーセル表示 |
| `.snv-c-nav--drawer` | ドロワー型ナビゲーション |
| `.snv-c-search-form--modal` | モーダル型検索フォーム |

---

### 10.11 ブレイクポイント名の統一

CSS内で使用するブレイクポイントは、プロジェクト全体で統一します。

#### 推奨ブレイクポイント

| 名前 | 幅 | 用途 |
|---|---:|---|
| `sp` | 〜767px | スマートフォン |
| `tab` | 768px〜1023px | タブレット |
| `pc` | 1024px〜1279px | PC / 小型PC |
| `wide` | 1280px〜 | ワイド画面 |

#### CSSコメント例

```css
/* ========================================
   Breakpoints
   sp:   0 - 767px
   tab:  768px - 1023px
   pc:   1024px - 1279px
   wide: 1280px -
======================================== */
```

---

### 10.12 CSSカスタムメディアを使う場合

PostCSSなどでカスタムメディアが使える環境では、以下のように管理します。

```css
@custom-media --snv-tab (min-width: 768px);
@custom-media --snv-pc (min-width: 1024px);
@custom-media --snv-wide (min-width: 1280px);
```

使用例：

```css
.snv-c-card {
  padding: 16px;
}

@media (--snv-tab) {
  .snv-c-card {
    padding: 24px;
  }
}
```

#### 注意点

WordPressテーマでPostCSSを使わない場合は、通常のメディアクエリで記述します。

```css
@media (min-width: 768px) {
  .snv-c-card {
    padding: 24px;
  }
}
```

---

### 10.13 モバイル関連クラスのNG / OK例

| 用途 | NG | OK |
|---|---|---|
| SPのみ表示 | `.sp-only` | `.snv-u-sp-only` |
| PCのみ表示 | `.pc-only` | `.snv-u-pc-only` |
| ハンバーガー | `.hamburger` | `.snv-c-menu-button` |
| モバイルナビ | `.mobile-menu` | `.snv-c-mobile-nav` |
| JSメニュー開閉 | `.menu-toggle` | `.snv-js-menu-toggle` |
| 開いている状態 | `.open` | `.snv-is-open` |
| 非表示状態 | `.hidden` | `.snv-is-hidden` |

---

## 11. 主要プロジェクト別の推奨プレフィックス

以下は、現在想定しているプロジェクト・テーマごとの推奨ベンダープレフィックスです。

---

### 11.1 サクッとお金ナビ

| 項目 | 内容 |
|---|---|
| プロジェクト名 | サクッとお金ナビ |
| 略称 | サクナビ |
| WordPressテーマ名 | sakunavi-theme |
| 推奨プレフィックス | `snv-` |
| 由来 | Saku Navi |
| 使用例 | `.snv-c-card`, `.snv-l-container`, `.snv-js-menu-toggle` |

---

## 12. ベンダープレフィックス込みの命名ルール早見表

| 種類 | 通常の命名 | ベンダープレフィックス込み |
|---|---|---|
| Component | `.c-card` | `.snv-c-card` |
| Component Element | `.c-card__title` | `.snv-c-card__title` |
| Component Modifier | `.c-card--featured` | `.snv-c-card--featured` |
| Layout | `.l-container` | `.snv-l-container` |
| Utility | `.u-text-center` | `.snv-u-text-center` |
| JavaScript Hook | `.js-modal-trigger` | `.snv-js-modal-trigger` |
| State | `.is-active` | `.snv-is-active` |
| State | `.has-error` | `.snv-has-error` |
| SPのみ表示 | `.sp-only` | `.snv-u-sp-only` |
| PCのみ表示 | `.pc-only` | `.snv-u-pc-only` |
| モバイルナビ | `.mobile-menu` | `.snv-c-mobile-nav` |
| メニュー開閉JS | `.menu-toggle` | `.snv-js-menu-toggle` |

---

## 13. 実装時の基本テンプレート

新しいWordPressテーマやWebサイト制作を開始する際は、以下のテンプレートに沿って命名ルールを決定します。

---

### 13.1 プロジェクト命名設定テンプレート

```markdown
# CSS命名設定

## プロジェクト情報

| 項目 | 内容 |
|---|---|
| プロジェクト名 |  |
| サイト名 |  |
| WordPressテーマ名 |  |
| 子テーマ名 |  |
| ブランド名 |  |
| 推奨ベンダープレフィックス |  |
| プレフィックスの由来 |  |

---

## 採用する命名ルール

- BEM記法を採用する
- クラス名はkebab-caseで統一する
- IDはスタイリングに使用しない
- CSSは原則class主体で管理する
- JavaScript用クラスには `js-` を付ける
- 状態管理には `is-` / `has-` を使用する
- WordPressテーマ・プラグインでは必ずプロジェクト固有プレフィックスを付ける
- モバイル用Utilityにもベンダープレフィックスを付ける

---

## 命名例

| 用途 | クラス名 |
|---|---|
| コンテナ | `xxx-l-container` |
| カード | `xxx-c-card` |
| カードタイトル | `xxx-c-card__title` |
| 主要ボタン | `xxx-c-btn xxx-c-btn--primary` |
| JSフック | `xxx-js-menu-toggle` |
| アクティブ状態 | `xxx-is-active` |
| SPのみ表示 | `xxx-u-sp-only` |
| PCのみ表示 | `xxx-u-pc-only` |
| モバイルナビ | `xxx-c-mobile-nav` |
```

---

### 13.2 CSSファイル冒頭に記載するコメント例

```css
/*
Theme Name: sakunavi-theme
Project: サクッとお金ナビ
CSS Prefix: snv-

Naming Rules:
- Component: snv-c-
- Layout: snv-l-
- Utility: snv-u-
- JavaScript Hook: snv-js-
- State: snv-is- / snv-has-
- Mobile Utility: snv-u-sp-only / snv-u-pc-only
- Mobile Nav: snv-c-mobile-nav
- BEM: snv-c-block__element--modifier
- IDs are not used for styling.

Breakpoints:
- sp:   0 - 767px
- tab:  768px - 1023px
- pc:   1024px - 1279px
- wide: 1280px -
*/
```

---

## 14. AI生成用プロンプト

新しいプロジェクトやコンポーネントを作成する際は、以下のプロンプトをAIに入力することで、命名ルールに沿ったHTML / CSSを生成しやすくなります。

---

### 14.1 ベンダープレフィックス決定プロンプト

```txt
以下のプロジェクト名・テーマ名から、CSSクラス命名用の英字3文字ベンダープレフィックスを提案してください。

条件：
- 英字3文字
- すべて小文字
- 最後にハイフンを付ける
- プロジェクト名またはテーマ名を連想しやすい
- WordPressテーマやプラグインのクラス名として衝突しにくい
- BEMやUtilityクラスと組み合わせても読みやすい
- 将来的に長く使える名前にする

プロジェクト名：
〇〇〇〇

テーマ名：
〇〇〇〇

ブランド名：
〇〇〇〇

出力形式：
| 候補 | 由来 | おすすめ度 | 使用例 | コメント |

最後に、最もおすすめのプレフィックスを1つ選び、その理由も説明してください。
```

---

### 14.2 CSS命名ルール適用プロンプト

```txt
以下のCSS命名ルールに従って、HTMLとCSSを作成してください。

前提：
- プロジェクト名：〇〇〇〇
- ベンダープレフィックス：xxx-
- BEM記法を使用する
- クラス名はkebab-caseにする
- IDはスタイリングに使用しない
- JavaScript用クラスには xxx-js- を付ける
- 状態管理には xxx-is- / xxx-has- を使う
- 見た目ではなく、役割や意味で命名する
- WordPressテーマでも衝突しにくい命名にする
- モバイル用Utilityにも xxx-u- を付ける
- モバイルナビは xxx-c-mobile-nav として命名する
- ハンバーガーボタンは xxx-c-menu-button として命名する

作成したいUI：
ここに作成したいUIの内容を書く

出力してほしいもの：
1. HTML
2. CSS
3. 命名意図の説明
4. レスポンシブ対応の考え方
5. 再利用時の注意点
```

---

## 15. 実装時のチェックリスト

### クラス設計

- [ ] スタイリングは原則クラスで行っている
- [ ] IDセレクタでCSSを書いていない
- [ ] クラス名はkebab-caseになっている
- [ ] 役割に応じたプレフィックスを使用している
- [ ] プロジェクト固有のベンダープレフィックスを使用している

### BEM設計

- [ ] 再利用するUIは `xxx-c-` コンポーネントとして定義している
- [ ] 子要素は `__` で命名している
- [ ] バリエーションは `--` で命名している
- [ ] 子孫セレクタが深くなりすぎていない
- [ ] Elementを過剰にネストしていない

### JavaScript連携

- [ ] JavaScript用のクラスには `xxx-js-` を付けている
- [ ] `xxx-js-` クラスにCSSを直接当てていない
- [ ] 状態変化には `xxx-is-` / `xxx-has-` を使用している

### WordPress対応

- [ ] テーマ名またはプロジェクト名由来の英字3文字プレフィックスを設定している
- [ ] `.card` / `.btn` / `.container` など汎用クラスを避けている
- [ ] 親テーマ・子テーマ・プラグインとの衝突を考慮している
- [ ] リリース後に変更しにくいクラス名を初期段階で確定している

### セマンティック命名

- [ ] 色名やサイズ名だけのクラスを避けている
- [ ] 見た目ではなく、役割や意味で命名している
- [ ] 将来的なデザイン変更に耐えられる命名になっている

### Tailwind併用

- [ ] 微調整はTailwindユーティリティで対応している
- [ ] 再利用UIは独自コンポーネントクラスにまとめている
- [ ] HTMLがユーティリティクラスで過剰に読みにくくなっていない

### モバイル・レスポンシブ命名

- [ ] モバイル専用クラスにもベンダープレフィックスを付けている
- [ ] `.sp-only` / `.pc-only` のような汎用クラスをそのまま使っていない
- [ ] 表示切り替えは `xxx-u-sp-only` / `xxx-u-pc-only` のようにUtilityで管理している
- [ ] ハンバーガーメニューは `xxx-c-menu-button` として命名している
- [ ] モバイルナビは `xxx-c-mobile-nav` として命名している
- [ ] JavaScript用の開閉フックは `xxx-js-menu-toggle` などで管理している
- [ ] 開閉状態は `xxx-is-open` で管理している
- [ ] 画面幅だけを理由に `--sp` / `--pc` Modifierを乱用していない
- [ ] レスポンシブ差分は基本的にメディアクエリで管理している

---

## 16. 最終まとめ

CSSクラスの命名は、以下のルールで統一します。

---

### 16.1 基本構造

```txt
[project-prefix]-[role-prefix]-[block]__[element]--[modifier]
```

#### 例

```css
.snv-c-card
.snv-c-card__title
.snv-c-card--featured
.snv-c-btn--primary
.snv-l-container
.snv-u-text-center
.snv-js-menu-toggle
.snv-is-active
```

---

### 16.2 最終的な命名方針

- BEMやUtilityなどの基本ルールは、組織・チーム全体で統一する
- プロジェクトごとに英字3文字のベンダープレフィックスを設定する
- WordPressテーマでは、テーマ名またはプロジェクト名由来のプレフィックスを必ず付ける
- 汎用的すぎる `.card` / `.btn` / `.active` などは使用しない
- `js-` クラスにはCSSを直接当てない
- 状態管理は `is-` / `has-` を使用する
- モバイル用Utilityにもベンダープレフィックスを付ける
- `.sp-only` / `.pc-only` のような汎用名を避ける
- モバイルナビやハンバーガーもコンポーネントとして命名する
- 一度公開したクラス名は原則変更しない
- 変更する場合は旧クラスとの互換性を考慮する
- 新規プロジェクト開始時に、必ず「CSS命名設定」を作成する

---

## 17. 完成版の運用ルール

プロジェクト開始時には、以下を必ず決定してからHTML / CSS実装に入ります。

- プロジェクト名
- WordPressテーマ名
- 子テーマ名
- ベンダープレフィックス
- 採用する命名ルール
- コンポーネント命名例
- レイアウト命名例
- JSフック命名例
- 状態クラス命名例
- モバイル用Utility命名例
- モバイルナビ命名例
- ブレイクポイント名

これにより、プロジェクトごとにCSSの影響範囲を安全に分離しながら、チーム全体では一貫した命名ルールを維持できます。