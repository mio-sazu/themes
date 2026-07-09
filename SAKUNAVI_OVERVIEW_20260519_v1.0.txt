# サクッとお金ナビ — サイト構成・ルール概要

> 作成日：2026-05-19 / バージョン：v1.0

---

## 1. プロジェクト概要

| 項目 | 内容 |
|---|---|
| サイト名 | サクッとお金ナビ |
| 略称 | サクナビ |
| ジャンル | 金融情報メディア（カードローン・お金コラム） |
| CMS | WordPress |
| 親テーマ | `sakunavi-theme` |
| 子テーマ | `sakunavi-child-migrated` |
| CSSプレフィックス | `snv-` |
| データベース | lolipop（`LAA1652092-cardloan`） |

---

## 2. テーマ構成

```
wp-content/themes/
├── sakunavi-theme/             # 親テーマ
└── sakunavi-child-migrated/    # 子テーマ（実装はこちら）
    ├── style.css               # 子テーマ共通スタイル（全ページ）
    ├── functions.php           # CPT登録 / CSS-JS読み込み / ウィジェット定義
    ├── front-page.php          # TOPページテンプレート
    ├── header.php              # ヘッダー
    ├── sidebar.php             # サイドバー
    ├── assets/
    │   ├── css/                # ページ別CSS（後述）
    │   └── js/                 # main.js / faq.js / support.js
    ├── inc/                    # CPT定義 / ACFフィールド / ショートコード
    └── template-parts/         # 再利用テンプレートパーツ（後述）
```

---

## 3. カスタム投稿タイプ（CPT）

| CPT slug | 表示名 | URL | 概要 |
|---|---|---|---|
| `column` | お金コラム | `/column/` | 金融解説記事。コラムカテゴリで分類 |
| `card_loan_company` | カードローン会社 | ─（親テーマで定義） | 各社詳細ページ |
| `ranking` | ランキング | `/ranking-list/` | ランキング記事 |
| `ranking_variant` | ランキング記事作成 | ─（非公開UI） | ランキング子ページ用 |
| `knowledge` | ナレッジ | ─ | FAQ・用語系コンテンツ |

**タクソノミー：**

| タクソノミー | 対象CPT | 概要 |
|---|---|---|
| `column_category` | column | コラムカテゴリ（階層あり） |
| `ranking_year` | ranking | ランキング年 |
| `ranking_category` | ranking | ランキング種別 |
| `knowledge_type` / `knowledge_category` | knowledge | ナレッジ分類 |

---

## 4. CSS ファイル構成と読み込み条件

| ファイル | 読み込み条件 |
|---|---|
| `style.css`（子テーマ） | **全ページ共通** |
| `support.css` | コラム・ランキング・カテゴリ・サイドバー付き固定ページ |
| `column.css` | コラム記事シングル（`is_singular('column')`） |
| `company.css` | カードローン会社詳細（`is_singular('card_loan_company')`） |
| `knowledge.css` | ナレッジ・コラム記事・フロントページ |
| `front-page.css` | TOPページ専用 |
| `hero-column.css` | TOPページのヒーローコラムビュー専用 |
| `archives.css` | アーカイブページ |
| `breadcrumbs.css` | パンくずリスト |
| `company-archive.css` | 会社アーカイブ |
| `ranking.css` | ランキングページ |
| `dialogue.css` / `monologue.css` | 対話・独り言ブロック |

---

## 5. ウィジェットエリア

| ID | 名前 | HTMLラッパー |
|---|---|---|
| `footer-1` | Footer 1：ブランド | `<div class="footer-col footer-col--brand">` |
| `footer-2` | Footer 2：メニュー | `<div class="footer-col footer-col--menu">` |
| `footer-3` | Footer 3：会社情報 | `<div class="footer-col footer-col--company">` |
| `column-sidebar` | コラム用サイドバー | `<div class="toc-box">` / `<div class="toc-title">` |

---

## 6. テンプレートパーツ

| ファイル | 用途 |
|---|---|
| `hero-column-view.php` | TOPページ：スライダー＋新着TOP5ヒーロー |
| `hero-global.php` | 各ページ共通ヒーロー |
| `loop-card.php` | 記事カード（一覧表示） |
| `loan-comparison-table.php` | カードローン比較表 |
| `expert-supervision.php` | 監修者情報ブロック |
| `breadcrumbs.php` | パンくずリスト |
| `pagination.php` | ページネーション |
| `page-loan-support.php` | ローンサポートページ |
| `mobile-drawer-links.php` | モバイルドロワーナビ |
| `blocks/monologue.php` | 独り言ブロック |
| `blocks/dialogue.php` | 対話ブロック |
| `knowledge/accordion.php` | ナレッジアコーディオン |
| `knowledge/supplement-card.php` | 補足カード |

---

## 7. デザインルール（TY_DESIGN_Rules.md）

### フォントサイズ

