<?php

namespace Gregurco\Bundle\GuzzleBundleWssePlugin\Test;

use EightPoints\Bundle\GuzzleBundle\EightPointsGuzzleBundlePlugin;
use Gregurco\Bundle\GuzzleBundleWssePlugin\GuzzleBundleWssePlugin;
use Gregurco\Bundle\GuzzleBundleWssePlugin\Middleware\WsseAuthMiddleware;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use PHPUnit\Framework\TestCase;

class GuzzleBundleWssePluginTest extends TestCase
{
    /** @var GuzzleBundleWssePlugin */
    protected $plugin;

    public function setUp()
    {
        parent::setUp();

        $this->plugin = new GuzzleBundleWssePlugin();
    }

    public function testSubClassesOfPlugin()
    {
        $this->assertInstanceOf(EightPointsGuzzleBundlePlugin::class, $this->plugin);
        $this->assertInstanceOf(Bundle::class, $this->plugin);
    }

    public function testAddConfiguration()
    {
        $arrayNode = new ArrayNodeDefinition('node');

        $this->plugin->addConfiguration($arrayNode);

        $node = $arrayNode->getNode();

        $this->assertFalse($node->isRequired());
        $this->assertTrue($node->hasDefaultValue());
        $this->assertSame(
            ['username' => null, 'password' => null, 'created_at' => null],
            $node->getDefaultValue()
        );
    }

    public function testGetPluginName()
    {
        $this->assertEquals('wsse', $this->plugin->getPluginName());
    }

    public function testLoad()
    {
        $container = new ContainerBuilder();

        $this->plugin->load([], $container);

        $this->assertTrue($container->hasParameter('guzzle_bundle_wsse_plugin.middleware.wsse.class'));
        $this->assertEquals(
            WsseAuthMiddleware::class,
            $container->getParameter('guzzle_bundle_wsse_plugin.middleware.wsse.class')
        );
    }

    public function testLoadForClient()
    {
        $handler = new Definition();
        $container = new ContainerBuilder();

        $this->plugin->loadForClient(
            ['username' => 'acme', 'password' => 'pa55w0rd', 'created_at' => null],
            $container, 'api_payment', $handler
        );

        $this->assertTrue($container->hasDefinition('guzzle_bundle_wsse_plugin.middleware.wsse.api_payment'));
        $this->assertCount(1, $handler->getMethodCalls());
        $this->assertCount(2, $handler->getMethodCalls()[0]);
        $this->assertEquals('push', $handler->getMethodCalls()[0][0]);
        $this->assertCount(2, $handler->getMethodCalls()[0][1]);
        $this->assertInstanceOf(Expression::class, $handler->getMethodCalls()[0][1][0]);
        $this->assertEquals('wsse', $handler->getMethodCalls()[0][1][1]);

        $clientMiddlewareDefinition = $container->getDefinition('guzzle_bundle_wsse_plugin.middleware.wsse.api_payment');
        $this->assertCount(3, $clientMiddlewareDefinition->getArguments());
        $this->assertEquals('acme', $clientMiddlewareDefinition->getArgument(0));
        $this->assertEquals('pa55w0rd', $clientMiddlewareDefinition->getArgument(1));
        $this->assertNull($clientMiddlewareDefinition->getArgument(2));
    }

    public function testLoadForClientWithoutData()
    {
        $handler = new Definition();
        $container = new ContainerBuilder();

        $this->plugin->loadForClient(
            ['username' => null, 'password' => null, 'created_at' => null],
            $container, 'api_payment', $handler
        );

        $this->assertFalse($container->hasDefinition('guzzle_bundle_wsse_plugin.middleware.wsse.api_payment'));
        $this->assertCount(0, $handler->getMethodCalls());
    }
}
