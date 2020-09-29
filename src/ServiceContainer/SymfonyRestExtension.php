<?php

namespace Gvf\SymfonyRestExtension\ServiceContainer;

use Behat\Behat\Context\ServiceContainer\ContextExtension;
use Behat\Testwork\ServiceContainer\Extension;
use Behat\Testwork\ServiceContainer\ExtensionManager;
use FriendsOfBehat\SymfonyExtension\ServiceContainer\SymfonyExtension;
use Gvf\SymfonyRestExtension\Context\Argument\ServiceArgumentResolver;
use Gvf\SymfonyRestExtension\HttpCall\HttpCallListener;
use Gvf\SymfonyRestExtension\HttpCall\HttpCallResultPool;
use Gvf\SymfonyRestExtension\HttpCall\RestContextVoter;
use Psr\Container\ContainerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class SymfonyRestExtension implements Extension
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
        if ($extensionManager->getExtension('fob_symfony') === null) {
            throw new \Exception('Friends of behat Symfony extension is needed to run Symfony Rest extension!');
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
        $container->register(KernelBrowser::class)
            ->addArgument(new Reference(SymfonyExtension::KERNEL_ID))
            ->setPublic(true)
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

        $this->loadArgumentResolver($container);
    }

    private function loadArgumentResolver(ContainerBuilder $container): void
    {
        $container->register(ServiceArgumentResolver::class)
            ->addArgument(new Reference(ContainerInterface::class))
            ->addTag(ContextExtension::ARGUMENT_RESOLVER_TAG)
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
    }
}
