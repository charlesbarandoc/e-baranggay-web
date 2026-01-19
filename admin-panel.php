<?php
include 'db.php';

if (!isAdmin()) {
    header('Location: admin-login.php');
    exit;
}

$csrf_token = generateCSRFToken();
$adminUser = $_SESSION['admin_username'] ?? 'Admin';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Barangay Portal</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;800&display=swap" rel="stylesheet">
</head>
<body class="bg-slate-100 text-slate-800" style="font-family: Inter, sans-serif;">

<div class="max-w-6xl mx-auto px-4 py-8">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl md:text-3xl font-extrabold">Admin Panel</h1>
            <p class="text-slate-500 text-sm">Logged in as <span class="font-semibold"><?php echo htmlspecialchars($adminUser); ?></span></p>
        </div>
        <div class="flex gap-2">
            <a href="index.php" class="px-4 py-2 bg-white border border-slate-200 rounded-lg shadow-sm hover:bg-slate-50">View Portal</a>
            <a href="admin-login.php?logout=1" class="px-4 py-2 bg-red-600 text-white rounded-lg shadow-sm hover:bg-red-700">Logout</a>
        </div>
    </div>

    <input type="hidden" id="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">

    <!-- Tabs -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="flex flex-wrap gap-2 p-3 border-b border-slate-200 bg-slate-50">
            <button class="tab-btn px-4 py-2 rounded-lg font-semibold text-sm bg-red-600 text-white" data-tab="news">News CRUD</button>
            <button class="tab-btn px-4 py-2 rounded-lg font-semibold text-sm bg-white border border-slate-200 hover:bg-slate-50" data-tab="requests">Document Requests CRUD</button>
            <button class="tab-btn px-4 py-2 rounded-lg font-semibold text-sm bg-white border border-slate-200 hover:bg-slate-50" data-tab="prompts">Chatbot Prompts CRUD</button>
            <button class="tab-btn px-4 py-2 rounded-lg font-semibold text-sm bg-white border border-slate-200 hover:bg-slate-50" data-tab="logs">Chat Logs CRUD</button>
        </div>

        <!-- NEWS TAB -->
        <section id="tab-news" class="tab-panel p-5">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-1">
                    <h2 class="text-xl font-extrabold mb-3">Add News</h2>
                    <form id="news-create-form" class="space-y-3">
                        <div>
                            <label class="text-sm font-semibold">Title</label>
                            <input name="title" required class="w-full px-3 py-2 border border-slate-300 rounded-lg" />
                        </div>
                        <div>
                            <label class="text-sm font-semibold">Excerpt</label>
                            <textarea name="excerpt" required rows="4" class="w-full px-3 py-2 border border-slate-300 rounded-lg"></textarea>
                        </div>
                        <div>
                            <label class="text-sm font-semibold">Category</label>
                            <select name="category" class="w-full px-3 py-2 border border-slate-300 rounded-lg">
                                <option value="General">General</option>
                                <option value="Agriculture">Agriculture</option>
                                <option value="Health">Health</option>
                                <option value="Infrastructure">Infrastructure</option>
                                <option value="Urgent">Urgent</option>
                            </select>
                        </div>
                        <label class="inline-flex items-center gap-2 text-sm">
                            <input type="checkbox" name="urgent" class="w-4 h-4" />
                            Mark as urgent
                        </label>
                        <button class="w-full bg-red-600 hover:bg-red-700 text-white font-extrabold py-2 rounded-lg" type="submit">Create</button>
                        <p id="news-create-msg" class="text-sm"></p>
                    </form>
                </div>

                <div class="lg:col-span-2">
                    <div class="flex items-center justify-between mb-3">
                        <h2 class="text-xl font-extrabold">News List</h2>
                        <button id="news-refresh" class="px-3 py-2 text-sm bg-white border border-slate-200 rounded-lg hover:bg-slate-50">Refresh</button>
                    </div>
                    <div class="overflow-x-auto bg-white border border-slate-200 rounded-xl">
                        <table class="min-w-full text-sm">
                            <thead class="bg-slate-50 text-slate-600">
                                <tr>
                                    <th class="text-left px-4 py-3">Title</th>
                                    <th class="text-left px-4 py-3">Category</th>
                                    <th class="text-left px-4 py-3">Urgent</th>
                                    <th class="text-left px-4 py-3">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="news-tbody" class="divide-y divide-slate-200"></tbody>
                        </table>
                    </div>
                    <p id="news-list-msg" class="text-sm mt-2"></p>
                </div>
            </div>
        </section>

        <!-- REQUESTS TAB -->
        <section id="tab-requests" class="tab-panel p-5 hidden">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-3">
                <h2 class="text-xl font-extrabold">Document Requests</h2>
                <div class="flex gap-2">
                    <select id="req-status-filter" class="px-3 py-2 border border-slate-300 rounded-lg text-sm">
                        <option value="">All statuses</option>
                        <option value="pending">pending</option>
                        <option value="approved">approved</option>
                        <option value="ready">ready</option>
                        <option value="released">released</option>
                        <option value="denied">denied</option>
                        <option value="cancelled">cancelled</option>
                    </select>
                    <button id="req-refresh" class="px-3 py-2 text-sm bg-white border border-slate-200 rounded-lg hover:bg-slate-50">Refresh</button>
                </div>
            </div>

            <div class="overflow-x-auto bg-white border border-slate-200 rounded-xl">
                <table class="min-w-full text-sm">
                    <thead class="bg-slate-50 text-slate-600">
                        <tr>
                            <th class="text-left px-4 py-3">Ref #</th>
                            <th class="text-left px-4 py-3">Name</th>
                            <th class="text-left px-4 py-3">Type</th>
                            <th class="text-left px-4 py-3">Status</th>
                            <th class="text-left px-4 py-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="req-tbody" class="divide-y divide-slate-200"></tbody>
                </table>
            </div>
            <p id="req-msg" class="text-sm mt-2"></p>
        </section>

        <!-- PROMPTS TAB -->
        <section id="tab-prompts" class="tab-panel p-5 hidden">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-1">
                    <h2 class="text-xl font-extrabold mb-3">Add Prompt</h2>
                    <form id="prompt-create-form" class="space-y-3">
                        <div>
                            <label class="text-sm font-semibold">Keyword</label>
                            <input name="keyword" required class="w-full px-3 py-2 border border-slate-300 rounded-lg" placeholder="e.g., clearance" />
                        </div>
                        <div>
                            <label class="text-sm font-semibold">Reply</label>
                            <textarea name="reply" required rows="5" class="w-full px-3 py-2 border border-slate-300 rounded-lg" placeholder="Bot reply..."></textarea>
                        </div>
                        <button class="w-full bg-red-600 hover:bg-red-700 text-white font-extrabold py-2 rounded-lg" type="submit">Create</button>
                        <p id="prompt-create-msg" class="text-sm"></p>
                    </form>

                    <div class="mt-6 bg-slate-50 border border-slate-200 rounded-xl p-4">
                        <h3 class="font-extrabold mb-1">Demo tip</h3>
                        <p class="text-sm text-slate-600">In the chatbot, type <span class="font-mono">/why &lt;question&gt;</span> to explain how the matching works.</p>
                    </div>
                </div>

                <div class="lg:col-span-2">
                    <div class="flex items-center justify-between mb-3">
                        <h2 class="text-xl font-extrabold">Prompts List</h2>
                        <button id="prompt-refresh" class="px-3 py-2 text-sm bg-white border border-slate-200 rounded-lg hover:bg-slate-50">Refresh</button>
                    </div>
                    <div class="overflow-x-auto bg-white border border-slate-200 rounded-xl">
                        <table class="min-w-full text-sm">
                            <thead class="bg-slate-50 text-slate-600">
                                <tr>
                                    <th class="text-left px-4 py-3">Keyword</th>
                                    <th class="text-left px-4 py-3">Reply (preview)</th>
                                    <th class="text-left px-4 py-3">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="prompt-tbody" class="divide-y divide-slate-200"></tbody>
                        </table>
                    </div>
                    <p id="prompt-msg" class="text-sm mt-2"></p>
                </div>
            </div>
        </section>

        <!-- LOGS TAB -->
        <section id="tab-logs" class="tab-panel p-5 hidden">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-3">
                <h2 class="text-xl font-extrabold">Chat Logs</h2>
                <div class="flex gap-2">
                    <select id="logs-filter" class="px-3 py-2 border border-slate-300 rounded-lg text-sm">
                        <option value="">All</option>
                        <option value="flagged">Flagged</option>
                    </select>
                    <button id="logs-refresh" class="px-3 py-2 text-sm bg-white border border-slate-200 rounded-lg hover:bg-slate-50">Refresh</button>
                </div>
            </div>

            <div class="overflow-x-auto bg-white border border-slate-200 rounded-xl">
                <table class="min-w-full text-sm">
                    <thead class="bg-slate-50 text-slate-600">
                        <tr>
                            <th class="text-left px-4 py-3">Time</th>
                            <th class="text-left px-4 py-3">User</th>
                            <th class="text-left px-4 py-3">Message</th>
                            <th class="text-left px-4 py-3">Reply</th>
                            <th class="text-left px-4 py-3">Flag</th>
                            <th class="text-left px-4 py-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="logs-tbody" class="divide-y divide-slate-200"></tbody>
                </table>
            </div>
            <p id="logs-msg" class="text-sm mt-2"></p>
        </section>
    </div>

    <!-- Edit modal -->
    <div id="modal" class="fixed inset-0 hidden items-center justify-center bg-black/50 p-4">
        <div class="bg-white rounded-xl w-full max-w-xl shadow-lg border border-slate-200">
            <div class="flex items-center justify-between p-4 border-b border-slate-200">
                <h3 id="modal-title" class="font-extrabold">Edit</h3>
                <button id="modal-close" class="px-2 py-1 rounded-lg hover:bg-slate-100">✕</button>
            </div>
            <div id="modal-body" class="p-4"></div>
        </div>
    </div>

