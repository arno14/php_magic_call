<?php

use Arno14\MagicCall\CallConfigBuilder;
use Arno14\MagicCall\MagicCallTrait;
use PHPUnit\Framework\TestCase;

class WritePropertyTest extends TestCase
{
    public function testAPrivatePropertyCanBeWriten()
    {
        $c = new MyClassWritePropertyTest('foo', 'bar');

        $c->myPrivateProp='modified';

        $this->assertEquals('modified', $c->myPrivateProp);
    }

    public function testAProtectedPropertyCanBeWritten()
    {
        $c = new MyClassWritePropertyTest('foo', 'bar');


        $c->myProtectedProp='modified';

        $this->assertEquals('modified', $c->myProtectedProp);
    }

    public function testAPrivatePropertyCanBeWritenWithCustomSetter()
    {
        $c = new MyClassWithCustomMethodWritePropertyTest('foo', 'bar');

        $c->myPrivateProp='Modified';

        $this->assertEquals('modified', $c->myPrivateProp);
    }

    public function testAProtectedPropertyCanBeWrittenWithCustomSetter()
    {
        $c = new MyClassWithCustomMethodWritePropertyTest('foo', 'bar');

        $c->myProtectedProp=' modified  ';

        $this->assertEquals('modified', $c->myProtectedProp);
    }

    public function testAPrivatePropertyCanBeGuessedFromPhpdocWithPropertyAnnotation()
    {
        $c = new MyClassWriteWithPhpDocPropertyTest('one', 'two');

        $c->myPrivateProp='modified';

        $this->assertEquals('modified', $c->myPrivateProp);
    }

    public function testAProtectedPropertyCanBeGuessedFromPhpdocWithPropertyWriteAnnotation()
    {
        $c = new MyClassWriteWithPhpDocPropertyTest('one', 'two');

        $c->myProtectedProp='modified';

        $this->assertEquals('modified', $c->myProtectedProp);
    }
}

class MyClassWritePropertyTest
{
    use MagicCallTrait;

    public static function configureMagicCall(CallConfigBuilder $builder)
    {
        $builder
        ->addPropertyRead('myPrivateProp')
        ->addPropertyRead('myProtectedProp')
        ->addPropertyWrite('myPrivateProp')
        ->addPropertyWrite('myProtectedProp');
    }

    public function __construct(
        private string $myPrivateProp,
        protected string $myProtectedProp
    ) {
    }
}

class MyClassWithCustomMethodWritePropertyTest
{
    use MagicCallTrait;

    public static function configureMagicCall(CallConfigBuilder $builder)
    {
        $builder
        ->addPropertyRead('myPrivateProp')
        ->addPropertyRead('myProtectedProp')
        ->addPropertyWrite('myPrivateProp', 'setMyPrivatePropInLower')
        ->addPropertyWrite('myProtectedProp', 'setMyProtectedPropTrimmed');
    }

    public function __construct(
        private string $myPrivateProp,
        protected string $myProtectedProp
    ) {
    }

    public function setMyPrivatePropInLower($value)
    {
        $this->myPrivateProp= strtolower($value);
    }
    public function setMyProtectedPropTrimmed($value)
    {
        $this->myProtectedProp= trim($value);
    }
}

/**
 * @property mixed $anUndefinedProp             lorem ipsum dolor sit amet
 * @method string getString()                   lorem ipsum dolor sit amet
 * @property string $myPrivateProp              lorem ipsum dolor sit amet
 * @property-write string $myProtectedProp      lorem ipsum dolor sit amet
 * @property-read string $myProtectedProp       lorem ipsum dolor sit amet
 * @method void setInteger(int $integer)        lorem ipsum dolor sit amet
 * @method setString(int $integer)              lorem ipsum dolor sit amet
 * @method static string staticGetter()         lorem ipsum dolor sit amet
 */
class MyClassWriteWithPhpDocPropertyTest
{
    use MagicCallTrait;

    public static function configureMagicCall(CallConfigBuilder $builder)
    {
        $builder
        ->guessPropertyWriteFromPhpDoc()
        ->addPropertyRead('myPrivateProp')
        ->addPropertyRead('myProtectedProp');
    }

    public function __construct(
        private string $myPrivateProp,
        protected string $myProtectedProp
    ) {
    }
}
