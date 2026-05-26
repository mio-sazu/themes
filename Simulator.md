<!DOCTYPE html>

<html class="light" lang="ja"><head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>サクナビ - 個別会社ページ返済シミュレーター</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&amp;family=Work+Sans:wght@400;500;600&amp;family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
<style>
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }
        :root {
            --tw-chart-principal: #006b2e;
            --tw-chart-interest: #fcbb30;
        }
        .chart-donut {
            background: conic-gradient(var(--tw-chart-principal) 0% 87.5%, var(--tw-chart-interest) 87.5% 100%);
        }
        .glass-card {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(8px);
        }
        @media (max-width: 768px) {
            .table-truncate tr:nth-child(n+5):not(:last-child) {
                display: none;
            }
        }
    </style>
<script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    "colors": {
                        "inverse-primary": "#74dc89",
                        "on-primary": "#ffffff",
                        "on-tertiary-fixed": "#271900",
                        "outline": "#6f7a6d",
                        "on-surface-variant": "#3f4a3e",
                        "tertiary-fixed-dim": "#fcbb30",
                        "outline-variant": "#becabb",
                        "primary": "#006b2e",
                        "on-tertiary-fixed-variant": "#5e4200",
                        "surface-gray": "#F9F9F9",
                        "surface-container-high": "#e8e8e8",
                        "surface-variant": "#e2e2e2",
                        "on-primary-container": "#f7fff3",
                        "on-tertiary": "#ffffff",
                        "surface-container-lowest": "#ffffff",
                        "inverse-on-surface": "#f1f1f1",
                        "on-secondary-container": "#004172",
                        "inverse-surface": "#2f3131",
                        "surface-container": "#eeeeee",
                        "on-surface": "#1a1c1c",
                        "on-secondary-fixed": "#001d37",
                        "accent-yellow": "#FEBD32",
                        "primary-fixed": "#90f9a2",
                        "primary-container": "#13863e",
                        "surface-container-highest": "#e2e2e2",
                        "on-error": "#ffffff",
                        "secondary-fixed": "#d2e4ff",
                        "on-secondary-fixed-variant": "#00497e",
                        "secondary": "#0061a5",
                        "secondary-container": "#66affe",
                        "background": "#f9f9f9",
                        "surface-dim": "#dadada",
                        "surface-tint": "#006e2f",
                        "error-container": "#ffdad6",
                        "error": "#ba1a1a",
                        "on-background": "#1a1c1c",
                        "on-secondary": "#ffffff",
                        "on-primary-fixed": "#002109",
                        "surface-container-low": "#f3f3f3",
                        "on-tertiary-container": "#fffbff",
                        "on-error-container": "#93000a",
                        "surface-bright": "#f9f9f9",
                        "tertiary": "#795600",
                        "trust-green": "#279349",
                        "stable-blue": "#3182CE",
                        "on-primary-fixed-variant": "#005322",
                        "primary-fixed-dim": "#74dc89",
                        "secondary-fixed-dim": "#9fcaff",
                        "surface": "#f9f9f9",
                        "tertiary-fixed": "#ffdea7",
                        "tertiary-container": "#986d00"
                    },
                    "borderRadius": {
                        "DEFAULT": "0.5rem",
                        "lg": "0.5rem",
                        "xl": "0.75rem",
                        "full": "9999px"
                    },
                    "spacing": {
                        "margin-desktop": "48px",
                        "base": "4px",
                        "margin-mobile": "16px",
                        "gutter": "24px",
                        "max-width": "1200px"
                    },
                    "fontFamily": {
                        "headline-xl": ["Plus Jakarta Sans"],
                        "body-lg": ["Work Sans"],
                        "body-sm": ["Work Sans"],
                        "headline-md": ["Plus Jakarta Sans"],
                        "label-sm": ["Work Sans"],
                        "body-md": ["Work Sans"],
                        "label-md": ["Work Sans"],
                        "headline-lg": ["Plus Jakarta Sans"],
                        "headline-lg-mobile": ["Plus Jakarta Sans"],
                        "headline-sm": ["Plus Jakarta Sans"]
                    },
                    "fontSize": {
                        "headline-xl": ["48px", {"lineHeight": "60px", "letterSpacing": "-0.02em", "fontWeight": "700"}],
                        "body-lg": ["18px", {"lineHeight": "28px", "fontWeight": "400"}],
                        "body-sm": ["14px", {"lineHeight": "20px", "fontWeight": "400"}],
                        "headline-md": ["24px", {"lineHeight": "32px", "fontWeight": "600"}],
                        "label-sm": ["12px", {"lineHeight": "14px", "fontWeight": "500"}],
                        "body-md": ["16px", {"lineHeight": "24px", "fontWeight": "400"}],
                        "label-md": ["14px", {"lineHeight": "16px", "letterSpacing": "0.05em", "fontWeight": "600"}],
                        "headline-lg": ["32px", {"lineHeight": "40px", "letterSpacing": "-0.01em", "fontWeight": "700"}],
                        "headline-lg-mobile": ["28px", {"lineHeight": "36px", "fontWeight": "700"}],
                        "headline-sm": ["20px", {"lineHeight": "28px", "fontWeight": "600"}]
                    }
                },
            },
        }
    </script>
