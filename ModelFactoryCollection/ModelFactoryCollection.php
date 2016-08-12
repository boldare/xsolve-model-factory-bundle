<?php

namespace Xsolve\ModelFactoryBundle\ModelFactoryCollection;

use Exception;
use InvalidArgumentException;
use Traversable;
use Xsolve\ModelFactoryBundle\ModelFactory\ModelFactoryInterface;
use Xsolve\ModelFactoryBundle\ModelFactoryCollection\Exception\ModelFactoryCollectionException;

class ModelFactoryCollection implements ModelFactoryCollectionInterface
{
    /** @var ModelFactoryInterface[] */
    protected $modelFactories = [];

    /**
     * {@inheritdoc}
     */
    public function addModelFactory(ModelFactoryInterface $modelFactory)
    {
        $this->modelFactories[] = $modelFactory;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsObject($object)
    {
        foreach ($this->modelFactories as $modelFactory) {
            if ($modelFactory->supportsObject($object)) {
                return true;
            }
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsObjects($objects)
    {
        foreach ($objects as $object) {
            if (!$this->supportsObject($object)) {
                return false;
            }
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function createModels($objects)
    {
        if ($objects instanceof Traversable) {
            $objects = iterator_to_array($objects);
        } elseif (!is_array($objects)) {
            throw new InvalidArgumentException('An array or an instance of Traversable expected.');
        }

        // First try to find a single model factory supporting all items.
        $modelFactory = $this->findSupportingModelFactoryForObjects($objects);
        if ($modelFactory instanceof ModelFactoryInterface) {
            return $this->createModelsAndSetDependencies($modelFactory, $objects);
        }

        // If that fails, try to find model factory for each item individually.
        $models = [];
        foreach ($objects as $object) {
            $modelFactory = $this->findSupportingModelFactoryForObject($object);
            if (!$modelFactory instanceof ModelFactoryInterface) {
                throw new ModelFactoryCollectionException(sprintf(
                    "Model factory for class '%s' not found.",
                    get_class($object)
                ));
            }
            $models[] = $this->createModelAndSetDependencies($modelFactory, $object);
        }

        foreach ($models as $model) {
            if ($model instanceof ModelFactoryCollectionAwareModelInterface) {
                $model->setModelFactoryCollection($this);
            }
        }

        return $models;
    }

    /**
     * {@inheritdoc}
     */
    public function createModel($object)
    {
        $models = $this->createModels([$object]);

        return reset($models);
    }

    /**
     * @param ModelFactoryInterface $modelFactory
     * @param array                 $objects
     *
     * @return array
     */
    protected function createModelsAndSetDependencies(ModelFactoryInterface $modelFactory, array $objects)
    {
        $models = $modelFactory->createModels($objects);
        foreach ($models as $model) {
            if ($model instanceof ModelFactoryCollectionAwareModelInterface) {
                $model->setModelFactoryCollection($this);
            }
        }

        return $models;
    }

    /**
     * @param ModelFactoryInterface $modelFactory
     * @param mixed                 $object
     *
     * @return mixed
     */
    protected function createModelAndSetDependencies(ModelFactoryInterface $modelFactory, $object)
    {
        $models = $this->createModelsAndSetDependencies($modelFactory, [$object]);

        return reset($models);
    }

    /**
     * @param array|Traversable $objects
     *
     * @return ModelFactoryInterface
     */
    protected function findSupportingModelFactoryForObjects($objects)
    {
        foreach ($this->modelFactories as $modelFactory) {
            if ($modelFactory->supportsObjects($objects)) {
                return $modelFactory;
            }
        }
    }

    /**
     * @param mixed $object
     *
     * @return ModelFactoryInterface|null
     */
    protected function findSupportingModelFactoryForObject($object)
    {
        foreach ($this->modelFactories as $modelFactory) {
            if ($modelFactory->supportsObject($object)) {
                return $modelFactory;
            }
        }
    }
}
