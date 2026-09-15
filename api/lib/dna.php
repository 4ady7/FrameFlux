<?php

declare(strict_types=1);

require_once __DIR__ . '/grammar.php';

const FRAMEFLUX_PATTERNS = ['flow', 'grid', 'particles', 'rings', 'mesh'];
const FRAMEFLUX_LAYOUTS = [
    'centered',
    'off-center-top',
    'off-center-bottom',
    'split-editorial',
    'frame-inset',
];
const FRAMEFLUX_STYLES = ['bold', 'elegant', 'condensed', 'geometric', 'editorial'];
const FRAMEFLUX_LIGHTING = [
    'chiaroscuro',
    'neon',
    'overcast',
    'golden-hour',
    'moonlit',
    'practical',
    'harsh',
    'rim',
    'backlit',
    'high-key',
    'theatrical',
    'coastal-haze',
    'bloom',
    'hard-sun',
    'shaft',
    'domestic-warm',
    'window-light',
];
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
    'chaotic-key',
    'chandelier-cluster',
    'tangled-cords',
    'coastal-compass',
    'handwritten-letter',
    'weathered-door',
    'correspondence-clock',
    'railway-route',
    'paired-objects',
    'postcard',
    'compass-rose',
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
    'foil',
    'cardstock',
    'linen',
    'leather',
    'brass',
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
    'confetti',
    'salt',
    'sand',
    'ember',
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
    'cords',
    'ribbons',
    'waves',
    'coastline',
    'handwriting',
    'horizon',
    'contour',
    'trails',
    'railway',
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
    'playfulness',
    'hope',
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

const FRAMEFLUX_LIGHTING_ALIASES = [
    'noir' => 'chiaroscuro',
    'hard' => 'harsh',
    'dramatic' => 'chiaroscuro',
    'neon-wash' => 'neon',
];

const FRAMEFLUX_METAPHOR_ALIASES = [
    'lock' => 'locked-mechanism',
    'clock-face' => 'clock-mechanism',
    'clock' => 'clock-mechanism',
    'signal-burst' => 'signal',
    'silhouette' => 'silhouette-threshold',
    'roots' => 'tangled-roots',
];

const FRAMEFLUX_LINE_ALIASES = [
    'thread' => 'threads',
    'wave' => 'waves',
    'trail' => 'trails',
    'crack' => 'cracks',
];

const FRAMEFLUX_PARTICLE_ALIASES = [
    'spark' => 'sparks',
    'starfield' => 'stars',
    'crystal-shard' => 'debris',
];

