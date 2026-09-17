<?php

declare(strict_types=1);

function openaiChatJson(
    string $apiKey,
    string $model,
    string $system,
    string $prompt,
    float $temperature,
    int $timeout = 25
): ?array {
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
        CURLOPT_TIMEOUT => $timeout,
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

function openaiImagePng(
    string $apiKey,
    string $model,
    string $prompt,
    int $timeout = 55
): ?string {
    $payload = [
        'model' => $model,
        'prompt' => $prompt,
        'n' => 1,
        'size' => '1024x1792',
        'response_format' => 'b64_json',
        'quality' => 'standard',
    ];

    $ch = curl_init('https://api.openai.com/v1/images/generations');
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $apiKey,
        ],
        CURLOPT_POSTFIELDS => json_encode($payload),
        CURLOPT_TIMEOUT => $timeout,
    ]);

    $response = curl_exec($ch);
    $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if (!is_string($response) || $status < 200 || $status >= 300) {
        return null;
    }

    $decoded = json_decode($response, true);
    $b64 = $decoded['data'][0]['b64_json'] ?? null;
    return is_string($b64) && $b64 !== '' ? $b64 : null;
}

function cinematicImagePrompt(array $dna): string
{
    $c = is_array($dna['cinematic'] ?? null) ? $dna['cinematic'] : [];
    $p = is_array($dna['palette'] ?? null) ? $dna['palette'] : [];
    $concept = is_array($dna['concept'] ?? null) ? $dna['concept'] : [];
    $semantic = is_array($dna['semantic'] ?? null) ? $dna['semantic'] : [];

    $subject = (string) ($c['subject'] ?? 'a cinematic figure');
    $environment = (string) ($c['environment'] ?? 'atmospheric landscape');
    $lighting = (string) ($c['lighting'] ?? 'chiaroscuro');
    $atmosphere = (string) ($c['atmosphere'] ?? 'haze');
    $camera = (string) ($c['camera'] ?? 'wide');
    $mood = (string) ($concept['mood'] ?? 'cinematic');
    $genre = (string) ($concept['genre'] ?? 'drama');
    $bg = (string) ($p['background'] ?? '#111111');
    $primary = (string) ($p['primary'] ?? '#444444');
    $accent = (string) ($p['accent'] ?? '#c9a227');

    $metaphor = str_replace('-', ' ', (string) ($semantic['visualMetaphor'] ?? 'silhouette threshold'));
    $material = str_replace('-', ' ', (string) ($semantic['material'] ?? 'paper'));
    $texture = (string) ($semantic['texture'] ?? 'grainy');
    $emotion = (string) ($semantic['emotionalCore'] ?? 'unease');
    $narrative = str_replace('-', ' ', (string) ($semantic['narrativeCore'] ?? 'discovery'));
    $spatial = (string) ($semantic['spatial'] ?? 'isolated');
    $human = (string) ($semantic['humanElements'] ?? 'none');
    $rawMetaphor = (string) ($semantic['visualMetaphor'] ?? '');

    $natureClause = in_array($rawMetaphor, [
        'canine-silhouette',
        'animal-tracks',
        'mountain-ridge',
        'alpine-peak',
        'wild-canopy',
        'botanical-press',
        'forest-fringe',
    ], true) || in_array($human, ['animal-silhouette', 'flora'], true)
        ? 'Wildlife, canines, mountains, alpine terrain, forests, and botanical environments ARE allowed when this story selects them. Photograph the natural subject as a cinematic still, never a posed human studio or fashion portrait.'
        : 'Do not transform this into a generic human studio-fashion portrait.';

    return <<<PROMPT
Cinematic still photograph for a movie, not a poster.
No typography, no titles, no captions, no logos, no credits, no UI, no HUD, no lettering, no metadata labels.
Do not render REF:// labels, reference identifiers, or archive codes.
{$camera} shot of {$subject} in {$environment}.
Visual metaphor: {$metaphor}. Material presence of {$material} with {$texture} surface.
Emotional core: {$emotion}. Narrative about {$narrative}. Spatial feel: {$spatial}.
Mood: {$mood}. Genre feeling: {$genre}.
Lighting: {$lighting}. Atmosphere: {$atmosphere}.
Colour grade keyed to {$bg}, {$primary}, and a spare accent of {$accent}.
One dominant subject, restrained detail, physically believable materials, filmic grain.
The plate must remain textless: no typography, no titles, no captions, no logos, no credits, no UI.
{$natureClause}
Avoid generic neon glow, circuit boards, holographic HUDs, and decorative symmetry unless the story is technological.
PROMPT;
}
