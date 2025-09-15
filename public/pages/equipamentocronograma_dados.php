<?php
require __DIR__ . '/../../inc/db.php';
$pdo = get_db();

/* equip é obrigatório */
$equipId = isset($_GET['equip']) ? (int)$_GET['equip'] : 0;
if ($equipId <= 0) { echo '<div class="col-12"><div class="alert alert-warning">Equipamento inválido.</div></div>'; return; }

/* salvar */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $titulo        = trim($_POST['titulo'] ?? '');
  $status        = trim($_POST['status'] ?? 'Previsto'); // Previsto|Realizado|Cancelado
  $dataprevisto  = $_POST['dataprevisto']  ?? null;      // 'YYYY-MM-DD' ou ''
  $daterealizado = $_POST['daterealizado'] ?? null;
  $obs           = trim($_POST['obs'] ?? '');

  $dataprevisto  = $dataprevisto  !== '' ? $dataprevisto  : null;
  $daterealizado = $daterealizado !== '' ? $daterealizado : null;

  $st = $pdo->prepare("
    INSERT INTO equipamentocronograma (equipamento, status, dataprevisto, daterealizado, obs, titulo)
    VALUES (?, ?, ?, ?, ?, ?)
  ");
  $st->execute([$equipId, $status, $dataprevisto, $daterealizado, $obs, $titulo]);

  header('Location: /?p=equipamentocronograma&id=' . $equipId);
  exit;
}

/* dados do equipamento para o título */
$eq = $pdo->prepare("SELECT idequipamento, nome, tag FROM equipamento WHERE idequipamento=?");
$eq->execute([$equipId]);
$equip = $eq->fetch();
?>
<div class="col-12">
  <div class="card shadow-sm">
    <div class="card-body">
      <h2 class="h6 mb-3">
        Novo agendamento • Equipamento #<?= htmlspecialchars($equipId) ?>
        <?= $equip ? ' — '.htmlspecialchars($equip['nome']).($equip['tag']?' ('.htmlspecialchars($equip['tag']).')':'') : '' ?>
      </h2>

      <form method="post" action="/pages/equipamentocronograma_dados.php?equip=<?= (int)$equipId ?>">
        <div class="row g-3">
          <div class="col-md-8">
            <label class="form-label">Título</label>
            <input type="text" name="titulo" class="form-control" required placeholder="Ex.: Calibração anual">
          </div>
          <div class="col-md-4">
            <label class="form-label">Status</label>
            <select name="status" class="form-select">
              <option value="Previsto">Previsto</option>
              <option value="Realizado">Realizado</option>
              <option value="Cancelado">Cancelado</option>
            </select>
          </div>
          <div class="col-md-4">
            <label class="form-label">Data prevista</label>
            <input type="date" name="dataprevisto" class="form-control">
          </div>
          <div class="col-md-4">
            <label class="form-label">Data realizada</label>
            <input type="date" name="daterealizado" class="form-control">
          </div>
          <div class="col-12">
            <label class="form-label">Observações</label>
            <textarea name="obs" class="form-control" rows="3" placeholder="Opcional"></textarea>
          </div>
        </div>

        <div class="mt-3 d-flex gap-2">
          <button type="submit" class="btn btn-success">Salvar</button>
          <a href="#" class="btn btn-secondary" data-page="equipamentocronograma" data-id="<?= (int)$equipId ?>">Cancelar</a>
        </div>
      </form>
    </div>
  </div>
</div>
