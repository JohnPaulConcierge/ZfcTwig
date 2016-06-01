<?php

namespace ZfcTwig\View;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Config;
use Zend\ServiceManager\ConfigInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use Zend\View\Exception;
use ZfcTwig\ModuleOptions;

class HelperPluginManagerFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return HelperPluginManager
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        /** @var ModuleOptions $options */
        $options        = $container->get(ModuleOptions::class);
        $managerOptions = $options->getHelperManager();
        $managerConfigs = isset($managerOptions['configs']) ? $managerOptions['configs'] : [];

        $twigManager = new HelperPluginManager($container, new Config($managerOptions));

        foreach ($managerConfigs as $configClass) {
            if (is_string($configClass) && class_exists($configClass)) {
                $config = new $configClass;

                if (!$config instanceof ConfigInterface) {
                    throw new Exception\RuntimeException(
                        sprintf(
                            'Invalid service manager configuration class provided; received "%s",
                                expected class implementing %s',
                            $configClass,
                            'Zend\ServiceManager\ConfigInterface'
                        )
                    );
                }

                $config->configureServiceManager($twigManager);
            }
        }

        return $twigManager;
    }
}
