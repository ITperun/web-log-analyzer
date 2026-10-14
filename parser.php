<?php

function parseLogLine(string $line): ?array
{
    $pattern = '/^(\S+) \S+ (\S+) \[([^\]]+)\] "(\S+) (\S+) [^"]*" (\d{3}) (\d+|-)$/';

    if (!preg_match($pattern, $line, $matches)) {
        return null;
    }

    return [
        'ip' => $matches[1],
        'user' => $matches[2],
        'time' => $matches[3],
        'method' => $matches[4],
        'path' => $matches[5],
        'status' => (int) $matches[6],
        'size' => $matches[7] === '-' ? 0 : (int) $matches[7],
    ];
}