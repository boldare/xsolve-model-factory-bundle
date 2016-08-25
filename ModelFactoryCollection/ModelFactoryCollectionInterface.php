<?php

namespace Xsolve\ModelFactoryBundle\ModelFactoryCollection;

use Xsolve\ModelFactoryBundle\ModelFactory\ModelFactoryInterface;

interface ModelFactoryCollectionInterface extends ModelFactoryInterface
{
    /**
     * @param ModelFactoryInterface $modelFactory
     * 
     * @return $this
     */
    public function addModelFactory(ModelFactoryInterface $modelFactory);
}
