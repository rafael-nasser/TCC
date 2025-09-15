<?php
require __DIR__ . '/../../inc/db.php';
$pdo = get_db();

// equipamento é obrigatório
$equipId = isset($_GET['equip']) ? (int)$_GET['equip'] : 0;
if ($equipId <= 0) {
  echo '<div class="col-12"><div class="alert alert-warning">Equipamento inválido.</div></div>';
  return;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $nomeprovedor    = trim($_POST['nomeprovedor'] ?? '');
  $acreditado      = strtoupper(trim($_POST['acreditado'] ?? 'N')) === 'S' ? 'S' : 'N'; 
  $relatorionumero = trim($_POST['relatorionumero'] ?? '');
  $datarelatorio   = $_POST['datarelatorio']   ?? null;
  $validaderelatorio = $_POST['validaderelatorio'] ?? null;
  $resumoanalise   = trim($_POST['resumoanalise'] ?? '');
  $analisedogpt    = null; 

  if ($datarelatorio === '')     $datarelatorio = null;
  if ($validaderelatorio === '') $validaderelatorio = null;

  $savedPath = null;
  if (!empty($_FILES['arquivo']['name']) && is_uploaded_file($_FILES['arquivo']['tmp_name'])) {
    $err = $_FILES['arquivo']['error'];
    if ($err === UPLOAD_ERR_OK) {
      $allowedExt = ['pdf','jpg','jpeg','png'];
      $name = $_FILES['arquivo']['name'];
      $tmp  = $_FILES['arquivo']['tmp_name'];
      $ext  = strtolower(pathinfo($name, PATHINFO_EXTENSION));

      if (!in_array($ext, $allowedExt, true)) {
        echo '<div class="col-12"><div class="alert alert-danger">Extensão não permitida. Use PDF/JPG/PNG.</div></div>';
        return;
      }

      if (function_exists('finfo_open')) {
        $f = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($f, $tmp);
        finfo_close($f);
        $allowedMime = ['application/pdf','image/jpeg','image/png'];
        if (!in_array($mime, $allowedMime, true)) {
          echo '<div class="col-12"><div class="alert alert-danger">Tipo de arquivo inválido.</div></div>';
          return;
        }
      }

      // Pasta segura fora do public: /.../storage/calibracoes/equip_<id>
         // Raiz do projeto (sobe 3 níveis a partir de /public/pages)
        $projectRoot = dirname(__DIR__, 2);         // /var/www/html
        $projectRoot = dirname($projectRoot, 1);    // /var/www
        $storageRoot = $projectRoot . '/storage';

        // Garante a existência do storage e da subpasta do equipamento
        $destDir = $storageRoot . '/calibracoes/equip_' . $equipId;
        if (!is_dir($destDir)) {
        if (!@mkdir($destDir, 0775, true) && !is_dir($destDir)) {
            echo '<div class="col-12"><div class="alert alert-danger">Não foi possível criar a pasta de armazenamento: '
                . htmlspecialchars($destDir) . '</div></div>';
            return;
        }
        @chown($storageRoot, 'www-data');
        @chgrp($storageRoot, 'www-data');
        @chown($destDir, 'www-data');
        @chgrp($destDir, 'www-data');
        }

        // Nome e caminho final
        $base = preg_replace('/[^a-zA-Z0-9-_]+/', '_', pathinfo($name, PATHINFO_FILENAME));
        $finalName = sprintf('%s_%s.%s', $base ?: 'arquivo', date('Ymd_His'), $ext);
        $destPath = $destDir . '/' . $finalName;

        if (!move_uploaded_file($tmp, $destPath)) {
        echo '<div class="col-12"><div class="alert alert-danger">Falha ao salvar o arquivo em '
            . htmlspecialchars($destPath) . '</div></div>';
        return;
        }
        $savedPath = 'calibracoes/equip_' . $equipId . '/' . $finalName;

    } else {
      echo '<div class="col-12"><div class="alert alert-danger">Erro de upload (código '.(int)$err.').</div></div>';
      return;
    }
  }

  //INSERT na tabela calibracao
  $sql = "INSERT INTO calibracao
          (equipamento, nomeprovedor, acreditado, relatorionumero, datarelatorio, validaderelatorio, resumoanalise, analisedogpt, arquivo)
          VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
  $st = $pdo->prepare($sql);
  $st->execute([
    $equipId, $nomeprovedor, $acreditado, $relatorionumero,
    $datarelatorio, $validaderelatorio, $resumoanalise, $analisedogpt, $savedPath
  ]);

  header('Location: /?p=calibracao');
  exit;
}

/** Dados do equipamento para título */
$eq = $pdo->prepare("SELECT idequipamento, nome, tag FROM equipamento WHERE idequipamento=?");
$eq->execute([$equipId]);
$equip = $eq->fetch();
?>
<div class="col-12">
  <div class="card shadow-sm">
    <div class="card-body">
      <h2 class="h6 mb-3">
        Nova calibração • Equipamento #<?= htmlspecialchars($equipId) ?>
        <?= $equip ? ' — '.htmlspecialchars($equip['nome']).($equip['tag']?' ('.htmlspecialchars($equip['tag']).')':'') : '' ?>
      </h2>

      <form method="post" action="/pages/calibracao_dados.php?equip=<?= (int)$equipId ?>" enctype="multipart/form-data">
        <div class="row g-3">
          <div class="col-md-8">
            <label class="form-label">Provedor / Laboratório</label>
            <input type="text" name="nomeprovedor" class="form-control" placeholder="Ex.: Laboratório XYZ" required>
          </div>
          <div class="col-md-4">
            <label class="form-label">Acreditado?</label>
            <select name="acreditado" class="form-select">
              <option value="N">Não</option>
              <option value="S">Sim</option>
            </select>
          </div>

          <div class="col-md-4">
            <label class="form-label">Nº do relatório</label>
            <input type="text" name="relatorionumero" class="form-control" placeholder="Ex.: REL-2025-0001">
          </div>
          <div class="col-md-4">
            <label class="form-label">Data do relatório</label>
            <input type="date" name="datarelatorio" class="form-control">
          </div>
          <div class="col-md-4">
            <label class="form-label">Validade do relatório</label>
            <input type="date" name="validaderelatorio" class="form-control">
          </div>

          <div class="col-12">
            <label class="form-label">Resumo da análise</label>
            <textarea name="resumoanalise" class="form-control" rows="3" placeholder="Observações, notas técnicas, limites, etc."></textarea>
          </div>

          <div class="col-12">
            <label class="form-label">Anexar certificado/relatório (PDF/JPG/PNG, até ~8MB)</label>
            <input type="file" name="arquivo" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
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
