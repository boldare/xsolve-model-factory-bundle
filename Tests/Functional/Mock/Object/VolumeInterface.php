<?php

namespace Xsolve\ModelFactoryBundle\Tests\Functional\Mock\Object;

interface VolumeInterface
{
    /**
     * @return float
     */
    public function getLength();

    /**
     * @return float
     */
    public function getWidth();

    /**
     * @return float
     */
    public function getHeight();
}
