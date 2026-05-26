/**
 * ヒーロー コラムビュー スライダー
 * hero-column-view.php / assets/css/hero-column.css と対になる
 */
(function () {
    'use strict';

    document.addEventListener('DOMContentLoaded', function () {
        var slider = document.querySelector('.hcv-slider');
        if (!slider) return;

        var slides  = slider.querySelectorAll('.hcv-slide');
        var dots    = slider.querySelectorAll('.hcv-dot');
        var prevBtn = slider.querySelector('.hcv-slider__prev');
        var nextBtn = slider.querySelector('.hcv-slider__next');
        var current = 0;
        var total   = slides.length;
        var timer   = null;
        var AUTO_MS = 5000; // ドットプログレスアニメーション(5s)と一致させる

        if (total <= 1) return;

        function activateDot(index) {
            var dot = dots[index];
            if (!dot) return;
            // is-active を一度外して reflow → 再付与でアニメーションを必ずリスタート
            dot.classList.remove('is-active');
            void dot.offsetWidth;
            dot.classList.add('is-active');
        }

        function activate(index) {
            slides[current].classList.remove('is-active');
            if (dots[current]) dots[current].classList.remove('is-active');

            current = (index + total) % total;

            slides[current].classList.add('is-active');
            activateDot(current);
        }

        function startAuto() {
            timer = setInterval(function () {
                activate(current + 1);
            }, AUTO_MS);
        }

        function stopAuto() {
            clearInterval(timer);
        }

        function resetAuto() {
            stopAuto();
            startAuto();
        }

        // 前へ / 次へ
        if (prevBtn) {
            prevBtn.addEventListener('click', function () {
                activate(current - 1);
                resetAuto();
            });
        }

        if (nextBtn) {
            nextBtn.addEventListener('click', function () {
                activate(current + 1);
                resetAuto();
            });
        }

        // ドット
        dots.forEach(function (dot, i) {
            dot.addEventListener('click', function () {
                activate(i);
                resetAuto();
            });
        });

        // タッチスワイプ（モバイル）
        var touchStartX = 0;
        slider.addEventListener('touchstart', function (e) {
            touchStartX = e.changedTouches[0].clientX;
        }, { passive: true });

        slider.addEventListener('touchend', function (e) {
            var diff = touchStartX - e.changedTouches[0].clientX;
            if (Math.abs(diff) > 40) {
                activate(diff > 0 ? current + 1 : current - 1);
                resetAuto();
            }
        }, { passive: true });

        // ホバーで一時停止 / 離脱で再開（タイマー＋メーターを同期リセット）
        slider.addEventListener('mouseenter', function () {
            slider.classList.add('is-paused');
            stopAuto();
        });

        slider.addEventListener('mouseleave', function () {
            slider.classList.remove('is-paused');
            // メーターをリスタートしてタイマーと同期
            activateDot(current);
            startAuto();
        });

        // 可視領域外に出たらオートストップ（バッテリー節約）
        if ('IntersectionObserver' in window) {
            var obs = new IntersectionObserver(function (entries) {
                if (entries[0].isIntersecting) {
                    activateDot(current);
                    startAuto();
                } else {
                    stopAuto();
                }
            }, { threshold: 0.2 });
            obs.observe(slider);
        } else {
            startAuto();
        }
    });
})();
