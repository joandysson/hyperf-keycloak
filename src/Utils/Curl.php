<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */
namespace Joandysson\Keycloak\Utils;

use CurlHandle;
use Joandysson\Keycloak\Exceptions\CurlException;

class Curl
{
    public const POST = 'POST';

    public const PUT = 'PUT';

    public const GET = 'GET';

    private static bool|CurlHandle $ch;

    /**
     * @throws CurlException
     */
    public static function post(string $url, array $headers = [], array $body = []): Response
    {
        return self::request(self::POST, $url, $headers, $body);
    }

    /**
     * @throws CurlException
     */
    public static function put(string $url, array $headers = [], array $parameters = []): Response
    {
        return self::request(self::PUT, $url, $headers, $parameters);
    }

    /**
     * @throws CurlException
     */
    public static function get(string $url, array $headers = []): Response
    {
        return self::request(self::GET, $url, $headers, []);
    }

    private static function init(string $url): void
    {
        static::$ch = curl_init($url);
    }

    private static function setReturnTransfer(): void
    {
        curl_setopt(self::$ch, CURLOPT_RETURNTRANSFER, true);
    }

    private static function setHeaders(array $headers): void
    {
        curl_setopt(self::$ch, CURLOPT_HTTPHEADER, static::formatHeaders($headers));
    }

    private static function setBody(array|string $parameters): void
    {
        $parameters = is_array($parameters) ? http_build_query($parameters) : $parameters;
        curl_setopt(self::$ch, CURLOPT_POSTFIELDS, $parameters);
    }

    private static function setMethodPost(): void
    {
        curl_setopt(self::$ch, CURLOPT_POST, true);
    }

    private static function setMethodPut(): void
    {
        curl_setopt(self::$ch, CURLOPT_CUSTOMREQUEST, 'PUT');
    }

    private static function exec(): string|bool
    {
        return curl_exec(self::$ch);
    }

    private static function hasError(): string
    {
        return curl_error(self::$ch);
    }

    private static function getInfo(): mixed
    {
        return curl_getinfo(self::$ch, CURLINFO_HTTP_CODE);
    }

    private static function close(): void
    {
        curl_close(self::$ch);
    }

    private static function setMethod(string $method): void
    {
        match ($method) {
            self::POST => self::setMethodPost(),
            self::PUT => self::setMethodPut(),
            default => null
        };
    }

    /**
     * @throws CurlException
     */
    private static function request(string $method, string $url, array $headers, array|string $body): Response
    {
        self::init($url);
        self::setReturnTransfer();
        self::setBody($body);
        self::setHeaders($headers);
        self::setMethod($method);

        $response = self::exec();

        if ($error = self::hasError()) {
            throw new CurlException(sprintf('Curl Error: %s', $error));
        }

        $httpCode = self::getInfo();

        self::close();

        return new Response($httpCode, json_decode($response, true));
    }

    private static function formatHeaders(array $headers): array
    {
        foreach ($headers as $key => $header) {
            $arrayHeaders[] = sprintf('%s: %s', $key, $header);
        }

        return $arrayHeaders ?? [];
    }
}
