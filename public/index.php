<?php
session_start(); // se não usar login ainda, pode remover
// if (empty($_SESSION['user'])) { header('Location: /login.php'); exit; }
$user = $_SESSION['user'] ?? ['nome' => 'Usuário'];
?>
<!doctype html>
<html lang="pt-br" data-bs-theme="light">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>TCC • Início</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    /* Sidebar layout em telas grandes */
@media (min-width: 992px){
  .sidebar{
    width: 220px;            /* reduz largura */
    flex-shrink: 0;
    height: 100vh;
    position: sticky;
    top: 0;
    border-right: 1px solid rgba(0,0,0,.075);
    background: #fff;
  }
  .main{
    margin-left: 220px;      /* acompanha sidebar */
    padding-left: 1rem;
    padding-right: 1rem;
  }
}

/* Corrige cor do item ativo */
.nav-link.active,
.list-group-item.active{
  background-color: #0d6efd !important; /* azul padrão */
  color: #fff !important;               /* texto branco */
  font-weight: 600;
}
.nav-link.active i,
.list-group-item.active i{
  color: #fff !important;               /* ícones brancos */
}

  </style>
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg bg-body-tertiary border-bottom sticky-top">
  <div class="container-fluid">
    <button class="btn btn-outline-secondary me-2 d-lg-none" data-bs-toggle="offcanvas" data-bs-target="#offSidebar"><i class="bi bi-list"></i></button>
    <a class="navbar-brand fw-semibold" href="#" data-page="dashboard">TCC</a>
    <div class="ms-auto small text-muted d-none d-sm-inline">Olá, <?php echo htmlspecialchars($user['nome']); ?></div>
  </div>
</nav>

<!-- SIDEBAR MOBILE (offcanvas) -->
<div class="offcanvas offcanvas-start d-lg-none" tabindex="-1" id="offSidebar">
  <div class="offcanvas-header">
    <h5 class="offcanvas-title">Menu</h5>
    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Fechar"></button>
  </div>
  <div class="offcanvas-body p-0">
    <div class="list-group list-group-flush" id="menuMobile">
      <a href="#" class="list-group-item list-group-item-action" data-page="dashboard"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a>
      <a href="#" class="list-group-item list-group-item-action" data-page="equipamento"><i class="bi bi-hdd-stack me-2"></i>Equipamentos</a>
      <a href="#" class="list-group-item list-group-item-action" data-page="cronograma"><i class="bi bi-calendar2-week me-2"></i>Cronograma</a>
      <a href="#" class="list-group-item list-group-item-action" data-page="calibracao"><i class="bi bi-calendar2-week me-2"></i>Calibração</a>
      <a href="#" class="list-group-item list-group-item-action" data-page="locais"><i class="bi bi-geo-alt me-2"></i>Locais</a>

    </div>
  </div>
</div>

<!-- LAYOUT DESKTOP -->
<div class="d-none d-lg-flex">
  <aside class="sidebar p-3">
    <div class="d-flex align-items-center mb-3">
      <i class="bi bi-kanban fs-4 me-2 text-primary"></i><span class="fw-semibold">Menu</span>
    </div>
    <div class="nav nav-pills flex-column gap-1" id="menuDesktop">
      <a href="#" class="nav-link" data-page="dashboard"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a>
      <a href="#" class="nav-link" data-page="equipamento"><i class="bi bi-hdd-stack me-2"></i>Equipamentos</a>
      <a href="#" class="nav-link" data-page="cronograma"><i class="bi bi-calendar2-week me-2"></i>Cronograma</a>
      <a href="#" class="nav-link" data-page="calibracao"><i class="bi bi-rulers me-2"></i>Calibração</a>
      <a href="#" class="nav-link" data-page="locais"><i class="bi bi-geo-alt me-2"></i>Locais</a>
    </div>
    <hr class="my-3"><div class="small text-muted">Versão 0.1 • <?php echo date('Y'); ?></div>
  </aside>

  <main class="main container-fluid py-4">
    <div class="d-flex align-items-center justify-content-between mb-3">
      <div>
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-1"><li class="breadcrumb-item"><a href="#" data-page="dashboard">Início</a></li><li class="breadcrumb-item active" id="crumb">Dashboard</li></ol></nav>
        <h1 class="h4 mb-0" id="pageTitle">Dashboard</h1>
      </div>
      <button class="btn btn-outline-secondary btn-sm d-none d-md-inline-flex" id="btnRefresh"><i class="bi bi-arrow-clockwise"></i></button>
    </div>

    <div id="content" class="row g-3">
      <!-- Conteúdo será injetado aqui -->
    </div>
  </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
