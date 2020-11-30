<?php

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use Namshi\Cuzzle\Middleware\CurlFormatterMiddleware;
use Psr\Log\LoggerInterface;

test('get', function () {
    $mock = new MockHandler([new \GuzzleHttp\Psr7\Response(204)]);
    $handler = HandlerStack::create($mock);
    $logger = $this->getMockBuilder('Psr\Log\LoggerInterface')->getMock();

    $logger
        ->expects($this->once())
        ->method('debug')
        ->with($this->stringStartsWith('curl'));

    $handler->after('cookies', new CurlFormatterMiddleware($logger));
    $client = new Client(['handler' => $handler]);

    $client->get('http://google.com');
});
