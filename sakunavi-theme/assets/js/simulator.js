(function () {
  "use strict";
  var root = document.getElementById('snv');
  if (!root || root.dataset.snvInit) return;
  root.dataset.snvInit = '1';

  // 会社データは PHP 側（sakunavi_simulator_companies）が card_loan_company から
  // 組み立てて wp_localize_script で渡してくる。会社ページの管理画面で
  // 「返済シミュレーターに表示する」をONにするだけで、ここに追加される。
  var COMPANIES = (window.snvSimulatorData && Array.isArray(window.snvSimulatorData.companies))
    ? window.snvSimulatorData.companies
    : [];

  var now = new Date();

  var state = {
    companyId: null,
    companyOpen: false,
    companyQuery: '',
    principal: '',
    monthly: '',
    rate: '',
    startY: now.getFullYear(),
    startM: now.getMonth() + 1,
    interestFree: false,
    showAll: false,
    checks: [false, false, false]
  };

  function currentCompany() {
    // companyId が null＝まだ何も選択していない状態。COMPANIES[0]へは
    // フォールバックしない（フォールバックすると、選択カードが未選択のまま
    // 上部バッジやCTAだけ会社情報が出てしまい表示が食い違う）。
    if (!COMPANIES.length || state.companyId === null) return null;
    for (var i = 0; i < COMPANIES.length; i++) {
      if (COMPANIES[i].id === state.companyId) return COMPANIES[i];
    }
    return COMPANIES[0];
  }
  function applyCompany(id) {
    state.companyId = id;
    var c = currentCompany();
    if (!c) return;
    state.rate = c.maxRate;
    state.interestFree = c.freeDays > 0;
  }

  function fmt(n) { return Math.round(n).toLocaleString('ja-JP'); }
  function esc(s) { return String(s).replace(/[&<>"]/g, function (c) { return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;' }[c]; }); }
  // type="number" はブラウザによって setSelectionRange が使えず、再描画のたびに
  // 入力キャレットが末尾へ飛んでしまう。text + inputmode で扱い、文字種だけ絞り込む。
  function sanitizeInt(v) { return String(v == null ? '' : v).replace(/[^0-9]/g, ''); }
  function sanitizeDecimal(v) {
    var s = String(v == null ? '' : v).replace(/[^0-9.]/g, '');
    var dot = s.indexOf('.');
    if (dot !== -1) s = s.slice(0, dot + 1) + s.slice(dot + 1).replace(/\./g, '');
    return s;
  }

  function amortize(principal, monthly, annualRate, applyFree) {
    var mr = annualRate / 100 / 12, bal = principal, totalInterest = 0, totalPaid = 0, sched = [], m = 0;
    while (bal > 0.5 && m < 600) {
      m++;
      var interest = (applyFree && m === 1) ? 0 : bal * mr;
      var principalApplied = monthly - interest;
      if (principalApplied <= 0) return { impossible: true, months: 0, totalInterest: 0, totalPaid: 0, schedule: [] };
      var pay = monthly;
      if (principalApplied >= bal) { principalApplied = bal; pay = bal + interest; }
      bal -= principalApplied;
      var iR = Math.round(interest), pR = Math.round(pay), paR = Math.round(principalApplied);
      totalInterest += iR; totalPaid += pR;
      sched.push({ n: m, payment: pR, principalApplied: paR, interest: iR, balance: Math.max(0, Math.round(bal)) });
    }
    if (bal > 0.5) {
      // 600ヶ月（50年）経っても残高が残る＝実質的に完済不可能な返済額
      return { impossible: true, months: 0, totalInterest: 0, totalPaid: 0, schedule: [] };
    }
    return { impossible: false, months: m, totalInterest: totalInterest, totalPaid: totalPaid, schedule: sched };
  }
  function ymParts(i) { var t = state.startY * 12 + (state.startM - 1) + i; return { y: Math.floor(t / 12), m: (t % 12) + 1 }; }
  function pad2(n) { return (n < 10 ? '0' : '') + n; }
  function ymLabel(i) { var p = ymParts(i); return p.y + '/' + pad2(p.m); }
  function termLabel(m) { var y = Math.floor(m / 12), mo = m % 12; if (y > 0 && mo > 0) return y + '年' + mo + 'ヶ月'; if (y > 0) return y + '年'; return mo + 'ヶ月'; }
  function gradeFor(r) {
    if (r <= 13) return { g: 'A', label: '理想的', color: '#39B167' };
    if (r <= 20) return { g: 'B', label: '良好', color: '#3182CE' };
    if (r <= 30) return { g: 'C', label: '標準', color: '#6a897c' };
    if (r <= 45) return { g: 'D', label: '要注意', color: '#f5a623' };
    return { g: 'E', label: '見直し推奨', color: '#C62828' };
  }

  function compute() {
    var s = state;
    var P = Math.max(0, Number(s.principal) || 0);
    var M = Math.max(0, Number(s.monthly) || 0);
    var R = Math.max(0, Number(s.rate) || 0);
    var free = s.interestFree;
    var empty = (P <= 0 || M <= 0);
    var base = amortize(P, M, R, free);
    var minPay = typicalMinPayment(P, R);
    var minR = amortize(P, minPay, R, free);
    var noFree = amortize(P, M, R, false);

    var C = 2 * Math.PI * 52, donutDash = '0 ' + C.toFixed(1), interestPct = 0;
    if (!base.impossible && base.totalPaid > 0) {
      interestPct = base.totalInterest / base.totalPaid * 100;
      var iLen = (base.totalInterest / base.totalPaid) * C;
      donutDash = iLen.toFixed(1) + ' ' + (C - iLen).toFixed(1);
    }
    var grade = gradeFor(interestPct);

    var scheduleRows = [];
    if (!base.impossible) {
      var full = base.schedule;
      var mkRow = function (r, isLast) {
        return {
          isData: true, n: r.n, ym: ymLabel(r.n - 1),
          paymentF: fmt(r.payment), principalF: fmt(r.principalApplied), interestF: fmt(r.interest), balanceF: fmt(r.balance),
          rowStyle: (isLast ? 'background:#F8FAF8; font-weight:700; border-top:1px solid #DDE7E0;' : 'border-top:1px solid #EDFAF2;')
        };
      };
      if (s.showAll || full.length <= 6) {
        scheduleRows = full.map(function (r, i) { return mkRow(r, i === full.length - 1); });
      } else {
        full.slice(0, 3).forEach(function (r) { scheduleRows.push(mkRow(r, false)); });
        scheduleRows.push({ isEllipsis: true });
        scheduleRows.push(mkRow(full[full.length - 1], true));
      }
    }

    var incDefs = [{ add: 3000, label: '+3,000円' }, { add: 5000, label: '+5,000円' }];
    var increasePlans = [];
    if (!base.impossible) {
      var maxSaved = Math.max(1, base.totalInterest);
      increasePlans = incDefs.map(function (d) {
        var sc = amortize(P, M + d.add, R, free);
        var scTotalI = sc.impossible ? base.totalInterest : sc.totalInterest;
        var scMonths = sc.impossible ? base.months : sc.months;
        var iSaved = Math.max(0, base.totalInterest - scTotalI);
        var mSaved = Math.max(0, base.months - scMonths);
        return {
          label: d.label, monthlyF: fmt(M + d.add), monthsLabel: scMonths + 'ヶ月',
          interestTotalF: fmt(scTotalI), interestSavedF: fmt(iSaved), monthsSavedLabel: mSaved + 'ヶ月短縮',
          interestBar: Math.min(100, (iSaved / maxSaved) * 100).toFixed(0) + '%',
          monthsBar: Math.min(100, (mSaved / Math.max(1, base.months)) * 100 * 2).toFixed(0) + '%'
        };
      });
    }

    var minInterestSaved = (!base.impossible && !minR.impossible) ? Math.max(0, minR.totalInterest - base.totalInterest) : 0;
    var minMonthsSaved = (!base.impossible && !minR.impossible) ? Math.max(0, minR.months - base.months) : 0;
    var freeBenefit = (!base.impossible && !noFree.impossible) ? Math.max(0, noFree.totalInterest - base.totalInterest) : 0;
    var freeBarWith = noFree.totalInterest > 0 ? Math.max(8, (base.totalInterest / noFree.totalInterest) * 100).toFixed(0) + '%' : '92%';

    return {
      base: base, minR: minR, empty: empty, minPay: minPay,
      donutDash: donutDash, interestPct: interestPct, grade: grade,
      scheduleRows: scheduleRows, increasePlans: increasePlans,
      minInterestSaved: minInterestSaved, minMonthsSaved: minMonthsSaved,
      freeBenefit: freeBenefit, freeBarWith: freeBarWith, P: P, M: M
    };
  }

  // ---- ビュー生成 ----
  var INPUT = function (action, type, val, extra, placeholder) {
    return '<input class="simInput" type="' + type + '" ' + (extra || '') + (placeholder ? ' placeholder="' + esc(placeholder) + '"' : '') + ' value="' + esc(val) + '" data-action="' + action + '" data-focus="' + action + '" style="width:100%; border:1.5px solid #DDE9E2; border-radius:12px; padding:14px 44px 14px 14px; font-size:18px; font-weight:700; font-family:\'Plus Jakarta Sans\',sans-serif; outline:none; background:#F8FAF8; transition:all .15s;">';
  };

  function rateHint() {
    var c = currentCompany();
    if (!c) return '<p style="margin:0 0 20px; font-size:12px; color:#8a9187;">会社を選択すると、実質年率の目安が自動で表示されます。</p>';
    return '<p style="margin:0 0 20px; font-size:12px; color:#8a9187;">' + esc(c.name) + 'の実質年率：' + c.minRate + '％〜' + c.maxRate + '％（上限を自動セット・手動で変更可）</p>';
  }
  function freeToggle() {
    var c = currentCompany();
    if (!c || c.freeDays <= 0) {
      var label = c ? esc(c.name) + 'には無利息期間はありません' : '会社を選択すると無利息期間の有無が表示されます';
      return '<label style="display:flex; align-items:center; gap:12px; padding:14px; border-radius:12px; background:#E5E9EF; border:1px solid #DDE9E2; cursor:not-allowed; opacity:.75;"><input type="checkbox" disabled style="width:20px; height:20px; accent-color:#8a9187;"><span style="font-size:13px; font-weight:600; color:#8a9187;">' + label + '</span></label>';
    }
    return '<label style="display:flex; align-items:center; gap:12px; padding:14px; border-radius:12px; background:#EDFAF2; border:1px solid #C5E8D0; cursor:pointer;"><input type="checkbox" data-action="free" ' + (state.interestFree ? 'checked' : '') + ' style="width:20px; height:20px; accent-color:#39B167;"><span style="font-size:13px; font-weight:600; color:#1a5e34;">初回 ' + c.freeDays + '日間 の無利息サービスを適用</span></label>';
  }
  function companyNote() {
    var c = currentCompany();
    if (!c) return '';
    return '<div style="margin-top:16px; padding:16px; background:#FBEAEA; border:1px solid #F3C6C6; border-radius:16px; display:flex; gap:10px; align-items:flex-start;"><span class="msr" style="color:#c62828; flex-shrink:0; font-size:20px;">warning</span><p style="margin:0; font-size:13px; line-height:1.6; color:#7a1f1f;"><b>' + esc(c.name) + 'の注意点：</b>' + esc(c.freeNote) + '</p></div>';
  }

  function inputPanelB() {
    var s = state;
    var pForHint = Math.max(0, Number(s.principal) || 0);
    var rForHint = Math.max(0, Number(s.rate) || 0);
    var minPayHint = pForHint > 0 && rForHint > 0
      ? '最低返済額の目安：' + fmt(typicalMinPayment(pForHint, rForHint)) + '円（10年ペースで完済する場合の目安）'
      : '最低返済額の目安：借入額と利率を入力すると表示されます';
    return '' +
      '<div style="background:#fff; border:1px solid #E5E9EF; border-radius:20px; padding:24px; box-shadow:0 1px 3px rgba(60,50,20,0.05);">' +
      '<h2 style="font-weight:700; font-size:18px; margin:0 0 20px; display:flex; align-items:center; gap:8px;"><span class="msr" style="color:#39B167;">edit_note</span>条件設定</h2>' +
      '<label style="display:block; font-size:13px; font-weight:700; color:#3f4a3e; margin-bottom:8px;">借入希望額</label>' +
      '<div style="position:relative; margin-bottom:20px;">' + INPUT('principal', 'text', s.principal, 'inputmode="numeric" pattern="[0-9]*"', '借入希望額を入力') + '<span style="position:absolute; right:16px; top:50%; transform:translateY(-50%); color:#8a9187; font-size:14px; font-weight:600;">円</span></div>' +
      '<label style="display:block; font-size:13px; font-weight:700; color:#3f4a3e; margin-bottom:8px;">毎月の返済希望額</label>' +
      '<div style="position:relative;">' + INPUT('monthly', 'text', s.monthly, 'inputmode="numeric" pattern="[0-9]*"', '毎月の返済希望額を入力') + '<span style="position:absolute; right:16px; top:50%; transform:translateY(-50%); color:#8a9187; font-size:14px; font-weight:600;">円</span></div>' +
      '<p style="margin:8px 0 20px; font-size:12px; color:#8a9187;">' + minPayHint + '</p>' +
      '<label style="display:block; font-size:13px; font-weight:700; color:#3f4a3e; margin-bottom:8px;">借入利率（実質年率）</label>' +
      '<div style="position:relative; margin-bottom:6px;">' + INPUT('rate', 'text', s.rate, 'inputmode="decimal"', '借入利率を入力') + '<span style="position:absolute; right:16px; top:50%; transform:translateY(-50%); color:#8a9187; font-size:14px; font-weight:600;">％</span></div>' +
      rateHint() +
      '<label style="display:block; font-size:13px; font-weight:700; color:#3f4a3e; margin-bottom:8px;">返済開始月</label>' +
      '<input class="simInput" type="month" data-action="start" data-focus="start" value="' + s.startY + '-' + pad2(s.startM) + '" style="width:100%; border:1.5px solid #DDE9E2; border-radius:12px; padding:13px 14px; font-size:15px; font-weight:600; outline:none; background:#F8FAF8; margin-bottom:20px; color:#1a1c1c;">' +
      freeToggle() +
      '</div>' +
      companyNote();
  }

  function freeBenefitCard(v) {
    var c = currentCompany();
    if (!c || c.freeDays <= 0) {
      var msg = c ? esc(c.name) + 'に無利息期間はありません。無利息期間を重視する場合は、初回無利息サービスのある他社もあわせてご検討ください。' : '会社を選択すると、無利息期間による節約額の目安が表示されます。';
      return '<div style="background:#fff; border:1px solid #E5E9EF; border-radius:20px; padding:24px; display:flex; flex-direction:column;"><h3 style="font-weight:700; font-size:15px; margin:0 0 16px;">無利息期間について</h3><div style="flex:1; display:flex; flex-direction:column; align-items:center; justify-content:center; text-align:center; gap:12px; padding:12px 0;"><span class="msr" style="color:#9FB0A6; font-size:48px;">block</span><p style="margin:0; font-size:13px; line-height:1.6; color:#8a9187;">' + msg + '</p></div></div>';
    }
    return '<div style="background:#fff; border:1px solid #E5E9EF; border-radius:20px; padding:24px;"><h3 style="font-weight:700; font-size:15px; margin:0 0 20px;">無利息期間のメリット</h3><div style="display:flex; flex-direction:column; gap:18px;"><div><div style="display:flex; justify-content:space-between; font-size:13px; margin-bottom:7px;"><span style="font-weight:600;">無利息あり</span><span style="font-weight:700; color:#39B167;">−' + fmt(v.freeBenefit) + ' 円</span></div><div style="height:30px; background:#E5E9EF; border-radius:8px; overflow:hidden;"><div style="height:100%; width:' + v.freeBarWith + '; background:linear-gradient(90deg,#2ea05a,#39B167); border-radius:8px; transform-origin:left; animation:snvBarGrow .5s ease;"></div></div></div><div><div style="display:flex; justify-content:space-between; font-size:13px; margin-bottom:7px;"><span style="font-weight:600; color:#8a9187;">無利息なし</span><span style="color:#8a9187;">通常時</span></div><div style="height:30px; background:#E5E9EF; border-radius:8px; overflow:hidden;"><div style="height:100%; width:100%; background:#9FB0A6; border-radius:8px;"></div></div></div></div><p style="margin:18px 0 0; font-size:13px; line-height:1.6; color:#5b6459; background:#F8FAF8; padding:12px; border-radius:10px;">初回' + c.freeDays + '日間の利息が0円になることで、通常の借入に比べ約<b style="color:#39B167;"> ' + fmt(v.freeBenefit) + ' 円</b>利息を抑えられます。</p></div>';
  }

  function donut(d, pctLabel) {
    return '<div style="position:relative; width:180px; height:180px; margin:0 auto;"><svg viewBox="0 0 120 120" style="width:100%; height:100%; transform:rotate(-90deg);"><circle cx="60" cy="60" r="52" fill="none" stroke="#39B167" stroke-width="16"></circle><circle cx="60" cy="60" r="52" fill="none" stroke="#1a6fd4" stroke-width="16" stroke-dasharray="' + d + '"></circle></svg><div style="position:absolute; inset:0; display:flex; flex-direction:column; align-items:center; justify-content:center;"><span style="font-size:12px; color:#8a9187;">利息比率</span><span style="font-weight:800; font-size:26px; color:#14528f;">' + pctLabel + '</span></div></div>';
  }

  function scheduleTableRows(rows) {
    return rows.map(function (r) {
      if (r.isEllipsis) return '<tr><td colspan="6" style="padding:10px 16px; text-align:center; font-size:12px; font-style:italic; color:#a5a08f;">… 途中省略 …</td></tr>';
      return '<tr class="schedRow" style="' + r.rowStyle + '">' +
        '<td style="padding:11px 16px; font-size:13px;">' + r.n + '</td>' +
        '<td style="padding:11px 16px; font-size:13px; color:#5b6459;">' + r.ym + '</td>' +
        '<td style="padding:11px 16px; font-size:13px; text-align:right;">' + r.paymentF + '</td>' +
        '<td style="padding:11px 16px; font-size:13px; text-align:right; color:#39B167; font-weight:600;">' + r.principalF + '</td>' +
        '<td style="padding:11px 16px; font-size:13px; text-align:right; color:#14528f;">' + r.interestF + '</td>' +
        '<td style="padding:11px 16px; font-size:13px; text-align:right; font-weight:600;">' + r.balanceF + '</td></tr>';
    }).join('');
  }

  function checklist() {
    var texts = [
      '毎月の返済額（' + fmt(state.monthly) + '円）が、生活費を圧迫しないか確認した',
      '無利息期間の適用条件（Web明細登録など）を理解した',
      '借入総額が年収の3分の1を超えていないか確認した（総量規制）'
    ];
    return texts.map(function (t, i) {
      var on = state.checks[i];
      return '<label style="background:#fff; border:1px solid ' + (on ? '#C5E8D0' : '#DDE7E0') + '; padding:16px; border-radius:12px; display:flex; gap:12px; align-items:flex-start; cursor:pointer; transition:all .15s;"><input type="checkbox" data-action="check" data-idx="' + i + '" ' + (on ? 'checked' : '') + ' style="width:20px; height:20px; margin-top:1px; accent-color:#39B167; flex-shrink:0;"><span style="font-size:14px; line-height:1.5; color:' + (on ? '#1e7a42' : '#3f4a3e') + ';">' + esc(t) + '</span></label>';
    }).join('');
  }

  // 完済できる理論上の最低ライン（初月の利息を1円でも上回る額）。
  // この額ちょうどだと残高がほぼ減らず現実には完済に何百年もかかるため、
  // 「これを下回ったら即アウト」というエラー判定にのみ使う。
  function minViablePayment(P, R) {
    var mr = Math.max(0, Number(R) || 0) / 100 / 12;
    return Math.floor(P * mr) + 1;
  }
  // 「最低返済額との比較」用の、無理のない現実的な最低ライン。
  // 10年（120回）で完済できるペースを目安にする（理論上の下限だと
  // 現実的な期間で終わらないため、比較の基準としては使えない）。
  function typicalMinPayment(P, R) {
    var mr = Math.max(0, Number(R) || 0) / 100 / 12;
    var n = 120;
    var payment = mr > 0 ? P * mr / (1 - Math.pow(1 + mr, -n)) : P / n;
    return Math.max(Math.ceil(payment), minViablePayment(P, R));
  }
  function impossibleBox(P) {
    var need = minViablePayment(P, state.rate);
    return '<div style="background:#FBEAEA; border:1px solid #F3C6C6; border-radius:20px; padding:24px; display:flex; gap:12px; align-items:flex-start;"><span class="msr fill" style="color:#c62828; font-size:24px;">error</span><div><p style="margin:0 0 4px; font-weight:700; color:#7a1f1f;">この返済額では完済できません</p><p style="margin:0; font-size:14px; color:#7a1f1f;">毎月の返済額が利息分を下回っています。この借入額・利率の場合、返済額を ' + fmt(need) + '円 以上に設定してください。</p></div></div>';
  }
  function emptyBox() {
    return '<div style="background:#fff; border:1.5px dashed #DDE9E2; border-radius:20px; padding:40px 24px; text-align:center; color:#8a9187;"><span class="msr" style="font-size:40px; color:#9FB0A6; display:block; margin-bottom:10px;">calculate</span><p style="margin:0; font-size:14px; line-height:1.8;">借入希望額と毎月の返済希望額を入力すると、返済シミュレーション結果がここに表示されます。</p></div>';
  }

  // 結果パネル（右側）のHTMLだけを組み立てる。条件設定（aside）とは
  // 完全に別DOMにして、数値入力のたびに結果パネルだけを差し替える。
  // aside自体を作り直さないので、入力中のフォーカス位置がずれる心配がない。
  function resultsMarkup(v) {
    var base = v.base;
    if (v.empty) return emptyBox();
    if (base.impossible) return impossibleBox(v.P);
    var toggleLabel = state.showAll ? '一部を表示' : '全て表示';
    var toggleIcon = state.showAll ? 'expand_less' : 'expand_more';
    return '' +
        '<div class="bentoGrid" style="display:grid; grid-template-columns:2fr 1fr; gap:16px;">' +
        '<div style="background:linear-gradient(135deg,#39B167,#1e7a42); color:#fff; padding:26px; border-radius:20px; display:flex; flex-direction:column; justify-content:space-between; box-shadow:0 6px 20px rgba(30,122,66,0.25);"><p style="margin:0; font-size:13px; font-weight:600; letter-spacing:0.05em; opacity:.85;">総返済額</p><div style="display:flex; align-items:baseline; gap:6px; margin-top:6px;"><span style="font-weight:800; font-size:44px; line-height:1; letter-spacing:-0.02em;">' + fmt(base.totalPaid) + '</span><span style="font-size:16px; font-weight:600;">円</span></div><div style="display:flex; gap:20px; margin-top:20px; padding-top:16px; border-top:1px solid rgba(255,255,255,0.22); flex-wrap:wrap;"><div><p style="margin:0; font-size:12px; opacity:.8;">利息合計</p><p style="margin:2px 0 0; font-size:16px; font-weight:700;">' + fmt(base.totalInterest) + ' 円</p></div><div><p style="margin:0; font-size:12px; opacity:.8;">返済期間</p><p style="margin:2px 0 0; font-size:16px; font-weight:700;">' + termLabel(base.months) + '</p></div></div></div>' +
        '<div style="background:#fff; border:1px solid #E5E9EF; border-radius:20px; padding:20px; display:flex; flex-direction:column; align-items:center; justify-content:center; text-align:center; gap:8px;"><p style="margin:0; font-size:12px; font-weight:700; letter-spacing:0.05em; color:#8a9187;">計画評価</p><div style="width:66px; height:66px; border-radius:50%; border:4px solid ' + v.grade.color + '; display:flex; align-items:center; justify-content:center;"><span style="font-weight:800; font-size:32px; color:' + v.grade.color + ';">' + v.grade.g + '</span></div><p style="margin:0; font-size:13px; font-weight:700; color:' + v.grade.color + ';">' + v.grade.label + '</p></div>' +
        '</div>' +
        '<div class="chartsGrid" style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">' +
        '<div style="background:#fff; border:1px solid #E5E9EF; border-radius:20px; padding:24px;"><h3 style="font-weight:700; font-size:15px; margin:0 0 16px;">元金・利息の内訳</h3>' + donut(v.donutDash, v.interestPct.toFixed(1) + '%') + '<div style="margin-top:20px; display:flex; flex-direction:column; gap:10px;"><div style="display:flex; justify-content:space-between; font-size:14px;"><span style="display:flex; align-items:center; gap:8px;"><span style="width:11px; height:11px; border-radius:3px; background:#39B167;"></span>借入元金</span><span style="font-weight:700;">' + fmt(v.P) + ' 円</span></div><div style="display:flex; justify-content:space-between; font-size:14px;"><span style="display:flex; align-items:center; gap:8px;"><span style="width:11px; height:11px; border-radius:3px; background:#1a6fd4;"></span>利息合計</span><span style="font-weight:700;">' + fmt(base.totalInterest) + ' 円</span></div></div></div>' +
        freeBenefitCard(v) +
        '</div>' +
        '<div style="background:#fff; border:1px solid #E5E9EF; border-radius:20px; padding:24px;"><h3 style="font-weight:700; font-size:16px; margin:0 0 18px; display:flex; align-items:center; gap:8px;"><span class="msr" style="color:#39B167;">rocket_launch</span>増額返済のシミュレーション</h3><div class="prepayGrid" style="display:grid; grid-template-columns:1fr 1fr; gap:14px;">' +
        v.increasePlans.map(function (p) { return '<div style="padding:18px; border-radius:14px; background:#F8FAF8; border:1px solid #DDE7E0;"><div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;"><span style="font-weight:800; font-size:16px;">' + p.label + '</span><span style="font-size:12px; font-weight:700; color:#39B167; background:rgba(57,177,103,0.1); padding:3px 9px; border-radius:999px;">月 ' + p.monthlyF + ' 円</span></div><div style="margin-bottom:14px;"><div style="display:flex; justify-content:space-between; font-size:12px; margin-bottom:5px;"><span style="color:#5b6459;">利息の削減</span><span style="font-weight:700; color:#39B167;">−' + p.interestSavedF + ' 円</span></div><div style="text-align:right; font-size:11px; color:#a5a08f; margin-bottom:5px;">利息総額 ' + p.interestTotalF + ' 円</div><div style="height:8px; background:#DDE7E0; border-radius:99px; overflow:hidden;"><div style="height:100%; width:' + p.interestBar + '; background:#39B167; border-radius:99px; transform-origin:left; animation:snvBarGrow .5s ease;"></div></div></div><div><div style="display:flex; justify-content:space-between; font-size:12px; margin-bottom:5px;"><span style="color:#5b6459;">期間の短縮</span><span style="font-weight:700; color:#3182CE;">' + p.monthsSavedLabel + '</span></div><div style="text-align:right; font-size:11px; color:#a5a08f; margin-bottom:5px;">返済期間 ' + p.monthsLabel + '</div><div style="height:8px; background:#DDE7E0; border-radius:99px; overflow:hidden;"><div style="height:100%; width:' + p.monthsBar + '; background:#3182CE; border-radius:99px; transform-origin:left; animation:snvBarGrow .5s ease;"></div></div></div></div>'; }).join('') +
        '</div><p style="margin:14px 0 0; font-size:12px; color:#8a9187; font-style:italic;">※ 毎月の返済額を少し増やすだけで、総支払額と完済までの期間が大きく減ります。</p></div>' +
        '<div style="background:linear-gradient(135deg,#EAF3FB,#D6E9FB); border:1px solid #CFE3F3; border-radius:20px; padding:24px;"><h3 class="snv-h-info" style="font-weight:700; font-size:16px; margin:0 0 4px; display:flex; align-items:center; gap:8px;"><span class="msr">trending_down</span>最低返済額との比較</h3><p style="margin:0 0 16px; font-size:13px; color:#3a6d94;">最低返済額（' + fmt(v.minPay) + '円）で払い続けた場合との差：</p><div style="display:flex; flex-wrap:wrap; gap:12px;"><div style="background:rgba(255,255,255,0.6); padding:14px 20px; border-radius:14px; flex:1; min-width:140px;"><p style="margin:0 0 4px; font-size:12px; color:#3a6d94;">完済までの期間</p><p style="margin:0; font-weight:800; font-size:22px; color:#14528f;">' + v.minMonthsSaved + 'ヶ月短縮</p></div><div style="background:rgba(255,255,255,0.6); padding:14px 20px; border-radius:14px; flex:1; min-width:140px;"><p style="margin:0 0 4px; font-size:12px; color:#3a6d94;">削減できる利息</p><p style="margin:0; font-weight:800; font-size:22px; color:#14528f;">' + fmt(v.minInterestSaved) + ' 円</p></div></div></div>' +
        '<div style="background:#fff; border:1px solid #E5E9EF; border-radius:20px; overflow:hidden;"><div style="padding:18px 24px; background:#F8FAF8; border-bottom:1px solid #DDE7E0; display:flex; justify-content:space-between; align-items:center;"><h3 style="font-weight:700; font-size:15px; margin:0;">返済スケジュール</h3><button data-action="toggleAll" style="background:none; border:none; color:#39B167; font-weight:700; font-size:13px; cursor:pointer; display:flex; align-items:center; gap:3px;">' + toggleLabel + '<span class="msr" style="font-size:18px;">' + toggleIcon + '</span></button></div><div style="overflow-x:auto;"><table style="width:100%; border-collapse:collapse; min-width:520px;"><thead><tr style="background:#F8FAF8; text-align:left;"><th style="padding:12px 16px; font-size:11px; font-weight:700; letter-spacing:0.05em; color:#8a9187;">回</th><th style="padding:12px 16px; font-size:11px; font-weight:700; letter-spacing:0.05em; color:#8a9187;">年月</th><th style="padding:12px 16px; font-size:11px; font-weight:700; letter-spacing:0.05em; color:#8a9187; text-align:right;">返済額</th><th style="padding:12px 16px; font-size:11px; font-weight:700; letter-spacing:0.05em; color:#8a9187; text-align:right;">元金充当</th><th style="padding:12px 16px; font-size:11px; font-weight:700; letter-spacing:0.05em; color:#8a9187; text-align:right;">利息</th><th style="padding:12px 16px; font-size:11px; font-weight:700; letter-spacing:0.05em; color:#8a9187; text-align:right;">残高</th></tr></thead><tbody>' + scheduleTableRows(v.scheduleRows) + '</tbody></table></div></div>' +
        '<div style="background:#fff; border:1px solid #E5E9EF; border-radius:20px; padding:24px;"><h3 style="font-weight:700; font-size:16px; margin:0 0 16px; display:flex; align-items:center; gap:8px;"><span class="msr fill" style="color:#39B167;">verified</span>お申し込み前チェックリスト</h3><div style="display:flex; flex-direction:column; gap:10px;">' + checklist() + '</div></div>' +
        resultActions();
  }

  // ---- 会社セレクター（検索付きドロップダウン、複数社対応） ----
  function companyPill(c) {
    var on = c.freeDays > 0;
    return '<span style="font-size:11px; font-weight:600; white-space:nowrap; color:' + (on ? '#1a5e34' : '#8a9187') + '; background:' + (on ? '#EDFAF2' : '#E5E9EF') + '; padding:3px 9px; border-radius:999px;">' + (on ? '無利息' + c.freeDays + '日' : '無利息なし') + '</span>';
  }
  function renderCompanies() {
    if (!COMPANIES.length) {
      return '<div style="background:#fff; border:1px dashed #DDE9E2; border-radius:16px; padding:20px; font-size:13px; color:#8a9187;">現在ご案内できるカードローン会社がありません。下記の金額・利率を直接入力してシミュレーションできます。</div>';
    }
    var featured = COMPANIES.filter(function (c) { return c.featured; });
    var customOn = state.companyId === null;
    var customCard = '<button data-action="companyClear" style="text-align:left; cursor:pointer; padding:16px; border-radius:16px; border:2px dashed ' + (customOn ? '#39B167' : '#9FB0A6') + '; background:' + (customOn ? 'rgba(57,177,103,0.06)' : '#F8FAF8') + '; transition:all .15s; font-family:inherit; display:flex; flex-direction:column; justify-content:center; gap:6px; position:relative;">' +
      (customOn ? '<span class="msr fill" style="position:absolute; top:12px; right:12px; color:#39B167; font-size:20px;">check_circle</span>' : '') +
      '<span class="msr" style="color:' + (customOn ? '#39B167' : '#8a9187') + '; font-size:22px;">tune</span>' +
      '<span style="font-weight:800; font-size:15px; color:' + (customOn ? '#1e7a42' : '#3f4a3e') + ';">会社を選ばずに入力</span>' +
      '<span style="font-size:12px; color:#8a9187;">金利・条件をすべて自分で設定します</span>' +
      '</button>';
    var cards = '<div style="display:grid; grid-template-columns:repeat(auto-fill,minmax(200px,1fr)); gap:12px; margin-bottom:14px;">' +
      featured.map(function (c) {
        var on = c.id === state.companyId;
        return '<button data-action="company" data-id="' + c.id + '" style="text-align:left; cursor:pointer; padding:16px; border-radius:16px; border:2px solid ' + (on ? '#39B167' : '#E5E9EF') + '; background:' + (on ? 'rgba(57,177,103,0.06)' : '#fff') + '; transition:all .15s; font-family:inherit; display:flex; flex-direction:column; gap:6px; position:relative;">' +
          (on ? '<span class="msr fill" style="position:absolute; top:12px; right:12px; color:#39B167; font-size:20px;">check_circle</span>' : '') +
          '<span style="font-weight:800; font-size:16px; color:' + (on ? '#1e7a42' : '#1a1c1c') + ';">' + esc(c.name) + '</span>' +
          '<span style="font-size:13px; font-weight:700; color:#39B167;">実質年率 ' + c.minRate + '〜' + c.maxRate + '％</span>' +
          '<div style="display:flex; gap:6px; flex-wrap:wrap; margin-top:2px;">' + companyPill(c) + '<span style="font-size:11px; font-weight:600; color:#5b6459; background:#EDFAF2; padding:2px 8px; border-radius:999px;">' + esc(c.limit) + '</span></div>' +
          '</button>';
      }).join('') + customCard + '</div>';
    var hasMore = COMPANIES.length > featured.length;
    var triggerLabel = hasMore ? 'その他の会社から選ぶ（全' + COMPANIES.length + '社）' : '会社を選ぶ';
    var trigger = '<button data-action="companyToggle" style="width:100%; text-align:left; cursor:pointer; padding:13px 16px; border-radius:14px; border:1.5px solid #DDE9E2; background:#fff; display:flex; align-items:center; justify-content:space-between; gap:12px; font-family:inherit;">' +
      '<span style="display:flex; align-items:center; gap:8px; color:#3f4a3e; font-size:14px; font-weight:700;"><span class="msr" style="color:#39B167; font-size:20px;">list</span>' + triggerLabel + '</span>' +
      '<span class="msr" style="color:#8a9187; font-size:22px;">' + (state.companyOpen ? 'expand_less' : 'expand_more') + '</span></button>';
    var dropdown = '<div style="position:relative; max-width:560px;">' + trigger;
    if (state.companyOpen) {
      var q = (state.companyQuery || '').trim();
      var list = COMPANIES.filter(function (c) { return !q || c.name.indexOf(q) >= 0; });
      var rows = list.map(function (c) {
        var on = c.id === state.companyId;
        return '<button data-action="company" data-id="' + c.id + '" style="width:100%; text-align:left; cursor:pointer; padding:12px 14px; border:none; border-bottom:1px solid #E5E9EF; background:' + (on ? 'rgba(57,177,103,0.06)' : '#fff') + '; display:flex; align-items:center; justify-content:space-between; gap:10px; font-family:inherit;">' +
          '<span style="display:flex; flex-direction:column; gap:3px;"><span style="font-weight:800; font-size:15px; color:' + (on ? '#1e7a42' : '#1a1c1c') + ';">' + esc(c.name) + '</span><span style="font-size:12px; color:#5b6459;">実質年率 ' + c.minRate + '〜' + c.maxRate + '％ ・ ' + esc(c.limit) + '</span></span>' + companyPill(c) + '</button>';
      }).join('') || '<p style="padding:18px; margin:0; text-align:center; color:#8a9187; font-size:13px;">該当する会社がありません。</p>';
      dropdown += '<div style="position:absolute; z-index:30; top:calc(100% + 8px); left:0; width:100%; background:#fff; border:1px solid #E5E9EF; border-radius:14px; box-shadow:0 12px 32px rgba(60,50,20,0.16); overflow:hidden;">' +
        '<div style="padding:10px; border-bottom:1px solid #E5E9EF; position:relative;"><span class="msr" style="position:absolute; left:20px; top:50%; transform:translateY(-50%); color:#8a9187; font-size:20px;">search</span><input data-action="companyQuery" data-focus="companyQuery" value="' + esc(state.companyQuery || '') + '" placeholder="会社名で検索…" style="width:100%; border:1.5px solid #DDE9E2; border-radius:10px; padding:10px 12px 10px 40px; font-size:14px; outline:none; background:#F8FAF8; font-family:inherit;"></div>' +
        '<div style="max-height:320px; overflow-y:auto;">' + rows + '</div>' +
        '<div style="padding:8px 14px; border-top:1px solid #E5E9EF; font-size:11px; color:#8a9187; text-align:right;">全' + COMPANIES.length + '社</div></div>';
    }
    dropdown += '</div>';
    return cards + dropdown;
  }

  // ---- 結果の保存・共有 ----
  function shareText() {
    var v = compute(), c = currentCompany();
    var name = c ? c.name : 'サクナビ';
    if (v.base.impossible) return name + 'の返済シミュレーション（サクナビ）';
    return [
      '【' + name + ' 返済シミュレーション】',
      '借入額：' + fmt(v.P) + '円 ／ 毎月：' + fmt(v.M) + '円',
      '金利：' + state.rate + '％（実質年率）' + (state.interestFree && c && c.freeDays > 0 ? '（無利息' + c.freeDays + '日適用）' : ''),
      '総返済額：' + fmt(v.base.totalPaid) + '円',
      '利息合計：' + fmt(v.base.totalInterest) + '円',
      '返済期間：' + termLabel(v.base.months) + '（' + v.base.months + '回）',
      '計画評価：' + v.grade.g + '（' + v.grade.label + '）'
    ].join('\n');
  }
  function shareURL() { try { return location.href.split('#')[0]; } catch (e) { return location.origin || ''; } }
  function ga(ev, extra) { try { if (typeof window.gtag === 'function') window.gtag('event', ev, extra || {}); } catch (e) { } }

  function downloadBlob(filename, content, mime) {
    try {
      var blob = new Blob([content], { type: mime });
      var url = URL.createObjectURL(blob);
      var a = document.createElement('a');
      a.href = url; a.download = filename;
      document.body.appendChild(a); a.click();
      setTimeout(function () { document.body.removeChild(a); URL.revokeObjectURL(url); }, 100);
    } catch (e) { alert('ダウンロードに失敗しました: ' + e); }
  }
  function dlTxt() {
    var c = currentCompany();
    downloadBlob('返済シミュレーション_' + (c ? c.name : 'サクナビ') + '.txt', shareText() + '\n\n' + shareURL(), 'text/plain;charset=utf-8');
    ga('simulator_save_click', { method: 'text', company: c ? c.id : '' });
  }
  function dlCsv() {
    var v = compute(), c = currentCompany();
    if (v.base.impossible) { alert('この条件では完済できないため、CSVを出力できません。'); return; }
    var rows = [];
    rows.push(['カードローン返済シミュレーション']);
    rows.push(['会社', c ? c.name : '(未選択)']);
    rows.push(['借入額(円)', v.P]);
    rows.push(['毎月返済額(円)', v.M]);
    rows.push(['実質年率(%)', state.rate]);
    rows.push(['無利息期間(日)', (state.interestFree && c && c.freeDays > 0) ? c.freeDays : 0]);
    rows.push(['総返済額(円)', v.base.totalPaid]);
    rows.push(['利息合計(円)', v.base.totalInterest]);
    rows.push(['返済回数', v.base.months]);
    rows.push(['計画評価', v.grade.g + ' ' + v.grade.label]);
    rows.push([]);
    rows.push(['回数', '年月', '返済額(円)', '元金充当(円)', '利息(円)', '残高(円)']);
    v.base.schedule.forEach(function (r) { rows.push([r.n, ymLabel(r.n - 1), r.payment, r.principalApplied, r.interest, r.balance]); });
    var csv = rows.map(function (row) {
      return row.map(function (cell) {
        var s = String(cell == null ? '' : cell);
        return /[",\n]/.test(s) ? '"' + s.replace(/"/g, '""') + '"' : s;
      }).join(',');
    }).join('\r\n');
    downloadBlob('返済スケジュール_' + (c ? c.name : 'サクナビ') + '.csv', '﻿' + csv, 'text/csv;charset=utf-8');
    ga('simulator_save_click', { method: 'csv', company: c ? c.id : '' });
  }
  function dlPdf() { ga('simulator_save_click', { method: 'pdf' }); window.print(); }
  function copyRes() {
    var t = shareText() + '\n' + shareURL();
    var done = function () { alert('結果をコピーしました。'); ga('simulator_save_click', { method: 'copy' }); };
    if (navigator.clipboard && navigator.clipboard.writeText) { navigator.clipboard.writeText(t).then(done, function () { fallbackCopy(t); done(); }); }
    else { fallbackCopy(t); done(); }
  }
  function fallbackCopy(t) { try { var ta = document.createElement('textarea'); ta.value = t; ta.style.position = 'fixed'; ta.style.opacity = '0'; document.body.appendChild(ta); ta.select(); document.execCommand('copy'); document.body.removeChild(ta); } catch (e) { } }
  function shareLine() { window.open('https://line.me/R/msg/text/?' + encodeURIComponent(shareText() + '\n' + shareURL()), '_blank'); ga('simulator_save_click', { method: 'line' }); }
  function shareX() { window.open('https://twitter.com/intent/tweet?text=' + encodeURIComponent(shareText()) + '&url=' + encodeURIComponent(shareURL()), '_blank'); ga('simulator_save_click', { method: 'x' }); }

  function actionBtn(action, icon, label, bg, fg, brd) {
    return '<button data-action="' + action + '" style="cursor:pointer; font-family:inherit; display:inline-flex; align-items:center; gap:6px; padding:10px 16px; border-radius:11px; border:1.5px solid ' + (brd || bg) + '; background:' + bg + '; color:' + fg + '; font-size:13px; font-weight:700; white-space:nowrap;"><span class="msr" style="font-size:18px;">' + icon + '</span>' + label + '</button>';
  }
  function resultActions() {
    return '<div class="snv-noprint" style="background:#fff; border:1px solid #E5E9EF; border-radius:20px; padding:20px 24px;">' +
      '<div style="display:flex; align-items:center; gap:8px; margin-bottom:14px;"><span class="msr" style="color:#39B167; font-size:20px;">ios_share</span><span style="font-weight:700; font-size:15px;">結果を保存・共有</span></div>' +
      '<div style="display:flex; flex-wrap:wrap; gap:10px;">' +
      actionBtn('dlPdf', 'picture_as_pdf', 'PDF保存', '#fff', '#3f4a3e', '#DDE9E2') +
      actionBtn('dlCsv', 'table_view', 'CSV（Excel）', '#fff', '#3f4a3e', '#DDE9E2') +
      actionBtn('dlTxt', 'description', 'テキスト', '#fff', '#3f4a3e', '#DDE9E2') +
      actionBtn('copyRes', 'content_copy', 'コピー', '#fff', '#3f4a3e', '#DDE9E2') +
      actionBtn('shareLine', 'chat', 'LINEで共有', '#06C755', '#fff', '#06C755') +
      actionBtn('shareX', 'share', 'Xで共有', '#1a1a1a', '#fff', '#1a1a1a') +
      '</div></div>';
  }

  // ---- 描画（部分更新） ----
  // 条件設定（aside）と結果パネルは別々のDOMコンテナに分離し、それぞれ
  // 必要なときだけ更新する。principal/monthly/rate/start の入力中は
  // 結果パネルしか触らないため、入力欄自体が作り直されずカーソル位置が保たれる。
  function paintCompanies() {
    // companyQuery（検索ボックス）だけは、この関数自身が作り直す対象なので
    // フォーカス位置を保持する
    var focus = document.activeElement;
    var focusKey = (focus && focus.getAttribute) ? focus.getAttribute('data-focus') : null;
    var selStart = (focus && focus.selectionStart != null) ? focus.selectionStart : null;

    document.getElementById('snv-companies').innerHTML = renderCompanies();

    if (focusKey === 'companyQuery') {
      var next = document.getElementById('snv-companies').querySelector('[data-focus="companyQuery"]');
      if (next) { next.focus(); try { if (selStart != null) next.setSelectionRange(selStart, selStart); } catch (e) { } }
    }
  }
  function paintHero() {
    var c = currentCompany();
    document.getElementById('snv-company').textContent = c ? c.name : 'カードローン返済シミュレーション';

    var maxRateEl = document.getElementById('snv-maxrate');
    if (c) { maxRateEl.style.display = ''; maxRateEl.textContent = '実質年率上限 ' + c.maxRate + '%'; }
    else { maxRateEl.style.display = 'none'; }

    var freeEl = document.getElementById('snv-freedays');
    if (c && c.freeDays > 0) { freeEl.style.display = ''; freeEl.textContent = '無利息 ' + c.freeDays + '日間'; }
    else { freeEl.style.display = 'none'; }

    var ctaBtn = document.getElementById('snv-cta-apply');
    if (ctaBtn) {
      if (c) { ctaBtn.href = c.ctaUrl; ctaBtn.style.display = ''; }
      else { ctaBtn.style.display = 'none'; }
    }
  }
  function ensureSkeleton() {
    if (!document.getElementById('snv-aside')) {
      document.getElementById('snv-main').innerHTML =
        '<div class="layoutGrid"><aside id="snv-aside" style="align-self:start;"></aside><div id="snv-results" style="display:flex; flex-direction:column; gap:20px;"></div></div>';
    }
  }
  function paintAside() {
    ensureSkeleton();
    document.getElementById('snv-aside').innerHTML = inputPanelB();
  }
  function paintResults() {
    ensureSkeleton();
    document.getElementById('snv-results').innerHTML = resultsMarkup(compute());
  }
  // 会社選択・無利息トグルなど、条件設定欄の表示内容自体が変わるとき用
  function renderFull() {
    paintCompanies();
    paintHero();
    paintAside();
    paintResults();
  }

  // ---- イベント委譲 ----
  root.addEventListener('input', function (e) {
    var a = e.target.getAttribute && e.target.getAttribute('data-action');
    if (!a) return;
    if (a === 'principal') { state.principal = sanitizeInt(e.target.value); paintResults(); }
    else if (a === 'monthly') { state.monthly = sanitizeInt(e.target.value); paintResults(); }
    else if (a === 'rate') { state.rate = sanitizeDecimal(e.target.value); paintResults(); }
    else if (a === 'companyQuery') { state.companyQuery = e.target.value; paintCompanies(); }
    else if (a === 'start') {
      var v = (e.target.value || (state.startY + '-' + pad2(state.startM))).split('-');
      state.startY = Number(v[0]); state.startM = Number(v[1]);
      paintResults();
    }
  });
  root.addEventListener('change', function (e) {
    var a = e.target.getAttribute && e.target.getAttribute('data-action');
    if (a === 'free') { state.interestFree = e.target.checked; paintResults(); }
    else if (a === 'check') { var i = Number(e.target.getAttribute('data-idx')); state.checks[i] = e.target.checked; paintResults(); }
  });
  root.addEventListener('click', function (e) {
    var btn = e.target.closest('[data-action]');
    var a = btn && btn.getAttribute('data-action');
    if (a === 'companyToggle') { state.companyOpen = !state.companyOpen; state.companyQuery = ''; paintCompanies(); return; }
    if (a === 'company') { applyCompany(btn.getAttribute('data-id')); state.companyOpen = false; state.companyQuery = ''; renderFull(); return; }
    if (a === 'companyClear') { state.companyId = null; state.companyOpen = false; state.companyQuery = ''; renderFull(); return; }
    if (a === 'companyQuery') { return; }
    if (a === 'toggleAll') { state.showAll = !state.showAll; paintResults(); return; }
    if (a === 'dlPdf') { dlPdf(); return; }
    if (a === 'dlCsv') { dlCsv(); return; }
    if (a === 'dlTxt') { dlTxt(); return; }
    if (a === 'copyRes') { copyRes(); return; }
    if (a === 'shareLine') { shareLine(); return; }
    if (a === 'shareX') { shareX(); return; }
    // 外側クリックでドロップダウンを閉じる
    if (state.companyOpen && !e.target.closest('#snv-picker')) { state.companyOpen = false; paintCompanies(); }
  });

  renderFull();
})();
