<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../src/GebruikersService.php';

final class GebruikersServiceTest extends TestCase
{
    public function testReturnsUsersAndRecentLoginAttempts(): void
    {
        $repository = new InMemoryGebruikersRepository(
            [
                [
                    'registratie_id' => 1,
                    'voornaam_achternaam' => 'Ada Lovelace',
                    'email' => 'ada@example.com',
                    'telefoon' => '0612345678',
                    'straat_huisnummer' => 'Main 1',
                    'postcode' => '1000AA',
                    'woonplaats' => 'Amsterdam',
                    'iban' => 'NL00TEST0000000001',
                    'aangemaakt_op' => '2024-01-01 12:00:00',
                    'rol' => 'klant',
                    'actief' => 1,
                    'laatste_login' => '2024-01-02 09:00:00',
                ],
            ],
            [
                [
                    'email' => 'ada@example.com',
                    'succesvol' => 1,
                    'bericht' => 'Inloggen gelukt',
                    'geprobeerd_op' => '2024-01-02 09:00:00',
                ],
            ]
        );
        $service = new GebruikersService($repository);

        $result = $service->getGebruikersOverview();

        $this->assertCount(1, $result['gebruikers']);
        $this->assertSame('Ada Lovelace', $result['gebruikers'][0]['voornaam_achternaam']);
        $this->assertCount(1, $result['pogingen']);
        $this->assertSame('Inloggen gelukt', $result['pogingen'][0]['bericht']);
    }
}
