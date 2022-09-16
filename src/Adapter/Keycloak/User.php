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
namespace Joandysson\Keycloak\Adapter\Keycloak;

use GuzzleHttp\Exception\GuzzleException;
use Joandysson\Keycloak\Utils\Keycloak\UserAPI;
use Psr\Http\Message\ResponseInterface;

class User
{
    private array $payload;

    /**
     * @param UserAPI $userAPI
     */
    public function __construct(
        private UserAPI $userAPI
    ) {
    }

    /**
     * @throws GuzzleException
     */
    public function create(string $token, array $userInfo): ResponseInterface
    {
        $payload = $this->generatePayload($userInfo);
        return $this->userAPI->create($token, $payload);
    }

    /**
     * @param mixed $filters
     * @throws GuzzleException
     */
    public function find(string $token, $filters = []): ResponseInterface
    {
        return $this->userAPI->find($token, $this->formatFilters($filters));
    }

    public function setCredentials(array $credentials): void
    {
        $this->payload['credentials'] = $credentials;
    }

    public function setAttributes(array $attributes): void
    {
        $this->payload['attributes'] = $attributes;
    }

    public function setRealmRoles(array $realmRoles): void
    {
        $this->payload['realmRoles'] = $realmRoles;
    }

    public function setClientConsents(array $clientConsents): void
    {
        $this->payload['clientConsents'] = $clientConsents;
    }

    public function setDisableableCredentialTypes(array $disableableCredentialTypes): void
    {
        $this->payload['disableableCredentialTypes'] = $disableableCredentialTypes;
    }

    public function setRequiredActions(array $requiredActions): void
    {
        $this->payload['requiredActions'] = $requiredActions;
    }

    public function setAccess(array $access): void
    {
        $this->payload['access'] = $access;
    }

    public function setClientRoles(array $clientRoles): void
    {
        $this->payload['clientRoles'] = $clientRoles;
    }

    private function formatFilters(array $filters): string
    {
        $query = '';
        foreach ($filters as $key => $value) {
            $query .= sprintf('%s:%s ', $key, $value);
        }

        return trim($query);
    }

    /**
     * @param $userInfo
     */
    private function generatePayload($userInfo): array
    {
        return array_merge($userInfo, $this->payload ?? []);
    }
}
