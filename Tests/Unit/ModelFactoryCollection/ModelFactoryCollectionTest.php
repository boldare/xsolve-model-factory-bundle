<?php

namespace Xsolve\ModelFactoryBundle\Tests\Unit\ModelFactoryCollection;

use ArrayObject;
use PHPUnit_Framework_TestCase;
use Prophecy\Argument;
use SplObjectStorage;
use stdClass;
use Traversable;
use Xsolve\ModelFactoryBundle\ModelFactory\ModelFactoryInterface;
use Xsolve\ModelFactoryBundle\ModelFactoryCollection\ModelFactoryCollection;
use Xsolve\ModelFactoryBundle\Tests\Unit\Mock\Model\BarModel;
use Xsolve\ModelFactoryBundle\Tests\Unit\Mock\Model\BazModel;
use Xsolve\ModelFactoryBundle\Tests\Unit\Mock\Model\FooModel;
use Xsolve\ModelFactoryBundle\Tests\Unit\Mock\Object\Bar;
use Xsolve\ModelFactoryBundle\Tests\Unit\Mock\Object\Baz;
use Xsolve\ModelFactoryBundle\Tests\Unit\Mock\Object\Foo;

class ModelFactoryCollectionTest extends PHPUnit_Framework_TestCase
{
    public function test_addModelFactory_and_supportsObject()
    {
        $foo = new Foo();
        $bar = new Bar();
        $baz = new Baz();

        $fooModelFactory = $this->prophesize(ModelFactoryInterface::class);
        $fooModelFactory
            ->supportsObject($foo)
            ->willReturn(true);
        $fooModelFactory
            ->supportsObject($bar)
            ->willReturn(false);
        $fooModelFactory
            ->supportsObject($baz)
            ->willReturn(false);
        $fooModelFactory = $fooModelFactory->reveal();

        $barModelFactory = $this->prophesize(ModelFactoryInterface::class);
        $barModelFactory
            ->supportsObject($foo)
            ->willReturn(false);
        $barModelFactory
            ->supportsObject($bar)
            ->willReturn(true);
        $barModelFactory
            ->supportsObject($baz)
            ->willReturn(false);
        $barModelFactory = $barModelFactory->reveal();

        $modelFactoryCollection = new ModelFactoryCollection();

        $this->assertFalse($modelFactoryCollection->supportsObject($foo));
        $this->assertFalse($modelFactoryCollection->supportsObject($bar));
        $this->assertFalse($modelFactoryCollection->supportsObject($baz));

        $modelFactoryCollection->addModelFactory($fooModelFactory);

        $this->assertTrue($modelFactoryCollection->supportsObject($foo));
        $this->assertFalse($modelFactoryCollection->supportsObject($bar));
        $this->assertFalse($modelFactoryCollection->supportsObject($baz));

        $modelFactoryCollection->addModelFactory($barModelFactory);

        $this->assertTrue($modelFactoryCollection->supportsObject($foo));
        $this->assertTrue($modelFactoryCollection->supportsObject($bar));
        $this->assertFalse($modelFactoryCollection->supportsObject($baz));
    }

