<?php

require_once 'parser.php';

function analyzeLog(string $filePath): array
{
    $handle = fopen($filePath, 'r');
    if ($handle === false) {
        throw new RuntimeException("Failed to open file: $filePath");
    }

    $result = [
        'total' => 0,
        'skipped' => 0,
        'by_ip' => [],
        'by_status' => [],
        'by_path' => [],
        'auth_failures' => [],
    ];

    while (($line = fgets($handle)) !== false) {
        $entry = parseLogLine(trim($line));

        if ($entry === null) {
            $result['skipped']++;
            continue;
        }

        $result['total']++;

        $ip = $entry['ip'];
        $status = $entry['status'];
        $path = $entry['path'];

        $result['by_ip'][$ip] = ($result['by_ip'][$ip] ?? 0) + 1;
        $result['by_status'][$status] = ($result['by_status'][$status] ?? 0) + 1;
        $result['by_path'][$path] = ($result['by_path'][$path] ?? 0) + 1;

        if ($status === 401 || $status === 403) {
            $result['auth_failures'][$ip] = ($result['auth_failures'][$ip] ?? 0) + 1;
        }
    }

    fclose($handle);

    arsort($result['by_ip']);
    arsort($result['auth_failures']);

    return $result;
}