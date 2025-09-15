<?php
require __DIR__ . '/../../inc/db.php';
$pdo = get_db();

$equip_total = (int)$pdo->query("SELECT COUNT(*) FROM equipamento")->fetchColumn();

$cronaberto_total = (int)$pdo->query("
  SELECT COUNT(*)
  FROM equipamentocronograma
  WHERE status = 'Previsto'
")->fetchColumn();

$proximo = $pdo->query("
  SELECT c.dataprevisto, e.nome AS equip_nome, e.tag
  FROM equipamentocronograma c
  JOIN equipamento e ON e.idequipamento = c.equipamento
  WHERE c.status = 'Previsto' AND c.dataprevisto IS NOT NULL
  ORDER BY c.dataprevisto ASC
  LIMIT 1
")->fetch();
?>

<div class="col-12 col-md-6 col-xl-3">
  <div class="card shadow-sm">
    <div class="card-body">
      <div class="text-muted small">Equipamentos</div>
      <div class="fs-2 fw-semibold"><?= $equip_total ?></div>
      <div class="mt-2">
        <a href="#" class="btn btn-sm btn-outline-primary" data-page="equipamento">
          Ver lista
        </a>
      </div>
    </div>
  </div>
</div>

<div class="col-12 col-md-6 col-xl-3">
  <div class="card shadow-sm">
    <div class="card-body">
      <div class="text-muted small">Cronogramas em aberto</div>
      <div class="fs-2 fw-semibold"><?= $cronaberto_total ?></div>
      <div class="mt-2 d-flex gap-2">
        <a href="#" class="btn btn-sm btn-outline-primary" data-page="cronograma">
          Ver cronograma
        </a>
      </div>
    </div>
  </div>
</div>

<?php if ($proximo): ?>
<div class="col-12 col-xl-6">
  <div class="card shadow-sm">
    <div class="card-body">
      <div class="text-muted small mb-1">Próximo agendamento</div>
      <div class="d-flex flex-wrap align-items-center gap-2">
        <span class="badge text-bg-info">
          <?= htmlspecialchars(date('d/m/Y', strtotime($proximo['dataprevisto']))) ?>
        </span>
        <span>
          <?= htmlspecialchars($proximo['equip_nome']) ?>
          <?= $proximo['tag'] ? ' <span class="text-muted">('.htmlspecialchars($proximo['tag']).')</span>' : '' ?>
        </span>
      </div>
    </div>
  </div>
</div>
<?php else: ?>
<div class="col-12 col-xl-6">
  <div class="card shadow-sm">
    <div class="card-body">
      <div class="text-muted small mb-1">Próximo agendamento</div>
      <div class="text-muted">Nenhuma data prevista.</div>
    </div>
  </div>
</div>
<?php endif; ?>
