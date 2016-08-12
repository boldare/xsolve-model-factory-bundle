<?php

namespace Xsolve\ModelFactoryBundle\Tests\Functional\Mock\ModelFactory;

use Xsolve\ModelFactoryBundle\ModelFactory\ModelFactory;
use Xsolve\ModelFactoryBundle\Tests\Functional\Mock\Helper\VolumetricWeightHelper;
use Xsolve\ModelFactoryBundle\Tests\Functional\Mock\Model\BarModel;
use Xsolve\ModelFactoryBundle\Tests\Functional\Mock\Object\Bar;

class BarModelFactory extends ModelFactory
{
    /**
     * {@inheritdoc}
     */
    public function supportsObject($object)
    {
        return $object instanceof Bar;
    }

    /**
     * @param Bar $object
     * @return BarModel
     */
    protected function instantiateModel($object)
    {
        return new BarModel($object);
    }
}
