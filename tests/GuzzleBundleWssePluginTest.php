<?php

namespace Gregurco\Bundle\GuzzleBundleWssePlugin\Test;

use EightPoints\Bundle\GuzzleBundle\EightPointsGuzzleBundlePlugin;
use Gregurco\Bundle\GuzzleBundleWssePlugin\GuzzleBundleWssePlugin;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class GuzzleBundleWssePluginTest extends TestCase
{
    public function testMainClassInstance()
    {
        $plugin = new GuzzleBundleWssePlugin();

        $this->assertInstanceOf(EightPointsGuzzleBundlePlugin::class, $plugin);
        $this->assertInstanceOf(Bundle::class, $plugin);
    }
}
