<?php
require 'admin_auth.php';
require 'database.php';
require 'src/BestellingenService.php';

$service = new BestellingenService(new PdoBestellingenRepository($pdo));
$bestellingen = $service->getBestellingenOverview();
?>
<!DOCTYPE html>
<html lang="nl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Bestellingen - Caribbean SH</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
<header>
  <div class="logo">
    <span>🌴</span>
    <div>
      <h2>Caribbean SH</h2>
      <p>Bestellingen overzicht</p>
    </div>
  </div>
  <nav>
    <a href="index.php">Terug naar website</a>
  </nav>
</header>

<main>
  <section class="page active">
    <div class="titel">
      <h1>Bestellingen</h1>
      <p>Hier zie je wat klanten hebben besteld, waar ze hebben besteld en hoeveel het heeft gekost.</p>
    </div>

    <?php if (count($bestellingen) === 0): ?>
      <div class="blok"><p>Er zijn nog geen bestellingen geplaatst.</p></div>
    <?php endif; ?>

    <?php foreach ($bestellingen as $bestelling): ?>
      <?php
        $stmt = $pdo->prepare("SELECT * FROM bestelling_regels WHERE bestelling_id = ?");
        $stmt->execute([$bestelling['bestelling_id']]);
        $regels = $stmt->fetchAll(PDO::FETCH_ASSOC);
      ?>
      <div class="blok" style="margin-bottom: 25px;">
        <h2>Bestelling #<?= htmlspecialchars($bestelling['bestelling_id']) ?></h2>
        <p><strong>Datum:</strong> <?= htmlspecialchars($bestelling['besteld_op']) ?></p>
        <p><strong>Klant:</strong> <?= htmlspecialchars($bestelling['naam']) ?> - <?= htmlspecialchars($bestelling['email']) ?></p>
        <p><strong>Telefoon:</strong> <?= htmlspecialchars($bestelling['telefoon']) ?></p>
        <p><strong>Adres:</strong> <?= htmlspecialchars($bestelling['straat']) ?> <?= htmlspecialchars($bestelling['huisnummer']) ?>, <?= htmlspecialchars($bestelling['postcode']) ?> <?= htmlspecialchars($bestelling['woonplaats']) ?></p>
        <p><strong>Betaalmethode:</strong> <?= htmlspecialchars($bestelling['betaalmethode']) ?></p>
        <p><strong>Status:</strong> <?= htmlspecialchars($bestelling['status']) ?></p>

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

        <p class="totaal">Totaal: €<?= number_format($bestelling['totaalprijs'], 2, ',', '.') ?></p>
      </div>
    <?php endforeach; ?>
  </section>
</main>
</body>
</html>
