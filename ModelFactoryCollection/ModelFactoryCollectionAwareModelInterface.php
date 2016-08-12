<?php

namespace Xsolve\ModelFactoryBundle\ModelFactoryCollection;

interface ModelFactoryCollectionAwareModelInterface
{
    /**
     * @param ModelFactoryCollectionInterface $modelFactoryCollection
     */
    public function setModelFactoryCollection(ModelFactoryCollectionInterface $modelFactoryCollection);
}
