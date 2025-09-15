<?php
require __DIR__ . '/../../inc/db.php';
$pdo = get_db();

$idcron  = isset($_GET['idcron']) ? (int)$_GET['idcron'] : 0;
$equipId = isset($_GET['equip'])  ? (int)$_GET['equip']  : 0;

if ($idcron > 0) {
  $st = $pdo->prepare("DELETE FROM equipamentocronograma WHERE idequipamentocronograma=?");
  $st->execute([$idcron]);
}

$dest = $equipId > 0 ? ('/?p=equipamentocronograma&id=' . $equipId) : '/?p=equipamento';
header('Location: ' . $dest);
exit;