| 要素 | サイズ |
|---|---:|
| H1 | `clamp(32px, 5vw, 48px)` |
| H2 | 24px 以上 |
| H3 | 20px 以上 |
| Body（本文） | **16px 以上（必須）** |
| Button / Nav | 14〜16px 以上 |
| Caption（注釈） | 12〜14px |

### 行間・字送り

| 項目 | 値 |
|---|---|
| Line-height（本文） | 1.7〜1.9 |
| Letter-spacing（日本語本文） | 0.04em〜0.08em |
| 段落間 Margin-bottom | フォントサイズの2倍以上（本文なら32px以上） |

### スペーシング（8pxグリッド）

```
4px / 8px / 12px / 16px / 24px / 32px / 48px / 64px
```
中途半端な値（5px・6px・7px・10px・14px・20px等）は原則禁止。

### コンポーネント寸法

| 種別 | サイズ |
|---|---|
| 主要CTAボタン高さ | **48px以上** |
| タップターゲット最小 | 44×44px |
| アイコンボックス | 24×24px |
| カード内部Padding | 16px または 24px |

### コンテナ幅

| 用途 | 幅 |
|---|---|
| サイト全体最大幅 | 1140〜1280px |
| テキスト記事コンテンツ幅 | 640〜760px |
| フルブリード（ヒーロー等） | 1440〜1920px |

### ブレイクポイント（mobile-first：`min-width` 基準）

| 名前 | 幅 |
|---|---|
| sp | 〜767px |
| tab | 768px〜1023px |
| pc | 1024px〜1279px |
| wide | 1280px〜 |

**原則：モバイルをベースに書き、`min-width` で拡張する。`max-width` での打ち消しは禁止。**

---

## 8. CSS命名ルール（TY_CSS_naming_Rules.md）

### 基本構造

```
[project-prefix]-[role-prefix]-[block]__[element]--[modifier]
```

### プロジェクトプレフィックス：`snv-`

| 役割 | プレフィックス | 例 |
|---|---|---|
| コンポーネント | `snv-c-` | `.snv-c-card`, `.snv-c-btn` |
| レイアウト | `snv-l-` | `.snv-l-container`, `.snv-l-grid` |
| ユーティリティ | `snv-u-` | `.snv-u-text-center`, `.snv-u-sp-only` |
| JavaScriptフック | `snv-js-` | `.snv-js-menu-toggle` |
| 状態 | `snv-is-` / `snv-has-` | `.snv-is-active`, `.snv-has-error` |

### BEM記法

```css
/* Block */    .snv-c-card
/* Element */  .snv-c-card__title
/* Modifier */ .snv-c-btn--primary
```

### 重要な禁止事項

- IDセレクタでのスタイリング禁止
- `.card` `.btn` `.container` 等の汎用名禁止
- `.js-*` クラスに直接CSSを当てない
- 公開後のクラス名変更は原則禁止

---

## 9. 主要CSSクラス（実装済み・現行）

### レイアウト系

| クラス | 役割 |
|---|---|
| `.layout` | コンテンツ＋サイドバー 2カラムグリッド |
| `.single-column` | コラム記事シングルのルートラッパー |
| `.column-sidebar` | コラム右サイドバー |
| `.hero-column-view` | TOPページヒーローセクション |
| `.hcv-main` | ヒーロー内グリッド（スライダー＋TOP5） |

### 目次（TOC）

| クラス | 役割 |
|---|---|
| `.toc-box` | サイドバーTOCウィジェットのラッパー |
| `.toc-title` | サイドバーTOCの見出し（緑背景） |
| `.toc` | 記事内TOC（本文内） |
| `.company_toc` / `.company_toc_list` | 会社記事内TOC |

### 会社詳細（company.css）

| クラス | 役割 |
|---|---|
| `.info-table` | 基本情報テーブル |
| `.basic-info` | 基本情報セクション |
| `.apply-btnblue` | 申込ボタン（青グラデ） |
| `.company_faq_box` | FAQ アコーディオン |
| `.expert-supervision__*` | 監修者プロフィール |

---

## 10. ショートコード

| ショートコード | 機能 |
|---|---|
| `[column_category_list]` | コラムカテゴリ一覧を出力 |
| `[page_content slug="xxx"]` | 固定ページ本文を埋め込み |
| `[column_faq]` | コラム記事のFAQセクションを出力 |

---

## 11. その他の仕組み

- **PVカウント**：`md_post_views` メタで管理。シングル表示時に自動インクリメント
- **JSON-LD**：シングルページで `Article` 型の構造化データを自動出力
- **SmartNews フィード**：`/feed/smartnews` エンドポイントで専用XMLを配信
- **コラムFAQ**：ACFフィールドで管理、`faq.js` でアコーディオン制御
- **ブロックエディタ**：独り言（monologue）・対話（dialogue）をACFブロックとして実装

---

## 更新履歴

| バージョン | 日付 | 内容 |
|---|---|---|
| v1.0 | 2026-05-19 | 初版作成 |