</head>
<body class="bg-background text-on-surface font-body-md">
<!-- TopAppBar -->
<header class="bg-surface border-b border-outline-variant docked full-width top-0 z-50 fixed">
<div class="flex justify-between items-center px-margin-mobile h-16 w-full max-w-max-width mx-auto">
<div class="flex items-center gap-2">
<span class="material-symbols-outlined text-trust-green">account_balance</span>
<span class="font-headline-lg-mobile text-headline-lg-mobile font-bold text-on-surface">サクナビ</span>
</div>
<nav class="hidden md:flex gap-6 items-center">
<a class="text-trust-green font-bold text-label-md" href="#">ホーム</a>
<a class="text-on-surface-variant text-label-md hover:bg-surface-container transition-colors px-2 py-1 rounded" href="#">シミュレーション</a>
<a class="text-on-surface-variant text-label-md hover:bg-surface-container transition-colors px-2 py-1 rounded" href="#">ランキング</a>
</nav>
<button class="material-symbols-outlined text-on-surface-variant md:hidden">menu</button>
</div>
</header>
<main class="pt-24 pb-32 px-margin-mobile md:px-margin-desktop max-w-max-width mx-auto">
<!-- Hero Section / Company Summary -->
<section class="mb-12">
<div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-8">
<div>
<h1 class="font-headline-lg text-headline-lg text-on-surface mb-2">返済シミュレーター <span class="text-on-surface-variant font-headline-md text-headline-md block md:inline md:ml-2">会社名：プロミス（仮）</span></h1>
<p class="text-on-surface-variant font-body-md text-body-md">申込前に、将来の返済計画を正確に把握しましょう。</p>
</div>
<div class="flex gap-2">
<span class="bg-primary-container text-on-primary-container px-3 py-1 rounded-full text-label-sm">上限金利 17.8%</span>
<span class="bg-tertiary-fixed text-on-tertiary-fixed px-3 py-1 rounded-full text-label-sm">無利息期間 30日間</span>
</div>
</div>
<!-- Main Interactive Shell -->
<div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
<!-- Input Panel (Left/Sticky) -->
<aside class="lg:col-span-4 h-fit lg:sticky lg:top-24">
<div class="bg-surface-container-lowest p-6 rounded-xl shadow-sm border border-outline-variant">
<h2 class="font-headline-sm text-headline-sm mb-6 flex items-center gap-2">
<span class="material-symbols-outlined text-trust-green">edit_note</span>条件設定
                        </h2>
<div class="space-y-6">
<div>
<label class="block font-label-md text-label-md mb-2">借入希望額</label>
<div class="relative">
<input class="w-full border-outline-variant rounded-lg p-3 font-semibold focus:ring-trust-green focus:border-trust-green" placeholder="金額を入力" type="number" value="300000"/>
<span class="absolute right-4 top-3 text-on-surface-variant">円</span>
</div>
</div>
<div>
<label class="block font-label-md text-label-md mb-2">毎月の返済希望額</label>
<div class="relative">
<input class="w-full border-outline-variant rounded-lg p-3 font-semibold focus:ring-trust-green focus:border-trust-green" placeholder="金額を入力" type="number" value="15000"/>
<span class="absolute right-4 top-3 text-on-surface-variant">円</span>
</div>
<p class="text-label-sm text-on-surface-variant mt-2">最低返済額：12,000円〜</p>
</div>
<div>
<label class="block font-label-md text-label-md mb-2">返済開始月</label>
<input class="w-full border-outline-variant rounded-lg p-3 focus:ring-trust-green focus:border-trust-green" type="month" value="2024-05"/>
</div>
<button class="w-full bg-trust-green text-on-primary font-label-md py-4 rounded-xl shadow-md hover:brightness-110 active:scale-[0.98] transition-all">
                                再計算する
                            </button>
