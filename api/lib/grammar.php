<?php
declare(strict_types=1);

/**
 * Genre visual grammars — story-first visual language.
 *
 * Genre is a grammar, not a colour overlay. Each family owns material,
 * colour ground, lighting, iconography, composition, and procedural behaviour.
 * Cyber / circuit / dark-grid language is reserved for thriller, scifi, and
 * horror. It is never the default for comedy, romance, adventure, or
 * contemporary stories.
 */

const GENRE_FAMILIES = [
    'comedy',
    'romance',
    'adventure',
    'contemporary',
    'drama',
    'thriller',
    'scifi',
    'horror',
    'fantasy',
    'mystery',
    'historical',
    'coming-of-age',
    'documentary',
    'musical',
    'animation',
    'family',
];

const PROCEDURAL_FAMILIES = [
    'organic',
    'geometric',
    'tactile',
    'chaotic',
    'atmospheric',
    'editorial',
    'topographic',
    'material',
    'linear',
    'particle',
];

const COMPOSITION_GRAMMARS = [
    'asymmetric',
    'central',
    'editorial',
    'diagonal',
    'layered',
    'expansive',
    'minimal',
    'crowded',
    'organic',
];

/**
 * Map a user-facing genre label onto a visual-grammar family.
 */
function inferGenreFamily(string $genre, string $story = '', string $tone = ''): string
{
    $g = strtolower(trim($genre));
    $blob = strtolower($genre . ' ' . $story . ' ' . $tone);

    if (str_contains($g, 'comedy') || str_contains($g, 'satire') || str_contains($g, 'farce')
        || str_contains($blob, 'workplace comedy') || str_contains($blob, 'screwball')
        || str_contains($blob, 'rom-com') || str_contains($blob, 'romcom')) {
        if (str_contains($blob, 'rom-com') || str_contains($blob, 'romcom') || str_contains($blob, 'romantic comedy')) {
            return 'comedy';
        }
        return 'comedy';
    }
    if (str_contains($g, 'sci') || str_contains($g, 'cyber') || str_contains($g, 'dystop')
        || str_contains($g, 'futur')) {
        return 'scifi';
    }
    if (str_contains($g, 'horror') || str_contains($g, 'gothic')) {
        return 'horror';
    }
    if (str_contains($g, 'thriller') || str_contains($g, 'heist') || str_contains($g, 'crime')
        || str_contains($g, 'noir') || str_contains($g, 'spy')) {
        return 'thriller';
    }
    if (str_contains($g, 'adventure') || str_contains($g, 'action') || str_contains($g, 'epic')
        || str_contains($g, 'expedition') || str_contains($g, 'quest')) {
        return 'adventure';
    }
    if (str_contains($g, 'fantasy') || str_contains($g, 'mythic') || str_contains($g, 'fairytale')) {
        return 'fantasy';
    }
    if (str_contains($g, 'mystery')) {
        return 'mystery';
    }
    if (str_contains($g, 'historical') || str_contains($g, 'period') || str_contains($g, 'war ')) {
        return 'historical';
    }
    if (str_contains($g, 'coming') || str_contains($g, 'coming-of-age') || str_contains($g, 'teen')) {
        return 'coming-of-age';
    }
    if (str_contains($g, 'document')) {
        return 'documentary';
    }
    if (str_contains($g, 'musical')) {
        return 'musical';
    }
    if (str_contains($g, 'animat')) {
        return 'animation';
    }
    if (str_contains($g, 'family')) {
        return 'family';
    }
    if (str_contains($g, 'romance') || str_contains($g, 'romantic')) {
        if (str_contains($g, 'contemporary')) {
            return 'contemporary';
        }
        return 'romance';
    }
    if (str_contains($g, 'contemporary') || str_contains($g, 'drama') || str_contains($g, 'literary')
        || str_contains($g, 'relationship') || str_contains($g, 'slice')) {
        if (str_contains($blob, 'romance') || str_contains($blob, 'love') || str_contains($blob, 'letter')) {
            return 'contemporary';
        }
        return 'drama';
    }

    // Story keywords when the genre label is vague ("Drama", "Other", empty).
    if (preg_match('/\b(mansion|landlord|sitcom|screwball|farce|hilarious|absurd)\b/', $blob)) {
        return 'comedy';
    }
    if (preg_match('/\b(cape|coast|solitude|summer romance|sea glass|lighthouse)\b/', $blob)) {
        return 'romance';
    }
    if (preg_match('/\b(letter|station|timetable|correspondence|commute)\b/', $blob)) {
        return 'contemporary';
    }
    if (preg_match('/\b(expedition|map|ruin|quest|jungle|desert)\b/', $blob)) {
        return 'adventure';
    }
    if (preg_match('/\b(circuit|android|ai |hacker|cyber)\b/', $blob)) {
        return 'scifi';
    }

    return 'drama';
}

