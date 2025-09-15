<?php
require __DIR__ . '/../../inc/db.php';
$pdo = get_db();

$idfaixa = isset($_GET['idfaixa']) ? (int)$_GET['idfaixa'] : 0;
$equipId = isset($_GET['equip'])   ? (int)$_GET['equip']   : 0;

if ($idfaixa > 0) {
  $st = $pdo->prepare("DELETE FROM equipamentofaixa WHERE idequipamentofaixa=?");
  $st->execute([$idfaixa]);
}

$dest = $equipId > 0 ? ('/?p=equipamentofaixa&id=' . $equipId) : '/?p=equipamento';
header('Location: ' . $dest);
exit;
