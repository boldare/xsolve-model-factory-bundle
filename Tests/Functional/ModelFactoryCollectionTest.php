<?php

namespace Xsolve\ModelFactoryBundle\Tests\Functional;

use ArrayObject;
use PHPUnit_Framework_TestCase;
use stdClass;
use Xsolve\ModelFactoryBundle\Tests\Functional\Mock\Helper\VolumetricWeightHelper;
use Xsolve\ModelFactoryBundle\Tests\Functional\Mock\ModelFactory\BarModelFactory;
use Xsolve\ModelFactoryBundle\Tests\Functional\Mock\ModelFactory\BazModelFactory;
use Xsolve\ModelFactoryBundle\Tests\Functional\Mock\ModelFactory\FooModelFactory;
use Xsolve\ModelFactoryBundle\Tests\Functional\Mock\Object\Bar;
use Xsolve\ModelFactoryBundle\Tests\Functional\Mock\Object\Baz;
use Xsolve\ModelFactoryBundle\Tests\Functional\Mock\Object\Foo;

class ModelFactoryTest extends PHPUnit_Framework_TestCase
{
    public function test_supportsObject()
    {
        $fooModelFactory = new FooModelFactory();
        $barModelFactory = new BarModelFactory();
        $bazModelFactory = new BazModelFactory(new VolumetricWeightHelper(0.05, 2));

        $foo = new Foo('foo.1', 'lorem');
        $bar = new Bar('bar.1', 12);
        $baz = new Baz('baz.1', 1.0, 0.2, 0.2, 0.2);

        $this->assertTrue($fooModelFactory->supportsObject($foo));
        $this->assertFalse($fooModelFactory->supportsObject($bar));
        $this->assertFalse($fooModelFactory->supportsObject($baz));

        $this->assertFalse($barModelFactory->supportsObject($foo));
        $this->assertTrue($barModelFactory->supportsObject($bar));
        $this->assertFalse($barModelFactory->supportsObject($baz));

        $this->assertFalse($bazModelFactory->supportsObject($foo));
        $this->assertFalse($bazModelFactory->supportsObject($bar));
        $this->assertTrue($bazModelFactory->supportsObject($baz));
    }

    public function test_supportsObjects()
    {
        $fooModelFactory = new FooModelFactory();
        $barModelFactory = new BarModelFactory();

        $foos = [
            new Foo('foo.1', 'lorem'),
            new Foo('foo.2', 'ipsum'),
            new Foo('foo.3', 'dolor'),
        ];
        $bars = [
            new Bar('bar.1', 12),
            new Bar('bar.2', 7),
        ];

        $this->assertTrue($fooModelFactory->supportsObjects([$foos[0]]));
        $this->assertTrue($fooModelFactory->supportsObjects([$foos[0], $foos[1]]));
        $this->assertTrue($fooModelFactory->supportsObjects([$foos[0], $foos[1], $foos[2]]));
        $this->assertFalse($fooModelFactory->supportsObjects([$foos[0], $foos[1], $bars[0]]));
        $this->assertFalse($fooModelFactory->supportsObjects([$bars[0], $bars[1]]));

        $this->assertFalse($barModelFactory->supportsObjects([$foos[0]]));
        $this->assertFalse($barModelFactory->supportsObjects([$foos[0], $foos[1]]));
        $this->assertFalse($barModelFactory->supportsObjects([$foos[0], $foos[1], $foos[2]]));
        $this->assertFalse($barModelFactory->supportsObjects([$foos[0], $foos[1], $bars[0]]));
        $this->assertTrue($barModelFactory->supportsObjects([$bars[0], $bars[1]]));

        $this->assertTrue($fooModelFactory->supportsObjects(new ArrayObject([$foos[0]])));
        $this->assertTrue($fooModelFactory->supportsObjects(new ArrayObject([$foos[0], $foos[1]])));
        $this->assertTrue($fooModelFactory->supportsObjects(new ArrayObject([$foos[0], $foos[1], $foos[2]])));
        $this->assertFalse($fooModelFactory->supportsObjects(new ArrayObject([$foos[0], $foos[1], $bars[0]])));
        $this->assertFalse($fooModelFactory->supportsObjects(new ArrayObject([$bars[0], $bars[1]])));

        $this->assertFalse($barModelFactory->supportsObjects(new ArrayObject([$foos[0]])));
        $this->assertFalse($barModelFactory->supportsObjects(new ArrayObject([$foos[0], $foos[1]])));
        $this->assertFalse($barModelFactory->supportsObjects(new ArrayObject([$foos[0], $foos[1], $foos[2]])));
        $this->assertFalse($barModelFactory->supportsObjects(new ArrayObject([$foos[0], $foos[1], $bars[0]])));
        $this->assertTrue($barModelFactory->supportsObjects(new ArrayObject([$bars[0], $bars[1]])));
    }

    /**
     * @dataProvider dataProvider_supportsObjects_onInvalidArgument
     * @expectedException \Xsolve\ModelFactoryBundle\ModelFactory\Exception\ModelFactoryException
     */
    public function test_supportsObjects_onInvalidArgument($invalidArgument)
    {
        $fooModelFactory = new FooModelFactory();

        $fooModelFactory->supportsObjects($invalidArgument);
    }

    /**
     * @return array
     */
    public function dataProvider_supportsObjects_onInvalidArgument()
    {
        return [
            ['string'],
            [3],
            [false],
            [new stdClass()],
        ];
    }
}