/**
 * Visual grammar for a family. Additional families can be added here without
 * rewriting the DNA or renderer pipelines.
 *
 * @return array<string, mixed>
 */
function genreGrammar(string $family): array
{
    $grammars = [
        'comedy' => [
            'family' => 'comedy',
            'groundTone' => 'light',
            'materials' => ['paper', 'glass', 'foil', 'cardstock', 'linen'],
            'textures' => ['glossy', 'scratched', 'distressed', 'smooth'],
            'metaphors' => ['chaotic-key', 'chandelier-cluster', 'tangled-cords', 'keyhole'],
            'lightingStyles' => ['high-key', 'theatrical', 'practical'],
            'compositionGrammars' => ['asymmetric', 'diagonal', 'crowded'],
            'proceduralPrimary' => ['chaotic', 'linear'],
            'proceduralSecondary' => ['geometric', 'material'],
            'proceduralAccent' => ['particle'],
            'lineSemantics' => ['cords', 'ribbons', 'threads'],
            'particleSemantics' => ['confetti', 'debris', 'dust'],
            'patterns' => ['flow', 'particles'],
            'spatialFeelings' => ['fragmented', 'claustrophobic', 'expanding'],
            'humanElements' => ['paired-objects', 'signage'],
            'titlePlacementBias' => ['lower-third', 'upper-third'],
            'titleTreatmentBias' => ['solid', 'layered', 'fragmented'],
            'quoteStyleBias' => ['caption', 'handwritten'],
            'narrativeEnergyDefault' => 0.82,
            'palettePresets' => [
                ['bg' => '#F4EFE4', 'ink' => '#1A1410', 'acc' => '#E11D74', 'hi' => '#C6FF3D', 'mute' => '#2BB3B1'],
                ['bg' => '#FFF6D8', 'ink' => '#2A1208', 'acc' => '#FF5A1F', 'hi' => '#FFE14A', 'mute' => '#0F9B8E'],
                ['bg' => '#E8F7F4', 'ink' => '#1C1530', 'acc' => '#FF2D92', 'hi' => '#D4FF4A', 'mute' => '#1E88A8'],
            ],
        ],
        'romance' => [
            'family' => 'romance',
            'groundTone' => 'light',
            'materials' => ['paper', 'linen', 'wood', 'cardstock'],
            'textures' => ['fibrous', 'weathered', 'photographic', 'grainy'],
            'metaphors' => ['coastal-compass', 'handwritten-letter', 'weathered-door', 'locked-mechanism', 'silhouette-threshold', 'eclipse'],
            'lightingStyles' => ['coastal-haze', 'golden-hour', 'bloom', 'backlit'],
            'compositionGrammars' => ['organic', 'central', 'minimal', 'expansive'],
            'proceduralPrimary' => ['organic', 'atmospheric'],
            'proceduralSecondary' => ['tactile', 'material'],
            'proceduralAccent' => ['particle'],
            'lineSemantics' => ['waves', 'coastline', 'handwriting', 'horizon'],
            'particleSemantics' => ['salt', 'pollen', 'dust', 'ember'],
            'patterns' => ['flow', 'particles'],
            'spatialFeelings' => ['expansive', 'isolated', 'drifting'],
            'humanElements' => ['hands', 'letter', 'paired-objects', 'silhouette'],
            'titlePlacementBias' => ['lower-third', 'upper-third', 'centered'],
            'titleTreatmentBias' => ['solid', 'textured', 'gradient'],
            'quoteStyleBias' => ['editorial-italic', 'handwritten', 'cinematic-subtitle'],
            'narrativeEnergyDefault' => 0.32,
            'palettePresets' => [
                ['bg' => '#F3E6D4', 'ink' => '#3A2A24', 'acc' => '#E07A6A', 'hi' => '#9FD8C8', 'mute' => '#C9B8A4'],
                ['bg' => '#EDE4D8', 'ink' => '#2C241E', 'acc' => '#D9897A', 'hi' => '#B7D4C8', 'mute' => '#E8C9B0'],
                ['bg' => '#F6EFE6', 'ink' => '#403028', 'acc' => '#C97B84', 'hi' => '#A8C5D4', 'mute' => '#D8C4A8'],
            ],
        ],
        'adventure' => [
            'family' => 'adventure',
            'groundTone' => 'mid',
            'materials' => ['leather', 'brass', 'stone', 'paper', 'wood', 'metal'],
            'textures' => ['weathered', 'corroded', 'grainy', 'scratched'],
            'metaphors' => ['map-fold', 'compass-rose', 'weathered-door', 'locked-mechanism', 'tangled-roots'],
            'lightingStyles' => ['chiaroscuro', 'hard-sun', 'shaft', 'harsh'],
            'compositionGrammars' => ['expansive', 'diagonal', 'layered'],
            'proceduralPrimary' => ['topographic', 'material'],
            'proceduralSecondary' => ['organic', 'geometric'],
            'proceduralAccent' => ['particle'],
            'lineSemantics' => ['contour', 'trails', 'threads', 'horizon'],
            'particleSemantics' => ['sand', 'dust', 'ember', 'ash'],
            'patterns' => ['flow', 'particles'],
            'spatialFeelings' => ['expansive', 'rising', 'expanding'],
            'humanElements' => ['silhouette', 'map'],
            'titlePlacementBias' => ['lower-third', 'upper-third', 'split'],
            'titleTreatmentBias' => ['solid', 'textured', 'outline'],
            'quoteStyleBias' => ['cinematic-subtitle', 'caption'],
            'narrativeEnergyDefault' => 0.68,
            'palettePresets' => [
                ['bg' => '#2C2118', 'ink' => '#F1E4C8', 'acc' => '#C47A2C', 'hi' => '#D4B46A', 'mute' => '#4A5C3A'],
                ['bg' => '#1E2A24', 'ink' => '#E8DCC4', 'acc' => '#8B5A2B', 'hi' => '#C9A227', 'mute' => '#3D5A6C'],
                ['bg' => '#3A2A1C', 'ink' => '#F4E8D0', 'acc' => '#B85C38', 'hi' => '#E0C070', 'mute' => '#5C6B4A'],
            ],
        ],
        'contemporary' => [
            'family' => 'contemporary',
            'groundTone' => 'light',
            'materials' => ['cardstock', 'paper', 'linen'],
            'textures' => ['fibrous', 'weathered', 'photographic', 'grainy'],
            'metaphors' => ['correspondence-clock', 'handwritten-letter', 'railway-route', 'paired-objects', 'postcard', 'locked-mechanism'],
            'lightingStyles' => ['domestic-warm', 'window-light', 'practical'],
            'compositionGrammars' => ['editorial', 'layered', 'asymmetric', 'minimal'],
            'proceduralPrimary' => ['editorial', 'tactile'],
            'proceduralSecondary' => ['linear', 'material'],
            'proceduralAccent' => ['particle'],
            'lineSemantics' => ['railway', 'handwriting', 'horizon', 'threads'],
            'particleSemantics' => ['dust', 'pollen'],
            'patterns' => ['flow', 'particles'],
            'spatialFeelings' => ['isolated', 'fragmented', 'drifting'],
            'humanElements' => ['letter', 'tickets', 'cups', 'hands', 'paired-objects'],
            'titlePlacementBias' => ['upper-third', 'lower-third', 'split'],
            'titleTreatmentBias' => ['solid', 'textured'],
            'quoteStyleBias' => ['editorial-italic', 'typewriter', 'handwritten'],
            'narrativeEnergyDefault' => 0.42,
            'palettePresets' => [
                ['bg' => '#F0E6D6', 'ink' => '#241814', 'acc' => '#6A1228', 'hi' => '#C9A227', 'mute' => '#6E655C'],
                ['bg' => '#EDE4D4', 'ink' => '#1E1612', 'acc' => '#5C0E22', 'hi' => '#D4B46A', 'mute' => '#7A7168'],
                ['bg' => '#F3E9DA', 'ink' => '#2A1C18', 'acc' => '#7A1830', 'hi' => '#C4A056', 'mute' => '#8A7E72'],
            ],
        ],
        'drama' => [
            'family' => 'drama',
            'groundTone' => 'mid',
            'materials' => ['paper', 'wood', 'linen', 'cardstock'],
            'textures' => ['grainy', 'fibrous', 'weathered', 'photographic'],
            'metaphors' => ['silhouette-threshold', 'weathered-door', 'handwritten-letter', 'paired-objects', 'eclipse'],
            'lightingStyles' => ['practical', 'window-light', 'backlit'],
            'compositionGrammars' => ['editorial', 'layered', 'central', 'minimal'],
            'proceduralPrimary' => ['tactile', 'atmospheric'],
            'proceduralSecondary' => ['material', 'organic'],
            'proceduralAccent' => ['particle'],
            'lineSemantics' => ['horizon', 'threads', 'handwriting'],
            'particleSemantics' => ['dust', 'ash'],
            'patterns' => ['flow', 'particles'],
            'spatialFeelings' => ['isolated', 'fragmented', 'drifting'],
            'humanElements' => ['silhouette', 'hands', 'paired-objects'],
            'titlePlacementBias' => ['lower-third', 'upper-third', 'centered'],
            'titleTreatmentBias' => ['solid', 'textured', 'gradient'],
            'quoteStyleBias' => ['editorial-italic', 'cinematic-subtitle'],
            'narrativeEnergyDefault' => 0.45,
            'palettePresets' => [
                ['bg' => '#E4D6C4', 'ink' => '#2A221C', 'acc' => '#8B4A3A', 'hi' => '#D4B896', 'mute' => '#6A5C50'],
                ['bg' => '#1C1814', 'ink' => '#E8DCC8', 'acc' => '#C45C48', 'hi' => '#E0C8A0', 'mute' => '#5A5048'],
            ],
        ],
        'thriller' => [
            'family' => 'thriller',
            'groundTone' => 'dark',
            'materials' => ['metal', 'concrete', 'glass', 'paper'],
            'textures' => ['scratched', 'grainy', 'distressed', 'smooth'],
            'metaphors' => ['keyhole', 'locked-mechanism', 'signal', 'eclipse', 'clock-mechanism'],
            'lightingStyles' => ['chiaroscuro', 'harsh', 'rim', 'shaft'],
            'compositionGrammars' => ['central', 'diagonal', 'minimal'],
            'proceduralPrimary' => ['geometric', 'linear'],
            'proceduralSecondary' => ['particle'],
            'proceduralAccent' => ['material'],
            'lineSemantics' => ['circuitry', 'wiring', 'horizon'],
            'particleSemantics' => ['sparks', 'ash', 'dust'],
            'patterns' => ['grid', 'mesh', 'rings'],
            'spatialFeelings' => ['claustrophobic', 'compressed', 'isolated'],
            'humanElements' => ['silhouette'],
            'titlePlacementBias' => ['upper-third', 'lower-third', 'split'],
            'titleTreatmentBias' => ['solid', 'outline', 'layered'],
            'quoteStyleBias' => ['cinematic-subtitle', 'caption'],
            'narrativeEnergyDefault' => 0.72,
            'palettePresets' => [
                ['bg' => '#0B1016', 'ink' => '#E8EEF4', 'acc' => '#3D7EA6', 'hi' => '#C9A227', 'mute' => '#4A5560'],
                ['bg' => '#101214', 'ink' => '#F2EFE8', 'acc' => '#C45C4A', 'hi' => '#E8C36A', 'mute' => '#5A5048'],
            ],
        ],
        'scifi' => [
            'family' => 'scifi',
            'groundTone' => 'dark',
            'materials' => ['metal', 'glass', 'concrete'],
            'textures' => ['scratched', 'smooth', 'grainy'],
            'metaphors' => ['signal', 'eclipse', 'clock-mechanism', 'keyhole', 'orbital-system'],
            'lightingStyles' => ['rim', 'neon', 'harsh', 'bloom'],
            'compositionGrammars' => ['central', 'expansive', 'minimal'],
            'proceduralPrimary' => ['geometric', 'particle'],
            'proceduralSecondary' => ['linear'],
            'proceduralAccent' => ['atmospheric'],
            'lineSemantics' => ['circuitry', 'horizon', 'wiring'],
            'particleSemantics' => ['sparks', 'stars', 'dust'],
            'patterns' => ['grid', 'mesh', 'rings'],
            'spatialFeelings' => ['isolated', 'expansive', 'compressed'],
            'humanElements' => [],
            'titlePlacementBias' => ['upper-third', 'split', 'centered'],
            'titleTreatmentBias' => ['outline', 'layered', 'gradient'],
            'quoteStyleBias' => ['cinematic-subtitle', 'typewriter'],
            'narrativeEnergyDefault' => 0.7,
            'palettePresets' => [
                ['bg' => '#070B12', 'ink' => '#E4EEF8', 'acc' => '#3D8EA8', 'hi' => '#7EC8E3', 'mute' => '#3A4A58'],
                ['bg' => '#0C1018', 'ink' => '#F0F4F8', 'acc' => '#6B4C9A', 'hi' => '#C9A227', 'mute' => '#4A5568'],
            ],
        ],
        'horror' => [
            'family' => 'horror',
            'groundTone' => 'dark',
            'materials' => ['paper', 'wood', 'concrete', 'stone'],
            'textures' => ['weathered', 'distressed', 'scratched', 'fibrous'],
            'metaphors' => ['eclipse', 'weathered-door', 'locked-mechanism', 'silhouette-threshold', 'tangled-roots'],
            'lightingStyles' => ['chiaroscuro', 'shaft', 'harsh'],
            'compositionGrammars' => ['central', 'minimal', 'layered'],
            'proceduralPrimary' => ['atmospheric', 'organic'],
            'proceduralSecondary' => ['material'],
            'proceduralAccent' => ['particle'],
            'lineSemantics' => ['cracks', 'threads', 'horizon'],
            'particleSemantics' => ['ash', 'dust', 'ember'],
            'patterns' => ['flow', 'particles'],
            'spatialFeelings' => ['claustrophobic', 'collapsing', 'isolated'],
            'humanElements' => ['silhouette'],
            'titlePlacementBias' => ['lower-third', 'upper-third'],
            'titleTreatmentBias' => ['textured', 'solid', 'fragmented'],
            'quoteStyleBias' => ['cinematic-subtitle', 'typewriter'],
            'narrativeEnergyDefault' => 0.55,
            'palettePresets' => [
                ['bg' => '#0A0808', 'ink' => '#E8D8C8', 'acc' => '#8B1E1E', 'hi' => '#C45C38', 'mute' => '#3A3028'],
            ],
        ],
        'fantasy' => [
            'family' => 'fantasy',
            'groundTone' => 'mid',
            'materials' => ['paper', 'brass', 'wood', 'linen', 'stone'],
            'textures' => ['fibrous', 'corroded', 'photographic', 'grainy'],
            'metaphors' => ['compass-rose', 'locked-mechanism', 'tangled-roots', 'eclipse', 'weathered-door'],
            'lightingStyles' => ['golden-hour', 'bloom', 'shaft', 'theatrical'],
            'compositionGrammars' => ['expansive', 'organic', 'layered'],
            'proceduralPrimary' => ['organic', 'atmospheric'],
            'proceduralSecondary' => ['material', 'topographic'],
            'proceduralAccent' => ['particle'],
            'lineSemantics' => ['contour', 'threads', 'horizon'],
            'particleSemantics' => ['ember', 'pollen', 'dust'],
            'patterns' => ['flow', 'particles'],
            'spatialFeelings' => ['expansive', 'rising', 'fragmented'],
            'humanElements' => ['silhouette'],
            'titlePlacementBias' => ['lower-third', 'centered', 'upper-third'],
            'titleTreatmentBias' => ['solid', 'gradient', 'textured'],
            'quoteStyleBias' => ['editorial-italic', 'cinematic-subtitle'],
            'narrativeEnergyDefault' => 0.58,
            'palettePresets' => [
                ['bg' => '#1A1624', 'ink' => '#F0E4C8', 'acc' => '#7A4C9A', 'hi' => '#D4B46A', 'mute' => '#4A3C58'],
                ['bg' => '#E8DCC8', 'ink' => '#2A1C14', 'acc' => '#6B3A8A', 'hi' => '#C9A227', 'mute' => '#6A5C48'],
            ],
        ],
        'mystery' => [
            'family' => 'mystery',
            'groundTone' => 'dark',
            'materials' => ['paper', 'wood', 'leather', 'cardstock'],
            'textures' => ['weathered', 'fibrous', 'distressed', 'grainy'],
            'metaphors' => ['keyhole', 'locked-mechanism', 'handwritten-letter', 'clock-mechanism', 'silhouette-threshold'],
            'lightingStyles' => ['chiaroscuro', 'practical', 'shaft'],
            'compositionGrammars' => ['editorial', 'layered', 'central'],
            'proceduralPrimary' => ['tactile', 'atmospheric'],
            'proceduralSecondary' => ['linear', 'editorial'],
            'proceduralAccent' => ['particle'],
            'lineSemantics' => ['handwriting', 'threads', 'horizon'],
            'particleSemantics' => ['dust', 'ash'],
            'patterns' => ['flow', 'particles'],
            'spatialFeelings' => ['isolated', 'claustrophobic', 'fragmented'],
            'humanElements' => ['letter', 'silhouette'],
            'titlePlacementBias' => ['upper-third', 'lower-third', 'split'],
            'titleTreatmentBias' => ['solid', 'textured'],
            'quoteStyleBias' => ['typewriter', 'editorial-italic', 'caption'],
            'narrativeEnergyDefault' => 0.5,
            'palettePresets' => [
                ['bg' => '#16120E', 'ink' => '#E8DCC8', 'acc' => '#8B5A2B', 'hi' => '#C9A227', 'mute' => '#5A4C40'],
            ],
        ],
        'historical' => [
            'family' => 'historical',
            'groundTone' => 'mid',
            'materials' => ['paper', 'leather', 'wood', 'linen'],
            'textures' => ['fibrous', 'weathered', 'distressed', 'photographic'],
            'metaphors' => ['map-fold', 'handwritten-letter', 'weathered-door', 'compass-rose'],
            'lightingStyles' => ['golden-hour', 'practical', 'window-light'],
            'compositionGrammars' => ['editorial', 'layered', 'expansive'],
            'proceduralPrimary' => ['tactile', 'material'],
            'proceduralSecondary' => ['organic', 'topographic'],
            'proceduralAccent' => ['particle'],
            'lineSemantics' => ['horizon', 'contour', 'handwriting'],
            'particleSemantics' => ['dust', 'ash'],
            'patterns' => ['flow', 'particles'],
            'spatialFeelings' => ['expansive', 'fragmented', 'rising'],
            'humanElements' => ['letter', 'silhouette'],
            'titlePlacementBias' => ['upper-third', 'lower-third', 'split'],
            'titleTreatmentBias' => ['solid', 'textured'],
            'quoteStyleBias' => ['editorial-italic', 'typewriter'],
            'narrativeEnergyDefault' => 0.48,
            'palettePresets' => [
                ['bg' => '#CDB89A', 'ink' => '#2A1C12', 'acc' => '#8B3A2A', 'hi' => '#D4B46A', 'mute' => '#6A5C48'],
            ],
        ],
        'coming-of-age' => [
            'family' => 'coming-of-age',
            'groundTone' => 'light',
            'materials' => ['paper', 'cardstock', 'linen'],
            'textures' => ['fibrous', 'distressed', 'weathered', 'grainy'],
            'metaphors' => ['postcard', 'handwritten-letter', 'paired-objects', 'silhouette-threshold'],
            'lightingStyles' => ['golden-hour', 'window-light', 'bloom'],
            'compositionGrammars' => ['organic', 'editorial', 'asymmetric'],
            'proceduralPrimary' => ['organic', 'tactile'],
            'proceduralSecondary' => ['atmospheric'],
            'proceduralAccent' => ['particle'],
            'lineSemantics' => ['horizon', 'handwriting', 'threads'],
            'particleSemantics' => ['pollen', 'dust'],
            'patterns' => ['flow', 'particles'],
            'spatialFeelings' => ['isolated', 'expansive', 'drifting'],
            'humanElements' => ['hands', 'letter', 'paired-objects'],
            'titlePlacementBias' => ['lower-third', 'upper-third', 'split'],
            'titleTreatmentBias' => ['solid', 'textured'],
            'quoteStyleBias' => ['handwritten', 'editorial-italic'],
            'narrativeEnergyDefault' => 0.52,
            'palettePresets' => [
                ['bg' => '#F0E4D0', 'ink' => '#2C241C', 'acc' => '#E07A4A', 'hi' => '#F0C060', 'mute' => '#8A7A68'],
            ],
        ],
        'documentary' => [
            'family' => 'documentary',
            'groundTone' => 'mid',
            'materials' => ['paper', 'cardstock', 'concrete'],
            'textures' => ['grainy', 'fibrous', 'photographic'],
            'metaphors' => ['postcard', 'silhouette-threshold', 'map-fold'],
            'lightingStyles' => ['practical', 'window-light', 'harsh'],
            'compositionGrammars' => ['editorial', 'minimal', 'asymmetric'],
            'proceduralPrimary' => ['editorial', 'tactile'],
            'proceduralSecondary' => ['linear'],
            'proceduralAccent' => ['particle'],
            'lineSemantics' => ['horizon', 'threads'],
            'particleSemantics' => ['dust'],
            'patterns' => ['particles', 'flow'],
            'spatialFeelings' => ['isolated', 'fragmented'],
            'humanElements' => ['silhouette', 'paired-objects'],
            'titlePlacementBias' => ['upper-third', 'split', 'lower-third'],
            'titleTreatmentBias' => ['solid', 'outline'],
            'quoteStyleBias' => ['caption', 'typewriter'],
            'narrativeEnergyDefault' => 0.4,
            'palettePresets' => [
                ['bg' => '#D8D0C4', 'ink' => '#1C1814', 'acc' => '#4A6A8A', 'hi' => '#C9A227', 'mute' => '#6A645C'],
            ],
        ],
        'musical' => [
            'family' => 'musical',
            'groundTone' => 'light',
            'materials' => ['paper', 'foil', 'glass'],
            'textures' => ['grainy', 'photographic', 'glossy'],
            'metaphors' => ['chandelier-cluster', 'signal', 'silhouette-threshold'],
            'lightingStyles' => ['theatrical', 'high-key', 'bloom'],
            'compositionGrammars' => ['diagonal', 'crowded', 'asymmetric'],
            'proceduralPrimary' => ['chaotic', 'particle'],
            'proceduralSecondary' => ['geometric'],
            'proceduralAccent' => ['linear'],
            'lineSemantics' => ['ribbons', 'cords', 'horizon'],
            'particleSemantics' => ['confetti', 'sparks', 'ember'],
            'patterns' => ['particles', 'rings'],
            'spatialFeelings' => ['fragmented', 'expansive'],
            'humanElements' => ['silhouette', 'signage'],
            'titlePlacementBias' => ['split', 'centered', 'upper-third'],
            'titleTreatmentBias' => ['layered', 'solid', 'gradient'],
            'quoteStyleBias' => ['caption', 'cinematic-subtitle'],
            'narrativeEnergyDefault' => 0.78,
            'palettePresets' => [
                ['bg' => '#1A1020', 'ink' => '#F8E8F0', 'acc' => '#E11D74', 'hi' => '#F0C060', 'mute' => '#6A4A78'],
                ['bg' => '#F4E8F0', 'ink' => '#2A1020', 'acc' => '#E11D74', 'hi' => '#C6FF3D', 'mute' => '#6A4A78'],
            ],
        ],
        'animation' => [
            'family' => 'animation',
            'groundTone' => 'light',
            'materials' => ['paper', 'foil', 'cardstock'],
            'textures' => ['grainy', 'fibrous'],
            'metaphors' => ['silhouette-threshold', 'postcard', 'chaotic-key'],
            'lightingStyles' => ['high-key', 'theatrical', 'bloom'],
            'compositionGrammars' => ['crowded', 'asymmetric', 'organic'],
            'proceduralPrimary' => ['chaotic', 'organic'],
            'proceduralSecondary' => ['particle'],
            'proceduralAccent' => ['geometric'],
            'lineSemantics' => ['ribbons', 'waves', 'cords'],
            'particleSemantics' => ['confetti', 'pollen'],
            'patterns' => ['particles', 'flow'],
            'spatialFeelings' => ['fragmented', 'expanding'],
            'humanElements' => ['silhouette', 'paired-objects'],
            'titlePlacementBias' => ['split', 'centered', 'upper-third'],
            'titleTreatmentBias' => ['solid', 'layered', 'fragmented'],
            'quoteStyleBias' => ['caption', 'handwritten'],
            'narrativeEnergyDefault' => 0.75,
            'palettePresets' => [
                ['bg' => '#FFF4D8', 'ink' => '#2A1810', 'acc' => '#FF5A1F', 'hi' => '#4EC4E8', 'mute' => '#7A6A50'],
            ],
        ],
        'family' => [
            'family' => 'family',
            'groundTone' => 'light',
            'materials' => ['paper', 'linen', 'wood', 'cardstock'],
            'textures' => ['fibrous', 'grainy', 'photographic'],
            'metaphors' => ['paired-objects', 'postcard', 'handwritten-letter', 'silhouette-threshold'],
            'lightingStyles' => ['window-light', 'golden-hour', 'domestic-warm'],
            'compositionGrammars' => ['organic', 'editorial', 'central'],
            'proceduralPrimary' => ['organic', 'tactile'],
            'proceduralSecondary' => ['atmospheric'],
            'proceduralAccent' => ['particle'],
            'lineSemantics' => ['horizon', 'threads', 'waves'],
            'particleSemantics' => ['pollen', 'dust'],
            'patterns' => ['flow', 'particles'],
            'spatialFeelings' => ['isolated', 'drifting', 'expansive'],
            'humanElements' => ['hands', 'paired-objects', 'silhouette'],
            'titlePlacementBias' => ['lower-third', 'centered', 'upper-third'],
            'titleTreatmentBias' => ['solid', 'textured'],
            'quoteStyleBias' => ['handwritten', 'editorial-italic'],
            'narrativeEnergyDefault' => 0.5,
            'palettePresets' => [
                ['bg' => '#F6EDE0', 'ink' => '#2C2418', 'acc' => '#D9894A', 'hi' => '#F0C878', 'mute' => '#8A7A68'],
            ],
        ],
    ];

    return $grammars[$family] ?? $grammars['drama'];
}

