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

namespace Easy\Keycloak;

use Easy\Keycloak\Exceptions\CurlException;
use Easy\Keycloak\Exceptions\KeycloakException;
use Easy\Keycloak\Utils\AccessToken;
use Easy\Keycloak\Utils\KeycloakAPI;
use Easy\Keycloak\Utils\RefreshToken;

/**
 * @property-read string       $apiClientId
 * @property-read string       $apiClientSecret
 * @property-read string       $apiUsername
 * @property-read string       $apiPassword
 * @property-read AccessToken  $apiAccessToken
 * @property-read RefreshToken $apiRefreshToken
 */
class KeycloakAdapterExtended extends KeycloakAdapter
{
    /** @var string */
    protected string $apiPassword;

    /**
     * @var string
     */
    protected string $apiUsername;

    /**
     * @var string
     */
    protected string $apiClientSecret;

    /**
     * @var string
     */
    protected string $apiClientId;

    /** @var AccessToken */
    protected AccessToken $apiAccessToken;

    /** @var RefreshToken */
    protected RefreshToken $apiRefreshToken;

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
        parent::__construct(
             $this->host,
             $this->clientId,
             $this->clientSecret,
             $this->redirectUri
        );

        if (!filter_var($this->redirectUri, FILTER_VALIDATE_URL)) {
            throw new KeycloakException('Invalid $redirectUri');
        }
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
        if (!isset($this->apiRefreshToken)) {
            $this->obtainTokens();

            return $this->apiRefreshToken;
        }

        return $this->apiRefreshToken;
    }

    /**
     * @return void
     * @throws CurlException
     * @throws KeycloakException
     */
    private function obtainTokens(): void
    {
        $response = KeycloakAPI::getApiAuthorization($this);
        $this->apiAccessToken = $response->accessToken;
        $this->apiRefreshToken = $response->refreshToken;

        $_SESSION['auth']['api_access_token']['expiration'] = $this->apiAccessToken->expiration;
        $_SESSION['auth']['api_refresh_token']['expiration'] = $this->apiRefreshToken->expiration;
    }

    /**
     * @return AccessToken
     * @throws CurlException
     * @throws KeycloakException
     */
    public function getApiAccessToken(): AccessToken
    {
        if (!isset($this->apiAccessToken)) {
            $this->obtainTokens();

            return $this->apiAccessToken;
        }

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
}
