// ================== SAFE HELPERS ==================
function safeLucideInit() {
  if (window.lucide && typeof window.lucide.createIcons === "function") {
    window.lucide.createIcons();
  }
}

// Run once (won't crash if Lucide is missing)
safeLucideInit();

// ================== NAVBAR SCROLL EFFECT ==================
const nav = document.getElementById('navbar');
const navText = document.getElementById('nav-text');
const navLinks = document.getElementById('nav-links');
const mobileBtn = document.getElementById('mobile-menu-btn');

window.addEventListener('scroll', () => {
  if (!nav) return;

  if (window.scrollY > 50) {
    nav.classList.remove('nav-default');
    nav.classList.add('scroll-active');

    navText?.classList.remove('text-white');
    navText?.classList.add('text-scroll');

    navLinks?.classList.remove('text-white');
    navLinks?.classList.add('text-scroll');

    mobileBtn?.classList.remove('text-white');
    mobileBtn?.classList.add('text-scroll');
  } else {
    nav.classList.add('nav-default');
    nav.classList.remove('scroll-active');

    navText?.classList.add('text-white');
    navText?.classList.remove('text-scroll');

    navLinks?.classList.add('text-white');
    navLinks?.classList.remove('text-scroll');

    mobileBtn?.classList.add('text-white');
    mobileBtn?.classList.remove('text-scroll');
  }
});

// ================== MOBILE MENU TOGGLE (SAFE) ==================
const mobileMenuBtn = document.getElementById('mobile-menu-btn');
if (mobileMenuBtn) {
  mobileMenuBtn.addEventListener('click', () => {
    document.getElementById('mobile-menu')?.classList.toggle('hidden');
  });
}

// ================== NEWS FILTER ==================
function filterNews(category) {
  const items = document.querySelectorAll('.news-item');
  const buttons = document.querySelectorAll('.filter-btn');

  buttons.forEach(btn => {
    if (btn.dataset.category === category) {
      btn.classList.remove('bg-white', 'text-slate-600', 'hover:bg-slate-200');
      btn.classList.add('bg-red-600', 'text-white', 'shadow-md');
    } else {
      btn.classList.add('bg-white', 'text-slate-600', 'hover:bg-slate-200');
      btn.classList.remove('bg-red-600', 'text-white', 'shadow-md');
    }
  });

  items.forEach(item => {
    const itemCat = item.dataset.category;
    const isUrgent = item.dataset.urgent === 'true';

    if (category === 'All') item.style.display = 'flex';
    else if (category === 'Urgent') item.style.display = isUrgent ? 'flex' : 'none';
    else item.style.display = itemCat === category ? 'flex' : 'none';
  });
}

// (Optional) expose to HTML onclick usage
window.filterNews = filterNews;

// ================== CHATBOT ==================
document.addEventListener('DOMContentLoaded', function () {
  const chatbotHeader = document.getElementById('chatbot-header');
  const chatbotBody = document.getElementById('chatbot-body');
  const chatbotInputArea = document.getElementById('chatbot-input-area');
  const chatbotInput = document.getElementById('chatbot-input');
  const chatbotSend = document.getElementById('chatbot-send');
  const chatbotUsername = document.getElementById('chatbot-username');

  if (!chatbotHeader || !chatbotBody || !chatbotInputArea) {
    console.error("Chatbot elements missing: check chatbot.php is included.");
    return;
  }

  let username = 'Guest';

  // Toggle chatbot on header click
  chatbotHeader.addEventListener('click', () => {
    const isHidden = chatbotBody.classList.contains('hidden');
    chatbotBody.classList.toggle('hidden', !isHidden);
    chatbotInputArea.classList.toggle('hidden', !isHidden);

    // Re-init icons only if Lucide exists
    safeLucideInit();
  });

  // Username entry
  if (chatbotUsername) {
    chatbotUsername.addEventListener('keypress', (e) => {
      if (e.key === 'Enter' && chatbotUsername.value.trim() !== '') {
        username = chatbotUsername.value.trim();
        chatbotUsername.disabled = true;
        appendMessage(`Hello ${username}! How can I assist you today?`, 'bot-msg');
      }
    });
  }

  // Send message
  chatbotSend?.addEventListener('click', sendMessage);
  chatbotInput?.addEventListener('keypress', (e) => {
    if (e.key === 'Enter') sendMessage();
  });

  function sendMessage() {
    const msg = chatbotInput.value.trim();
    if (!msg) return;

    appendMessage(msg, 'user-msg');
    chatbotInput.value = '';

    fetch('chatbot-response.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: `message=${encodeURIComponent(msg)}&username=${encodeURIComponent(username)}`
    })
      .then(res => res.text())
      .then(data => appendMessage(data, 'bot-msg', true))
      .catch(err => {
        console.error('Chatbot error:', err);
        appendMessage('Sorry, I had trouble connecting. Please try again.', 'bot-msg');
      });
  }

  function appendMessage(message, type, parseHTML = false) {
    const msgDiv = document.createElement('div');
    msgDiv.classList.add('chatbot-msg', type);

    const now = new Date();
    const time = now.getHours().toString().padStart(2, '0') + ':' + now.getMinutes().toString().padStart(2, '0');

    if (parseHTML) {
      msgDiv.innerHTML = message + `<div class="timestamp">${time}</div>`;
    } else {
      msgDiv.textContent = message;
      const ts = document.createElement('div');
      ts.className = 'timestamp';
      ts.textContent = time;
      msgDiv.appendChild(ts);
    }

    chatbotBody.appendChild(msgDiv);
    chatbotBody.scrollTop = chatbotBody.scrollHeight;
  }
});
