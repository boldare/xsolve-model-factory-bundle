<?php

namespace Xsolve\ModelFactoryBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Xsolve\ModelFactoryBundle\DependencyInjection\CompilerPass\ModelFactoryCollectionCompilerPass;

class XsolveModelFactoryBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new ModelFactoryCollectionCompilerPass());
    }
}
