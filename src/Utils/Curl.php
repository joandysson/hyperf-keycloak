<?php

declare(strict_types=1);

namespace Easy\Keycloak\Utils;

use CurlHandle;
use Easy\Keycloak\Exceptions\CurlException;

/**
 * This file is part of Hyper Keycloak.
 *
 * @link     https://github.com/joandysson
 * @document https://github.com/joandysson/hyperf-keycloak/blob/main/readme.md
 * @contact  @joandysson
 * @license  https://github.com/joandysson/hyperf-keycloak/blob/main/LICENSE
 */
class Curl
{
    const POST = 'POST';
    const PUT = 'PUT';
    const GET = 'GET';

    /**
     * @var bool|CurlHandle
     */
    private static bool|CurlHandle $ch;

    /**
     * @param string $url
     * @return void
     */
    private static function init(string $url): void
    {
        static::$ch = curl_init($url);
    }

    /**
     * @return void
     */
    private static function setReturnTransfer(): void
    {
        curl_setopt(self::$ch, CURLOPT_RETURNTRANSFER, true);
    }

    /**
     * @param array $headers
     * @return void
     */
    private static function setHeaders(array $headers): void
    {
        curl_setopt(self::$ch, CURLOPT_HTTPHEADER, static::formatHeaders($headers));
    }

    /**
     * @param array|string $parameters
     * @return void
     */
    private static function setParameters(array|string $parameters): void
    {
        $parameters = is_array($parameters)? http_build_query($parameters): $parameters;
        curl_setopt(self::$ch, CURLOPT_POSTFIELDS, $parameters);
    }

    /**
     * @return void
     */
    private static function setMethodPost(): void
    {
        curl_setopt(self::$ch, CURLOPT_POST, true);
    }

    /**
     * @return void
     */
    private static function setMethodPut(): void
    {
        curl_setopt(self::$ch, CURLOPT_CUSTOMREQUEST, 'PUT');
    }

    /**
     * @return string|bool
     */
    private static function exec(): string|bool
    {
        return curl_exec(self::$ch);
    }

    /**
     * @return string
     */
    private static function hasError(): string
    {
        return curl_error(self::$ch);
    }

    /**
     * @return mixed
     */
    private static function getInfo(): mixed
    {
        return curl_getinfo(self::$ch, CURLINFO_HTTP_CODE);
    }

    /**
     * @return void
     */
    private static function close(): void
    {
         curl_close(self::$ch);
    }

    /**
     * @param string $method
     * @return void
     */
    private static function setMethod(string $method): void
    {
//        TODO: TO TEST IT
//        $string = sprintf('%s%s%s%s', __NAMESPACE__, __CLASS__, __METHOD__, ucfirst($method));

        match($method) {
            self::POST => self::setMethodPost(),
            self::PUT => self::setMethodPut(),
            default => null
        };
    }

    /**
     * @param string $method
     * @param string $url
     * @param array $headers
     * @param array|string $parameters
     * @return Response
     * @throws CurlException
     */
    private static function request(string $method, string $url, array $headers, array|string $parameters): Response {
        self::init($url);
        self::setReturnTransfer();
        self::setParameters($parameters);
        self::setHeaders($headers);
        self::setMethod($method);

        $response = self::exec();

        if ($error = self::hasError()) {
            throw new CurlException(sprintf('Curl Error: %s' , $error));
        }

        $httpCode = self::getInfo();

        self::close();

        return new Response($httpCode, json_decode($response), null);
    }

    /**
     * @param string $url
     * @param array  $headers
     * @param mixed|null $parameters
     * @return Response
     * @throws CurlException
     */
    public static function post(string $url, array $headers = [], array $parameters = []): Response
    {
        return self::request(self::POST, $url, $headers, json_encode($parameters));
    }

    /**
     * @param string $url
     * @param array $headers
     * @param array $parameters
     * @return Response
     * @throws CurlException
     */
    public static function put(string $url, array $headers = [], array $parameters = []): Response
    {
        return self::request(self::PUT, $url, $headers, json_encode($parameters));
    }

    /**
     * @param string $url
     * @param array  $headers
     * @return Response
     * @throws CurlException
     */
    public static function get(string $url, array $headers = []): Response
    {
        return self::request(self::GET, $url, $headers, []);
    }

    /**
     * @param array $headers
     * @return array
     */
    private static function formatHeaders(array $headers): array
    {
        foreach ($headers as $key => $header) {
            $arrayHeaders[] = sprintf('%s: %s', $key, $header);
        }

        return $arrayHeaders ?? [];
    }
}
