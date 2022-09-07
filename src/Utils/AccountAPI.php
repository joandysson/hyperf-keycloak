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
 * Class AccountAPI.
 */
class AccountAPI
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
    public function getUser(string $token): ResponseInterface
    {
        return $this->client->get('/realms/easy/account', [
            'headers' => $this->getHeaders($token),
        ]);
    }

    /**
     * @throws GuzzleException
     */
    public function update(string $token, array $data): ResponseInterface
    {
        return $this->client->post('/realms/easy/account', [
            'headers' => $this->getHeaders($token),
            'body' => json_encode($data),
        ]);
    }

    private function getHeaders(string $token): array
    {
        return [
            'Content-Type' => 'application/json',
            'Authorization' => sprintf('Bearer %s', $token),
        ];
    }
}
