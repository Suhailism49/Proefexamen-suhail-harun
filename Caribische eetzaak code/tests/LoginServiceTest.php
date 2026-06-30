<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../src/LoginService.php';

final class LoginServiceTest extends TestCase
{
    public function testReturnsErrorWhenCredentialsAreMissing(): void
    {
        $service = new LoginService(new InMemoryLoginRepository());
        $session = [];

        $result = $service->authenticate(['email' => '', 'wachtwoord' => ''], $session);

        $this->assertFalse($result['success']);
        $this->assertSame('Vul email en wachtwoord in.', $result['message']);
    }

    public function testLogsInSuccessfullyWithValidCredentials(): void
    {
        $repository = new InMemoryLoginRepository([
            'test@example.com' => [
                'inlog_id' => 1,
                'registratie_id' => 42,
                'email' => 'test@example.com',
                'wachtwoord_hash' => password_hash('secret', PASSWORD_DEFAULT),
                'actief' => 1,
                'voornaam_achternaam' => 'Test Persoon',
            ],
        ]);
        $service = new LoginService($repository);
        $session = [];

        $result = $service->authenticate(['email' => 'test@example.com', 'wachtwoord' => 'secret'], $session);

        $this->assertTrue($result['success']);
        $this->assertSame('U bent ingelogd.', $result['message']);
        $this->assertSame(42, $session['gebruiker_id']);
        $this->assertSame('Test Persoon', $session['gebruiker_naam']);
        $this->assertCount(1, $repository->loginAttempts);
        $this->assertTrue($repository->loginAttempts[0]['succesvol']);
    }
}
