<?php

namespace Namshi\Cuzzle\Middleware;

use Namshi\Cuzzle\Formatter\CurlFormatter;
use Psr\Log\LoggerInterface;
use Psr\Http\Message\RequestInterface;

class CurlFormatterMiddleware
{

    /**
     * @var LoggerInterface
     */
    protected $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }


    /**
     * @param callable $handler
     * @return \Closure
     */
    public function __invoke(callable $handler)
    {
        return function (RequestInterface $request, array $options) use ($handler) {
            $curlCommand = (new CurlFormatter())->format($request, $options);
            $this->logger->debug($curlCommand);

            return $handler($request, $options);
        };
    }
}
