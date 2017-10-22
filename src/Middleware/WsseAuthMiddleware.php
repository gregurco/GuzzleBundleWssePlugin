<?php

namespace Gregurco\Bundle\GuzzleBundleWssePlugin\Middleware;

use Psr\Http\Message\RequestInterface;

/**
 * Adds WSSE auth headers to request
 * Based on http://www.xml.com/pub/a/2003/12/17/dive.html
 */
class WsseAuthMiddleware
{
    /** @var string */
    protected $username;

    /** @var string */
    protected $password;

    /**
     * Relative datetime string
     * Doc: http://php.net/manual/en/datetime.formats.relative.php
     *
     * @var string|null
     */
    protected $createdAt;

    /**
     * @param string $username
     * @param string $password
     * @param string|null $createdAt
     */
    public function __construct($username, $password, $createdAt = null)
    {
        $this->username = $username;
        $this->password = $password;
        $this->createdAt = $createdAt;
    }

    /**
     * @return string $username
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @return string $password
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @return string|null
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Add WSSE auth headers to Request
     *
     * @throws \InvalidArgumentException
     *
     * @return \Closure
     */
    public function attach() : \Closure
    {
        return function (callable $handler) :  \Closure {

            return function (RequestInterface $request, array $options) use ($handler) {

                $createdAt = (new \DateTime($this->createdAt))->format('c');
                $nonce = $this->generateNonce();
                $digest = $this->generateDigest($nonce, $createdAt, $this->password);

                $xwsse = [
                    sprintf('Username="%s"', $this->username),
                    sprintf('PasswordDigest="%s"', $digest),
                    sprintf('Nonce="%s"', $nonce),
                    sprintf('Created="%s"', $createdAt)
                ];

                $request = $request->withHeader('Authorization', 'WSSE profile="UsernameToken"');
                $request = $request->withHeader('X-WSSE', sprintf('UsernameToken %s', implode(', ', $xwsse)));

                return $handler($request, $options);
            };
        };
    }

    /**
     * @param string $nonce
     * @param string $createdAt
     * @param string $password
     *
     * @return string
     */
    protected function generateDigest($nonce, $createdAt, $password) : string
    {
        return base64_encode(sha1(base64_decode($nonce) . $createdAt . $password, true));
    }

    /**
     * Generate Nonce (number user once)
     *
     * @return string
     */
    protected function generateNonce() : string
    {
        return base64_encode(hash('sha512', uniqid(true)));
    }
}
