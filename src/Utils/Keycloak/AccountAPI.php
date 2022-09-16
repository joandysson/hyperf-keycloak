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
class AccountAPI
{
    private Client $client;

    public function __construct(private AdapterConfig $config)
    {
        $this->client = make(Client::class, [
            'config' => $this->config(),
        ]);
    }

    /**
     * @param string $token
     * @return ResponseInterface
     * @throws GuzzleException
     */
    public function getUser(string $token): ResponseInterface
    {
        return $this->client->get($this->getAccountUri(), [
            'headers' => $this->getHeaders($token),
        ]);
    }

    /**
     * @param string $token
     * @param array $data
     * @return ResponseInterface
     * @throws GuzzleException
     */
    public function update(string $token, array $data): ResponseInterface
    {
        return $this->client->post($this->getAccountUri(), [
            'headers' => $this->getHeaders($token),
            'body' => json_encode($data),
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

    /**
     * @return string
     */
    private function getAccountUri(): string
    {
        return sprintf('/realms/%s/account', $this->config->clientId());
    }
}
