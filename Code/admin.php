<?php
// Beheerpagina voor het menu en de bestellingen in de database.
require_once __DIR__ . '/config.php';

$pdo = getDb();

// Verwerk opslaan of verwijderen van menu-items.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'save') {
        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $price = (float) ($_POST['price'] ?? 0);
        $id = (int) ($_POST['id'] ?? 0);

        if ($id > 0) {
            $stmt = $pdo->prepare('UPDATE menu_items SET name = ?, description = ?, price = ? WHERE id = ?');
            $stmt->execute([$name, $description, $price, $id]);
        } else {
            $stmt = $pdo->prepare('INSERT INTO menu_items (name, description, price) VALUES (?, ?, ?)');
            $stmt->execute([$name, $description, $price]);
        }
    } elseif ($action === 'delete') {
        $id = (int) ($_POST['id'] ?? 0);
        if ($id > 0) {
            $stmt = $pdo->prepare('DELETE FROM menu_items WHERE id = ?');
            $stmt->execute([$id]);
        }
    }

    header('Location: admin.php');
    exit;
}

$editId = isset($_GET['edit']) ? (int) $_GET['edit'] : 0;
$editItem = null;
if ($editId > 0) {
    $stmt = $pdo->prepare('SELECT * FROM menu_items WHERE id = ?');
    $stmt->execute([$editId]);
    $editItem = $stmt->fetch();
}

$menuStmt = $pdo->query('SELECT * FROM menu_items ORDER BY id ASC');
$menuItems = $menuStmt->fetchAll();

$orderStmt = $pdo->query('SELECT * FROM orders ORDER BY created_at DESC LIMIT 20');
$orders = $orderStmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="nl">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Beheer | Caribbean Spice</title>
    <link rel="stylesheet" href="styles.css" />
  </head>
  <body>
    <main class="container admin-page">
      <h1>Menu en bestellingen beheren</h1>
      <p>Wijzig hier het menu of bekijk recente bestellingen. Alle gegevens worden opgeslagen in de XAMPP-database.</p>

      <!-- Formulier om een nieuw gerecht toe te voegen of een bestaand gerecht te bewerken. -->
      <section class="card admin-card">
        <h2><?= $editItem ? 'Menu-item bewerken' : 'Nieuw menu-item toevoegen' ?></h2>
        <form method="post" class="order-form">
          <input type="hidden" name="action" value="save" />
          <?php if ($editItem): ?>
            <input type="hidden" name="id" value="<?= (int) $editItem['id'] ?>" />
          <?php endif; ?>

          <label>
            Naam
            <input type="text" name="name" value="<?= htmlspecialchars($editItem['name'] ?? '') ?>" required />
          </label>

          <label>
            Beschrijving
            <textarea name="description" rows="3" required><?= htmlspecialchars($editItem['description'] ?? '') ?></textarea>
          </label>

          <label>
            Prijs
            <input type="number" step="0.01" name="price" value="<?= htmlspecialchars($editItem['price'] ?? '0.00') ?>" required />
          </label>

          <button type="submit" class="btn btn-primary">Opslaan</button>
        </form>
      </section>

      <!-- Laat alle huidige menu-items zien met opties om ze te wijzigen. -->
      <section class="card admin-card">
        <h2>Menu-items</h2>
        <table class="admin-table">
          <thead>
            <tr>
              <th>Naam</th>
              <th>Prijs</th>
              <th>Acties</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($menuItems as $item): ?>
              <tr>
                <td><?= htmlspecialchars($item['name']) ?></td>
                <td>€<?= number_format((float) $item['price'], 2, ',', '.') ?></td>
                <td>
                  <a class="action-link" href="admin.php?edit=<?= (int) $item['id'] ?>">Bewerken</a>
                  <form method="post" class="inline-form">
                    <input type="hidden" name="action" value="delete" />
                    <input type="hidden" name="id" value="<?= (int) $item['id'] ?>" />
                    <button type="submit" class="link-button">Verwijderen</button>
                  </form>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </section>

      <!-- Toont de nieuwste bestellingen die zijn opgeslagen in de database. -->
      <section class="card admin-card">
        <h2>Recente bestellingen</h2>
        <table class="admin-table">
          <thead>
            <tr>
              <th>Naam</th>
              <th>Type</th>
              <th>Totaal</th>
              <th>Datum</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($orders as $order): ?>
              <tr>
                <td><?= htmlspecialchars($order['customer_name']) ?></td>
                <td><?= htmlspecialchars($order['delivery_type']) ?></td>
                <td>€<?= number_format((float) $order['total'], 2, ',', '.') ?></td>
                <td><?= htmlspecialchars($order['created_at']) ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </section>

      <p><a href="index.php" class="btn btn-secondary">Terug naar de website</a></p>
    </main>
  </body>
</html>
