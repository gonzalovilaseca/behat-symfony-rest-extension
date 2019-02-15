<?php

namespace Gvf\SymfonyRestExtension\ServiceContainer;

use Behat\Testwork\ServiceContainer\Extension as ExtensionInterface;
use Behat\Testwork\ServiceContainer\ExtensionManager;
use Psr\Container\ContainerInterface;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Gvf\SymfonyRestExtension\Context\Argument\ServiceArgumentResolver;
use Gvf\SymfonyRestExtension\HttpCall\HttpCallListener;
use Gvf\SymfonyRestExtension\HttpCall\HttpCallResultPool;
use Gvf\SymfonyRestExtension\HttpCall\RestContextVoter;

final class SymfonyRestExtension implements ExtensionInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigKey()
    {
        return 'gvf_symfony_rest';
    }

    /**
     * {@inheritdoc}
     */
    public function initialize(ExtensionManager $extensionManager)
    {
        if ($extensionManager->getExtension('symfony2') === null) {
            throw new \Exception('Symfony2 extension is needed to run Symfony Rest extension!');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configure(ArrayNodeDefinition $builder)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function load(ContainerBuilder $container, array $config)
    {
        $container->register(ServiceArgumentResolver::class)
            ->addArgument(new Reference(ContainerInterface::class))
            ->addTag('context.argument_resolver')
        ;

        $container->register(RestContextVoter::class);

        $container->register(HttpCallResultPool::class)
            ->setPublic(true)
        ;

        $container->register(HttpCallListener::class)
            ->setArguments([
                    new Reference(RestContextVoter::class),
                    new Reference(HttpCallResultPool::class),
                ]
            )
            ->addTag('event_dispatcher.subscriber')
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
    }
}
