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

/**
 * Class RefreshToken.
 * @property string $refreshToken
 */
class RefreshToken extends Token
{
    /**
     * RefreshToken constructor.
     */
    public function __construct(string $refreshToken, int $expiresIn)
    {
        parent::__construct(time() + $expiresIn);
        $this->refreshToken = $refreshToken;
    }

    public function getRefreshToken(): string
    {
        return $this->refreshToken;
    }
}
