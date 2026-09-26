<?php
class ChatbotController {
    private PDO $pdo;

    public function __construct() {
        $this->pdo = Database::getInstance()->getPdo();
    }

    public function repondre(): void {
        header('Content-Type: application/json; charset=utf-8');

        $input = json_decode(file_get_contents('php://input'), true) ?? [];
        $question = trim($input['question'] ?? '');
        $sessionId = substr((string)($input['session_id'] ?? ''), 0, 64);

        if ($question === '') {
            echo json_encode(['reponse' => 'Posez-moi une question !']);
            return;
        }

        // Limiter la taille de la question (anti-abus)
        if (mb_strlen($question) > 500) {
            $question = mb_substr($question, 0, 500);
        }

        $reponse = $this->chercherRegle($question);
        $source = 'regle';

        if (!$reponse) {
            $reponse = $this->chercherDansContenu($question);
            $source = 'contenu';
        }

        if (!$reponse && !empty($_ENV['OPENAI_API_KEY'])) {
            $reponse = $this->appelIA($question);
            $source = 'ia';
        }

        if (!$reponse) {
            $reponse = "Je n'ai pas trouvé de réponse précise. Contactez-nous au +212 5 28 XX XX XX ou à contact@immocolucci.ma.";
            $source = 'fallback';
        }

        // ⚠️ On renvoie du TEXTE BRUT (le JS échappe déjà).
        // On convertit les <br> en \n et on supprime les balises.
        $reponse = $this->nettoyerReponse($reponse);

        $this->log($sessionId, $question, $reponse, $source);

        echo json_encode(['reponse' => $reponse], JSON_UNESCAPED_UNICODE);
    }

    /** Convertit le HTML minimal en texte brut lisible */
    private function nettoyerReponse(string $html): string {
        $txt = preg_replace('#<br\s*/?>#i', "\n", $html);
        $txt = preg_replace('#</p>#i', "\n", $txt);
        $txt = strip_tags($txt);
        return trim($txt);
    }

    private function chercherRegle(string $question): ?string {
        $q = mb_strtolower($question);
        $stmt = $this->pdo->query(
            "SELECT question, reponse, mots_cles FROM chatbot_connaissances WHERE actif = 1 ORDER BY priorite DESC"
        );
        $regles = $stmt->fetchAll();

        $meilleurScore = 0;
        $meilleureReponse = null;

        foreach ($regles as $regle) {
            $score = 0;
            $motsCles = array_filter(array_map('trim', explode(',', mb_strtolower($regle['mots_cles']))));

            foreach ($motsCles as $mot) {
                if ($mot !== '' && mb_strpos($q, $mot) !== false) {
                    $score += 2;
                }
            }
            similar_text($q, mb_strtolower($regle['question']), $pct);
            $score += $pct / 50;

            if ($score > $meilleurScore && $score >= 2) {
                $meilleurScore = $score;
                $meilleureReponse = $regle['reponse'];
            }
        }
        return $meilleureReponse;
    }

    private function chercherDansContenu(string $question): ?string {
        $q = mb_strtolower($question);

        if (preg_match('/\b(bien|villa|riad|appartement|maison|acheter|louer|vente|location)\b/u', $q)) {
            $stmt = $this->pdo->query(
                "SELECT titre, slug, prix, transaction_type, type_bien, quartier
                 FROM biens WHERE publie = 1 ORDER BY vedette DESC, created_at DESC LIMIT 3"
            );
            $biens = $stmt->fetchAll();
            if ($biens) {
                $liste = "Voici quelques biens disponibles :\n";
                foreach ($biens as $b) {
                    $prix = number_format((float)$b['prix'], 0, ',', ' ');
                    $liste .= "• {$b['titre']} ({$b['type_bien']}, {$b['transaction_type']}) - {$prix} DH\n";
                }
                return $liste . "\nConsultez tous nos biens sur /biens";
            }
        }

        if (preg_match('/\b(service|conciergerie|gestion|aménagement)\b/u', $q)) {
            $stmt = $this->pdo->query(
                "SELECT titre, slug, description_courte FROM services WHERE publie = 1 LIMIT 4"
            );
            $services = $stmt->fetchAll();
            if ($services) {
                $liste = "Nos services :\n";
                foreach ($services as $s) {
                    $liste .= "• {$s['titre']} : {$s['description_courte']}\n";
                }
                return $liste;
            }
        }

        if (preg_match('/\b(prix|tarif|combien|coût)\b/u', $q)) {
            return "Nos tarifs varient selon le bien et le service. Consultez nos biens ou contactez-nous pour un devis personnalisé.";
        }

        if (preg_match('/\b(contact|téléphone|email|adresse|joindre)\b/u', $q)) {
            return "📞 +212 5 28 XX XX XX\n✉️ contact@immocolucci.ma\n📍 123, Boulevard Mohammed V, Guéliz, Agadir";
        }

        if (preg_match('/\b(horaire|ouvert|fermé|heure)\b/u', $q)) {
            return "🕐 Lundi - Vendredi : 9h00 – 19h00\n🕐 Samedi : 9h00 – 13h00";
        }

        return null;
    }

    private function appelIA(string $question): ?string {
        $apiKey = $_ENV['OPENAI_API_KEY'];
        $contexte = "Tu es l'assistant d'Immo Colucci, agence immobilière et conciergerie de luxe à Agadir, Maroc. Réponds en texte simple (pas de HTML), de manière concise et professionnelle.";

        $ch = curl_init('https://api.openai.com/v1/chat/completions');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_TIMEOUT        => 15,
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $apiKey,
            ],
            CURLOPT_POSTFIELDS => json_encode([
                'model' => 'gpt-3.5-turbo',
                'messages' => [
                    ['role' => 'system', 'content' => $contexte],
                    ['role' => 'user',   'content' => $question],
                ],
                'max_tokens'  => 200,
                'temperature' => 0.7,
            ]),
        ]);
        $res = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($res === false || $httpCode !== 200) return null;

        $data = json_decode($res, true);
        return $data['choices'][0]['message']['content'] ?? null;
    }

    private function log(string $sessionId, string $question, string $reponse, string $source): void {
        try {
            $stmt = $this->pdo->prepare(
                "INSERT INTO chatbot_conversations (session_id, question, reponse, source) VALUES (?, ?, ?, ?)"
            );
            $stmt->execute([$sessionId, $question, $reponse, $source]);
        } catch (Throwable $e) {
            error_log('Chatbot log : ' . $e->getMessage());
        }
    }
}