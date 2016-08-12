<?php

namespace Xsolve\ModelFactoryBundle\Tests\Functional\Mock\ModelFactory;

use Xsolve\ModelFactoryBundle\ModelFactory\ModelFactory;
use Xsolve\ModelFactoryBundle\Tests\Functional\Mock\Helper\VolumetricWeightHelper;
use Xsolve\ModelFactoryBundle\Tests\Functional\Mock\Model\FooModel;
use Xsolve\ModelFactoryBundle\Tests\Functional\Mock\Object\Foo;

class FooModelFactory extends ModelFactory
{
    /**
     * {@inheritdoc}
     */
    public function supportsObject($object)
    {
        return $object instanceof Foo;
    }

    /**
     * @param Foo $object
     * @return FooModel
     */
    protected function instantiateModel($object)
    {
        return new FooModel($object);
    }
}
