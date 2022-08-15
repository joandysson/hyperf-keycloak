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

class AccessToken extends Token
{
    /**
     * AccessToken constructor.
     */
    public function __construct(string $accessToken, int $expiresIn)
    {
        parent::__construct(time() + $expiresIn);
        $this->accessToken = $accessToken;
    }

    public function getBearer(): string
    {
        return $this->accessToken;
    }
}