</div>

<script>
const CSRF = document.getElementById('csrf_token').value;

// ---------- Tabs ----------
document.querySelectorAll('.tab-btn').forEach(btn => {
  btn.addEventListener('click', () => {
    document.querySelectorAll('.tab-btn').forEach(b => b.className = 'tab-btn px-4 py-2 rounded-lg font-semibold text-sm bg-white border border-slate-200 hover:bg-slate-50');
    btn.className = 'tab-btn px-4 py-2 rounded-lg font-semibold text-sm bg-red-600 text-white';

    const tab = btn.dataset.tab;
    document.querySelectorAll('.tab-panel').forEach(p => p.classList.add('hidden'));
    document.getElementById('tab-' + tab).classList.remove('hidden');

    if (tab === 'news') loadNews();
    if (tab === 'requests') loadRequests();
    if (tab === 'prompts') loadPrompts();
    if (tab === 'logs') loadLogs();
  });
});

// ---------- Modal helpers ----------
const modal = document.getElementById('modal');
const modalBody = document.getElementById('modal-body');
const modalTitle = document.getElementById('modal-title');
document.getElementById('modal-close').addEventListener('click', () => {
  modal.classList.add('hidden');
  modal.classList.remove('flex');
  modalBody.innerHTML = '';
});
function openModal(title, html) {
  modalTitle.textContent = title;
  modalBody.innerHTML = html;
  modal.classList.remove('hidden');
  modal.classList.add('flex');
}

