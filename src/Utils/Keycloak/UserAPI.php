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
namespace Joandysson\Keycloak\Utils\Keycloak;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Joandysson\Keycloak\AdapterConfig;
use Psr\Http\Message\ResponseInterface;

/**
 * Class UserAPI.
 */
class UserAPI
{
    private AdapterConfig $config;

    private Client $client;

    public function __construct()
    {
        $this->config = make(AdapterConfig::class);
        $this->client = make(Client::class, [
            'config' => $this->config(),
        ]);
    }

    /**
     * @throws GuzzleException
     */
    public function create(string $token, array $data): ResponseInterface
    {
        return $this->client->post($this->getUserUri(), [
            'headers' => $this->getHeaders($token),
            'body' => json_encode($data),
        ]);
    }

    /**
     * @throws GuzzleException
     */
    public function update(string $token, string $id, array $data): ResponseInterface
    {
        $uri = sprintf('%s/%s', $this->getUserUri(), $id);
        return $this->client->post($uri, [
            'headers' => $this->getHeaders($token),
            'body' => json_encode($data),
        ]);
    }

    /**
     * @throws GuzzleException
     */
    public function resetPassword(string $token, string $id, array $data): ResponseInterface
    {
        $uri = sprintf('%s/%s/reset-password', $this->getUserUri(), $id);
        return $this->client->post($uri, [
            'headers' => $this->getHeaders($token),
            'body' => json_encode($data),
        ]);
    }

    /**
     * @throws GuzzleException
     */
    public function find(string $token, string $query): ResponseInterface
    {
        $uri = sprintf('%s?q=%s', $this->getUserUri(), $query);
        return $this->client->get($uri, [
            'headers' => $this->getHeaders($token),
        ]);
    }

    /**
     * @throws GuzzleException
     */
    public function findOne(string $token, string $id): ResponseInterface
    {
        $uri = sprintf('%s/%s', $this->getUserUri(), $id);
        return $this->client->post($uri, [
            'headers' => $this->getHeaders($token),
        ]);
    }

    /**
     * @throws GuzzleException
     */
    public function count(string $token): ResponseInterface
    {
        $uri = sprintf('%s/count', $this->getUserUri());
        return $this->client->post($uri, [
            'headers' => $this->getHeaders($token),
        ]);
    }

    private function getHeaders(string $token): array
    {
        return [
            'Content-Type' => 'application/json',
            'Authorization' => sprintf('Bearer %s', $token),
        ];
    }

    private function config(): array
    {
        return [
            'base_uri' => $this->config->host(),
            'timeout' => $this->config->timeout(),
        ];
    }

    private function getUserUri(): string
    {
        return sprintf('/admin/realms/%s/users', $this->config->clientId());
    }
}
