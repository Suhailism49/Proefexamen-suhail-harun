<?php
class GebruikersService
{
    private $repository;

    public function __construct($repository)
    {
        $this->repository = $repository;
    }

    public function getGebruikersOverview(): array
    {
        return [
            'gebruikers' => $this->repository->getGebruikers(),
            'pogingen' => $this->repository->getInlogPogingen(),
        ];
    }
}

class InMemoryGebruikersRepository
{
    private array $gebruikers;
    private array $pogingen;

    public function __construct(array $gebruikers = [], array $pogingen = [])
    {
        $this->gebruikers = $gebruikers;
        $this->pogingen = $pogingen;
    }

    public function getGebruikers(): array
    {
        return $this->gebruikers;
    }

    public function getInlogPogingen(): array
    {
        return $this->pogingen;
    }
}

class PdoGebruikersRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function getGebruikers(): array
    {
        $sql = "SELECT r.registratie_id, r.voornaam_achternaam, r.email, r.telefoon,
                      r.straat_huisnummer, r.postcode, r.woonplaats, r.iban, r.aangemaakt_op,
                      i.rol, i.actief, i.laatste_login
                FROM registraties r
                JOIN inloggegevens i ON r.registratie_id = i.registratie_id
                ORDER BY r.aangemaakt_op DESC";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getInlogPogingen(): array
    {
        $stmt = $this->pdo->query("SELECT * FROM inlog_pogingen ORDER BY geprobeerd_op DESC LIMIT 20");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
