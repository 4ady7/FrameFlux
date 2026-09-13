<?php

declare(strict_types=1);

const FRAMEFLUX_PATTERNS = ['flow', 'grid', 'particles', 'rings', 'mesh'];
const FRAMEFLUX_LAYOUTS = [
    'centered',
    'off-center-top',
    'off-center-bottom',
    'split-editorial',
    'frame-inset',
];
const FRAMEFLUX_STYLES = ['bold', 'elegant', 'condensed', 'geometric', 'editorial'];
const FRAMEFLUX_LIGHTING = ['chiaroscuro', 'neon', 'overcast', 'golden-hour', 'moonlit', 'practical', 'harsh'];
const FRAMEFLUX_CAMERA = ['wide', 'close', 'aerial', 'dutch', 'tracking', 'static'];

const FRAMEFLUX_LAYOUT_ALIASES = [
    'hero' => 'centered',
    'centered' => 'centered',
    'editorial' => 'off-center-top',
    'top-heavy' => 'off-center-top',
    'off-center-top' => 'off-center-top',
    'billing' => 'off-center-bottom',
    'bottom-heavy' => 'off-center-bottom',
    'off-center-bottom' => 'off-center-bottom',
    'split-editorial' => 'split-editorial',
    'diagonal' => 'split-editorial',
    'frame-inset' => 'frame-inset',
];

const FRAMEFLUX_PATTERN_ALIASES = [
    'flow' => 'flow',
    'grid' => 'grid',
    'particles' => 'particles',
    'rings' => 'rings',
    'mesh' => 'mesh',
    'concentric' => 'rings',
    'angular' => 'mesh',
];

function sanitizeHex(string $value, string $fallback): string
{
    return preg_match('/^#([0-9a-fA-F]{6})$/', $value) ? strtolower($value) : $fallback;
}

function clamp(float $value, float $min, float $max): float
{
    return max($min, min($max, $value));
}

function enumValue(string $value, array $allowed, string $fallback): string
{
    return in_array($value, $allowed, true) ? $value : $fallback;
}

function clipText(string $value, int $max, string $fallback = ''): string
{
    $trimmed = trim($value);
    if ($trimmed === '') {
        return $fallback;
    }
    return mb_substr($trimmed, 0, $max);
}

function resolveLayout(string $raw): string
{
    $key = strtolower(trim($raw));
    return FRAMEFLUX_LAYOUT_ALIASES[$key] ?? 'centered';
}

function resolvePattern(string $raw, string $fallback = 'flow'): string
{
    $key = strtolower(trim($raw));
    return FRAMEFLUX_PATTERN_ALIASES[$key] ?? $fallback;
}

/**
 * Keep only the fields the model is allowed to see on Improve / Reimagine.
 */
function whitelistPrevious(?array $previous): ?array
{
    if ($previous === null) {
        return null;
    }

    $palette = is_array($previous['palette'] ?? null) ? $previous['palette'] : [];
    $concept = is_array($previous['concept'] ?? null) ? $previous['concept'] : [];
    $cinematic = is_array($previous['cinematic'] ?? null) ? $previous['cinematic'] : [];
    $composition = is_array($previous['composition'] ?? null) ? $previous['composition'] : [];
    $procedural = is_array($previous['procedural'] ?? null) ? $previous['procedural'] : [];
    $typography = is_array($previous['typography'] ?? null) ? $previous['typography'] : [];

    $pattern = resolvePattern((string) ($procedural['primaryPattern'] ?? $previous['pattern'] ?? 'flow'));
    $secondary = resolvePattern((string) ($procedural['secondaryPattern'] ?? 'grid'));
    $layout = resolveLayout((string) ($composition['layout'] ?? $previous['layout'] ?? 'centered'));

    return [
        'mood' => clipText((string) ($concept['mood'] ?? $previous['mood'] ?? ''), 80),
        'quote' => clipText((string) ($concept['quote'] ?? $previous['quote'] ?? ''), 120),
        'palette' => [
            'background' => sanitizeHex((string) ($palette['background'] ?? ''), '#111111'),
            'primary' => sanitizeHex((string) ($palette['primary'] ?? ''), '#884422'),
            'secondary' => sanitizeHex((string) ($palette['secondary'] ?? ''), '#336688'),
            'accent' => sanitizeHex((string) ($palette['accent'] ?? ''), '#e8c36a'),
            'text' => sanitizeHex((string) ($palette['text'] ?? ''), '#f5f0e6'),
        ],
        'layout' => $layout,
        'primaryPattern' => $pattern,
        'secondaryPattern' => $secondary === $pattern ? nextPattern($pattern) : $secondary,
        'titleStyle' => enumValue(
            (string) ($typography['titleStyle'] ?? $previous['titleStyle'] ?? 'bold'),
            FRAMEFLUX_STYLES,
            'bold'
        ),
        'lighting' => clipText((string) ($cinematic['lighting'] ?? ''), 40),
        'atmosphere' => clipText((string) ($cinematic['atmosphere'] ?? ''), 60),
        'camera' => clipText((string) ($cinematic['camera'] ?? ''), 24),
    ];
}

