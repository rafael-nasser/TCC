<?php
require __DIR__ . '/../../inc/db.php';
$pdo = get_db();

$id   = $_GET['id'] ?? null;
$local = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $nome = trim($_POST['nome'] ?? '');
  if ($nome !== '') {
    if ($id) {
      $st = $pdo->prepare("UPDATE locais SET nome=? WHERE idlocais=?");
      $st->execute([$nome, $id]);
    } else {
      $st = $pdo->prepare("INSERT INTO locais (nome) VALUES (?)");
      $st->execute([$nome]);
    }
  }
  header('Location: /?p=locais');
  exit;
}

if ($id) {
  $st = $pdo->prepare("SELECT * FROM locais WHERE idlocais=?");
  $st->execute([$id]);
  $local = $st->fetch();
}
?>

<div class="col-12">
  <div class="card shadow-sm">
    <div class="card-body">
      <h2 class="h6 mb-3"><?= $id ? "Editar Local #{$id}" : "Novo Local" ?></h2>

      <form method="post" action="/pages/locais_dados.php<?= $id ? '?id='.$id : '' ?>">
        <div class="mb-3">
          <label class="form-label">Nome do local</label>
          <input type="text" name="nome" class="form-control" required
                 value="<?= htmlspecialchars($local['nome'] ?? '') ?>"
                 placeholder="Ex.: Laboratório A">
        </div>

        <button type="submit" class="btn btn-success">Salvar</button>
        <a href="#" class="btn btn-secondary" data-page="locais">Cancelar</a>
      </form>
    </div>
  </div>
</div>
