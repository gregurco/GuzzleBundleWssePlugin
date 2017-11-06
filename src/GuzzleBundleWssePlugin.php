<?php

namespace Gregurco\Bundle\GuzzleBundleWssePlugin;


use EightPoints\Bundle\GuzzleBundle\EightPointsGuzzleBundlePlugin;
use Gregurco\Bundle\GuzzleBundleWssePlugin\DependencyInjection\GuzzleWsseExtension;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\ExpressionLanguage\Expression;

class GuzzleBundleWssePlugin extends Bundle implements EightPointsGuzzleBundlePlugin
{
    /**
     * @param array $configs
     * @param ContainerBuilder $container
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $extension = new GuzzleWsseExtension();
        $extension->load($configs, $container);
    }

    /**
     * @param array $config
     * @param ContainerBuilder $container
     * @param string $clientName
     * @param Definition $handler
     */
    public function loadForClient(array $config, ContainerBuilder $container, string $clientName, Definition $handler)
    {
        if ($config['username'] && $config['password']) {
            $wsse = new Definition('%guzzle_bundle_wsse_plugin.middleware.wsse.class%');
            $wsse->setArguments([$config['username'], $config['password'], $config['created_at']]);

            $wsseServiceName = sprintf('guzzle_bundle_wsse_plugin.middleware.wsse.%s', $clientName);

            $container->setDefinition($wsseServiceName, $wsse);

            $wsseExpression = new Expression(sprintf('service("%s").attach()', $wsseServiceName));

            $handler->addMethodCall('push', [$wsseExpression, 'wsse']);
        }
    }

    /**
     * @param ArrayNodeDefinition $pluginNode
     */
    public function addConfiguration(ArrayNodeDefinition $pluginNode)
    {
        $pluginNode
            ->addDefaultsIfNotSet()
            ->children()
                ->scalarNode('username')->defaultNull()->end()
                ->scalarNode('password')->defaultNull()->end()
                ->scalarNode('created_at')->defaultNull()->end()
            ->end();
    }

    /**
     * @return string
     */
    public function getPluginName() : string
    {
        return 'wsse';
    }
}
