<?php

namespace Client;

use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Utils;
use Namshi\Cuzzle\Formatter\CurlFormatter;

beforeEach(function () {
    $this->client        = new Client();
    $this->curlFormatter = new CurlFormatter();
});

test('get with cookies', function () {
    $request = new Request('GET', 'http://local.example');
    $jar = CookieJar::fromArray(['Foo' => 'Bar', 'identity' => 'xyz'], 'local.example');
    $curl    = $this->curlFormatter->format($request, ['cookies' => $jar]);

    expect(str_replace('"', '\'', $curl))->not()->toContain("-H 'Host: local.example'");
    expect(str_replace('"', '\'', $curl))->toContain("-b 'Foo=Bar; identity=xyz'");
});

test('POST', function () {
    $request = new Request('POST', 'http://local.example', [], Utils::streamFor('foo=bar&hello=world'));
    $curl    = $this->curlFormatter->format($request);

    expect(str_replace('"', '\'', $curl))->toContain("-d 'foo=bar&hello=world'");
});

test('PUT', function () {
    $request = new Request('PUT', 'http://local.example', [], Utils::streamFor('foo=bar&hello=world'));
    $curl    = $this->curlFormatter->format($request);

    expect(str_replace('"', '\'', $curl))->toContain("-d 'foo=bar&hello=world'");
    expect($curl)->toContain('-X PUT');
});

test('DELETE', function () {
    $request = new Request('DELETE', 'http://local.example');
    $curl    = $this->curlFormatter->format($request);

    expect($curl)->toContain('-X DELETE');
});

test('HEAD', function () {
    $request = new Request('HEAD', 'http://local.example');
    $curl    = $this->curlFormatter->format($request);

    expect(str_replace('"', '\'', $curl))->toContain("curl 'http://local.example' --head");
});

test('OPTIONS', function () {
    $request = new Request('OPTIONS', 'http://local.example');
    $curl    = $this->curlFormatter->format($request);

    expect($curl)->toContain('-X OPTIONS');
});
