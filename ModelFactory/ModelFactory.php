<?php

namespace Xsolve\ModelFactoryBundle\ModelFactory;

use InvalidArgumentException;
use Traversable;

abstract class ModelFactory implements ModelFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    abstract public function supportsObject($object);

    /**
     * {@inheritdoc}
     */
    public function supportsObjects($objects)
    {
        if (!is_array($objects) && !$objects instanceof Traversable) {
            throw new InvalidArgumentException();
        }
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
    public function createModel($object)
    {
        $model = $this->instantiateModel($object);
        $this->injectModelFactory($model);

        return $model;
    }

    /**
     * {@inheritdoc}
     */
    public function createModels($objects)
    {
        return array_map(
            [$this, 'createModel'],
            $objects
        );
    }

    /**
     * @param mixed $object
     *
     * @return mixed
     */
    abstract protected function instantiateModel($object);

    /**
     * @param mixed $model
     */
    protected function injectModelFactory($model)
    {
        if ($model instanceof ModelFactoryAwareModelInterface) {
            $model->setModelFactory($this);
        }
    }
}
