<?php

namespace Xsolve\ModelFactoryBundle\Tests\Functional;

use ArrayObject;
use PHPUnit_Framework_TestCase;
use stdClass;
use Xsolve\ModelFactoryBundle\ModelFactoryCollection\Exception\ModelFactoryCollectionException;
use Xsolve\ModelFactoryBundle\ModelFactoryCollection\ModelFactoryCollection;
use Xsolve\ModelFactoryBundle\Tests\Functional\Mock\Helper\VolumetricWeightHelper;
use Xsolve\ModelFactoryBundle\Tests\Functional\Mock\Model\BarModel;
use Xsolve\ModelFactoryBundle\Tests\Functional\Mock\Model\FooModel;
use Xsolve\ModelFactoryBundle\Tests\Functional\Mock\ModelFactory\BarModelFactory;
use Xsolve\ModelFactoryBundle\Tests\Functional\Mock\ModelFactory\BazModelFactory;
use Xsolve\ModelFactoryBundle\Tests\Functional\Mock\ModelFactory\FooModelFactory;
use Xsolve\ModelFactoryBundle\Tests\Functional\Mock\Object\Bar;
use Xsolve\ModelFactoryBundle\Tests\Functional\Mock\Object\Baz;
use Xsolve\ModelFactoryBundle\Tests\Functional\Mock\Object\Foo;

class ModelFactoryCollectionTest extends PHPUnit_Framework_TestCase
{
    public function test_supportsObject()
    {
        $modelFactoryCollection = new ModelFactoryCollection();
        $modelFactoryCollection
            ->addModelFactory(
                new FooModelFactory()
            )
            ->addModelFactory(
                new BarModelFactory()
            );

        $foo = new Foo('foo.1', 'lorem');
        $bar = new Bar('bar.1', 12);
        $baz = new Baz('baz.1', 1.0, 0.2, 0.2, 0.2);

        $this->assertTrue($modelFactoryCollection->supportsObject($foo));
        $this->assertTrue($modelFactoryCollection->supportsObject($bar));
        $this->assertFalse($modelFactoryCollection->supportsObject($baz));
    }

    public function test_supportsObjects()
    {
        $modelFactoryCollection = new ModelFactoryCollection();
        $modelFactoryCollection
            ->addModelFactory(
                new FooModelFactory()
            )
            ->addModelFactory(
                new BarModelFactory()
            );

        $foo = new Foo('foo.1', 'lorem');
        $bar = new Bar('bar.1', 12);
        $baz = new Baz('baz.1', 1.0, 0.2, 0.2, 0.2);

        $this->assertTrue($modelFactoryCollection->supportsObjects([$foo]));
        $this->assertTrue($modelFactoryCollection->supportsObjects([$bar]));
        $this->assertFalse($modelFactoryCollection->supportsObjects([$baz]));
        $this->assertTrue($modelFactoryCollection->supportsObjects([$foo, $bar]));
        $this->assertFalse($modelFactoryCollection->supportsObjects([$foo, $baz]));
        $this->assertFalse($modelFactoryCollection->supportsObjects([$bar, $baz]));
        $this->assertFalse($modelFactoryCollection->supportsObjects([$foo, $bar, $baz]));

        $this->assertTrue($modelFactoryCollection->supportsObjects(new ArrayObject([$foo])));
        $this->assertTrue($modelFactoryCollection->supportsObjects(new ArrayObject([$bar])));
        $this->assertFalse($modelFactoryCollection->supportsObjects(new ArrayObject([$baz])));
        $this->assertTrue($modelFactoryCollection->supportsObjects(new ArrayObject([$foo, $bar])));
        $this->assertFalse($modelFactoryCollection->supportsObjects(new ArrayObject([$foo, $baz])));
        $this->assertFalse($modelFactoryCollection->supportsObjects(new ArrayObject([$bar, $baz])));
        $this->assertFalse($modelFactoryCollection->supportsObjects(new ArrayObject([$foo, $bar, $baz])));

    }

