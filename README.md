Minimalist and Ready-to-Go Password/Key Generation Scripts.

## Random password generation

```
$ php derive_from_random.php --groups=aA#0 --exclude=0O --size=30

groups: #aA0
alphabet: ABCDEFGHIJKLMNPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz123456789!"#$%&'()*+,-./:;<=>?@[\]^_`{|}~
alphabet_size: 92
excluded: 0O
pw_size: 30

/-(eBx4arpPn9c&3g|}c~YxVC6+r2t
```

```
$ php derive_from_random.php --groups=aA0 --size=100

groups: aA0
alphabet: ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789
alphabet_size: 62
excluded: `"<>
pw_size: 100

zyvRKpiie0B2HbCmmxKrhkv9RXGULk44mZne63LEqfm3IqOKfXexWYaKbjr9B2iI3HozUYaOlJ5MgXDoi8IALBYSchSTfWZFOspA
```

## Key derivation password

```
$ php derive_from_key.php --groups=0#aA --exclude=\`\" --size=40 --coverage=1 --seed=$(prompt)
Target (SLD): github
Version [0]:

groups: #aA0
alphabet: ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!#$%&'()*+,-./:;<=>?@[\]^_{|}~
alphabet_size: 92
excluded: `"
pw_size: 40
iterations: 500000
seed: a..a
target: github
version: 0

P9CoLB0_^4e?]elQp<K}EGyBj)#ZD}Qa(-MvzjT^
```

## Parameters

### --groups

Groups of characters used in the alphabet.

```
default: aA0#
```

|key|chars|activated by|
|--|--|--|
|a|abcdefghijklmnopqrstuvwxyz|any lowercase alpha|
|A|ABCDEFGHIJKLMNOPQRSTUVWXYZ|any uppercase alpha|
|0|0123456789|any digit|
|#|!#$%&'()*+,-./:;<=>?@[\]^_{\|}~|any symbol

### --coverage

Specifies whether the password must always contain at least one character from each group.

```
default: 1
```

|value||
|--|--|
|0|no|
|1|yes|

### --exclude

Characters to exclude from the alphabet.

```
default: `"<>
```

### --size

Number of characters in the output password.

```
default: 32
```

### --iterations

> Only applicable for key derivation.

Number of iterations sent to the key derivation function (KDF).

```
default: 500000
```

## Customization

Copy `src/Generator/KdfGenerator.php.dist` to `src/Generator/KdfGenerator.php`

Change the generator recipe as needed.

```php
<?php
require_once('GeneratorInterface.php');

class KdfGenerator implements GeneratorInterface
{
    private string $state;

    public function init(string $seed, int $iterations): static
    {
        $this->state = $seed;

        return $this->stretch($iterations);
    }

    /**
     * Change to your squeezing recipe.
     */
    public function squeeze(): string
    {
        $this->stretch(1);

        return $this->state;
    }

    /**
     * Change to your stretching recipe.
     */
    private function stretch(int $iterations): static
    {
        $this->state = hash_pbkdf2('sha3-512', $this->state, '', $iterations, 0, true);

        return $this;
    }
}
```
