<?php

namespace Xsolve\ModelFactoryBundle\Tests\Functional\Mock\Model;

use Xsolve\ModelFactoryBundle\ModelFactoryCollection\ModelFactoryCollectionAwareModelInterface;
use Xsolve\ModelFactoryBundle\ModelFactoryCollection\ModelFactoryCollectionAwareModelTrait;
use Xsolve\ModelFactoryBundle\Tests\Functional\Mock\Object\Bar;

class BarModel implements ModelFactoryCollectionAwareModelInterface
{
    use ModelFactoryCollectionAwareModelTrait;

    /** @var Bar */
    protected $bar;

    /**
     * @param Bar $bar
     */
    public function __construct(Bar $bar)
    {
        $this->bar = $bar;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->bar->getId();
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->bar->getName();
    }

    /**
     * @return BazModel[]
     */
    public function getBazModelCollection()
    {
        return $this
            ->getModelFactoryCollection()
            ->createModels(
                $this->bar->getBazCollection()
            );
    }
}
