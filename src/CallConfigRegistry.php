<?php

namespace Arno14\MagicCall;

class CallConfigRegistry
{
    /**
     * @var CallConfig[]
     */
    private static ?array $configurations;

    /**
     * @var mixed[]
     */
    private static array $options=[
        'guess_property_read_from_phpdoc'=>false,
        'guess_property_write_from_phpdoc'=>false,
        'guess_method_read_from_phpdoc'=>false,
        'guess_method_write_from_phpdoc'=>false,
        'enable_cache'=>false,
        'cache_ttl'=>3600
    ];

    /**
     * @param mixed[] $options
     */
    public static function configure(array $options): void
    {
        self::$options=$options+self::$options;
    }

    /**
    * @param class-string $className
    */
    public static function getConfig(string $className): CallConfig
    {
        if (!isset(self::$configurations[$className])) {
            self::$configurations[$className]=self::createConfig($className);
        }

        return self::$configurations[$className];
    }

    /**
     * @param class-string $className
     */
    private static function createConfig(string $className): CallConfig
    {
        $builder = new CallConfigBuilder($className);

        if (method_exists($className, 'configureMagicCall')) {
            //@phpstan-ignore-next-line
            call_user_func([$className,'configureMagicCall'], $builder);
        }

        if (self::$options['guess_property_read_from_phpdoc']) {
            $builder->guessPropertyReadFromPhpDoc();
        }

        if (self::$options['guess_property_write_from_phpdoc']) {
            $builder->guessPropertyReadFromPhpDoc();
        }

        return $builder->getConfig();
    }
}
