<?php

declare(strict_types=1);

require_once __DIR__ . '/lib/config.php';
require_once __DIR__ . '/lib/dna.php';
require_once __DIR__ . '/lib/openai.php';

header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$raw = file_get_contents('php://input') ?: '';
if (strlen($raw) > 65536) {
    http_response_code(413);
    echo json_encode(['error' => 'Request is too large.']);
    exit;
}

$data = json_decode($raw, true);
if (!is_array($data)) {
    http_response_code(400);
    echo json_encode(['error' => 'Expected JSON body']);
    exit;
}

$dna = is_array($data['dna'] ?? null) ? $data['dna'] : $data;
if (!isset($dna['concept']) && isset($data['title'])) {
    $dna = normalizeParams($data, (string) $data['title'], (string) ($data['genre'] ?? ''), (string) ($data['pitch'] ?? ''));
}

$config = framefluxConfig();
$apiKey = framefluxApiKey($config);
$model = framefluxImageModel($config);

if ($apiKey === '') {
    echo json_encode([
        'source' => 'fallback',
        'image' => null,
        'note' => 'No API key — cinematic plate will be drawn locally from Visual DNA.',
    ]);
    exit;
}

$prompt = cinematicImagePrompt($dna);
$b64 = openaiImagePng($apiKey, $model, $prompt);

if ($b64 === null) {
    echo json_encode([
        'source' => 'fallback',
        'image' => null,
        'note' => 'Image model unavailable — cinematic plate will be drawn locally from Visual DNA.',
    ]);
    exit;
}

echo json_encode([
    'source' => 'ai',
    'image' => 'data:image/png;base64,' . $b64,
]);
