<?php

namespace Xsolve\ModelFactoryBundle\Tests\Functional;

use PHPUnit_Framework_TestCase;
use Xsolve\ModelFactoryBundle\ModelFactoryCollection\ModelFactoryCollection;
use Xsolve\ModelFactoryBundle\Tests\Functional\Mock\Helper\VolumetricWeightHelper;
use Xsolve\ModelFactoryBundle\Tests\Functional\Mock\Model\BarModel;
use Xsolve\ModelFactoryBundle\Tests\Functional\Mock\Model\BazModel;
use Xsolve\ModelFactoryBundle\Tests\Functional\Mock\Model\FooModel;
use Xsolve\ModelFactoryBundle\Tests\Functional\Mock\ModelFactory\BarModelFactory;
use Xsolve\ModelFactoryBundle\Tests\Functional\Mock\ModelFactory\BazModelFactory;
use Xsolve\ModelFactoryBundle\Tests\Functional\Mock\ModelFactory\FooModelFactory;
use Xsolve\ModelFactoryBundle\Tests\Functional\Mock\Object\Bar;
use Xsolve\ModelFactoryBundle\Tests\Functional\Mock\Object\Baz;
use Xsolve\ModelFactoryBundle\Tests\Functional\Mock\Object\Foo;

class ModelCollectionFactoryAwareModelTest extends PHPUnit_Framework_TestCase
{
    public function test_createModel()
    {
        $modelFactoryCollection = new ModelFactoryCollection();
        $modelFactoryCollection
            ->addModelFactory(
                new FooModelFactory()
            )
            ->addModelFactory(
                new BarModelFactory()
            )
            ->addModelFactory(
                new BazModelFactory(new VolumetricWeightHelper(5000, 2))
            );

        $foo = new Foo('foo.1', 'lorem');

        $bars = [
            new Bar('bar.1', 7),
            new Bar('bar.2', 12),
        ];

        $bazs = [
            [
                new Baz('baz.1', 1.0, 0.2, 0.2, 0.2),
            ],
            [
                new Baz('baz.2', 1.0, 0.3, 0.5, 0.11111),
            ],
        ];

        $expectedBazVolumetricWeights = [
            [
                40.0,
            ],
            [
                83.33,
            ],
        ];

        foreach ($bars as $index => $bar) {
            $foo->addBar($bar);
            foreach ($bazs[$index] as $baz) {
                $bar->addBaz($baz);
            }
        }

        $this->assertTrue($modelFactoryCollection->supportsObject($foo));

        $fooModel = $modelFactoryCollection->createModel($foo);

        $this->assertTrue($fooModel instanceof FooModel);
        /* @var FooModel $fooModel */
        $this->assertEquals($foo->getId(), $fooModel->getId());
        $this->assertEquals($foo->getName(), $fooModel->getName());

        $barModels = $fooModel->getBarModelCollection();
        foreach ($bars as $index1 => $bar) {
            /* @var Bar $bar */
            $barModel = $barModels[$index1];
            $this->assertTrue($barModel instanceof BarModel);
            /* @var BarModel $barModel */
            $this->assertEquals($bar->getId(), $barModel->getId());
            $this->assertEquals($bar->getValue(), $barModel->getValue());
            $bazModels = $barModel->getBazModelCollection();
            foreach ($bazs[$index1] as $index2 => $baz) {
                /* @var Baz $baz */
                $bazModel = $bazModels[$index2];
                $this->assertTrue($bazModel instanceof BazModel);
                /* @var BazModel $bazModel */
                $this->assertEquals($baz->getId(), $bazModel->getId());
                $this->assertEquals($baz->getWeight(), $bazModel->getWeight());
                $this->assertEquals($expectedBazVolumetricWeights[$index1][$index2], $bazModel->getVolumetricWeight());
            }
        }
    }
}
