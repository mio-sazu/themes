(function () {
  "use strict";
  var root = document.getElementById('ldx');
  if (!root || root.dataset.ldxInit) return;
  root.dataset.ldxInit = '1';

  var COMPANIES = (window.ldxData && Array.isArray(window.ldxData.companies)) ? window.ldxData.companies : [];
  var resultEl = document.getElementById('ldxResult');
  var submitBtn = document.getElementById('ldxSubmit');

  var answers = { amount: '', job: '', conditions: [] };

  // 「低金利重視」「高額融資に対応」は専用フィールドを持たないため、
  // 各社の実質年率下限・融資限度額の目安ラインで判定する。
  var LOW_RATE_MAX = 4.5;   // rateMin がこれ以下なら「低金利」とみなす
  var HIGH_LIMIT_MIN = 500; // limitMax（万円）がこれ以上なら「高額融資対応」とみなす

  function fmt(n) { return Math.round(n).toLocaleString('ja-JP'); }
  function esc(s) { return String(s).replace(/[&<>"]/g, function (c) { return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;' }[c]; }); }

  // ---- 選択UI ----
  root.querySelectorAll('.ldx-pills').forEach(function (group) {
    var name = group.getAttribute('data-group');
    var multi = group.getAttribute('data-multi') === '1';

    group.addEventListener('click', function (e) {
      var btn = e.target.closest('.ldx-pill');
      if (!btn) return;
      var value = btn.getAttribute('data-value');

      if (multi) {
        var idx = answers.conditions.indexOf(value);
        if (idx === -1) { answers.conditions.push(value); btn.classList.add('is-selected'); }
        else { answers.conditions.splice(idx, 1); btn.classList.remove('is-selected'); }
      } else {
        var already = answers[name] === value;
        group.querySelectorAll('.ldx-pill').forEach(function (b) { b.classList.remove('is-selected'); });
        answers[name] = already ? '' : value;
        if (!already) btn.classList.add('is-selected');
      }
    });
  });

  // ---- マッチ度スコアリング ----
  function scoreCompany(c) {
    var total = 0, max = 0;

    if (answers.amount) {
      max++;
      var amt = Number(answers.amount);
      var lo = (c.limitMin !== null && c.limitMin !== undefined) ? c.limitMin : 0;
      var hi = (c.limitMax !== null && c.limitMax !== undefined) ? c.limitMax : Infinity;
      if (amt >= lo && amt <= hi) total++;
    }

    if (answers.job) {
      max++;
      if (answers.job === 'student' && c.studentOk) total++;
      else if (answers.job === 'parttime' && c.parttimeOk) total++;
      else if (answers.job === 'employee') total++; // 正社員はほぼ全社対応のため加点のみ
    }

    answers.conditions.forEach(function (cond) {
      max++;
      if (cond === 'sameday' && c.samedayOk) total++;
      else if (cond === 'nointerest' && c.freeDays > 0) total++;
      else if (cond === 'web' && c.webOnly) total++;
      else if (cond === 'noverify' && c.noVerify) total++;
      else if (cond === 'lowrate' && c.rateMin <= LOW_RATE_MAX) total++;
      else if (cond === 'highlimit' && c.limitMax !== null && c.limitMax >= HIGH_LIMIT_MIN) total++;
      else if (cond === 'housewife' && c.housewifeOk) total++;
      else if (cond === 'cardless' && c.cardlessOk) total++;
      else if (cond === 'refinance' && c.refinanceOk) total++;
      else if (cond === 'weekend' && c.weekendOk) total++;
    });

    return { total: total, max: max, pct: max > 0 ? Math.round((total / max) * 100) : 0 };
  }

  function matchColor(pct) {
    if (pct >= 80) return { bg: '#EDFAF2', border: '#C5E8D0', text: '#1a5e34', bar: '#39B167' };
    if (pct >= 50) return { bg: '#EAF3FB', border: '#CFE3F3', text: '#14528f', bar: '#1a6fd4' };
    return { bg: '#F5F7F5', border: '#dde9e2', text: '#5b6459', bar: '#9aa79f' };
  }

  function specChip(label) {
    return '<span style="font-size:11px; font-weight:600; color:#5b6459; background:#F5F7F5; border:1px solid #E5E9EF; padding:3px 9px; border-radius:999px;">' + label + '</span>';
  }

  function companyCard(c, score) {
    var col = matchColor(score.pct);
    var chips = [];
    chips.push(specChip('実質年率 ' + esc(c.rateMinLabel) + '〜' + esc(c.rateMaxLabel) + '%'));
    if (c.limitMax) chips.push(specChip('限度額 最大' + fmt(c.limitMax) + '万円'));
    if (c.freeDays > 0) chips.push(specChip(esc(c.freeLabel || ('無利息 ' + c.freeDays + '日間'))));
    if (c.examFast) chips.push(specChip('審査 ' + esc(c.examFast)));
    if (c.webOnly) chips.push(specChip('WEB完結'));
    if (c.cardlessOk) chips.push(specChip('アプリ完結・カードレス'));
    if (c.housewifeOk) chips.push(specChip('専業主婦（夫）も利用可'));
    if (c.refinanceOk) chips.push(specChip('おまとめ・借り換え対応'));
    if (c.weekendOk) chips.push(specChip('土日・夜間も対応'));

    return '<div style="background:#fff; border:1px solid #E5E9EF; border-radius:16px; padding:18px 20px; display:flex; flex-direction:column; gap:12px;">' +
      '<div style="display:flex; align-items:center; justify-content:space-between; gap:12px; flex-wrap:wrap;">' +
      '<span style="font-weight:800; font-size:17px; color:#20291f;">' + esc(c.name) + '</span>' +
      '<span style="display:inline-flex; align-items:center; gap:6px; font-size:12px; font-weight:700; color:' + col.text + '; background:' + col.bg + '; border:1px solid ' + col.border + '; padding:4px 12px; border-radius:999px;">マッチ度 ' + score.pct + '%</span>' +
      '</div>' +
      '<div style="height:6px; background:#F0EDE3; border-radius:99px; overflow:hidden;"><div style="height:100%; width:' + score.pct + '%; background:' + col.bar + '; border-radius:99px;"></div></div>' +
      '<div style="display:flex; flex-wrap:wrap; gap:6px;">' + chips.join('') + '</div>' +
      '<a href="' + esc(c.ctaUrl) + '" target="_blank" rel="noopener sponsored" style="align-self:flex-start; display:inline-flex; align-items:center; gap:6px; margin-top:4px; background:#39B167; color:#fff; font-weight:700; font-size:13px; padding:10px 20px; border-radius:10px; text-decoration:none;">' + esc(c.ctaLabel) + '<span>→</span></a>' +
      '</div>';
  }

  function render() {
    if (!COMPANIES.length) {
      resultEl.innerHTML = '<div style="padding:20px; text-align:center; font-size:13px; color:#8a9187; background:#fff; border:1px dashed #dde9e2; border-radius:14px;">現在ご案内できるカードローン会社がありません。</div>';
      return;
    }

    if (!answers.amount && !answers.job && !answers.conditions.length) {
      resultEl.innerHTML = '<div style="padding:20px; text-align:center; font-size:13px; color:#8a9187; background:#fff; border:1px dashed #dde9e2; border-radius:14px;">条件を1つ以上選んで「この条件で診断する」を押してください。</div>';
      return;
    }

    var scored = COMPANIES.map(function (c) { return { c: c, score: scoreCompany(c) }; });
    scored.sort(function (a, b) {
      if (b.score.pct !== a.score.pct) return b.score.pct - a.score.pct;
      return (b.c.rankScore || 0) - (a.c.rankScore || 0);
    });

    var top = scored.slice(0, 5);
    resultEl.innerHTML =
      '<div style="margin-bottom:12px; font-size:13px; font-weight:700; color:#2f3b33;">マッチ度が高い順に表示しています</div>' +
      '<div style="display:flex; flex-direction:column; gap:12px;">' +
      top.map(function (item) { return companyCard(item.c, item.score); }).join('') +
      '</div>';
  }

  submitBtn.addEventListener('click', render);

  render();
})();
