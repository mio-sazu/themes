# 記事装飾パーツ 使い方ガイド

お金コラム記事・カードローン会社記事の本文中で使える装飾パーツ(囲み枠・ボックス・カード)の一覧と使い方です。基本的に**本文エディタの「テキスト(HTML)」モード**でそのまま貼り付けて使います(ビジュアルモードだと`[`や`]`が変換されてしまうことがあるので注意)。

対応記事タイプ: 特記が無いものは **お金コラム記事・カードローン会社記事の両方** で使えます。

---

## POINTボックス(水色の注目ポイント枠)

```html
<div class="sakunavi_point_box">
    <span class="sakunavi_point_box_title">ここにタイトル</span>
    <p>ここに文章</p>
</div>
```

---

## 注意書き・ALERTボックス(薄い赤・びっくりマーク付き)

```html
<div class="sakunavi_alert_box">
<div class="sakunavi_alert_box_title">タイトルを入力</div>
   <p>びっくりマーク！で強調</p>
</div>
```

---

## TIP・豆知識ボックス(緑・電球アイコン付き)

ALERT(注意)と対になる、前向きな補足情報用です。

```html
<div class="sakunavi_tip_box">
<div class="sakunavi_tip_box_title">豆知識</div>
   <p>ここに文章</p>
</div>
```

---

## 引用ブロック(アイコン付き・`snv-c-quote`)

記事内で他サイト・書籍などの文章を引用するときに使う、緑の引用符アイコン付きの引用ブロックです。

```html
<blockquote class="snv-c-quote">
    <p class="snv-c-quote__text">ここに引用文を入力します。</p>
    <cite class="snv-c-quote__cite">出典元・引用元URLなど</cite>
</blockquote>
```

- `snv-c-quote__cite`(出典)は省略可能です。不要な場合は`<cite>`ごと削除してください
- 引用文が複数段落になる場合は`snv-c-quote__text`の`<p>`を複数並べてください
- 素のWordPress引用ブロック(クラスなしの`<blockquote>`)を貼った場合は、この装飾は当たらず、コラム記事側の既存のデフォルト引用スタイル(緑の左ボーダー)が適用されます。装飾を効かせたい場合は必ず`class="snv-c-quote"`を付けてください

---

## まとめボックス(四隅の角カッコ囲み・アイコン付き)

```html
<div class="sakunavi_summary_box">
<p class="sakunavi_summary_box_title">まとめ</p>
<p>ここに文章</p>
</div>
```

---

## メリット・デメリットボックス(青・赤)

タイトル部分と本文部分が別々の`<div>`になっている点に注意してください(1セットずつ)。

```html
<div class="sakunavi_merit_box_title"><p>メリット</p></div>
<div class="sakunavi_merit_box">
<p>ここに文字を入力</p>
</div>

<div class="sakunavi_demerit_box_title"><p>デメリット</p></div>
<div class="sakunavi_demerit_box">
<p>ここに文字を入力</p>
</div>
```

### 横並びにしたい場合

`sakunavi_meritdemerit_row` / `sakunavi_meritdemerit_col`で囲むと、PC幅では2カラム横並び、スマホ幅では自動的に縦積みになります。

```html
<div class="sakunavi_meritdemerit_row">
  <div class="sakunavi_meritdemerit_col">
    <div class="sakunavi_merit_box_title"><p>メリット</p></div>
    <div class="sakunavi_merit_box">
      <p>ここに文字を入力</p>
    </div>
  </div>
  <div class="sakunavi_meritdemerit_col">
    <div class="sakunavi_demerit_box_title"><p>デメリット</p></div>
    <div class="sakunavi_demerit_box">
      <p>ここに文字を入力</p>
    </div>
  </div>
</div>
```

---

## STEPガイド(番号付き手順・補足ふきだし付き)

「①公式サイトにアクセス→②必要書類をアップロード→③申込完了」のような、番号付きの手順を点線でつないで見せるパーツです。ショートコードではなく、以下のHTMLを**「カスタムHTML」ブロック**として貼り付けて使います。STEPの数は`step-guide__item`ごと自由に増減できます。

