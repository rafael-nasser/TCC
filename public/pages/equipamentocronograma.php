<?php
require __DIR__ . '/../../inc/db.php';
$pdo = get_db();

$equipId = isset($_GET['id']) ? (int)$_GET['id'] : (isset($_GET['equip']) ? (int)$_GET['equip'] : 0);
if ($equipId <= 0) { echo '<div class="col-12"><div class="alert alert-warning">Equipamento inválido.</div></div>'; return; }

$eq = $pdo->prepare("SELECT idequipamento, nome, tag FROM equipamento WHERE idequipamento=?");
$eq->execute([$equipId]);
$equip = $eq->fetch();
if (!$equip) { echo '<div class="col-12"><div class="alert alert-warning">Equipamento não encontrado.</div></div>'; return; }

$sql = "
  SELECT idequipamentocronograma, equipamento, status, dataprevisto, daterealizado, titulo, obs
  FROM equipamentocronograma
  WHERE equipamento=?
  ORDER BY COALESCE(dataprevisto, '9999-12-31') ASC, idequipamentocronograma DESC
";
$rows = $pdo->prepare($sql);
$rows->execute([$equipId]);
$itens = $rows->fetchAll();
?>

<div class="col-12">
  <div class="card shadow-sm">
    <div class="card-body">
      <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
        <div>
          <h2 class="h6 mb-1">Cronograma do equipamento</h2>
          <div class="text-muted small">
            #<?= htmlspecialchars($equip['idequipamento']) ?> •
            <?= htmlspecialchars($equip['nome']) ?><?= $equip['tag'] ? ' ('.htmlspecialchars($equip['tag']).')' : '' ?>
          </div>
        </div>
        <div class="d-flex gap-2">
          <a href="#" class="btn btn-secondary btn-sm" data-page="equipamento">
            <i class="bi bi-arrow-left me-1"></i>Voltar
          </a>
          <a href="#" class="btn btn-primary btn-sm"
             data-page="equipamentocronograma_dados"
             data-q="equip=<?= (int)$equipId ?>">
            <i class="bi bi-plus-lg me-1"></i>Novo agendamento
          </a>
        </div>
      </div>

      <div class="table-responsive">
        <table class="table table-sm align-middle">
          <thead>
            <tr>
              <th style="width:90px">#</th>
              <th>Título</th>
              <th>Status</th>
              <th>Previsto</th>
              <th>Realizado</th>
              <th class="text-end" style="width:140px">Ações</th>
            </tr>
          </thead>
          <tbody>
          <?php if (empty($itens)): ?>
            <tr><td colspan="6" class="text-muted text-center">Nenhuma atividade no cronograma.</td></tr>
          <?php else: foreach ($itens as $r): ?>
            <tr>
              <td><?= (int)$r['idequipamentocronograma'] ?></td>
              <td><?= htmlspecialchars($r['titulo'] ?? '') ?></td>
              <td><?= htmlspecialchars($r['status'] ?? '') ?></td>
              <td><?= htmlspecialchars($r['dataprevisto'] ?? '') ?></td>
              <td><?= htmlspecialchars($r['daterealizado'] ?? '') ?></td>
              <td class="text-end">
                <a href="/pages/equipamentocronograma_excluir.php?idcron=<?= (int)$r['idequipamentocronograma'] ?>&equip=<?= (int)$equipId ?>"
                   class="btn btn-sm btn-outline-danger"
                   onclick="return confirm('Excluir este agendamento?');"
                   title="Excluir">
                  <i class="bi bi-trash"></i>
                </a>
              </td>
            </tr>
          <?php endforeach; endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
