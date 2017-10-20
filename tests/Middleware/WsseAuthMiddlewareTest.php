<?php

namespace Gregurco\Bundle\GuzzleBundleWssePlugin\Test\Middleware;

use Gregurco\Bundle\GuzzleBundleWssePlugin\Middleware\WsseAuthMiddleware;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use PHPUnit\Framework\TestCase;

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
        $handler->method('__invoke')->will($this->returnCallback(function(Request $request) : Request {
            return $request;
        }));

        $result = $attachResult($handler);

        $this->assertInstanceOf(\Closure::class, $result);

        $request = new Request('GET', 'test.com');
        /** @var Request $requestAfterHandler */
        $requestAfterHandler = $result($request, []);

        $this->assertNotSame($requestAfterHandler, $request);
        $this->assertTrue($requestAfterHandler->hasHeader('X-WSSE'));
        $this->assertTrue($requestAfterHandler->hasHeader('Authorization'));
        $this->assertEquals('WSSE profile="UsernameToken"', $requestAfterHandler->getHeaderLine('Authorization'));
    }
}
