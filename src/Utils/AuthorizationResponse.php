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

namespace Easy\Keycloak\Utils;

use stdClass;

/**
 * @property-read AccessToken  $accessToken
 * @property-read RefreshToken $refreshToken
 * @property-read string       $sessionState
 */
class AuthorizationResponse
{
    /** @var stdClass */
    private stdClass $response;

    /** @var AccessToken */
    protected AccessToken $accessToken;

    /** @var RefreshToken */
    protected RefreshToken $refreshToken;

    /** @var string */
    protected string $sessionState;

    /**
     * AuthorizationResponse constructor.
     * @param stdClass $response
     */
    public function __construct(stdClass $response)
    {
        $this->response = $response;
    }

    /**
     * @return AccessToken
     */
    public function getAccessToken(): AccessToken
    {
        return new AccessToken($this->response->access_token, $this->response->expires_in);
    }

    /**
     * @return RefreshToken
     */
    public function getRefreshToken(): RefreshToken
    {
        return new RefreshToken($this->response->refresh_token, $this->response->refresh_expires_in);
    }

    /**
     * @return string
     */
    public function getSessionState(): string
    {
        return $this->response->session_state;
    }
}
