<?php

$file = fopen('sample-access.log', 'w');
$ips = ['192.168.1.10', '10.0.0.5', '172.16.0.23', '198.51.100.42'];
$paths = ['/', '/products', '/about', '/api/items'];
$codes = [200, 200, 200, 404, 500];
$start = strtotime('2026-10-14 10:00:00');

for ($i = 0; $i < 500; $i++) {
    $time = date('d/M/Y:H:i:s O', $start + $i * 3);

    if ($i % 5 === 0) {
        $line = "203.0.113.7 - - [$time] \"POST /login HTTP/1.1\" 401 512";
    } else {
        $ip = $ips[array_rand($ips)];
        $path = $paths[array_rand($paths)];
        $code = $codes[array_rand($codes)];
        $line = "$ip - - [$time] \"GET $path HTTP/1.1\" $code 1024";
    }

    fwrite($file, $line . PHP_EOL);
}

$adminLines = [
    '10.0.0.8 - admin [14/Oct/2026:03:15:00 +0000] "POST /login HTTP/1.1" 200 512',
    '10.0.0.8 - admin [14/Oct/2026:02:47:00 +0000] "POST /login HTTP/1.1" 200 512',
    '10.0.0.8 - admin [14/Oct/2026:10:30:00 +0000] "POST /login HTTP/1.1" 200 512',
];

foreach ($adminLines as $line) {
    fwrite($file, $line . PHP_EOL);
}

fclose($file);
echo "Ready: sample-access.log\n";