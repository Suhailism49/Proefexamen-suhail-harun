<?php
require_once __DIR__ . '/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$pdo = getDb();

$name = trim($_POST['name'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$deliveryType = trim($_POST['deliveryType'] ?? 'bezorgen');
$address = trim($_POST['address'] ?? '');
$notes = trim($_POST['notes'] ?? '');
$cartData = $_POST['cartData'] ?? '[]';

$cart = json_decode($cartData, true);

if (!$name || !$phone || !is_array($cart) || empty($cart)) {
    $message = urlencode('Vul alle gegevens in en voeg producten toe aan het mandje.');
    header('Location: index.php?error=' . $message);
    exit;
}

if ($deliveryType === 'bezorgen' && !$address) {
    $message = urlencode('Voor bezorging hebben we ook een adres nodig.');
    header('Location: index.php?error=' . $message);
    exit;
}

$total = 0.0;
$validatedItems = [];

foreach ($cart as $item) {
    if (!isset($item['id'], $item['quantity'])) {
        continue;
    }

    $itemId = (int) $item['id'];
    $quantity = max(1, (int) $item['quantity']);
    $stmt = $pdo->prepare('SELECT id, name, price FROM menu_items WHERE id = ?');
    $stmt->execute([$itemId]);
    $menuItem = $stmt->fetch();

    if (!$menuItem) {
        continue;
    }

    $price = (float) $menuItem['price'];
    $total += $price * $quantity;
    $validatedItems[] = [
        'menu_item_id' => $itemId,
        'quantity' => $quantity,
        'unit_price' => $price,
    ];
}

if (empty($validatedItems)) {
    $message = urlencode('Er zijn geen geldige producten geselecteerd.');
    header('Location: index.php?error=' . $message);
    exit;
}

$pdo->beginTransaction();

$stmt = $pdo->prepare('INSERT INTO orders (customer_name, phone, delivery_type, address, notes, total) VALUES (?, ?, ?, ?, ?, ?)');
$stmt->execute([$name, $phone, $deliveryType, $address, $notes, number_format($total, 2, '.', '')]);
$orderId = $pdo->lastInsertId();

$itemStmt = $pdo->prepare('INSERT INTO order_items (order_id, menu_item_id, quantity, unit_price) VALUES (?, ?, ?, ?)');
foreach ($validatedItems as $item) {
    $itemStmt->execute([$orderId, $item['menu_item_id'], $item['quantity'], number_format($item['unit_price'], 2, '.', '')]);
}

$pdo->commit();

header('Location: index.php?success=1');
exit;
