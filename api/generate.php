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
if (strlen($raw) > 32768) {
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

$title = trim((string) ($data['title'] ?? ''));
$genre = trim((string) ($data['genre'] ?? ''));
$pitch = trim((string) ($data['pitch'] ?? ''));
$mode = trim((string) ($data['mode'] ?? 'generate'));
$variation = (int) ($data['variation'] ?? random_int(1, 1_000_000_000));
if ($variation < 1) {
    $variation = random_int(1, 1_000_000_000);
}

$allowedModes = ['generate', 'improve', 'reimagine'];
if (!in_array($mode, $allowedModes, true)) {
    $mode = 'generate';
}

if ($title === '' || mb_strlen($title) > 80) {
    http_response_code(400);
    echo json_encode(['error' => 'A film title is required (max 80 characters).']);
    exit;
}

if ($genre === '' || mb_strlen($genre) > 80) {
    http_response_code(400);
    echo json_encode(['error' => 'A genre is required (max 80 characters).']);
    exit;
}

if (mb_strlen($pitch) > 280) {
    http_response_code(400);
    echo json_encode(['error' => 'Pitch must be 280 characters or fewer.']);
    exit;
}

$previous = whitelistPrevious(is_array($data['previous'] ?? null) ? $data['previous'] : null);

if (($mode === 'improve' || $mode === 'reimagine') && $previous === null) {
    http_response_code(400);
    echo json_encode(['error' => ucfirst($mode) . ' needs the previous Visual DNA.']);
    exit;
}

$config = framefluxConfig();
$apiKey = framefluxApiKey($config);
$model = framefluxChatModel($config);

$params = null;
$source = 'fallback';

if ($apiKey !== '') {
    $params = requestVisualDna($apiKey, $model, $title, $genre, $pitch, $variation, $mode, $previous);
    if ($params !== null) {
        $source = 'ai';
    }
}

if ($params === null) {
    $params = fallbackVisualParams($title, $genre, $pitch, $variation, $mode, $previous);
}

$dna = normalizeParams($params, $title, $genre, $pitch);
$dna['source'] = $source;
$dna['mode'] = $mode;

echo json_encode($dna);

function requestVisualDna(
    string $apiKey,
    string $model,
    string $title,
    string $genre,
    string $pitch,
    int $variation,
    string $mode,
    ?array $previous
): ?array {
    $shape = dnaShapePrompt();
    $rules = dnaRulesPrompt();

    if ($mode === 'improve' && $previous !== null) {
        $prevJson = json_encode($previous, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        $prompt = <<<PROMPT
You are refining an existing FrameFlux Visual DNA. Return JSON only with this exact shape:
{$shape}

{$rules}

Keep the same film identity. Strengthen palette contrast, cinematic staging, layout, and quote.
Do not return an identical copy. Variation seed: {$variation}.

Film title: {$title}
Genre: {$genre}
Pitch: {$pitch}

Previous Visual DNA (whitelisted):
{$prevJson}
PROMPT;
        $system = 'You refine FrameFlux Visual DNA into a stronger cinematic direction while staying faithful to the film.';
        $temperature = 0.85;
    } elseif ($mode === 'reimagine' && $previous !== null) {
        $prevJson = json_encode($previous, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        $prompt = <<<PROMPT
You reinterpret the same film as a new cinematic direction. Return JSON only with this exact shape:
{$shape}

{$rules}

Same title, genre, and pitch. New mood, lighting, camera, palette, layout, and patterns.
It must feel like a different creative take, not a seed change. Variation seed: {$variation}.

Film title: {$title}
Genre: {$genre}
Pitch: {$pitch}

Previous Visual DNA (whitelisted) — do not copy it:
{$prevJson}
PROMPT;
        $system = 'You reimagine a film as a new Visual DNA without abandoning the concept.';
        $temperature = 1.05;
    } else {
        $prompt = <<<PROMPT
You are the FrameFlux art director. Convert a film concept into Visual DNA. Return JSON only with this exact shape:
{$shape}

{$rules}

This is a new design pass (variation {$variation}). Invent a distinct cinematic world, palette, pattern pair, layout, and quote.

Film title: {$title}
Genre: {$genre}
Pitch: {$pitch}
PROMPT;
        $system = 'You convert film concepts into strict Visual DNA for a hybrid cinematic / procedural poster system.';
        $temperature = 1.05;
    }

    return openaiChatJson($apiKey, $model, $system, $prompt, $temperature);
}
