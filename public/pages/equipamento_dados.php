<?php
require __DIR__ . '/../../inc/db.php';
$pdo = get_db();

$id = $_GET['id'] ?? null;
$equip = null;

// salva antes de renderizar
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome  = $_POST['nome'] ?? '';
    $tag   = $_POST['tag'] ?? '';
    $local = $_POST['local'] ?? null;

    if ($id) {
        $stmt = $pdo->prepare("UPDATE equipamento SET nome=?, tag=?, local=? WHERE idequipamento=?");
        $stmt->execute([$nome, $tag, $local, $id]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO equipamento (nome, tag, local) VALUES (?,?,?)");
        $stmt->execute([$nome, $tag, $local]);
    }

    header('Location: /?p=equipamento');
    exit;
}

if ($id) {
    $stmt = $pdo->prepare("SELECT * FROM equipamento WHERE idequipamento=?");
    $stmt->execute([$id]);
    $equip = $stmt->fetch();
}

$stmt = $pdo->query("SELECT idlocais, nome FROM locais ORDER BY nome");
$locais = $stmt->fetchAll();
?>

<div class="col-12">
  <div class="card shadow-sm">
    <div class="card-body">
      <h2 class="h6 mb-3"><?= $id ? "Editar Equipamento #{$id}" : "Novo Equipamento" ?></h2>

      <form method="post" action="/pages/equipamento_dados.php<?= $id ? '?id='.$id : '' ?>">
        <div class="mb-3">
          <label class="form-label">Nome</label>
          <input type="text" name="nome" class="form-control"
                 value="<?= htmlspecialchars($equip['nome'] ?? '') ?>" required>
        </div>

        <div class="mb-3">
          <label class="form-label">TAG</label>
          <input type="text" name="tag" class="form-control"
                 value="<?= htmlspecialchars($equip['tag'] ?? '') ?>">
        </div>

        <div class="mb-3">
          <label class="form-label">Local</label>
          <select name="local" class="form-select" required>
            <option value="">-- selecione --</option>
            <?php foreach ($locais as $l): ?>
              <option value="<?= $l['idlocais'] ?>"
                <?= ($equip && $equip['local'] == $l['idlocais']) ? 'selected' : '' ?>>
                <?= htmlspecialchars($l['nome']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <button type="submit" class="btn btn-success">Salvar</button>
        <a href="#" class="btn btn-secondary" data-page="equipamento">Cancelar</a>
      </form>
    </div>
  </div>
</div>
