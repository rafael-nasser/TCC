<?php
require __DIR__ . '/../../inc/db.php';
$pdo = get_db();

$equipId = isset($_GET['id']) ? (int)$_GET['id'] : (isset($_GET['equip']) ? (int)$_GET['equip'] : 0);
if ($equipId <= 0) {
  echo '<div class="col-12"><div class="alert alert-warning">Equipamento inválido.</div></div>';
  return;
}

$eqStmt = $pdo->prepare("SELECT idequipamento, nome, tag FROM equipamento WHERE idequipamento=?");
$eqStmt->execute([$equipId]);
$equip = $eqStmt->fetch();
if (!$equip) {
  echo '<div class="col-12"><div class="alert alert-warning">Equipamento não encontrado.</div></div>';
  return;
}

$fxStmt = $pdo->prepare("
  SELECT idequipamentofaixa, equipamento, grandeza, faixade, faixaate, criterio, calibrarem
  FROM equipamentofaixa
  WHERE equipamento = ?
  ORDER BY idequipamentofaixa DESC
");
$fxStmt->execute([$equipId]);
$faixas = $fxStmt->fetchAll();
?>
<div class="col-12">
  <div class="card shadow-sm">
    <div class="card-body">
      <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
        <div>
          <h2 class="h6 mb-1">Faixas do equipamento</h2>
          <div class="text-muted small">
            #<?= htmlspecialchars($equip['idequipamento']) ?> •
            <?= htmlspecialchars($equip['nome']) ?>
            <?= $equip['tag'] ? ' ('.htmlspecialchars($equip['tag']).')' : '' ?>
          </div>
        </div>
        <div class="d-flex gap-2">
            <a href="#" class="btn btn-secondary btn-sm" data-page="equipamento">
                <i class="bi bi-arrow-left me-1"></i>Voltar
            </a>
            <a href="#" class="btn btn-primary btn-sm"
                data-page="equipamentofaixa_dados"
                data-q="equip=<?= (int)$equipId ?>">
                <i class="bi bi-plus-lg me-1"></i>Nova faixa
            </a>
        </div>
      </div>

      <div class="table-responsive">
        <table class="table table-sm align-middle">
          <thead>
            <tr>
              <th style="width:90px">#</th>
              <th>Grandeza</th>
              <th>Faixa de</th>
              <th>Faixa até</th>
              <th>Critério</th>
              <th>Calibrar em</th>
              <th class="text-end" style="width:120px">Ações</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($faixas)): ?>
              <tr><td colspan="7" class="text-muted text-center">Nenhuma faixa cadastrada.</td></tr>
            <?php else: foreach ($faixas as $row): ?>
              <tr>
                <td><?= htmlspecialchars($row['idequipamentofaixa']) ?></td>
                <td><?= htmlspecialchars($row['grandeza'] ?? '') ?></td>
                <td><?= htmlspecialchars($row['faixade'] ?? '') ?></td>
                <td><?= htmlspecialchars($row['faixaate'] ?? '') ?></td>
                <td><?= htmlspecialchars($row['criterio'] ?? '') ?></td>
                <td class="text-break" style="max-width:280px"><?= nl2br(htmlspecialchars($row['calibrarem'] ?? '')) ?></td>
                <td class="text-end">
                    <a href="#"
                      class="btn btn-sm btn-outline-secondary"
                      data-page="equipamentofaixa_dados"
                      data-q="idfaixa=<?= (int)$row['idequipamentofaixa'] ?>&equip=<?= (int)$equipId ?>"
                      title="Editar">
                      <i class="bi bi-pencil"></i>
                    </a>
                    <a href="/pages/equipamentofaixa_excluir.php?idfaixa=<?= (int)$row['idequipamentofaixa'] ?>&equip=<?= (int)$equipId ?>"
                        class="btn btn-sm btn-outline-danger"
                        onclick="return confirm('Excluir esta faixa?');"
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
