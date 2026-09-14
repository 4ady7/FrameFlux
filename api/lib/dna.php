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
const FRAMEFLUX_LIGHTING = ['chiaroscuro', 'neon', 'overcast', 'golden-hour', 'moonlit', 'practical', 'harsh', 'rim', 'backlit'];
const FRAMEFLUX_CAMERA = ['wide', 'close', 'aerial', 'dutch', 'tracking', 'static'];

const FRAMEFLUX_METAPHORS = [
    'fractured-glass',
    'eclipse',
    'locked-mechanism',
    'decaying-photograph',
    'tangled-roots',
    'maze',
    'burning-document',
    'distorted-reflection',
    'clock-mechanism',
    'biological-cell',
    'architectural-ruin',
    'orbital-system',
    'keyhole',
    'map-fold',
    'signal',
    'silhouette-threshold',
];

const FRAMEFLUX_MATERIALS = [
    'glass',
    'metal',
    'paper',
    'concrete',
    'fabric',
    'film-stock',
    'smoke',
    'water',
    'dust',
    'wood',
    'rust',
    'ink',
    'stone',
    'plastic',
];

const FRAMEFLUX_TEXTURES = [
    'distressed',
    'smooth',
    'grainy',
    'scratched',
    'weathered',
    'glossy',
    'dusty',
    'corroded',
    'fibrous',
    'translucent',
    'photographic',
];

const FRAMEFLUX_SPATIAL = [
    'compressed',
    'fragmented',
    'expanding',
    'collapsing',
    'spiralling',
    'rising',
    'drifting',
    'converging',
    'isolated',
    'claustrophobic',
    'expansive',
];

const FRAMEFLUX_PARTICLE_SEMANTICS = [
    'dust',
    'ash',
    'stars',
    'rain',
    'sparks',
    'pollen',
    'debris',
    'grain',
];

const FRAMEFLUX_LINE_SEMANTICS = [
    'cracks',
    'roots',
    'wiring',
    'threads',
    'veins',
    'roads',
    'circuitry',
    'plans',
];

const FRAMEFLUX_EMOTIONS = [
    'paranoia',
    'wonder',
    'grief',
    'isolation',
    'urgency',
    'nostalgia',
    'dread',
    'intimacy',
    'triumph',
    'unease',
    'longing',
];

const FRAMEFLUX_NARRATIVES = [
    'escape',
    'investigation',
    'forbidden-love',
    'survival',
    'identity',
    'betrayal',
    'discovery',
    'control',
    'family',
    'transformation',
    'obsession',
    'memory',
];

const FRAMEFLUX_TITLE_WEIGHTS = ['hairline', 'light', 'regular', 'bold', 'black'];
const FRAMEFLUX_TITLE_CASES = ['uppercase', 'title', 'lowercase', 'mixed'];
const FRAMEFLUX_LETTERFORMS = [
    'classical-serif',
    'slab-serif',
    'geometric-sans',
    'grotesque',
    'condensed',
    'extended',
    'hand-lettered',
    'distressed',
    'technical-stencil',
];
const FRAMEFLUX_TRACKING = ['tight', 'normal', 'wide'];
const FRAMEFLUX_TITLE_STRUCTURES = ['solid', 'outline', 'fragmented', 'layered', 'textured', 'gradient'];
const FRAMEFLUX_TITLE_PLACEMENTS = ['upper-third', 'lower-third', 'centered', 'split'];

const FRAMEFLUX_QUOTE_STYLES = [
    'editorial-italic',
    'caption',
    'cinematic-subtitle',
    'typewriter',
    'handwritten',
];
const FRAMEFLUX_QUOTE_LEGIBILITY = ['scrim', 'shadow', 'plate', 'none'];
const FRAMEFLUX_QUOTE_PLACEMENTS = ['below-title', 'bottom-anchored', 'focal-adjacent'];

/**
 * Letterform families grouped by typographic category. Used to force the quote
 * face to contrast with the title face instead of repeating it smaller.
 */
const FRAMEFLUX_LETTERFORM_CATEGORY = [
    'classical-serif' => 'serif',
    'slab-serif' => 'serif',
    'distressed' => 'serif',
    'geometric-sans' => 'sans',
    'grotesque' => 'sans',
    'condensed' => 'sans',
    'extended' => 'sans',
    'technical-stencil' => 'sans',
    'hand-lettered' => 'script',
];

const FRAMEFLUX_QUOTE_STYLE_CATEGORY = [
    'editorial-italic' => 'serif',
    'caption' => 'sans',
    'cinematic-subtitle' => 'sans',
    'typewriter' => 'mono',
    'handwritten' => 'script',
];

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
    $key = strtolower(trim($value));
    return in_array($key, $allowed, true) ? $key : $fallback;
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

function nextPattern(string $pattern): string
{
    $i = array_search($pattern, FRAMEFLUX_PATTERNS, true);
    $i = $i === false ? 0 : $i;
    return FRAMEFLUX_PATTERNS[($i + 1) % count(FRAMEFLUX_PATTERNS)];
}

/**
 * Keep only the fields the model is allowed to see on Improve / Reimagine.
 */
function whitelistPrevious(?array $previous): ?array
{
    if ($previous === null) {
        return null;
    }

    $normalized = normalizeParams($previous, (string) ($previous['title'] ?? $previous['concept']['title'] ?? 'Film'), (string) ($previous['genre'] ?? $previous['concept']['genre'] ?? 'Drama'), (string) ($previous['pitch'] ?? $previous['concept']['pitch'] ?? ''));

    return [
        'emotionalCore' => $normalized['semantic']['emotionalCore'],
        'narrativeCore' => $normalized['semantic']['narrativeCore'],
        'visualMetaphor' => $normalized['semantic']['visualMetaphor'],
        'narrativeAnchor' => $normalized['semantic']['narrativeAnchor'],
        'material' => $normalized['semantic']['material'],
        'texture' => $normalized['semantic']['texture'],
        'spatial' => $normalized['semantic']['spatial'],
        'particleSemantics' => $normalized['semantic']['particleSemantics'],
        'lineSemantics' => $normalized['semantic']['lineSemantics'],
        'mood' => $normalized['mood'],
        'quote' => $normalized['quote'],
        'palette' => [
            'background' => $normalized['palette']['background'],
            'primary' => $normalized['palette']['primary'],
            'secondary' => $normalized['palette']['secondary'],
            'accent' => $normalized['palette']['accent'],
            'text' => $normalized['palette']['text'],
        ],
        'layout' => $normalized['layout'],
        'primaryPattern' => $normalized['pattern'],
        'secondaryPattern' => $normalized['procedural']['secondaryPattern'],
        'titleStyle' => $normalized['titleStyle'],
        'titleTypographyDirection' => $normalized['typography']['title'],
        'quoteTypographyDirection' => $normalized['typography']['quote'],
        'lighting' => $normalized['cinematic']['lighting'],
        'atmosphere' => $normalized['cinematic']['atmosphere'],
        'camera' => $normalized['cinematic']['camera'],
        'lightDirection' => $normalized['lighting']['direction'],
    ];
}

