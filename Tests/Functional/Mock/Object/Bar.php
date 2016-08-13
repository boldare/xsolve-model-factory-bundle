<?php

namespace Xsolve\ModelFactoryBundle\Tests\Functional\Mock\Object;

class Bar
{
    /** @var string */
    protected $id;

    /** @var int */
    protected $value;

    /** @var Baz[] */
    protected $bazCollection = [];

    /**
     * @param string $id
     * @param int    $value
     */
    public function __construct($id, $value)
    {
        $this->id = $id;
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param Baz $baz
     *
     * @return $this
     */
    public function addBaz(Baz $baz)
    {
        $this->bazCollection[] = $baz;

        return $this;
    }

    /**
     * @return Baz[]
     */
    public function getBazCollection()
    {
        return $this->bazCollection;
    }
}
