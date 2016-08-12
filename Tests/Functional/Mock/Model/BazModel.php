<?php

namespace Xsolve\ModelFactoryBundle\Tests\Functional\Mock\Model;

use Xsolve\ModelFactoryBundle\ModelFactory\ModelFactoryAwareModelInterface;
use Xsolve\ModelFactoryBundle\ModelFactory\ModelFactoryAwareModelTrait;
use Xsolve\ModelFactoryBundle\Tests\Functional\Mock\Object\Baz;

class BazModel implements ModelFactoryAwareModelInterface
{
    use ModelFactoryAwareModelTrait;

    /** @var Baz */
    protected $baz;

    /**
     * @param Baz $baz
     */
    public function __construct(Baz $baz)
    {
        $this->baz = $baz;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->baz->getId();
    }

    /**
     * @return float
     */
    public function getWeight()
    {
        return $this->baz->getWeight();
    }

    /**
     * @return float
     */
    public function getVolumetricWeight()
    {
        return $this->getModelFactory()->getBazVolumetricWeight($this->baz);
    }
}
