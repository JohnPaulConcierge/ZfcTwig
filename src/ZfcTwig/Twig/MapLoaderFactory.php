<?php

namespace ZfcTwig\Twig;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use ZfcTwig\ModuleOptions;

class MapLoaderFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return MapLoader
     * @throws \Twig_Error_Loader
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        /** @var ModuleOptions $options */
        $options = $container->get(ModuleOptions::class);

        /** @var \Zend\View\Resolver\TemplateMapResolver */
        $zfTemplateMap = $container->get('ViewTemplateMapResolver');

        $templateMap = new MapLoader();
        foreach ($zfTemplateMap as $name => $path) {
            if ($options->getSuffix() == pathinfo($path, PATHINFO_EXTENSION)) {
                $templateMap->add($name, $path);
            }
        }

        return $templateMap;
    }

    /**
     * @param ServiceLocatorInterface $serviceLocator
     * @return MapLoader
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return $this($serviceLocator, MapLoader::class);
    }


}