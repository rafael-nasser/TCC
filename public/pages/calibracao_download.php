<?php
require __DIR__ . '/../../inc/db.php';
$pdo = get_db();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) { http_response_code(404); exit('Arquivo não encontrado.'); }

$st = $pdo->prepare("SELECT arquivo FROM calibracao WHERE idcalibracao=?");
$st->execute([$id]);
$row = $st->fetch();

if (!$row || empty($row['arquivo'])) {
  http_response_code(404);
  exit('Arquivo não encontrado.');
}

$relPath = $row['arquivo']; 

// Caminho absoluto até a pasta storage (fora do public)
$projectRoot = dirname(__DIR__, 2);     // /var/www/html
$projectRoot = dirname($projectRoot, 1);// /var/www
$absPath = $projectRoot . '/storage/' . $relPath;

if (!is_file($absPath)) {
  http_response_code(404);
  exit('Arquivo não encontrado no disco.');
}

// Tipo MIME básico
$mime = 'application/octet-stream';
if (function_exists('finfo_open')) {
  $f = finfo_open(FILEINFO_MIME_TYPE);
  $det = finfo_file($f, $absPath);
  finfo_close($f);
  if ($det) $mime = $det;
}

$fname = basename($absPath);
header('Content-Type: ' . $mime);
header('Content-Length: ' . filesize($absPath));
header('Content-Disposition: attachment; filename="' . $fname . '"');
header('X-Content-Type-Options: nosniff');

readfile($absPath);