function nextPattern(string $pattern): string
{
    $i = array_search($pattern, FRAMEFLUX_PATTERNS, true);
    $i = $i === false ? 0 : $i;
    return FRAMEFLUX_PATTERNS[($i + 1) % count(FRAMEFLUX_PATTERNS)];
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
    if (($seed % 3) === 0 || $mode === 'reimagine') {
        [$bg, $primary, $secondary, $accent, $text] = $palettes[$altKey];
    }

    $pattern = FRAMEFLUX_PATTERNS[$seed % count(FRAMEFLUX_PATTERNS)];
    $secondPat = FRAMEFLUX_PATTERNS[($seed + 2) % count(FRAMEFLUX_PATTERNS)];
    if ($secondPat === $pattern) {
        $secondPat = nextPattern($pattern);
    }

    $layout = FRAMEFLUX_LAYOUTS[intdiv($seed, 3) % count(FRAMEFLUX_LAYOUTS)];
    $titleStyle = FRAMEFLUX_STYLES[intdiv($seed, 7) % 3]; // keep mapping to bold/elegant/condensed mostly
    $lighting = FRAMEFLUX_LIGHTING[$seed % count(FRAMEFLUX_LIGHTING)];
    $camera = FRAMEFLUX_CAMERA[intdiv($seed, 5) % count(FRAMEFLUX_CAMERA)];

    if ($mode === 'improve' && is_array($previous)) {
        $prevPattern = (string) ($previous['primaryPattern'] ?? $previous['pattern'] ?? '');
        $prevLayout = (string) ($previous['layout'] ?? '');
        $pattern = FRAMEFLUX_PATTERNS[($seed + 1) % count(FRAMEFLUX_PATTERNS)];
        if ($pattern === resolvePattern($prevPattern, $pattern)) {
            $pattern = FRAMEFLUX_PATTERNS[($seed + 2) % count(FRAMEFLUX_PATTERNS)];
        }
        $layout = FRAMEFLUX_LAYOUTS[($seed + 1) % count(FRAMEFLUX_LAYOUTS)];
        if ($layout === resolveLayout($prevLayout)) {
            $layout = FRAMEFLUX_LAYOUTS[($seed + 2) % count(FRAMEFLUX_LAYOUTS)];
        }
        $titleStyle = FRAMEFLUX_STYLES[($seed + 1) % 3];
        $secondPat = nextPattern($pattern);
    }

    if ($mode === 'reimagine' && is_array($previous)) {
        $pattern = FRAMEFLUX_PATTERNS[($seed + 3) % count(FRAMEFLUX_PATTERNS)];
        $layout = FRAMEFLUX_LAYOUTS[($seed + 4) % count(FRAMEFLUX_LAYOUTS)];
        $secondPat = nextPattern($pattern);
    }

    $environments = [
        'horror' => 'abandoned interior at night',
        'sci-fi' => 'rain-slick megacity',
        'romance' => 'quiet coastal town',
        'comedy' => 'sunlit suburban street',
        'action' => 'industrial overpass',
        'thriller' => 'empty parking structure',
        'drama' => 'weathered apartment stairwell',
        'fantasy' => 'mist over black water',
        'documentary' => 'working harbour at dawn',
        'mystery' => 'fogged riverside archive',
        'animation' => 'impossible stacked city',
        'western' => 'dust road at dusk',
    ];

    return [
        'palette' => [
            'background' => $bg,
            'primary' => $primary,
            'secondary' => $secondary,
            'accent' => $accent,
            'text' => $text,
        ],
        'pattern' => $pattern,
        'secondaryPattern' => $secondPat,
        'layout' => $layout,
        'mood' => $genreKey . ' atmosphere',
        'quote' => fallbackQuote($title, $genre, $pitch, $seed),
        'density' => 0.45 + ($seed % 40) / 100,
        'contrast' => 0.72,
        'titleStyle' => $titleStyle,
        'cinematic' => [
            'subject' => 'a lone figure held in the frame',
            'environment' => $environments[$genreKey] ?? 'cinematic landscape',
            'lighting' => $lighting,
            'atmosphere' => $genreKey . ' haze',
            'camera' => $camera,
            'tension' => 0.35 + ($seed % 50) / 100,
            'intensity' => 0.4 + ($seed % 45) / 100,
        ],
        'focalX' => 0.42 + ($seed % 16) / 100,
        'focalY' => 0.38 + (($seed >> 3) % 18) / 100,
    ];
}

