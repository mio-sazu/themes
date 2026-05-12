# Sakunavi テーマ リファクタリング作業ログ

対象テーマ: `sakunavi-child-migrated`（子テーマ）/ `sakunavi-theme`（親テーマ）  
作業期間: 2026年5月  
作業者: Claude Code + Mitsumaru Design

---

## 概要

WordPress 子テーマの CSS・テンプレートを整理し、以下の状態を実現した。

- **グローバルスタイル** → 子テーマ `style.css` に集約（全ページ共通）
- **ページ別スタイル** → 各 CSS ファイルに分離（条件付き読み込み）
- **親テーマの重複コード** → 削除または `_backup/` に退避
- **テンプレート** → 共通コードを `template-parts/` に抽出し doc ブロックを付与

---

## Phase 1 — バックアップ・初期整理 ✅

### 退避したフォルダ（親テーマ `sakunavi-theme/_backup/` へ移動）

| 元のパス | 内容 |
|---|---|
| `sakunavi-theme/css_old/` | 旧スナップショット CSS 8ファイル（未参照） |
| `sakunavi-theme/article/` | HTML 3件＋画像（front-page.php から参照されていた旧コンテンツ） |
| `sakunavi-theme/column/` | HTML 21件＋画像（WordPress URL パスとしてのみ存在） |
| `sakunavi-theme/side/` | HTML 1件＋画像（未参照） |

> `_backup/` はローカル保管のみ。サーバー上の元パスから削除済み。

---

## Phase 2 — CSS 読み込み条件整理 ✅

### CSS 読み込みマップ（整理後）

| CSS ファイル | 読み込み条件 |
|---|---|
| `style.css`（子テーマ） | 全ページ（常時） |
| `assets/css/support.css` | column / ranking / page-with-sidebar（会社ページ除く） |
| `assets/css/company.css` | `card_loan_company` 単一記事ページのみ |
| `assets/css/breadcrumbs.css` | 全ページ（常時） |
| `assets/css/knowledge.css` | knowledge 関連ページ + フロントページ |
| `assets/css/column.css` | support.css と同条件 |
| `assets/css/front-page.css` | フロントページのみ |
| `assets/css/simulator.css` | `[repayment_simulator]` ショートコード使用時のみ |
| `assets/css/ranking.css` | ranking 関連ページのみ |
| `assets/css/archives.css` | column / ranking アーカイブ・タクソノミーのみ |

### 親テーマ `functions.php` の修正

- `simulator.css` の**無条件読み込みを削除**（ショートコード起動時のみに変更）
- `chat.js` の読み込みを**全ページ → フロントページのみ**に制限

---

## Phase 3 — CSS 分離・整理 ✅

### 子テーマ `style.css` への移動（全ページ共通スタイル）

以下のクラスを各 CSS ファイルから削除し、`style.css` に集約。

| クラス | 移動元 |
|---|---|
| `.logo img` | `support.css` |
| `.menu-bar` / `.main-menu` / `.sub-menu` / `.has-sub` | `support.css` / `company.css` |
| `.section-title` | `company.css`（`page-coming-soon.php` 等でも使用されるため） |
| `.footer-note` / `.footer-note p` | 親テーマ `assets/css/style.css`（会社ページで `support.css` 非読み込みのため） |
| `.hero-global` / `__inner` / `__title` | 親テーマ（`min-height: 18vh` の新版に更新） |
| `.wrapper` / `.layout`（グリッド版） | 新規追加 |
| `.column-sidebar` / `.sidebar` / `.toc-wrapper` | 新規追加 |
| `.banner-column` | 新規追加 |
| ヘッダー・フッター・レスポンシブ共通ルール | 新規追加 |

### 各 CSS ファイルのクリーンアップ

**`company.css`（935 → 626行）**
- グローバル系スタイル 199行削除（`body`/`html`/`img`、`.logo img`、`.hero-*`、`.menu-bar`、`.column-sidebar` 等）
- `html { scroll-behavior: smooth; }` 重複削除
- 旧フッターブロック（`.footer-note`、`.footer-container` 等）削除
- 未使用の `.breadcrumb`（単数形）削除

**`support.css`（784 → 741行）**
- `.logo img` ブロック削除
- `.menu-bar` / `.main-menu` ブロック削除

**`column.css`**
- 未使用の `.breadcrumb`（単数形）削除

**`knowledge.css`**
- 全クラスが `kn-*` プレフィックス付きでスコープ済み → 変更なし

**`breadcrumbs.css`**
- `.breadcrumbs`（複数形）のみ定義、グローバル読み込み済み → 変更なし

---

## Phase 4 — 親テーマ削減 ✅

### 親テーマ `assets/css/style.css`（2083 → 1841行）

以下のブロックを削除（子テーマ `style.css` で上書き済みのため）。

