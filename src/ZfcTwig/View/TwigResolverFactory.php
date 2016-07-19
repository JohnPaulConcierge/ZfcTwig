<?php

namespace ZfcTwig\View;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class TwigResolverFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return TwigResolver
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new TwigResolver($container->get('Twig_Environment'));
    }

    /**
     * @param ServiceLocatorInterface $serviceLocator
     * @return TwigResolver
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return $this($serviceLocator, TwigResolver::class);
    }


}