function pickFromGrammar(array $list, int $seed, int $salt = 0): string
{
    if ($list === []) {
        return '';
    }
    $i = abs($seed + $salt) % count($list);
    return (string) $list[$i];
}

function paletteFromGrammar(array $grammar, int $seed): array
{
    $presets = $grammar['palettePresets'] ?? [];
    if ($presets === []) {
        return ['bg' => '#EFE6D8', 'ink' => '#1C1814', 'acc' => '#8B3A4A', 'hi' => '#C9A227', 'mute' => '#8A8178'];
    }
    return $presets[abs($seed) % count($presets)];
}

/**
 * Story-specific metaphor hints that still live inside the family's allow-list.
 */
function storyMetaphorHint(string $story, array $allowed): ?string
{
    $s = strtolower($story);
    $prefer = [];
    if (preg_match('/\b(mansion|key|landlord|rent|lock)\b/', $s)) {
        $prefer = ['chaotic-key', 'keyhole', 'chandelier-cluster', 'tangled-cords'];
    } elseif (preg_match('/\b(cape|coast|solitude|summer|sea|compass|lighthouse)\b/', $s)) {
        $prefer = ['coastal-compass', 'handwritten-letter', 'weathered-door'];
    } elseif (preg_match('/\b(letter|station|train|timetable|correspondence)\b/', $s)) {
        $prefer = ['correspondence-clock', 'railway-route', 'handwritten-letter', 'postcard'];
    } elseif (preg_match('/\b(map|expedition|ruin|quest)\b/', $s)) {
        $prefer = ['map-fold', 'compass-rose', 'roots'];
    }
    foreach ($prefer as $m) {
        if (in_array($m, $allowed, true)) {
            return $m;
        }
    }
    return null;
}

