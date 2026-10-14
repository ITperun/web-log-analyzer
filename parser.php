<?php

function parseLogLine(string $line): ?array
{
    $pattern = '/^(\S+) \S+ \S+ \[([^\]]+)\] "(\S+) (\S+) [^"]*" (\d{3}) (\d+|-)$/';

    if (!preg_match($pattern, $line, $matches)) {
        return null;
    }

    return [
        'ip' => $matches[1],
        'time' => $matches[2],
        'method' => $matches[3],
        'path' => $matches[4],
        'status' => (int) $matches[5],
        'size' => $matches[6] === '-' ? 0 : (int) $matches[6],
    ];
}