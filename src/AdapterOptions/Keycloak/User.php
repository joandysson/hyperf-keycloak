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
namespace Joandysson\Keycloak\AdapterOptions\Keycloak;

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
     * @param string $token
     * @param array $userInfo
     * @return ResponseInterface
     * @throws GuzzleException
     */
    public function create(string $token, array $userInfo): ResponseInterface
    {
        $payload = $this->generatePayload($userInfo);
        return $this->userAPI->create($token, $payload);
    }

    /**
     * @param string $token
     * @param array $filters
     * @return ResponseInterface
     * @throws GuzzleException
     */
    public function find(string $token, array $filters = []): ResponseInterface
    {
        return $this->userAPI->find($token, $this->formatFilters($filters));
    }

    /**
     * @param $userInfo
     * @return array
     */
    private function generatePayload($userInfo): array
    {
        return array_merge($userInfo, $this->payload ?? []);
    }

    /**
     * @param array $filters
     * @return string
     */
    private function formatFilters(array $filters): string
    {
        $query = '';
        foreach ($filters as $key => $value) {
            $query .= sprintf('%s:%s ', $key, $value);
        }

        return trim($query);
    }

    /**
     * @param array $credentials
     * @return void
     */
    public function setCredentials(array $credentials): void
    {
        $this->payload['credentials'] = $credentials;
    }

    /**
     * @param array $attributes
     * @return void
     */
    public function setAttributes(array $attributes): void
    {
        $this->payload['attributes'] = $attributes;
    }

    /**
     * @param array $realmRoles
     * @return void
     */
    public function setRealmRoles(array $realmRoles): void
    {
        $this->payload['realmRoles'] = $realmRoles;
    }

    /**
     * @param array $clientConsents
     * @return void
     */
    public function setClientConsents(array $clientConsents): void
    {
        $this->payload['clientConsents'] = $clientConsents;
    }

    /**
     * @param array $disableableCredentialTypes
     * @return void
     */
    public function setDisableableCredentialTypes(array $disableableCredentialTypes): void
    {
        $this->payload['disableableCredentialTypes'] = $disableableCredentialTypes;
    }

    /**
     * @param array $requiredActions
     * @return void
     */
    public function setRequiredActions(array $requiredActions): void
    {
        $this->payload['requiredActions'] = $requiredActions;
    }

    /**
     * @param array $access
     * @return void
     */
    public function setAccess(array $access): void
    {
        $this->payload['access'] = $access;
    }

    /**
     * @param array $clientRoles
     * @return void
     */
    public function setClientRoles(array $clientRoles): void
    {
        $this->payload['clientRoles'] = $clientRoles;
    }
}
