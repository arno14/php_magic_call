<?php

namespace Arno14\MagicCall;

class CallConfigRegistry
{
    /**
     * @var CallConfig[]
     */
    private static ?array $configurations;

    private static array $options=[
        'guess_property_read_from_phpdoc'=>false,
        'guess_property_write_from_phpdoc'=>false,
        'guess_method_read_from_phpdoc'=>false,
        'guess_method_write_from_phpdoc'=>false,
        'enable_cache'=>false,
        'cache_ttl'=>3600
    ];

    public static function configure(array $options)
    {
        self::$options=$options+self::$options;
    }

    public static function getConfiguration(string $className): CallConfig
    {
        if (!isset(self::$configurations[$className])) {
            self::$configurations[$className]=self::createConfiguration($className);
        }

        return self::$configurations[$className];
    }

    private static function createConfiguration(string $className): CallConfig
    {
        $builder = new CallConfigBuilder($className);

        if (method_exists($className, 'configureMagicCall')) {
            call_user_func([$className,'configureMagicCall'], $builder);
        }

        if (self::$options['guess_property_read_from_phpdoc']) {
            $builder->guessPropertyReadFromPhpDoc();
        }

        if (self::$options['guess_property_write_from_phpdoc']) {
            $builder->guessPropertyReadFromPhpDoc();
        }

        return $builder->getConfiguration();
    }
}
