<?php
require __DIR__ . '/../../inc/db.php';
$pdo = get_db();

/* Lista somente status Previsto */
$sql = "
  SELECT
    c.idequipamentocronograma AS id,
    c.equipamento,
    c.status,
    c.dataprevisto,
    c.daterealizado,
    c.titulo,
    c.obs,
    e.nome AS equip_nome,
    e.tag  AS equip_tag
  FROM equipamentocronograma c
  JOIN equipamento e ON e.idequipamento = c.equipamento
  WHERE c.status = 'Previsto'
  ORDER BY c.dataprevisto IS NULL, c.dataprevisto ASC, c.idequipamentocronograma DESC
";
$st = $pdo->query($sql);
$rows = $st->fetchAll();
?>

<div class="col-12">
  <div class="card shadow-sm">
    <div class="card-body">
      <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <h2 class="h6 mb-0">Cronograma — Atividades previstas</h2>
        <div class="d-flex gap-2">
          <a href="#" class="btn btn-outline-secondary btn-sm" data-page="equipamento">
            <i class="bi bi-hdd-stack me-1"></i> Equipamentos
          </a>
        </div>
      </div>

      <div class="table-responsive">
        <table class="table table-sm align-middle">
          <thead>
            <tr>
              <th style="width:80px">#</th>
              <th>Equipamento</th>
              <th>Título</th>
              <th>Previsto</th>
              <th class="text-end" style="width:220px">Ações</th>
            </tr>
          </thead>
          <tbody>
          <?php if (empty($rows)): ?>
            <tr><td colspan="5" class="text-muted text-center">Nenhuma atividade prevista.</td></tr>
          <?php else: foreach ($rows as $r): ?>
            <tr>
              <td><?= (int)$r['id'] ?></td>
              <td>
                #<?= (int)$r['equipamento'] ?> — 
                <?= htmlspecialchars($r['equip_nome']) ?>
                <?= $r['equip_tag'] ? ' <span class="text-muted">('.htmlspecialchars($r['equip_tag']).')</span>' : '' ?>
              </td>
              <td><?= htmlspecialchars($r['titulo'] ?? '') ?></td>
              <td><?= htmlspecialchars($r['dataprevisto'] ?? '') ?></td>
              <td class="text-end">
                <a href="#" class="btn btn-sm btn-outline-secondary"
                   data-page="equipamentocronograma" data-id="<?= (int)$r['equipamento'] ?>">
                  <i class="bi bi-list-task"></i>
                </a>
                <a href="/pages/cronograma_executar.php?idcron=<?= (int)$r['id'] ?>"
                   class="btn btn-sm btn-success"
                   onclick="return confirm('Marcar como Realizado hoje?');">
                  <i class="bi bi-check2-circle me-1"></i>Executar
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
