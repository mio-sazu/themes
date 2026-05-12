<?php
/* Template Name: Chat Page */
get_header();
?>
<div class="chat-container"></div>
<form id="chatForm">
    <input type="text" id="chatInput" placeholder="メッセージを入力…" autocomplete="off" required>
    <button type="submit">送信</button>
</form>
<?php get_footer(); ?>