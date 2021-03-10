<?php

use GuzzleHttp\Psr7\Utils;
use Namshi\Cuzzle\Formatter\CurlFormatter;
use GuzzleHttp\Psr7\Request;

beforeEach(function () {
    $this->curlFormatter = new CurlFormatter();
});

test('multiline disabled', function () {
    $this->curlFormatter->setCommandLineLength(10);

    $request = new Request('GET', 'http://example.local', ['foo' => 'bar']);
    $curl    = $this->curlFormatter->format($request);

    $this->assertEquals(substr_count($curl, "\n"), 2);
});

test('skip host in headers', function () {
    $request = new Request('GET', 'http://example.local');
    $curl    = $this->curlFormatter->format($request);

    $this->assertEquals("curl 'http://example.local'", $curl);
});

test('simple get', function () {
    $request = new Request('GET', 'http://example.local');
    $curl    = $this->curlFormatter->format($request);

    $this->assertEquals("curl 'http://example.local'", $curl);
});

test('simple GET with header', function () {
    $request = new Request('GET', 'http://example.local', ['foo' => 'bar']);
    $curl    = $this->curlFormatter->format($request);

    $this->assertEquals("curl 'http://example.local' -H 'foo: bar'", $curl);
});

test('simple GET with multiple header', function () {
    $request = new Request('GET', 'http://example.local', ['foo' => 'bar', 'Accept-Encoding' => 'gzip,deflate,sdch']);
    $curl    = $this->curlFormatter->format($request);

    $this->assertEquals("curl 'http://example.local' -H 'foo: bar' -H 'Accept-Encoding: gzip,deflate,sdch'", $curl);
});

test('GET With Query String', function () {
    $request = new Request('GET', 'http://example.local?foo=bar');
    $curl    = $this->curlFormatter->format($request);

    $this->assertEquals("curl 'http://example.local?foo=bar'", $curl);

    $request = new Request('GET', 'http://example.local?foo=bar');
    $curl = $this->curlFormatter->format($request);

    $this->assertEquals("curl 'http://example.local?foo=bar'", $curl);

    $body = Utils::streamFor(http_build_query(['foo' => 'bar', 'hello' => 'world'], '', '&'));

    $request = new Request('GET', 'http://example.local',[],$body);
    $curl    = $this->curlFormatter->format($request);

    $this->assertEquals("curl 'http://example.local' -G  -d 'foo=bar&hello=world'",$curl);
});

test('POST', function () {
    $body = Utils::streamFor(http_build_query(['foo' => 'bar', 'hello' => 'world'], '', '&'));

    $request = new Request('POST', 'http://example.local', [], $body);
    $curl    = $this->curlFormatter->format($request);

    expect($curl)->not()->toContain(" -G ");
    expect($curl)->toContain("-d 'foo=bar&hello=world'");
});

test('large POST request', function () {
    ini_set('memory_limit', -1);

    $body = str_repeat('A', 1024*1024*64);

    $request = new Request('POST', 'http://example.local', [], \GuzzleHttp\Psr7\stream_for($body));
    $curl = $this->curlFormatter->format($request);

    expect($curl)->not()->toBeNull();
});

test('HEAD', function () {
    $request = new Request('HEAD', 'http://example.local');
    $curl    = $this->curlFormatter->format($request);

    expect($curl)->toContain("--head");
});

test('OPTIONS', function () {
    $request = new Request('OPTIONS', 'http://example.local');
    $curl    = $this->curlFormatter->format($request);

    expect($curl)->toContain("-X OPTIONS");
});

test('DELETE', function () {
    $request = new Request('DELETE', 'http://example.local/users/4');
    $curl    = $this->curlFormatter->format($request);

    expect($curl)->toContain("-X DELETE");
});

test('PUT', function () {
    $request = new Request('PUT', 'http://example.local', [], Utils::streamFor('foo=bar&hello=world'));
    $curl    = $this->curlFormatter->format($request);

    expect($curl)->toContain("-d 'foo=bar&hello=world'");
    expect($curl)->toContain("-X PUT");
});

test('proper body relative', function () {
    $request = new Request('PUT', 'http://example.local', [], Utils::streamFor('foo=bar&hello=world'));
    $curl    = $this->curlFormatter->format($request);

    expect($curl)->toContain("-d 'foo=bar&hello=world'");
    expect($curl)->toContain("-X PUT");
});

test('extract body argument', function ($headers, $body) {
    // clean input of null bytes
    $body = str_replace(chr(0), '', $body);
    $request = new Request('POST', 'http://example.local', $headers, Utils::streamFor($body));

    $curl = $this->curlFormatter->format($request);

    expect($curl)->toContain('foo=bar&hello=world');
})->with([
    [
        ['X-Foo' => 'Bar'],
        chr(0). 'foo=bar&hello=world',
    ]
]);
