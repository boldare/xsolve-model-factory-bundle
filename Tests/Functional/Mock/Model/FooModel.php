<?php

namespace Xsolve\ModelFactoryBundle\Tests\Functional\Mock\Model;

use Xsolve\ModelFactoryBundle\ModelFactoryCollection\ModelFactoryCollectionAwareModelInterface;
use Xsolve\ModelFactoryBundle\ModelFactoryCollection\ModelFactoryCollectionAwareModelTrait;
use Xsolve\ModelFactoryBundle\Tests\Functional\Mock\Object\Foo;

class FooModel implements ModelFactoryCollectionAwareModelInterface
{
    use ModelFactoryCollectionAwareModelTrait;

    /** @var Foo */
    protected $foo;

    /**
     * @param Foo $foo
     */
    public function __construct(Foo $foo)
    {
        $this->foo = $foo;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->foo->getId();
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->foo->getName();
    }

    /**
     * @return BarModel[]
     */
    public function getBarModelCollection()
    {
        return $this
            ->getModelFactoryCollection()
            ->createModels(
                $this->foo->getBarCollection()
            );
    }
}
