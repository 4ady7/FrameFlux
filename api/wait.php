<?php

declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');

$dir = dirname(__DIR__) . '/data';
$path = $dir . '/generation-waits.csv';
$headers = [
    'title',
    'runtime',
    'date',
    'gpt_image_status',
];

function waitCsvAverages(string $path): array
{
    $enabled = [];
    $disabled = [];
    if (!is_file($path)) {
        return ['enabled' => null, 'disabled' => null, 'count_enabled' => 0, 'count_disabled' => 0];
    }
    $handle = fopen($path, 'r');
    if ($handle === false) {
        return ['enabled' => null, 'disabled' => null, 'count_enabled' => 0, 'count_disabled' => 0];
    }
    $header = fgetcsv($handle);
    $idxWait = is_array($header) ? array_search('waited_seconds', $header, true) : false;
    $idxGpt = is_array($header) ? array_search('gpt_image', $header, true) : false;
    $idxOk = is_array($header) ? array_search('ok', $header, true) : false;
    if ($idxWait === false || $idxGpt === false) {
        fclose($handle);
        return ['enabled' => null, 'disabled' => null, 'count_enabled' => 0, 'count_disabled' => 0];
    }
    while (($row = fgetcsv($handle)) !== false) {
        if (!isset($row[$idxWait], $row[$idxGpt])) {
            continue;
        }
        if ($idxOk !== false && ($row[$idxOk] ?? '') === 'no') {
            continue;
        }
        $waited = (float) $row[$idxWait];
        if ($waited <= 0) {
            continue;
        }
        if ($row[$idxGpt] === 'yes') {
            $enabled[] = $waited;
        } elseif ($row[$idxGpt] === 'no') {
            $disabled[] = $waited;
        }
    }
    fclose($handle);
    $avg = static function (array $values): ?float {
        if ($values === []) {
            return null;
        }
        return round(array_sum($values) / count($values), 2);
    };
    return [
        'enabled' => $avg($enabled),
        'disabled' => $avg($disabled),
        'count_enabled' => count($enabled),
        'count_disabled' => count($disabled),
    ];
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    echo json_encode(waitCsvAverages($path));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$raw = file_get_contents('php://input') ?: '';
if (strlen($raw) > 4096) {
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

$waited = round((float) ($data['waited_seconds'] ?? -1), 2);
$secondsLeft = max(0, round((float) ($data['seconds_left'] ?? 0), 2));
$expected = round((float) ($data['expected_seconds'] ?? 0), 2);
$gptImage = ($data['gpt_image'] ?? '') === true || ($data['gpt_image'] ?? '') === 'yes' ? 'yes' : 'no';
$mode = trim((string) ($data['mode'] ?? 'generate'));
$ok = !empty($data['ok']) ? 'yes' : 'no';
$title = trim((string) ($data['title'] ?? ''));

if ($waited < 0 || $waited > 600) {
    http_response_code(400);
    echo json_encode(['error' => 'waited_seconds is out of range.']);
    exit;
}

$allowedModes = ['generate', 'improve', 'reimagine'];
if (!in_array($mode, $allowedModes, true)) {
    $mode = 'generate';
}

if (mb_strlen($title) > 80) {
    $title = mb_substr($title, 0, 80);
}

if (!is_dir($dir) && !mkdir($dir, 0755, true) && !is_dir($dir)) {
    http_response_code(500);
    echo json_encode(['error' => 'Could not create data directory.']);
    exit;
}

$handle = fopen($path, 'c+');
if ($handle === false) {
    http_response_code(500);
    echo json_encode(['error' => 'Could not open wait CSV.']);
    exit;
}

flock($handle, LOCK_EX);
$stat = fstat($handle);
$needsHeader = !is_array($stat) || $stat['size'] === 0;
if ($needsHeader) {
    fputcsv($handle, $headers, ',', '"', '\\');
} else {
    fseek($handle, 0, SEEK_END);
}
fputcsv($handle, [
    gmdate('c'),
    number_format($waited, 2, '.', ''),
    number_format($secondsLeft, 2, '.', ''),
    number_format($expected, 2, '.', ''),
    $gptImage,
    $mode,
    $ok,
    $title,
], ',', '"', '\\');
flock($handle, LOCK_UN);
fclose($handle);

echo json_encode(['ok' => true, 'path' => 'data/generation-waits.csv']);