</div>
</div>
<!-- Company Specific Warning -->
<div class="mt-6 p-4 bg-error-container text-on-error-container rounded-lg border border-error/10">
<div class="flex gap-2 items-start">
<span class="material-symbols-outlined shrink-0 text-error">warning</span>
<p class="text-body-sm font-body-sm">
<span class="font-bold">注意点：</span>プロミスではメールアドレス登録とWeb明細利用が無利息期間適用の条件となります。
                            </p>
</div>
</div>
</aside>
<!-- Results Display (Right) -->
<div class="lg:col-span-8 space-y-8">
<!-- Summary Cards Bento -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-4">
<div class="md:col-span-2 bg-primary text-on-primary p-6 rounded-xl flex flex-col justify-between shadow-sm">
<p class="font-label-md opacity-90">総返済額</p>
<div class="flex items-baseline gap-2 mt-2">
<span class="font-headline-xl text-headline-xl">342,850</span>
<span class="font-body-md">円</span>
</div>
<div class="mt-6 pt-4 border-t border-white/20 flex justify-between">
<p class="text-body-sm">利息合計: <span class="font-bold">42,850円</span></p>
<p class="text-body-sm">返済期間: <span class="font-bold">24ヶ月 (2年)</span></p>
</div>
</div>
<div class="bg-surface-container-low p-6 rounded-xl border border-outline-variant flex flex-col items-center justify-center text-center">
<p class="font-label-md text-on-surface-variant mb-2">計画評価</p>
<div class="w-16 h-16 rounded-full border-4 border-trust-green flex items-center justify-center mb-2">
<span class="text-trust-green font-headline-lg font-bold">A</span>
</div>
<p class="text-trust-green font-label-md">理想的</p>
</div>
</div>
<!-- Visualizations -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
<!-- Donut: Ratio -->
<div class="bg-surface-container-lowest p-6 rounded-xl border border-outline-variant shadow-sm">
<h3 class="font-label-md text-label-md mb-6">元金・利息の内訳</h3>
<div class="relative w-48 h-48 mx-auto">
<div class="chart-donut w-full h-full rounded-full"></div>
<div class="absolute inset-4 bg-surface-container-lowest rounded-full flex flex-col items-center justify-center">
<span class="text-label-sm text-on-surface-variant">利息比率</span>
<span class="font-headline-md font-bold text-tertiary-container">12.5%</span>
</div>
</div>
<div class="mt-8 space-y-3">
<div class="flex justify-between items-center text-body-sm">
<span class="flex items-center gap-2"><span class="w-3 h-3 bg-primary rounded-full"></span>借入元金</span>
<span class="font-semibold">300,000円</span>
</div>
<div class="flex justify-between items-center text-body-sm">
<span class="flex items-center gap-2"><span class="w-3 h-3 bg-tertiary-fixed-dim rounded-full"></span>利息合計</span>
<span class="font-semibold">42,850円</span>
</div>
</div>
</div>
<!-- Bar: Benefit Comparison -->
<div class="bg-surface-container-lowest p-6 rounded-xl border border-outline-variant shadow-sm">
<h3 class="font-label-md text-label-md mb-6">無利息期間のメリット</h3>
<div class="space-y-6">
<div>
<div class="flex justify-between text-label-sm mb-2">
<span>無利息あり</span>
<span class="font-bold text-trust-green">-4,380円の節約</span>
</div>
<div class="h-8 bg-surface-container-low rounded-full overflow-hidden relative">
<div class="h-full bg-trust-green w-[85%]"></div>
</div>
</div>
<div>
<div class="flex justify-between text-label-sm mb-2">
<span>無利息なし</span>
<span class="text-on-surface-variant">通常時</span>
</div>
<div class="h-8 bg-surface-container rounded-full overflow-hidden">
<div class="h-full bg-outline-variant w-full"></div>
</div>
</div>
</div>
<p class="mt-6 text-body-sm text-on-surface-variant bg-surface p-4 rounded-lg">
                                初回30日間の金利が0円になることで、通常の借入に比べ約<span class="font-bold text-trust-green">4,380円</span>利息を抑えられます。
                            </p>
</div>
</div>
<!-- Comparison with Minimum Payment -->
<div class="bg-tertiary-container text-on-primary-container p-6 rounded-xl border border-tertiary/20">
<h3 class="font-headline-sm text-headline-sm mb-2 flex items-center gap-2">
<span class="material-symbols-outlined">trending_down</span>最低返済額との比較
                        </h3>
