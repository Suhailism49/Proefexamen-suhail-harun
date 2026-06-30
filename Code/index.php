<?php
// Laad de databaseverbinding en de menu-items uit de database.
require_once __DIR__ . '/config.php';

$pdo = getDb();
$stmt = $pdo->query('SELECT * FROM menu_items ORDER BY id ASC');
$menuItems = $stmt->fetchAll();

// Toon een melding na het plaatsen van een bestelling.
$message = '';
if (isset($_GET['success']) && $_GET['success'] === '1') {
    $message = 'Bedankt! Je bestelling is ontvangen en opgeslagen in de database.';
} elseif (isset($_GET['error'])) {
    $message = urldecode($_GET['error']);
}
?>
<!DOCTYPE html>
<html lang="nl">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Caribbean Spice | Caribische keuken</title>
    <link rel="stylesheet" href="styles.css" />
  </head>
  <body>
    <header class="hero">
      <nav class="nav">
        <a href="#home" class="logo">Caribbean Spice</a>
        <div class="nav-links">
          <a href="#menu">Menu</a>
          <a href="#bestellen">Bestellen</a>
          <a href="register.php">Account</a>
          <a href="admin.php">Beheer</a>
        </div>
      </nav>
      <div class="hero-content" id="home">
        <div>
          <p class="eyebrow">Caribische keuken van dichtbij</p>
          <h1>Bestel eenvoudig voor bezorging of afhalen.</h1>
          <p class="hero-text">Maak je keuze uit het database-menu, voeg producten toe aan je mandje en plaats je bestelling.</p>
          <a href="#menu" class="btn btn-primary">Bekijk het menu</a>
        </div>
      </div>
    </header>

    <main class="container">
      <?php if ($message): ?>
        <div class="success-box"><?= htmlspecialchars($message) ?></div>
      <?php endif; ?>

      <div class="info-note">Tip: maak een account aan om jouw gegevens sneller terug te vinden en toekomstige bestellingen makkelijker te plaatsen.</div>

      <!-- Dit onderdeel laat het menu van de eetzaak zien. -->
      <section id="menu" class="menu-section">
        <div class="section-title">
          <p class="eyebrow">Ons menu</p>
          <h2>Heerlijke gerechten uit de Caraïben</h2>
        </div>
        <div class="menu-grid">
          <?php foreach ($menuItems as $item): ?>
            <article class="card">
              <h3><?= htmlspecialchars($item['name']) ?></h3>
              <p><?= htmlspecialchars($item['description']) ?></p>
              <div class="card-footer">
                <span>€<?= number_format((float) $item['price'], 2, ',', '.') ?></span>
                <button class="add-to-cart" data-id="<?= (int) $item['id'] ?>" data-name="<?= htmlspecialchars($item['name'], ENT_QUOTES) ?>" data-price="<?= (float) $item['price'] ?>">Voeg toe</button>
              </div>
            </article>
          <?php endforeach; ?>
        </div>
      </section>

      <!-- //Dit gedeelte bevat het winkelmandje en het bestelformulier. -->
      <section id="bestellen" class="order-layout">
        <div class="cart-panel">
          <div class="section-title">
            <p class="eyebrow">Winkelmandje</p>
            <h2>Jouw bestelling</h2>
          </div>
          <div id="cartItems" class="cart-items">
            <p class="empty-cart">Je winkelmandje is nog leeg.</p>
          </div>
          <div class="cart-summary">
            <div class="summary-row">
              <span>Totaal</span>
              <strong id="cartTotal">€0,00</strong>
            </div>
            <button id="clearCart" class="btn btn-secondary">Leeg maken</button>
          </div>
        </div>

        <form id="orderForm" class="order-form" action="submit_order.php" method="post">
          <div class="section-title">
            <p class="eyebrow">Bestelgegevens</p>
            <h2>Bezorgen of afhalen?</h2>
          </div>
          <input type="hidden" name="cartData" id="cartData" />

          <label>
            Naam
            <input type="text" name="name" placeholder="Jouw naam" required />
          </label>

          <label>
            Telefoon
            <input type="tel" name="phone" placeholder="06 12345678" required />
          </label>

          <fieldset>
            <legend>Bestelwijze</legend>
            <label class="radio-option">
              <input type="radio" name="deliveryType" value="bezorgen" checked />
              Bezorgen
            </label>
            <label class="radio-option">
              <input type="radio" name="deliveryType" value="afhalen" />
              Afhalen
            </label>
          </fieldset>

          <label>
            Adres
            <input type="text" name="address" placeholder="Straatnaam, huisnummer, postcode" />
          </label>

          <label>
            Opmerkingen
            <textarea name="notes" rows="4" placeholder="Bijvoorbeeld: extra saus of snelle bezorging"></textarea>
          </label>

          <div id="orderMessage" class="order-message" aria-live="polite"></div>
          <button type="submit" class="btn btn-primary full-width">Plaats bestelling</button>
        </form>
      </section>
    </main>

    <script src="script.js"></script>
  </body>
</html>
