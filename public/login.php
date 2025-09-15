<?php session_start(); if (!empty($_SESSION['user'])) { header('Location: /'); exit; } ?>
<!doctype html>
<html lang="pt-br">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Entrar • TCC</title>
  <!-- Bootstrap CSS (CDN) -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Bootstrap Icons (opcional) -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center" style="min-height:100vh">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-12 col-sm-10 col-md-6 col-lg-4">
        <div class="card shadow-sm">
          <div class="card-body p-4">
            <h1 class="h4 mb-3 text-center">Acessar o sistema</h1>
            <form id="loginForm" class="needs-validation" novalidate>
              <div class="mb-3">
                <label class="form-label">E-mail</label>
                <input type="email" class="form-control" id="email" required autocomplete="username" placeholder="voce@empresa.com">
                <div class="invalid-feedback">Informe um e-mail válido.</div>
              </div>
              <div class="mb-3">
                <label class="form-label">Senha</label>
                <div class="input-group">
                  <input type="password" class="form-control" id="password" required autocomplete="current-password" placeholder="••••••••">
                  <button class="btn btn-outline-secondary" type="button" id="togglePwd">
                    <i class="bi bi-eye"></i>
                  </button>
                  <div class="invalid-feedback">Informe sua senha.</div>
                </div>
              </div>
              <button class="btn btn-primary w-100" type="submit" id="btnLogin">
                <span class="spinner-border spinner-border-sm me-2 d-none" id="sp"></span>
                Entrar
              </button>
            </form>
            <div class="alert alert-danger mt-3 d-none" id="err"></div>
          </div>
        </div>
        <p class="text-center text-muted small mt-3">© <?php echo date('Y'); ?> TCC</p>
      </div>
    </div>
  </div>

  <!-- Bootstrap JS (CDN) -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // reveal/hide password
    document.getElementById('togglePwd')?.addEventListener('click', () => {
      const i = document.getElementById('password');
      const icon = document.querySelector('#togglePwd i');
      i.type = i.type === 'password' ? 'text' : 'password';
      icon.classList.toggle('bi-eye');
      icon.classList.toggle('bi-eye-slash');
    });

    // form submit
    const form = document.getElementById('loginForm');
    const btn  = document.getElementById('btnLogin');
    const sp   = document.getElementById('sp');
    const err  = document.getElementById('err');

    form.addEventListener('submit', async (e) => {
      e.preventDefault();
      if (!form.checkValidity()) { form.classList.add('was-validated'); return; }
      err.classList.add('d-none'); err.textContent = '';
      btn.disabled = true; sp.classList.remove('d-none');

      try {
        const res = await fetch('/api.php?action=auth.login', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
          body: JSON.stringify({
            email: document.getElementById('email').value.trim(),
            password: document.getElementById('password').value
          })
        });
        const json = await res.json();
        if (!res.ok) throw new Error(json?.error || `HTTP ${res.status}`);
        window.location.href = '/';
      } catch (ex) {
        err.textContent = ex.message || 'Falha ao autenticar.';
        err.classList.remove('d-none');
      } finally {
        btn.disabled = false; sp.classList.add('d-none');
      }
    });
  </script>
</body>
</html>