```html
<ul class="step-guide">
    <li class="step-guide__item">
        <div class="step-guide__marker">
            <div class="step-guide__circle">
                <span class="step-guide__label">STEP</span>
                <span class="step-guide__num">1</span>
            </div>
        </div>
        <div class="step-guide__body">
            <h4 class="step-guide__title">見出しを入力</h4>
            <p class="step-guide__text">説明文を入力します。改行したい場合は&lt;br&gt;を使ってください。</p>
        </div>
    </li>
    <li class="step-guide__item">
        <div class="step-guide__marker">
            <div class="step-guide__circle">
                <span class="step-guide__label">STEP</span>
                <span class="step-guide__num">2</span>
            </div>
        </div>
        <div class="step-guide__body">
            <h4 class="step-guide__title">見出しを入力</h4>
            <p class="step-guide__text">説明文を入力します。</p>
        </div>
    </li>
</ul>
```

- STEPを増やす場合は`<li class="step-guide__item">`ブロックごと複製し、`step-guide__num`の数字を振り直してください
- 丸バッジの色はブランドカラー(緑)固定です

### 特定のSTEPに補足の吹き出しを付ける(`step-guide__callout`)

「郵送不要」「最短即日」のような一言メモを、特定のSTEPだけに電球アイコン付きの吹き出しで添えられます。付けたいSTEPの`step-guide__body`内、`step-guide__text`の直後に追加してください(全STEPに揃える必要はありません)。

```html
<div class="step-guide__body">
    <h4 class="step-guide__title">必要書類をアップロード</h4>
    <p class="step-guide__text">本人確認書類などをスマホで撮影してアップロードします。</p>
    <div class="step-guide__callout">
        <span class="step-guide__callout-icon">💡</span>
        <p class="step-guide__callout-text">郵送や店頭に行く必要がなく<br>すぐに提出可能</p>
    </div>
</div>
```

- `step-guide__callout-icon`の中身(`💡`)は絵文字なので、内容に合わせて`📌`「⚡」などに差し替え可能です
- 吹き出しはSTEP本文の下に流し込みで表示されるため、スマホ幅でも崩れません

---

## ミニ返済シミュレーター(`[repayment_simulator_mini]`ショートコード)

借入希望額・毎月の返済希望額などを入力すると、その場で返済目安が分かる簡易シミュレーターです。トップページと同じ機能を記事本文中にそのまま差し込めます。

```
[repayment_simulator_mini]
```

- 属性は無し。この1行だけで動きます(見た目・計算ロジックともに固定です)
- どの記事タイプでも使えます(コラム・カードローン会社・その他ページ含め、全ページでJSが読み込まれる設定になっています)
- 他のパーツと同様、前後に空行を1行あけて単独行で書いてください

---

## あわせて読みたいカード(`[post_embed]`ショートコード)

記事下の自動関連記事とは別に、**本文中の好きな位置に、狙った1記事だけをカード形式で差し込める**ショートコードです。

```
[post_embed id="123"]
```

- `id`: 差し込みたい記事の投稿ID(数字)。管理画面でその記事の編集画面を開いたときのURL(`post.php?post=123&action=edit`)の`post=`の数字部分
- `label`(省略可): カード上部のラベル文言。省略時は「あわせて読みたい」。`label=""`で非表示
- 埋め込める記事タイプ: **お金コラム記事・ランキング記事・カードローン会社記事**
- **埋め込む記事は「公開」状態である必要があります**(下書き・非公開のIDを指定すると、エラーにはならず何も表示されません)

```
[post_embed id="456" label="こちらもおすすめ"]
```

枠の左上に「あわせて読みたい」のタイトルタグが乗った、モノトーン(濃いグレー)のボーダー付きカードになります(コラム記事・カードローン会社記事どちらのページに貼っても同じ見た目です)。

---

## 注意点(共通)

- 装飾パーツを複数並べる時は、**間に空行を1行あける**ようにしてください。空行なしで詰めて書くと、WordPressの自動整形(wpautop)によって余計な`<br>`が入ってしまうことがあります
- 見た目が崩れた場合は、まずテスト環境で確認してから公開してください
