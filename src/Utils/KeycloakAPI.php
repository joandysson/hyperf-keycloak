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
            'base_uri' => $this->config->host(),
            'timeout' => $this->config->timeout(),
        ]);
    }

    /**
     * @throws GuzzleException
     */
    public function authorization(array $grantValue): ResponseInterface
    {
        return $this->client->post('/protocol/openid-connect/token', [
            'headers' => $this->getHeaders(),
            'form_parameters' => $this->formAuthorization($grantValue),
        ]);
    }

    /**
     * @throws GuzzleException
     */
    public function introspect(array $data): ResponseInterface
    {
        return $this->client->post('/protocol/openid-connect/token/introspect', [
            'headers' => $this->getHeaders(),
            'form_parameters' => $this->formIntrospect($data),
        ]);
    }

    /**
     * @throws GuzzleException
     */
    public function logout(string $refreshToken): ResponseInterface
    {
        return $this->client->post('/protocol/openid-connect/logout', [
            'headers' => $this->getHeaders(),
            'form_parameters' => $this->formLogout($refreshToken),
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
}
