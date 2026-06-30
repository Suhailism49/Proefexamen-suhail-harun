<?php
require_once __DIR__ . '/src/DatabaseConnection.php';

$config = [
    'driver' => 'mysql',
    'host' => 'localhost',
    'dbname' => 'caribbean_sh',
    'username' => 'root',
    'password' => '',
];

try {
    $pdo = createDatabaseConnection($config);
} catch (PDOException $e) {
    die('Database verbinding mislukt: ' . $e->getMessage());
}
?>