// ---------- NEWS CRUD ----------
const newsTbody = document.getElementById('news-tbody');
const newsListMsg = document.getElementById('news-list-msg');
const newsCreateMsg = document.getElementById('news-create-msg');

async function loadNews() {
  newsListMsg.textContent = '';
  newsTbody.innerHTML = '<tr><td class="px-4 py-3" colspan="4">Loading...</td></tr>';
  const res = await fetch('news-api.php');
  const data = await res.json();
  if (!data.success) {
    newsTbody.innerHTML = '';
    newsListMsg.textContent = data.message || 'Failed to load news.';
    newsListMsg.className = 'text-sm mt-2 text-red-600';
    return;
  }

  const rows = data.data || [];
  if (rows.length === 0) {
    newsTbody.innerHTML = '<tr><td class="px-4 py-3" colspan="4">No news yet.</td></tr>';
    return;
  }

  newsTbody.innerHTML = rows.map(n => {
    const urgent = (n.urgent == 1) ? 'Yes' : 'No';
    return `
      <tr>
        <td class="px-4 py-3 font-semibold">${escapeHtml(n.title)}</td>
        <td class="px-4 py-3">${escapeHtml(n.category)}</td>
        <td class="px-4 py-3">${urgent}</td>
        <td class="px-4 py-3 whitespace-nowrap">
          <button class="px-3 py-1 rounded-lg bg-slate-100 hover:bg-slate-200" onclick='editNews(${n.id}, ${jsonStr(n.title)}, ${jsonStr(n.excerpt)}, ${jsonStr(n.category)}, ${n.urgent ? 1 : 0})'>Edit</button>
          <button class="px-3 py-1 rounded-lg bg-red-600 text-white hover:bg-red-700" onclick='deleteNews(${n.id})'>Delete</button>
        </td>
      </tr>
    `;
  }).join('');
}

