<?php
/**
 * Template: フロントページ
 * CSS: style.css（グローバル）, assets/css/support.css
 * JS:  assets/js/chat.js（チャット UI）
 */
get_header(); ?>

<h1 class="sr-only">サクッとお金ナビ｜金融のプロが厳選したカードローン比較・ランキングサイト</h1>

<!-- ヒーローセクション
     切替: 外観 > カスタマイズ > ヒーロービュー設定 -->
<?php
// 管理者向けデバッグ（確認後に削除）
if (current_user_can('manage_options')) {
    $debug_val = get_theme_mod('sakunavi_hero_type', 'original');
    echo '<!-- [HERO DEBUG] sakunavi_hero_type = "' . esc_html($debug_val) . '" -->';
}
// URL パラメータ ?hero=column で管理者のみ強制テスト可能
$hero_type = get_theme_mod('sakunavi_hero_type', 'original');
if (current_user_can('manage_options') && isset($_GET['hero'])) {
    $hero_type = sanitize_key($_GET['hero']);
}
?>
<?php if ($hero_type === 'column'): ?>
    <?php get_template_part('template-parts/hero-column-view'); ?>
<?php else: ?>
<div class="hero-inner">
    <div class="hero-image">
        <img src="<?php echo esc_url(get_template_directory_uri()); ?>/images/index/index_img_01.png" width="822" height="1038" alt="貯金を積み上げている人" fetchpriority="high">
    </div>
    <div class="hero-text">
        <img src="<?php echo esc_url(get_template_directory_uri()); ?>/images/index/index_img_02.png" width="839" height="419" alt="金融のプロが厳選したカードローンランキング">
    </div>
