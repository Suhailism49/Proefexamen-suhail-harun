<?php
class BestellingenService
{
    private $repository;

    public function __construct($repository)
    {
        $this->repository = $repository;
    }

    public function getBestellingenOverview(): array
    {
        $bestellingen = $this->repository->getBestellingen();
        usort($bestellingen, function ($a, $b) {
            return strtotime($b['besteld_op']) <=> strtotime($a['besteld_op']);
        });

        foreach ($bestellingen as &$bestelling) {
            $bestelling['regels'] = $this->repository->getBestellingRegels((int)$bestelling['bestelling_id']);
        }
        unset($bestelling);

        return $bestellingen;
    }
}

class InMemoryBestellingenRepository
{
    private array $bestellingen;
    private array $regels;

    public function __construct(array $bestellingen = [], array $regels = [])
    {
        $this->bestellingen = $bestellingen;
        $this->regels = $regels;
    }

    public function getBestellingen(): array
    {
        return $this->bestellingen;
    }

    public function getBestellingRegels(int $bestellingId): array
    {
        return $this->regels[$bestellingId] ?? [];
    }
}

class PdoBestellingenRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function getBestellingen(): array
    {
        $sql = "SELECT b.bestelling_id, b.besteld_op, b.betaalmethode, b.totaalprijs, b.status,
                      k.naam, k.email, k.telefoon, k.straat, k.huisnummer, k.postcode, k.woonplaats
                FROM bestellingen b
                JOIN klanten k ON b.klant_id = k.klant_id
                ORDER BY b.besteld_op DESC";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getBestellingRegels(int $bestellingId): array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM bestelling_regels WHERE bestelling_id = ?");
        $stmt->execute([$bestellingId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