/**
 * Words a tagline must never end on, so a trimmed pitch cannot read "under an."
 */
const FRAMEFLUX_DANGLING_WORDS = [
    'a', 'an', 'the', 'and', 'or', 'but', 'of', 'in', 'on', 'at', 'to', 'for',
    'from', 'with', 'by', 'as', 'into', 'onto', 'over', 'under', 'that', 'which',
    'who', 'whose', 'is', 'are', 'was', 'were', 'be', 'been', 'his', 'her',
    'their', 'its', 'this', 'these', 'those', 'when', 'while', 'after', 'before',
];

/**
 * The genre never reaches the poster, so it is deliberately not a quote source.
 */
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
        'Some doors only open once.',
        'What is kept is never safe.',
    ];

    if ($pitch !== '') {
        $words = preg_split('/\s+/', trim($pitch)) ?: [];
        $snippet = array_slice($words, 0, 9);
        // Never end a tagline on a dangling article or preposition.
        while ($snippet !== []) {
            $last = strtolower(rtrim((string) end($snippet), '.,;:!?'));
            if (!in_array($last, FRAMEFLUX_DANGLING_WORDS, true)) {
                break;
            }
            array_pop($snippet);
        }
        if (count($snippet) >= 4) {
            $options[] = rtrim(implode(' ', $snippet), '.,;:') . '.';
        }
    }

    $options[] = $shortTitle . ' begins after dark.';

    return $options[$seed % count($options)];
}

/**
 * Extract a film-specific semantic profile from title, genre, and pitch.
 * Prefer pitch/title cues over genre stereotypes.
 */