const FRAMEFLUX_HUMAN_ELEMENTS = [
    'none',
    'silhouette',
    'hands',
    'letter',
    'tickets',
    'cups',
    'paired-objects',
    'signage',
    'map',
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
    'organic' => 'flow',
    'topo' => 'flow',
    'fiber' => 'flow',
    'halftone' => 'particles',
    'noise' => 'particles',
    'none' => 'flow',
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

function aliasedEnum(string $value, array $aliases, array $allowed, string $fallback): string
{
    $key = strtolower(trim($value));
    $key = $aliases[$key] ?? $key;
    return enumValue($key, $allowed, $fallback);
}

function coerceToList(string $value, array $allowed, string $fallback): string
{
    if ($allowed === []) {
        return $fallback;
    }
    return in_array($value, $allowed, true) ? $value : $fallback;
}

function hexLuminance(string $hex): float
{
    $hex = ltrim($hex, '#');
    if (strlen($hex) !== 6) {
        return 0.15;
    }
    $r = hexdec(substr($hex, 0, 2));
    $g = hexdec(substr($hex, 2, 2));
    $b = hexdec(substr($hex, 4, 2));
    return (0.299 * $r + 0.587 * $g + 0.114 * $b) / 255.0;
}

function familyEmotionDefault(string $family): string
{
    return match ($family) {
        'comedy', 'animation', 'musical' => 'playfulness',
        'romance' => 'longing',
        'contemporary', 'coming-of-age', 'family' => 'nostalgia',
        'adventure', 'fantasy' => 'wonder',
        'thriller' => 'urgency',
        'horror' => 'dread',
        'scifi' => 'unease',
        'mystery' => 'unease',
        'historical', 'documentary' => 'nostalgia',
        default => 'intimacy',
    };
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
        'grammarFamily' => $normalized['semantic']['grammarFamily'],
        'narrativeEnergy' => $normalized['semantic']['narrativeEnergy'],
        'compositionGrammar' => $normalized['composition']['grammar'],
        'compositionMode' => $normalized['composition']['mode'] ?? null,
        'artFamily' => $normalized['semantic']['artFamily'] ?? null,
        'proceduralFamily' => $normalized['procedural']['family'],
        'humanElements' => $normalized['semantic']['humanElements'],
        'groundTone' => $normalized['semantic']['groundTone'],
        'print' => $normalized['print'] ?? null,
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
 * Genre family is a visual grammar: metaphor, material, and line language are
 * chosen from that family's allow-list. Keyword hits still win when they fit.
 */
function inferSemanticProfile(string $title, string $genre, string $pitch, int $seed): array
{
    $text = strtolower($title . ' ' . $genre . ' ' . $pitch);
    $family = inferGenreFamily($genre, $title . ' ' . $pitch);
    $grammar = genreGrammar($family);

    $emotionRules = [
        'playfulness' => ['comedy', 'funny', 'joke', 'absurd', 'sitcom', 'hilarious', 'farce', 'screwball', 'punchline'],
        'paranoia' => ['paranoid', 'watch', 'surveil', 'followed', 'suspect', 'trust'],
        'dread' => ['horror', 'haunt', 'curse', 'nightmare', 'terror', 'dread'],
        'isolation' => ['alone', 'isolat', 'desert', 'empty', 'abandoned', 'lone', 'solitude'],
        'wonder' => ['wonder', 'magic', 'dream', 'discover', 'star', 'cosmos', 'fantasy'],
        'grief' => ['grief', 'loss', 'mourn', 'funeral', 'widow', 'death', 'goodbye'],
        'urgency' => ['race', 'escape', 'deadline', 'chase', 'countdown', 'heist', 'bomb'],
        'nostalgia' => ['memory', 'childhood', 'past', 'remember', 'archive', 'letter', 'summer'],
        'intimacy' => ['love', 'romance', 'kiss', 'affair', 'heart', 'desire', 'letters'],
        'triumph' => ['victory', 'win', 'rise', 'champion', 'freedom'],
        'unease' => ['mystery', 'strange', 'uncanny', 'wrong', 'secret'],
        'longing' => ['miss', 'distant', 'wait', 'yearn', 'away', 'solitude', 'between'],
        'hope' => ['hope', 'dawn', 'begin', 'promise'],
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
        'memory' => ['memory', 'remember', 'forget', 'archive', 'photograph', 'letter'],
        'transformation' => ['become', 'transform', 'mutation', 'change'],
        'family' => ['family', 'mother', 'father', 'daughter', 'son', 'home'],
    ];

    $metaphorRules = [
        'chaotic-key' => ['mansion', 'rent', 'landlord', 'keyhole', 'key ring'],
        'chandelier-cluster' => ['chandelier', 'ballroom', 'gala', 'luxury'],
        'tangled-cords' => ['tangled', 'cords', 'wires', 'mess'],
        'coastal-compass' => ['cape', 'coast', 'solitude', 'lighthouse', 'sea glass', 'harbour', 'harbor'],
        'handwritten-letter' => ['letter', 'correspondence', 'handwrit', 'stationery'],
        'correspondence-clock' => ['station', 'timetable', 'commute', 'between stations'],
        'railway-route' => ['train', 'railway', 'platform', 'tracks'],
        'postcard' => ['postcard', 'summer', 'holiday', 'vacation'],
        'weathered-door' => ['doorway', 'threshold', 'old house', 'porch'],
        'paired-objects' => ['two', 'pair', 'together', 'between'],
        'compass-rose' => ['compass', 'expedition', 'voyage', 'navigate'],
        'map-fold' => ['map', 'cartograph', 'city', 'street', 'atlas', 'border'],
        'fractured-glass' => ['glass', 'mirror', 'shatter', 'crack', 'window', 'reflection'],
        'keyhole' => ['key', 'lock', 'vault', 'door', 'secret', 'heist'],
        'locked-mechanism' => ['machine', 'mechanism', 'gear', 'clockwork', 'device'],
        'clock-mechanism' => ['time', 'clock', 'deadline', 'hour', 'countdown'],
        'decaying-photograph' => ['memory', 'photograph', 'archive', 'past'],
        'tangled-roots' => ['root', 'family', 'forest', 'organic', 'bloodline'],
        'maze' => ['maze', 'labyrinth', 'corridor', 'lost', 'confus'],
        'eclipse' => ['eclipse', 'moon', 'sun', 'shadow', 'orbit'],
        'orbital-system' => ['space', 'planet', 'orbit', 'satellite', 'cosmos', 'sci'],
        'burning-document' => ['burn', 'fire', 'ash', 'document', 'evidence destroyed'],
        'distorted-reflection' => ['double', 'reflection', 'identity', 'impostor', 'twin'],
        'biological-cell' => ['body', 'virus', 'blood', 'organic', 'mutation'],
        'architectural-ruin' => ['ruin', 'building', 'concrete', 'collapse', 'cityscape'],
        'signal' => ['signal', 'radio', 'broadcast', 'frequency', 'message'],
        'silhouette-threshold' => ['figure', 'arrival', 'departure', 'silhouette'],
    ];

    $materialRules = [
        'glass' => ['glass', 'mirror', 'window', 'crystal', 'chandelier'],
        'foil' => ['foil', 'luxury', 'glitz', 'mansion'],
        'cardstock' => ['cardstock', 'ticket', 'stationery', 'brochure'],
        'linen' => ['linen', 'cloth', 'coast', 'summer'],
        'leather' => ['leather', 'expedition', 'journal', 'saddle'],
        'brass' => ['brass', 'compass', 'instrument', 'spyglass'],
        'metal' => ['metal', 'steel', 'copper', 'iron', 'vault', 'machine'],
        'paper' => ['paper', 'letter', 'document', 'map', 'photograph', 'archive'],
        'concrete' => ['concrete', 'bunker', 'brutal', 'parking', 'overpass'],
        'fabric' => ['fabric', 'cloth', 'curtain', 'dress', 'veil'],
        'film-stock' => ['film', 'cinema', 'photograph', 'memory'],
        'smoke' => ['smoke', 'fog', 'haze', 'ash', 'burn'],
        'water' => ['water', 'river', 'rain', 'ocean', 'flood', 'sea', 'coast'],
        'dust' => ['dust', 'desert', 'abandoned', 'attic'],
        'wood' => ['wood', 'cabin', 'forest', 'western'],
        'rust' => ['rust', 'corrosion', 'decay', 'industrial'],
        'ink' => ['ink', 'print', 'newspaper', 'blueprint'],
        'stone' => ['stone', 'ruin', 'temple', 'grave'],
        'plastic' => ['plastic', 'neon', 'synthetic', 'chrome'],
    ];

    $pickHits = static function (array $rules, string $text, array $allow = []) {
        $hits = [];
        foreach ($rules as $label => $needles) {
            if ($allow !== [] && !in_array($label, $allow, true)) {
                continue;
            }
            foreach ($needles as $needle) {
                if (str_contains($text, $needle)) {
                    $hits[$label] = ($hits[$label] ?? 0) + 1;
                }
            }
        }
        if ($hits === []) {
            return null;
        }
        arsort($hits);
        return array_key_first($hits);
    };

    $allowedMetaphors = $grammar['metaphors'] ?? FRAMEFLUX_METAPHORS;
    $allowedMaterials = $grammar['materials'] ?? FRAMEFLUX_MATERIALS;

    $emotion = $pickHits($emotionRules, $text) ?? familyEmotionDefault($family);
    $narrative = $pickHits($narrativeRules, $text) ?? FRAMEFLUX_NARRATIVES[($seed + 3) % count(FRAMEFLUX_NARRATIVES)];

    $hint = storyMetaphorHint($title . ' ' . $pitch, $allowedMetaphors);
    $metaphor = $hint ?? $pickHits($metaphorRules, $text) ?? pickFromGrammar($allowedMetaphors, $seed + 7);
    if (!in_array($metaphor, FRAMEFLUX_METAPHORS, true)) {
        $metaphor = pickFromGrammar($allowedMetaphors, $seed + 7);
    }

    $material = $pickHits($materialRules, $text, $allowedMaterials)
        ?? $pickHits($materialRules, $text)
        ?? pickFromGrammar($allowedMaterials, $seed + 11);
    if (!in_array($material, $allowedMaterials, true) && in_array($material, ['metal', 'plastic', 'concrete'], true) && !isTechFamily($family)) {
        $material = pickFromGrammar($allowedMaterials, $seed + 11);
    }
    $material = in_array($material, FRAMEFLUX_MATERIALS, true) ? $material : pickFromGrammar($allowedMaterials, $seed + 11);

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
        'chaotic-key' => 'confetti',
        'chandelier-cluster' => 'confetti',
        'tangled-cords' => 'confetti',
        'coastal-compass' => 'salt',
        'handwritten-letter' => 'dust',
        'correspondence-clock' => 'dust',
        'railway-route' => 'dust',
        'postcard' => 'pollen',
        'compass-rose' => 'sand',
        'weathered-door' => 'dust',
        'paired-objects' => 'dust',
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
        'chaotic-key' => 'cords',
        'chandelier-cluster' => 'ribbons',
        'tangled-cords' => 'cords',
        'coastal-compass' => 'waves',
        'handwritten-letter' => 'handwriting',
        'correspondence-clock' => 'railway',
        'railway-route' => 'railway',
        'postcard' => 'horizon',
        'compass-rose' => 'contour',
        'weathered-door' => 'horizon',
        'paired-objects' => 'threads',
    ];

    $allowedParticles = $grammar['particleSemantics'] ?? FRAMEFLUX_PARTICLE_SEMANTICS;
    $allowedLines = $grammar['lineSemantics'] ?? FRAMEFLUX_LINE_SEMANTICS;
    $particle = coerceToList(
        $particleMap[$metaphor] ?? pickFromGrammar($allowedParticles, $seed + 5),
        $allowedParticles,
        pickFromGrammar($allowedParticles, $seed + 5)
    );
    $line = coerceToList(
        $lineMap[$metaphor] ?? pickFromGrammar($allowedLines, $seed + 9),
        $allowedLines,
        pickFromGrammar($allowedLines, $seed + 9)
    );

    if (!isTechFamily($family) && in_array($line, FRAMEFLUX_TECH_LINES, true)) {
        $line = pickFromGrammar($allowedLines, $seed + 9);
    }

    $textureMap = [
        'glass' => 'glossy',
        'foil' => 'glossy',
        'metal' => 'scratched',
        'brass' => 'corroded',
        'leather' => 'weathered',
        'paper' => 'fibrous',
        'cardstock' => 'fibrous',
        'linen' => 'fibrous',
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
    $allowedTextures = $grammar['textures'] ?? FRAMEFLUX_TEXTURES;
    $texture = coerceToList(
        $textureMap[$material] ?? pickFromGrammar($allowedTextures, $seed + 13),
        $allowedTextures,
        pickFromGrammar($allowedTextures, $seed + 13)
    );

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
        'playfulness' => 'fragmented',
        'longing' => 'drifting',
        'nostalgia' => 'isolated',
        'intimacy' => 'isolated',
        'hope' => 'rising',
    ];
    $allowedSpatial = $grammar['spatialFeelings'] ?? FRAMEFLUX_SPATIAL;
    $spatial = coerceToList(
        $spatialMap[$emotion] ?? ($spatialMap[$narrative] ?? pickFromGrammar($allowedSpatial, $seed)),
        $allowedSpatial,
        pickFromGrammar($allowedSpatial, $seed)
    );

    $compositionGrammar = pickFromGrammar($grammar['compositionGrammars'] ?? COMPOSITION_GRAMMARS, $seed + 17);
    $procPrimary = pickFromGrammar($grammar['proceduralPrimary'] ?? PROCEDURAL_FAMILIES, $seed + 19);
    $procSecondary = pickFromGrammar($grammar['proceduralSecondary'] ?? PROCEDURAL_FAMILIES, $seed + 23);
    if ($procSecondary === $procPrimary) {
        $procSecondary = pickFromGrammar($grammar['proceduralSecondary'] ?? PROCEDURAL_FAMILIES, $seed + 29);
    }
    $procAccent = pickFromGrammar($grammar['proceduralAccent'] ?? ['particle'], $seed + 31);
    $human = pickFromGrammar(($grammar['humanElements'] ?? []) !== [] ? $grammar['humanElements'] : ['none'], $seed + 37);
    if ($human === '') {
        $human = 'none';
    }

    $energy = (float) ($grammar['narrativeEnergyDefault'] ?? 0.5);
    if ($emotion === 'playfulness' || $emotion === 'urgency' || $emotion === 'triumph') {
        $energy = min(0.95, $energy + 0.08);
    }
    if ($emotion === 'isolation' || $emotion === 'longing' || $emotion === 'grief' || $emotion === 'nostalgia') {
        $energy = max(0.12, $energy - 0.08);
    }

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
        'grammarFamily' => $family,
        'narrativeEnergy' => round($energy, 2),
        'compositionGrammar' => $compositionGrammar,
        'proceduralFamily' => $procPrimary,
        'proceduralSecondaryFamily' => $procSecondary,
        'proceduralAccent' => $procAccent,
        'humanElements' => $human,
        'groundTone' => $grammar['groundTone'] ?? 'mid',
        'lightingStyle' => pickFromGrammar($grammar['lightingStyles'] ?? FRAMEFLUX_LIGHTING, $seed + 41),
        'compositionMode' => compositionModeFor($family, $compositionGrammar, $seed + 43),
        'artFamily' => artFamilyFor($family, $metaphor, $procPrimary, $seed + 47),
    ];
}

