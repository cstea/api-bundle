<?php declare(strict_types = 1);

namespace Cstea\ApiBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\Config\FileLocator;

/**
 * Class CsteaApiExtension
 *
 * @package Cstea\ApiBundle\DependencyInjection
 */
class CsteaApiExtension extends \Symfony\Component\DependencyInjection\Extension\Extension
{

    /**
     * @param mixed[]          $configs   Configs.
     * @param ContainerBuilder $container Container builder.
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__.'/../Resources/config')
        );
        $loader->load('services.yaml');

        $configuration = new Configuration();
        $processedConfig = $this->processConfiguration($configuration, $configs);

        if (isset($processedConfig['handle_exceptions'])) {
            $container->setParameter('cstea.api_bundle.handle_exceptions', $processedConfig['handle_exceptions']);
        }

        if (isset($processedConfig['pattern'])) {
            $container->setParameter('cstea.api_bundle.pattern', $processedConfig['pattern']);
        }

        if (isset($processedConfig['response_headers'])) {
            $keys = \array_keys($processedConfig['response_headers']);
            $values = \array_values($processedConfig['response_headers']);

            $fixedKeys = \array_map(function($key) {
                return \str_replace('_', '-', $key);
            }, $keys);

            $newHeaders = \array_combine($fixedKeys, $values);
            $container->setParameter('cstea.api_bundle.response_headers', $newHeaders);
        }
    }
}
