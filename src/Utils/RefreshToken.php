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

/**
 * Class RefreshToken
 * @package Easy\Utils
 * @property-read string $refreshToken
 */
class RefreshToken extends Token
{
    protected string $refreshToken;

    /**
     * RefreshToken constructor.
     * @param string $refreshToken
     * @param int    $expiresIn
     */
    public function __construct(string $refreshToken, int $expiresIn)
    {
        parent::__construct(time() + $expiresIn);
        $this->refreshToken = $refreshToken;
    }

    /**
     * @return string
     */
    public function getRefreshToken(): string
    {
        return $this->refreshToken;
    }
}
