<?php
function slugify(string $text): string {
    // Table de conversion des accents
    $accents = [
        'à'=>'a','â'=>'a','ä'=>'a','á'=>'a','ã'=>'a','å'=>'a',
        'è'=>'e','é'=>'e','ê'=>'e','ë'=>'e',
        'ì'=>'i','í'=>'i','î'=>'i','ï'=>'i',
        'ò'=>'o','ó'=>'o','ô'=>'o','ö'=>'o','õ'=>'o',
        'ù'=>'u','ú'=>'u','û'=>'u','ü'=>'u',
        'ý'=>'y','ÿ'=>'y',
        'ç'=>'c','ñ'=>'n',
        'À'=>'a','Â'=>'a','Ä'=>'a','Á'=>'a','Ã'=>'a','Å'=>'a',
        'È'=>'e','É'=>'e','Ê'=>'e','Ë'=>'e',
        'Ì'=>'i','Í'=>'i','Î'=>'i','Ï'=>'i',
        'Ò'=>'o','Ó'=>'o','Ô'=>'o','Ö'=>'o','Õ'=>'o',
        'Ù'=>'u','Ú'=>'u','Û'=>'u','Ü'=>'u',
        'Ý'=>'y','Ç'=>'c','Ñ'=>'n',
    ];
    $text = strtr($text, $accents);
    $text = strtolower($text);
    $text = preg_replace('/[^a-z0-9]+/', '-', $text);
    return trim($text, '-');
}

function formatPrix(float $prix): string {
    return number_format($prix, 0, ',', ' ') . ' DH';
}

function redirect(string $url): void {
    header("Location: $url");
    exit;
}

function jsonResponse(array $data, int $code = 200): void {
    http_response_code($code);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}