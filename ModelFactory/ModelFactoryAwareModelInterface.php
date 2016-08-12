<?php

namespace Xsolve\ModelFactoryBundle\ModelFactory;

interface ModelFactoryAwareModelInterface
{
    /**
     * @param ModelFactoryInterface $modelFactory
     */
    public function setModelFactory(ModelFactoryInterface $modelFactory);
}