<p class="text-body-sm mb-6 opacity-90">最低返済額（12,000円）で返済し続けた場合と比較：</p>
<div class="flex flex-wrap gap-4">
<div class="bg-white/10 backdrop-blur-sm px-6 py-3 rounded-xl">
<p class="text-label-sm opacity-80 mb-1">完済までの期間</p>
<p class="font-bold text-headline-sm">8ヶ月短縮</p>
</div>
<div class="bg-white/10 backdrop-blur-sm px-6 py-3 rounded-xl">
<p class="text-label-sm opacity-80 mb-1">削減できる利息</p>
<p class="font-bold text-headline-sm">18,200円削減</p>
</div>
</div>
</div>
<!-- Time Table -->
<div class="bg-surface-container-lowest rounded-xl border border-outline-variant overflow-hidden shadow-sm">
<div class="p-4 bg-surface-container-low border-b border-outline-variant flex justify-between items-center">
<h3 class="font-label-md text-label-md">返済スケジュール（抜粋）</h3>
<button class="text-trust-green font-bold text-label-sm">全て表示</button>
</div>
<div class="overflow-x-auto">
<table class="w-full text-left table-truncate">
<thead class="bg-surface-container-low text-label-sm text-on-surface-variant">
<tr>
<th class="px-4 py-4">回数</th>
<th class="px-4 py-4">年月</th>
<th class="px-4 py-4">返済額</th>
<th class="px-4 py-4">元金充当</th>
<th class="px-4 py-4">利息</th>
<th class="px-4 py-4">残高</th>
</tr>
</thead>
<tbody class="text-body-sm divide-y divide-outline-variant">
<tr class="hover:bg-surface-container transition-colors">
<td class="px-4 py-4">1回</td>
<td class="px-4 py-4">2024/05</td>
<td class="px-4 py-4">15,000</td>
<td class="px-4 py-4">15,000</td>
<td class="px-4 py-4 text-trust-green font-bold">0</td>
<td class="px-4 py-4">285,000</td>
</tr>
<tr class="hover:bg-surface-container transition-colors">
<td class="px-4 py-4">2回</td>
<td class="px-4 py-4">2024/06</td>
<td class="px-4 py-4">15,000</td>
<td class="px-4 py-4">10,777</td>
<td class="px-4 py-4">4,223</td>
<td class="px-4 py-4">274,223</td>
</tr>
<tr class="hover:bg-surface-container transition-colors">
<td class="px-4 py-4">3回</td>
<td class="px-4 py-4">2024/07</td>
<td class="px-4 py-4">15,000</td>
<td class="px-4 py-4">10,937</td>
<td class="px-4 py-4">4,063</td>
<td class="px-4 py-4">263,286</td>
</tr>
<tr class="bg-surface-container-lowest">
<td class="px-4 py-3 text-center text-on-surface-variant font-body-sm italic" colspan="6">... 以下省略 ...</td>
</tr>
<tr class="hover:bg-surface-container transition-colors bg-surface-container font-bold">
<td class="px-4 py-4">24回</td>
<td class="px-4 py-4">2026/04</td>
<td class="px-4 py-4">7,850</td>
<td class="px-4 py-4">7,734</td>
<td class="px-4 py-4">116</td>
<td class="px-4 py-4 text-trust-green">0</td>
</tr>
</tbody>
</table>
</div>
</div>
<!-- Checklist -->
<div class="bg-surface-container-lowest p-6 rounded-xl border border-outline-variant shadow-sm">
<h3 class="font-headline-sm text-headline-sm mb-6 flex items-center gap-2 text-on-surface">
<span class="material-symbols-outlined text-trust-green">check_circle</span>申込前チェックリスト
                        </h3>
<div class="space-y-4">
<label class="flex items-start gap-4 p-4 hover:bg-surface-container-low rounded-xl cursor-pointer transition-colors border border-transparent hover:border-outline-variant">
<input class="mt-1 rounded text-trust-green focus:ring-trust-green w-5 h-5" type="checkbox"/>
<span class="text-body-md">毎月の返済額（15,000円）が生活費を圧迫しないか確認した</span>
</label>
<label class="flex items-start gap-4 p-4 hover:bg-surface-container-low rounded-xl cursor-pointer transition-colors border border-transparent hover:border-outline-variant">
<input class="mt-1 rounded text-trust-green focus:ring-trust-green w-5 h-5" type="checkbox"/>
<span class="text-body-md">無利息期間の適用条件（Web明細など）を理解した</span>
</label>
<label class="flex items-start gap-4 p-4 hover:bg-surface-container-low rounded-xl cursor-pointer transition-colors border border-transparent hover:border-outline-variant">
<input class="mt-1 rounded text-trust-green focus:ring-trust-green w-5 h-5" type="checkbox"/>
<span class="text-body-md">借入総額が年収の3分の1を超えていないか確認した（総量規制）</span>
</label>
</div>
</div>
</div>
</div>
</section>
<!-- Final CTA Section -->
<section class="mt-20 text-center space-y-8 max-w-2xl mx-auto">
<h2 class="font-headline-md text-headline-md text-on-surface">シミュレーション結果に納得できましたか？</h2>
<p class="text-on-surface-variant text-body-lg">公式サイトではさらに詳細なリアルタイム審査が可能です。</p>
<div class="flex flex-col sm:flex-row gap-4 justify-center">
<a class="inline-flex items-center justify-center bg-accent-yellow text-on-tertiary-fixed px-10 py-5 rounded-xl font-bold text-body-lg shadow-lg hover:brightness-110 active:scale-[0.98] transition-all w-full sm:w-auto" href="#">
                    公式サイトで最新条件を確認する
                    <span class="material-symbols-outlined ml-2">open_in_new</span>
