<?php
header('Content-Type: application/json');
require 'database.php';

$data = json_decode(file_get_contents('php://input'), true);

if (!$data || empty($data['producten'])) {
    echo json_encode(['success' => false, 'message' => 'Geen producten ontvangen.']);
    exit;
}

$klant = $data['klant'];
$verplicht = ['naam', 'email', 'straat', 'huisnummer', 'postcode', 'woonplaats'];
foreach ($verplicht as $veld) {
    if (empty($klant[$veld])) {
        echo json_encode(['success' => false, 'message' => 'Vul alle verplichte gegevens in.']);
        exit;
    }
}

try {
    $pdo->beginTransaction();

    $stmt = $pdo->prepare("INSERT INTO klanten (naam, email, telefoon, straat, huisnummer, postcode, woonplaats)
                           VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        $klant['naam'],
        $klant['email'],
        $klant['telefoon'] ?? '',
        $klant['straat'],
        $klant['huisnummer'],
        $klant['postcode'],
        $klant['woonplaats']
    ]);
    $klantId = $pdo->lastInsertId();

    $totaal = 0;
    foreach ($data['producten'] as $product) {
        $totaal += (float)$product['prijs'] * (int)$product['aantal'];
    }

    $stmt = $pdo->prepare("INSERT INTO bestellingen (klant_id, betaalmethode, totaalprijs)
                           VALUES (?, ?, ?)");
    $stmt->execute([$klantId, $data['betaalmethode'], $totaal]);
    $bestellingId = $pdo->lastInsertId();

    $stmt = $pdo->prepare("INSERT INTO bestelling_regels
        (bestelling_id, product_id, product_naam, grootte, aantal, prijs_per_stuk, subtotaal)
        VALUES (?, ?, ?, ?, ?, ?, ?)");

    foreach ($data['producten'] as $product) {
        $aantal = (int)$product['aantal'];
        $prijs = (float)$product['prijs'];
        $subtotaal = $aantal * $prijs;

        $stmt->execute([
            $bestellingId,
            $product['id'],
            $product['naam'],
            $product['grootte'],
            $aantal,
            $prijs,
            $subtotaal
        ]);
    }

    $pdo->commit();

    echo json_encode([
        'success' => true,
        'bestelling_id' => $bestellingId
    ]);
} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(['success' => false, 'message' => 'Database fout: ' . $e->getMessage()]);
}
?>
