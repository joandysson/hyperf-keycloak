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

use Joandysson\Keycloak\Exceptions\CurlException;

/**
 * Class KeycloakAPI.
 */
class KeycloakAPI
{
    public function __construct(
        private string $host,
        private string $redirectUri,
        private string $clientId,
        private string $secret
    ) {
    }

    /**
     * @param array $grantValue
     * @return Response
     * @throws CurlException
     */
    public function authorization(array $grantValue): Response
    {
        $host = sprintf('%s/protocol/openid-connect/token', $this->host);

        return Curl::post(
            $host,
            $this->getHeaders(),
            $this->formAuthorization($grantValue)
        );
    }

    /**
     * @param array $data
     * @return Response
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
     * @param string $refreshToken
     * @return Response
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

    /**
     * @param array $grantValue
     * @return array
     */
    private function formAuthorization(array $grantValue): array
    {
        return array_merge(
            $grantValue,
            $this->clientCredentials(),
            [
                'redirect_uri' => $this->redirectUri,
            ]
        );
    }

    /**
     * @param string $refreshToken
     * @return array
     */
    private function formLogout(string $refreshToken): array
    {
        return array_merge([
                'refresh_token' => $refreshToken,
            ],
            $this->clientCredentials()
        );
    }

    /**
     * @param array $data
     * @return array
     */
    private function formIntrospect(array $data): array
    {
        return array_merge($data, $this->clientCredentials());
    }

    /**
     * @return array
     */
    private function clientCredentials(): array
    {
        return [
            'client_id' => $this->clientId,
            'client_secret' => $this->secret,
        ];
    }

    /**
     * @param string $uri
     * @return string
     */
    private function endpoint(string $uri): string
    {
        return sprintf('%s%s', $this->host, $uri);
    }
}
