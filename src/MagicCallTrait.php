<?php

namespace Arno14\MagicCall;

trait MagicCallTrait
{
    public static function configureMagicCall(CallConfigBuilder $builder)
    {
    }

    public function __get($name)
    {
        return CallConfigRegistry::getConfiguration(__CLASS__)->readProperty($this, $name);
    }

    public function __set($name, $value)
    {
        return CallConfigRegistry::getConfiguration(__CLASS__)->writeProperty($this, $name, $value);
    }
}
