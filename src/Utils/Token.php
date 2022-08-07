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

namespace Joandysson\Keycloak\Utils;

/**
 * Class Token
 * @package Easy\Utils
 * @property-read int $expiration
 */
abstract class Token
{
    /** @var string */
    protected string $accessToken;

    /** @var string */
    protected string $refreshToken;

    /**
     * Token constructor.
     * @param int $expiration
     */
    public function __construct(protected int $expiration)
    {
        $this->expiration = $expiration;
    }

    /**
     * @return int
     */
    public function getExpiration(): int
    {
        return $this->expiration;
    }
}
