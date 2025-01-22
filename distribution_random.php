<?php

require_once('src/Generator/RandomGenerator.php');
require_once('src/PasswordEncoder.php');

$pr = new PasswordEncoder(str_split('aA#0'), '', false);
$size = 1000000;
$pw = $pr->get(new RandomGenerator(), $size);
$dist = [];
foreach (str_split($pw) as $c) {
    $dist[$c] = ($dist[$c] ?? -1) + 1;
}
$dist = array_map(fn($i) => 100 * $i / $size, $dist);
var_dump($dist);
