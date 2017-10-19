Guzzle Bundle WSSE Plugin
==================

[![Build Status](https://travis-ci.org/gregurco/GuzzleBundleWssePlugin.svg?branch=init)](https://travis-ci.org/gregurco/GuzzleBundleWssePlugin) [![Coverage Status](https://coveralls.io/repos/gregurco/GuzzleBundleWssePlugin/badge.svg?branch=init)](https://coveralls.io/r/gregurco/GuzzleBundleWssePlugin)

This plugin integrates [WSSE][1] functionality into Guzzle Bundle, a bundle for building RESTful web service clients.


Requirements
------------
 - PHP 7.0 or above
 - [Guzzle Bundle][2]

 
Installation
------------
Using [composer][3]:

``` json
{
    "require": {
        "gregurco/guzzle-bundle-wsse-plugin": "dev-master"
    }
}
```


Usage
-----
Load plugin in AppKernel.php:
``` php
new EightPoints\Bundle\GuzzleBundle\EightPointsGuzzleBundle([
    new Gregurco\Bundle\GuzzleBundleWssePlugin\GuzzleBundleWssePlugin(),
])
```

Configuration in config.yml:
``` yaml
eight_points_guzzle:
    clients:
        api_payment:
            base_url: "http://api.domain.tld"

            # define headers, options

            # plugin settings
            plugin:
                wsse:
                    username:   "acme"
                    password:   "pa55w0rd"
                    created_at: "-10 seconds" # optional
```

Or use with guzzle directly:
``` php
<?php 
# Optional: Set third parameter by a expression (if not, current time will be used automatically)
# http://php.net/manual/en/datetime.formats.relative.php
# Useful if there is a small difference of time between client and server
# DateTime object will be regenerated for every request
$wsse = new \Gregurco\Bundle\GuzzleBundleWssePlugin\Middleware\WsseAuthMiddleware($username, $password);

$stack = \GuzzleHttp\HandlerStack::create();

// Add the wsse middleware to the handler stack.
$stack->push($wsse->attach());

$client   = new \GuzzleHttp\Client(['handler' => $stack]);
$response = $client->get('http://www.8points.de');
```

License
-------
This middleware is licensed under the MIT License - see the LICENSE file for details

[1]: http://www.xml.com/pub/a/2003/12/17/dive.html
[2]: https://github.com/8p/GuzzleBundle
[3]: https://getcomposer.org/
