<?php
require __DIR__ . '/../../inc/db.php';
$pdo = get_db();

$stmt = $pdo->query("SELECT idlocais AS id, nome FROM locais ORDER BY nome");
$locais = $stmt->fetchAll();
?>

<div class="col-12">
  <div class="card shadow-sm">
    <div class="card-body">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="h6 mb-0">Locais</h2>
        <a href="#" class="btn btn-primary btn-sm" data-page="locais_dados">
          <i class="bi bi-plus-lg me-1"></i>Novo local
        </a>
      </div>

      <div class="table-responsive">
        <table class="table table-sm align-middle">
          <thead>
            <tr>
              <th style="width:100px">ID</th>
              <th>Nome</th>
              <th class="text-end" style="width:120px">Ações</th>
            </tr>
          </thead>
          <tbody>
          <?php if (empty($locais)): ?>
            <tr><td colspan="3" class="text-muted text-center">Nenhum local cadastrado.</td></tr>
          <?php else: foreach ($locais as $l): ?>
            <tr>
              <td><?= htmlspecialchars($l['id']) ?></td>
              <td><?= htmlspecialchars($l['nome']) ?></td>
              <td class="text-end">
                    <a href="#" class="btn btn-sm btn-outline-secondary" 
                        data-page="locais_dados" 
                        data-id="<?= $l['id'] ?>" 
                        title="Editar">
                        <i class="bi bi-pencil"></i>
                    </a>
                    <button class="btn btn-sm btn-outline-danger" disabled title="Em breve">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>

            </tr>
          <?php endforeach; endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