function patternForSemantic(array $semantic, int $seed): array
{
    $family = (string) ($semantic['grammarFamily'] ?? inferGenreFamily('drama'));
    $grammar = genreGrammar($family);
    $allowed = $grammar['patterns'] ?? FRAMEFLUX_PATTERNS;
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
        'chaotic-key' => 'flow',
        'chandelier-cluster' => 'particles',
        'tangled-cords' => 'flow',
        'coastal-compass' => 'flow',
        'handwritten-letter' => 'flow',
        'weathered-door' => 'flow',
        'correspondence-clock' => 'flow',
        'railway-route' => 'flow',
        'paired-objects' => 'particles',
        'postcard' => 'particles',
        'compass-rose' => 'flow',
    ];
    $primary = $primaryMap[$metaphor] ?? pickFromGrammar($allowed, $seed);
    if ($spatial === 'fragmented' || $spatial === 'collapsing') {
        $primary = in_array('flow', $allowed, true) ? 'flow' : $primary;
        if (isTechFamily($family)) {
            $primary = 'mesh';
        }
    } elseif ($spatial === 'spiralling' || $spatial === 'converging') {
        $primary = in_array('rings', $allowed, true) ? 'rings' : $primary;
    } elseif ($spatial === 'drifting' || $spatial === 'expansive') {
        $primary = $primary === 'grid' ? 'flow' : $primary;
    }

    $primary = coerceToList($primary, $allowed, pickFromGrammar($allowed, $seed));
    if (!isTechFamily($family) && in_array($primary, FRAMEFLUX_TECH_PATTERNS, true)) {
        $primary = in_array('flow', $allowed, true) ? 'flow' : pickFromGrammar($allowed, $seed);
    }

    $secondary = count($allowed) > 1
        ? $allowed[(array_search($primary, $allowed, true) + 1) % count($allowed)]
        : nextPattern($primary);
    if (in_array($semantic['particleSemantics'], ['ash', 'dust', 'sparks', 'stars', 'confetti', 'salt', 'sand'], true)
        && in_array('particles', $allowed, true)) {
        $secondary = 'particles';
    }
    if ($secondary === $primary) {
        $secondary = nextPattern($primary);
        $secondary = coerceToList($secondary, $allowed, $secondary);
        if ($secondary === $primary && count($allowed) > 1) {
            $secondary = $allowed[0] === $primary ? $allowed[1] : $allowed[0];
        }
    }
    if (!isTechFamily($family) && in_array($secondary, FRAMEFLUX_TECH_PATTERNS, true)) {
        $secondary = 'particles';
    }
    return [$primary, $secondary];
}