function inferSemanticProfile(string $title, string $genre, string $pitch, int $seed): array
{
    $text = strtolower($title . ' ' . $genre . ' ' . $pitch);

    $emotionRules = [
        'paranoia' => ['paranoid', 'watch', 'surveil', 'followed', 'suspect', 'trust'],
        'dread' => ['horror', 'haunt', 'curse', 'nightmare', 'terror', 'dread'],
        'isolation' => ['alone', 'isolat', 'desert', 'empty', 'abandoned', 'lone'],
        'wonder' => ['wonder', 'magic', 'dream', 'discover', 'star', 'cosmos', 'fantasy'],
        'grief' => ['grief', 'loss', 'mourn', 'funeral', 'widow', 'death', 'goodbye'],
        'urgency' => ['race', 'escape', 'deadline', 'chase', 'countdown', 'heist', 'bomb'],
        'nostalgia' => ['memory', 'childhood', 'past', 'remember', 'archive', 'letter'],
        'intimacy' => ['love', 'romance', 'kiss', 'affair', 'heart', 'desire'],
        'triumph' => ['victory', 'win', 'rise', 'champion', 'freedom'],
        'unease' => ['mystery', 'strange', 'uncanny', 'wrong', 'secret'],
        'longing' => ['miss', 'distant', 'wait', 'yearn', 'away'],
    ];

    $narrativeRules = [
        'investigation' => ['detect', 'clue', 'investig', 'case', 'evidence', 'mystery'],
        'escape' => ['escape', 'flee', 'prison', 'run', 'break'],
        'obsession' => ['obsess', 'fixat', 'compuls', 'cannot stop'],
        'forbidden-love' => ['affair', 'forbidden', 'secret love', 'romance'],
        'survival' => ['survive', 'apocalypse', 'stranded', 'war', 'disaster'],
        'identity' => ['identity', 'who am i', 'impostor', 'double', 'mask'],
        'betrayal' => ['betray', 'double-cross', 'traitor', 'lie'],
        'discovery' => ['discover', 'found', 'map', 'secret city', 'uncover'],
        'control' => ['control', 'system', 'surveil', 'regime', 'algorithm'],
        'memory' => ['memory', 'remember', 'forget', 'archive', 'photograph'],
        'transformation' => ['become', 'transform', 'mutation', 'change'],
        'family' => ['family', 'mother', 'father', 'daughter', 'son', 'home'],
    ];

    $metaphorRules = [
        'map-fold' => ['map', 'cartograph', 'city', 'street', 'atlas', 'border'],
        'fractured-glass' => ['glass', 'mirror', 'shatter', 'crack', 'window', 'reflection'],
        'keyhole' => ['key', 'lock', 'vault', 'door', 'secret', 'heist'],
        'locked-mechanism' => ['machine', 'mechanism', 'gear', 'clockwork', 'device'],
        'clock-mechanism' => ['time', 'clock', 'deadline', 'hour', 'countdown'],
        'decaying-photograph' => ['memory', 'photograph', 'archive', 'letter', 'past'],
        'tangled-roots' => ['root', 'family', 'forest', 'organic', 'bloodline'],
        'maze' => ['maze', 'labyrinth', 'corridor', 'lost', 'confus'],
        'eclipse' => ['eclipse', 'moon', 'sun', 'shadow', 'orbit'],
        'orbital-system' => ['space', 'planet', 'orbit', 'satellite', 'cosmos', 'sci'],
        'burning-document' => ['burn', 'fire', 'ash', 'document', 'evidence destroyed'],
        'distorted-reflection' => ['double', 'reflection', 'identity', 'impostor', 'twin'],
        'biological-cell' => ['body', 'virus', 'blood', 'organic', 'mutation'],
        'architectural-ruin' => ['ruin', 'building', 'concrete', 'collapse', 'cityscape'],
        'signal' => ['signal', 'radio', 'broadcast', 'frequency', 'message'],
        'silhouette-threshold' => ['doorway', 'threshold', 'figure', 'arrival', 'departure'],
    ];

    $materialRules = [
        'glass' => ['glass', 'mirror', 'window', 'crystal'],
        'metal' => ['metal', 'steel', 'copper', 'iron', 'vault', 'machine'],
        'paper' => ['paper', 'letter', 'document', 'map', 'photograph', 'archive'],
        'concrete' => ['concrete', 'bunker', 'brutal', 'parking', 'overpass'],
        'fabric' => ['fabric', 'cloth', 'curtain', 'dress', 'veil'],
        'film-stock' => ['film', 'cinema', 'photograph', 'memory'],
        'smoke' => ['smoke', 'fog', 'haze', 'ash', 'burn'],
        'water' => ['water', 'river', 'rain', 'ocean', 'flood'],
        'dust' => ['dust', 'desert', 'abandoned', 'attic'],
        'wood' => ['wood', 'cabin', 'forest', 'western'],
        'rust' => ['rust', 'corrosion', 'decay', 'industrial'],
        'ink' => ['ink', 'print', 'newspaper', 'blueprint'],
        'stone' => ['stone', 'ruin', 'temple', 'grave'],
        'plastic' => ['plastic', 'neon', 'synthetic', 'chrome'],
    ];

    $pick = static function (array $rules, array $fallbackList, string $text, int $seed) {
        $hits = [];
        foreach ($rules as $label => $needles) {
            foreach ($needles as $needle) {
                if (str_contains($text, $needle)) {
                    $hits[$label] = ($hits[$label] ?? 0) + 1;
                }
            }
        }
        if ($hits === []) {
            return $fallbackList[$seed % count($fallbackList)];
        }
        arsort($hits);
        return array_key_first($hits);
    };

    $emotion = $pick($emotionRules, FRAMEFLUX_EMOTIONS, $text, $seed);
    $narrative = $pick($narrativeRules, FRAMEFLUX_NARRATIVES, $text, $seed + 3);
    $metaphor = $pick($metaphorRules, FRAMEFLUX_METAPHORS, $text, $seed + 7);
    $material = $pick($materialRules, FRAMEFLUX_MATERIALS, $text, $seed + 11);

    // Particle / line semantics follow metaphor and material.
    $particleMap = [
        'burning-document' => 'ash',
        'eclipse' => 'stars',
        'orbital-system' => 'stars',
        'architectural-ruin' => 'dust',
        'tangled-roots' => 'pollen',
        'fractured-glass' => 'debris',
        'map-fold' => 'dust',
        'signal' => 'sparks',
        'biological-cell' => 'pollen',
    ];
    $lineMap = [
        'fractured-glass' => 'cracks',
        'tangled-roots' => 'roots',
        'maze' => 'plans',
        'map-fold' => 'roads',
        'locked-mechanism' => 'wiring',
        'clock-mechanism' => 'wiring',
        'orbital-system' => 'circuitry',
        'signal' => 'circuitry',
        'biological-cell' => 'veins',
        'decaying-photograph' => 'threads',
        'architectural-ruin' => 'plans',
    ];

    $particle = $particleMap[$metaphor] ?? FRAMEFLUX_PARTICLE_SEMANTICS[($seed + 5) % count(FRAMEFLUX_PARTICLE_SEMANTICS)];
    $line = $lineMap[$metaphor] ?? FRAMEFLUX_LINE_SEMANTICS[($seed + 9) % count(FRAMEFLUX_LINE_SEMANTICS)];

    $textureMap = [
        'glass' => 'glossy',
        'metal' => 'scratched',
        'paper' => 'fibrous',
        'concrete' => 'weathered',
        'fabric' => 'fibrous',
        'film-stock' => 'photographic',
        'smoke' => 'translucent',
        'water' => 'translucent',
        'dust' => 'dusty',
        'wood' => 'weathered',
        'rust' => 'corroded',
        'ink' => 'grainy',
        'stone' => 'weathered',
        'plastic' => 'smooth',
    ];
    $texture = $textureMap[$material] ?? 'grainy';

    $spatialMap = [
        'paranoia' => 'claustrophobic',
        'isolation' => 'isolated',
        'urgency' => 'compressed',
        'wonder' => 'expansive',
        'dread' => 'collapsing',
        'grief' => 'drifting',
        'obsession' => 'converging',
        'discovery' => 'expanding',
        'control' => 'compressed',
        'transformation' => 'spiralling',
    ];
    $spatial = $spatialMap[$emotion] ?? ($spatialMap[$narrative] ?? FRAMEFLUX_SPATIAL[$seed % count(FRAMEFLUX_SPATIAL)]);

    return [
        'emotionalCore' => $emotion,
        'narrativeCore' => $narrative,
        'visualMetaphor' => $metaphor,
        'narrativeAnchor' => $metaphor,
        'material' => $material,
        'texture' => $texture,
        'spatial' => $spatial,
        'particleSemantics' => $particle,
        'lineSemantics' => $line,
    ];
}

function patternForSemantic(array $semantic, int $seed): array
{
    $metaphor = $semantic['visualMetaphor'];
    $spatial = $semantic['spatial'];
    $primaryMap = [
        'fractured-glass' => 'mesh',
        'eclipse' => 'rings',
        'locked-mechanism' => 'grid',
        'clock-mechanism' => 'rings',
        'decaying-photograph' => 'particles',
        'tangled-roots' => 'flow',
        'maze' => 'grid',
        'burning-document' => 'particles',
        'distorted-reflection' => 'flow',
        'biological-cell' => 'rings',
        'architectural-ruin' => 'mesh',
        'orbital-system' => 'rings',
        'keyhole' => 'rings',
        'map-fold' => 'grid',
        'signal' => 'rings',
        'silhouette-threshold' => 'flow',
    ];
    $primary = $primaryMap[$metaphor] ?? FRAMEFLUX_PATTERNS[$seed % count(FRAMEFLUX_PATTERNS)];
    if ($spatial === 'fragmented' || $spatial === 'collapsing') {
        $primary = 'mesh';
    } elseif ($spatial === 'spiralling' || $spatial === 'converging') {
        $primary = 'rings';
    } elseif ($spatial === 'drifting' || $spatial === 'expansive') {
        $primary = $primary === 'grid' ? 'flow' : $primary;
    }
    $secondary = nextPattern($primary);
    if (in_array($semantic['particleSemantics'], ['ash', 'dust', 'sparks', 'stars'], true)) {
        $secondary = 'particles';
    }
    if ($secondary === $primary) {
        $secondary = nextPattern($primary);
    }
    return [$primary, $secondary];
}

function layoutForSemantic(array $semantic, int $seed): string
{
    return match ($semantic['spatial']) {
        'isolated', 'expansive' => 'centered',
        'rising', 'expanding' => 'off-center-bottom',
        'compressed', 'claustrophobic' => 'frame-inset',
        'fragmented' => 'split-editorial',
        'drifting' => 'off-center-top',
        default => FRAMEFLUX_LAYOUTS[intdiv($seed, 3) % count(FRAMEFLUX_LAYOUTS)],
    };
}