function normalizeParams(array $params, string $title, string $genre, string $pitch): array
{
    $warnings = [];

    $paletteIn = is_array($params['palette'] ?? null) ? $params['palette'] : [];
    $conceptIn = is_array($params['concept'] ?? null) ? $params['concept'] : [];
    $cinematicIn = is_array($params['cinematic'] ?? null) ? $params['cinematic'] : [];
    $compositionIn = is_array($params['composition'] ?? null) ? $params['composition'] : [];
    $proceduralIn = is_array($params['procedural'] ?? null) ? $params['procedural'] : [];
    $typographyIn = is_array($params['typography'] ?? null) ? $params['typography'] : [];
    $anchorsIn = is_array($params['anchors'] ?? null) ? $params['anchors'] : [];

    $rawPattern = (string) ($proceduralIn['primaryPattern'] ?? $params['pattern'] ?? 'flow');
    $primary = resolvePattern($rawPattern);
    if ($primary !== strtolower(trim($rawPattern)) && $rawPattern !== '') {
        $warnings[] = 'primaryPattern coerced';
    }

    $rawSecondary = (string) ($proceduralIn['secondaryPattern'] ?? $params['secondaryPattern'] ?? '');
    $secondary = $rawSecondary === '' ? nextPattern($primary) : resolvePattern($rawSecondary, nextPattern($primary));
    if ($secondary === $primary) {
        $secondary = nextPattern($primary);
        $warnings[] = 'secondaryPattern duplicated primary';
    }

    $layout = resolveLayout((string) ($compositionIn['layout'] ?? $params['layout'] ?? 'centered'));
    $titleStyle = enumValue(
        (string) ($typographyIn['titleStyle'] ?? $params['titleStyle'] ?? 'bold'),
        FRAMEFLUX_STYLES,
        'bold'
    );

    $quote = clipText((string) ($conceptIn['quote'] ?? $params['quote'] ?? ''), 120, 'A story waiting for its first frame.');
    $mood = clipText((string) ($conceptIn['mood'] ?? $params['mood'] ?? 'cinematic'), 80, 'cinematic');

    $lighting = enumValue(
        (string) ($cinematicIn['lighting'] ?? ''),
        FRAMEFLUX_LIGHTING,
        'chiaroscuro'
    );
    $camera = enumValue(
        (string) ($cinematicIn['camera'] ?? ''),
        FRAMEFLUX_CAMERA,
        'wide'
    );

    $focalX = clamp((float) ($compositionIn['focalX'] ?? $anchorsIn['focalX'] ?? $params['focalX'] ?? 0.5), 0.15, 0.85);
    $focalY = clamp((float) ($compositionIn['focalY'] ?? $anchorsIn['focalY'] ?? $params['focalY'] ?? 0.42), 0.15, 0.85);

    $density = round(clamp((float) ($proceduralIn['density'] ?? $params['density'] ?? 0.6), 0.25, 0.95), 2);
    $contrast = round(clamp((float) ($params['contrast'] ?? $proceduralIn['contrast'] ?? 0.75), 0.4, 1.0), 2);
    $intensity = round(clamp((float) ($cinematicIn['intensity'] ?? $proceduralIn['intensity'] ?? 0.55), 0.2, 1.0), 2);
    $tension = round(clamp((float) ($cinematicIn['tension'] ?? 0.5), 0.0, 1.0), 2);

    $highlight = sanitizeHex((string) ($paletteIn['highlight'] ?? $paletteIn['accent'] ?? ''), '#e8c36a');
    $neutral = sanitizeHex((string) ($paletteIn['neutral'] ?? $paletteIn['text'] ?? ''), '#f5f0e6');

    return [
        'schemaVersion' => '1.0',
        'concept' => [
            'title' => $title,
            'genre' => $genre,
            'pitch' => $pitch,
            'mood' => $mood,
            'quote' => $quote,
        ],
        'cinematic' => [
            'subject' => clipText((string) ($cinematicIn['subject'] ?? 'a figure held in cinematic space'), 80, 'a figure held in cinematic space'),
            'environment' => clipText((string) ($cinematicIn['environment'] ?? 'atmospheric landscape'), 80, 'atmospheric landscape'),
            'lighting' => $lighting,
            'atmosphere' => clipText((string) ($cinematicIn['atmosphere'] ?? $mood), 60, $mood),
            'camera' => $camera,
            'tension' => $tension,
            'intensity' => $intensity,
        ],
        'palette' => [
            'background' => sanitizeHex((string) ($paletteIn['background'] ?? ''), '#111111'),
            'primary' => sanitizeHex((string) ($paletteIn['primary'] ?? ''), '#884422'),
            'secondary' => sanitizeHex((string) ($paletteIn['secondary'] ?? ''), '#336688'),
            'accent' => sanitizeHex((string) ($paletteIn['accent'] ?? ''), '#e8c36a'),
            'text' => sanitizeHex((string) ($paletteIn['text'] ?? ''), '#f5f0e6'),
            'highlight' => $highlight,
            'neutral' => $neutral,
        ],
        'composition' => [
            'layout' => $layout,
            'focalX' => $focalX,
            'focalY' => $focalY,
        ],
        'procedural' => [
            'primaryPattern' => $primary,
            'secondaryPattern' => $secondary,
            'density' => $density,
            'intensity' => $intensity,
            'contrast' => $contrast,
        ],
        'anchors' => [
            'protectTitle' => true,
            'protectSubject' => true,
            'focalX' => $focalX,
            'focalY' => $focalY,
        ],
        'typography' => [
            'titleStyle' => $titleStyle,
        ],
        'variation' => [
            'role' => 'signature',
        ],
        'warnings' => $warnings,
        // Convenience aliases used by the existing renderer and tests.
        'title' => $title,
        'genre' => $genre,
        'pitch' => $pitch,
        'mood' => $mood,
        'quote' => $quote,
        'pattern' => $primary,
        'layout' => $layout,
        'density' => $density,
        'contrast' => $contrast,
        'titleStyle' => $titleStyle,
    ];
}

