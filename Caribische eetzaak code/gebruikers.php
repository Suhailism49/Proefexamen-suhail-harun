<?php
require 'admin_auth.php';
require 'database.php';
require 'src/GebruikersService.php';

$service = new GebruikersService(new PdoGebruikersRepository($pdo));
$data = $service->getGebruikersOverview();
$gebruikers = $data['gebruikers'];
$pogingen = $data['pogingen'];
?>
<!DOCTYPE html>
<html lang="nl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Gebruikers - Caribbean SH</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
<header>
  <div class="logo">
    <span>🌴</span>
    <div>
      <h2>Caribbean SH</h2>
      <p>Registraties en inlogs</p>
    </div>
  </div>
  <nav>
    <a href="index.php">Terug naar website</a>
    <a href="bestellingen.php">Bestellingen</a>
  </nav>
</header>

<main>
  <section class="page active">
    <div class="titel">
      <h1>Gebruikers</h1>
      <p>Hier zie je welke klanten geregistreerd zijn en wanneer ze voor het laatst hebben ingelogd.</p>
    </div>

    <table>
      <tr>
        <th>ID</th>
        <th>Naam</th>
        <th>Email</th>
        <th>Adres</th>
        <th>Rol</th>
        <th>Laatste login</th>
      </tr>
      <?php foreach ($gebruikers as $gebruiker): ?>
        <tr>
          <td><?= htmlspecialchars($gebruiker['registratie_id']) ?></td>
          <td><?= htmlspecialchars($gebruiker['voornaam_achternaam']) ?></td>
          <td><?= htmlspecialchars($gebruiker['email']) ?></td>
          <td><?= htmlspecialchars($gebruiker['straat_huisnummer']) ?>, <?= htmlspecialchars($gebruiker['postcode']) ?> <?= htmlspecialchars($gebruiker['woonplaats']) ?></td>
          <td><?= htmlspecialchars($gebruiker['rol']) ?></td>
          <td><?= htmlspecialchars($gebruiker['laatste_login'] ?? 'Nog niet ingelogd') ?></td>
        </tr>
      <?php endforeach; ?>
    </table>

    <div class="titel" style="margin-top: 35px;">
      <h1>Laatste inlogpogingen</h1>
      <p>Deze tabel laat zien wanneer iemand heeft geprobeerd in te loggen en of dat gelukt is.</p>
    </div>

    <table>
      <tr>
        <th>Email</th>
        <th>Gelukt</th>
        <th>Bericht</th>
        <th>Datum</th>
      </tr>
      <?php foreach ($pogingen as $poging): ?>
        <tr>
          <td><?= htmlspecialchars($poging['email']) ?></td>
          <td><?= $poging['succesvol'] ? 'Ja' : 'Nee' ?></td>
          <td><?= htmlspecialchars($poging['bericht']) ?></td>
          <td><?= htmlspecialchars($poging['geprobeerd_op']) ?></td>
        </tr>
      <?php endforeach; ?>
    </table>
  </section>
</main>
</body>
</html>