/**
 * Pick the first preferred quote style that does not sit in the same
 * typographic category as the title, so contrast is guaranteed without
 * every clash collapsing onto a single fallback face.
 */
function pickContrastingQuoteStyle(string $titleCategory, array $preferences, int $seed): string
{
    $rotation = FRAMEFLUX_QUOTE_STYLES;
    $offset = $seed % count($rotation);
    $rotated = array_merge(array_slice($rotation, $offset), array_slice($rotation, 0, $offset));

    foreach (array_merge($preferences, $rotated) as $style) {
        if (!in_array($style, FRAMEFLUX_QUOTE_STYLES, true)) {
            continue;
        }
        if ((FRAMEFLUX_QUOTE_STYLE_CATEGORY[$style] ?? 'serif') !== $titleCategory) {
            return $style;
        }
    }

    return 'caption';
}

/**
 * Derive title and quote typography from the story, not from a genre lookup.
 * Genre still nudges the result, but material / emotion / spatial lead.
 */
function inferTypographyDirection(array $semantic, string $genre, int $seed): array
{
    $emotion = $semantic['emotionalCore'];
    $narrative = $semantic['narrativeCore'];
    $material = $semantic['material'];
    $texture = $semantic['texture'];
    $spatial = $semantic['spatial'];
    $g = strtolower($genre);

    // Letterforms follow what the poster is made of.
    $letterforms = match ($material) {
        'glass', 'plastic' => 'geometric-sans',
        'water' => 'grotesque',
        'metal' => 'technical-stencil',
        'rust' => 'condensed',
        'paper', 'ink' => 'classical-serif',
        'film-stock' => 'grotesque',
        'concrete' => 'extended',
        'stone' => 'extended',
        'fabric' => 'classical-serif',
        'wood' => 'slab-serif',
        'smoke', 'dust' => 'distressed',
        default => 'grotesque',
    };
    if ($emotion === 'intimacy' && in_array($material, ['fabric', 'paper'], true)) {
        $letterforms = 'hand-lettered';
    }
    if ($narrative === 'control' && $material !== 'paper') {
        $letterforms = 'technical-stencil';
    }
    // Comedy resists heavy institutional letterforms.
    if (str_contains($g, 'comedy') && $letterforms === 'technical-stencil') {
        $letterforms = 'extended';
    }

    // Case treatment follows emotional register.
    $case = match ($emotion) {
        'intimacy', 'longing', 'grief' => 'lowercase',
        'nostalgia' => 'title',
        'urgency', 'paranoia', 'dread', 'triumph' => 'uppercase',
        'wonder' => 'title',
        'isolation' => 'lowercase',
        default => 'uppercase',
    };

    // Weight follows how loud the story is.
    $weight = match ($emotion) {
        'urgency', 'triumph' => 'black',
        'dread', 'paranoia' => 'bold',
        'intimacy', 'longing' => 'light',
        'isolation' => 'hairline',
        'grief' => 'light',
        default => 'regular',
    };
    if ($letterforms === 'hand-lettered' && in_array($weight, ['hairline', 'black'], true)) {
        $weight = 'regular';
    }

    // Tracking follows how much air the space has.
    $tracking = match ($spatial) {
        'compressed', 'claustrophobic', 'converging' => 'tight',
        'expansive', 'expanding', 'drifting', 'rising' => 'wide',
        default => 'normal',
    };

    // Structure follows material behaviour and spatial break-up.
    $structure = match (true) {
        in_array($spatial, ['fragmented', 'collapsing'], true) => 'fragmented',
        $material === 'glass' => 'outline',
        in_array($material, ['metal', 'rust'], true) => 'textured',
        in_array($texture, ['corroded', 'scratched', 'weathered'], true) => 'textured',
        in_array($material, ['smoke', 'dust'], true) => 'gradient',
        in_array($material, ['concrete', 'stone'], true) => 'layered',
        default => 'solid',
    };

    // Placement keeps the title out of the focal mass.
    $placement = match ($spatial) {
        'rising', 'expanding' => 'lower-third',
        'drifting' => 'upper-third',
        'isolated' => 'centered',
        'fragmented' => 'split',
        'claustrophobic', 'compressed' => 'lower-third',
        default => FRAMEFLUX_TITLE_PLACEMENTS[$seed % count(FRAMEFLUX_TITLE_PLACEMENTS)],
    };

    // Quote style follows how the story is "recorded", in preference order, so a
    // clash with the title face falls through to the next meaningful choice.
    $quotePrefs = [];
    if ($material === 'film-stock' || $texture === 'photographic') {
        $quotePrefs[] = 'cinematic-subtitle';
    }
    if (in_array($narrative, ['investigation', 'control', 'betrayal'], true)) {
        $quotePrefs[] = 'typewriter';
    }
    if ($emotion === 'intimacy' || $letterforms === 'hand-lettered') {
        $quotePrefs[] = 'handwritten';
    }
    if (in_array($emotion, ['nostalgia', 'grief', 'longing'], true)) {
        $quotePrefs[] = 'editorial-italic';
    }
    if (in_array($emotion, ['urgency', 'paranoia', 'dread'], true)) {
        $quotePrefs[] = 'caption';
    }
    // Secondary cues, kept narrow so they do not swamp the seeded rotation.
    if ($material === 'fabric') {
        $quotePrefs[] = 'handwritten';
    }
    if (in_array($emotion, ['wonder', 'triumph'], true)) {
        $quotePrefs[] = 'cinematic-subtitle';
    }

    $titleCategory = FRAMEFLUX_LETTERFORM_CATEGORY[$letterforms] ?? 'sans';
    $quoteStyle = pickContrastingQuoteStyle($titleCategory, $quotePrefs, $seed);
    $pairing = (FRAMEFLUX_QUOTE_STYLE_CATEGORY[$quoteStyle] ?? 'serif') === $titleCategory
        ? 'complement'
        : 'contrast';

    // Legibility technique belongs to the quote style.
    $quoteLegibility = match ($quoteStyle) {
        'cinematic-subtitle' => 'shadow',
        'typewriter' => 'plate',
        'handwritten' => 'plate',
        'caption' => 'scrim',
        default => 'scrim',
    };

    $quotePlacement = match ($placement) {
        'split' => 'bottom-anchored',
        'centered' => 'below-title',
        'lower-third' => 'below-title',
        'upper-third' => 'bottom-anchored',
        default => 'below-title',
    };
    if ($narrative === 'memory' && $quoteStyle === 'editorial-italic') {
        $quotePlacement = 'bottom-anchored';
    }

    return [
        'title' => [
            'weight' => $weight,
            'case' => $case,
            'letterforms' => $letterforms,
            'tracking' => $tracking,
            'structure' => $structure,
            'placement' => $placement,
        ],
        'quote' => [
            'style' => $quoteStyle,
            'pairing' => $pairing,
            'legibility' => $quoteLegibility,
            'placement' => $quotePlacement,
        ],
    ];
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
    $semantic = inferSemanticProfile($title, $genre, $pitch, $seed);

    if ($mode === 'improve' && is_array($previous)) {
        // Keep metaphor/material identity; shift spatial emphasis and secondary pattern only.
        $semantic['visualMetaphor'] = enumValue((string) ($previous['visualMetaphor'] ?? $semantic['visualMetaphor']), FRAMEFLUX_METAPHORS, $semantic['visualMetaphor']);
        $semantic['narrativeAnchor'] = enumValue((string) ($previous['narrativeAnchor'] ?? $semantic['narrativeAnchor']), FRAMEFLUX_METAPHORS, $semantic['narrativeAnchor']);
        $semantic['material'] = enumValue((string) ($previous['material'] ?? $semantic['material']), FRAMEFLUX_MATERIALS, $semantic['material']);
        $semantic['emotionalCore'] = enumValue((string) ($previous['emotionalCore'] ?? $semantic['emotionalCore']), FRAMEFLUX_EMOTIONS, $semantic['emotionalCore']);
        $semantic['narrativeCore'] = enumValue((string) ($previous['narrativeCore'] ?? $semantic['narrativeCore']), FRAMEFLUX_NARRATIVES, $semantic['narrativeCore']);
        $semantic['spatial'] = FRAMEFLUX_SPATIAL[($seed + 1) % count(FRAMEFLUX_SPATIAL)];
    }

    if ($mode === 'reimagine' && is_array($previous)) {
        // New metaphor while staying on the same narrative family when possible.
        $idx = array_search($semantic['visualMetaphor'], FRAMEFLUX_METAPHORS, true);
        $semantic['visualMetaphor'] = FRAMEFLUX_METAPHORS[(($idx === false ? 0 : $idx) + 3 + ($seed % 4)) % count(FRAMEFLUX_METAPHORS)];
        $semantic['narrativeAnchor'] = $semantic['visualMetaphor'];
        $semantic['material'] = FRAMEFLUX_MATERIALS[($seed + 4) % count(FRAMEFLUX_MATERIALS)];
        $semantic['spatial'] = FRAMEFLUX_SPATIAL[($seed + 2) % count(FRAMEFLUX_SPATIAL)];
    }

    $palettes = [
        'glass' => ['#0b1218', '#1c3a4a', '#6ea0b4', '#d7e8ef', '#f4f8fa'],
        'metal' => ['#101214', '#3a3f46', '#8a929b', '#c9a24a', '#ece7dc'],
        'paper' => ['#1a1510', '#5a4030', '#b08968', '#e7d3b0', '#f7efe2'],
        'concrete' => ['#121416', '#3b4046', '#7a828a', '#c2b8a3', '#efece6'],
        'fabric' => ['#171018', '#5a2a3c', '#a85d74', '#e2b7c2', '#f6ecef'],
        'film-stock' => ['#140f0c', '#4a2f1a', '#a8673a', '#e0b37a', '#f3e6d4'],
        'smoke' => ['#0d0e12', '#2c3340', '#6d7888', '#b9c0c9', '#e8ebef'],
        'water' => ['#061018', '#0f3a4a', '#1f7f8f', '#7fd0d8', '#e7f7f8'],
        'dust' => ['#17130e', '#5a4632', '#a8875a', '#d8c29a', '#f2e8d4'],
        'wood' => ['#140f0a', '#4a2f18', '#8a5a28', '#d2a46a', '#f0e0c4'],
        'rust' => ['#140c0a', '#5a2414', '#a84820', '#d4a06a', '#f0e2cc'],
        'ink' => ['#0c1016', '#1c2a44', '#3d5c7a', '#b9a06a', '#efe6d2'],
        'stone' => ['#121110', '#3c3934', '#7a7468', '#cfc6b4', '#f1ece2'],
        'plastic' => ['#0a0c14', '#1e3cff', '#ff4d6d', '#ffe36b', '#ffffff'],
    ];

    $genrePalettes = [
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

    [$bg, $primary, $secondary, $accent, $text] = $palettes[$semantic['material']] ?? $genrePalettes['drama'];
    foreach ($genrePalettes as $name => $unused) {
        if (str_contains($key, $name)) {
            // Blend: keep material-led palette but allow genre accent pull for familiarity.
            $accent = $genrePalettes[$name][3];
            break;
        }
    }

    [$pattern, $secondPat] = patternForSemantic($semantic, $seed);
    $layout = layoutForSemantic($semantic, $seed);
    $titleStyle = match ($semantic['emotionalCore']) {
        'intimacy', 'nostalgia', 'grief', 'longing' => 'elegant',
        'urgency', 'paranoia', 'control' => 'condensed',
        'wonder', 'triumph' => 'editorial',
        default => 'bold',
    };

    $lighting = match ($semantic['material']) {
        'glass', 'water' => 'backlit',
        'metal', 'rust' => 'harsh',
        'paper', 'fabric' => 'practical',
        'smoke', 'dust' => 'moonlit',
        'plastic' => 'neon',
        default => FRAMEFLUX_LIGHTING[$seed % count(FRAMEFLUX_LIGHTING)],
    };

    $camera = match ($semantic['spatial']) {
        'claustrophobic', 'compressed' => 'close',
        'expansive' => 'wide',
        'spiralling' => 'dutch',
        'isolated' => 'static',
        default => FRAMEFLUX_CAMERA[intdiv($seed, 5) % count(FRAMEFLUX_CAMERA)],
    };

    $typeDirection = inferTypographyDirection($semantic, $genre, $seed);

    return [
        'palette' => [
            'background' => $bg,
            'primary' => $primary,
            'secondary' => $secondary,
            'accent' => $accent,
            'text' => $text,
            'highlight' => $accent,
            'neutral' => $text,
        ],
        'pattern' => $pattern,
        'secondaryPattern' => $secondPat,
        'layout' => $layout,
        'titleTypographyDirection' => $typeDirection['title'],
        'quoteTypographyDirection' => $typeDirection['quote'],
        'mood' => $semantic['emotionalCore'] . ' · ' . $semantic['narrativeCore'],
        'quote' => fallbackQuote($title, $genre, $pitch, $seed),
        'density' => 0.4 + ($seed % 35) / 100,
        'contrast' => 0.74,
        'titleStyle' => $titleStyle,
        'emotionalCore' => $semantic['emotionalCore'],
        'narrativeCore' => $semantic['narrativeCore'],
        'visualMetaphor' => $semantic['visualMetaphor'],
        'narrativeAnchor' => $semantic['narrativeAnchor'],
        'material' => $semantic['material'],
        'texture' => $semantic['texture'],
        'spatial' => $semantic['spatial'],
        'particleSemantics' => $semantic['particleSemantics'],
        'lineSemantics' => $semantic['lineSemantics'],
        'lightDirection' => 0.15 + ($seed % 70) / 100,
        'shadowDensity' => 0.45 + ($seed % 40) / 100,
        'cinematic' => [
            'subject' => str_replace('-', ' ', $semantic['narrativeAnchor']),
            'environment' => $semantic['material'] . ' space under ' . $lighting . ' light',
            'lighting' => $lighting,
            'atmosphere' => $semantic['texture'] . ' ' . $semantic['material'],
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
    $semanticIn = is_array($params['semantic'] ?? null) ? $params['semantic'] : [];
    $lightingIn = is_array($params['lighting'] ?? null) ? $params['lighting'] : [];

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
        (string) ($cinematicIn['lighting'] ?? $lightingIn['type'] ?? ''),
        FRAMEFLUX_LIGHTING,
        'chiaroscuro'
    );
    $camera = enumValue(
        (string) ($cinematicIn['camera'] ?? ''),
        FRAMEFLUX_CAMERA,
        'wide'
    );

    $inferred = inferSemanticProfile(
        $title,
        $genre,
        $pitch,
        abs(crc32($title . '|' . $genre . '|' . $pitch))
    );

    $emotionalCore = enumValue(
        (string) ($semanticIn['emotionalCore'] ?? $params['emotionalCore'] ?? ''),
        FRAMEFLUX_EMOTIONS,
        $inferred['emotionalCore']
    );
    $narrativeCore = enumValue(
        (string) ($semanticIn['narrativeCore'] ?? $params['narrativeCore'] ?? ''),
        FRAMEFLUX_NARRATIVES,
        $inferred['narrativeCore']
    );
    $visualMetaphor = enumValue(
        (string) ($semanticIn['visualMetaphor'] ?? $params['visualMetaphor'] ?? ''),
        FRAMEFLUX_METAPHORS,
        $inferred['visualMetaphor']
    );
    $narrativeAnchor = enumValue(
        (string) ($semanticIn['narrativeAnchor'] ?? $params['narrativeAnchor'] ?? $visualMetaphor),
        FRAMEFLUX_METAPHORS,
        $visualMetaphor
    );
    $material = enumValue(
        (string) ($semanticIn['material'] ?? $params['material'] ?? ''),
        FRAMEFLUX_MATERIALS,
        $inferred['material']
    );
    $texture = enumValue(
        (string) ($semanticIn['texture'] ?? $params['texture'] ?? ''),
        FRAMEFLUX_TEXTURES,
        $inferred['texture']
    );
    $spatial = enumValue(
        (string) ($semanticIn['spatial'] ?? $params['spatial'] ?? ''),
        FRAMEFLUX_SPATIAL,
        $inferred['spatial']
    );
    $particleSemantics = enumValue(
        (string) ($semanticIn['particleSemantics'] ?? $params['particleSemantics'] ?? $proceduralIn['particleSemantics'] ?? ''),
        FRAMEFLUX_PARTICLE_SEMANTICS,
        $inferred['particleSemantics']
    );
    $lineSemantics = enumValue(
        (string) ($semanticIn['lineSemantics'] ?? $params['lineSemantics'] ?? $proceduralIn['lineSemantics'] ?? ''),
        FRAMEFLUX_LINE_SEMANTICS,
        $inferred['lineSemantics']
    );

    $focalX = clamp((float) ($compositionIn['focalX'] ?? $anchorsIn['focalX'] ?? $params['focalX'] ?? 0.5), 0.15, 0.85);
    $focalY = clamp((float) ($compositionIn['focalY'] ?? $anchorsIn['focalY'] ?? $params['focalY'] ?? 0.42), 0.15, 0.85);

    $density = round(clamp((float) ($proceduralIn['density'] ?? $params['density'] ?? 0.55), 0.2, 0.85), 2);
    $contrast = round(clamp((float) ($params['contrast'] ?? $proceduralIn['contrast'] ?? 0.75), 0.4, 1.0), 2);
    $intensity = round(clamp((float) ($cinematicIn['intensity'] ?? $proceduralIn['intensity'] ?? 0.55), 0.2, 1.0), 2);
    $tension = round(clamp((float) ($cinematicIn['tension'] ?? 0.5), 0.0, 1.0), 2);
    $materialEmphasis = round(clamp((float) ($params['materialEmphasis'] ?? $proceduralIn['materialEmphasis'] ?? 0.7), 0.3, 1.0), 2);
    $anchorScale = round(clamp((float) ($params['anchorScale'] ?? $compositionIn['anchorScale'] ?? 0.92), 0.7, 1.2), 2);
    $lightDirection = round(clamp((float) ($lightingIn['direction'] ?? $params['lightDirection'] ?? 0.35), 0.0, 1.0), 2);
    $shadowDensity = round(clamp((float) ($lightingIn['shadowDensity'] ?? $params['shadowDensity'] ?? 0.55), 0.2, 0.95), 2);

    $highlight = sanitizeHex((string) ($paletteIn['highlight'] ?? $paletteIn['accent'] ?? ''), '#e8c36a');
    $neutral = sanitizeHex((string) ($paletteIn['neutral'] ?? $paletteIn['text'] ?? ''), '#f5f0e6');

    $inferredType = inferTypographyDirection(
        [
            'emotionalCore' => $emotionalCore,
            'narrativeCore' => $narrativeCore,
            'material' => $material,
            'texture' => $texture,
            'spatial' => $spatial,
        ],
        $genre,
        abs(crc32($title . '|' . $genre . '|type'))
    );

    $titleDirIn = is_array($typographyIn['title'] ?? null)
        ? $typographyIn['title']
        : (is_array($params['titleTypographyDirection'] ?? null) ? $params['titleTypographyDirection'] : []);
    $quoteDirIn = is_array($typographyIn['quote'] ?? null)
        ? $typographyIn['quote']
        : (is_array($params['quoteTypographyDirection'] ?? null) ? $params['quoteTypographyDirection'] : []);

    $titleDirection = [
        'weight' => enumValue((string) ($titleDirIn['weight'] ?? ''), FRAMEFLUX_TITLE_WEIGHTS, $inferredType['title']['weight']),
        'case' => enumValue((string) ($titleDirIn['case'] ?? ''), FRAMEFLUX_TITLE_CASES, $inferredType['title']['case']),
        'letterforms' => enumValue((string) ($titleDirIn['letterforms'] ?? ''), FRAMEFLUX_LETTERFORMS, $inferredType['title']['letterforms']),
        'tracking' => enumValue((string) ($titleDirIn['tracking'] ?? ''), FRAMEFLUX_TRACKING, $inferredType['title']['tracking']),
        'structure' => enumValue((string) ($titleDirIn['structure'] ?? ''), FRAMEFLUX_TITLE_STRUCTURES, $inferredType['title']['structure']),
        'placement' => enumValue((string) ($titleDirIn['placement'] ?? ''), FRAMEFLUX_TITLE_PLACEMENTS, $inferredType['title']['placement']),
    ];

    $quoteStyle = enumValue((string) ($quoteDirIn['style'] ?? ''), FRAMEFLUX_QUOTE_STYLES, $inferredType['quote']['style']);

    // The quote must never simply repeat the title face at a smaller size.
    $titleCategory = FRAMEFLUX_LETTERFORM_CATEGORY[$titleDirection['letterforms']] ?? 'sans';
    if ((FRAMEFLUX_QUOTE_STYLE_CATEGORY[$quoteStyle] ?? 'serif') === $titleCategory) {
        $quoteStyle = pickContrastingQuoteStyle(
            $titleCategory,
            [$inferredType['quote']['style']],
            abs(crc32($title . '|' . $genre . '|quote'))
        );
        $warnings[] = 'quoteStyle re-paired against title';
    }

    $quoteDirection = [
        'style' => $quoteStyle,
        'pairing' => (FRAMEFLUX_QUOTE_STYLE_CATEGORY[$quoteStyle] ?? 'serif') === $titleCategory ? 'complement' : 'contrast',
        'legibility' => enumValue(
            (string) ($quoteDirIn['legibility'] ?? ''),
            FRAMEFLUX_QUOTE_LEGIBILITY,
            $inferredType['quote']['legibility']
        ),
        'placement' => enumValue(
            (string) ($quoteDirIn['placement'] ?? ''),
            FRAMEFLUX_QUOTE_PLACEMENTS,
            $inferredType['quote']['placement']
        ),
    ];

    return [
        'schemaVersion' => '1.1',
        'concept' => [
            'title' => $title,
            'genre' => $genre,
            'pitch' => $pitch,
            'mood' => $mood,
            'quote' => $quote,
        ],
        'semantic' => [
            'emotionalCore' => $emotionalCore,
            'narrativeCore' => $narrativeCore,
            'visualMetaphor' => $visualMetaphor,
            'narrativeAnchor' => $narrativeAnchor,
            'material' => $material,
            'texture' => $texture,
            'spatial' => $spatial,
            'particleSemantics' => $particleSemantics,
            'lineSemantics' => $lineSemantics,
        ],
        'cinematic' => [
            'subject' => clipText((string) ($cinematicIn['subject'] ?? str_replace('-', ' ', $narrativeAnchor)), 80, str_replace('-', ' ', $narrativeAnchor)),
            'environment' => clipText((string) ($cinematicIn['environment'] ?? $material . ' atmosphere'), 80, $material . ' atmosphere'),
            'lighting' => $lighting,
            'atmosphere' => clipText((string) ($cinematicIn['atmosphere'] ?? $texture . ' ' . $material), 60, $texture . ' ' . $material),
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
            'anchorScale' => $anchorScale,
        ],
        'procedural' => [
            'primaryPattern' => $primary,
            'secondaryPattern' => $secondary,
            'density' => $density,
            'intensity' => $intensity,
            'contrast' => $contrast,
            'materialEmphasis' => $materialEmphasis,
            'particleSemantics' => $particleSemantics,
            'lineSemantics' => $lineSemantics,
        ],
        'lighting' => [
            'type' => $lighting,
            'direction' => $lightDirection,
            'shadowDensity' => $shadowDensity,
        ],
        'anchors' => [
            'protectTitle' => true,
            'protectSubject' => true,
            'focalX' => $focalX,
            'focalY' => $focalY,
            'narrativeAnchor' => $narrativeAnchor,
        ],
        'typography' => [
            'titleStyle' => $titleStyle,
            'tracking' => $titleStyle === 'condensed' ? -0.02 : ($titleStyle === 'elegant' ? 0.04 : 0),
            // Locked: genre is an input for interpretation, never poster copy.
            'genreVisibility' => 'hidden',
            'title' => $titleDirection,
            'quote' => $quoteDirection,
        ],
        'variation' => [
            'role' => 'signature',
        ],
        'warnings' => $warnings,
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
  "density": number between 0.2 and 0.85,
  "contrast": number between 0.4 and 1,
  "titleStyle": "bold" | "elegant" | "condensed" | "geometric" | "editorial",
  "titleTypographyDirection": {
    "weight": "hairline" | "light" | "regular" | "bold" | "black",
    "case": "uppercase" | "title" | "lowercase" | "mixed",
    "letterforms": "classical-serif" | "slab-serif" | "geometric-sans" | "grotesque" | "condensed" | "extended" | "hand-lettered" | "distressed" | "technical-stencil",
    "tracking": "tight" | "normal" | "wide",
    "structure": "solid" | "outline" | "fragmented" | "layered" | "textured" | "gradient",
    "placement": "upper-third" | "lower-third" | "centered" | "split"
  },
  "quoteTypographyDirection": {
    "style": "editorial-italic" | "caption" | "cinematic-subtitle" | "typewriter" | "handwritten",
    "legibility": "scrim" | "shadow" | "plate" | "none",
    "placement": "below-title" | "bottom-anchored" | "focal-adjacent"
  },
  "emotionalCore": "paranoia" | "wonder" | "grief" | "isolation" | "urgency" | "nostalgia" | "dread" | "intimacy" | "triumph" | "unease" | "longing",
  "narrativeCore": "escape" | "investigation" | "forbidden-love" | "survival" | "identity" | "betrayal" | "discovery" | "control" | "family" | "transformation" | "obsession" | "memory",
  "visualMetaphor": "fractured-glass" | "eclipse" | "locked-mechanism" | "decaying-photograph" | "tangled-roots" | "maze" | "burning-document" | "distorted-reflection" | "clock-mechanism" | "biological-cell" | "architectural-ruin" | "orbital-system" | "keyhole" | "map-fold" | "signal" | "silhouette-threshold",
  "narrativeAnchor": "same controlled set as visualMetaphor — the dominant object the poster is about",
  "material": "glass" | "metal" | "paper" | "concrete" | "fabric" | "film-stock" | "smoke" | "water" | "dust" | "wood" | "rust" | "ink" | "stone" | "plastic",
  "texture": "distressed" | "smooth" | "grainy" | "scratched" | "weathered" | "glossy" | "dusty" | "corroded" | "fibrous" | "translucent" | "photographic",
  "spatial": "compressed" | "fragmented" | "expanding" | "collapsing" | "spiralling" | "rising" | "drifting" | "converging" | "isolated" | "claustrophobic" | "expansive",
  "particleSemantics": "dust" | "ash" | "stars" | "rain" | "sparks" | "pollen" | "debris" | "grain",
  "lineSemantics": "cracks" | "roots" | "wiring" | "threads" | "veins" | "roads" | "circuitry" | "plans",
  "lightDirection": number 0-1,
  "shadowDensity": number 0.2-0.95,
  "anchorScale": number 0.4-1.1,
  "materialEmphasis": number 0.3-1,
  "cinematic": {
    "subject": "who or what occupies the frame, no readable text",
    "environment": "place and time of day",
    "lighting": "chiaroscuro" | "neon" | "overcast" | "golden-hour" | "moonlit" | "practical" | "harsh" | "rim" | "backlit",
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
You are translating a SPECIFIC film concept into Visual DNA. Prefer story cues from the title and pitch over genre stereotypes.
Avoid generic mappings like horror=red/black or sci-fi=neon blue unless the story itself demands them.

LESS BUT BETTER:
- Choose ONE dominant visualMetaphor / narrativeAnchor.
- Choose ONE primary material.
- secondaryPattern must support the metaphor, not compete with it.
- density should usually stay moderate (0.35–0.7). Busy decoration is a failure.

Metaphor meanings (these become the poster's narrative anchor object):
- fractured-glass = identity, violence, fragile truth
- eclipse = omen, concealment, cosmic scale
- locked-mechanism / keyhole = secrets, heists, denied access
- decaying-photograph = memory, archive, grief
- tangled-roots = family, origin, entanglement
- maze = confusion, bureaucracy, being lost
- burning-document = erased evidence, danger
- distorted-reflection = doubles, impostors
- clock-mechanism = time pressure
- biological-cell = body, contagion, mutation
- architectural-ruin = collapse of systems/places
- orbital-system = systems, surveillance, space
- map-fold = cities, borders, cartography
- signal = messages, frequencies, contact
- silhouette-threshold = arrival, departure, liminal figures

particleSemantics and lineSemantics must match the metaphor (ash for fire, cracks for glass, roads for maps, etc).
Invent a short original quote. Do not copy the pitch. Max 12 words.
Cinematic subject describes the still, never poster type.

TYPOGRAPHY IS ART DIRECTION, NOT A TEMPLATE:
- Genre is NEVER printed on the poster. Do not request a genre label, badge, tag, or single-letter mark.
  Genre only informs colour, material, lighting, letterforms, and procedural choices.
- titleTypographyDirection must be derived from the story: weight from how loud it is, case from its
  emotional register, letterforms from the material language, tracking from how much air the space has,
  structure from material behaviour (glass wants outline, corroded metal wants textured, smoke wants gradient,
  fragmented space wants fragmented type).
- titleTypographyDirection.placement must sit in the composition's negative space, away from the focal mass.
- quoteTypographyDirection must CONTRAST with the title face, never repeat it smaller. A serif display title
  pairs with a sans or mono quote; a sans title pairs with an editorial serif quote.
- quoteTypographyDirection.legibility should feel native to the chosen quote style: editorial and caption
  styles take a thin scrim, cinematic subtitles take a soft shadow, typewriter and handwritten styles take a
  translucent plate.
- The quote must always read as clearly subordinate to the title.
RULES;
}

/**
 * Soft quality guardrails — flags generic or conflicting DNA without blocking generation.
 */
function assessDnaQuality(array $dna): array
{
    $notes = [];
    $semantic = is_array($dna['semantic'] ?? null) ? $dna['semantic'] : [];
    $proc = is_array($dna['procedural'] ?? null) ? $dna['procedural'] : [];
    $density = (float) ($proc['density'] ?? $dna['density'] ?? 0.55);

    if ($density > 0.78) {
        $notes[] = 'density-high';
    }
    if (($semantic['visualMetaphor'] ?? '') === ($semantic['narrativeAnchor'] ?? '') && ($semantic['visualMetaphor'] ?? '') === 'silhouette-threshold') {
        $notes[] = 'anchor-generic';
    }
    if (($proc['primaryPattern'] ?? '') === ($proc['secondaryPattern'] ?? '')) {
        $notes[] = 'pattern-collision';
    }

    $typography = is_array($dna['typography'] ?? null) ? $dna['typography'] : [];
    if (($typography['genreVisibility'] ?? '') !== 'hidden') {
        $notes[] = 'genre-visibility-unlocked';
    }
    if (($typography['quote']['pairing'] ?? 'contrast') !== 'contrast') {
        $notes[] = 'quote-pairing-not-contrasting';
    }

    $particle = $semantic['particleSemantics'] ?? '';
    $metaphor = $semantic['visualMetaphor'] ?? '';
    $expected = [
        'burning-document' => 'ash',
        'fractured-glass' => 'debris',
        'eclipse' => 'stars',
        'orbital-system' => 'stars',
        'map-fold' => 'dust',
    ];
    if (isset($expected[$metaphor]) && $particle !== '' && $particle !== $expected[$metaphor] && !in_array($particle, ['dust', 'grain'], true)) {
        // Soft note only — AI may choose intentionally.
        $notes[] = 'particle-metaphor-loose';
    }

    return $notes;
}
