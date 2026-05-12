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



