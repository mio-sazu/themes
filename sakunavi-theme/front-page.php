<?php get_header(); ?>


<!-- ヒーローセクション -->
<div class="hero-inner">
    <div class="hero-image">
        <img src="/wp-content/themes/sakunavi-theme/images/index/index_img_01.png" alt="貯金を積み上げている人">
    </div>
    <div class="hero-text">
        <img src="/wp-content/themes/sakunavi-theme/images/index/index_img_02.png" alt="金融のプロが厳選したカードローンランキング">
    </div>
</div>
<!-- ヒーローセクション -->
<?php
// スライダー
get_template_part('template-parts/slider');
?>

<!-- ここに他のセクション（loan-category, loan-table, sim-section, faq-section など）を static HTML で貼るか、
            必要に応じてカスタムループ化して呼び出してください -->
<div class="wrapper">
    <article>
        <div class="layout">
            <?php get_sidebar(); ?>
            <main>
                <!-- ここからタブ表示 -->
                <section id="tab" class="tab">
                    <!-- タブ見出し -->
                    <ul class="tab-nav">
                        <li><a href="#cf" class="tab-nav-item active" data-tab="cf">おすすめ比較</a></li>
                        <li><a href="#rank" class="tab-nav-item" data-tab="rank">ランキングTOP3</a></li>
                        <li><a href="#card" class="tab-nav-item" data-tab="card">カードローンを探す</a></li>
                    </ul>

                    <!-- タブパネル -->
                    <div class="tab-panels">
                        <!-- 1) おすすめ比較：ランダム3件 -->
                        <div id="cf" class="tab-panel active">
                            <?php
                            $cf = get_posts([
                                'post_type'      => 'tab_content',
                                'posts_per_page' => 3,
                                'orderby'        => 'rand',
                                'tax_query'      => [[
                                    'taxonomy' => 'tab_category',
                                    'field'    => 'slug',
                                    'terms'    => 'recommend',
                                ]],
                            ]);
                            foreach ($cf as $post): setup_postdata($post); ?>
                                <div class="loan-card">
                                    <?php the_content(); // editor にHTML張り付け済みならそのまま 
                                    ?>
                                </div>
                            <?php endforeach;
                            wp_reset_postdata(); ?>
                        </div>

                        <!-- 2) ランキングTOP3：管理UIで順序を指定 -->
                        <div id="rank" class="tab-panel">
                            <?php
                            $rank = get_posts([
                                'post_type'      => 'tab_content',
                                'posts_per_page' => 3,
                                'orderby'        => 'menu_order',
                                'order'          => 'ASC',
                                'tax_query'      => [[
                                    'taxonomy' => 'tab_category',
                                    'field'    => 'slug',
                                    'terms'    => 'ranking-top3',
                                ]],
                            ]);
                            foreach ($rank as $post): setup_postdata($post); ?>
                                <div class="loan-card">
                                    <?php the_content(); ?>
                                </div>
                            <?php endforeach;
                            wp_reset_postdata(); ?>
                        </div>

                        <!-- 3) カードローンを探す：現行の診断フォーム -->
                        <div id="card" class="tab-panel">
                            <?php get_template_part('template-parts/loan-diagnosis'); ?>
                        </div>

                    </div>
                </section>
                <!-- ここまでタブ表示 -->

                <!--自分に合ったカードローンの選び方↓-->
                <section class="loan-category">
                    <h2>
                        自分に合ったカードローンの選び方
                    </h2>
                    <div class="categoryflex">
                        <div class="category">
                            <a href="article/cardloan-housewife.html">
                                <img src="/wp-content/themes/sakunavi-theme/images/index/index_img_04.PNG" alt="パート・主婦向け">
                                <p>パート・主婦向け</p>
                                <span class="arrow">➜</span>
                            </a>
                        </div>
                        <div class="category">
                            <a href="article/cardloan-student.html">
                                <img src="/wp-content/themes/sakunavi-theme/images/index/index_img_05.PNG" alt="学生・20代向け">
                                <p>学生・20代向け</p>
                                <span class="arrow">➜</span>
                            </a>
                        </div>
                        <div class="category">
                            <a href="article/cardloan-worker.html">
                                <img src="/wp-content/themes/sakunavi-theme/images/index/index_img_06.PNG" alt="会社員向け">
                                <p>会社員向け</p>
                                <span class="arrow">➜</span>
                            </a>
                        </div>
                    </div>
                </section>
                <!--カードローン一覧↓-->
                <section class="loan-table">
                    <h2>カードローン比較早見表</h2>
                    <table>
                        <thead>
                            <tr>
                                <th>カード</th>
                                <th>限度額</th>
                                <th>最短審査</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <a href="">
                                        SMBCモビット
                                    </a>
                                </td>
                                <td>
                                    最大800万円まで※1
                                </td>
                                <td>
                                    審査は最短15分<br>10秒で簡易審査
                                </td>
                                <td>
                                    <a href="#" class="apply-btnred">
                                        申し込む
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <a href="">
                                        プロミス
                                    </a>
                                </td>
                                <td>
                                    最大500万円まで※1
                                </td>
                                <td>
                                    審査は最短3分※3
                                </td>
                                <td>
                                    <a href="#" class="apply-btnred">
                                        申し込む
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <a href="">
                                        アイフル
                                    </a>
                                </td>
                                <td>
                                    最大800万円まで※2
                                </td>
                                <td>
                                    審査最短18分※3
                                </td>
                                <td>
                                    <a href="#" class="apply-btnred">
                                        申し込む
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <a href="">
                                        アコム
                                    </a>
                                </td>
                                <td>
                                    1万～800万
                                </td>
                                <td>
                                    最短20分※3
                                </td>
                                <td>
                                    <a href="#" class="apply-btnred">
                                        申し込む
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <a href="">
                                        三井住友銀行
                                    </a>
                                </td>
                                <td>
                                    10万円～800万円まで
                                </td>
                                <td>
                                    最短当日
                                </td>
                                <td>
                                    <a href="#" class="apply-btnred">
                                        申し込む
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <a href="">
                                        住信SBIネット銀行
                                    </a>
                                </td>
                                <td>
                                    10万円～1000万円
                                </td>
                                <td>
                                    記載なし
                                </td>
                                <td>
                                    <a href="#" class="apply-btnred">
                                        申し込む
                                    </a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <div>
                        <p>
                            ※1借入限度額は審査によって決定致します。
                        </p>
                        <p>
                            ※2ご利用限度額50万円越、または他社を含めた借り入れ金額が100万越えの場合は源泉徴収票など収入を証明するものが必要です。
                        </p>
                        <p>
                            ※3お申込み時間や審査状況によりご希望にそえない場合があります。
                        </p>
                        <p>
                            ※4審査の結果、ご希望の限度額を減額させていただく場合もあります。
                        </p>
                        <p>
                            ※5新規ご契約の方ご利用可能金額50万円まで
                        </p>
                    </div>
                </section><!-- /.loan-table -->
                <section class="sim-section">
                    <div class="sim-box">
                        <div class="sim-header">返済シミュレーション</div>
                        <table class="sim-table">
                            <tr>
                                <th>借り入れ予定額</th>
                                <td>
                                    <select>
                                        <option>選択してください</option>
                                        <option>10万円</option>
                                        <option>30万円</option>
                                        <option>50万円</option>
                                        <option>100万円</option>
                                        <option>200万円</option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <th>希望返済期間</th>
                                <td>
                                    <select>
                                        <option>選択してください</option>
                                        <option>3ヶ月</option>
                                        <option>6ヶ月</option>
                                        <option>1年</option>
                                        <option>2年</option>
                                        <option>3年</option>
                                        <option>5年</option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <th>金利</th>
                                <td>
                                    <select>
                                        <option>選択してください</option>
                                        <option>3%</option>
                                        <option>10%</option>
                                        <option>15%</option>
                                        <option>18%</option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <th>目的</th>
                                <td>
                                    <select>
                                        <option>選択してください</option>
                                        <option>生活費</option>
                                        <option>学費</option>
                                        <option>旅行・趣味</option>
                                        <option>その他</option>
                                    </select>
                                </td>
                            </tr>
                        </table>
                        <!-- 結果表示エリア（最初は非表示） -->
                        <div id="simResult" style="display:none; margin-top: 20px; padding: 15px; background: #f0f9ff; border: 2px solid #87b5ff; border-radius: 8px; font-weight: bold;"></div>
                        <!-- ボタン -->
                        <div class="sim-button" style="margin-top: 20px;">
                            <button id="calculateBtn">シミュレーションする</button>
                        </div>
                    </div>
                    <script>
                        document.addEventListener("DOMContentLoaded", function() {
                            const resultBox = document.getElementById("simResult");

                            document.getElementById("calculateBtn").addEventListener("click", function() {
                                const amount = getNumberFromSelect("借り入れ予定額");
                                const termText = getSelectText("希望返済期間");
                                const rate = getNumberFromSelect("金利");

                                const months = getMonthsFromText(termText);

                                if (!amount || !months || !rate) {
                                    alert("借り入れ額・返済期間・金利をすべて選択してください。");
                                    return;
                                }

                                const monthlyRate = rate / 100 / 12;
                                const monthlyPayment = amount * (monthlyRate * Math.pow(1 + monthlyRate, months)) / (Math.pow(1 + monthlyRate, months) - 1);
                                const totalRepayment = monthlyPayment * months;

                                const result = `
                                    📌 毎月の返済額：${(monthlyPayment / 10000).toFixed(1)}万円（全${months}回）<br>
                                    💰 総返済額：${(totalRepayment / 10000).toFixed(1)}万円
                                    `;

                                resultBox.innerHTML = result;
                                resultBox.style.display = "block";
                            });

                            // ▼ 各セレクトボックスから数値を取得（万円→円に変換）
                            function getNumberFromSelect(labelText) {
                                const rows = document.querySelectorAll(".sim-table tr");
                                for (const row of rows) {
                                    if (row.querySelector("th")?.textContent.trim() === labelText) {
                                        const select = row.querySelector("select");
                                        const value = select.value.replace(/[^\d.]/g, "");
                                        const num = Number(value);

                                        if (labelText === "借り入れ予定額") {
                                            return num * 10000; // 万円 → 円
                                        } else if (labelText === "金利") {
                                            return num; // パーセントのまま
                                        } else {
                                            return num;
                                        }
                                    }
                                }
                                return null;
                            }


                            // ▼ テキスト（期間など）を取得
                            function getSelectText(labelText) {
                                const rows = document.querySelectorAll(".sim-table tr");
                                for (const row of rows) {
                                    if (row.querySelector("th")?.textContent.trim() === labelText) {
                                        const select = row.querySelector("select");
                                        return select.value.trim();
                                    }
                                }
                                return null;
                            }

                            // ▼ 「○ヶ月」や「○年」から月数を計算
                            function getMonthsFromText(text) {
                                if (text.includes("ヶ月")) return parseInt(text.replace("ヶ月", ""));
                                if (text.includes("年")) return parseInt(text.replace("年", "")) * 12;
                                return null;
                            }
                        });
                    </script>
                </section><!-- /.sim-section -->
                <script>
                    document.addEventListener("DOMContentLoaded", () => {
                        const popup = document.getElementById("popupBanner");
                        const overlay = document.getElementById("popupOverlay");
                        const closeBtn = document.querySelector(".popup-close");

                        // 表示ロジック
                        window.addEventListener("scroll", () => {
                            if (sessionStorage.getItem("popupDisplayed")) return;
                            const scrollRatio = window.scrollY / (document.body.scrollHeight - window.innerHeight);
                            if (scrollRatio > 0.5) {
                                popup.style.display = "block";
                                overlay.style.display = "block";
                                sessionStorage.setItem("popupDisplayed", "true");
                            }
                        });

                        // 閉じる処理
                        closeBtn.addEventListener("click", () => {
                            popup.style.display = "none";
                            overlay.style.display = "none";
                        });
                    });
                </script>

                <!-- ここからチャットセクション -->
                <section class="faq-section">
                    <h2 class="faq-heading">カードローンに関するよくある質問</h2>

                    <!-- ユーザーの質問 -->
                    <div class="faq-chat faq-question">
                        <div class="faq-bubble">申し込みから融資までどれくらいかかりますか？</div>
                        <span class="faq-icon" style="background-image: url('user-icon.png');"></span>
                    </div>

                    <!-- オペレーターの回答 -->
                    <div class="faq-chat faq-answer">
                        <span class="faq-icon" style="background-image: url('operator-icon.png');"></span>
                        <div class="faq-bubble">最短即日での融資が可能です。審査状況によって前後する場合があります。</div>
                    </div>

                    <!-- 2つ目の質問 -->
                    <div class="faq-chat faq-question">
                        <div class="faq-bubble">審査に必要な書類は何ですか？</div>
                        <span class="faq-icon" style="background-image: url('user-icon.png');"></span>
                    </div>

                    <!-- 2つ目の回答 -->
                    <div class="faq-chat faq-answer">
                        <span class="faq-icon" style="background-image: url('operator-icon.png');"></span>
                        <div class="faq-bubble">本人確認書類（運転免許証など）と、収入証明書（必要な場合のみ）が必要です。</div>
                    </div>

                    <!-- 3つ目の質問 -->
                    <div class="faq-chat faq-question">
                        <div class="faq-bubble">郵送物が発生することはありますか？</div>
                        <span class="faq-icon" style="background-image: url('user-icon.png');"></span>
                    </div>

                    <!-- 3つ目の回答 -->
                    <div class="faq-chat faq-answer">
                        <span class="faq-icon" style="background-image: url('operator-icon.png');"></span>
                        <div class="faq-bubble">通常、契約書や明細書が郵送されることがあります。申し込み方法によって、郵送物が発生しない場合もあります。</div>
                    </div>
                </section>
            </main>
            <!--
            <div class="chat-section">
                <h2>みんなでチャット</h2>
                <div class="chat-container"></div>

                <form id="chatForm">
                    <input type="text"
                        id="chatInput"
                        placeholder="メッセージを入力…"
                        autocomplete="off"
                        required>
                    <button type="submit">送信</button>
                </form>
            </div> -->
            <!-- ここまでチャットセクション -->
        </div>
    </article>
</div><!-- #Wrapper*end -->


<?php get_footer(); ?>