<?php

function get_db(): PDO {
    static $pdo = null;
    if ($pdo instanceof PDO) {
        return $pdo;
    }

    // Carrega config.php
    $cfg = require __DIR__ . '/config.php';

    if (!isset($cfg['DB'])) {
        throw new RuntimeException("Configuração de banco de dados não encontrada em config.php");
    }

    $db = $cfg['DB'];

    $dsn = sprintf(
        "mysql:host=%s;port=%s;dbname=%s;charset=%s",
        $db['HOST'],
        $db['PORT'],
        $db['NAME'],
        $db['CHARSET'] ?? 'utf8mb4'
    );

    try {
        $pdo = new PDO($dsn, $db['USER'], $db['PASS'], [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]);
    } catch (PDOException $e) {
        throw new RuntimeException("Erro ao conectar ao banco de dados: " . $e->getMessage());
    }

    return $pdo;
}