    public function test_supportsObjects()
    {
        $foo = new Foo();
        $bar = new Bar();
        $baz = new Baz();

        $fooModelFactory = $this->prophesize(ModelFactoryInterface::class);
        $fooModelFactory
            ->supportsObject($foo)
            ->willReturn(true);
        $fooModelFactory
            ->supportsObject($bar)
            ->willReturn(false);
        $fooModelFactory
            ->supportsObject($baz)
            ->willReturn(false);
        $fooModelFactory
            ->supportsObjects(Argument::that(
                function ($objects) {
                    return is_array($objects) || $objects instanceof Traversable;
                }
            ))
            ->will(function (array $args) use ($foo) {
                foreach ($args[0] as $object) {
                    if ($object !== $foo) {
                        return false;
                    }
                }

                return true;
            });
        $fooModelFactory = $fooModelFactory->reveal();

        $barModelFactory = $this->prophesize(ModelFactoryInterface::class);
        $barModelFactory
            ->supportsObject($foo)
            ->willReturn(false);
        $barModelFactory
            ->supportsObject($bar)
            ->willReturn(true);
        $barModelFactory
            ->supportsObject($baz)
            ->willReturn(false);
        $barModelFactory
            ->supportsObjects(
                Argument::that(function ($objects) {
                    return is_array($objects) || $objects instanceof Traversable;
                })
            )
            ->will(function (array $args) use ($bar) {
                foreach ($args[0] as $object) {
                    if ($object !== $bar) {
                        return false;
                    }
                }

                return true;
            });
        $barModelFactory = $barModelFactory->reveal();

        $modelFactoryCollection = new ModelFactoryCollection();
        $modelFactoryCollection
            ->addModelFactory($fooModelFactory)
            ->addModelFactory($barModelFactory);

        $this->assertTrue($modelFactoryCollection->supportsObjects([$foo]));
        $this->assertTrue($modelFactoryCollection->supportsObjects([$foo, $foo]));
        $this->assertTrue($modelFactoryCollection->supportsObjects([$foo, $bar]));
        $this->assertTrue($modelFactoryCollection->supportsObjects([$foo, $foo, $bar, $bar]));
        $this->assertFalse($modelFactoryCollection->supportsObjects([$foo, $bar, $baz]));
        $this->assertFalse($modelFactoryCollection->supportsObjects([$foo, $baz]));
        $this->assertFalse($modelFactoryCollection->supportsObjects([$baz]));

        $this->assertTrue($modelFactoryCollection->supportsObjects(new ArrayObject([$foo])));
        $this->assertTrue($modelFactoryCollection->supportsObjects(new ArrayObject([$foo, $foo])));
        $this->assertTrue($modelFactoryCollection->supportsObjects(new ArrayObject([$foo, $bar])));
        $this->assertTrue($modelFactoryCollection->supportsObjects(new ArrayObject([$foo, $foo, $bar, $bar])));
        $this->assertFalse($modelFactoryCollection->supportsObjects(new ArrayObject([$foo, $bar, $baz])));
        $this->assertFalse($modelFactoryCollection->supportsObjects(new ArrayObject([$foo, $baz])));
        $this->assertFalse($modelFactoryCollection->supportsObjects(new ArrayObject([$baz])));
    }

    /**
     * @param mixed $invalidArgument
     *
     * @dataProvider dataProvider_supportsObjects_onInvalidArgument
     * @expectedException \Xsolve\ModelFactoryBundle\ModelFactoryCollection\Exception\ModelFactoryCollectionException
     */
    public function test_supportsObjects_onInvalidArgument($invalidArgument)
    {
        $modelFactoryCollection = new ModelFactoryCollection();

        $modelFactoryCollection->supportsObjects($invalidArgument);
    }

    /**
     * @return array
     */
    public function dataProvider_supportsObjects_onInvalidArgument()
    {
        return [
            ['string'],
            [3],
            [false],
            [new stdClass()],
        ];
    }

