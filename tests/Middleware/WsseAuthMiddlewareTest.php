<?php

namespace Gregurco\Bundle\GuzzleBundleWssePlugin\Test\Middleware;

use Gregurco\Bundle\GuzzleBundleWssePlugin\Middleware\WsseAuthMiddleware;
use GuzzleHttp\HandlerStack;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;

class WsseAuthMiddlewareTest extends TestCase
{
    public function testConstruct()
    {
        $middleware = new WsseAuthMiddleware('admin', 'password', '-10 sec');

        $this->assertEquals('admin', $middleware->getUsername());
        $this->assertEquals('password', $middleware->getPassword());
        $this->assertEquals('-10 sec', $middleware->getCreatedAt());
    }

    public function testAttach()
    {
        $middleware = new WsseAuthMiddleware('admin', 'password');
        $attachResult = $middleware->attach();

        $this->assertInstanceOf(\Closure::class, $attachResult);

        $handler = $this->getMockBuilder(HandlerStack::class)->getMock();
        $handler->method('__invoke')->willReturn('');

        $result = $attachResult($handler);

        $this->assertInstanceOf(\Closure::class, $result);

        $request = $this->getMockBuilder(RequestInterface::class)->getMock();
        $request->expects($this->exactly(2))->method('withHeader')->willReturnSelf();
        $result($request, []);
    }
}
