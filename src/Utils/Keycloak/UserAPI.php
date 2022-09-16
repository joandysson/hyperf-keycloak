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
 * Class AccountAPI.
 */
class UserAPI
{
    private AdapterConfig $config;

    private Client $client;

    public function __construct()
    {
        $this->config = make(AdapterConfig::class);
        $this->client = make(Client::class, [
           'config' => $this->config()
        ]);
    }

    /**
     * @param string $token
     * @param array $data
     * @return ResponseInterface
     * @throws GuzzleException
     */
    public function create(string $token, array $data): ResponseInterface
    {
        return $this->client->get($this->getUserUri(), [
            'headers' => $this->getHeaders($token),
            'body' => json_encode($data)
        ]);
    }


    /**
     * @param string $token
     * @param string $query
     * @return ResponseInterface
     * @throws GuzzleException
     */
    public function find(string $token, string $query): ResponseInterface
    {
        $uri = sprintf('%s?q=%s', $this->getUserUri(), $query);
        return $this->client->get('/admin/realms/easy/users', [
            'headers' => $this->getHeaders($token)
        ]);
    }

    /**
     * @param string $token
     * @return array
     */
    private function getHeaders(string $token): array
    {
        return [
            'Content-Type' => 'application/json',
            'Authorization' => sprintf('Bearer %s', $token),
        ];
    }

    /**
     * @return array
     */
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
