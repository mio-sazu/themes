document.addEventListener("DOMContentLoaded", ()=> {
    const btn     = document.getElementById("calculateBtn");
    const result  = document.getElementById("simResult");
    const getNum  = (id) => {
      const sel = document.getElementById(id);
      if(!sel || !sel.value) return null;
      return Number(sel.value);
    };
  
    btn.addEventListener("click", ()=> {
      const amount  = getNum("sim-amount");
      const months  = getNum("sim-term");
      const rate    = getNum("sim-rate");
  
      if (!amount || !months || !rate) {
        alert("借り入れ額・返済期間・金利をすべて選択してください。");
        return;
      }
  
      const monthlyRate    = rate / 100 / 12;
      const monthlyPayment = amount * (monthlyRate * Math.pow(1 + monthlyRate, months))
                               / (Math.pow(1 + monthlyRate, months) - 1);
      const total          = monthlyPayment * months;
  
      result.innerHTML = `
        <p>📌 毎月の返済額：<strong>${(monthlyPayment/10000).toFixed(1)}万円</strong>（全${months}回）</p>
        <p>💰 総返済額：<strong>${(total/10000).toFixed(1)}万円</strong></p>
      `;
      result.style.display = "block";
    });
  });
  