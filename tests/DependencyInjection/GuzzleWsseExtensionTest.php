<?php

namespace Gregurco\Bundle\GuzzleBundleWssePlugin\Test\DependencyInjection;

use Gregurco\Bundle\GuzzleBundleWssePlugin\DependencyInjection\GuzzleWsseExtension;
use Gregurco\Bundle\GuzzleBundleWssePlugin\Middleware\WsseAuthMiddleware;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use PHPUnit\Framework\TestCase;

class GuzzleWsseExtensionTest extends TestCase
{
    public function testLoad()
    {
        $container = new ContainerBuilder();

        $extension = new GuzzleWsseExtension();
        $extension->load([], $container);

        $this->assertTrue($container->hasParameter('guzzle_bundle_wsse_plugin.middleware.wsse.class'));
        $this->assertEquals(
            WsseAuthMiddleware::class,
            $container->getParameter('guzzle_bundle_wsse_plugin.middleware.wsse.class')
        );
    }
}
