<?php

namespace Joandysson\Keycloak\Adapter\Keycloak;

use GuzzleHttp\Exception\GuzzleException;
use Joandysson\Keycloak\Utils\Keycloak\UserAPI;
use Psr\Http\Message\ResponseInterface;

class User
{
    /**
     * @var array
     */
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
     * @throws GuzzleException
     */
    public function find(string $token, $filters = []): ResponseInterface
    {
        return $this->userAPI->find($token, $this->formatFilters($filters));
    }

    /**
     * @param array $filters
     * @return string
     */
    private function formatFilters(array $filters): string
    {

        $query = '';
        foreach($filters as $key => $value) {
            $query .=  sprintf('%s:%s ', $key,$value);
        }

        return trim($query);

    }

    /**
     * @param $userInfo
     * @return array
     */
    private function generatePayload($userInfo): array {
        return array_merge($userInfo, $this->payload ?? []);
    }

    /**
     * @param array $credentials
     */
    public function setCredentials(array $credentials): void
    {
        $this->payload['credentials'] = $credentials;
    }

    /**
     * @param array $attributes
     */
    public function setAttributes(array $attributes): void
    {
        $this->payload['attributes'] = $attributes;
    }

    /**
     * @param array $realmRoles
     */
    public function setRealmRoles(array $realmRoles): void
    {
        $this->payload['realmRoles'] = $realmRoles;
    }

    /**
     * @param array $clientConsents
     */
    public function setClientConsents(array $clientConsents): void
    {
        $this->payload['clientConsents'] = $clientConsents;
    }

    /**
     * @param array $disableableCredentialTypes
     */
    public function setDisableableCredentialTypes(array $disableableCredentialTypes): void
    {
        $this->payload['disableableCredentialTypes'] = $disableableCredentialTypes;
    }

    /**
     * @param array $requiredActions
     */
    public function setRequiredActions(array $requiredActions): void
    {
        $this->payload['requiredActions'] = $requiredActions;
    }

    /**
     * @param array $access
     */
    public function setAccess(array $access): void
    {
        $this->payload['access'] = $access;
    }

    /**
     * @param array $clientRoles
     */
    public function setClientRoles(array $clientRoles): void
    {
        $this->payload['clientRoles'] = $clientRoles;
    }

}