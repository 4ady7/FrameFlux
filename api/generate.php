<?php

declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$raw = file_get_contents('php://input');
$data = json_decode($raw ?: '', true);

if (!is_array($data)) {
    http_response_code(400);
    echo json_encode(['error' => 'Expected JSON body']);
    exit;
}

$title = trim((string) ($data['title'] ?? ''));
$genre = trim((string) ($data['genre'] ?? ''));
$pitch = trim((string) ($data['pitch'] ?? ''));
$mode = trim((string) ($data['mode'] ?? 'generate'));
$previous = is_array($data['previous'] ?? null) ? $data['previous'] : null;
$variation = (int) ($data['variation'] ?? random_int(1, 1_000_000_000));
if ($variation < 1) {
    $variation = random_int(1, 1_000_000_000);
}

$allowedModes = ['generate', 'improve', 'background'];
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

if ($mode === 'improve' && $previous === null) {
    http_response_code(400);
    echo json_encode(['error' => 'Improve mode needs the previous poster parameters.']);
    exit;
}

$config = [];
$configPath = dirname(__DIR__) . '/config.php';
if (is_file($configPath)) {
    $loaded = require $configPath;
    if (is_array($loaded)) {
        $config = $loaded;
    }
}

$apiKey = (string) ($config['openai_api_key'] ?? getenv('OPENAI_API_KEY') ?: '');
$model = (string) ($config['openai_model'] ?? 'gpt-4o-mini');

$params = null;
$source = 'fallback';

if ($apiKey !== '') {
    $params = requestVisualParams($apiKey, $model, $title, $genre, $pitch, $variation, $mode, $previous);
    if ($params !== null) {
        $source = 'ai';
    }
}

if ($params === null) {
    $params = fallbackVisualParams($title, $genre, $pitch, $variation, $mode, $previous);
}

$params = normalizeParams($params, $title, $genre, $pitch);
$params['source'] = $source;
$params['mode'] = $mode;

echo json_encode($params);

function requestVisualParams(
    string $apiKey,
    string $model,
    string $title,
    string $genre,
    string $pitch,
    int $variation,
    string $mode,
    ?array $previous
): ?array {
    $shape = <<<'SHAPE'
{
  "palette": {
    "background": "#hex",
    "primary": "#hex",
    "secondary": "#hex",
    "accent": "#hex",
    "text": "#hex"
  },
  "pattern": "flow" | "grid" | "particles",
  "layout": "hero" | "editorial" | "billing",
  "mood": "short mood phrase",
  "quote": "short cinematic poster tagline, max 12 words",
  "density": number between 0.25 and 0.95,
  "contrast": number between 0.4 and 1,
  "titleStyle": "bold" | "elegant" | "condensed"
}
SHAPE;

    $layoutRules = <<<'RULES'
Layout meanings:
- hero = symmetric, centre-aligned hero block in the vertical middle.
- editorial = left-aligned editorial composition anchored near the top.
- billing = traditional bottom-billing poster stack near the lower edge.

Pattern meanings:
- flow = Perlin-noise flow field (organic, atmospheric).
- grid = geometric grid (precision, thriller, documentary).
- particles = scatter system (energy, horror, comedy).

Also invent a short original quote / tagline that feels like a poster line for this film.
Do not copy the pitch verbatim. Keep the quote under 12 words.
Colours must have strong poster contrast. Prefer cinematic palettes.
RULES;

    if ($mode === 'improve' && $previous !== null) {
        $prevJson = json_encode($previous, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        $prompt = <<<PROMPT
You are refining an existing FrameFlux poster concept. Return JSON only with this exact shape:
{$shape}

{$layoutRules}

The user liked the film idea but wants a stronger design. Keep the same film identity.
Improve on the previous output: stronger palette contrast, a clearer layout choice, a sharper quote, and a more fitting pattern.
Do not return an identical copy. Variation seed: {$variation}.

Film title: {$title}
Genre: {$genre}
Pitch: {$pitch}

Previous parameters:
{$prevJson}
PROMPT;
        $system = 'You refine existing poster parameters into a stronger design while staying faithful to the film.';
        $temperature = 0.85;
    } elseif ($mode === 'background') {
        $prompt = <<<PROMPT
You redesign ONLY the visual background direction for an existing FrameFlux poster.
Return JSON only with this exact shape:
{$shape}

{$layoutRules}

Keep the title, genre, and pitch identity. Prefer changing palette, pattern, density, and mood so the background feels freshly composed from the one-line pitch.
You may keep layout/titleStyle if they already work, but the background language should clearly change. Variation seed: {$variation}.

Film title: {$title}
Genre: {$genre}
Pitch: {$pitch}
PROMPT;
        $system = 'You regenerate poster background parameters from a film pitch while preserving film identity.';
        $temperature = 0.95;
    } else {
        $prompt = <<<PROMPT
You design visual direction for a conceptual movie poster. Return JSON only with this exact shape:
{$shape}

{$layoutRules}

This is a new design pass (variation {$variation}). Invent a distinct palette, pattern, layout, and quote.

Film title: {$title}
Genre: {$genre}
Pitch: {$pitch}
PROMPT;
        $system = 'You convert film concepts into strict visual parameters for a procedural poster generator.';
        $temperature = 1.05;
    }

    $payload = [
        'model' => $model,
        'temperature' => $temperature,
        'response_format' => ['type' => 'json_object'],
        'messages' => [
            ['role' => 'system', 'content' => $system],
            ['role' => 'user', 'content' => $prompt],
        ],
    ];

    $ch = curl_init('https://api.openai.com/v1/chat/completions');
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $apiKey,
        ],
        CURLOPT_POSTFIELDS => json_encode($payload),
        CURLOPT_TIMEOUT => 25,
    ]);

    $response = curl_exec($ch);
    $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if (!is_string($response) || $status < 200 || $status >= 300) {
        return null;
    }

    $decoded = json_decode($response, true);
    $content = $decoded['choices'][0]['message']['content'] ?? null;
    if (!is_string($content)) {
        return null;
    }

    $parsed = json_decode($content, true);
    return is_array($parsed) ? $parsed : null;
}

