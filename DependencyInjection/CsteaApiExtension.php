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
        if (isset($processedConfig['output_headers'])) {
            $container->setParameter('cstea.api_bundle.output_headers', $processedConfig['output_headers']);
        }
    }
}
