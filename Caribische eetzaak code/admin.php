<?php
require 'admin_auth.php';
require 'database.php';

// Simpele adminpagina voor schoolproject.
// Hier zie je wat klanten hebben besteld, waar het bezorgd moet worden en wat het totaal kost.

$sql = "SELECT b.bestelling_id, b.besteld_op, b.betaalmethode, b.totaalprijs, b.status,
               k.naam, k.email, k.telefoon, k.straat, k.huisnummer, k.postcode, k.woonplaats
        FROM bestellingen b
        JOIN klanten k ON b.klant_id = k.klant_id
        ORDER BY b.besteld_op DESC";
$bestellingen = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);

$totaalOmzet = 0;
foreach ($bestellingen as $bestelling) {
    $totaalOmzet += $bestelling['totaalprijs'];
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Adminpaneel - Caribbean SH</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
<header>
  <div class="logo">
    <span>🌴</span>
    <div>
      <h2>Caribbean SH</h2>
      <p>Adminpaneel</p>
    </div>
  </div>
  <nav>
    <a href="index.php">Website</a>
    <a href="gebruikers.php">Gebruikers</a>
    <a href="bestellingen.php">Oude bestellingenpagina</a>
    <a href="admin_logout.php">Uitloggen</a>
  </nav>
</header>

<main>
  <section class="page active">
    <div class="titel">
      <h1>Adminpagina bestellingen</h1>
      <p>Ingelogd als admin: <?= htmlspecialchars($_SESSION['admin_naam']) ?>. Hier kan de eigenaar zien wat klanten hebben besteld, waar het bezorgd moet worden en hoeveel het heeft gekost.</p>
    </div>

    <div class="admin-overzicht">
      <div class="stat-card">
        <h3>Aantal bestellingen</h3>
        <p><?= count($bestellingen) ?></p>
      </div>
      <div class="stat-card">
        <h3>Totale omzet</h3>
        <p>€<?= number_format($totaalOmzet, 2, ',', '.') ?></p>
      </div>
    </div>

    <?php if (count($bestellingen) === 0): ?>
      <div class="blok">
        <p>Er zijn nog geen bestellingen geplaatst.</p>
      </div>
    <?php endif; ?>

    <?php foreach ($bestellingen as $bestelling): ?>
      <?php
        $stmt = $pdo->prepare("SELECT product_naam, grootte, aantal, prijs_per_stuk, subtotaal
                               FROM bestelling_regels
                               WHERE bestelling_id = ?");
        $stmt->execute([$bestelling['bestelling_id']]);
        $regels = $stmt->fetchAll(PDO::FETCH_ASSOC);
      ?>

      <div class="admin-bestelling">
        <div class="admin-kop">
          <div>
            <h2>Bestelling #<?= htmlspecialchars($bestelling['bestelling_id']) ?></h2>
            <p><?= htmlspecialchars($bestelling['besteld_op']) ?></p>
          </div>
          <div class="status-label"><?= htmlspecialchars($bestelling['status']) ?></div>
        </div>

        <div class="admin-info-grid">
          <div>
            <h3>Klantgegevens</h3>
            <p><strong>Naam:</strong> <?= htmlspecialchars($bestelling['naam']) ?></p>
            <p><strong>Email:</strong> <?= htmlspecialchars($bestelling['email']) ?></p>
            <p><strong>Telefoon:</strong> <?= htmlspecialchars($bestelling['telefoon']) ?></p>
          </div>
          <div>
            <h3>Waar bezorgd?</h3>
            <p><?= htmlspecialchars($bestelling['straat']) ?> <?= htmlspecialchars($bestelling['huisnummer']) ?></p>
            <p><?= htmlspecialchars($bestelling['postcode']) ?> <?= htmlspecialchars($bestelling['woonplaats']) ?></p>
          </div>
          <div>
            <h3>Betaling</h3>
            <p><strong>Methode:</strong> <?= htmlspecialchars($bestelling['betaalmethode']) ?></p>
            <p><strong>Totaal:</strong> €<?= number_format($bestelling['totaalprijs'], 2, ',', '.') ?></p>
          </div>
        </div>

        <h3>Wat is er besteld?</h3>
        <table>
          <tr>
            <th>Product</th>
            <th>Grootte</th>
            <th>Aantal</th>
            <th>Prijs per stuk</th>
            <th>Subtotaal</th>
          </tr>
          <?php foreach ($regels as $regel): ?>
            <tr>
              <td><?= htmlspecialchars($regel['product_naam']) ?></td>
              <td><?= htmlspecialchars($regel['grootte']) ?></td>
              <td><?= htmlspecialchars($regel['aantal']) ?></td>
              <td>€<?= number_format($regel['prijs_per_stuk'], 2, ',', '.') ?></td>
              <td>€<?= number_format($regel['subtotaal'], 2, ',', '.') ?></td>
            </tr>
          <?php endforeach; ?>
        </table>
      </div>
    <?php endforeach; ?>
  </section>
</main>

<footer>
  <p>© 2026 Caribbean SH - Adminpagina</p>
</footer>
</body>
</html>