| 削除したブロック | 理由 |
|---|---|
| `body {}` 空ルール / `.flex { display: flex; }` | 空・汎用すぎる |
| 旧フッター（`.footer-note`、`.footer-inner`、`.footer-container`、`.footer-nav`、`.copyright` 等） | 子テーマの `style.css` + `footer.php` で管理 |
| `.logo img` | 子テーマ `style.css` に移動 |
| `aside { width: 200px; padding-right: 30px; }` | 子テーマのグリッド版で上書き |
| `.column-sidebar` 全ブロック | 子テーマ `style.css` に包括版あり |
| `.sidebar-box` / `.banner-column` 静的ブロック | 子テーマ `style.css` に移動 |
| `.hero-global`（`min-height: 38vh` 版） | 子テーマで `18vh` 版に更新 |

**保持したもの（削除不可）**
- ハンバーガーメニュー CSS（`#nav-input`、`#nav-open`、`#nav-close`、`#nav-content`）  
  → 子テーマ `header.php` が直接参照
- タブシステム、ローンカード、スライダー、ポップアップ等のページ固有 CSS

---

## Phase 5 — テンプレート整理・共通化 ✅

### `sakunavi_safe_excerpt()` の集約

| 変更 | 詳細 |
|---|---|
| `functions.php` に追加 | 共通関数として1か所で定義 |
| `archive-column.php` から削除 | `if (!function_exists(...))` のインライン定義を除去 |
| `taxonomy-column_category.php` から削除 | 同上 |

### `template-parts/pagination.php` を新規作成

6ファイルに重複していた `paginate_links()` ブロックを抽出。

```php
get_template_part('template-parts/pagination');
```

に置き換えたファイル：

- `archive-column.php`
- `taxonomy-column_category.php`
- `archive-knowledge.php`
- `archive-ranking.php`
- `taxonomy-ranking_category.php`
- `taxonomy-ranking_year.php`

### doc ブロック追加（11ファイル）

テンプレート用途・使用 CSS を明記。

| ファイル |
|---|
| `front-page.php` |
| `single-column.php` |
| `single-knowledge.php` |
| `archive-card_company.php` |
| `archive-column.php` |
| `archive-knowledge.php` |
| `archive-ranking.php` |
| `taxonomy-column_category.php` |
| `taxonomy-loan_genre.php` |
| `taxonomy-ranking_category.php` |
| `taxonomy-ranking_year.php` |
| `template-parts/breadcrumbs.php` |
| `template-parts/hero-global.php` |

---

## 不具合修正

### シミュレーター CSS 欠落（Phase 4 副作用）

**現象:** フロントページの返済シミュレーターがデフォルトスタイルになった  
**原因:** Phase 4 で親テーマの `simulator.css` 無条件読み込みを削除した結果、`front-page.css` が依存していた `.sim-table { border-collapse: collapse; }` が届かなくなった  
**対処:** `front-page.css` に `.sim-table` ベーススタイルを追加し、`simulator.css` への依存を解消

---

## 後始末（推奨）

| 項目 | 対応 |
|---|---|
| `sakunavi-child-migrated/___functions.php` | WordPress が読み込まない下書きファイル。不要なら削除 |
| `themes/` 直下の `.zip` ファイル4件 | URL から直接ダウンロード可能なため、ローカル・サーバー両方から削除推奨 |

---

---

## Phase 6 — CSS 詳細修正・親子テーマ重複解消 ✅

### 6-1. フロントページ テーブル修正

**ファイル：** `assets/css/front-page.css`

#### 比較早見表（カードローン早見表）の列幅設定
セレクタを `.loan-table .table-scroll-area th:nth-child()` に絞り、タブ内テーブルへの影響を防止。

| 列 | 幅 |
|---|---|
| カード（1列目） | 22% |
| 限度額（2列目） | 30% |
| 最短審査（3列目） | 28% |
| ボタン（4列目） | 1%（最小幅） |

#### タブ内カードテーブル（縦型 `table.loan-table`）
- `table.loan-table th { width: 35%; }` でラベル列を固定
- `border-bottom: 1px solid #dde9e2` を `th` にも追加（左列の下線が消えていた問題を修正）

#### `td:last-child` セレクタの絞り込み
- **修正前：** `.loan-table td:last-child { width: 1%; }`
- **修正後：** `.loan-table .table-scroll-area td:last-child { width: 1%; }`
  - タブ内テーブルの値セルに `width: 1%` が効いて潰れていた問題を解消

#### テーブル下線消え問題
- `.loan-table table { overflow: visible; }` を追加
  - 親テーマの `overflow: hidden` が `border-collapse: collapse` と組み合わさり最終行の下線をクリップしていた

---

### 6-2. 企業ページ CSS 修正

**ファイル：** `assets/css/company.css`

#### `info-table` 列比率の調整
| 項目 | 変更内容 |
|---|---|
| `th` 幅 | 明示的に `32%` を設定 |
| `td` 幅 | 明示的に `68%` を設定 |
| padding | `10px 14px` に統一 |

