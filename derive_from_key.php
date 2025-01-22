<?php

// php derive_from_key.php --groups=0#aA --excludes=0O --size=30 --seed=$(prompt)

require_once(file_exists('src/Generator/KdfGenerator.php')
    ? 'src/Generator/KdfGenerator.php'
    : 'src/Generator/KdfGenerator.php.dist'
);
require_once('src/PasswordEncoder.php');

const DEFAULT_ARGS = [
    'groups' => '#aA0', // combinaison of {#,a,A,0}
    'exclude' => '`"<>', // excluded chars
    'size' => 32, // size of output password in bytes
    'iterations' => 500000,
    'coverage' => '1', // one char of each group (0|1 <=> false|true)
    'seed' => '',
];

[$groups, $exclude, $size, $iterations, $coverage, $seed] = array_values(DEFAULT_ARGS);
array_shift($argv);
foreach ($argv as $arg) { // overwrite with given arguments
    preg_match('/^--([\w]+)=(.*)/', $arg, $m);
    ${$m[1]} = $m[2];
}
$coverage = (bool) $coverage;
$target = readline('Target (SLD): ') ?? '';
$version = readline('Version [0]: ') ?: '0';

$pr = new PasswordEncoder(str_split($groups), $exclude, $coverage);
$s = (new KdfGenerator)->init("$seed/$target/$version", $iterations);
$pw = $pr->get($s, $size);

echo PHP_EOL . implode(PHP_EOL, [
    'groups: ' . implode('', $pr->groupKeys),
    "alphabet: $pr->alphabet",
    'alphabet_size: ' . strlen($pr->alphabet),
    "excluded: $exclude",
    'pw_size: ' . strlen($pw),
    "iterations: $iterations",
    'seed: ' . ($seed ? $seed[0] . '..' . $seed[-1] : 'N/A'),
    'target: ' . ($target ?: 'N/A'),
    "version: $version",
    PHP_EOL . $pw
]) . PHP_EOL . PHP_EOL;
