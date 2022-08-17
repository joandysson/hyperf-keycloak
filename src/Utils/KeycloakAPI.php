<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf Keycloak.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */
namespace Joandysson\Keycloak\Utils;

use Joandysson\Keycloak\AdapterConfig;
use Joandysson\Keycloak\Exceptions\CurlException;

/**
 * Class KeycloakAPI.
 */
class KeycloakAPI
{
    public function __construct(private AdapterConfig $config)
    {
    }

    /**
     * @throws CurlException
     */
    public function authorization(array $grantValue): Response
    {
        $host = sprintf('%s/protocol/openid-connect/token', $this->config->host());

        return Curl::post(
            $host,
            $this->getHeaders(),
            $this->formAuthorization($grantValue)
        );
    }

    /**
     * @throws CurlException
     */
    public function introspect(array $data): Response
    {
        return Curl::post(
            $this->endpoint('/protocol/openid-connect/token/introspect'),
            $this->getHeaders(),
            $this->formIntrospect($data)
        );
    }

    /**
     * @throws CurlException
     */
    public function logout(string $refreshToken): Response
    {
        return Curl::post(
            $this->endpoint('/protocol/openid-connect/logout'),
            $this->getHeaders(),
            $this->formLogout($refreshToken)
        );
    }

    /**
     * @return string[]
     */
    private function getHeaders(): array
    {
        return [
            'Content-Type' => 'application/x-www-form-urlencoded',
            // 'Authorization' => 'Bearer ' . $this->apiAccessToken->bearer
        ];
    }

    private function formAuthorization(array $grantValue): array
    {
        return array_merge(
            $grantValue,
            $this->clientCredentials(),
            [
                'redirect_uri' => $this->config->redirectUri(),
            ]
        );
    }

    private function formLogout(string $refreshToken): array
    {
        return array_merge(
            [
                'refresh_token' => $refreshToken,
            ],
            $this->clientCredentials()
        );
    }

    private function formIntrospect(array $data): array
    {
        return array_merge($data, $this->clientCredentials());
    }

    private function clientCredentials(): array
    {
        return [
            'client_id' => $this->config->clientId(),
            'client_secret' => $this->config->secret(),
        ];
    }

    private function endpoint(string $uri): string
    {
        return sprintf('%s%s', $this->config->host(), $uri);
    }
}