#### `.apply-btnblue` ボタンの redesign（shimmer なし版）
企業単体ページ（`single-card_loan_company.php`）専用のボタン。

| 項目 | 変更前 | 変更後 |
|---|---|---|
| 色 | 薄い水色グラデ | ロイヤルブルー `#1a6fd4 → #2e8ef0` |
| 横幅 | `max-width: 90%` | `max-width: 480px` |
| 角丸 | `border-radius: 12px` | `border-radius: 50px`（ピル型） |
| shimmer | あり（`::before` スライド） | なし（`content: none;`） |
| hover | `background-position` アニメーション | `translateY(-2px)` + shadow強調 |

---

### 6-3. `.apply-btnblue` のグローバル統一

**使用箇所：** `sakunavi-theme/archive-card_loan_company.php`（カードローン会社一覧ページ）

| ファイル | 対応内容 |
|---|---|
| `style.css`（子テーマ・常時読込） | 新デザインを追加。archive ページに適用。shimmer あり |
| `support.css` | 同デザインに統一。shimmer あり |
| `company.css` | 単体会社ページ用（shimmer なし）は別途維持 |

**統一後デザイン仕様：**
- `background: linear-gradient(135deg, #1a6fd4 0%, #2e8ef0 60%, #1a6fd4 100%)`
- `max-width: 320px` / `padding: 14px 36px`
- `border-radius: 50px` / `font-size: 16px`
- shimmer（`::before` スライドアニメーション）維持

---

### 6-4. 親テーマ・子テーマ 重複セレクタ解消

**方針：** 子テーマの値が正しい最終形のため、親テーマから重複ブロックを削除して子テーマに一本化。

**ファイル：** `sakunavi-theme/assets/css/style.css`

| 削除したブロック | 理由 |
|---|---|
| `.menu-bar` / `.main-menu` / `.sub-menu` 系 | 子テーマ `style.css` に正しい値がある |
| `.wrapper` / `.layout`（flex版） | 子テーマが grid 化・幅調整済み |
| `.apply-btnblue` 系 | 子テーマで redesign 済み |

削除箇所にはコメント `→ 子テーマ style.css に移管` を残した。

**復元が必要だったプロパティ：**  
親テーマ削除時に `.main-menu > li { width: 220px; text-align: center; }` が失われたため、子テーマ `style.css` に明示的に追加。

---

### 6-5. モバイルレイアウト崩れの修正

#### 原因1：親テーマの `display: block` が grid を上書き

**ファイル：** `sakunavi-theme/assets/css/style.css`

- **修正前：** `@media (max-width: 768px) { .wrapper, .layout { display: block; } }`
- **修正後：** `@media (max-width: 768px) { .wrapper { display: block; } }`
  - `.layout` の `display: block` が子テーマの `display: grid` を上書きしてモバイルレイアウトを破壊していた

**ファイル：** `sakunavi-child-migrated/style.css`

- モバイルの `.layout` ブロックに `display: grid;` を明示追加（親テーマに負けないように）

#### 原因2：`.logo` の固定幅によるページ全体の横スクロール

**ファイル：** `sakunavi-theme/assets/css/style.css`（親テーマ）

```css
/* 親テーマにあった問題のあるルール */
.logo {
    width: 1100px;  /* ← スマホで 1100px 分の横幅が強制され横スクロール発生 */
    margin: 0 auto;
}
```

**ファイル：** `sakunavi-child-migrated/style.css`（子テーマで上書き）

```css
.logo {
    width: min(1100px, 100%);  /* PC は最大 1100px、スマホは 100% */
    max-width: 100%;
    box-sizing: border-box;
}
```

---

## アップロードファイル一覧（Phase 6）

| ファイル | 変更概要 |
|---|---|
| `sakunavi-child-migrated/style.css` | `.apply-btnblue` 追加、`.logo` 修正、モバイルgrid明示、メニュー幅復元 |
| `sakunavi-child-migrated/assets/css/front-page.css` | テーブル列幅・下線・`td:last-child` セレクタ修正 |
| `sakunavi-child-migrated/assets/css/company.css` | `info-table` 比率・`.apply-btnblue` redesign |
| `sakunavi-child-migrated/assets/css/support.css` | `.apply-btnblue` 新デザインに統一 |
| `sakunavi-theme/assets/css/style.css` | 重複CSS削除・モバイル `display` 修正 |

---

## template-parts 一覧（整理後）

```
template-parts/
├── blocks/
│   ├── dialogue.php
│   └── monologue.php
├── knowledge/
│   ├── accordion.php
│   ├── related-links.php
│   └── supplement-card.php
├── breadcrumbs.php
├── expert-supervision.php
├── hero-global.php
├── loop-card.php
├── mobile-drawer-links.php
├── page-loan-support.php
└── pagination.php          ← Phase 5 で新規追加
```
