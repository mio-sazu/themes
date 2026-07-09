(function () {
  "use strict";
  var root = document.getElementById('snvm');
  if (!root || root.dataset.snvmInit) return;
  root.dataset.snvmInit = '1';

  var principalEl = document.getElementById('snvm-principal');
  var monthlyEl = document.getElementById('snvm-monthly');
  var rateEl = document.getElementById('snvm-rate');
  var resultEl = document.getElementById('snvm-result');

  function sanitizeInt(v) { return String(v == null ? '' : v).replace(/[^0-9]/g, ''); }
  function sanitizeDecimal(v) {
    var s = String(v == null ? '' : v).replace(/[^0-9.]/g, '');
    var dot = s.indexOf('.');
    if (dot !== -1) s = s.slice(0, dot + 1) + s.slice(dot + 1).replace(/\./g, '');
    return s;
  }
  function fmt(n) { return Math.round(n).toLocaleString('ja-JP'); }
  function termLabel(m) {
    var y = Math.floor(m / 12), mo = m % 12;
    if (y > 0 && mo > 0) return y + '年' + mo + 'ヶ月';
    if (y > 0) return y + '年';
    return mo + 'ヶ月';
  }

  // 本格版と同じ考え方の簡易計算（会社選択・無利息期間などは省略）
  function amortize(principal, monthly, annualRate) {
    var mr = annualRate / 100 / 12, bal = principal, totalInterest = 0, totalPaid = 0, m = 0;
    while (bal > 0.5 && m < 600) {
      m++;
      var interest = bal * mr;
      var principalApplied = monthly - interest;
      if (principalApplied <= 0) return { impossible: true };
      var pay = monthly;
      if (principalApplied >= bal) { principalApplied = bal; pay = bal + interest; }
      bal -= principalApplied;
      totalInterest += Math.round(interest);
      totalPaid += Math.round(pay);
    }
    if (bal > 0.5) return { impossible: true };
    return { impossible: false, months: m, totalInterest: totalInterest, totalPaid: totalPaid };
  }

  function render() {
    var P = Math.max(0, Number(principalEl.value) || 0);
    var M = Math.max(0, Number(monthlyEl.value) || 0);
    var R = Math.max(0, Number(rateEl.value) || 0);

    var EYE_ICON = '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" style="flex-shrink:0;"><path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7z" stroke="#9aa79f" stroke-width="1.8" stroke-linejoin="round"/><circle cx="12" cy="12" r="3" stroke="#9aa79f" stroke-width="1.8"/></svg>';
    var WARN_ICON = '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" style="flex-shrink:0;"><path d="M12 3.5 22 20.5H2Z" stroke="#c62828" stroke-width="1.8" stroke-linejoin="round"/><line x1="12" y1="9.5" x2="12" y2="14" stroke="#c62828" stroke-width="1.8" stroke-linecap="round"/><circle cx="12" cy="17" r="1" fill="#c62828"/></svg>';

    if (P <= 0 || M <= 0) {
      resultEl.innerHTML = '<div style="display:flex; align-items:center; gap:10px; padding:16px; background:#fff; border:1.5px dashed #dde9e2; border-radius:14px;">' + EYE_ICON + '<p style="margin:0; font-size:12px; line-height:1.6; color:#8a9187;">借入希望額と毎月の返済希望額を入力すると、<br>ここに結果が表示されます。</p></div>';
      return;
    }

    var r = amortize(P, M, R);
    if (r.impossible) {
      resultEl.innerHTML = '<div style="display:flex; align-items:center; gap:10px; padding:14px; background:#FBEAEA; border:1px solid #F3C6C6; border-radius:14px;">' + WARN_ICON + '<p style="margin:0; font-size:12px; line-height:1.6; color:#7a1f1f;">この条件では完済できません。返済額を増やすか、金利を見直してください。</p></div>';
      return;
    }

    resultEl.innerHTML =
      '<div style="background:#fff; border:1px solid #dde9e2; border-radius:16px; padding:16px 18px; margin-bottom:8px;">' +
      '<p style="margin:0; font-size:11px; font-weight:600; color:#8a9187; letter-spacing:0.04em;">総返済額</p>' +
      '<p style="margin:2px 0 0; font-weight:800; font-size:28px; color:#1e7a42; letter-spacing:-0.02em;">' + fmt(r.totalPaid) + '<span style="font-size:14px; font-weight:600; margin-left:2px; color:#5b6459;">円</span></p>' +
      '</div>' +
      '<div style="display:flex; gap:8px;">' +
      '<div style="flex:1; background:#fff; border:1px solid #dde9e2; border-radius:12px; padding:10px 12px; text-align:center;"><p style="margin:0 0 3px; font-size:11px; color:#8a9187;">返済期間</p><p style="margin:0; font-weight:800; font-size:16px; color:#2f3b33;">' + termLabel(r.months) + '</p></div>' +
      '<div style="flex:1; background:#fff; border:1px solid #dde9e2; border-radius:12px; padding:10px 12px; text-align:center;"><p style="margin:0 0 3px; font-size:11px; color:#8a9187;">利息合計</p><p style="margin:0; font-weight:800; font-size:16px; color:#14528f;">' + fmt(r.totalInterest) + '円</p></div>' +
      '</div>';
  }

  function bindSanitized(el, sanitize) {
    el.addEventListener('input', function (e) {
      var v = sanitize(e.target.value);
      if (v !== e.target.value) e.target.value = v; // 値が変わる時だけ書き戻し、カーソル位置を保つ
      render();
    });
  }
  bindSanitized(principalEl, sanitizeInt);
  bindSanitized(monthlyEl, sanitizeInt);
  bindSanitized(rateEl, sanitizeDecimal);

  render();
})();
