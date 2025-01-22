<?php

require_once('Generator/GeneratorInterface.php');

class PasswordEncoder
{
    public const GROUPS = [
        '#' => '!"#$%&\'()*+,-./:;<=>?@[\\]^_`{|}~', // ascii appearance order
        'a' => 'abcdefghijklmnopqrstuvwxyz',
        'A' => 'ABCDEFGHIJKLMNOPQRSTUVWXYZ',
        '0' => '0123456789',
    ];
    private const ALPHABET = self::GROUPS['A'] . self::GROUPS['a'] . self::GROUPS['0'] . self::GROUPS['#'];

    public readonly array $groupKeys;
    private readonly ?string $coverPattern;
    public readonly string $alphabet;

    public function __construct(array $groupKeys, string $excludedChars, bool $groupCover)
    {
        $this->groupKeys = static::normalizeGroupKeys($groupKeys);
        $this->coverPattern = $groupCover ? static::getCoverPattern($this->groupKeys) : null;
        $this->alphabet = static::getAlphabet($this->groupKeys, $excludedChars);
    }

    public function get(GeneratorInterface $generator, int $size): string
    {
        if (null !== $this->coverPattern && count($this->groupKeys) > $size) {
            throw new InvalidArgumentException('Size too small');
        }

        do {
            $pw = $this->getRaw($generator, $size);
        } while (null !== $this->coverPattern && !preg_match($this->coverPattern, $pw));

        return $pw;
    }

    protected function getRaw(GeneratorInterface $generator, int $size): string
    {
        $pw = '';
        while (strlen($pw) < $size) {
            $pw .= $this->encode($generator->squeeze());
        }

        return substr($pw, 0, $size);
    }

    protected function encode(string $input): string
    {
        $alphabet = str_split($this->alphabet);
        $alphabetSize = count($alphabet);
        $limit = 256 - (256 % $alphabetSize);
        $output = '';
        foreach (str_split($input) as $char) {
            if (($ord = ord($char)) >= $limit) {
                continue; // to keep a uniform distribution
            }
            $output .= $alphabet[$ord % $alphabetSize];
        }

        return $output;
    }

    /**
     * @param array $groupKeys eg ['5', '!]
     * @return array normalized group keys eg ['#', '0']
     */
    protected static function normalizeGroupKeys(array $groupKeys): array
    {
        $ugroupKeys = [];
        foreach (static::GROUPS as $key => $str) {
            foreach ($groupKeys as $groupKey) {
                if (false !== strpos($str, $groupKey)) {
                    $ugroupKeys[] = $key;
                    break;
                }
            }
        }

        if (!count($ugroupKeys)) {
            throw new InvalidArgumentException('No group keys found');
        }

        return $ugroupKeys;
    }

    protected static function getCoverPattern(array $groupKeys): string
    {
        $pattern = '/^';
        foreach ($groupKeys as $group) {
            $pattern .= '(?=.*[' . preg_quote(static::GROUPS[$group], '/') . '])';
        }
        return $pattern . '.*$/';
    }

    protected static function getAlphabet(array $groupKeys, string $excludes): string
    {
        $qincludes = preg_quote(implode(array_intersect_key(static::GROUPS, array_flip($groupKeys))), '/');
        $qexcludes = strlen($excludes) ? '|[' . preg_quote($excludes, '/') . ']' : '';

        return preg_replace("/[^$qincludes]$qexcludes/", '', self::ALPHABET);
    }
}
