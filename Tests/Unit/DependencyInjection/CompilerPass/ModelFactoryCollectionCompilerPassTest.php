<?php

namespace Xsolve\ModelFactoryBundle\Tests\Unit\DependencyInjection\CompilerPass;

use PHPUnit_Framework_TestCase;
use Prophecy\Argument;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Xsolve\ModelFactoryBundle\DependencyInjection\CompilerPass\ModelFactoryCollectionCompilerPass;

class ModelFactoryCollectionCompilerPassTest extends PHPUnit_Framework_TestCase
{
    public function test_build()
    {
        $modelFactoryServiceIdToTags = [
            'model_factory.foo' => [
                [ModelFactoryCollectionCompilerPass::ATTRIBUTE_NAME_MODEL_FACTORY_COLLECTION_SERVICE_ID => 'model_factory_collection.first'],
            ],
            'model_factory.bar' => [
                [ModelFactoryCollectionCompilerPass::ATTRIBUTE_NAME_MODEL_FACTORY_COLLECTION_SERVICE_ID => 'model_factory_collection.first'],
                [ModelFactoryCollectionCompilerPass::ATTRIBUTE_NAME_MODEL_FACTORY_COLLECTION_SERVICE_ID => 'model_factory_collection.second'],
            ],
            'model_factory.baz' => [
                [ModelFactoryCollectionCompilerPass::ATTRIBUTE_NAME_MODEL_FACTORY_COLLECTION_SERVICE_ID => 'model_factory_collection.second'],
            ],
        ];

        $modelFactoryCollectionDefinitionFirst = $this->prophesize(Definition::class);
        $modelFactoryCollectionDefinitionFirst
            ->addMethodCall(
                'addModelFactory',
                Argument::that(function (array $args) {
                    return
                        count($args) === 1
                        && $args[0] instanceof Reference
                        && (string) $args[0] === 'model_factory.foo'
                    ;
                })
            )
            ->shouldBeCalledTimes(1);
        $modelFactoryCollectionDefinitionFirst
            ->addMethodCall(
                'addModelFactory',
                Argument::that(function (array $args) {
                    return
                        count($args) === 1
                        && $args[0] instanceof Reference
                        && (string) $args[0] === 'model_factory.bar'
                    ;
                })
            )
            ->shouldBeCalledTimes(1);
        $modelFactoryCollectionDefinitionFirst = $modelFactoryCollectionDefinitionFirst->reveal();

        $modelFactoryCollectionDefinitionSecond = $this->prophesize(Definition::class);
        $modelFactoryCollectionDefinitionSecond
            ->addMethodCall(
                'addModelFactory',
                Argument::that(function (array $args) {
                    return
                        count($args) === 1
                        && $args[0] instanceof Reference
                        && (string) $args[0] === 'model_factory.bar'
                    ;
                })
            )
            ->shouldBeCalledTimes(1);
        $modelFactoryCollectionDefinitionSecond
            ->addMethodCall(
                'addModelFactory',
                Argument::that(function (array $args) {
                    return
                        count($args) === 1
                        && $args[0] instanceof Reference
                        && (string) $args[0] === 'model_factory.baz'
                    ;
                })
            )
            ->shouldBeCalledTimes(1);
        $modelFactoryCollectionDefinitionSecond = $modelFactoryCollectionDefinitionSecond->reveal();

        $container = $this->prophesize(ContainerBuilder::class);
        $container
            ->findTaggedServiceIds(ModelFactoryCollectionCompilerPass::TAG_NAME)
            ->shouldBeCalledTimes(1)
            ->willReturn($modelFactoryServiceIdToTags);
        $container
            ->getDefinition('model_factory_collection.first')
            ->shouldBeCalledTimes(2)
            ->willReturn($modelFactoryCollectionDefinitionFirst);
        $container
            ->getDefinition('model_factory_collection.second')
            ->shouldBeCalledTimes(2)
            ->willReturn($modelFactoryCollectionDefinitionSecond);
        $container = $container->reveal();

        $modelFactoryCollectionCompilerPass = new ModelFactoryCollectionCompilerPass();

        $modelFactoryCollectionCompilerPass->process($container);
    }
}