function fallbackVisualParams(
    string $title,
    string $genre,
    string $pitch,
    int $variation,
    string $mode,
    ?array $previous
): array {
    $key = strtolower($genre . ' ' . $pitch . ' ' . $title);
    $seed = abs(crc32($title . '|' . $genre . '|' . $pitch . '|' . $variation . '|' . $mode));

    $palettes = [
        'horror' => ['#12080c', '#3b0d16', '#7a1f2b', '#c45c2a', '#f3e6d4'],
        'sci-fi' => ['#061018', '#0b3a4a', '#148f9a', '#7ef0d2', '#e8fff8'],
        'romance' => ['#1c0d14', '#5c1f33', '#c45d6b', '#e8b4a2', '#f7efe8'],
        'comedy' => ['#14110a', '#d4550a', '#f08a2a', '#f2e27a', '#fff8e8'],
        'action' => ['#0c0c0c', '#8a1c12', '#e23a1a', '#f0a050', '#f5f1ea'],
        'thriller' => ['#0a1014', '#163044', '#2a6f8f', '#c9d6df', '#eef4f7'],
        'drama' => ['#15110d', '#3a2a1c', '#8a5a32', '#d8b48a', '#f4ead8'],
        'fantasy' => ['#0d0a18', '#2a1b4a', '#6b3fa0', '#d4a84b', '#f3ead4'],
        'documentary' => ['#101010', '#2c2c2c', '#6e6e6e', '#c8c4b8', '#f2f0ea'],
        'mystery' => ['#0c1018', '#1c2a44', '#3d5c7a', '#b9a06a', '#efe6d2'],
        'animation' => ['#10141c', '#1e5cff', '#ff5d3a', '#ffe36b', '#ffffff'],
        'western' => ['#16100a', '#5a3214', '#b56a28', '#e6c17a', '#f6ecd4'],
    ];

    $genreKey = 'drama';
    foreach ($palettes as $name => $unused) {
        if (str_contains($key, $name) || str_contains(strtolower($genre), $name)) {
            $genreKey = $name;
            break;
        }
    }
    if (str_contains($key, 'sci')) {
        $genreKey = 'sci-fi';
    }

    [$bg, $primary, $secondary, $accent, $text] = $palettes[$genreKey];
    $paletteNames = array_keys($palettes);
    $altKey = $paletteNames[$seed % count($paletteNames)];
    if (($seed % 3) === 0 || $mode === 'background') {
        [$bg, $primary, $secondary, $accent, $text] = $palettes[$altKey];
    }

    $patterns = ['flow', 'grid', 'particles'];
    $pattern = $patterns[$seed % count($patterns)];

    $layouts = ['hero', 'editorial', 'billing'];
    $layout = $layouts[intdiv($seed, 3) % count($layouts)];

    $styles = ['bold', 'elegant', 'condensed'];
    $titleStyle = $styles[intdiv($seed, 7) % count($styles)];

    if ($mode === 'improve' && is_array($previous)) {
        $prevPattern = (string) ($previous['pattern'] ?? '');
        $prevLayout = (string) ($previous['layout'] ?? '');
        $pattern = $patterns[($seed + 1) % count($patterns)];
        if ($pattern === $prevPattern) {
            $pattern = $patterns[($seed + 2) % count($patterns)];
        }
        $layout = $layouts[($seed + 1) % count($layouts)];
        if ($layout === $prevLayout) {
            $layout = $layouts[($seed + 2) % count($layouts)];
        }
        $titleStyle = $styles[($seed + 1) % count($styles)];
    }

    if ($mode === 'background' && is_array($previous)) {
        $layout = in_array($previous['layout'] ?? '', $layouts, true)
            ? (string) $previous['layout']
            : $layout;
        $titleStyle = in_array($previous['titleStyle'] ?? '', $styles, true)
            ? (string) $previous['titleStyle']
            : $titleStyle;
        $pattern = $patterns[($seed + 2) % count($patterns)];
    }

    $quote = fallbackQuote($title, $genre, $pitch, $seed);

    return [
        'palette' => [
            'background' => $bg,
            'primary' => $primary,
            'secondary' => $secondary,
            'accent' => $accent,
            'text' => $text,
        ],
        'pattern' => $pattern,
        'layout' => $layout,
        'mood' => $genreKey . ' atmosphere',
        'quote' => $quote,
        'density' => 0.45 + ($seed % 40) / 100,
        'contrast' => 0.7,
        'titleStyle' => $titleStyle,
    ];
}

