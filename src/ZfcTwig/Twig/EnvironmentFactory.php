<?php

namespace ZfcTwig\Twig;

use Interop\Container\ContainerInterface;
use RuntimeException;
use Twig_Environment;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use ZfcTwig\ModuleOptions;

class EnvironmentFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return Twig_Environment
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        /** @var ModuleOptions $options */
        $options  = $container->get(ModuleOptions::class);
        $envClass = $options->getEnvironmentClass();

        /** @var Twig_Environment $env */
        $env = new $envClass(null, $options->getEnvironmentOptions());

        if ($options->getEnableFallbackFunctions()) {
            $helperPluginManager = $container->get('ViewHelperManager');
            $env->registerUndefinedFunctionCallback(
                function ($name) use ($helperPluginManager) {
                    if ($helperPluginManager->has($name)) {
                        return new FallbackFunction($name);
                    }
                    return false;
                }
            );
        }

        if (!$container->has($options->getEnvironmentLoader())) {
            throw new RuntimeException(
                sprintf(
                    'Loader with alias "%s" could not be found!',
                    $options->getEnvironmentLoader()
                )
            );
        }

        $env->setLoader($container->get($options->getEnvironmentLoader()));

        foreach ($options->getGlobals() as $name => $value) {
            $env->addGlobal($name, $value);
        }

        // Extensions are loaded later to avoid circular dependencies (for example, if an extension needs Renderer).
        return $env;
    }

    /**
     * @param ServiceLocatorInterface $serviceLocator
     * @return Twig_Environment
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return $this($serviceLocator, '');
    }


}
