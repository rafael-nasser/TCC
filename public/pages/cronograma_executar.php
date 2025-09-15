<?php
require __DIR__ . '/../../inc/db.php';
$pdo = get_db();

$idcron = isset($_GET['idcron']) ? (int)$_GET['idcron'] : 0;

if ($idcron > 0) {
  // Marca como Realizado e seta a data de hoje
  $st = $pdo->prepare("
    UPDATE equipamentocronograma
       SET status = 'Realizado',
           daterealizado = CURDATE()
     WHERE idequipamentocronograma = ?
  ");
  $st->execute([$idcron]);
}

// Volta para o cronograma principal
header('Location: /?p=cronograma');
exit;
