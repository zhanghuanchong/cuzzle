<?php

use Psr\Log\LoggerInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use Namshi\Cuzzle\Middleware\CurlFormatterMiddleware;

test('get', function () {
    $mock = new MockHandler([new \GuzzleHttp\Psr7\Response(204)]);
    $handler = HandlerStack::create($mock);
    $logger = $this->getMockBuilder(LoggerInterface::class)->getMock();

    $logger
        ->expects($this->once())
        ->method('debug')
        ->with($this->stringStartsWith('curl'));

    $handler->after('cookies', new CurlFormatterMiddleware($logger));
    $client = new Client(['handler' => $handler]);

    $client->get('https://google.com');
});