const contentEl = document.getElementById('content');
const crumbEl   = document.getElementById('crumb');
const titleEl   = document.getElementById('pageTitle');

const titles = { dashboard:'Dashboard', equipamento:'Equipamentos', cronograma:'Cronograma' };

function setActive(page){
  document.querySelectorAll('#menuDesktop .nav-link, #menuMobile .list-group-item').forEach(el=>el.classList.remove('active'));
  document.querySelectorAll(`[data-page="${page}"]`).forEach(el=>el.classList.add('active'));
  titleEl.textContent = titles[page] ?? 'Início';
  crumbEl.textContent = titles[page] ?? 'Início';
}

async function loadPage(page, pushState=true){
  setActive(page);
  contentEl.innerHTML = `
    <div class="col-12"><div class="card shadow-sm"><div class="card-body">
      <div class="d-flex align-items-center"><div class="spinner-border spinner-border-sm me-2"></div>Carregando ${titles[page] ?? page}...</div>
    </div></div></div>`;

  try {
    const [name, ...restParts] = page.split('&');
    const qs  = restParts.join('&');                   // preserva idfaixa=..&equip=..
    const url = `/pages/${name}.php${qs ? `?${qs}` : ''}`;
    const res = await fetch(url, { headers: { 'X-Requested-With': 'fetch' }});


    if(!res.ok) throw new Error(`HTTP ${res.status}`);
    const html = await res.text();
    contentEl.innerHTML = html;
    if (pushState) history.pushState({ page }, '', `?p=${name}${qs ? `&${qs}` : ''}`);
  } catch (e) {
    contentEl.innerHTML = `
      <div class="col-12"><div class="alert alert-danger">
        Falha ao carregar a página (<code>${page}</code>): ${e.message}
      </div></div>`;
  }
}

document.addEventListener('click', (e) => {
  const link = e.target.closest('[data-page]');
  if (!link) return;
  e.preventDefault();

  let page = link.getAttribute('data-page');
  const q   = link.getAttribute('data-q');
  const id  = link.getAttribute('data-id');

  if (q) {
    page += `&${q}`;
  } else if (id) {
    const param = page.startsWith('equipamentofaixa') ? 'equip' : 'id';
    page += `&${param}=${encodeURIComponent(id)}`;
  }

  loadPage(page, true);
});

window.addEventListener('popstate', (e)=>{
  const page = (e.state && e.state.page) || new URLSearchParams(location.search).get('p') || 'dashboard';
  loadPage(page, false);
});

document.addEventListener('DOMContentLoaded', () => {
  const sp = new URLSearchParams(location.search);
  const p  = (sp.get('p') || 'dashboard').trim();

  // monta "equipamentofaixa&id=5" (ou só "dashboard" se não houver mais params)
  sp.delete('p');
  const rest = sp.toString();
  const page = rest ? `${p}&${rest}` : p;

  if (typeof loadPage === 'function') {
    loadPage(page, false); // não faz pushState na carga inicial
  } else {
    // fallback: se não tiver loadPage, simula clique no item do menu
    document.querySelector(`[data-page="${p}"]`)
      ?.dispatchEvent(new MouseEvent('click', { bubbles: true, cancelable: true }));
  }
});

// pega ?p=... da URL (ex.: /?p=equipamento)
function getPageFromQuery() {
  const p = new URLSearchParams(window.location.search).get('p');
  return p && p.trim() ? p.trim() : 'dashboard';
}

document.addEventListener('DOMContentLoaded', () => {
  const page = getPageFromQuery();

  // Se você já tem a função loadPage(page, pushState)
  if (typeof loadPage === 'function') {
    loadPage(page, false); // false = não empurra novo estado no histórico
    return;
  }

  // Se NÃO tiver loadPage, tenta clicar no item do menu com data-page
  const link = document.querySelector(`[data-page="${page}"]`);
  if (link) {
    link.dispatchEvent(new MouseEvent('click', { bubbles: true, cancelable: true }));
  }
});
</script>
</body>
</html>
