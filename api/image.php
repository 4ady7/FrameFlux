<?php

declare(strict_types=1);

require_once __DIR__ . '/lib/config.php';
require_once __DIR__ . '/lib/dna.php';
require_once __DIR__ . '/lib/openai.php';

set_time_limit(120);
ignore_user_abort(true);

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
// #region agent log
$log = json_encode(['sessionId' => '78eac4', 'hypothesisId' => 'A', 'location' => 'api/image.php:37', 'message' => 'image api key check', 'data' => ['hasKey' => $apiKey !== '', 'model' => $model], 'timestamp' => (int) (microtime(true) * 1000)]) . "\n";
file_put_contents('/Users/shady/Projects/FrameFlux/.cursor/debug-78eac4.log', $log, FILE_APPEND);
// #endregion
$title = (string) ($dna['concept']['title'] ?? $dna['title'] ?? 'Untitled');
$timestamp = date('H:i:s');

if ($apiKey === '') {
    error_log("[{$timestamp}] ⚠️ gpt-image-2 SKIPPED: No API key resolved from config. Using procedural plate for '{$title}'.");
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
    error_log("[{$timestamp}] ❌ gpt-image-2 FAILED: OpenAI rejected request, cURL timed out, or returned null for '{$title}'. Falling back to local plate.");
    echo json_encode([
        'source' => 'fallback',
        'image' => null,
        'note' => 'Image model unavailable — cinematic plate will be drawn locally from Visual DNA.',
    ]);
    exit;
}

error_log("[{$timestamp}] ✅ gpt-image-2 SUCCESS: Received key art still ({$model}) for '{$title}'.");
echo json_encode([
    'source' => 'ai',
    'image' => 'data:image/png;base64,' . $b64,
]);
