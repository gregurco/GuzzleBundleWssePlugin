<?php

namespace Gregurco\Bundle\GuzzleBundleWssePlugin\Test;

use EightPoints\Bundle\GuzzleBundle\EightPointsGuzzleBundlePlugin;
use Gregurco\Bundle\GuzzleBundleWssePlugin\GuzzleBundleWssePlugin;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class GuzzleBundleWssePluginTest extends TestCase
{
    public function testMainClassInstance()
    {
        $plugin = new GuzzleBundleWssePlugin();

        $this->assertInstanceOf(EightPointsGuzzleBundlePlugin::class, $plugin);
        $this->assertInstanceOf(Bundle::class, $plugin);
    }

    public function testAddConfiguration()
    {
        $arrayNode = new ArrayNodeDefinition('node');

        $plugin = new GuzzleBundleWssePlugin();
        $plugin->addConfiguration($arrayNode);

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
        $this->assertEquals('wsse', (new GuzzleBundleWssePlugin())->getPluginName());
    }
}
