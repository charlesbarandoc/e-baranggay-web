function safeLucideInit() {
  if (window.lucide && typeof window.lucide.createIcons === "function") {
    window.lucide.createIcons();
  }
}


safeLucideInit();


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


const mobileMenuBtn = document.getElementById('mobile-menu-btn');
if (mobileMenuBtn) {
  mobileMenuBtn.addEventListener('click', () => {
    document.getElementById('mobile-menu')?.classList.toggle('hidden');
  });
}


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


window.filterNews = filterNews;


document.addEventListener('DOMContentLoaded', function () {
  const chatbotHeader = document.getElementById('chatbot-header');
  const chatbotBody = document.getElementById('chatbot-body');
  const chatbotInputArea = document.getElementById('chatbot-input-area');
  const chatbotInput = document.getElementById('chatbot-input');
  const chatbotSend = document.getElementById('chatbot-send');
  const chatbotUsername = document.getElementById('chatbot-username');
  const quickReplies = document.getElementById('chatbot-quick-replies');


  const docModal = document.getElementById('documentModal');
  const emergencyModal = document.getElementById('emergencyModal');
  const docForm = document.getElementById('documentForm');
  const formMessage = document.getElementById('formMessage');
  const submitBtn = document.getElementById('submitBtn');
  const modalTitle = document.getElementById('modalTitle');
  const modalDescription = document.getElementById('modalDescription');
  const documentType = document.getElementById('documentType');

  if (!chatbotHeader || !chatbotBody || !chatbotInputArea) {
    console.error("Chatbot elements missing: check chatbot.php is included.");
    return;
  }

  let username = 'Guest';


  chatbotHeader.addEventListener('click', () => {
    const isHidden = chatbotBody.classList.contains('hidden');
    chatbotBody.classList.toggle('hidden', !isHidden);
    chatbotInputArea.classList.toggle('hidden', !isHidden);


    safeLucideInit();
  });


  if (chatbotUsername) {
    chatbotUsername.addEventListener('keypress', (e) => {
      if (e.key === 'Enter' && chatbotUsername.value.trim() !== '') {
        username = chatbotUsername.value.trim();
        chatbotUsername.disabled = true;
        appendMessage(`Hello ${username}! How can I assist you today?`, 'bot-msg');

      
        if (quickReplies) {
  quickReplies.classList.remove('hidden');
  quickReplies.style.display = 'flex';   
  quickReplies.style.flexWrap = 'wrap';
  quickReplies.style.gap = '8px';
}

      }
    });
  }

  
  quickReplies?.addEventListener('click', (e) => {
    const btn = e.target.closest('button');
    if (!btn) return;

    const action = btn.dataset.action;
    const message = btn.dataset.message;

    if (action === 'openDocument') {
      openDocumentModal(btn.dataset.doc);
      return;
    }
    if (action === 'openEmergency') {
      openEmergencyModal();
      return;
    }

    if (message) {
      chatbotInput.value = message;
      sendMessage();
    }
  });

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
      .then(res => res.json())
      .then(data => appendMessage((data && data.reply) ? data.reply : 'Sorry, I could not understand the response.', 'bot-msg'))
      .catch(err => {
        console.error('Chatbot error:', err);
        appendMessage('Sorry, I had trouble connecting. Please try again.', 'bot-msg');
      });
  }

  function appendMessage(message, type) {
    const msgDiv = document.createElement('div');
    msgDiv.classList.add('chatbot-msg', type);

    const now = new Date();
    const time = now.getHours().toString().padStart(2, '0') + ':' + now.getMinutes().toString().padStart(2, '0');


    msgDiv.textContent = message;

    const ts = document.createElement('div');
    ts.className = 'timestamp';
    ts.textContent = time;
    msgDiv.appendChild(ts);

    chatbotBody.appendChild(msgDiv);
    chatbotBody.scrollTop = chatbotBody.scrollHeight;
  }

 
  const docInfo = {
    barangay_clearance: {
      title: 'Barangay Clearance',
      desc: 'For employment, permits, IDs, and general verification.'
    },
    certificate_of_residency: {
      title: 'Certificate of Residency',
      desc: 'Proof of residency within the barangay.'
    },
    certificate_of_indigency: {
      title: 'Certificate of Indigency',
      desc: 'For medical, educational, or government assistance requirements.'
    },
    barangay_id: {
      title: 'Barangay ID',
      desc: 'Request for barangay identification.'
    }
  };

  function openDocumentModal(type) {
    if (!docModal || !documentType || !modalTitle || !modalDescription) {
      // Fallback: send a message instead
      chatbotInput.value = type ? `request ${type}` : 'request document';
      sendMessage();
      return;
    }

    const info = docInfo[type] || { title: 'Document Request', desc: 'Please fill out the form to request your document.' };
    documentType.value = type || '';
    modalTitle.textContent = info.title;
    modalDescription.textContent = info.desc;

  
    if (formMessage) {
      formMessage.classList.add('hidden');
      formMessage.textContent = '';
      formMessage.className = 'mt-4 hidden';
    }

    docModal.classList.add('active');
  }

  function closeModal() {
    docModal?.classList.remove('active');
  }

  function openEmergencyModal() {
    emergencyModal?.classList.add('active');
    safeLucideInit();
  }

  function closeEmergencyModal() {
    emergencyModal?.classList.remove('active');
  }

  
  window.closeModal = closeModal;
  window.openEmergencyModal = openEmergencyModal;
  window.closeEmergencyModal = closeEmergencyModal;

 
  if (docForm) {
    docForm.addEventListener('submit', async (e) => {
      e.preventDefault();
      if (!submitBtn) return;

      const lastDocType = documentType?.value || '';

      submitBtn.disabled = true;
      submitBtn.textContent = 'Submitting...';

      try {
        const fd = new FormData(docForm);
        const res = await fetch('document-request.php', { method: 'POST', body: fd });
        const data = await res.json();

        if (data && data.success) {
          const ref = data.reference_number ? `\nReference: ${data.reference_number}` : '';
          showFormMessage(`✅ Request sent successfully!${ref}`, true);

         
          appendMessage(`Your document request was submitted.${ref}`, 'bot-msg');

          docForm.reset();
     
          if (documentType) documentType.value = lastDocType;
        } else {
          showFormMessage(`❌ ${data?.message || 'Failed to submit request.'}`, false);
        }
      } catch (err) {
        console.error('Document request error:', err);
        showFormMessage('❌ Network error. Please try again.', false);
      } finally {
        submitBtn.disabled = false;
        submitBtn.textContent = 'Submit Request';
      }
    });
  }

  function showFormMessage(text, success) {
    if (!formMessage) return;
    formMessage.classList.remove('hidden');
    formMessage.textContent = text;
    formMessage.className = `mt-4 p-3 rounded-lg text-sm ${success ? 'bg-green-50 text-green-800' : 'bg-red-50 text-red-800'}`;
  }
});