    public function test_createModels()
    {
        $modelFactoryCollection = new ModelFactoryCollection();
        $modelFactoryCollection
            ->addModelFactory(
                new FooModelFactory()
            )
            ->addModelFactory(
                new BarModelFactory()
            );

        $foosOrBars = [
            new Foo('foo.1', 'lorem'),
            new Foo('foo.2', 'ipsum'),
            new Bar('bar.1', 7),
            new Bar('bar.2', 12),
        ];

        $fooOrBarModelBatches = [];

        $fooOrBarModelBatches[] = $modelFactoryCollection->createModels($foosOrBars);
        $fooOrBarModelBatches[] = $modelFactoryCollection->createModels(new ArrayObject($foosOrBars));

        foreach ($fooOrBarModelBatches as $fooOrBarModels) {
            foreach ($foosOrBars as $index => $fooOrBar) {
                $fooOrBarModel = $fooOrBarModels[$index];
                if ($fooOrBar instanceof Foo) {
                    $this->assertTrue($fooOrBarModel instanceof FooModel);
                    /* @var FooModel $fooOrBarModel */
                    $this->assertEquals($fooOrBar->getId(), $fooOrBarModel->getId());
                    $this->assertEquals($fooOrBar->getName(), $fooOrBarModel->getName());
                } elseif ($fooOrBar instanceof Bar) {
                    $this->assertTrue($fooOrBarModel instanceof BarModel);
                    /* @var BarModel $fooOrBarModel */
                    $this->assertEquals($fooOrBar->getId(), $fooOrBarModel->getId());
                    $this->assertEquals($fooOrBar->getValue(), $fooOrBarModel->getValue());
                } else {
                    $this->fail();
                }
            }
        }
    }

    /**
     * @expectedException \Xsolve\ModelFactoryBundle\ModelFactoryCollection\Exception\ModelFactoryCollectionException
     */
    public function test_createModels_onUnsupportedObject()
    {
        $modelFactoryCollection = new ModelFactoryCollection();
        $modelFactoryCollection
            ->addModelFactory(
                new FooModelFactory()
            )
            ->addModelFactory(
                new BarModelFactory()
            );

        $foosOrBarsOrBazs = [
            new Foo('foo.1', 'lorem'),
            new Foo('foo.2', 'ipsum'),
            new Bar('bar.1', 7),
            new Bar('bar.2', 12),
            new Baz('baz.1', 1.0, 0.2, 0.2, 0.2),
        ];

        $fooOrBarModelBatches = [];

        $fooOrBarModelBatches[] = $modelFactoryCollection->createModels($foosOrBarsOrBazs);
    }

    /**
     * @param mixed $invalidArgument
     * @dataProvider dataProvider_supportsObjects_onInvalidArgument
     * @expectedException \Xsolve\ModelFactoryBundle\ModelFactoryCollection\Exception\ModelFactoryCollectionException
     */
    public function test_supportsObjects_onInvalidArgument($invalidArgument)
    {
        $modelFactoryCollection = new ModelFactoryCollection();
        $modelFactoryCollection
            ->addModelFactory(
                new FooModelFactory()
            )
            ->addModelFactory(
                new BarModelFactory()
            );

        $modelFactoryCollection->supportsObjects($invalidArgument);
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

    /**
     * @param mixed $invalidArgument
     * @dataProvider dataProvider_createModels_onInvalidArgument
     * @expectedException \Xsolve\ModelFactoryBundle\ModelFactoryCollection\Exception\ModelFactoryCollectionException
     */
    public function test_createModels_onInvalidArgument($invalidArgument)
    {
        $modelFactoryCollection = new ModelFactoryCollection();
        $modelFactoryCollection
            ->addModelFactory(
                new FooModelFactory()
            )
            ->addModelFactory(
                new BarModelFactory()
            );

        $modelFactoryCollection->createModels($invalidArgument);
    }

    /**
     * @return array
     */
    public function dataProvider_createModels_onInvalidArgument()
    {
        return $this->dataProvider_supportsObjects_onInvalidArgument();
    }
}
