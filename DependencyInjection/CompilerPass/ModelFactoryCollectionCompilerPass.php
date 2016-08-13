<?php

namespace Xsolve\ModelFactoryBundle\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class ModelFactoryCollectionCompilerPass implements CompilerPassInterface
{
    const TAG_NAME = 'xsolve.model_factory_bundle.model_factory';
    const ATTRIBUTE_NAME_MODEL_FACTORY_COLLECTION_SERVICE_ID = 'model-factory-collection-id';

    /**
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        $modelFactoryServiceIdToTags = $container->findTaggedServiceIds(self::TAG_NAME);
        foreach ($modelFactoryServiceIdToTags as $modelFactoryServiceId => $tags) {
            foreach ($tags as $tag) {
                $container
                    ->getDefinition($tag[self::ATTRIBUTE_NAME_MODEL_FACTORY_COLLECTION_SERVICE_ID])
                    ->addMethodCall('addModelFactory', [new Reference($modelFactoryServiceId)]);
            }
        }
    }
}
