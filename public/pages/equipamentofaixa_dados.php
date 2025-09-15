<?php
require __DIR__ . '/../../inc/db.php';
$pdo = get_db();

$equipId  = isset($_GET['equip'])   ? (int)$_GET['equip']   : 0;
$idfaixa  = isset($_GET['idfaixa']) ? (int)$_GET['idfaixa'] : 0;

if ($equipId <= 0) {
  echo '<div class="col-12"><div class="alert alert-warning">Equipamento inválido.</div></div>';
  return;
}

/* salvar */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $grandeza   = trim($_POST['grandeza']   ?? '');
  $faixade    = trim($_POST['faixade']    ?? '');
  $faixaate   = trim($_POST['faixaate']   ?? '');
  $criterio   = trim($_POST['criterio']   ?? '');
  $calibrarem = trim($_POST['calibrarem'] ?? '');

  if ($idfaixa > 0) {
    $st = $pdo->prepare("
      UPDATE equipamentofaixa
         SET grandeza=?, faixade=?, faixaate=?, criterio=?, calibrarem=?
       WHERE idequipamentofaixa=? AND equipamento=?
    ");
    $st->execute([$grandeza, $faixade, $faixaate, $criterio, $calibrarem, $idfaixa, $equipId]);
  } else {
    $st = $pdo->prepare("
      INSERT INTO equipamentofaixa (equipamento, grandeza, faixade, faixaate, criterio, calibrarem)
      VALUES (?, ?, ?, ?, ?, ?)
    ");
    $st->execute([$equipId, $grandeza, $faixade, $faixaate, $criterio, $calibrarem]);
  }

  header('Location: /?p=equipamentofaixa&id=' . $equipId);
  exit;
}

/* carregar dados p/ edição */
$faixa = null;
if ($idfaixa > 0) {
  $st = $pdo->prepare("
    SELECT idequipamentofaixa, equipamento, grandeza, faixade, faixaate, criterio, calibrarem
      FROM equipamentofaixa
     WHERE idequipamentofaixa=? AND equipamento=?
     LIMIT 1
  ");
  $st->execute([$idfaixa, $equipId]);
  $faixa = $st->fetch();
}

/* info do equipamento (título) */
$eqStmt = $pdo->prepare("SELECT idequipamento, nome, tag FROM equipamento WHERE idequipamento=?");
$eqStmt->execute([$equipId]);
$equip = $eqStmt->fetch();
?>
<div class="col-12">
  <div class="card shadow-sm">
    <div class="card-body">
      <h2 class="h6 mb-3">
        <?= $idfaixa ? 'Editar' : 'Nova' ?> faixa • Equipamento #<?= htmlspecialchars($equipId) ?>
        <?= $equip ? ' — '.htmlspecialchars($equip['nome']).($equip['tag']?' ('.htmlspecialchars($equip['tag']).')':'') : '' ?>
      </h2>

      <form method="post" action="/pages/equipamentofaixa_dados.php?equip=<?= (int)$equipId ?><?= $idfaixa ? '&idfaixa='.(int)$idfaixa : '' ?>">
        <div class="row g-3">
          <div class="col-md-4">
            <label class="form-label">Grandeza (unidade)</label>
            <input type="text" name="grandeza" class="form-control" placeholder="Ex.: °C, V, bar"
                   value="<?= htmlspecialchars($faixa['grandeza'] ?? '') ?>">
          </div>

          <div class="col-md-4">
            <label class="form-label">Faixa de</label>
            <input type="text" name="faixade" class="form-control" placeholder="Ex.: 0"
                   value="<?= htmlspecialchars($faixa['faixade'] ?? '') ?>">
          </div>

          <div class="col-md-4">
            <label class="form-label">Faixa até</label>
            <input type="text" name="faixaate" class="form-control" placeholder="Ex.: 100"
                   value="<?= htmlspecialchars($faixa['faixaate'] ?? '') ?>">
          </div>

          <div class="col-md-6">
            <label class="form-label">Critério</label>
            <input type="text" name="criterio" class="form-control" placeholder="Ex.: ±0,5 °C / Classe A / etc."
                   value="<?= htmlspecialchars($faixa['criterio'] ?? '') ?>">
          </div>

          <div class="col-12">
            <label class="form-label">Calibrar em</label>
            <textarea name="calibrarem" class="form-control" rows="3"
                      placeholder="Ex.: 0 °C, 25 °C, 50 °C, 75 °C, 100 °C"><?= htmlspecialchars($faixa['calibrarem'] ?? '') ?></textarea>
          </div>
        </div>

        <div class="mt-3 d-flex gap-2">
          <button type="submit" class="btn btn-success">Salvar</button>
          <a href="#" class="btn btn-secondary" data-page="equipamentofaixa" data-id="<?= (int)$equipId ?>">Cancelar</a>
        </div>
      </form>
    </div>
  </div>
</div>
