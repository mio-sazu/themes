/*////////メインタブ////////*/
document.addEventListener('DOMContentLoaded', () => {
    const tabs   = document.querySelectorAll('.tab-nav-item');
    const panels = document.querySelectorAll('.tab-panel');

    function activateTab(tab) {
        tabs.forEach(t => t.classList.remove('active'));
        panels.forEach(p => p.classList.remove('active'));
        tab.classList.add('active');
        document.getElementById(tab.dataset.tab).classList.add('active');
    }

    tabs.forEach(tab => {
        tab.addEventListener('click', e => {
        e.preventDefault();
        activateTab(tab);
        });
    });

    // 初期表示
    if (tabs.length) activateTab(tabs[0]);
    });


/*////////ページ読み込み時にスクロール位置をリセットする////////*/
window.addEventListener("pageshow", function (event) {
    if (event.persisted || (window.performance && window.performance.navigation.type === 2)) {
      window.scrollTo(0, 0);
    }
  });

/*////////返済シュミレーション////////*/






/* --- Added by child theme: robust tab switcher --- */
document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('[data-tabs]').forEach(root => {
    const btns   = root.querySelectorAll('.tab-btn');
    const panels = root.querySelectorAll('.tab-panel, [data-panel]');
    function activateById(id, pushHash = false){
      btns.forEach(b => {
        const tgt = b.dataset.target || b.getAttribute('href') || '';
        const on  = tgt.replace(/^#/, '') === id;
        b.classList.toggle('active', on);
        b.setAttribute('aria-selected', on ? 'true' : 'false');
        b.setAttribute('tabindex', on ? '0' : '-1');
      });
      panels.forEach(p => {
        const on = p.id === id;
        p.classList.toggle('active', on);
        p.hidden = !on;
      });
      if (pushHash) { try { history.replaceState(null, '', '#' + id); } catch(e){} }
    }
    btns.forEach(b => {
      b.addEventListener('click', e => {
        const tgt = b.dataset.target || b.getAttribute('href');
        if (!tgt) return;
        if (b.tagName.toLowerCase() === 'a') e.preventDefault();
        activateById(tgt.replace(/^#/, ''), true);
      });
      b.addEventListener('keydown', e => {
        if (e.key !== 'ArrowRight' && e.key !== 'ArrowLeft') return;
        const arr = Array.from(btns);
        const i   = arr.indexOf(b);
        const dir = e.key === 'ArrowRight' ? 1 : -1;
        const next = arr[(i + dir + arr.length) % arr.length];
        next.focus();
        next.click();
      });
    });
    const initial = (location.hash || '').replace('#','') || (panels[0] && panels[0].id);
    if (initial) activateById(initial, false);
  });
});

/*//////// 比較表スクロール案内 ////////*/
document.addEventListener('DOMContentLoaded', function () {
  const wraps = document.querySelectorAll('.js-scroll-hint');

  wraps.forEach(function (wrap) {
    const area = wrap.querySelector('.table-scroll-area');
    if (!area) return;

    const isScrollable = area.scrollWidth > area.clientWidth;
    if (!isScrollable) return;

    area.classList.add('is-scrollable');

    area.addEventListener('scroll', function () {
      if (area.scrollLeft > 8) {
        wrap.classList.add('is-scrolled');
      }
    }, { passive: true });
  });
});