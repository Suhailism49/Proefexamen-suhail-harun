<?php
function createDatabaseConnection(array $config): PDO
{
    $driver = $config['driver'] ?? 'mysql';

    if ($driver === 'sqlite') {
        $path = $config['path'] ?? ':memory:';
        $pdo = new PDO("sqlite:$path");
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    }

    $host = $config['host'] ?? 'localhost';
    $dbname = $config['dbname'] ?? 'caribbean_sh';
    $username = $config['username'] ?? 'root';
    $password = $config['password'] ?? '';

    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $pdo;
}
