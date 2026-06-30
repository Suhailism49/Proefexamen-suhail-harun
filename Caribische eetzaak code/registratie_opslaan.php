<?php
header('Content-Type: application/json');
require 'database.php';

$data = json_decode(file_get_contents('php://input'), true);

$verplicht = ['naam', 'adres', 'woonplaats', 'postcode', 'email', 'wachtwoord', 'wachtwoord2'];
foreach ($verplicht as $veld) {
    if (empty($data[$veld])) {
        echo json_encode(['success' => false, 'message' => 'Vul alle verplichte velden in.']);
        exit;
    }
}

if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Vul een geldig emailadres in.']);
    exit;
}

if ($data['wachtwoord'] !== $data['wachtwoord2']) {
    echo json_encode(['success' => false, 'message' => 'De wachtwoorden zijn niet hetzelfde.']);
    exit;
}

if (strlen($data['wachtwoord']) < 6) {
    echo json_encode(['success' => false, 'message' => 'Het wachtwoord moet minimaal 6 tekens hebben.']);
    exit;
}

try {
    $pdo->beginTransaction();

    $stmt = $pdo->prepare("INSERT INTO registraties
        (voornaam_achternaam, straat_huisnummer, woonplaats, telefoon, postcode, iban, email)
        VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        $data['naam'],
        $data['adres'],
        $data['woonplaats'],
        $data['telefoon'] ?? '',
        $data['postcode'],
        $data['iban'] ?? '',
        $data['email']
    ]);

    $registratieId = $pdo->lastInsertId();
    $hash = password_hash($data['wachtwoord'], PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("INSERT INTO inloggegevens (registratie_id, email, wachtwoord_hash)
                           VALUES (?, ?, ?)");
    $stmt->execute([$registratieId, $data['email'], $hash]);

    $pdo->commit();

    echo json_encode(['success' => true, 'message' => 'Account is opgeslagen in de database.']);
} catch (PDOException $e) {
    $pdo->rollBack();

    if ($e->getCode() == 23000) {
        echo json_encode(['success' => false, 'message' => 'Dit emailadres bestaat al.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Database fout: ' . $e->getMessage()]);
    }
}
?>
