<?php

/**
 * template-parts/loan-diagnosis.php
 * タブ「カードローンを探す」内の診断フォーム
 */
?>
<div id="card" class="content">
    <section class="diagnosis-section">
        <h2 class="section-title">あなたに合ったカードローンを探す</h2>
        <form id="loanDiagnosisForm" class="diagnosis-form">
            <div class="form-group">
                <label for="amount">借り入れ予定額</label>
                <select id="amount">
                    <option value="">選択する</option>
                    <option value="10">10万円以下</option>
                    <option value="30">30万円</option>
                    <option value="50">50万円以上</option>
                </select>
            </div>
            <div class="form-group">
                <label for="job">あなたの職業</label>
                <select id="job">
                    <option value="">選択する</option>
                    <option value="student">学生</option>
                    <option value="part">パート・アルバイト</option>
                    <option value="employee">正社員</option>
                </select>
            </div>
            <div class="form-group">
                <label for="purpose">ご希望目的</label>
                <select id="purpose">
                    <option value="">選択する</option>
                    <option value="life">生活費</option>
                    <option value="study">学費</option>
                    <option value="hobby">趣味・娯楽</option>
                    <option value="other">その他</option>
                </select>
            </div>
            <div class="form-group">
                <label for="option">こだわり条件</label>
                <select id="option">
                    <option value="">選択する</option>
                    <option value="sameday">即日融資希望</option>
                    <option value="nointerest">無利息期間あり</option>
                    <option value="web">WEB完結希望</option>
                    <option value="noverify">在籍確認なし</option>
                </select>
            </div>
            <button type="button" id="diagnosisBtn">探す</button>
        </form>
        <div id="diagnosisResult" class="diagnosis-result" style="display:none;"></div>
    </section>

    <script>
        document.getElementById("diagnosisBtn").addEventListener("click", function() {
            const amount = document.getElementById("amount").value;
            const job = document.getElementById("job").value;
            const purpose = document.getElementById("purpose").value;
            const option = document.getElementById("option").value;
            const resultBox = document.getElementById("diagnosisResult");
            resultBox.style.display = "block";

            const results = [{
                    title: "プロミス",
                    match: ["student", "life", "10", "nointerest"],
                    link: "https://cyber.promise.co.jp/BPA01X/BPA01X06_",
                    desc: "学生OK・生活費に対応・無利息期間あり"
                },
                {
                    title: "レイク",
                    match: ["student", "sameday", "nointerest", "study"],
                    link: "https://lakealsa.com/landingpage/",
                    desc: "学生利用可能・即日融資・無利息期間あり"
                },
                {
                    title: "セブン銀行",
                    match: ["student", "10", "life"],
                    link: "https://www.sevenbank.co.jp/oos/adv/tmp_210_04.html",
                    desc: "少額融資に対応・学生利用OK・生活費にも柔軟"
                },
                {
                    title: "アコム",
                    match: ["part", "sameday", "web"],
                    link: "https://www.acom.co.jp/6adbe8b2/index.html",
                    desc: "即日融資対応・パートOK・WEB完結"
                },
                {
                    title: "アイフル",
                    match: ["part", "sameday", "noverify"],
                    link: "https://www.aiful.co.jp/cashing/id16/",
                    desc: "即日融資・在籍確認なし・パートも対応"
                },
                {
                    title: "モビット",
                    match: ["employee", "study", "web", "noverify", "50"],
                    link: "https://www.mobit.ne.jp/pl/al02_macbee/index.html",
                    desc: "正社員向け・在籍確認なし・WEB完結可能"
                },
                {
                    title: "PayPay銀行",
                    match: ["employee", "study", "web"],
                    link: "https://www.paypay-bank.co.jp/cardloan/index.html",
                    desc: "学費・生活費などの目的対応・WEBで完結OK"
                }
            ];

            const matched = results.filter(item =>
                item.match.includes(job) || item.match.includes(amount) ||
                item.match.includes(purpose) || item.match.includes(option)
            );

            if (!matched.length) {
                resultBox.innerHTML = '<p>条件に合致するおすすめカードローンは見つかりませんでした。条件を変更して再度お試しください。</p>';
                return;
            }

            let html = '<h3>🟩 あなたに合ったカードローンはこちら！</h3><ul>';
            matched.slice(0, 3).forEach(item => {
                html += `<li><strong>${item.title}</strong>：${item.desc} ▶ <a href="${item.link}" target="_blank">公式サイト</a></li>`;
            });
            html += '</ul><div class="see-more"><a href="<?php echo esc_url(home_url('/column/money-column/')); ?>">他のカードローンも見る</a></div>';
            resultBox.innerHTML = html;
        });
    </script>
</div>