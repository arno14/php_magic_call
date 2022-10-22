<?php

namespace Classes;

use Arno14\MagicCall\CallConfigBuilder;
use Arno14\MagicCall\MagicCallTrait;

/**
 * @property mixed $undefined_prop
 * @property mixed $foo
 * @property-read mixed $bar
 */
class A
{
    use MagicCallTrait;

    public function __construct(private $foo, private $bar)
    {
    }
    public static function configureMagicCall(CallConfigBuilder $builder)
    {
        $builder->guessFromPhpDoc();
    }
}
