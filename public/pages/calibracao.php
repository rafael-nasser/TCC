<?php
require __DIR__ . '/../../inc/db.php';
$pdo = get_db();

$sql = "
  SELECT
    c.idcalibracao,
    c.equipamento,
    c.nomeprovedor,
    c.acreditado,
    c.relatorionumero,
    c.datarelatorio,
    c.validaderelatorio,
    c.arquivo,
    e.nome AS equip_nome,
    e.tag  AS equip_tag
  FROM calibracao c
  JOIN equipamento e ON e.idequipamento = c.equipamento
  ORDER BY c.datarelatorio IS NULL, c.datarelatorio DESC, c.idcalibracao DESC
";
$rows = $pdo->query($sql)->fetchAll();
?>
<div class="col-12">
  <div class="card shadow-sm">
    <div class="card-body">
      <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <h2 class="h6 mb-0">Calibrações — Todos os equipamentos</h2>
        <div class="d-flex gap-2">
          <a href="#" class="btn btn-outline-secondary btn-sm" data-page="equipamento">
            <i class="bi bi-hdd-stack me-1"></i>Equipamentos
          </a>
        </div>
      </div>

      <div class="table-responsive">
        <table class="table table-sm align-middle">
          <thead>
            <tr>
              <th style="width:90px">#</th>
              <th>Equipamento</th>
              <th>Provedor</th>
              <th>Acred.</th>
              <th>Relatório</th>
              <th>Data</th>
              <th>Validade</th>
              <th class="text-end" style="width:160px">Ações</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($rows)): ?>
              <tr><td colspan="8" class="text-muted text-center">Nenhuma calibração cadastrada.</td></tr>
            <?php else: foreach ($rows as $r): ?>
              <tr>
                <td><?= (int)$r['idcalibracao'] ?></td>
                <td>
                  #<?= (int)$r['equipamento'] ?> —
                  <?= htmlspecialchars($r['equip_nome']) ?>
                  <?= $r['equip_tag'] ? ' <span class="text-muted">('.htmlspecialchars($r['equip_tag']).')</span>' : '' ?>
                </td>
                <td><?= htmlspecialchars($r['nomeprovedor'] ?? '') ?></td>
                <td><?= $r['acreditado'] === 'S' ? 'Sim' : 'Não' ?></td>
                <td><?= htmlspecialchars($r['relatorionumero'] ?? '') ?></td>
                <td><?= $r['datarelatorio'] ? htmlspecialchars(date('d/m/Y', strtotime($r['datarelatorio']))) : '—' ?></td>
                <td><?= $r['validaderelatorio'] ? htmlspecialchars(date('d/m/Y', strtotime($r['validaderelatorio']))) : '—' ?></td>
                <td class="text-end">
                  <a href="#" class="btn btn-sm btn-outline-secondary"
                     data-page="equipamentocronograma" data-id="<?= (int)$r['equipamento'] ?>"
                     title="Cronograma">
                    <i class="bi bi-list-task"></i>
                  </a>
                    <a href="#"
                    class="btn btn-sm btn-warning"
                    data-page="calibracao_analise"
                    data-q="id=<?= (int)$r['idcalibracao'] ?>"
                    title="Análise da IA (ISO/IEC 17025)">
                    <i class="bi bi-robot"></i>
                    </a>
                  <?php if (!empty($r['arquivo'])): ?>
                    <a href="/pages/calibracao_download.php?id=<?= (int)$r['idcalibracao'] ?>"
                       class="btn btn-sm btn-primary" title="Baixar arquivo">
                      <i class="bi bi-download"></i>
                    </a>
                  <?php else: ?>
                    <button class="btn btn-sm btn-outline-secondary" disabled title="Sem arquivo">
                      <i class="bi bi-file-earmark"></i>
                    </button>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endforeach; endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
