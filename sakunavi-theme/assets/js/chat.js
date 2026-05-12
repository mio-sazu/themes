document.addEventListener('DOMContentLoaded', () => {
    const container = document.querySelector('.chat-container');
    const form      = document.getElementById('chatForm');
    const input     = document.getElementById('chatInput');
    let lastDate    = null;
  
    // メッセージ取得＆描画
    async function fetchMessages() {
      let url = chatSettings.restRoot + 'messages';
      if (lastDate) {
        url += '?after=' + encodeURIComponent(lastDate);
      }
      const res = await fetch(url);
      if (!res.ok) return;
      const msgs = await res.json();
      msgs.forEach(m => {
        const el = createBubble(m);
        container.appendChild(el);
        lastDate = m.date;
      });
      container.scrollTop = container.scrollHeight;
    }
  
    // バブル要素を作る
    function createBubble({ content, author }) {
      const wrap = document.createElement('div');
      wrap.classList.add('message');
      wrap.classList.add(author === chatSettings.currentUser ? 'sent' : 'received');
      const bubble = document.createElement('div');
      bubble.classList.add('bubble');
      bubble.textContent = content;
      wrap.appendChild(bubble);
      return wrap;
    }
  
    // 送信処理
    form.addEventListener('submit', async e => {
      e.preventDefault();
      const text = input.value.trim();
      if (!text) return;
      await fetch(chatSettings.restRoot + 'messages', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-WP-Nonce':    chatSettings.nonce,
        },
        body: JSON.stringify({ content: text }),
      });
      input.value = '';
      fetchMessages();
    });
  
    // 初回とポーリング
    fetchMessages();
    setInterval(fetchMessages, 5000);  // 5秒ごとに新着を取得
  });
  

 // LINE
 $(function() {
    $('.ballon-image-left').find('p,br').remove();
    $('.ballon-image-right').find('p,br').remove();
});