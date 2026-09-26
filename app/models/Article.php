<?php
class Article {
    private PDO $pdo;
    public function __construct() {
        $this->pdo = Database::getInstance()->getPdo();
    }
    public function getAll(int $limite = 12): array {
        $stmt = $this->pdo->prepare("SELECT * FROM articles WHERE statut = 'publie' ORDER BY created_at DESC LIMIT :limite");
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getBySlug(string $slug): ?array {
        $stmt = $this->pdo->prepare("SELECT * FROM articles WHERE slug = :slug AND statut = 'publie'");
        $stmt->execute(['slug' => $slug]);
        $row = $stmt->fetch();
        return $row === false ? null : $row;
    }
}
