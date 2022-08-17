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

use Joandysson\Keycloak\Exceptions\CurlException;
use Joandysson\Keycloak\Exceptions\KeycloakException;
use Joandysson\Keycloak\Utils\KeycloakAPI;
use Joandysson\Keycloak\Utils\Response;

/**
 * Class Keycloak.
 */
class Keycloak
{
    public int $reAuthSleepTime = 30;

    private KeycloakAPI $keycloakAPI;

    private AdapterConfig $config;

    private string $scope;

    private string $state;

    /**
     * @throws KeycloakException
     */
    public function __construct()
    {
        /* @var AdapterConfig */
        $this->config = make(AdapterConfig::class, ['oidcConfig' => 'keycloak']);
        $this->keycloakAPI = new KeycloakAPI($this->config);
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

    public function getRedirectUri(): string
    {
        return $this->config->redirectUri();
    }

    public function getHost(): string
    {
        return $this->config->host();
    }

    public function setScope(string $scope): void
    {
        $this->scope = $scope;
    }

    public function getLoginUrl(): string
    {
        return sprintf(
            '%s/protocol/openid-connect/auth?%s',
            $this->config->host(),
            $this->parameters()
        );
    }

    /**
     * @throws CurlException
     */
    public function logout(string $refreshToken): Response
    {
        return $this->keycloakAPI->logout($refreshToken);
    }

    public function getRegistrationUrl(): string
    {
        return sprintf(
            '%s/clients-registrations/openid-connect?%s',
            $this->config->host(),
            $this->parameters()
        );

//        TODO: to watch after
//        return '$this->config->host()/protocol/openid-connect/registrations?client_id=$this->config->clientId()&response_type=code&scope=openid%20email&redirect_uri=' .
//            urlencode($this->redirectUri);
    }

    public function getClientId(): string
    {
        return $this->config->clientId();
    }

    public function getClientSecret(): ?string
    {
        return $this->config->secret();
    }

    /**
     * @throws CurlException
     */
    public function authorizationCode(string $code): Response
    {
        $grantTypeValue = $this->prepareGrantTypeValue(GrantTypes::AUTHORIZATION_CODE, [
            'code' => $code,
        ]);

        return $this->keycloakAPI->authorization($grantTypeValue);
    }

    /**
     * @throws CurlException
     */
    public function authorizationToken(string $refreshToken): Response
    {
        $grantTypeValue = $this->prepareGrantTypeValue(GrantTypes::REFRESH_TOKEN, [
            'refresh_token' => $refreshToken,
        ]);

        return $this->keycloakAPI->authorization($grantTypeValue);
    }

    /**
     * @throws CurlException
     */
    public function authorizationLogin(string $username, string $password): Response
    {
        $grantTypeValue = $this->prepareGrantTypeValue(GrantTypes::PASSWORD, [
            'username' => $username,
            'password' => $password,
            'scope' => $this->scope,
        ]);

        return $this->keycloakAPI->authorization($grantTypeValue);
    }

    /**
     * @throws CurlException
     */
    public function authorizationClientCredentials(): Response
    {
        $grantTypeValue = $this->prepareGrantTypeValue(GrantTypes::CLIENT_CREDENTIALS, [
            'scope' => $this->scope,
        ]);

        return $this->keycloakAPI->authorization($grantTypeValue);
    }

    /**
     * @throws CurlException
     */
    public function introspect(string $token, string $username): Utils\Response
    {
        return $this->keycloakAPI->introspect([
            'token' => $token,
            'username' => $username,
        ]);
    }

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

    private function addState(array $parameters): array
    {
        if ($this->state) {
            return array_merge($parameters, [
                'state' => $this->state,
            ]);
        }

        return $parameters;
    }

    private function addScope(array $parameters): array
    {
        $scope = sprintf('%s %s', $this->config->scope(), $this->scope);

        if (! empty($this->scope)) {
            return array_merge($parameters, [
                'scope' => $scope,
            ]);
        }

        return $parameters;
    }

    private function prepareGrantTypeValue(string $grantType, array $grantValue): array
    {
        return array_merge(['grant_type' => $grantType], $grantValue);
    }
}