document.getElementById('news-refresh').addEventListener('click', loadNews);

document.getElementById('news-create-form').addEventListener('submit', async (e) => {
  e.preventDefault();
  newsCreateMsg.textContent = '';
  const fd = new FormData(e.target);
  fd.append('csrf_token', CSRF);

  const res = await fetch('news-api.php', { method: 'POST', body: fd });
  const data = await res.json();

  if (!data.success) {
    newsCreateMsg.textContent = data.message || 'Create failed.';
    newsCreateMsg.className = 'text-sm text-red-600';
    return;
  }
  e.target.reset();
  newsCreateMsg.textContent = 'News created!';
  newsCreateMsg.className = 'text-sm text-green-600';
  loadNews();
});

function editNews(id, title, excerpt, category, urgent) {
  openModal('Edit News', `
    <form id="news-edit-form" class="space-y-3">
      <input type="hidden" name="id" value="${id}">
      <div>
        <label class="text-sm font-semibold">Title</label>
        <input name="title" value="${escapeAttr(title)}" required class="w-full px-3 py-2 border border-slate-300 rounded-lg" />
      </div>
      <div>
        <label class="text-sm font-semibold">Excerpt</label>
        <textarea name="excerpt" rows="4" required class="w-full px-3 py-2 border border-slate-300 rounded-lg">${escapeTextarea(excerpt)}</textarea>
      </div>
      <div>
        <label class="text-sm font-semibold">Category</label>
        <select name="category" class="w-full px-3 py-2 border border-slate-300 rounded-lg">
          ${['General','Agriculture','Health','Infrastructure','Urgent'].map(c => `<option value="${c}" ${c===category?'selected':''}>${c}</option>`).join('')}
        </select>
      </div>
      <label class="inline-flex items-center gap-2 text-sm">
        <input type="checkbox" name="urgent" class="w-4 h-4" ${urgent ? 'checked' : ''} />
        Mark as urgent
      </label>
      <button class="w-full bg-red-600 hover:bg-red-700 text-white font-extrabold py-2 rounded-lg" type="submit">Save</button>
      <p id="news-edit-msg" class="text-sm"></p>
    </form>
  `);

  document.getElementById('news-edit-form').addEventListener('submit', async (e) => {
    e.preventDefault();
    const msg = document.getElementById('news-edit-msg');
    msg.textContent = '';
    const fd = new FormData(e.target);
    fd.append('csrf_token', CSRF);

    const res = await fetch('update-news.php', { method: 'POST', body: fd });
    const data = await res.json();

    if (!data.success) {
      msg.textContent = data.message || 'Update failed.';
      msg.className = 'text-sm text-red-600';
      return;
    }

    msg.textContent = 'Updated!';
    msg.className = 'text-sm text-green-600';
    loadNews();
    setTimeout(() => document.getElementById('modal-close').click(), 300);
  });
}

