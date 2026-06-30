<?php
class LoginService
{
    private $repository;

    public function __construct($repository)
    {
        $this->repository = $repository;
    }

    public function authenticate(array $input, array &$session): array
    {
        $email = $input['email'] ?? '';
        $wachtwoord = $input['wachtwoord'] ?? '';

        if (empty($email) || empty($wachtwoord)) {
            return ['success' => false, 'message' => 'Vul email en wachtwoord in.'];
        }

        $gebruiker = $this->repository->findByEmail($email);

        if (!$gebruiker || !password_verify($wachtwoord, $gebruiker['wachtwoord_hash'])) {
            $this->repository->recordAttempt($email, false, 'Verkeerde gegevens');
            return ['success' => false, 'message' => 'Email of wachtwoord klopt niet.'];
        }

        if ((int)$gebruiker['actief'] !== 1) {
            return ['success' => false, 'message' => 'Dit account is niet actief.'];
        }

        $session['gebruiker_id'] = $gebruiker['registratie_id'];
        $session['gebruiker_naam'] = $gebruiker['voornaam_achternaam'];
        $session['gebruiker_email'] = $gebruiker['email'];

        $this->repository->markSuccessfulLogin((int)$gebruiker['inlog_id']);
        $this->repository->recordAttempt($email, true, 'Inloggen gelukt');

        return ['success' => true, 'message' => 'U bent ingelogd.'];
    }
}

class InMemoryLoginRepository
{
    public array $loginAttempts = [];
    private array $users;

    public function __construct(array $users = [])
    {
        $this->users = $users;
    }

    public function findByEmail(string $email): ?array
    {
        return $this->users[$email] ?? null;
    }

    public function recordAttempt(string $email, bool $succesvol, string $bericht): void
    {
        $this->loginAttempts[] = [
            'email' => $email,
            'succesvol' => $succesvol,
            'bericht' => $bericht,
        ];
    }

    public function markSuccessfulLogin(int $inlogId): void
    {
    }
}

class PdoLoginRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function findByEmail(string $email): ?array
    {
        $stmt = $this->pdo->prepare("SELECT i.inlog_id, i.registratie_id, i.email, i.wachtwoord_hash, i.actief,
                                      r.voornaam_achternaam
                               FROM inloggegevens i
                               JOIN registraties r ON i.registratie_id = r.registratie_id
                               WHERE i.email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function recordAttempt(string $email, bool $succesvol, string $bericht): void
    {
        $stmt = $this->pdo->prepare("INSERT INTO inlog_pogingen (email, succesvol, bericht) VALUES (?, ?, ?)");
        $stmt->execute([$email, $succesvol ? 1 : 0, $bericht]);
    }

    public function markSuccessfulLogin(int $inlogId): void
    {
        $stmt = $this->pdo->prepare("UPDATE inloggegevens SET laatste_login = NOW() WHERE inlog_id = ?");
        $stmt->execute([$inlogId]);
    }
}