</div>
<?php endif; ?>
<!-- /ヒーローセクション -->
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
                        <li><a href="#" class="tab-nav-item active" data-tab="cf">おすすめ比較</a></li>
                        <li><a href="#" class="tab-nav-item" data-tab="rank">デイリーアクセス<br class="sp-only">TOP3</a></li>
                        <li><a href="#" class="tab-nav-item" data-tab="card">カードローンを<br class="sp-only">探す</a></li>
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
                            $rank_pos = 0;
                            foreach ($rank as $post): setup_postdata($post);
                                $rank_pos++;
                            ?>
                                <div class="rank-card-wrapper rank-card-wrapper--rank-<?php echo $rank_pos; ?>">
                                    <div class="rank-badge rank-badge--rank-<?php echo $rank_pos; ?>"><?php echo $rank_pos; ?>位</div>
                                    <div class="loan-card">
                                        <?php the_content(); ?>
                                    </div>
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

                <section class="popular-keywords-section">
                    <?php get_template_part('template-parts/popular-keywords'); ?>
                </section>

                <!--自分に合ったカードローンの選び方↓-->
                <section class="loan-category">
                    <h2>
                        自分に合った<br class="sp-only">カードローンの選び方
                    </h2>
                    <div class="categoryflex">
                        <div class="category">
                            <a href="https://saku-okane-navi.com/column/381/">
                                <img src="/wp-content/themes/sakunavi-theme/images/index/index_img_04.PNG" alt="パート・主婦向け">
                                <p>パート・主婦向け</p>
                                <span class="arrow">➜</span>
                            </a>
                        </div>
                        <div class="category">
                            <a href="https://saku-okane-navi.com/column/407/">
                                <img src="/wp-content/themes/sakunavi-theme/images/index/index_img_05.PNG" alt="学生・20代向け">
                                <p>学生・20代向け</p>
                                <span class="arrow">➜</span>
                            </a>
                        </div>
                        <div class="category">
                            <a href="https://saku-okane-navi.com/column/417/">
                                <img src="/wp-content/themes/sakunavi-theme/images/index/index_img_06.PNG" alt="会社員向け">
                                <p>会社員向け</p>
                                <span class="arrow">➜</span>
                            </a>
                        </div>
                    </div>
                </section>
                <!--カードローン一覧↓-->

                <?php get_template_part( 'template-parts/loan-comparison-table' ); ?>

                <section class="sim-section">
                    <?php echo do_shortcode('[repayment_simulator_mini]'); ?>
                </section><!-- /.sim-section -->

                <!-- ▼▼ ポップアップバナー：外観 > カスタマイズ > ポップアップバナー設定 から編集できます ▼▼ -->
                <?php if (get_theme_mod('sakunavi_popup_enabled', true)):
                    $popup_image     = get_theme_mod('sakunavi_popup_image', '');
                    $popup_text      = get_theme_mod('sakunavi_popup_text', 'ここにお知らせ・キャンペーン文言が入ります');
                    $popup_btn_label = get_theme_mod('sakunavi_popup_btn_label', '詳しく見る');
                    $popup_btn_url   = get_theme_mod('sakunavi_popup_btn_url', '');
                ?>
                <div id="popupOverlay" class="popup-overlay"></div>
                <div id="popupBanner" class="popup-banner" role="dialog" aria-modal="true" aria-labelledby="popupBannerTitle" aria-hidden="true">
                    <button type="button" class="popup-close" aria-label="閉じる">&times;</button>
                    <div class="popup-content">
                        <?php if ($popup_image): ?>
                            <img src="<?php echo esc_url($popup_image); ?>" alt="">
                        <?php endif; ?>
                        <p id="popupBannerTitle"><?php echo nl2br(esc_html($popup_text)); ?></p>
                        <?php if ($popup_btn_url): ?>
                            <a href="<?php echo esc_url($popup_btn_url); ?>" class="popup-btn"><?php echo esc_html($popup_btn_label); ?></a>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>
                <!-- ▲▲ ポップアップバナー ▲▲ -->

                <script>
                    document.addEventListener("DOMContentLoaded", () => {
                        const popup = document.getElementById("popupBanner");
                        const overlay = document.getElementById("popupOverlay");
                        const closeBtn = popup ? popup.querySelector(".popup-close") : null;
                        if (!popup || !overlay || !closeBtn) return;

                        let lastFocused = null;

                        const openPopup = () => {
                            lastFocused = document.activeElement;
                            popup.style.display = "block";
                            overlay.style.display = "block";
                            popup.setAttribute("aria-hidden", "false");
                            closeBtn.focus();
                        };

                        const closePopup = () => {
                            popup.style.display = "none";
                            overlay.style.display = "none";
                            popup.setAttribute("aria-hidden", "true");
                            if (lastFocused) lastFocused.focus();
                        };

                        // 表示ロジック
                        window.addEventListener("scroll", () => {
                            if (sessionStorage.getItem("popupDisplayed")) return;
                            const scrollRatio = window.scrollY / (document.body.scrollHeight - window.innerHeight);
                            if (scrollRatio > 0.5) {
                                openPopup();
                                sessionStorage.setItem("popupDisplayed", "true");
                            }
                        });

                        // 閉じる処理（×ボタン／オーバーレイクリック／Escキー）
                        closeBtn.addEventListener("click", closePopup);
                        overlay.addEventListener("click", closePopup);
                        document.addEventListener("keydown", (e) => {
                            if (e.key === "Escape" && popup.style.display === "block") closePopup();
                        });
                    });
                </script>

                <!-- ここからチャットセクション -->
                <section class="chat-faq-section">
                    <h2 class="chat-faq-heading">カードローンに関する<br class="sp-only">よくある質問</h2>

                    <?php
                    $user_icon = get_theme_file_uri('assets/img/Q_icon1.png');
                    $op_icon   = get_theme_file_uri('assets/img/Q_icon2.png');
                    ?>

                    <!-- ユーザーの質問 -->
                    <div class="chat-faq chat-faq-question">
                        <div class="chat-faq-bubble">申し込みから融資までどれくらいかかりますか？</div>
                        <span class="chat-faq-icon" style="background-image:url('<?php echo esc_url($user_icon); ?>');"></span>
                    </div>

                    <!-- オペレーターの回答 -->
                    <div class="chat-faq chat-faq-answer">
                        <span class="chat-faq-icon" style="background-image:url('<?php echo esc_url($op_icon); ?>');"></span>
                        <div class="chat-faq-bubble">最短即日での融資が可能です。審査状況によって前後する場合があります。</div>
                    </div>

                    <!-- 2つ目の質問 -->
                    <div class="chat-faq chat-faq-question">
                        <div class="chat-faq-bubble">審査に必要な書類は何ですか？</div>
                        <span class="chat-faq-icon" style="background-image:url('<?php echo esc_url($user_icon); ?>');"></span>
                    </div>

                    <!-- 2つ目の回答 -->
                    <div class="chat-faq chat-faq-answer">
                        <span class="chat-faq-icon" style="background-image:url('<?php echo esc_url($op_icon); ?>');"></span>
                        <div class="chat-faq-bubble">本人確認書類（運転免許証など）と、収入証明書（必要な場合のみ）が必要です。</div>
                    </div>

                    <!-- 3つ目の質問 -->
                    <div class="chat-faq chat-faq-question">
                        <div class="chat-faq-bubble">郵送物が発生することはありますか？</div>
                        <span class="chat-faq-icon" style="background-image:url('<?php echo esc_url($user_icon); ?>');"></span>
                    </div>

                    <!-- 3つ目の回答 -->
                    <div class="chat-faq chat-faq-answer">
                        <span class="chat-faq-icon" style="background-image:url('<?php echo esc_url($op_icon); ?>');"></span>
                        <div class="chat-faq-bubble">通常、契約書や明細書が郵送されることがあります。申し込み方法によって、郵送物が発生しない場合もあります。</div>
                    </div>
                </section><!-- /.chat-faq-section -->

                <!-- ナレッジ よくある質問 トップ10 -->
                <?php
                $fp_top10 = new WP_Query([
                    'post_type'      => 'knowledge',
                    'posts_per_page' => 10,
                    'orderby'        => 'meta_value_num',
                    'order'          => 'ASC',
                    'meta_key'       => 'kn_display_order',
                    'meta_query'     => [[
                        'key'     => 'kn_display_order',
                        'value'   => 0,
                        'compare' => '>=',
                        'type'    => 'NUMERIC',
                    ]],
                ]);

                if ($fp_top10->have_posts()):
                    $faq_pages = get_pages(['meta_key' => '_wp_page_template', 'meta_value' => 'page-knowledge-faq.php', 'number' => 1]);
                    $faq_url   = $faq_pages ? get_permalink($faq_pages[0]->ID) : null;
                ?>
                <section class="fp-kn-faq">
                    <div class="kn-faq-box">
                        <?php while ($fp_top10->have_posts()): $fp_top10->the_post();
                            $pid       = get_the_ID();
                            $one_liner = function_exists('get_field') ? get_field('one_liner',    $pid) : '';
                            $short_ans = function_exists('get_field') ? get_field('short_answer', $pid) : '';
                            $cta_label = function_exists('get_field') ? get_field('kn_cta_label', $pid) : '';
                            $cta_label = $cta_label ?: '詳しく見る';
                        ?>
                        <div class="kn-faq-item">
                            <button class="kn-faq-question" type="button" aria-expanded="false">
                                <span class="kn-faq-question__text"><?php the_title(); ?></span>
                                <span class="kn-faq-icon" aria-hidden="true">+</span>
                            </button>
                            <div class="kn-faq-answer-box" hidden>
                                <?php if ($one_liner): ?>
                                <p><?php echo esc_html($one_liner); ?></p>
                                <?php endif; ?>
                                <?php if ($short_ans): ?>
                                <div><?php echo nl2br(esc_html($short_ans)); ?></div>
                                <?php endif; ?>
                                <p class="kn-faq-more">
                                    <a href="<?php the_permalink(); ?>"><?php echo esc_html($cta_label); ?></a>
                                </p>
                            </div>
                        </div>
                        <?php endwhile; wp_reset_postdata(); ?>
                    </div>

                    <?php if ($faq_url): ?>
                    <div class="fp-kn-faq__footer">
                        <a href="<?php echo esc_url($faq_url); ?>" class="fp-kn-faq__btn">一覧で見る</a>
                    </div>
                    <?php endif; ?>
                </section>
                <?php endif; ?>
                <!-- /ナレッジ よくある質問 トップ10 -->

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