async function deleteNews(id) {
  if (!confirm('Delete this news item?')) return;
  const res = await fetch('news-api.php', {
    method: 'DELETE',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: `id=${encodeURIComponent(id)}&csrf_token=${encodeURIComponent(CSRF)}`
  });
  const data = await res.json();
  if (!data.success) {
    newsListMsg.textContent = data.message || 'Delete failed.';
    newsListMsg.className = 'text-sm mt-2 text-red-600';
    return;
  }
  loadNews();
}

// ---------- REQUESTS CRUD ----------
const reqTbody = document.getElementById('req-tbody');
const reqMsg = document.getElementById('req-msg');

document.getElementById('req-refresh').addEventListener('click', loadRequests);
document.getElementById('req-status-filter').addEventListener('change', loadRequests);

async function loadRequests() {
  reqMsg.textContent = '';
  reqTbody.innerHTML = '<tr><td class="px-4 py-3" colspan="5">Loading...</td></tr>';
  const status = document.getElementById('req-status-filter').value;
  const url = status ? `document-requests-api.php?status=${encodeURIComponent(status)}` : 'document-requests-api.php';
  const res = await fetch(url);
  const data = await res.json();
  if (!data.success) {
    reqTbody.innerHTML = '';
    reqMsg.textContent = data.message || 'Failed to load requests.';
    reqMsg.className = 'text-sm mt-2 text-red-600';
    return;
  }

  const rows = data.data || [];
  if (rows.length === 0) {
    reqTbody.innerHTML = '<tr><td class="px-4 py-3" colspan="5">No requests found.</td></tr>';
    return;
  }

  reqTbody.innerHTML = rows.map(r => {
    return `
      <tr>
        <td class="px-4 py-3 font-mono">${escapeHtml(r.reference_number || '')}</td>
        <td class="px-4 py-3">${escapeHtml(r.full_name || '')}</td>
        <td class="px-4 py-3">${escapeHtml(r.document_type || '')}</td>
        <td class="px-4 py-3">
          <select class="px-2 py-1 border border-slate-300 rounded" onchange='updateRequestStatus(${r.id}, this.value)'>
            ${['pending','approved','ready','released','denied','cancelled'].map(s => `<option value="${s}" ${s===r.status?'selected':''}>${s}</option>`).join('')}
          </select>
        </td>
        <td class="px-4 py-3 whitespace-nowrap">
          <button class="px-3 py-1 rounded-lg bg-slate-100 hover:bg-slate-200" onclick='viewRequest(${jsonStr(r)})'>View</button>
          <button class="px-3 py-1 rounded-lg bg-red-600 text-white hover:bg-red-700" onclick='deleteRequest(${r.id})'>Delete</button>
        </td>
      </tr>
    `;
  }).join('');
}

async function updateRequestStatus(id, status) {
  const res = await fetch('document-requests-api.php', {
    method: 'PUT',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ id, status, csrf_token: CSRF })
  });
  const data = await res.json();
  if (!data.success) {
    reqMsg.textContent = data.message || 'Update failed.';
    reqMsg.className = 'text-sm mt-2 text-red-600';
  } else {
    reqMsg.textContent = 'Status updated.';
    reqMsg.className = 'text-sm mt-2 text-green-600';
  }
}