</a>
</div>
<p class="text-label-sm text-on-surface-variant opacity-70">※ここから先は提携先の公式サイトへ移動します。</p>
</section>
</main>
<!-- BottomNavBar (Mobile Only) -->
<nav class="md:hidden bg-surface-container-lowest border-t border-outline-variant fixed bottom-0 w-full z-50">
<div class="flex justify-around items-center h-20 px-4 w-full">
<a class="flex flex-col items-center justify-center text-on-surface-variant p-2 active:scale-95 transition-transform" href="#">
<span class="material-symbols-outlined">home</span>
<span class="text-label-sm mt-1">ホーム</span>
</a>
<a class="flex flex-col items-center justify-center text-trust-green font-bold p-2 active:scale-95 transition-transform" href="#">
<span class="material-symbols-outlined">calculate</span>
<span class="text-label-sm mt-1">計算</span>
</a>
<a class="flex flex-col items-center justify-center text-on-surface-variant p-2 active:scale-95 transition-transform" href="#">
<span class="material-symbols-outlined">leaderboard</span>
<span class="text-label-sm mt-1">ランキング</span>
</a>
<a class="flex flex-col items-center justify-center text-on-surface-variant p-2 active:scale-95 transition-transform" href="#">
<span class="material-symbols-outlined">bookmark</span>
<span class="text-label-sm mt-1">保存</span>
</a>
</div>
</nav>
<!-- Footer -->
<footer class="bg-surface-container-low py-16 px-4 border-t border-outline-variant mb-20 md:mb-0">
<div class="max-w-max-width mx-auto text-center space-y-6">
<div class="flex justify-center items-center gap-2 mb-4">
<span class="material-symbols-outlined text-trust-green">account_balance</span>
<span class="font-headline-md font-bold text-on-surface">サクナビ</span>
</div>
<p class="text-label-sm text-on-surface-variant max-w-xl mx-auto leading-relaxed">
                当シミュレーションの結果はあくまで目安であり、実際の契約条件とは異なる場合があります。詳細は必ず各金融機関の公式サイトにてご確認ください。
            </p>
<div class="flex justify-center gap-6 text-label-sm text-trust-green font-bold">
<a class="hover:underline" href="#">運営会社</a>
<a class="hover:underline" href="#">プライバシーポリシー</a>
<a class="hover:underline" href="#">利用規約</a>
</div>
<p class="text-xs text-on-surface-variant opacity-60">© 2024 Sakunavi Financial Solutions.</p>
</div>
</footer>
<script>
        // Micro-interactions for number inputs
        document.querySelectorAll('input[type="number"]').forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.classList.add('ring-2', 'ring-trust-green/20');
            });
            input.addEventListener('blur', function() {
                this.parentElement.classList.remove('ring-2', 'ring-trust-green/20');
            });
        });

        // Floating action button simple logic
        const scrollBtn = document.createElement('button');
        scrollBtn.className = "fixed right-6 bottom-24 md:bottom-10 bg-trust-green text-on-primary w-12 h-12 rounded-full shadow-xl flex items-center justify-center active:scale-90 transition-all opacity-0 pointer-events-none z-40";
        scrollBtn.innerHTML = '<span class="material-symbols-outlined">arrow_upward</span>';
        document.body.appendChild(scrollBtn);

        window.addEventListener('scroll', () => {
            if (window.scrollY > 500) {
                scrollBtn.classList.add('opacity-100', 'pointer-events-auto');
            } else {
                scrollBtn.classList.remove('opacity-100', 'pointer-events-auto');
            }
        });

        scrollBtn.onclick = () => window.scrollTo({top: 0, behavior: 'smooth'});
    </script>
</body></html>