function isTechFamily(string $family): bool
{
    return in_array($family, ['thriller', 'scifi', 'horror'], true);
}

const FRAMEFLUX_TECH_PATTERNS = ['grid', 'mesh'];
const FRAMEFLUX_TECH_LINES = ['circuitry', 'wiring'];

function layoutForComposition(string $grammar, int $seed, string $family = 'drama'): string
{
    // Comedy titles are often long and hyphenated; a 40% split column
    // forces character-breaks. Keep comedy in full-width off-center layouts.
    if ($family === 'comedy') {
        return $seed % 2 === 0 ? 'off-center-top' : 'off-center-bottom';
    }
    return match ($grammar) {
        'asymmetric', 'crowded' => $seed % 2 === 0 ? 'off-center-top' : 'off-center-bottom',
        'diagonal' => 'split-editorial',
        'editorial', 'minimal' => $seed % 2 === 0 ? 'off-center-top' : 'split-editorial',
        'layered' => 'off-center-bottom',
        'expansive', 'organic', 'central' => 'centered',
        default => 'centered',
    };
}

function grammarDirectorBrief(string $family): string
{
    $g = genreGrammar($family);
    $materials = implode(', ', $g['materials'] ?? []);
    $metaphors = implode(', ', $g['metaphors'] ?? []);
    $lighting = implode(', ', $g['lightingStyles'] ?? []);
    $ground = $g['groundTone'] ?? 'mid';
    $proc = implode('/', array_filter([
        ($g['proceduralPrimary'] ?? [])[0] ?? '',
        ($g['proceduralSecondary'] ?? [])[0] ?? '',
    ]));

    $ban = isTechFamily($family)
        ? 'Technology, circuitry, and dark grids ARE appropriate for this story.'
        : 'Do NOT use circuit traces, neon grids, blue/purple tech glow, generic geometric voids, or a dark cyber background unless the story itself is about technology. Translate complexity into the material world of this genre.';

    return <<<BRIEF
Visual grammar family: {$family} (ground: {$ground}).
Allowed materials: {$materials}.
Allowed metaphors: {$metaphors}.
Lighting language: {$lighting}.
Procedural family: {$proc}.
{$ban}
The story determines the emotional interpretation. The emotional interpretation determines the visual language. Do not decorate a generic poster with genre colour.
BRIEF;
}
