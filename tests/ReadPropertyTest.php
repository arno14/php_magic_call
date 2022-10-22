<?php

use Arno14\MagicCall\CallConfigBuilder;
use Arno14\MagicCall\MagicCallTrait;
use PHPUnit\Framework\TestCase;

class ReadPropertyTest extends TestCase
{
    public function testAPrivatePropertyCanBeRead()
    {
        $c = new MyClassReadPropertyTest('one', 'two');

        $this->assertEquals('one', $c->myPrivateProp);
    }

    public function testAProtectedPropertyCanBeRead()
    {
        $c = new MyClassReadPropertyTest('one', 'two');

        $this->assertEquals('two', $c->myProtectedProp);
    }

    public function testAPrivatePropertyCanBeReadWithCustomGetter()
    {
        $c = new MyClassReadPropertyWithCustomGetterTest('ONE', 'two');

        $this->assertEquals('one', $c->myPrivateProp);
    }

    public function testAProtectedPropertyCanBeReadWithCustomGetter()
    {
        $c = new MyClassReadPropertyWithCustomGetterTest('one', '  two  ');

        $this->assertEquals('two', $c->myProtectedProp);
    }

    public function testAPrivatePropertyCanBeGuessedFromPhpdocWithPropertyAnnotation()
    {
        $c = new MyClassReadPropertyWithPhpdocTest('one', 'two');

        $this->assertEquals('one', $c->myPrivateProp);
    }

    public function testAProtectedPropertyCanBeGuessedFromPhpdocWithPropertyReadAnnotation()
    {
        $c = new MyClassReadPropertyWithPhpdocTest('one', 'two');

        $this->assertEquals('two', $c->myProtectedProp);
    }
}

class MyClassReadPropertyTest
{
    use MagicCallTrait;

    public static function configureMagicCall(CallConfigBuilder $builder)
    {
        $builder
        ->addPropertyRead('myPrivateProp')
        ->addPropertyRead('myProtectedProp');
    }

    public function __construct(
        private string $myPrivateProp,
        protected string $myProtectedProp
    ) {
    }
}

class MyClassReadPropertyWithCustomGetterTest
{
    use MagicCallTrait;

    public static function configureMagicCall(CallConfigBuilder $builder)
    {
        $builder
        ->addPropertyRead('myPrivateProp', 'getPrivatePropInLower')
        ->addPropertyRead('myProtectedProp', 'getProtectedPropTrimmed');
    }

    public function __construct(
        private string $myPrivateProp,
        protected string $myProtectedProp
    ) {
    }

    public function getPrivatePropInLower()
    {
        return strtolower($this->myPrivateProp);
    }

    public function getProtectedPropTrimmed()
    {
        return trim($this->myProtectedProp);
    }
}


/**
 * @property mixed $anUndefinedProp lorem ipsum dolor sit amet
 * @property string $myPrivateProp lorem ipsum dolor sit amet
 * @property-write string $myProtectedProp lorem ipsum dolor sit amet
 * @property-read string $myProtectedProp lorem ipsum dolor sit amet
 * @method string getString() lorem ipsum dolor sit amet
 * @method void setInteger(int $integer) lorem ipsum dolor sit amet
 * @method setString(int $integer) lorem ipsum dolor sit amet
 * @method static string staticGetter() lorem ipsum dolor sit amet
 */
class MyClassReadPropertyWithPhpdocTest
{
    use MagicCallTrait;

    public static function configureMagicCall(CallConfigBuilder $builder)
    {
        $builder->guessPropertyReadFromPhpDoc();
    }

    public function __construct(
        private string $myPrivateProp,
        protected string $myProtectedProp
    ) {
    }
}