function viewRequest(r) {
  openModal('Request Details', `
    <div class="space-y-2 text-sm">
      <div><span class="font-semibold">Reference:</span> <span class="font-mono">${escapeHtml(r.reference_number || '')}</span></div>
      <div><span class="font-semibold">Name:</span> ${escapeHtml(r.full_name || '')}</div>
      <div><span class="font-semibold">Contact:</span> ${escapeHtml(r.contact_number || '')}</div>
      <div><span class="font-semibold">Email:</span> ${escapeHtml(r.email || '')}</div>
      <div><span class="font-semibold">Address:</span> ${escapeHtml(r.address || '')}</div>
      <div><span class="font-semibold">Document:</span> ${escapeHtml(r.document_type || '')}</div>
      <div><span class="font-semibold">Purpose:</span> ${escapeHtml(r.purpose || '')}</div>
      <div><span class="font-semibold">Status:</span> ${escapeHtml(r.status || '')}</div>
      <div><span class="font-semibold">Created:</span> ${escapeHtml(r.created_at || '')}</div>
    </div>
  `);
}

async function deleteRequest(id) {
  if (!confirm('Delete this request permanently?')) return;
  const res = await fetch('document-requests-api.php', {
    method: 'DELETE',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ id, csrf_token: CSRF })
  });
  const data = await res.json();
  if (!data.success) {
    reqMsg.textContent = data.message || 'Delete failed.';
    reqMsg.className = 'text-sm mt-2 text-red-600';
    return;
  }
  reqMsg.textContent = 'Deleted.';
  reqMsg.className = 'text-sm mt-2 text-green-600';
  loadRequests();
}

// ---------- PROMPTS CRUD ----------
const promptTbody = document.getElementById('prompt-tbody');
const promptMsg = document.getElementById('prompt-msg');
const promptCreateMsg = document.getElementById('prompt-create-msg');

document.getElementById('prompt-refresh').addEventListener('click', loadPrompts);

document.getElementById('prompt-create-form').addEventListener('submit', async (e) => {
  e.preventDefault();
  promptCreateMsg.textContent = '';
  const fd = new FormData(e.target);
  const payload = {
    keyword: fd.get('keyword'),
    reply: fd.get('reply'),
    csrf_token: CSRF
  };

  const res = await fetch('chatbot-prompts-api.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(payload)
  });
  const data = await res.json();
  if (!data.success) {
    promptCreateMsg.textContent = data.message || 'Create failed.';
    promptCreateMsg.className = 'text-sm text-red-600';
    return;
  }
  e.target.reset();
  promptCreateMsg.textContent = 'Prompt created!';
  promptCreateMsg.className = 'text-sm text-green-600';
  loadPrompts();
});

async function loadPrompts() {
  promptMsg.textContent = '';
  promptTbody.innerHTML = '<tr><td class="px-4 py-3" colspan="3">Loading...</td></tr>';
  const res = await fetch('chatbot-prompts-api.php');
  const data = await res.json();
  if (!data.success) {
    promptTbody.innerHTML = '';
    promptMsg.textContent = data.message || 'Failed to load prompts.';
    promptMsg.className = 'text-sm mt-2 text-red-600';
    return;
  }

  const rows = data.data || [];
  if (rows.length === 0) {
    promptTbody.innerHTML = '<tr><td class="px-4 py-3" colspan="3">No prompts yet.</td></tr>';
    return;
  }

  promptTbody.innerHTML = rows.map(p => {
    const preview = (p.reply || '').slice(0, 60) + ((p.reply || '').length > 60 ? '…' : '');
    return `
      <tr>
        <td class="px-4 py-3 font-semibold">${escapeHtml(p.keyword || '')}</td>
        <td class="px-4 py-3 text-slate-600">${escapeHtml(preview)}</td>
        <td class="px-4 py-3 whitespace-nowrap">
          <button class="px-3 py-1 rounded-lg bg-slate-100 hover:bg-slate-200" onclick='editPrompt(${p.id}, ${jsonStr(p.keyword)}, ${jsonStr(p.reply)})'>Edit</button>
          <button class="px-3 py-1 rounded-lg bg-red-600 text-white hover:bg-red-700" onclick='deletePrompt(${p.id})'>Delete</button>
        </td>
      </tr>
    `;
  }).join('');
}

