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
namespace Joandysson\Keycloak;

use GuzzleHttp\Exception\GuzzleException;
use Joandysson\Keycloak\Utils\GrantTypes;
use Joandysson\Keycloak\Utils\OidcAPI;
use Psr\Http\Message\ResponseInterface;

/**
 * Class Keycloak.
 */
class Keycloak
{
    private OidcAPI $keycloakAPI;

    private AdapterConfig $config;

    private string $scope;

    private string $state = '';

    public function __construct()
    {
        $this->config = make(AdapterConfig::class);
        $this->keycloakAPI = make(OidcAPI::class, ['config' => $this->config]);
        $this->scope = $this->config->scope();
    }

    /**
     * Sets the state.
     *
     * The state is a string that returns with a code from KeycloakAdapter, when an user has successfully logged in.
     */
    public function setState(string $state): void
    {
        $this->state = $state;
    }

    /**
     * @return string
     */
    public function getRedirectUri(): string
    {
        return $this->config->redirectUri();
    }

    /**
     * @return string
     */
    public function getHost(): string
    {
        return $this->config->host();
    }

    /**
     * @param string $scope
     * @return void
     */
    public function setScope(string $scope): void
    {
        $this->scope = sprintf('%s %s', $this->config->scope(), $scope);
    }

    /**
     * @return string
     */
    public function getLoginUrl(): string
    {
        return sprintf(
            '%s/protocol/openid-connect/auth?%s',
            $this->config->host(),
            $this->parameters()
        );
    }

    /**
     * @throws GuzzleException
     */
    public function logout(string $refreshToken): ResponseInterface
    {
        return $this->keycloakAPI->logout($refreshToken);
    }

    public function getClientId(): string
    {
        return $this->config->clientId();
    }

    /**
     * @return string|null
     */
    public function getClientSecret(): ?string
    {
        return $this->config->secret();
    }

    /**
     * @throws GuzzleException
     */
    public function authorizationCode(string $code): ResponseInterface
    {
        $grantTypeValue = $this->prepareGrantTypeValue(GrantTypes::AUTHORIZATION_CODE, [
            'code' => $code,
        ]);

        return $this->keycloakAPI->authorization($grantTypeValue);
    }

    /**
     * @throws GuzzleException
     */
    public function authorizationToken(string $refreshToken): ResponseInterface
    {
        $grantTypeValue = $this->prepareGrantTypeValue(GrantTypes::REFRESH_TOKEN, [
            'refresh_token' => $refreshToken,
        ]);

        return $this->keycloakAPI->authorization($grantTypeValue);
    }

    /**
     * @throws GuzzleException
     */
    public function authorizationLogin(string $username, string $password): ResponseInterface
    {
        $grantTypeValue = $this->prepareGrantTypeValue(GrantTypes::PASSWORD, [
            'username' => $username,
            'password' => $password,
            'scope' => $this->scope,
        ]);

        return $this->keycloakAPI->authorization($grantTypeValue);
    }

    /**
     * @throws GuzzleException
     */
    public function authorizationClientCredentials(): ResponseInterface
    {
        $grantTypeValue = $this->prepareGrantTypeValue(GrantTypes::CLIENT_CREDENTIALS, [
            'scope' => $this->scope,
        ]);

        return $this->keycloakAPI->authorization($grantTypeValue);
    }

    /**
     * @throws GuzzleException
     */
    public function introspect(string $token, string $username): ResponseInterface
    {
        return $this->keycloakAPI->introspect([
            'token' => $token,
            'username' => $username,
        ]);
    }

    /**
     * @return string
     */
    private function parameters(): string
    {
        $parameters = [
            'client_id' => $this->config->clientId(),
            'response_type' => 'code',
            'redirect_uri' => $this->config->redirectUri(),
        ];

        $parameters = $this->addState($parameters);
        $parameters = $this->addScope($parameters);

        return http_build_query($parameters, '', null, PHP_QUERY_RFC3986);
    }

    /**
     * @param array $parameters
     * @return array
     */
    private function addState(array $parameters): array
    {
        if ($this->state) {
            return array_merge($parameters, [
                'state' => $this->state,
            ]);
        }

        return $parameters;
    }

    /**
     * @param array $parameters
     * @return array
     */
    private function addScope(array $parameters): array
    {
        if (empty($this->scope)) {
            return $parameters;
        }

        return array_merge($parameters, ['scope' => $this->scope]);
    }

    /**
     * @param string $grantType
     * @param array $grantValue
     * @return array
     */
    private function prepareGrantTypeValue(string $grantType, array $grantValue): array
    {
        return array_merge(['grant_type' => $grantType], $grantValue);
    }
}