function layoutForSemantic(array $semantic, int $seed): string
{
    $family = (string) ($semantic['grammarFamily'] ?? 'drama');
    $composition = (string) ($semantic['compositionGrammar'] ?? '');
    if ($composition !== '') {
        $layout = layoutForComposition($composition, $seed, $family);
    } else {
        $layout = match ($semantic['spatial'] ?? '') {
            'isolated', 'expansive' => 'centered',
            'rising', 'expanding' => 'off-center-bottom',
            'compressed', 'claustrophobic' => isTechFamily($family) ? 'frame-inset' : ($family === 'comedy' ? 'off-center-bottom' : 'split-editorial'),
            'fragmented' => $family === 'comedy' ? 'off-center-top' : 'split-editorial',
            'drifting' => 'off-center-top',
            default => FRAMEFLUX_LAYOUTS[intdiv($seed, 3) % count(FRAMEFLUX_LAYOUTS)],
        };
    }
    if ($layout === 'frame-inset' && !isTechFamily($family)) {
        $layout = $family === 'comedy' ? 'off-center-bottom' : 'split-editorial';
    }
    if ($family === 'comedy' && $layout === 'split-editorial') {
        $layout = $seed % 2 === 0 ? 'off-center-top' : 'off-center-bottom';
    }
    return $layout;
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
    $family = (string) ($semantic['grammarFamily'] ?? inferGenreFamily($genre));
    $grammar = genreGrammar($family);

    // Letterforms follow what the poster is made of.
    $letterforms = match ($material) {
        'glass', 'plastic', 'foil' => 'geometric-sans',
        'water' => 'grotesque',
        'metal' => 'technical-stencil',
        'brass' => 'slab-serif',
        'rust' => 'condensed',
        'paper', 'ink', 'cardstock' => 'classical-serif',
        'linen', 'fabric' => 'classical-serif',
        'leather' => 'slab-serif',
        'film-stock' => 'grotesque',
        'concrete' => 'extended',
        'stone' => 'extended',
        'wood' => 'slab-serif',
        'smoke', 'dust' => 'distressed',
        default => 'grotesque',
    };
    if ($emotion === 'intimacy' && in_array($material, ['fabric', 'paper', 'linen', 'cardstock'], true)) {
        $letterforms = 'hand-lettered';
    }
    if ($narrative === 'control' && $material !== 'paper' && isTechFamily($family)) {
        $letterforms = 'technical-stencil';
    }
    if (in_array($family, ['comedy', 'animation', 'musical'], true)) {
        if (in_array($letterforms, ['technical-stencil', 'distressed', 'classical-serif'], true)) {
            $letterforms = 'extended';
        }
    }
    if (in_array($family, ['romance', 'contemporary', 'coming-of-age'], true) && $letterforms === 'technical-stencil') {
        $letterforms = 'classical-serif';
    }
    if ($family === 'contemporary' && $letterforms === 'hand-lettered') {
        $letterforms = 'classical-serif';
    }
    if (str_contains($g, 'comedy') && $letterforms === 'technical-stencil') {
        $letterforms = 'extended';
    }

    // Case treatment follows emotional register.
    $case = match ($emotion) {
        'intimacy', 'longing', 'grief' => 'lowercase',
        'nostalgia', 'hope' => 'title',
        'playfulness' => 'mixed',
        'urgency', 'paranoia', 'dread', 'triumph' => 'uppercase',
        'wonder' => 'title',
        'isolation' => 'lowercase',
        default => 'uppercase',
    };

    $weight = match ($emotion) {
        'urgency', 'triumph', 'playfulness' => 'black',
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
        $material === 'glass' || $material === 'foil' => $family === 'comedy' ? 'layered' : 'outline',
        in_array($material, ['metal', 'rust', 'brass'], true) => 'textured',
        in_array($texture, ['corroded', 'scratched', 'weathered'], true) => 'textured',
        in_array($material, ['smoke', 'dust'], true) => 'gradient',
        in_array($material, ['concrete', 'stone'], true) => 'layered',
        default => 'solid',
    };
    $structure = coerceToList($structure, $grammar['titleTreatmentBias'] ?? FRAMEFLUX_TITLE_STRUCTURES, $structure);

    $placement = match ($spatial) {
        'rising', 'expanding' => 'lower-third',
        'drifting' => 'upper-third',
        'isolated' => 'centered',
        'fragmented' => 'split',
        'claustrophobic', 'compressed' => 'lower-third',
        default => FRAMEFLUX_TITLE_PLACEMENTS[$seed % count(FRAMEFLUX_TITLE_PLACEMENTS)],
    };
    $placement = coerceToList($placement, $grammar['titlePlacementBias'] ?? FRAMEFLUX_TITLE_PLACEMENTS, $placement);

    $quotePrefs = $grammar['quoteStyleBias'] ?? [];
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
    if (in_array($emotion, ['urgency', 'paranoia', 'dread', 'playfulness'], true)) {
        $quotePrefs[] = 'caption';
    }
    if ($material === 'fabric' || $material === 'linen') {
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
    $seed = abs(crc32($title . '|' . $genre . '|' . $pitch . '|' . $variation . '|' . $mode));
    $semantic = inferSemanticProfile($title, $genre, $pitch, $seed);

    if ($mode === 'improve' && is_array($previous)) {
        $family = (string) ($previous['grammarFamily'] ?? $semantic['grammarFamily']);
        $grammar = genreGrammar($family);
        $allowedMetaphors = $grammar['metaphors'] ?? FRAMEFLUX_METAPHORS;
        $allowedMaterials = $grammar['materials'] ?? FRAMEFLUX_MATERIALS;
        $semantic['grammarFamily'] = $family;
        $semantic['visualMetaphor'] = coerceToList(
            aliasedEnum((string) ($previous['visualMetaphor'] ?? $semantic['visualMetaphor']), FRAMEFLUX_METAPHOR_ALIASES, FRAMEFLUX_METAPHORS, $semantic['visualMetaphor']),
            $allowedMetaphors,
            $semantic['visualMetaphor']
        );
        $semantic['narrativeAnchor'] = $semantic['visualMetaphor'];
        $semantic['material'] = coerceToList(
            enumValue((string) ($previous['material'] ?? $semantic['material']), FRAMEFLUX_MATERIALS, $semantic['material']),
            $allowedMaterials,
            $semantic['material']
        );
        $semantic['emotionalCore'] = enumValue((string) ($previous['emotionalCore'] ?? $semantic['emotionalCore']), FRAMEFLUX_EMOTIONS, $semantic['emotionalCore']);
        $semantic['narrativeCore'] = enumValue((string) ($previous['narrativeCore'] ?? $semantic['narrativeCore']), FRAMEFLUX_NARRATIVES, $semantic['narrativeCore']);
        $semantic['humanElements'] = enumValue((string) ($previous['humanElements'] ?? $semantic['humanElements'] ?? 'none'), FRAMEFLUX_HUMAN_ELEMENTS, $semantic['humanElements'] ?? 'none');
        $semantic['groundTone'] = $grammar['groundTone'] ?? $semantic['groundTone'];
        $semantic['spatial'] = coerceToList(
            FRAMEFLUX_SPATIAL[($seed + 1) % count(FRAMEFLUX_SPATIAL)],
            $grammar['spatialFeelings'] ?? FRAMEFLUX_SPATIAL,
            $semantic['spatial']
        );
        $semantic['compositionGrammar'] = pickFromGrammar($grammar['compositionGrammars'] ?? COMPOSITION_GRAMMARS, $seed + 17);
        $semantic['compositionMode'] = enumValue((string) ($previous['compositionMode'] ?? $semantic['compositionMode'] ?? ''), COMPOSITION_MODES, $semantic['compositionMode'] ?? compositionModeFor($family, $semantic['compositionGrammar'], $seed));
        $semantic['artFamily'] = enumValue((string) ($previous['artFamily'] ?? $semantic['artFamily'] ?? ''), ART_FAMILIES, $semantic['artFamily'] ?? artFamilyFor($family, $semantic['visualMetaphor'], $semantic['proceduralFamily'] ?? 'organic', $seed));
        $semantic['narrativeEnergy'] = (float) ($previous['narrativeEnergy'] ?? $semantic['narrativeEnergy']);
    }

    if ($mode === 'reimagine' && is_array($previous)) {
        $family = (string) ($previous['grammarFamily'] ?? $semantic['grammarFamily']);
        $grammar = genreGrammar($family);
        $allowedMetaphors = $grammar['metaphors'] ?? FRAMEFLUX_METAPHORS;
        $allowedMaterials = $grammar['materials'] ?? FRAMEFLUX_MATERIALS;
        $semantic['grammarFamily'] = $family;
        $current = array_search($semantic['visualMetaphor'], $allowedMetaphors, true);
        $semantic['visualMetaphor'] = $allowedMetaphors[(($current === false ? 0 : $current) + 1 + ($seed % max(1, count($allowedMetaphors) - 1))) % count($allowedMetaphors)];
        $semantic['narrativeAnchor'] = $semantic['visualMetaphor'];
        $semantic['material'] = pickFromGrammar($allowedMaterials, $seed + 4);
        $semantic['spatial'] = pickFromGrammar($grammar['spatialFeelings'] ?? FRAMEFLUX_SPATIAL, $seed + 2);
        $semantic['compositionGrammar'] = pickFromGrammar($grammar['compositionGrammars'] ?? COMPOSITION_GRAMMARS, $seed + 8);
        $semantic['compositionMode'] = compositionModeFor($family, $semantic['compositionGrammar'], $seed + 13);
        $semantic['artFamily'] = artFamilyFor($family, $semantic['visualMetaphor'], $semantic['proceduralFamily'] ?? 'organic', $seed + 19);
        $semantic['groundTone'] = $grammar['groundTone'] ?? $semantic['groundTone'];
        $semantic['lightingStyle'] = pickFromGrammar($grammar['lightingStyles'] ?? FRAMEFLUX_LIGHTING, $seed + 41);
        $semantic['lineSemantics'] = pickFromGrammar($grammar['lineSemantics'] ?? FRAMEFLUX_LINE_SEMANTICS, $seed + 9);
        $semantic['particleSemantics'] = pickFromGrammar($grammar['particleSemantics'] ?? FRAMEFLUX_PARTICLE_SEMANTICS, $seed + 5);
        $semantic['humanElements'] = pickFromGrammar(($grammar['humanElements'] ?? []) !== [] ? $grammar['humanElements'] : ['none'], $seed + 37) ?: 'none';
    }

    $grammar = genreGrammar((string) $semantic['grammarFamily']);
    $preset = paletteFromGrammar($grammar, $seed);
    $ground = (string) ($semantic['groundTone'] ?? $grammar['groundTone'] ?? 'mid');
    $bg = $preset['bg'];
    $text = $preset['ink'];
    $accent = $preset['acc'];
    $highlight = $preset['hi'];
    $secondary = $preset['mute'];
    $primary = $preset['acc'];

    [$pattern, $secondPat] = patternForSemantic($semantic, $seed);
    $layout = layoutForSemantic($semantic, $seed);
    $titleStyle = match ($semantic['emotionalCore']) {
        'intimacy', 'nostalgia', 'grief', 'longing' => 'elegant',
        'urgency', 'paranoia', 'control' => 'condensed',
        'playfulness' => 'geometric',
        'wonder', 'triumph' => 'editorial',
        default => 'bold',
    };

    $lighting = aliasedEnum(
        (string) ($semantic['lightingStyle'] ?? ''),
        FRAMEFLUX_LIGHTING_ALIASES,
        FRAMEFLUX_LIGHTING,
        pickFromGrammar($grammar['lightingStyles'] ?? FRAMEFLUX_LIGHTING, $seed)
    );

    $camera = match ($semantic['spatial']) {
        'claustrophobic', 'compressed' => 'close',
        'expansive' => 'wide',
        'spiralling' => 'dutch',
        'isolated' => 'static',
        default => FRAMEFLUX_CAMERA[intdiv($seed, 5) % count(FRAMEFLUX_CAMERA)],
    };

    $energy = (float) ($semantic['narrativeEnergy'] ?? 0.5);
    $density = round(clamp(0.22 + $energy * 0.52 + (($seed % 9) / 100), 0.2, 0.85), 2);
    $shadow = $ground === 'light' ? 0.22 + ($seed % 12) / 100 : ($ground === 'mid' ? 0.38 + ($seed % 16) / 100 : 0.55 + ($seed % 22) / 100);
    $contrast = $lighting === 'high-key' ? 0.58 : ($lighting === 'chiaroscuro' || $lighting === 'hard-sun' ? 0.86 : 0.72);

    $comp = (string) ($semantic['compositionGrammar'] ?? 'central');
    $focalX = match ($comp) {
        'asymmetric', 'editorial', 'diagonal' => 0.34 + ($seed % 12) / 100,
        'crowded' => 0.46 + ($seed % 18) / 100,
        default => 0.44 + ($seed % 14) / 100,
    };
    $focalY = match ($comp) {
        'expansive', 'organic' => 0.4 + (($seed >> 3) % 10) / 100,
        'crowded', 'diagonal' => 0.46 + (($seed >> 3) % 16) / 100,
        'editorial' => 0.38 + (($seed >> 3) % 12) / 100,
        default => 0.4 + (($seed >> 3) % 14) / 100,
    };

    $typeDirection = inferTypographyDirection($semantic, $genre, $seed);

    return [
        'palette' => [
            'background' => $bg,
            'primary' => $primary,
            'secondary' => $secondary,
            'accent' => $accent,
            'text' => $text,
            'highlight' => $highlight,
            'neutral' => $text,
        ],
        'pattern' => $pattern,
        'secondaryPattern' => $secondPat,
        'layout' => $layout,
        'titleTypographyDirection' => $typeDirection['title'],
        'quoteTypographyDirection' => $typeDirection['quote'],
        'mood' => $semantic['emotionalCore'] . ' · ' . $semantic['narrativeCore'],
        'quote' => fallbackQuote($title, $genre, $pitch, $seed),
        'density' => $density,
        'contrast' => $contrast,
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
        'grammarFamily' => $semantic['grammarFamily'],
        'narrativeEnergy' => $energy,
        'compositionGrammar' => $comp,
        'compositionMode' => $semantic['compositionMode'] ?? compositionModeFor((string) $semantic['grammarFamily'], $comp, $seed),
        'artFamily' => $semantic['artFamily'] ?? artFamilyFor((string) $semantic['grammarFamily'], (string) $semantic['visualMetaphor'], (string) ($semantic['proceduralFamily'] ?? 'organic'), $seed),
        'proceduralFamily' => $semantic['proceduralFamily'] ?? 'organic',
        'proceduralSecondaryFamily' => $semantic['proceduralSecondaryFamily'] ?? 'tactile',
        'proceduralAccent' => $semantic['proceduralAccent'] ?? 'particle',
        'humanElements' => $semantic['humanElements'] ?? 'none',
        'groundTone' => $ground,
        'lightDirection' => 0.15 + ($seed % 70) / 100,
        'shadowDensity' => $shadow,
        'cinematic' => [
            'subject' => str_replace('-', ' ', $semantic['narrativeAnchor']),
            'environment' => $semantic['material'] . ' space under ' . $lighting . ' light',
            'lighting' => $lighting,
            'atmosphere' => $semantic['texture'] . ' ' . $semantic['material'],
            'camera' => $camera,
            'tension' => round(clamp(0.2 + $energy * 0.55, 0, 1), 2),
            'intensity' => round(clamp(0.3 + $energy * 0.5, 0.2, 1), 2),
        ],
        'focalX' => $focalX,
        'focalY' => $focalY,
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

    $inferred = inferSemanticProfile(
        $title,
        $genre,
        $pitch,
        abs(crc32($title . '|' . $genre . '|' . $pitch))
    );

    $family = enumValue(
        (string) ($semanticIn['grammarFamily'] ?? $params['grammarFamily'] ?? $inferred['grammarFamily'] ?? ''),
        GENRE_FAMILIES,
        $inferred['grammarFamily']
    );
    $grammar = genreGrammar($family);

    $lighting = aliasedEnum(
        (string) ($cinematicIn['lighting'] ?? $lightingIn['type'] ?? $params['lighting'] ?? ''),
        FRAMEFLUX_LIGHTING_ALIASES,
        FRAMEFLUX_LIGHTING,
        (string) ($inferred['lightingStyle'] ?? 'practical')
    );
    $lighting = coerceToList($lighting, $grammar['lightingStyles'] ?? FRAMEFLUX_LIGHTING, (string) ($inferred['lightingStyle'] ?? 'practical'));
    $camera = enumValue(
        (string) ($cinematicIn['camera'] ?? ''),
        FRAMEFLUX_CAMERA,
        'wide'
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
    $visualMetaphor = aliasedEnum(
        (string) ($semanticIn['visualMetaphor'] ?? $params['visualMetaphor'] ?? ''),
        FRAMEFLUX_METAPHOR_ALIASES,
        FRAMEFLUX_METAPHORS,
        $inferred['visualMetaphor']
    );
    if (!isTechFamily($family) && in_array($visualMetaphor, ['orbital-system', 'signal'], true)) {
        $visualMetaphor = $inferred['visualMetaphor'];
        $warnings[] = 'tech metaphor coerced';
    }
    $narrativeAnchor = aliasedEnum(
        (string) ($semanticIn['narrativeAnchor'] ?? $params['narrativeAnchor'] ?? $visualMetaphor),
        FRAMEFLUX_METAPHOR_ALIASES,
        FRAMEFLUX_METAPHORS,
        $visualMetaphor
    );
    if (!isTechFamily($family) && in_array($narrativeAnchor, ['orbital-system', 'signal'], true)) {
        $narrativeAnchor = $visualMetaphor;
    }
    $material = enumValue(
        (string) ($semanticIn['material'] ?? $params['material'] ?? ''),
        FRAMEFLUX_MATERIALS,
        $inferred['material']
    );
    $lightHuman = in_array($family, ['comedy', 'romance', 'contemporary', 'coming-of-age', 'family', 'animation', 'musical'], true);
    if ($lightHuman && !in_array($material, $grammar['materials'] ?? FRAMEFLUX_MATERIALS, true)
        && in_array($material, ['metal', 'plastic', 'concrete'], true)) {
        $material = $inferred['material'];
        $warnings[] = 'tech material coerced';
    }
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
    $particleSemantics = aliasedEnum(
        (string) ($semanticIn['particleSemantics'] ?? $params['particleSemantics'] ?? $proceduralIn['particleSemantics'] ?? ''),
        FRAMEFLUX_PARTICLE_ALIASES,
        FRAMEFLUX_PARTICLE_SEMANTICS,
        $inferred['particleSemantics']
    );
    $lineSemantics = aliasedEnum(
        (string) ($semanticIn['lineSemantics'] ?? $params['lineSemantics'] ?? $proceduralIn['lineSemantics'] ?? ''),
        FRAMEFLUX_LINE_ALIASES,
        FRAMEFLUX_LINE_SEMANTICS,
        $inferred['lineSemantics']
    );
    if (!isTechFamily($family) && in_array($lineSemantics, FRAMEFLUX_TECH_LINES, true)) {
        $lineSemantics = $inferred['lineSemantics'];
        $warnings[] = 'tech line language coerced';
    }

    $compositionGrammar = enumValue(
        (string) ($compositionIn['grammar'] ?? $params['compositionGrammar'] ?? $inferred['compositionGrammar'] ?? ''),
        COMPOSITION_GRAMMARS,
        $inferred['compositionGrammar'] ?? 'central'
    );
    $compositionMode = enumValue(
        (string) ($compositionIn['mode'] ?? $params['compositionMode'] ?? $inferred['compositionMode'] ?? ''),
        COMPOSITION_MODES,
        $inferred['compositionMode'] ?? compositionModeFor($family, $compositionGrammar, abs(crc32($title)))
    );
    $artFamily = enumValue(
        (string) ($semanticIn['artFamily'] ?? $params['artFamily'] ?? $inferred['artFamily'] ?? ''),
        ART_FAMILIES,
        $inferred['artFamily'] ?? artFamilyFor($family, (string) ($inferred['visualMetaphor'] ?? ''), $inferred['proceduralFamily'] ?? 'organic', abs(crc32($title . '|art')))
    );
    $printIn = is_array($params['print'] ?? null) ? $params['print'] : [];
    $printDefaults = printProfileFor($family, (float) ($params['narrativeEnergy'] ?? $inferred['narrativeEnergy'] ?? 0.5));
    $print = [
        'registration' => round(clamp((float) ($printIn['registration'] ?? $printDefaults['registration']), 0.05, 0.8), 2),
        'halftone' => round(clamp((float) ($printIn['halftone'] ?? $printDefaults['halftone']), 0.05, 0.8), 2),
        'scanlines' => round(clamp((float) ($printIn['scanlines'] ?? $printDefaults['scanlines']), 0.0, 0.6), 2),
        'grain' => round(clamp((float) ($printIn['grain'] ?? $printDefaults['grain']), 0.15, 0.9), 2),
    ];
    $negativeSpace = round(clamp((float) ($compositionIn['negativeSpace'] ?? $params['negativeSpace'] ?? $printDefaults['negativeSpace']), 0.15, 0.85), 2);
    $proceduralFamily = enumValue(
        (string) ($proceduralIn['family'] ?? $params['proceduralFamily'] ?? $inferred['proceduralFamily'] ?? ''),
        PROCEDURAL_FAMILIES,
        $inferred['proceduralFamily'] ?? 'organic'
    );
    $proceduralSecondaryFamily = enumValue(
        (string) ($proceduralIn['secondaryFamily'] ?? $params['proceduralSecondaryFamily'] ?? $inferred['proceduralSecondaryFamily'] ?? ''),
        PROCEDURAL_FAMILIES,
        $inferred['proceduralSecondaryFamily'] ?? 'tactile'
    );
    $proceduralAccent = enumValue(
        (string) ($proceduralIn['accent'] ?? $params['proceduralAccent'] ?? $inferred['proceduralAccent'] ?? ''),
        PROCEDURAL_FAMILIES,
        $inferred['proceduralAccent'] ?? 'particle'
    );
    $humanElements = enumValue(
        (string) ($semanticIn['humanElements'] ?? $params['humanElements'] ?? $inferred['humanElements'] ?? ''),
        FRAMEFLUX_HUMAN_ELEMENTS,
        $inferred['humanElements'] ?? 'none'
    );
    $groundTone = enumValue(
        (string) ($semanticIn['groundTone'] ?? $params['groundTone'] ?? $inferred['groundTone'] ?? 'mid'),
        ['light', 'mid', 'dark'],
        $inferred['groundTone'] ?? 'mid'
    );
    $narrativeEnergy = round(clamp((float) ($semanticIn['narrativeEnergy'] ?? $params['narrativeEnergy'] ?? $inferred['narrativeEnergy'] ?? 0.5), 0.05, 0.98), 2);

    if (!isTechFamily($family) && in_array($primary, FRAMEFLUX_TECH_PATTERNS, true)) {
        $primary = coerceToList('flow', $grammar['patterns'] ?? FRAMEFLUX_PATTERNS, 'flow');
        $warnings[] = 'tech pattern coerced';
    }
    if (!isTechFamily($family) && in_array($secondary, FRAMEFLUX_TECH_PATTERNS, true)) {
        $secondary = 'particles';
    }
    if ($secondary === $primary) {
        $secondary = nextPattern($primary);
        if ($secondary === $primary || (!isTechFamily($family) && in_array($secondary, FRAMEFLUX_TECH_PATTERNS, true))) {
            $secondary = $primary === 'flow' ? 'particles' : 'flow';
        }
    }
    if ($layout === 'frame-inset' && !isTechFamily($family)) {
        $layout = layoutForComposition($compositionGrammar, abs(crc32($title)), $family);
        $warnings[] = 'frame-inset reserved for tech families';
    }
    if ($family === 'comedy' && $layout === 'split-editorial') {
        $layout = layoutForComposition('asymmetric', abs(crc32($title)), $family);
        $warnings[] = 'comedy avoids split-editorial title column';
    }

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
    $background = sanitizeHex((string) ($paletteIn['background'] ?? ''), '#111111');
    $primaryColor = sanitizeHex((string) ($paletteIn['primary'] ?? ''), '#884422');
    $secondaryColor = sanitizeHex((string) ($paletteIn['secondary'] ?? ''), '#336688');
    $accentColor = sanitizeHex((string) ($paletteIn['accent'] ?? ''), '#e8c36a');
    $textColor = sanitizeHex((string) ($paletteIn['text'] ?? ''), '#f5f0e6');

    $suppliedBg = sanitizeHex((string) ($paletteIn['background'] ?? ''), '');
    if ($suppliedBg !== '') {
        $lum = hexLuminance($suppliedBg);
        if (($groundTone === 'light' && $lum < 0.38) || ($groundTone === 'dark' && $lum > 0.55)) {
            $preset = paletteFromGrammar($grammar, abs(crc32($title . '|' . $family)));
            $background = $preset['bg'];
            $primaryColor = $preset['acc'];
            $secondaryColor = $preset['mute'];
            $accentColor = $preset['acc'];
            $textColor = $preset['ink'];
            $highlight = $preset['hi'];
            $neutral = $preset['ink'];
            $warnings[] = 'palette coerced to genre ground';
        }
    }

    if ($groundTone === 'light') {
        $shadowDensity = min($shadowDensity, 0.42);
    }

    $inferredType = inferTypographyDirection(
        [
            'emotionalCore' => $emotionalCore,
            'narrativeCore' => $narrativeCore,
            'material' => $material,
            'texture' => $texture,
            'spatial' => $spatial,
            'grammarFamily' => $family,
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
        'schemaVersion' => '1.3',
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
            'grammarFamily' => $family,
            'narrativeEnergy' => $narrativeEnergy,
            'humanElements' => $humanElements,
            'groundTone' => $groundTone,
            'artFamily' => $artFamily,
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
            'background' => $background,
            'primary' => $primaryColor,
            'secondary' => $secondaryColor,
            'accent' => $accentColor,
            'text' => $textColor,
            'highlight' => $highlight,
            'neutral' => $neutral,
        ],
        'composition' => [
            'layout' => $layout,
            'grammar' => $compositionGrammar,
            'mode' => $compositionMode,
            'focalX' => $focalX,
            'focalY' => $focalY,
            'anchorScale' => $anchorScale,
            'negativeSpace' => $negativeSpace,
        ],
        'procedural' => [
            'primaryPattern' => $primary,
            'secondaryPattern' => $secondary,
            'family' => $proceduralFamily,
            'secondaryFamily' => $proceduralSecondaryFamily,
            'accent' => $proceduralAccent,
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
        'print' => $print,
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
  "emotionalCore": "paranoia" | "wonder" | "grief" | "isolation" | "urgency" | "nostalgia" | "dread" | "intimacy" | "triumph" | "unease" | "longing" | "playfulness" | "hope",
  "narrativeCore": "escape" | "investigation" | "forbidden-love" | "survival" | "identity" | "betrayal" | "discovery" | "control" | "family" | "transformation" | "obsession" | "memory",
  "visualMetaphor": "fractured-glass" | "eclipse" | "locked-mechanism" | "decaying-photograph" | "tangled-roots" | "maze" | "burning-document" | "distorted-reflection" | "clock-mechanism" | "biological-cell" | "architectural-ruin" | "orbital-system" | "keyhole" | "map-fold" | "signal" | "silhouette-threshold" | "chaotic-key" | "chandelier-cluster" | "tangled-cords" | "coastal-compass" | "handwritten-letter" | "weathered-door" | "correspondence-clock" | "railway-route" | "paired-objects" | "postcard" | "compass-rose",
  "narrativeAnchor": "same controlled set as visualMetaphor — the dominant object the poster is about",
  "material": "glass" | "metal" | "paper" | "concrete" | "fabric" | "film-stock" | "smoke" | "water" | "dust" | "wood" | "rust" | "ink" | "stone" | "plastic" | "foil" | "cardstock" | "linen" | "leather" | "brass",
  "texture": "distressed" | "smooth" | "grainy" | "scratched" | "weathered" | "glossy" | "dusty" | "corroded" | "fibrous" | "translucent" | "photographic",
  "spatial": "compressed" | "fragmented" | "expanding" | "collapsing" | "spiralling" | "rising" | "drifting" | "converging" | "isolated" | "claustrophobic" | "expansive",
  "particleSemantics": "dust" | "ash" | "stars" | "rain" | "sparks" | "pollen" | "debris" | "grain" | "confetti" | "salt" | "sand" | "ember",
  "lineSemantics": "cracks" | "roots" | "wiring" | "threads" | "veins" | "roads" | "circuitry" | "plans" | "cords" | "ribbons" | "waves" | "coastline" | "handwriting" | "horizon" | "contour" | "trails" | "railway",
  "grammarFamily": "comedy" | "romance" | "adventure" | "contemporary" | "drama" | "thriller" | "scifi" | "horror" | "fantasy" | "mystery" | "historical" | "coming-of-age" | "documentary" | "musical" | "animation" | "family",
  "narrativeEnergy": number 0.1-0.95,
  "compositionGrammar": "asymmetric" | "central" | "editorial" | "diagonal" | "layered" | "expansive" | "minimal" | "crowded" | "organic",
  "compositionMode": "central-focus" | "editorial" | "split-field" | "framed-object" | "type-dominant" | "edge-flow" | "diagonal" | "quiet-minimal",
  "artFamily": "angular" | "organic" | "particles" | "ordered-grid" | "radial" | "topographic" | "pattern",
  "print": { "registration": number 0-1, "halftone": number 0-1, "scanlines": number 0-1, "grain": number 0-1 },
  "proceduralFamily": "organic" | "geometric" | "tactile" | "chaotic" | "atmospheric" | "editorial" | "topographic" | "material" | "linear" | "particle",
  "humanElements": "none" | "silhouette" | "hands" | "letter" | "tickets" | "cups" | "paired-objects" | "signage" | "map",
  "groundTone": "light" | "mid" | "dark",
  "lightDirection": number 0-1,
  "shadowDensity": number 0.2-0.95,
  "anchorScale": number 0.4-1.1,
  "materialEmphasis": number 0.3-1,
  "cinematic": {
    "subject": "who or what occupies the frame, no readable text",
    "environment": "place and time of day",
    "lighting": "chiaroscuro" | "neon" | "overcast" | "golden-hour" | "moonlit" | "practical" | "harsh" | "rim" | "backlit" | "high-key" | "theatrical" | "coastal-haze" | "bloom" | "hard-sun" | "shaft" | "domestic-warm" | "window-light",
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
You are translating a SPECIFIC film concept into Visual DNA. Story first, then emotion, then genre grammar, then metaphor, then material / colour / light / type.

BAN THE GENERIC CYBER DEFAULT. Circuit traces, neon grids, blue/purple tech glow, and dark geometric voids are ONLY for cyber thrillers, hacker dramas, AI stories, dystopian sci-fi, and technological horror. Comedy, romance, adventure, contemporary, drama, and family stories must live in a physical material world.

LESS BUT BETTER:
- Choose ONE dominant visualMetaphor / narrativeAnchor that contains the emotional conflict.
- Choose ONE primary material that the whole poster inhabits.
- secondaryPattern must support the metaphor, not compete with it.
- density follows narrativeEnergy (contemplative ~0.25, chaotic ~0.75).

Metaphor meanings:
- chaotic-key / tangled-cords / chandelier-cluster = comic luxury, access, excess
- coastal-compass / handwritten-letter / weathered-door = memory, distance, coastal intimacy
- correspondence-clock / railway-route / postcard / paired-objects = time, travel, human connection
- compass-rose / map-fold = exploration, borders
- fractured-glass = identity, violence, fragile truth
- eclipse = omen, concealment, cosmic scale
- locked-mechanism / keyhole = secrets, heists, denied access
- decaying-photograph = memory, archive, grief
- tangled-roots = family, origin, entanglement
- maze = confusion, bureaucracy
- burning-document = erased evidence
- distorted-reflection = doubles, impostors
- clock-mechanism = time pressure (mechanical, not correspondence)
- biological-cell = body, contagion
- architectural-ruin = collapse of systems/places
- orbital-system = systems, surveillance, space (sci-fi only unless the story is cosmic)
- signal = frequencies, contact (tech stories)
- silhouette-threshold = arrival, departure, liminal figures

Colour must communicate emotion, not decoration. Light-ground genres (comedy, romance, contemporary) use cream / paper / coastal grounds — do not automatically darken them.
Lighting is emotional: high-key for comedy, coastal-haze for romance, chiaroscuro for adventure, domestic-warm for contemporary.
particleSemantics and lineSemantics must match the metaphor (confetti/cords for comedy, salt/waves for coast, railway/handwriting for letters).
Invent a short original quote. Do not copy the pitch. Max 12 words.
Cinematic subject describes the still, never poster type.

TYPOGRAPHY IS ART DIRECTION, NOT A TEMPLATE:
- Genre is NEVER printed on the poster. Do not request a genre label, badge, tag, or single-letter mark.
  Genre only informs colour, material, lighting, letterforms, and procedural choices.
- titleTypographyDirection must be derived from the story: weight from how loud it is, case from its
  emotional register, letterforms from the material language, tracking from how much air the space has,
  structure from material behaviour (glass wants outline, corroded metal wants textured, smoke wants gradient,
  fragmented space wants fragmented type). Comedy may tilt and split; romance stays literary; contemporary is editorial.
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
    if (!isTechFamily((string) ($semantic['grammarFamily'] ?? '')) && in_array($proc['primaryPattern'] ?? '', ['grid', 'mesh'], true)) {
        $notes[] = 'tech-pattern-on-human-genre';
    }
    $bg = (string) (($dna['palette']['background'] ?? ''));
    if (($semantic['groundTone'] ?? '') === 'light' && $bg !== '' && hexLuminance($bg) < 0.35) {
        $notes[] = 'light-genre-dark-ground';
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
