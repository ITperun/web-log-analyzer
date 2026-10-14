<?php

require 'parser.php';

$handle = fopen('sample-access.log', 'r');
$count = 0;

while (($line = fgets($handle)) !== false && $count < 10) {
    print_r(parseLogLine(trim($line)));
    $count++;
}

fclose($handle);