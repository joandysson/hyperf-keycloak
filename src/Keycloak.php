<?php

declare(strict_types=1);

/**
 * This file is part of Hyper Keycloak.
 *
 * @link     https://github.com/joandysson
 * @document https://github.com/joandysson/hyperf-keycloak/blob/main/readme.md
 * @contact  @joandysson
 * @license  https://github.com/joandysson/hyperf-keycloak/blob/main/LICENSE
 */

namespace Joandysson\Keycloak;

use Joandysson\Keycloak\Exceptions\CurlException;
use Joandysson\Keycloak\Exceptions\KeycloakException;
use Joandysson\Keycloak\Utils\KeycloakAPI;
use Joandysson\Keycloak\Utils\AccessToken;
use Joandysson\Keycloak\Utils\RefreshToken;

/**
 * Class Keycloak
 * @package Joandysson\Keycloak
 */
class Keycloak
{
    /** @var string|null */
    private ?string $state = null;

    /** @var array */
    private array $scopes = [];

    /** @var int */
    public int $reAuthSleepTime = 30;

    /** @var string */
    private string $apiPassword;

    /**
     * @var string
     */
    private string $apiUsername;

    /**
     * @var string
     */
    private string $apiClientSecret;

    /**
     * @var string
     */
    private string $apiClientId;

    /** @var AccessToken */
    private AccessToken $apiAccessToken;

    /** @var RefreshToken */
    private RefreshToken $apiRefreshToken;

    /**
     * @param string $host
     * @param string $clientId
     * @param string $clientSecret
     * @param string $redirectUri
     * @throws KeycloakException
     */
    public function __construct(
        protected string $host,
        protected string $clientId,
        protected string $clientSecret,
        protected string $redirectUri
    ){
        if (!filter_var($this->redirectUri, FILTER_VALIDATE_URL)) {
            throw new KeycloakException('Invalid redirect Uri');
        }
    }

    /**
     * Keycloak constructor.
     * @param KeycloakAdapter $keycloak
     */

    /**
     * Sets the state
     *
     * The state is a string that returns with a code from KeycloakAdapter, when an user has successfully logged in.
     *
     * @param string $state
     */
    public function setState(string $state): void
    {
        $this->state = $state;
    }

    /**
     * @param string $username
     * @param string $password
     * @return bool
     * @throws CurlException
     */
    public function logIn(string $username, string $password)
    {
        $userProfile = KeycloakAPI::logIn($this->keycloak, $username, $password);

        // return $this->authorized($userProfile);
    }

    /**
     * @return string
     */
    public function getApiClientId(): string
    {
        return $this->apiClientId;
    }

    /**
     * @return string
     */
    public function getApiClientSecret(): string
    {
        return $this->apiClientSecret;
    }

    /**
     * @return string
     */
    public function getApiPassword(): string
    {
        return $this->apiPassword;
    }

    /**
     * @return string
     */
    public function getApiUsername(): string
    {
        return $this->apiUsername;
    }

    /**
     * @return RefreshToken
     * @throws CurlException
     */
    public function getApiRefreshToken(): RefreshToken
    {
        return $this->apiRefreshToken;
    }

    /**
     * @return AccessToken
     * @throws CurlException
     * @throws KeycloakException
     */
    public function getApiAccessToken(): AccessToken
    {

        return $this->apiAccessToken;
    }

    /**
     * @param string $firstname
     * @param string $lastname
     * @param string $email
     * @return bool
     * @throws CurlException
     */
    public function createUser(string $firstname, string $lastname, string $email): bool
    {
        return KeycloakAPI::createUser($this, $email, $firstname, $lastname, $email);
    }

    /**
     * @return bool
     */
    public function hasApiAccessTokenExpired(): bool
    {
        if (!isset($_SESSION['auth']['api_access_token']['expiration'])) {
            return true;
        }

        if ($_SESSION['auth']['api_access_token']['expiration'] < time()) {
            return true;
        }

        return false;
    }
    /**
     * @return string
     */
    public function getRedirectUri(): string
    {
        return $this->redirectUri;
    }

    /**
     * @return string
     */
    public function getHost(): string
    {
        return $this->host;
    }

    /**
     * @param array $scopes
     * @return void
     */
    public function setScopes(array $scopes): void
    {
        $this->scopes = array_merge($this->scopes, $scopes);
    }

    /**
     * @return string
     * @throws KeycloakException
     */
    public function getLoginUrl(): string
    {
        return sprintf('%s/protocol/openid-connect/auth?%s',
            $this->host,
            $this->parameters()
        );
    }

    private function parameters(): string
    {
        $parameters = [
            'client_id' => $this->clientId,
            'response_type' => 'code',
            'redirect_uri' => $this->redirectUri
        ];

        if ($this->state) {
            $parameters = array_merge($parameters, [
                'state' => $this->state
            ]);
        }

        if (!empty($this->scopes)) {
            $parameters = array_merge($parameters, [
                'scope' => implode(' ', $this->scopes)
            ]);
        }

        return http_build_query($parameters);
    }

    /**
     * @return string
     */
    public function getRegistrationUrl(): string
    {
        return sprintf('%s/clients-registrations/openid-connect?%s',
            $this->host, $this->parameters());

//        TODO: to watch after
//        return '$this->host/protocol/openid-connect/registrations?client_id=$this->clientId&response_type=code&scope=openid%20email&redirect_uri=' .
//            urlencode($this->redirectUri);
    }

    /**
     * @return string
     */
    public function getClientId(): string
    {
        return $this->clientId;
    }

    /**
     * @return AccessToken
     * @throws KeycloakException
     */
    public function getAccessToken(): AccessToken
    {
        if (!isset($this->accessToken)) {
            throw new KeycloakException('AccessToken is missing.');
        }

        return $this->accessToken;
    }

    /**
     * @return RefreshToken
     * @throws KeycloakException
     */
    public function getRefreshToken(): RefreshToken
    {
        if (!isset($this->refreshToken)) {
            throw new KeycloakException('RefreshToken is missing.');
        }

        return $this->refreshToken;
    }

    /**
     * @return string|null
     */
    public function getClientSecret(): ?string
    {
        return $this->clientSecret;
    }

}
