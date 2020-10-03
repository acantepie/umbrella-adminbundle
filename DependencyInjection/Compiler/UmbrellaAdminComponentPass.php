<?php
/**
 * Created by PhpStorm.
 * User: acantepie
 * Date: 16/03/18
 * Time: 14:48
 */

namespace Umbrella\AdminBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Umbrella\AdminBundle\FileWriter\Handler\FileWriterHandlerFactory;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

/**
 * Class UmbrellaAdminComponentPass
 */
class UmbrellaAdminComponentPass implements CompilerPassInterface
{
    /**
     * You can modify the container here before it is dumped to PHP code.
     */
    public function process(ContainerBuilder $container)
    {
        $this->storeTaggedServiceToRegistry($container, FileWriterHandlerFactory::class, 'umbrella.filewriter.handler', 'registerHandler');
    }

    private function storeTaggedServiceToRegistry(ContainerBuilder $container, $registryClass, $tag, $method)
    {
        // always first check if the primary service is defined
        if (!$container->has($registryClass)) {
            return;
        }

        $definition = $container->findDefinition($registryClass);
        $taggedServices = $container->findTaggedServiceIds($tag);

        foreach ($taggedServices as $id => $tags) {
            $definition->addMethodCall($method, [$id, new Reference($id)]);
        }
    }
}