function dnaShapePrompt(): string
{
    return <<<'SHAPE'
{
  "palette": {
    "background": "#hex",
    "primary": "#hex",
    "secondary": "#hex",
    "accent": "#hex",
    "text": "#hex",
    "highlight": "#hex",
    "neutral": "#hex"
  },
  "pattern": "flow" | "grid" | "particles" | "rings" | "mesh",
  "secondaryPattern": "flow" | "grid" | "particles" | "rings" | "mesh",
  "layout": "centered" | "off-center-top" | "off-center-bottom" | "split-editorial" | "frame-inset",
  "mood": "short mood phrase",
  "quote": "short cinematic poster tagline, max 12 words",
  "density": number between 0.25 and 0.95,
  "contrast": number between 0.4 and 1,
  "titleStyle": "bold" | "elegant" | "condensed" | "geometric" | "editorial",
  "cinematic": {
    "subject": "who or what occupies the frame, no readable text",
    "environment": "place and time of day",
    "lighting": "chiaroscuro" | "neon" | "overcast" | "golden-hour" | "moonlit" | "practical" | "harsh",
    "atmosphere": "weather / haze / dust",
    "camera": "wide" | "close" | "aerial" | "dutch" | "tracking" | "static",
    "tension": number 0-1,
    "intensity": number 0-1
  },
  "focalX": number 0.15-0.85,
  "focalY": number 0.15-0.85
}
SHAPE;
}

function dnaRulesPrompt(): string
{
    return <<<'RULES'
Layout meanings (these change crop, negative space, and title-safe geometry — not just title Y):
- centered = symmetric hero block, subject in the middle.
- off-center-top = left-aligned editorial type high in the frame; image crop biased downward.
- off-center-bottom = traditional billing stack; image crop biased upward.
- split-editorial = type holds the left third; cinematic world occupies the right.
- frame-inset = poster sits inside a margin; type near the lower third.

Pattern meanings:
- flow = Perlin-noise flow field (movement, emotion, atmosphere).
- grid = geometric structure (systems, architecture, control).
- particles = scatter (dust, sparks, ash, energy).
- rings = concentric / elliptical signals around a focal point.
- mesh = angular fragmentation, tension, digital space.

secondaryPattern MUST differ from pattern.
Invent a short original quote / tagline. Do not copy the pitch. Max 12 words.
Cinematic subject and environment describe a still photograph, never poster type.
Colours must have strong poster contrast. Prefer cinematic palettes.
RULES;
}
