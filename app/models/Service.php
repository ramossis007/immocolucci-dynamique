<?php
class Service {
    private PDO $pdo;
    public function __construct() {
        $this->pdo = Database::getInstance()->getPdo();
    }
    public function getAll(): array {
        return $this->pdo->query(
            "SELECT * FROM services WHERE publie = 1 ORDER BY ordre ASC, id ASC"
        )->fetchAll();
    }

    public function getBySlug(string $slug): ?array {
        $stmt = $this->pdo->prepare("SELECT * FROM services WHERE slug = :slug AND publie = 1");
        $stmt->execute(['slug' => $slug]);
        $row = $stmt->fetch();
        return $row === false ? null : $row;
    }
}
