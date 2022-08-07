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

class AccessToken extends Token
{
    /**
     * AccessToken constructor.
     * @param string $accessToken
     * @param int    $expiresIn
     */
    public function __construct(string $accessToken, int $expiresIn)
    {
        parent::__construct(time() + $expiresIn);
        $this->accessToken = $accessToken;
    }

    /**
     * @return string
     */
    public function getBearer(): string
    {
        return $this->accessToken;
    }
}