    public function test_createModel_and_createModels()
    {
        $foos = [
            new Foo(),
            new Foo(),
        ];
        $bars = [
            new Bar(),
            new Bar(),
        ];
        $bazs = [
            new Baz(),
            new Baz(),
        ];

        $fooModelsMap = new SplObjectStorage();
        $barModelsMap = new SplObjectStorage();
        $bazModelsMap = new SplObjectStorage();

        $fooModelFactory = $this->prophesize(ModelFactoryInterface::class);
        $fooModelFactory
            ->supportsObject(Argument::type(Foo::class))
            ->willReturn(true);
        $fooModelFactory
            ->supportsObject(Argument::that(
                function ($object) {
                    return !$object instanceof Foo;
                }
            ))
            ->willReturn(false);
        $fooModelFactory
            ->supportsObjects(Argument::that(
                function ($objects) {
                    return is_array($objects) || $objects instanceof Traversable;
                }
            ))
            ->will(
                function (array $args) {
                    foreach ($args[0] as $object) {
                        if (!$object instanceof Foo) {
                            return false;
                        }
                    }

                    return true;
                }
            );
        $fooModelFactory
            ->createModel(Argument::type(Foo::class))
            ->will(function ($args) use ($fooModelsMap) {
                return $fooModelsMap->offsetGet($args[0]);
            });
        $fooModelFactory
            ->createModels(Argument::that(
                function ($objects) {
                    if (!is_array($objects) && !$objects instanceof Traversable) {
                        return false;
                    }
                    foreach ($objects as $object) {
                        if (!$object instanceof Foo) {
                            return false;
                        }
                    }

                    return true;
                }
            ))
            ->will(
                function (array $args) use ($fooModelsMap) {
                    $models = [];
                    foreach ($args[0] as $object) {
                        $models[] = $fooModelsMap->offsetGet($object);
                    }

                    return array_combine(
                        array_keys(
                            is_array($args[0])
                                ? $args[0]
                                : iterator_to_array($args[0])
                        ),
                        $models
                    );
                }
            );
        $fooModelFactory = $fooModelFactory->reveal();

        $barModelFactory = $this->prophesize(ModelFactoryInterface::class);
        $barModelFactory
            ->supportsObject(Argument::type(Bar::class))
            ->willReturn(true);
        $barModelFactory
            ->supportsObject(Argument::that(
                function ($object) {
                    return !$object instanceof Bar;
                }
            ))
            ->willReturn(false);
        $barModelFactory
            ->supportsObjects(Argument::that(
                function ($objects) {
                    return is_array($objects) || $objects instanceof Traversable;
                }
            ))
            ->will(
                function (array $args) {
                    foreach ($args[0] as $object) {
                        if (!$object instanceof Bar) {
                            return false;
                        }
                    }

                    return true;
                }
            );
        $barModelFactory
            ->createModel(Argument::type(Bar::class))
            ->will(function ($args) use ($barModelsMap) {
                return $barModelsMap->offsetGet($args[0]);
            });
        $barModelFactory
            ->createModels(Argument::that(
                function ($objects) {
                    if (!is_array($objects) && !$objects instanceof Traversable) {
                        return false;
                    }
                    foreach ($objects as $object) {
                        if (!$object instanceof Bar) {
                            return false;
                        }
                    }

                    return true;
                }
            ))
            ->will(
                function (array $args) use ($barModelsMap) {
                    $models = [];
                    foreach ($args[0] as $object) {
                        $models[] = $barModelsMap->offsetGet($object);
                    }

                    return array_combine(
                        array_keys(
                            is_array($args[0])
                                ? $args[0]
                                : iterator_to_array($args[0])
                        ),
                        $models
                    );
                }
            );
        $barModelFactory = $barModelFactory->reveal();

        $bazModelFactory = $this->prophesize(ModelFactoryInterface::class);
        $bazModelFactory
            ->supportsObject(Argument::type(Baz::class))
            ->willReturn(true);
        $bazModelFactory
            ->supportsObject(Argument::that(
                function ($object) {
                    return !$object instanceof Baz;
                }
            ))
            ->willReturn(false);
        $bazModelFactory
            ->supportsObjects(Argument::that(
                function ($objects) {
                    return is_array($objects) || $objects instanceof Traversable;
                }
            ))
            ->will(
                function (array $args) {
                    foreach ($args[0] as $object) {
                        if (!$object instanceof Baz) {
                            return false;
                        }
                    }

                    return true;
                }
            );
        $bazModelFactory
            ->createModel(Argument::type(Baz::class))
            ->will(function ($args) use ($bazModelsMap) {
                return $bazModelsMap->offsetGet($args[0]);
            });
        $bazModelFactory
            ->createModels(Argument::that(
                function ($objects) {
                    if (!is_array($objects) && !$objects instanceof Traversable) {
                        return false;
                    }
                    foreach ($objects as $object) {
                        if (!$object instanceof Baz) {
                            return false;
                        }
                    }

                    return true;
                }
            ))
            ->will(
                function (array $args) use ($bazModelsMap) {
                    $models = [];
                    foreach ($args[0] as $object) {
                        $models[] = $bazModelsMap->offsetGet($object);
                    }

                    return array_combine(
                        array_keys(
                            is_array($args[0])
                                ? $args[0]
                                : iterator_to_array($args[0])
                        ),
                        $models
                    );
                }
            );
        $bazModelFactory = $bazModelFactory->reveal();

        $modelFactoryCollection = new ModelFactoryCollection();
        $modelFactoryCollection
            ->addModelFactory($fooModelFactory)
            ->addModelFactory($barModelFactory)
            ->addModelFactory($bazModelFactory);

        $fooModelsMap->offsetSet($foos[0], new FooModel());
        $fooModelsMap->offsetSet($foos[1], new FooModel());

        $barModelsMap->offsetSet($bars[0], new BarModel());
        $barModelsMap->offsetSet($bars[1], new BarModel());

        $createBazModel = function () use ($modelFactoryCollection) {
            $bazModel = $this->prophesize(BazModel::class);
            $bazModel
                ->setModelFactoryCollection($modelFactoryCollection)
                ->shouldBeCalled();

            return $bazModel->reveal();
        };

        $bazModelsMap->offsetSet($bazs[0], $createBazModel());
        $bazModelsMap->offsetSet($bazs[1], $createBazModel());

        $this->assertEquals(
            $fooModelsMap->offsetGet($foos[0]),
            $modelFactoryCollection->createModel($foos[0])
        );
        $this->assertEquals(
            $fooModelsMap->offsetGet($foos[1]),
            $modelFactoryCollection->createModel($foos[1])
        );

        $this->assertEquals(
            $barModelsMap->offsetGet($bars[0]),
            $modelFactoryCollection->createModel($bars[0])
        );
        $this->assertEquals(
            $barModelsMap->offsetGet($bars[1]),
            $modelFactoryCollection->createModel($bars[1])
        );

        $this->assertEquals(
            $bazModelsMap->offsetGet($bazs[0]),
            $modelFactoryCollection->createModel($bazs[0])
        );
        $this->assertEquals(
            $bazModelsMap->offsetGet($bazs[1]),
            $modelFactoryCollection->createModel($bazs[1])
        );

        $this->assertEquals(
            [$fooModelsMap->offsetGet($foos[0])],
            $modelFactoryCollection->createModels([$foos[0]])
        );
        $this->assertEquals(
            [$fooModelsMap->offsetGet($foos[0]), $fooModelsMap->offsetGet($foos[1])],
            $modelFactoryCollection->createModels([$foos[0], $foos[1]])
        );
        $this->assertEquals(
            [$barModelsMap->offsetGet($bars[0]), $barModelsMap->offsetGet($bars[1])],
            $modelFactoryCollection->createModels([$bars[0], $bars[1]])
        );
        $this->assertEquals(
            [$fooModelsMap->offsetGet($foos[0]), $barModelsMap->offsetGet($bars[1])],
            $modelFactoryCollection->createModels([$foos[0], $bars[1]])
        );

        $this->assertEquals(
            ['a' => $fooModelsMap->offsetGet($foos[0])],
            $modelFactoryCollection->createModels(['a' => $foos[0]])
        );
        $this->assertEquals(
            ['a' => $fooModelsMap->offsetGet($foos[0]), 'b' => $fooModelsMap->offsetGet($foos[1])],
            $modelFactoryCollection->createModels(['a' => $foos[0], 'b' => $foos[1]])
        );
        $this->assertEquals(
            ['a' => $barModelsMap->offsetGet($bars[0]), 'b' => $barModelsMap->offsetGet($bars[1])],
            $modelFactoryCollection->createModels(['a' => $bars[0], 'b' => $bars[1]])
        );
        $this->assertEquals(
            ['a' => $fooModelsMap->offsetGet($foos[0]), 'b' => $barModelsMap->offsetGet($bars[1])],
            $modelFactoryCollection->createModels(['a' => $foos[0], 'b' => $bars[1]])
        );

        $this->assertEquals(
            [$fooModelsMap->offsetGet($foos[0])],
            $modelFactoryCollection->createModels(new ArrayObject([$foos[0]]))
        );
        $this->assertEquals(
            [$fooModelsMap->offsetGet($foos[0]), $fooModelsMap->offsetGet($foos[1])],
            $modelFactoryCollection->createModels(new ArrayObject([$foos[0], $foos[1]]))
        );
        $this->assertEquals(
            [$barModelsMap->offsetGet($bars[0]), $barModelsMap->offsetGet($bars[1])],
            $modelFactoryCollection->createModels(new ArrayObject([$bars[0], $bars[1]]))
        );
        $this->assertEquals(
            [$fooModelsMap->offsetGet($foos[0]), $barModelsMap->offsetGet($bars[1])],
            $modelFactoryCollection->createModels(new ArrayObject([$foos[0], $bars[1]]))
        );
    }
}
