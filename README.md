# Guzzle Bundle WSSE Plugin

[![Build Status](https://travis-ci.org/gregurco/GuzzleBundleWssePlugin.svg?branch=master)](https://travis-ci.org/gregurco/GuzzleBundleWssePlugin)
[![Coverage Status](https://coveralls.io/repos/gregurco/GuzzleBundleWssePlugin/badge.svg?branch=master)](https://coveralls.io/r/gregurco/GuzzleBundleWssePlugin)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/537660c3-913a-4ce2-876a-4abb73f642f2/mini.png)](https://insight.sensiolabs.com/projects/537660c3-913a-4ce2-876a-4abb73f642f2)

This plugin integrates [WSSE][1] functionality into Guzzle Bundle, a bundle for building RESTful web service clients.


## Requirements
 - PHP 7.0 or above
 - [Guzzle Bundle][2]

 
### Installation
Using [composer][3]:

##### composer.json
``` json
{
    "require": {
        "gregurco/guzzle-bundle-wsse-plugin": "dev-master"
    }
}
```

##### command line
``` bash
$ composer require gregurco/guzzle-bundle-wsse-plugin
```

## Usage
### Enable bundle

#### Symfony 2.x and 3.x
Plugin will be activated/connected through bundle constructor in `app/AppKernel.php`, like this:

``` php 
new EightPoints\Bundle\GuzzleBundle\EightPointsGuzzleBundle([
    new Gregurco\Bundle\GuzzleBundleWssePlugin\GuzzleBundleWssePlugin(),
])
```

#### Symfony 4
The registration of bundles was changed in Symfony 4 and now you have to change `src/Kernel.php` to achieve the same functionality.  
Find next lines:

```php
foreach ($contents as $class => $envs) {
    if (isset($envs['all']) || isset($envs[$this->environment])) {
        yield new $class();
    }
}
```

and replace them by:

```php
foreach ($contents as $class => $envs) {
    if (isset($envs['all']) || isset($envs[$this->environment])) {
        if ($class === \EightPoints\Bundle\GuzzleBundle\EightPointsGuzzleBundle::class) {
            yield new $class([
                new \Gregurco\Bundle\GuzzleBundleWssePlugin\GuzzleBundleWssePlugin(),
            ]);
        } else {
            yield new $class();
        }
    }
}
```

### Basic configuration
``` yaml
# app/config/config.yml

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

## Usage with guzzle
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

## License
This middleware is licensed under the MIT License - see the LICENSE file for details

[1]: http://www.xml.com/pub/a/2003/12/17/dive.html
[2]: https://github.com/8p/EightPointsGuzzleBundle
[3]: https://getcomposer.org/
