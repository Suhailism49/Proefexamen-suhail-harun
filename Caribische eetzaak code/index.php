<!DOCTYPE html>
<html lang="nl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Caribbean SH</title>
  <?php
    session_start();
    require 'database.php';
  ?>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <!-- Header met logo en navigatie -->
  <header>
    <div class="logo" onclick="showPage('home')">
      <span>🌴</span>
      <div>
        <h2>Caribbean SH</h2>
        <p>Caribische eetzaak</p>
      </div>
    </div>

    <nav>
      <a onclick="showPage('home')">Homepage</a>
      <a onclick="showPage('producten')">Producten</a>
      <a onclick="showPage('overons')">Over ons</a>
      <a onclick="showPage('winkelwagen')">Winkelwagen <span id="cartAantal">0</span></a>
      <?php if (isset($_SESSION['admin_id'])): ?>
        <a href="admin.php">Adminpaneel</a>
        <a href="bestellingen.php">Bestellingen</a>
        <a href="gebruikers.php">Gebruikers</a>
        <a href="admin_logout.php">Admin uitloggen</a>
      <?php else: ?>
        <a href="admin_login.php">Admin login</a>
      <?php endif; ?>
    </nav>

    <div class="account-links">
      <?php if (isset($_SESSION['gebruiker_naam'])): ?>
        <span>Welkom, <?= htmlspecialchars($_SESSION['gebruiker_naam']) ?></span>
        <a href="uitloggen.php">Uitloggen</a>
      <?php else: ?>
        <a onclick="showPage('login')">Inloggen</a>
        <a onclick="showPage('registreren')">Registreren</a>
      <?php endif; ?>
    </div>
  </header>

  <!-- Hoofdcontent met alle pagina secties -->
  <main>
    <section id="home" class="page active">
      <div class="hero">
        <div>
          <p class="small-title">Welkom bij onze eetzaak</p>
          <h1>Verse Caribische gerechten bestellen</h1>
          <p>Bij Caribbean SH kunt u verschillende Caribische gerechten, toetjes en drankjes bestellen. Kies iets uit het menu en voeg het toe aan uw winkelwagen.</p>
          <button onclick="showPage('producten')">Bekijk producten</button>
        </div>
        <div class="hero-box">
          <h3>Meest gekozen</h3>
          <p>Spicy gestoomd vispakketje</p>
          <strong>Vanaf €14,95</strong>
        </div>
      </div>

      <div class="info-blokken">
        <div class="blok rood">
          <h2>Meest gekozen gerechten</h2>
          <p>Spicy gestoomd vispakketje</p>
          <p>Caribische pasteitjes</p>
          <p>Jerk chicken met vegetarische kip</p>
        </div>
        <div class="blok groen">
          <h2>Meest gekozen toetjes</h2>
          <p>Ananas kokoscake</p>
          <p>Mango Mouse</p>
          <p>Frozen aardbei daiquiri mocktail</p>
        </div>
      </div>
    </section>

    <section id="producten" class="page">
      <div class="titel">
        <h1>Producten</h1>
        <p>Hier zijn al onze gerechten en toetjes. Gebruik de zoekbalk of filter om sneller te zoeken.</p>
      </div>

      <div class="filter-balk">
        <input type="text" id="zoekInput" placeholder="Zoekbalk..." onkeyup="toonProducten()">
        <select id="filterSelect" onchange="toonProducten()">
          <option value="alles">Meest relevant</option>
          <option value="laag">Prijs laag-hoog</option>
          <option value="hoog">Prijs hoog-laag</option>
          <option value="Gerecht">Gerechten</option>
          <option value="Toetje">Toetjes</option>
        </select>
      </div>

      <div id="productenLijst" class="producten-grid"></div>
    </section>

    <section id="detail" class="page">
      <button class="terug" onclick="showPage('producten')">Terug</button>
      <div id="detailBox" class="detail-box"></div>
    </section>

    <section id="winkelwagen" class="page">
      <div class="titel">
        <h1>Winkelwagen</h1>
        <p>Hier kunt u zien wat er allemaal in uw winkelwagen zit. Klik op afrekenen als u klaar bent.</p>
      </div>
      <div id="winkelwagenInhoud"></div>
      <button class="rechts" onclick="showPage('afrekenen')">Afrekenen</button>
    </section>

    <section id="afrekenen" class="page">
      <button class="terug" onclick="showPage('winkelwagen')">Terug naar winkelwagen</button>
      <h1>Afrekenen</h1>
      <div id="afrekenInhoud"></div>

      <div class="betaal-box">
        <label>Naam:</label>
        <input id="klantNaam" placeholder="Voornaam en achternaam">

        <label>Email:</label>
        <input id="klantEmail" type="email" placeholder="email@voorbeeld.nl">

        <label>Telefoon:</label>
        <input id="klantTelefoon" placeholder="0612345678">

        <label>Straat:</label>
        <input id="klantStraat" placeholder="Straatnaam">

        <label>Huisnummer:</label>
        <input id="klantHuisnummer" placeholder="12A">

        <label>Postcode:</label>
        <input id="klantPostcode" placeholder="1234AB">

        <label>Woonplaats:</label>
        <input id="klantWoonplaats" placeholder="Rotterdam">

        <label>Betaalmethode:</label>
        <select id="betaalmethode">
          <option>ING</option>
          <option>ABN AMRO</option>
          <option>Apple Pay</option>
          <option>Google Pay</option>
        </select>

        <button onclick="bestellingAfronden()">Bestelling opslaan</button>
      </div>
    </section>

    <section id="overons" class="page">
      <div class="titel">
        <h1>Over ons</h1>
        <p>Op deze webpagina kunt u zien wie we zijn, waar we zijn en hoe u contact met ons kunt opnemen.</p>
      </div>

      <div class="contact-grid">
        <div class="contact-card">
          <h3>Harun Can</h3>
          <p>0612345900</p>
          <p>HCmailtjuu@hotmail.com</p>
        </div>
        <div class="contact-card">
          <h3>Suhail Ismaïli</h3>
          <p>0647281901</p>
          <p>vktrntkbt.k@gmail.com</p>
        </div>
        <div class="contact-card">
          <h3>Locatie</h3>
          <p>Eetzaak Plein 1023</p>
          <p>2142QT Rotterdam</p>
          <p>0173731198</p>
        </div>
      </div>
    </section>

    <section id="login" class="page">
      <div class="formulier">
        <h1>Loginscherm</h1>
        <label>Emailadres:</label>
        <input id="loginEmail" type="email" placeholder="email@voorbeeld.nl">
        <label>Wachtwoord:</label>
        <input id="wachtwoord" type="password" placeholder="Wachtwoord">
        <a onclick="wachtwoordTonen()">Wachtwoord weergeven</a>
        <button onclick="inloggen()">Inloggen</button>
        <p id="loginMelding"></p>
        <p>Bent u nieuw? <a onclick="showPage('registreren')">Registreer hier</a></p>
      </div>
    </section>

    <section id="registreren" class="page">
      <div class="formulier groot-formulier">
        <h1>Registratiepagina</h1>
        <input id="regNaam" placeholder="Voornaam en achternaam">
        <input id="regAdres" placeholder="Straat + huisnummer">
        <input id="regWoonplaats" placeholder="Woonplaats">
        <input id="regTelefoon" placeholder="Telefoon">
        <input id="regPostcode" placeholder="Postcode">
        <input id="regIban" placeholder="IBAN">
        <input id="regEmail" type="email" placeholder="Emailadres">
        <input id="regWachtwoord" type="password" placeholder="Wachtwoord">
        <input id="regWachtwoord2" type="password" placeholder="Wachtwoord bevestigen">
        <button onclick="registreren()">Registreren</button>
        <p id="registratieMelding"></p>
        <p>Hebt u al een account? <a onclick="showPage('login')">Log dan hier in</a></p>
      </div>
    </section>
  </main>

  <!-- Footer informatie -->
  <footer>
    <p>© 2026 Caribbean SH - Schoolproject</p>
  </footer>

  <!-- JavaScript logica laden -->
  <script>
    const productenUitDatabase = <?php
      $stmt = $pdo->query("SELECT product_id AS id, naam, prijs, gram, grootte, soort, icoon, ingredienten FROM producten");
      echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    ?>;
  </script>
  <script src="script.js"></script>
</body>
</html>
