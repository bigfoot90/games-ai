#!/bin/env php
<?php

const PROJECT_ROOT = __DIR__ . '/..';

$handle = fopen(PROJECT_ROOT.'/output/history.suffle.log', 'r') or die('File not found or not readable');

$outputs = [];

function positions2board(array $positions): array
{
    $board = array_fill(0, 9, 0);

    foreach ($positions as $pos) {
        $board[$pos] = 1;
    }

    return $board;
}

function board2key(array $board): int
{
    return base_convert(implode('', $board), 3, 10);
}

while (($line = fgets($handle)) !== false) {
    $decoded = json_decode($line, true, 512, JSON_OBJECT_AS_ARRAY | JSON_THROW_ON_ERROR);

    $len = count($decoded['H']);
    if (!$len) continue;
    $board = array_fill(0, 9, 0);

    echo $line;

    for ($j = 0; $j <= $len -1; $j++) {
        for ($i = $j; $i >= 0; $i--) {
            $firstPlayer = $i % 2 === 0;
            $board[$decoded['H'][$i]] = $firstPlayer ? 1 : 2;
        }
        $next = $decoded['H'][$j];
        $key = board2key($board);
        echo json_encode($board) . "\t\t" . $key . "\t\t" . $next . "\n";

        $outputs[$key][$next] = ($outputs[$key][$next] ?? 0) +1;
    }
    echo "\n";
}

fclose($handle);

$outputs = array_map(fn($moves) => board2key(positions2board(array_keys($moves))), $outputs);


// Write moves map

@unlink(PROJECT_ROOT.'/output/moves.json');
file_put_contents(PROJECT_ROOT.'/output/moves.json', json_encode($outputs, JSON_THROW_ON_ERROR));
