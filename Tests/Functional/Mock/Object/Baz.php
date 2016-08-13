<?php

namespace Xsolve\ModelFactoryBundle\Tests\Functional\Mock\Object;

class Baz implements WeightInterface, VolumeInterface
{
    /** @var string */
    protected $id;

    /** @var float */
    protected $weight;

    /** @var float */
    protected $length;

    /** @var float */
    protected $width;

    /** @var float */
    protected $height;

    /**
     * @param string $id
     * @param float  $weight
     * @param float  $length
     * @param float  $width
     * @param float  $height
     */
    public function __construct($id, $weight, $length, $width, $height)
    {
        $this->id = $id;
        $this->weight = $weight;
        $this->length = $length;
        $this->width = $width;
        $this->height = $height;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getWeight()
    {
        return $this->weight;
    }

    /**
     * {@inheritdoc}
     */
    public function getLength()
    {
        return $this->length;
    }

    /**
     * {@inheritdoc}
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * {@inheritdoc}
     */
    public function getHeight()
    {
        return $this->height;
    }
}
