<?php
require_once __DIR__ . '/../config/config.php';

function uploadMultiple(array $files, string $dossier = 'general'): array {
    $resultats = [];
    $uploadDir = UPLOAD_DIR . $dossier . '/';

    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    // Protection : empêcher l'exécution de scripts
    $htaccess = $uploadDir . '.htaccess';
    if (!file_exists($htaccess)) {
        file_put_contents($htaccess, "php_flag engine off\nOptions -Indexes\n");
    }

    $allowed = ALLOWED_MIME;
    $maxSize = MAX_UPLOAD_SIZE;

    // Extensions autorisées (whitelist)
    $allowedExt = ['jpg', 'jpeg', 'png', 'webp', 'gif'];

    $count = count($files['name'] ?? []);
    for ($i = 0; $i < $count; $i++) {
        if (($files['error'][$i] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            $resultats[] = ['success' => false, 'nom' => $files['name'][$i], 'erreur' => 'Erreur upload'];
            continue;
        }

        $nom  = $files['name'][$i];
        $tmp  = $files['tmp_name'][$i];
        $taille = (int)$files['size'][$i];

        if (!is_uploaded_file($tmp)) {
            $resultats[] = ['success' => false, 'nom' => $nom, 'erreur' => 'Fichier invalide'];
            continue;
        }

        $type = mime_content_type($tmp);
        if (!in_array($type, $allowed, true) || $taille > $maxSize) {
            $resultats[] = ['success' => false, 'nom' => $nom, 'erreur' => 'Type ou taille invalide'];
            continue;
        }

        // Vérifier que c'est une vraie image
        $info = @getimagesize($tmp);
        if ($info === false) {
            $resultats[] = ['success' => false, 'nom' => $nom, 'erreur' => 'Image corrompue'];
            continue;
        }

        // Extension forcée selon le type MIME (empêche l'upload de .php)
        $extMap = [
            'image/jpeg' => 'jpg',
            'image/png'  => 'png',
            'image/webp' => 'webp',
            'image/gif'  => 'gif',
        ];
        $extension = $extMap[$type] ?? 'jpg';

        $nomSafe     = bin2hex(random_bytes(8)) . '_' . time() . '.' . $extension;
        $destination = $uploadDir . $nomSafe;

        if (!move_uploaded_file($tmp, $destination)) {
            $resultats[] = ['success' => false, 'nom' => $nom, 'erreur' => 'Échec enregistrement'];
            continue;
        }

        @chmod($destination, 0644);

        [$largeur, $hauteur] = $info;

        $resultats[] = [
            'success' => true,
            'nom'     => $nom,
            'chemin'  => UPLOAD_URL . $dossier . '/' . $nomSafe,
            'type'    => $type,
            'taille'  => $taille,
            'largeur' => $largeur,
            'hauteur' => $hauteur,
        ];
    }

    return $resultats;
}