function fallbackQuote(string $title, string $genre, string $pitch, int $seed): string
{
    $shortTitle = mb_strlen($title) > 28 ? mb_substr($title, 0, 25) . '…' : $title;
    $options = [
        'Where the story refuses to stay still.',
        'A quiet truth, lit by danger.',
        'Every map hides another city.',
        'Love, dread, and unfinished business.',
        'The night keeps better secrets.',
        'Nothing stays buried for long.',
    ];

    if ($pitch !== '') {
        $words = preg_split('/\s+/', trim($pitch)) ?: [];
        $snippet = implode(' ', array_slice($words, 0, 8));
        if ($snippet !== '') {
            $options[] = rtrim($snippet, '.,;:') . '.';
        }
    }

    $options[] = $shortTitle . ' begins after dark.';
    $options[] = ucfirst(strtolower($genre)) . ' never asks permission.';

    return $options[$seed % count($options)];
}

function normalizeParams(array $params, string $title, string $genre, string $pitch): array
{
    $patterns = ['flow', 'grid', 'particles'];
    $layouts = ['hero', 'editorial', 'billing'];
    $styles = ['bold', 'elegant', 'condensed'];

    // Migrate older layout names from earlier builds.
    $layoutAliases = [
        'centered' => 'hero',
        'top-heavy' => 'editorial',
        'bottom-heavy' => 'billing',
        'diagonal' => 'editorial',
    ];

    $rawLayout = (string) ($params['layout'] ?? 'hero');
    if (isset($layoutAliases[$rawLayout])) {
        $rawLayout = $layoutAliases[$rawLayout];
    }

    $palette = is_array($params['palette'] ?? null) ? $params['palette'] : [];
    $quote = trim((string) ($params['quote'] ?? ''));
    if ($quote === '') {
        $quote = 'A story waiting for its first frame.';
    }
    $quote = mb_substr($quote, 0, 120);

    return [
        'title' => $title,
        'genre' => $genre,
        'pitch' => $pitch,
        'palette' => [
            'background' => sanitizeHex($palette['background'] ?? '#111111', '#111111'),
            'primary' => sanitizeHex($palette['primary'] ?? '#884422', '#884422'),
            'secondary' => sanitizeHex($palette['secondary'] ?? '#336688', '#336688'),
            'accent' => sanitizeHex($palette['accent'] ?? '#e8c36a', '#e8c36a'),
            'text' => sanitizeHex($palette['text'] ?? '#f5f0e6', '#f5f0e6'),
        ],
        'pattern' => in_array($params['pattern'] ?? '', $patterns, true) ? $params['pattern'] : 'flow',
        'layout' => in_array($rawLayout, $layouts, true) ? $rawLayout : 'hero',
        'mood' => mb_substr(trim((string) ($params['mood'] ?? 'cinematic')), 0, 80),
        'quote' => $quote,
        'density' => round(clamp((float) ($params['density'] ?? 0.6), 0.25, 0.95), 2),
        'contrast' => clamp((float) ($params['contrast'] ?? 0.75), 0.4, 1.0),
        'titleStyle' => in_array($params['titleStyle'] ?? '', $styles, true) ? $params['titleStyle'] : 'bold',
    ];
}

function sanitizeHex(string $value, string $fallback): string
{
    return preg_match('/^#([0-9a-fA-F]{6})$/', $value) ? strtolower($value) : $fallback;
}

function clamp(float $value, float $min, float $max): float
{
    return max($min, min($max, $value));
}
