<?php

require_once('GeneratorInterface.php');

class RandomGenerator implements GeneratorInterface
{
    public function squeeze(): string
    {
        return random_bytes(100);
    }
}
