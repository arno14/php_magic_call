<?php

use Arno14\MagicCall\CallConfigRegistry;
use Arno14\MagicCall\MagicCallTrait;

include __DIR__.'/../vendor/autoload.php';

if (count($argv)===1) {
    printf('Missing argument directory path %s', json_encode($argv, JSON_PRETTY_PRINT));
    return;
}

$path=$argv[count($argv)-1];

echo PHP_EOL,'-----------------------------------------------------------------------------',PHP_EOL;
printf('--- Debug Magic call class config in path "%s"', $path);
echo PHP_EOL,'-----------------------------------------------------------------------------',PHP_EOL;


$dumpClasses=function (SplFileInfo $item) {
    $actualClasses = get_declared_classes();
    include($item->getPathname());
    $newClasses = array_diff(get_declared_classes(), $actualClasses);

    foreach ($newClasses as $newClass) {
        echo PHP_EOL,PHP_EOL, sprintf('"%s" in "%s"', $newClass, $item->getPathname());

        $config = CallConfigRegistry::getConfig($newClass);

        if (!in_array(MagicCallTrait::class, array_keys((array)$config->reflection->getTraits()))) {
            echo PHP_EOL, ' !!! does not use ',MagicCallTrait::class;
        }
        if (!$config->reflection->hasMethod('configureMagicCall')) {
            echo PHP_EOL, ' !!! does not have method configureMagicCall';
        }

        foreach (['property_read','property_write'] as $attr) {
            echo PHP_EOL, $attr, ': ',json_encode($config->$attr, JSON_PRETTY_PRINT);
        }

        if ($config->debug_logs) {
            echo PHP_EOL, ' !!! debug logs', json_encode($config->debug_logs, JSON_PRETTY_PRINT);
        }
    }
};

$directory = new \RecursiveDirectoryIterator($path);
$iterator = new \RecursiveIteratorIterator($directory);


foreach ($iterator as $item) {
    if ($item->isFile()) {
        $dumpClasses($item);
        continue;
    }
    // print_r($item);
}

echo PHP_EOL;
