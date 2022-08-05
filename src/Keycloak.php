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
use Joandysson\Keycloak\Utils\AuthorizationResponse;
use Joandysson\Keycloak\Utils\KeycloakAPI;
use Joandysson\Keycloak\Utils\RefreshToken;
use Joandysson\Keycloak\Utils\UserProfile;
use Exception;

/**
 * Class Keycloak
 * @package Joandysson\Keycloak
 */
class Keycloak
{
    /** @var KeycloakAdapter */
    protected KeycloakAdapter $keycloak;

    /** @var string */
    protected string $state;

    /**
     * Re-authentication loads KeycloakAdapter, so keep this number as high as possible.
     * This means the re-authentication process with Refresh Token will be skipped for 30 seconds after last
     * re-authentication.
     *
     * @var int
     */
    public int $reAuthSleepTime = 30;

    /**
     * Keycloak constructor.
     * @param KeycloakAdapter $keycloak
     */
    public function __construct(KeycloakAdapter $keycloak)
    {
        $this->keycloak = $keycloak;
    }

    /**
     * @param string|null $authorizationCode
     * @return bool
     * @throws CurlException
     * @throws Exceptions\KeycloakException
     */
    public function authorize(string $authorizationCode = null): bool
    {
        if (empty($authorizationCode)) {
            return false;
        }

        $response = KeycloakAPI::getAuthorization($this->keycloak, $authorizationCode);

        $this->setAuthorized(true);
        $this->authorized($this->getUserProfile($response));

        return true;
    }

    /**
     * Authorizes and returns TRUE or FALSE.
     * And triggers method authorized() and setAuthorized()
     *
     * @return bool
     * @throws KeycloakException
     */
    public function invokeForceAuthorization(): bool
    {
        $refreshToken = $this->getRefreshToken();

        // waiting for next re-auth
        if (time() < ($this->getLastReAuth() + $this->reAuthSleepTime)) {
            return true;
        }

        try {
            $response = KeycloakAPI::reauthorize($this->keycloak, $refreshToken);
            $this->setAuthorized(true);
            $this->authorized($this->getUserProfile($response));
            $this->notifyReAuth();
        } catch (Exception $e) {
            // TODO: To remove it
            header('Location: ' . $this->keycloak->getLoginUrl());
            exit();
        }

        return true;
    }

    public function isSessionExpired(): bool
    {
        $refreshToken = $this->getRefreshToken();

        // waiting for next re-auth
        if (time() < ($this->getLastReAuth() + $this->reAuthSleepTime)) {
            return false;
        }

        try {
            $response = KeycloakAPI::reauthorize($this->keycloak, $refreshToken);
            $this->setAuthorized(true);
            $this->authorized($this->getUserProfile($response));
            $this->notifyReAuth();

            return false;
        } catch (Exception $e) {
            return true;
        }
    }

    /**
     * @param AuthorizationResponse $response
     * @return UserProfile
     */
    private function getUserProfile(AuthorizationResponse $response): UserProfile
    {
        $userIdentity = $response->accessToken->getUserIdentity();

        return new UserProfile(
            $userIdentity->getId(),
            $userIdentity->getName(),
            $userIdentity->getEmail(),
            $response->refreshToken->refreshToken,
            $response->refreshToken->expiration,
            $userIdentity->getRoles($this->keycloak->clientId),
            $userIdentity->username
        );
    }

    /**
     * @throws CurlException
     */
    public function logoutSSO(): void
    {
        $refreshToken = $this->getRefreshToken();
        KeycloakAPI::logout($this->keycloak, $refreshToken);
    }

    /**
     * Returns a login URL to KeycloakAdapter.
     *
     * @return string
     * @throws KeycloakException
     */
    public function getLoginUrl(): string
    {
        return $this->keycloak->getLoginUrl() . '&state=' . $this->state;
    }

    /**
     * @return string
     */
    public function getRegistrationUrl(): string
    {
        return $this->keycloak->getRegistrationUrl();
    }

    /**
     * @return bool
     */
    abstract public function isAuthorized(): bool;

    /**
     * @param bool $authorized
     * @return bool
     */
    abstract protected function setAuthorized(bool $authorized): bool;

    /**
     * @param UserProfile $userProfile
     * @return bool
     */
    abstract protected function authorized(UserProfile $userProfile): bool;

    public function setAuthSleep(int $seconds): void
    {
    }

    /**
     * @return int
     */
    private function getLastReAuth(): int
    {
        if (isset($_SESSION['auth']['lastReAuth'])) {
            return $_SESSION['auth']['lastReAuth'];
        }

        return 0;
    }

    private function notifyReAuth(): void
    {
        $_SESSION['auth']['lastReAuth'] = time();
    }

    /**
     * @return RefreshToken
     */
    abstract protected function getRefreshToken(): RefreshToken;

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
    public function logIn(string $username, string $password): bool
    {
        $userProfile = KeycloakAPI::logIn($this->keycloak, $username, $password);

        return $this->authorized($userProfile);
    }
}
