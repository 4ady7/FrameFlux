<?php

declare(strict_types=1);

function framefluxConfig(): array
{
    $config = [];
    $path = dirname(__DIR__, 2) . '/config.php';
    $exists = is_file($path);
    $loadedType = 'none';
    if ($exists) {
        $loaded = require $path;
        $loadedType = gettype($loaded);
        if (is_array($loaded)) {
            $config = $loaded;
        }
    }
    // #region agent log
    $log = json_encode(['sessionId' => '78eac4', 'hypothesisId' => 'A', 'location' => 'api/lib/config.php:framefluxConfig', 'message' => 'config load', 'data' => ['path' => $path, 'exists' => $exists, 'loadedType' => $loadedType, 'hasKey' => trim((string) ($config['openai_api_key'] ?? '')) !== '', 'keyCount' => count($config)], 'timestamp' => (int) (microtime(true) * 1000)]) . "\n";
    file_put_contents('/Users/shady/Projects/FrameFlux/.cursor/debug-78eac4.log', $log, FILE_APPEND);
    // #endregion
    return $config;
}

function framefluxApiKey(array $config): string
{
    $fromFile = trim((string) ($config['openai_api_key'] ?? ''));
    if ($fromFile !== '') {
        // #region agent log
        $log = json_encode(['sessionId' => '78eac4', 'hypothesisId' => 'B', 'location' => 'api/lib/config.php:framefluxApiKey', 'message' => 'api key source', 'data' => ['source' => 'config_file', 'len' => strlen($fromFile)], 'timestamp' => (int) (microtime(true) * 1000)]) . "\n";
        file_put_contents('/Users/shady/Projects/FrameFlux/.cursor/debug-78eac4.log', $log, FILE_APPEND);
        // #endregion
        return $fromFile;
    }
    $fromEnv = getenv('OPENAI_API_KEY');
    $envKey = is_string($fromEnv) ? trim($fromEnv) : '';
    // #region agent log
    $log = json_encode(['sessionId' => '78eac4', 'hypothesisId' => 'B', 'location' => 'api/lib/config.php:framefluxApiKey', 'message' => 'api key source', 'data' => ['source' => $envKey !== '' ? 'env' : 'empty', 'len' => strlen($envKey), 'libFileHasTrailingReturn' => true], 'timestamp' => (int) (microtime(true) * 1000)]) . "\n";
    file_put_contents('/Users/shady/Projects/FrameFlux/.cursor/debug-78eac4.log', $log, FILE_APPEND);
    // #endregion
    return $envKey;
}

function framefluxChatModel(array $config): string
{
    $model = trim((string) ($config['openai_model'] ?? 'gpt-5o-mini'));
    return $model !== '' ? $model : 'gpt-5o-mini';
}

function framefluxImageModel(array $config): string
{
    $model = trim((string) ($config['openai_image_model'] ?? 'gpt-image-2'));
    return $model !== '' ? $model : 'gpt-image-2';
}