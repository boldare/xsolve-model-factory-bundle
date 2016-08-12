<?php

namespace Xsolve\ModelFactoryBundle\ModelFactoryCollection;

trait ModelFactoryCollectionAwareModelTrait
{
    /** @var ModelFactoryCollectionInterface $modelFactoryCollection */
    protected $modelFactoryCollection;

    /**
     * @param ModelFactoryCollectionInterface $modelFactoryCollection
     */
    public function setModelFactoryCollection(ModelFactoryCollectionInterface $modelFactoryCollection)
    {
        $this->modelFactoryCollection = $modelFactoryCollection;
    }

    /**
     * @return ModelFactoryCollectionInterface
     */
    public function getModelFactoryCollection()
    {
        return $this->modelFactoryCollection;
    }
}
