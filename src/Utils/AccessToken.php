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

class AccessToken extends Token
{
    /** @var string */
    public string $bearer;

    /**
     * AccessToken constructor.
     * @param string $accessToken
     * @param int    $expiresIn
     */
    public function __construct(string $accessToken, int $expiresIn)
    {
        parent::__construct(time() + $expiresIn);
        $this->bearer = $accessToken;
    }

    /**
     * @return UserIdentity
     */
    public function getUserIdentity(): UserIdentity
    {
        $exploded = explode('.', $this->bearer);
        $stdObject = json_decode(
            base64_decode(str_pad(
                strtr($exploded[1], '-_', '+/'),
                strlen($exploded[1]) % 4,
                '=',
                STR_PAD_RIGHT
            ))
        );

        return new UserIdentity($stdObject);
    }

    /**
     * @return string
     */
    public function getBearer(): string
    {
        return $this->bearer;
    }
}
