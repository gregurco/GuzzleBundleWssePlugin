<?php

namespace Gregurco\Bundle\EightPointsGuzzleWssePlugin;

namespace Gregurco\Bundle\EightPointsGuzzleWssePlugin;

use EightPoints\Bundle\GuzzleBundle\EightPointsGuzzlePlugin;
use Gregurco\Bundle\EightPointsGuzzleWssePlugin\DependencyInjection\GuzzleWsseExtension;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\ExpressionLanguage\Expression;

class EightPointsGuzzleWssePlugin extends Bundle implements EightPointsGuzzlePlugin
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
            $username = $config['username'];
            $password = $config['password'];
            $createdAtExpression = null;

            if (isset($config['created_at']) && $config['created_at']) {
                $createdAtExpression = $config['created_at'];
            }

            $wsse = new Definition('%eight_points_guzzle.middleware.wsse.class%');
            $wsse->setArguments([$username, $password]);

            if ($createdAtExpression) {
                $wsse->addMethodCall('setCreatedAtTimeExpression', [$createdAtExpression]);
            }

            $wsseServiceName = sprintf('eight_points_guzzle.middleware.wsse.%s', $clientName);

            $container->setDefinition($wsseServiceName, $wsse);

            $wsseExpression = new Expression(sprintf('service("%s").attach()', $wsseServiceName));

            $handler->addMethodCall('push', [$wsseExpression]);
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
                ->scalarNode('username')->defaultFalse()->end()
                ->scalarNode('password')->defaultValue('')->end()
                ->scalarNode('created_at')->defaultFalse()->end()
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
