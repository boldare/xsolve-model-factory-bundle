<?php

namespace Xsolve\ModelFactoryBundle\Tests\Functional;

use ArrayObject;
use InvalidArgumentException;
use PHPUnit_Framework_TestCase;
use stdClass;
use Xsolve\ModelFactoryBundle\Tests\Functional\Mock\Helper\VolumetricWeightHelper;
use Xsolve\ModelFactoryBundle\Tests\Functional\Mock\Model\BazModel;
use Xsolve\ModelFactoryBundle\Tests\Functional\Mock\ModelFactory\BarModelFactory;
use Xsolve\ModelFactoryBundle\Tests\Functional\Mock\ModelFactory\BazModelFactory;
use Xsolve\ModelFactoryBundle\Tests\Functional\Mock\ModelFactory\FooModelFactory;
use Xsolve\ModelFactoryBundle\Tests\Functional\Mock\Object\Bar;
use Xsolve\ModelFactoryBundle\Tests\Functional\Mock\Object\Baz;
use Xsolve\ModelFactoryBundle\Tests\Functional\Mock\Object\Foo;

class ModelFactoryAwareModelTest extends PHPUnit_Framework_TestCase
{
    public function test_createModel()
    {
        $bazModelFactory = new BazModelFactory(new VolumetricWeightHelper(5000, 2));

        $baz = new Baz('baz.1', 1.0, 0.2, 0.2, 0.2);

        $this->assertTrue($bazModelFactory->supportsObject($baz));

        $bazModel = $bazModelFactory->createModel($baz);

        $this->assertTrue($bazModel instanceof BazModel);
        /* @var BazModel $bazModel */
        $this->assertEquals(40.0, $bazModel->getVolumetricWeight());
    }

    public function test_createModels()
    {
        $bazModelFactory = new BazModelFactory(new VolumetricWeightHelper(5000, 2));

        /* @var Baz[] $bazs */
        $bazs = [
            new Baz('baz.1', 1.0, 0.2, 0.2, 0.2),
            new Baz('baz.2', 1.0, 0.3, 0.5, 0.11111),
        ];

        $this->assertTrue($bazModelFactory->supportsObjects($bazs));

        $bazModels = $bazModelFactory->createModels($bazs);

        /* @var BazModel[] $bazModels */
        foreach ($bazs as $index => $baz) {
            $bazModel = $bazModels[$index];
            $this->assertTrue($bazModel instanceof BazModel);
            $this->assertEquals($baz->getId(), $bazModel->getId());
            $this->assertEquals($baz->getWeight(), $bazModel->getWeight());
        }
        $this->assertEquals(40.0, $bazModels[0]->getVolumetricWeight());
        $this->assertEquals(83.33, $bazModels[1]->getVolumetricWeight());
    }
}
