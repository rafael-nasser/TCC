<?php
require __DIR__ . '/../../inc/db.php';
$pdo = get_db();

$sql = "
  SELECT
    e.idequipamento AS id,
    e.nome,
    e.tag,
    e.local AS id_local,
    CASE
      WHEN e.local IS NULL OR e.local = 0 THEN 'Não informado'
      ELSE COALESCE(l.nome, 'Não informado')
    END AS local_nome
  FROM equipamento e
  LEFT JOIN locais l ON l.idlocais = e.local
  ORDER BY e.idequipamento DESC
";
$equipamentos = $pdo->query($sql)->fetchAll();
?>

<div class="col-12">
  <div class="card shadow-sm">
    <div class="card-body">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="h6 mb-0">Equipamentos</h2>
        <a href="#" class="btn btn-primary btn-sm" data-page="equipamento_dados">
          <i class="bi bi-plus-lg me-1"></i>Novo equipamento
        </a>
      </div>

      <div class="table-responsive">
        <table class="table table-sm align-middle">
          <thead>
            <tr>
              <th style="width:100px">ID</th>
              <th>Nome</th>
              <th>TAG</th>
              <th>Local</th>
              <th class="text-end" style="width:120px">Ações</th>
            </tr>
          </thead>
          <tbody>
          <?php if (empty($equipamentos)): ?>
            <tr><td colspan="5" class="text-muted text-center">Nenhum equipamento cadastrado.</td></tr>
          <?php else: foreach ($equipamentos as $eq): ?>
            <tr>
              <td><?= htmlspecialchars($eq['id']) ?></td>
              <td><?= htmlspecialchars($eq['nome']) ?></td>
              <td><?= htmlspecialchars($eq['tag']) ?></td>
              <td><?= htmlspecialchars($eq['local_nome']) ?></td>
              <td class="text-end">
                <a href="#"
                   class="btn btn-sm btn-outline-secondary"
                   data-page="equipamento_dados"
                   data-id="<?= (int)$eq['id'] ?>"
                   title="Editar">
                  <i class="bi bi-pencil"></i>
                </a>
                <a href="#"
                    class="btn btn-sm btn-outline-primary"
                    data-page="equipamentofaixa"
                    data-id="<?= (int)$eq['id'] ?>"
                    title="Faixas do equipamento">
                    <i class="bi bi-sliders"></i>
                </a>
                <a href="#"
                  class="btn btn-sm btn-outline-primary"
                  data-page="equipamentocronograma"
                  data-id="<?= (int)$eq['id'] ?>"
                  title="Cronograma">
                  <i class="bi bi-calendar3"></i>
                </a>
                <a href="#"
                  class="btn btn-sm btn-outline-info"
                  data-page="calibracao_dados"
                  data-q="equip=<?= (int)$eq['id'] ?>"
                  title="Adicionar calibração">
                  <i class="bi bi-file-earmark-plus"></i>
                </a>
                <a href="/pages/equipamento_excluir.php?id=<?= (int)$eq['id'] ?>"
                    class="btn btn-sm btn-outline-danger"
                    title="Excluir"
                    onclick="return confirm('Tem certeza que deseja excluir este equipamento?');">
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
