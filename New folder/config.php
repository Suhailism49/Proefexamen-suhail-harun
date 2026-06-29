<?php
$host = 'localhost';
$dbName = 'caribbean_spice';
$dbUser = 'root';
$dbPass = '';

try {
    $pdo = new PDO("mysql:host=$host;charset=utf8mb4", $dbUser, $dbPass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbName`");
    $pdo->exec("USE `$dbName`");

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS menu_items (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            description TEXT NOT NULL,
            price DECIMAL(8,2) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ");

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS orders (
            id INT AUTO_INCREMENT PRIMARY KEY,
            customer_name VARCHAR(100) NOT NULL,
            phone VARCHAR(40) NOT NULL,
            delivery_type VARCHAR(20) NOT NULL,
            address VARCHAR(255) DEFAULT NULL,
            notes TEXT DEFAULT NULL,
            total DECIMAL(8,2) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ");

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS order_items (
            id INT AUTO_INCREMENT PRIMARY KEY,
            order_id INT NOT NULL,
            menu_item_id INT NOT NULL,
            quantity INT NOT NULL,
            unit_price DECIMAL(8,2) NOT NULL,
            FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
        )
    ");

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            email VARCHAR(150) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ");

    $count = $pdo->query("SELECT COUNT(*) FROM menu_items")->fetchColumn();
    if ((int) $count === 0) {
        $pdo->exec("
            INSERT INTO menu_items (name, description, price) VALUES
            ('Jerk Chicken Bowl', 'Gemarineerde kip, kokosrijst, zwarte bonen en mango salsa.', 14.50),
            ('Roti met groenten', 'Knapperige roti gevuld met gegrilde groenten en pittige saus.', 12.00),
            ('Fish Curry', 'Verse vis in rijke kokos curry met rijst en limoen.', 16.00),
            ('Plantain Snack Box', 'Gefrituurde plantain, salsa, pikante bonen en een frisse salade.', 8.50)
        ");
    }
} catch (PDOException $e) {
    die('Databaseverbinding mislukt: ' . $e->getMessage());
}

function getDb(): PDO
{
    global $pdo;
    return $pdo;
}
?>
