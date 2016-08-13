<?php

namespace Xsolve\ModelFactoryBundle\Tests\Functional\Mock\Helper;

use Xsolve\ModelFactoryBundle\Tests\Functional\Mock\Object\VolumeInterface;

class VolumetricWeightHelper
{
    /** @var float */
    protected $weightToVolumeCoef;

    /** @var int */
    protected $precision;

    /**
     * @param float $weightToVolumeCoef
     * @param int   $precision
     */
    public function __construct($weightToVolumeCoef, $precision)
    {
        $this->weightToVolumeCoef = (float) $weightToVolumeCoef;
        $this->precision = (int) $precision;
    }

    /**
     * @return float
     */
    public function getVolumetricWeight(VolumeInterface $object)
    {
        return round(
            $this->weightToVolumeCoef * $object->getLength() * $object->getWidth() * $object->getHeight(),
            $this->precision
        );
    }
}
