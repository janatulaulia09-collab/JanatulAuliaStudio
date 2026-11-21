<?php if (!isset($pdo)) { return; } ?>
<div id="studio-chat" class="fixed bottom-5 right-5 z-50 font-sans">
  <section id="studio-chat-panel" class="hidden mb-3 w-[min(92vw,390px)] overflow-hidden rounded-2xl border border-stone-200 bg-white shadow-2xl" aria-label="Asisten JanatulAulia">
    <div class="flex items-center justify-between bg-[#17211f] px-5 py-4 text-white">
      <div>
        <p class="font-semibold">Asisten JanatulAulia</p>
        <p class="mt-1 text-[11px] text-white/60">Konsultasi informasi awal studio</p>
      </div>
      <button id="studio-chat-close" type="button" class="rounded-full px-2 py-1 text-xl text-white/70 hover:bg-white/10" aria-label="Tutup chatbot">×</button>
    </div>
    <div id="studio-chat-messages" class="max-h-[360px] min-h-[210px] space-y-3 overflow-y-auto bg-[#f7f5f0] p-4" aria-live="polite">
      <div class="max-w-[90%] rounded-xl bg-white p-3 text-sm leading-6 text-stone-700 shadow-sm">Halo! Aku bisa membantu menjelaskan layanan, portofolio, dan proses awal JanatulAulia Studio. Apa yang ingin kamu ketahui?</div>
    </div>
    <div class="border-t border-stone-100 bg-white p-3">
      <p class="mb-2 text-[10px] leading-4 text-stone-400">Jangan kirim nomor telepon, email, password, atau informasi pribadi melalui chat.</p>
      <form id="studio-chat-form" class="flex gap-2">
        <label class="sr-only" for="studio-chat-input">Pertanyaan</label>
        <input id="studio-chat-input" name="question" maxlength="1000" required class="min-w-0 flex-1 rounded-xl border border-stone-200 px-3 py-3 text-sm outline-none focus:border-[#a17b4d]" placeholder="Tulis pertanyaan...">
        <button id="studio-chat-send" type="submit" class="rounded-xl bg-[#1c2b27] px-4 py-3 text-sm font-semibold text-white disabled:opacity-50">Kirim</button>
      </form>
      <a href="<?= e(app_url('index.php#brief')) ?>" class="mt-3 inline-block text-xs font-semibold text-[#98744d]">Ingin mengajukan proyek? Isi Project Brief ↗</a>
    </div>
  </section>
  <button id="studio-chat-toggle" type="button" class="flex items-center gap-2 rounded-full bg-[#17211f] px-5 py-4 text-sm font-semibold text-white shadow-xl transition hover:bg-[#2a3b34]" aria-expanded="false" aria-controls="studio-chat-panel">
    <span aria-hidden="true">✦</span><span>Tanya Asisten AI</span>
  </button>
</div>
<script>
(() => {
  const endpoint = <?= json_encode(app_url('api/chat.php'), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
  const csrfToken = <?= json_encode(csrf_token(), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
  const panel = document.getElementById('studio-chat-panel');
  const toggle = document.getElementById('studio-chat-toggle');
  const close = document.getElementById('studio-chat-close');
  const form = document.getElementById('studio-chat-form');
  const input = document.getElementById('studio-chat-input');
  const send = document.getElementById('studio-chat-send');
  const messages = document.getElementById('studio-chat-messages');
  const history = [];

  function addMessage(role, content) {
    const bubble = document.createElement('div');
    bubble.className = role === 'user'
      ? 'ml-auto max-w-[90%] rounded-xl bg-[#dce5de] p-3 text-sm leading-6 text-[#18252b]'
      : 'max-w-[90%] rounded-xl bg-white p-3 text-sm leading-6 text-stone-700 shadow-sm';
    bubble.textContent = content;
    messages.appendChild(bubble);
    messages.scrollTop = messages.scrollHeight;
    return bubble;
  }

  function setOpen(open) {
    panel.classList.toggle('hidden', !open);
    toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
    if (open) input.focus();
  }

  toggle.addEventListener('click', () => setOpen(panel.classList.contains('hidden')));
  close.addEventListener('click', () => setOpen(false));

  form.addEventListener('submit', async (event) => {
    event.preventDefault();
    const question = input.value.trim();
    if (!question || send.disabled) return;

    addMessage('user', question);
    const historyForRequest = history.slice(-6);
    history.push({role: 'user', content: question});
    input.value = '';
    send.disabled = true;
    send.textContent = '...';
    const pending = addMessage('assistant', 'Sedang menyiapkan jawaban...');

    try {
      const response = await fetch(endpoint, {
        method: 'POST',
        headers: {'Content-Type': 'application/json', 'X-CSRF-Token': csrfToken},
        body: JSON.stringify({question, history: historyForRequest})
      });
      const responseText = await response.text();
      let data;
      try {
        data = JSON.parse(responseText);
      } catch (parseError) {
        console.error('[JanatulAulia Chatbot] Endpoint returned non-JSON.', {
          httpStatus: response.status,
          responsePreview: responseText.slice(0, 300)
        });
        pending.textContent = `Endpoint chatbot mengembalikan respons tidak valid (HTTP ${response.status}). Periksa log PHP/Apache.`;
        return;
      }
      const reply = typeof data.reply === 'string'
        ? data.reply
        : `Server merespons HTTP ${response.status}, tetapi format jawabannya tidak dikenali.`;
      pending.textContent = reply;
      if (data.ok) history.push({role: 'assistant', content: reply});
      if (history.length > 8) history.splice(0, history.length - 8);
    } catch (error) {
      console.error('[JanatulAulia Chatbot] Browser request failed.', error);
      pending.textContent = 'Browser gagal menghubungi endpoint chatbot. Periksa Apache, URL api/chat.php, dan koneksi jaringan lokal.';
    } finally {
      send.disabled = false;
      send.textContent = 'Kirim';
      input.focus();
      messages.scrollTop = messages.scrollHeight;
    }
  });
})();
</script>
