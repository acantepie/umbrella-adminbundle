<?php

namespace Umbrella\AdminBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Umbrella\AdminBundle\DependencyInjection\Compiler\UmbrellaAdminComponentPass;

/**
 * Class UmbrellaAdminBundle
 */
class UmbrellaAdminBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(new UmbrellaAdminComponentPass());
    }
}
