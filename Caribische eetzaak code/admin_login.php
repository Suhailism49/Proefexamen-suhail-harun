<?php
session_start();
require 'database.php';

if (isset($_SESSION['admin_id'])) {
    header('Location: admin.php');
    exit;
}

$melding = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $wachtwoord = $_POST['wachtwoord'] ?? '';

    if ($email === '' || $wachtwoord === '') {
        $melding = 'Vul email en wachtwoord in.';
    } else {
        // Demo admin automatisch goedzetten.
        // Dit voorkomt dat je geen toegang krijgt als je oude database al bestond
        // en de admin nog niet was geïmporteerd.
        if ($email === 'admin@caribbeansh.nl' && $wachtwoord === 'Admin123!') {
            $adminHash = password_hash('Admin123!', PASSWORD_DEFAULT);

            $check = $pdo->prepare("SELECT registratie_id FROM registraties WHERE email = ?");
            $check->execute([$email]);
            $registratie = $check->fetch(PDO::FETCH_ASSOC);

            if (!$registratie) {
                $maakRegistratie = $pdo->prepare("INSERT INTO registraties
                    (voornaam_achternaam, straat_huisnummer, woonplaats, telefoon, postcode, iban, email)
                    VALUES (?, ?, ?, ?, ?, ?, ?)");
                $maakRegistratie->execute([
                    'Admin Caribbean SH',
                    'Eetzaak Plein 1023',
                    'Rotterdam',
                    '0173731198',
                    '2142QT',
                    null,
                    $email
                ]);
                $registratieId = $pdo->lastInsertId();
            } else {
                $registratieId = $registratie['registratie_id'];
            }

            $checkLogin = $pdo->prepare("SELECT inlog_id FROM inloggegevens WHERE email = ?");
            $checkLogin->execute([$email]);
            $login = $checkLogin->fetch(PDO::FETCH_ASSOC);

            if (!$login) {
                $maakLogin = $pdo->prepare("INSERT INTO inloggegevens
                    (registratie_id, email, wachtwoord_hash, rol, actief)
                    VALUES (?, ?, ?, 'admin', 1)");
                $maakLogin->execute([$registratieId, $email, $adminHash]);
            } else {
                $updateLogin = $pdo->prepare("UPDATE inloggegevens
                    SET wachtwoord_hash = ?, rol = 'admin', actief = 1
                    WHERE email = ?");
                $updateLogin->execute([$adminHash, $email]);
            }
        }

        $stmt = $pdo->prepare("SELECT i.inlog_id, i.registratie_id, i.email, i.wachtwoord_hash, i.rol, i.actief, r.voornaam_achternaam
                               FROM inloggegevens i
                               JOIN registraties r ON i.registratie_id = r.registratie_id
                               WHERE i.email = ? AND i.rol = 'admin'");
        $stmt->execute([$email]);
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($admin && (int)$admin['actief'] === 1 && password_verify($wachtwoord, $admin['wachtwoord_hash'])) {
            $_SESSION['admin_id'] = $admin['registratie_id'];
            $_SESSION['admin_naam'] = $admin['voornaam_achternaam'];
            $_SESSION['admin_email'] = $admin['email'];

            $update = $pdo->prepare("UPDATE inloggegevens SET laatste_login = NOW() WHERE inlog_id = ?");
            $update->execute([$admin['inlog_id']]);

            $poging = $pdo->prepare("INSERT INTO inlog_pogingen (email, succesvol, bericht) VALUES (?, 1, ?)");
            $poging->execute([$email, 'Admin login gelukt']);

            header('Location: admin.php');
            exit;
        } else {
            $poging = $pdo->prepare("INSERT INTO inlog_pogingen (email, succesvol, bericht) VALUES (?, 0, ?)");
            $poging->execute([$email, 'Admin login mislukt']);
            $melding = 'Geen toegang. Alleen de admin mag hier inloggen.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin login - Caribbean SH</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
<header>
  <div class="logo">
    <span>🌴</span>
    <div>
      <h2>Caribbean SH</h2>
      <p>Admin login</p>
    </div>
  </div>
  <nav>
    <a href="index.php">Terug naar website</a>
  </nav>
</header>

<main>
  <section class="page active">
    <div class="formulier">
      <h1>Admin inloggen</h1>
      <p>Deze pagina is alleen voor de beheerder.</p>

      <?php if ($melding !== ''): ?>
        <p class="foutmelding"><?= htmlspecialchars($melding) ?></p>
      <?php endif; ?>

      <form method="POST">
        <label>Email:</label>
        <input type="email" name="email" placeholder="admin@caribbeansh.nl" required>

        <label>Wachtwoord:</label>
        <input type="password" name="wachtwoord" required>

        <button type="submit">Inloggen als admin</button>
      </form>

      <p class="admin-tip"><strong>Demo admin:</strong> admin@caribbeansh.nl<br><strong>Wachtwoord:</strong> Admin123!</p>
    </div>
  </section>
</main>
</body>
</html>