function editPrompt(id, keyword, reply) {
  openModal('Edit Prompt', `
    <form id="prompt-edit-form" class="space-y-3">
      <input type="hidden" name="id" value="${id}">
      <div>
        <label class="text-sm font-semibold">Keyword</label>
        <input name="keyword" value="${escapeAttr(keyword)}" required class="w-full px-3 py-2 border border-slate-300 rounded-lg" />
      </div>
      <div>
        <label class="text-sm font-semibold">Reply</label>
        <textarea name="reply" rows="6" required class="w-full px-3 py-2 border border-slate-300 rounded-lg">${escapeTextarea(reply)}</textarea>
      </div>
      <button class="w-full bg-red-600 hover:bg-red-700 text-white font-extrabold py-2 rounded-lg" type="submit">Save</button>
      <p id="prompt-edit-msg" class="text-sm"></p>
    </form>
  `);

  document.getElementById('prompt-edit-form').addEventListener('submit', async (e) => {
    e.preventDefault();
    const msg = document.getElementById('prompt-edit-msg');
    msg.textContent = '';
    const fd = new FormData(e.target);

    const res = await fetch('chatbot-prompts-api.php', {
      method: 'PUT',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        id: parseInt(fd.get('id'), 10),
        keyword: fd.get('keyword'),
        reply: fd.get('reply'),
        csrf_token: CSRF
      })
    });

    const data = await res.json();
    if (!data.success) {
      msg.textContent = data.message || 'Update failed.';
      msg.className = 'text-sm text-red-600';
      return;
    }

    msg.textContent = 'Updated!';
    msg.className = 'text-sm text-green-600';
    loadPrompts();
    setTimeout(() => document.getElementById('modal-close').click(), 300);
  });
}

async function deletePrompt(id) {
  if (!confirm('Delete this prompt?')) return;
  const res = await fetch('chatbot-prompts-api.php', {
    method: 'DELETE',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ id, csrf_token: CSRF })
  });
  const data = await res.json();
  if (!data.success) {
    promptMsg.textContent = data.message || 'Delete failed.';
    promptMsg.className = 'text-sm mt-2 text-red-600';
    return;
  }
  loadPrompts();
}

// ---------- CHAT LOGS CRUD (Read / Update flag / Delete) ----------
const logsTbody = document.getElementById('logs-tbody');
const logsMsg = document.getElementById('logs-msg');

document.getElementById('logs-refresh')?.addEventListener('click', loadLogs);
document.getElementById('logs-filter')?.addEventListener('change', loadLogs);

