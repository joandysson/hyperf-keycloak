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
namespace Joandysson\Keycloak\Utils;

use stdClass;

/**
 * @property AccessToken $accessToken
 * @property RefreshToken $refreshToken
 * @property string $sessionState
 */
class AuthorizationResponse
{
    protected AccessToken $accessToken;

    protected RefreshToken $refreshToken;

    protected string $sessionState;

    private stdClass $response;

    /**
     * @param stdClass $response
     */
    public function __construct(stdClass $response)
    {
        $this->response = $response;
    }

    public function getAccessToken(): AccessToken
    {
        return new AccessToken($this->response->access_token, $this->response->expires_in);
    }

    public function getRefreshToken(): RefreshToken
    {
        return new RefreshToken($this->response->refresh_token, $this->response->refresh_expires_in);
    }

    public function getSessionState(): string
    {
        return $this->response->session_state;
    }
}
