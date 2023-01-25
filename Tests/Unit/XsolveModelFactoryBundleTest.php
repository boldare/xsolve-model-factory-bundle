<?php

namespace Xsolve\ModelFactoryBundle\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Xsolve\ModelFactoryBundle\DependencyInjection\CompilerPass\ModelFactoryCollectionCompilerPass;
use Xsolve\ModelFactoryBundle\XsolveModelFactoryBundle;

class XsolveModelFactoryBundleTest extends TestCase
{
    public function test_build()
    {
        $container = $this->prophesize(ContainerBuilder::class);
        $container
            ->addCompilerPass(Argument::type(ModelFactoryCollectionCompilerPass::class))
            ->willReturn($container->reveal())
            ->shouldBeCalledTimes(1);

        $bundle = new XsolveModelFactoryBundle();

        $bundle->build($container->reveal());
    }
}