async function loadLogs() {
  if (!logsTbody) return;
  logsMsg.textContent = '';
  logsTbody.innerHTML = '<tr><td class="px-4 py-3" colspan="6">Loading...</td></tr>';

  const filter = document.getElementById('logs-filter')?.value || '';
  const url = filter === 'flagged' ? 'chatbot-logs-api.php?flagged=1' : 'chatbot-logs-api.php';
  const res = await fetch(url);
  const data = await res.json();
  if (!data.success) {
    logsTbody.innerHTML = '';
    logsMsg.textContent = data.message || 'Failed to load logs.';
    logsMsg.className = 'text-sm mt-2 text-red-600';
    return;
  }

  const rows = data.data || [];
  if (rows.length === 0) {
    logsTbody.innerHTML = '<tr><td class="px-4 py-3" colspan="6">No logs found.</td></tr>';
    return;
  }

  logsTbody.innerHTML = rows.map(l => {
    const shortMsg = (l.message || '').slice(0, 60) + ((l.message || '').length > 60 ? '…' : '');
    const shortReply = (l.response || '').slice(0, 60) + ((l.response || '').length > 60 ? '…' : '');
    return `
      <tr>
        <td class="px-4 py-3 whitespace-nowrap">${escapeHtml(l.created_at || '')}</td>
        <td class="px-4 py-3 font-semibold">${escapeHtml(l.username || '')}</td>
        <td class="px-4 py-3">${escapeHtml(shortMsg)}</td>
        <td class="px-4 py-3">${escapeHtml(shortReply)}</td>
        <td class="px-4 py-3">
          <input type="checkbox" ${l.flagged == 1 ? 'checked' : ''} onchange="toggleLogFlag(${l.id}, this.checked)">
        </td>
        <td class="px-4 py-3 whitespace-nowrap">
          <button class="px-3 py-1 rounded-lg bg-slate-100 hover:bg-slate-200" onclick='viewLog(${jsonStr(l)})'>View</button>
          <button class="px-3 py-1 rounded-lg bg-red-600 text-white hover:bg-red-700" onclick='deleteLog(${l.id})'>Delete</button>
        </td>
      </tr>
    `;
  }).join('');
}

function viewLog(l) {
  openModal('Chat Log Details', `
    <div class="space-y-2 text-sm">
      <div><span class="font-semibold">Time:</span> ${escapeHtml(l.created_at || '')}</div>
      <div><span class="font-semibold">User:</span> ${escapeHtml(l.username || '')}</div>
      <div class="pt-2">
        <div class="font-semibold mb-1">Message</div>
        <pre class="whitespace-pre-wrap bg-slate-50 border border-slate-200 rounded-lg p-3">${escapeHtml(l.message || '')}</pre>
      </div>
      <div class="pt-2">
        <div class="font-semibold mb-1">Reply</div>
        <pre class="whitespace-pre-wrap bg-slate-50 border border-slate-200 rounded-lg p-3">${escapeHtml(l.response || '')}</pre>
      </div>
    </div>
  `);
}

async function toggleLogFlag(id, checked) {
  const res = await fetch('chatbot-logs-api.php', {
    method: 'PUT',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ id, flagged: checked ? 1 : 0, csrf_token: CSRF })
  });
  const data = await res.json();
  if (!data.success) {
    logsMsg.textContent = data.message || 'Update failed.';
    logsMsg.className = 'text-sm mt-2 text-red-600';
  } else {
    logsMsg.textContent = 'Updated.';
    logsMsg.className = 'text-sm mt-2 text-green-600';
  }
}

async function deleteLog(id) {
  if (!confirm('Delete this chat log?')) return;
  const res = await fetch('chatbot-logs-api.php', {
    method: 'DELETE',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ id, csrf_token: CSRF })
  });
  const data = await res.json();
  if (!data.success) {
    logsMsg.textContent = data.message || 'Delete failed.';
    logsMsg.className = 'text-sm mt-2 text-red-600';
    return;
  }
  logsMsg.textContent = 'Deleted.';
  logsMsg.className = 'text-sm mt-2 text-green-600';
  loadLogs();
}

// ---------- Utils ----------
function escapeHtml(str) {
  return String(str).replace(/[&<>"]/g, (c) => ({
    '&': '&amp;',
    '<': '&lt;',
    '>': '&gt;',
    '"': '&quot;'
  }[c] || c));
}
function escapeAttr(str) {
  return escapeHtml(str).replace(/'/g, '&#39;');
}
function escapeTextarea(str) {
  return String(str).replace(/</g, '&lt;').replace(/>/g, '&gt;');
}
function jsonStr(v) {
  return JSON.stringify(v ?? '');
}

// Initial load
loadNews();
</script>

</body>
</html>
