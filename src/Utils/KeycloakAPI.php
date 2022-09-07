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

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Joandysson\Keycloak\AdapterConfig;
use Psr\Http\Message\ResponseInterface;

/**
 * Class KeycloakAPI.
 */
class KeycloakAPI
{
    /**
     * @param AdapterConfig $config
     * @param Client $client
     */
    public function __construct(private AdapterConfig $config, private Client $client)
    {
        $this->client = make(Client::class, [
            'config' => $this->config()
        ]);
    }

    /**
     * @param array $grantValue
     * @return ResponseInterface
     * @throws GuzzleException
     */
    public function authorization(array $grantValue): ResponseInterface
    {
        return $this->client->post('/protocol/openid-connect/token', [
            'headers' => $this->getHeaders(),
            'form_params' => $this->formAuthorization($grantValue),
        ]);
    }

    /**
     * @param array $data
     * @return ResponseInterface
     * @throws GuzzleException
     */
    public function introspect(array $data): ResponseInterface
    {
        return $this->client->post('/protocol/openid-connect/token/introspect', [
            'headers' => $this->getHeaders(),
            'form_params' => $this->formIntrospect($data),
        ]);
    }

    /**
     * @param string $refreshToken
     * @return ResponseInterface
     * @throws GuzzleException
     */
    public function logout(string $refreshToken): ResponseInterface
    {
        return $this->client->post('/protocol/openid-connect/logout', [
            'headers' => $this->getHeaders(),
            'form_params' => $this->formLogout($refreshToken),
        ]);
    }

    /**
     * @return string[]
     */
    private function getHeaders(): array
    {
        return [
            'Content-Type' => 'application/x-www-form-urlencoded',
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
                'redirect_uri' => $this->config->redirectUri(),
            ]
        );
    }

    /**
     * @param string $refreshToken
     * @return array
     */
    private function formLogout(string $refreshToken): array
    {
        return array_merge(
            [
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
            'client_id' => $this->config->clientId(),
            'client_secret' => $this->config->secret(),
        ];
    }

    private function config(): array
    {
        return [
            'base_uri' => $this->config->host(),
            'timeout' => $this->config->timeout(),
        ];
    }
}
