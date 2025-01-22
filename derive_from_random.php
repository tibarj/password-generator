<?php

// php derive_from_random.php--groups=0#aA --excludes=0O --size=30

require_once('src/Generator/RandomGenerator.php');
require_once('src/PasswordEncoder.php');

const DEFAULT_ARGS = [
    'groups' => '#aA0', // combinaison of {#,a,A,0}
    'exclude' => '`"<>', // excluded chars
    'size' => 32, // size of output password in bytes
    'coverage' => '1', // one char of each group (0|1 <=> false|true)
];

[$groups, $exclude, $size, $coverage] = array_values(DEFAULT_ARGS);
array_shift($argv);
foreach ($argv as $arg) { // overwrite with given arguments
    preg_match('/^--([\w]+)=(.*)/', $arg, $m);
    ${$m[1]} = $m[2];
}
$coverage = (bool) $coverage;

$pr = new PasswordEncoder(str_split($groups), $exclude, $coverage);
$pw = $pr->get(new RandomGenerator(), $size);

echo PHP_EOL . implode(PHP_EOL, [
    'groups: ' . implode('', $pr->groupKeys),
    "alphabet: $pr->alphabet",
    'alphabet_size: ' . strlen($pr->alphabet),
    "excluded: $exclude",
    'pw_size: ' . strlen($pw),
    PHP_EOL . $pw
]) . PHP_EOL . PHP_EOL;
