<?php
class Bien {
    private PDO $pdo;

    public function __construct() {
        $this->pdo = Database::getInstance()->getPdo();
    }

    public function getAll(array $filtres = [], int $limite = 20, int $offset = 0): array {
        $sql = "SELECT b.*,
                (SELECT chemin FROM medias m
                 JOIN biens_medias bm ON m.id = bm.media_id
                 WHERE bm.bien_id = b.id AND bm.est_principale = 1
                 LIMIT 1) AS image_principale
                FROM biens b
                WHERE b.publie = 1";
        $params = [];

        if (!empty($filtres['transaction'])) {
            $sql .= " AND b.transaction_type = :transaction";
            $params['transaction'] = $filtres['transaction'];
        }
        if (!empty($filtres['type'])) {
            $sql .= " AND b.type_bien = :type";
            $params['type'] = $filtres['type'];
        }
        if (!empty($filtres['quartier'])) {
            $sql .= " AND b.quartier = :quartier";
            $params['quartier'] = $filtres['quartier'];
        }
        if (!empty($filtres['prix_max'])) {
            $sql .= " AND b.prix <= :prix_max";
            $params['prix_max'] = (float)$filtres['prix_max'];
        }
        if (!empty($filtres['vedette'])) {
            $sql .= " AND b.vedette = 1";
        }

        $sql .= " ORDER BY b.vedette DESC, b.created_at DESC LIMIT :limite OFFSET :offset";

        $stmt = $this->pdo->prepare($sql);
        foreach ($params as $key => $val) {
            $stmt->bindValue(":$key", $val, is_int($val) ? PDO::PARAM_INT : PDO::PARAM_STR);
        }
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function countAll(array $filtres = []): int {
        $sql = "SELECT COUNT(*) FROM biens b WHERE b.publie = 1";
        $params = [];

        if (!empty($filtres['transaction'])) {
            $sql .= " AND b.transaction_type = :transaction";
            $params['transaction'] = $filtres['transaction'];
        }
        if (!empty($filtres['type'])) {
            $sql .= " AND b.type_bien = :type";
            $params['type'] = $filtres['type'];
        }
        if (!empty($filtres['quartier'])) {
            $sql .= " AND b.quartier = :quartier";
            $params['quartier'] = $filtres['quartier'];
        }
        if (!empty($filtres['prix_max'])) {
            $sql .= " AND b.prix <= :prix_max";
            $params['prix_max'] = (float)$filtres['prix_max'];
        }
        if (!empty($filtres['vedette'])) {
            $sql .= " AND b.vedette = 1";
        }

        $stmt = $this->pdo->prepare($sql);
        foreach ($params as $key => $val) {
            $stmt->bindValue(":$key", $val, is_int($val) ? PDO::PARAM_INT : PDO::PARAM_STR);
        }
        $stmt->execute();
        return (int)$stmt->fetchColumn();
    }

    public function getById(int $id): ?array {
        $stmt = $this->pdo->prepare("SELECT * FROM biens WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public function getBySlug(string $slug): ?array {
        $stmt = $this->pdo->prepare("SELECT * FROM biens WHERE slug = :slug AND publie = 1 LIMIT 1");
        $stmt->execute(['slug' => $slug]);
        return $stmt->fetch() ?: null;
    }

    public function getMedias(int $bien_id): array {
        $stmt = $this->pdo->prepare(
            "SELECT m.*, bm.ordre, bm.est_principale
             FROM medias m
             JOIN biens_medias bm ON m.id = bm.media_id
             WHERE bm.bien_id = :id
             ORDER BY bm.est_principale DESC, bm.ordre ASC"
        );
        $stmt->execute(['id' => $bien_id]);
        return $stmt->fetchAll();
    }

    public function genererSlug(string $titre): string {
        $slug = slugify($titre);
        $base = $slug;
        $i = 1;

        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM biens WHERE slug = ?");
        while (true) {
            $stmt->execute([$slug]);
            if ((int)$stmt->fetchColumn() === 0) break;
            $slug = $base . '-' . $i++;
        }
        return $slug;
    }
}
