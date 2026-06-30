<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../src/BestellingenService.php';

final class BestellingenServiceTest extends TestCase
{
    public function testReturnsEmptyListWhenNoOrdersExist(): void
    {
        $service = new BestellingenService(new InMemoryBestellingenRepository([], []));

        $result = $service->getBestellingenOverview();

        $this->assertSame([], $result);
    }

    public function testOrdersAreSortedByNewestFirstAndIncludeTheirLines(): void
    {
        $repository = new InMemoryBestellingenRepository([
            [
                'bestelling_id' => 2,
                'besteld_op' => '2024-01-02 10:00:00',
                'betaalmethode' => 'iDEAL',
                'totaalprijs' => 18.00,
                'status' => 'Verzonden',
                'naam' => 'B. De Wit',
                'email' => 'b@example.com',
                'telefoon' => '0612345678',
                'straat' => 'Laan',
                'huisnummer' => '12',
                'postcode' => '1234AB',
                'woonplaats' => 'Amsterdam',
            ],
            [
                'bestelling_id' => 1,
                'besteld_op' => '2024-01-03 10:00:00',
                'betaalmethode' => 'Contant',
                'totaalprijs' => 25.50,
                'status' => 'In behandeling',
                'naam' => 'A. Janssen',
                'email' => 'a@example.com',
                'telefoon' => '0698765432',
                'straat' => 'Straat',
                'huisnummer' => '3',
                'postcode' => '4321BA',
                'woonplaats' => 'Rotterdam',
            ],
        ], [
            1 => [[
                'product_naam' => 'Babi Pangang',
                'grootte' => 'Medium',
                'aantal' => 2,
                'prijs_per_stuk' => 12.75,
                'subtotaal' => 25.50,
            ]],
            2 => [[
                'product_naam' => 'Kipsaté',
                'grootte' => 'Large',
                'aantal' => 1,
                'prijs_per_stuk' => 18.00,
                'subtotaal' => 18.00,
            ]],
        ]);
        $service = new BestellingenService($repository);

        $result = $service->getBestellingenOverview();

        $this->assertCount(2, $result);
        $this->assertSame(1, $result[0]['bestelling_id']);
        $this->assertSame('A. Janssen', $result[0]['naam']);
        $this->assertCount(1, $result[0]['regels']);
        $this->assertSame('Babi Pangang', $result[0]['regels'][0]['product_naam']);
    }
}
