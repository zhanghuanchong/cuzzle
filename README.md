# Cuzzle, cURL command from Guzzle requests

<p align="center">
<a href="https://github.com/octoper/cuzzle/actions?query=workflow%3ATests"><img src="https://github.com/octoper/cuzzle/workflows/Tests/badge.svg" alt="Tests"/></a>

<a href="https://packagist.org/packages/octoper/cuzzle"><img src="https://img.shields.io/packagist/v/octoper/cuzzle?label=stable" alt="Latest Stable Version"/></a>

<a href="https://packagist.org/packages/octoper/cuzzle"><img src="https://img.shields.io/packagist/l/octoper/cuzzle.svg" alt="License"/></a>
</p>

This library let's you dump a Guzzle request to a cURL command for debug and log purpose.

## Prerequisites

This library needs PHP 7.3+.

## Installation

You can install the library directly with composer:
```
composer require octoper/cuzzle
```
(Add `--dev` if you don't need it in production environment)

## Usage

```php

use Namshi\Cuzzle\Formatter\CurlFormatter;
use GuzzleHttp\Message\Request;

$request = new Request('GET', 'example.local');
$options = [];

echo (new CurlFormatter())->format($request, $options);

```

To log the cURL request generated from a Guzzle request, simply add CurlFormatterSubscriber to Guzzle:

```php

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use Namshi\Cuzzle\Middleware\CurlFormatterMiddleware;
use Monolog\Logger;
use Monolog\Handler\TestHandler;

$logger = new Logger('guzzle.to.curl'); //initialize the logger
$testHandler = new TestHandler(); //test logger handler
$logger->pushHandler($testHandler);

$handler = HandlerStack::create();
$handler->after('cookies', new CurlFormatterMiddleware($logger)); //add the cURL formatter middleware
$client  = new Client(['handler' => $handler]); //initialize a Guzzle client

$response = $client->get('http://google.com'); //let's fire a request

var_dump($testHandler->getRecords()); //check the cURL request in the logs, 
//you should see something like: "curl 'http://google.com' -H 'User-Agent: Guzzle/4.2.1 curl/7.37.1 PHP/5.5.16"

```

## Tests

You can run tests locally with

```
./vendor/bin/pest
```
