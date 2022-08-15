<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */
namespace Joandysson\Keycloak;

use Joandysson\Keycloak\Exceptions\CurlException;
use Joandysson\Keycloak\Exceptions\KeycloakException;
use Joandysson\Keycloak\Utils\AccessToken;
use Joandysson\Keycloak\Utils\KeycloakAPI;
use Joandysson\Keycloak\Utils\RefreshToken;
use Joandysson\Keycloak\Utils\Response;

/**
 * Class Keycloak.
 */
class Keycloak
{
    public int $reAuthSleepTime = 30;

    private KeycloakAPI $keycloakAPI;

    /**
     * @throws KeycloakException
     */
    public function __construct(
        private string $host,
        private string $clientId,
        private string $clientSecret,
        private string $redirectUri,
        private string $scopes = '',
        private ?string $state = null
    ) {
        if (! filter_var($this->redirectUri, FILTER_VALIDATE_URL)) {
            throw new KeycloakException('Invalid redirect Uri');
        }

        $this->keycloakAPI = new KeycloakAPI($this->host, $this->redirectUri, $this->clientId, $this->clientSecret);
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

    public function hasApiAccessTokenExpired(): bool
    {
        if (! isset($_SESSION['auth']['api_access_token']['expiration'])) {
            return true;
        }

        if ($_SESSION['auth']['api_access_token']['expiration'] < time()) {
            return true;
        }

        return false;
    }

    public function getRedirectUri(): string
    {
        return $this->redirectUri;
    }

    public function getHost(): string
    {
        return $this->host;
    }

    public function setScopes(string $scopes): void
    {
        $this->scopes = $scopes;
    }

    public function getLoginUrl(): string
    {
        return sprintf(
            '%s/protocol/openid-connect/auth?%s',
            $this->host,
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
            $this->host,
            $this->parameters()
        );

//        TODO: to watch after
//        return '$this->host/protocol/openid-connect/registrations?client_id=$this->clientId&response_type=code&scope=openid%20email&redirect_uri=' .
//            urlencode($this->redirectUri);
    }

    public function getClientId(): string
    {
        return $this->clientId;
    }

    /**
     * @throws KeycloakException
     */
    public function getAccessToken(): AccessToken
    {
        if (! isset($this->accessToken)) {
            throw new KeycloakException('AccessToken is missing.');
        }

        return $this->accessToken;
    }

    /**
     * @throws KeycloakException
     */
    public function getRefreshToken(): RefreshToken
    {
        if (! isset($this->refreshToken)) {
            throw new KeycloakException('RefreshToken is missing.');
        }

        return $this->refreshToken;
    }

    public function getClientSecret(): ?string
    {
        return $this->clientSecret;
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
            'scope' => $this->scopes,
        ]);

        return $this->keycloakAPI->authorization($grantTypeValue);
    }

    /**
     * @return Response
     * @throws CurlException
     */
    public function authorizationClientCredentials(): Response
    {
        $grantTypeValue = $this->prepareGrantTypeValue(GrantTypes::CLIENT_CREDENTIALS, [
            'scope' => $this->scopes,
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
            'client_id' => $this->clientId,
            'response_type' => 'code',
            'redirect_uri' => $this->redirectUri,
        ];

        $parameters = $this->addState($parameters);
        $parameters = $this->addScopes($parameters);

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

    private function addScopes(array $parameters): array
    {
        if (! empty($this->scopes)) {
            return array_merge($parameters, [
                'scope' => $this->scopes,
            ]);
        }

        return $parameters;
    }

    private function prepareGrantTypeValue(string $grantType, array $grantValue): array
    {
        return array_merge(['grant_type' => $grantType], $grantValue);
    }
}
