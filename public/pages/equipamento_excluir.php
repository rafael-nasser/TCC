<?php
require __DIR__ . '/../../inc/db.php';
$pdo = get_db();

$id = $_GET['id'] ?? null;

if ($id) {
    $stmt = $pdo->prepare("DELETE FROM equipamento WHERE idequipamento=?");
    $stmt->execute([$id]);
}

header('Location: /?p=equipamento');
exit;
