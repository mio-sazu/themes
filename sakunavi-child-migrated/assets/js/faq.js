// FAQ accordion (delegation, accessible)
document.addEventListener('click', (e) => {
  const btn = e.target.closest('.company_faq_question');
  if (!btn) return;

  const box = btn.closest('.company_faq_box');
  const ans = box ? box.querySelector('.company_faq_answer') : null;
  if (!ans) return;

  const expanded = btn.getAttribute('aria-expanded') === 'true';
  btn.setAttribute('aria-expanded', String(!expanded));

  if (expanded) {
    ans.hidden = true;
    box.classList.remove('active');
  } else {
    ans.hidden = false;
    box.classList.add('active');
  }
});

// column FAQ
document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('.faq-item').forEach(item => {
    const btn = item.querySelector('.faq-question');
    const answer = item.querySelector('.faq-answer');

    if (!btn || !answer) return;

    answer.hidden = true;
    btn.setAttribute('aria-expanded', 'false');

    btn.addEventListener('click', () => {
      const isOpen = btn.getAttribute('aria-expanded') === 'true';

      btn.setAttribute('aria-expanded', isOpen ? 'false' : 'true');
      answer.hidden = isOpen;
      item.classList.toggle('is-open', !isOpen);

      const icon = btn.querySelector('.faq-icon');
      if (icon) {
        icon.textContent = isOpen ? '+' : '−';
      }
    });
  });
});