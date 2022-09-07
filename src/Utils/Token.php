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
namespace Joandysson\Keycloak\Utils;

/**
 * Class Token.
 * @property int $expiration
 */
abstract class Token
{
    protected string $accessToken;

    protected string $refreshToken;

    /**
     * Token constructor.
     */
    public function __construct(protected int $expiration)
    {
    }

    public function getExpiration(): int
    {
        return $this->expiration;
    }
}
