<?php

namespace Xsolve\ModelFactoryBundle\Tests\Functional\Mock\Object;

class Foo
{
    /** @var string */
    protected $id;

    /** @var string */
    protected $name;

    /** @var Bar[] */
    protected $barCollection;

    /**
     * @param string $id
     * @param string $name
     */
    public function __construct($id, $name)
    {
        $this->id = $id;
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }


    /**
     * @param Bar $bar
     * @return $this
     */
    public function addBar(Bar $bar)
    {
        $this->barCollection[] = $bar;

        return $this;
    }

    /**
     * @return Bar[]
     */
    public function getBarCollection()
    {
        return $this->barCollection;
    }
}
