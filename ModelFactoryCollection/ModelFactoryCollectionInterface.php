<?php

namespace Xsolve\ModelFactoryBundle\ModelFactoryCollection;

use Xsolve\ModelFactoryBundle\ModelFactory\ModelFactoryInterface;

interface ModelFactoryCollectionInterface extends ModelFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function addModelFactory(ModelFactoryInterface $modelFactory);
}
