(function () {
  'use strict';

  // ── アコーディオン（.kn-faq-item / .kn-faq-question / .kn-faq-answer-box）────
  document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.kn-faq-item').forEach(function (item) {
      var btn    = item.querySelector('.kn-faq-question');
      var answer = item.querySelector('.kn-faq-answer-box');
      if (!btn || !answer) return;

      answer.hidden = true;
      btn.setAttribute('aria-expanded', 'false');

      btn.addEventListener('click', function () {
        var isOpen = btn.getAttribute('aria-expanded') === 'true';
        btn.setAttribute('aria-expanded', isOpen ? 'false' : 'true');
        answer.hidden = isOpen;
        item.classList.toggle('is-open', !isOpen);

        var icon = btn.querySelector('.kn-faq-icon');
        if (icon) {
          icon.textContent = isOpen ? '+' : '−';
        }
      });
    });

    // ── 検索フィルター ──────────────────────────────────────────────
    var searchInput = document.getElementById('kn-faq-search-input');
    if (!searchInput) return;

    var topSection  = document.getElementById('kn-faq-top');
    var listSection = document.getElementById('kn-faq-list');
    var noResults   = document.getElementById('kn-faq-no-results');
    var resultMsg   = document.getElementById('kn-faq-search-result');

    searchInput.addEventListener('input', function () {
      var kw = this.value.trim().toLowerCase();

      if (!kw) {
        resetVisibility();
        if (resultMsg) { resultMsg.hidden = true; }
        if (noResults)  { noResults.hidden = true; }
        return;
      }

      var totalVisible = 0;

      // トップ10アコーディオンをフィルター
      if (topSection) {
        var topItems   = topSection.querySelectorAll('.kn-faq-item');
        var topVisible = 0;
        topItems.forEach(function (item) {
          var title = (item.dataset.searchTitle || '');
          var body  = (item.dataset.searchBody  || '');
          var match = title.includes(kw) || body.includes(kw);
          item.hidden = !match;
          if (match) topVisible++;
        });
        topSection.hidden = topVisible === 0;
        totalVisible += topVisible;
      }

      // 全一覧をフィルター
      if (listSection) {
        var catGroups   = listSection.querySelectorAll('.kn-faq-cat-group');
        var listVisible = 0;
        catGroups.forEach(function (group) {
          var items        = group.querySelectorAll('.kn-faq-all-item');
          var groupVisible = 0;
          items.forEach(function (item) {
            var title = (item.dataset.searchTitle || '');
            var body  = (item.dataset.searchBody  || '');
            var match = title.includes(kw) || body.includes(kw);
            item.hidden = !match;
            if (match) groupVisible++;
          });
          group.hidden = groupVisible === 0;
          listVisible += groupVisible;
        });
        totalVisible += listVisible;

        if (noResults) {
          noResults.hidden = totalVisible > 0;
        }
      }

      if (resultMsg) {
        if (totalVisible > 0) {
          resultMsg.textContent = '「' + searchInput.value.trim() + '」の検索結果：' + totalVisible + '件';
          resultMsg.hidden = false;
        } else {
          resultMsg.hidden = true;
        }
      }
    });

    function resetVisibility() {
      if (topSection) {
        topSection.hidden = false;
        topSection.querySelectorAll('.kn-faq-item').forEach(function (el) { el.hidden = false; });
      }
      if (listSection) {
        listSection.querySelectorAll('.kn-faq-cat-group').forEach(function (el) { el.hidden = false; });
        listSection.querySelectorAll('.kn-faq-all-item').forEach(function (el) { el.hidden = false; });
      }
    }
  });
})();