window.openDocumentModal = function (type) {
  const docModal = document.getElementById('documentModal');
  const documentType = document.getElementById('documentType');
  const modalTitle = document.getElementById('modalTitle');
  const modalDescription = document.getElementById('modalDescription');

  if (!docModal || !documentType || !modalTitle || !modalDescription) {
    console.error('Document modal elements not found.');
    return;
  }

  const docInfo = {
    barangay_clearance: {
      title: 'Barangay Clearance',
      desc: 'For employment, business permit, or ID requirements.'
    },
    certificate_of_indigency: {
      title: 'Certificate of Indigency',
      desc: 'For educational assistance, medical help, or legal aid.'
    },
    certificate_of_residency: {
      title: 'Certificate of Residency',
      desc: 'Proof of residency for voters registration or bank purposes.'
    }
  };

  const info = docInfo[type] || {
    title: 'Document Request',
    desc: 'Please complete the form below.'
  };

  documentType.value = type;
  modalTitle.textContent = info.title;
  modalDescription.textContent = info.desc;

  docModal.classList.add('active');
};


  const CSRF_TOKEN = "<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? ''); ?>";

  const promptsTbody = document.getElementById('promptsTbody');
  const btnPromptsRefresh = document.getElementById('btnPromptsRefresh');

  async function loadPrompts() {
    if (!promptsTbody) return;

    promptsTbody.innerHTML = `<tr><td colspan="3" class="p-4 text-slate-500">Loading...</td></tr>`;

    try {
      const res = await fetch('chatbot-prompts-api.php', { method: 'GET' });
      const data = await res.json();

      if (!data.success) {
        promptsTbody.innerHTML = `<tr><td colspan="3" class="p-4 text-red-600">${data.message || 'Failed to load prompts'}</td></tr>`;
        return;
      }

      renderPrompts(data.data || []);
    } catch (err) {
      console.error(err);
      promptsTbody.innerHTML = `<tr><td colspan="3" class="p-4 text-red-600">Network error loading prompts.</td></tr>`;
    }
  }

  function escapeHtml(str) {
    return String(str ?? '')
      .replaceAll('&', '&amp;')
      .replaceAll('<', '&lt;')
      .replaceAll('>', '&gt;')
      .replaceAll('"', '&quot;')
      .replaceAll("'", '&#039;');
  }

  function renderPrompts(rows) {
    if (!rows.length) {
      promptsTbody.innerHTML = `<tr><td colspan="3" class="p-4 text-slate-500">No prompts yet.</td></tr>`;
      return;
    }

    promptsTbody.innerHTML = rows.map(r => {
      const preview = (r.reply || '').length > 80 ? (r.reply.slice(0, 80) + '…') : (r.reply || '');
      return `
        <tr class="border-t">
          <td class="p-3 font-medium text-slate-800">${escapeHtml(r.keyword)}</td>
          <td class="p-3 text-slate-600">${escapeHtml(preview)}</td>
          <td class="p-3">
            <div class="flex gap-2">
              <button class="px-3 py-1 rounded bg-slate-200 hover:bg-slate-300 text-slate-800 text-sm"
                      onclick="editPrompt(${r.id}, '${escapeHtml(r.keyword)}', '${escapeHtml(r.reply)}')">
                Edit
              </button>
              <button class="px-3 py-1 rounded bg-red-600 hover:bg-red-700 text-white text-sm"
                      onclick="deletePrompt(${r.id})">
                Delete
              </button>
            </div>
          </td>
        </tr>
      `;
    }).join('');
  }

  // Create prompt (connect to your form)
  async function createPrompt(keyword, reply) {
    const res = await fetch('chatbot-prompts-api.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ csrf_token: CSRF_TOKEN, keyword, reply })
    });
    const data = await res.json();
    if (!data.success) alert(data.message || 'Failed to create prompt');
    await loadPrompts();
  }

  // Edit prompt: simple prompt() UI (you can replace with modal later)
  window.editPrompt = async function(id, keyword, reply) {
    const newKeyword = prompt('Edit keyword:', keyword);
    if (newKeyword === null) return;

    const newReply = prompt('Edit reply:', reply);
    if (newReply === null) return;

    const res = await fetch('chatbot-prompts-api.php', {
      method: 'PUT',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ csrf_token: CSRF_TOKEN, id, keyword: newKeyword, reply: newReply })
    });

    const data = await res.json();
    if (!data.success) alert(data.message || 'Failed to update prompt');
    await loadPrompts();
  }

  window.deletePrompt = async function(id) {
    if (!confirm('Delete this prompt?')) return;

    const res = await fetch('chatbot-prompts-api.php', {
      method: 'DELETE',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ csrf_token: CSRF_TOKEN, id })
    });

    const data = await res.json();
    if (!data.success) alert(data.message || 'Failed to delete prompt');
    await loadPrompts();
  }

  // Hook refresh button
  if (btnPromptsRefresh) {
    btnPromptsRefresh.addEventListener('click', loadPrompts);
  }

  // Auto-load on page load
  document.addEventListener('DOMContentLoaded', loadPrompts);

  // OPTIONAL: hook your existing Create form
  // If your form has ids #promptKeyword and #promptReply and #btnPromptCreate
  const promptKeyword = document.getElementById('promptKeyword');
  const promptReply = document.getElementById('promptReply');
  const btnPromptCreate = document.getElementById('btnPromptCreate');

  if (btnPromptCreate) {
    btnPromptCreate.addEventListener('click', (e) => {
      e.preventDefault();
      const k = (promptKeyword?.value || '').trim();
      const r = (promptReply?.value || '').trim();
      if (!k || !r) return alert('Keyword and Reply are required.');
      createPrompt(k, r);
      promptKeyword.value = '';
      promptReply.value = '';
    });
  }
