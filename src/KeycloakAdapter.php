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
 * @property-read string       $host
 * @property-read string       $clientId
 * @property-read AccessToken  $accessToken
 * @property-read RefreshToken $refreshToken;
 * @property-read string       $loginUrl
 * @property-read string|null  $clientSecret
 */
class KeycloakAdapter
{
    /** @var array */
    protected array $scopes;

    /** @var string */
    protected string $state;

    /** @var AccessToken */
    protected AccessToken $accessToken;

    /** @var RefreshToken */
    protected RefreshToken $refreshToken;

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
    ) {
        if (!filter_var($this->redirectUri, FILTER_VALIDATE_URL)) {
            throw new KeycloakException('Invalid $redirectUri');
        }
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

    public function setState(string $state): void
    {
         $this->state =  $state;
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
            'redirect_uri' => urlencode($this->redirectUri)
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
     * @param string $authorizationCode
     * @return AccessToken
     * @throws CurlException
     * @throws KeycloakException
     */
    public function authorize(string $authorizationCode): AccessToken
    {
        $response = KeycloakAPI::getAuthorization($this, $authorizationCode);
        $this->accessToken = $response->accessToken;
        $this->refreshToken = $response->refreshToken;

        $_SESSION['auth']['access_token']['expiration'] = $this->accessToken->expiration;

        return $this->accessToken;
    }

    /**
     * @return string|null
     */
    public function getClientSecret(): ?string
    {
        return $this->clientSecret;
    }
}
