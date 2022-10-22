<?php

namespace Classes\Sub;

use Arno14\MagicCall\CallConfigBuilder;
use Arno14\MagicCall\MagicCallTrait;

class B
{
    use MagicCallTrait;

    public function __construct(private $foo, private $bar)
    {
    }
    public static function configureMagicCall(CallConfigBuilder $builder)
    {
        $builder->guessPropertyReadFromPhpDoc()
                ->addPropertyRead('foo')
                ->addPropertyWrite('bar');
    }
}
