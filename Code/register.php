<?php
require_once __DIR__ . '/config.php';
$pdo = getDb();

$message = '';
$messageType = 'success';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    if (!$name || !$email || !$password || !$confirmPassword) {
        $message = 'Vul alle velden in.';
        $messageType = 'error';
    } elseif ($password !== $confirmPassword) {
        $message = 'Wachtwoorden komen niet overeen.';
        $messageType = 'error';
    } else {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare('INSERT INTO users (name, email, password) VALUES (?, ?, ?)');
        try {
            $stmt->execute([$name, $email, $hash]);
            $message = 'Account aangemaakt! Je kunt nu inloggen.';
            $messageType = 'success';
        } catch (PDOException $e) {
            $message = 'Dit e-mailadres is al in gebruik.';
            $messageType = 'error';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="nl">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Account aanmaken | Caribbean Spice</title>
    <link rel="stylesheet" href="styles.css" />
  </head>
  <body>
    <main class="container admin-page">
      <h1>Account aanmaken</h1>
      <p>Maak een account aan om je bestellingen makkelijker te volgen.</p>

      <?php if ($message): ?>
        <div class="<?= $messageType === 'error' ? 'error-box' : 'success-box' ?>"><?= htmlspecialchars($message) ?></div>
      <?php endif; ?>

      <section class="card admin-card">
        <form method="post" class="order-form">
          <label>
            Naam
            <input type="text" name="name" required />
          </label>
          <label>
            E-mail
            <input type="email" name="email" required />
          </label>
          <label>
            Wachtwoord
            <input type="password" name="password" required />
          </label>
          <label>
            Bevestig wachtwoord
            <input type="password" name="confirm_password" required />
          </label>
          <button type="submit" class="btn btn-primary">Account aanmaken</button>
        </form>
      </section>

      <p><a href="index.php" class="btn btn-secondary">Terug naar de website</a></p>
    </main>
  </body>
</html>
