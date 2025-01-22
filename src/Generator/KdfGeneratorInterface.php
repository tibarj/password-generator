<?php

require_once('GeneratorInterface.php');

interface KdfGeneratorInterface extends GeneratorInterface {
    /**
     * Init, including key stretching
     */
    public function init(string $state, int $iterations): static;
}
