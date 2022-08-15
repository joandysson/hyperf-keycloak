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
 * Class UserProfile.
 * @property string $id
 * @property string $name
 * @property string $email
 * @property string $refreshToken
 * @property int $refreshTokenExpiration
 * @property string[] $roles
 * @property string $username
 */
class UserProfile
{
    protected string $username;

    protected string $refreshToken;

    protected string $email;

    protected string $name;

    protected string $id;

    /** @var string[] */
    protected array $roles;

    protected int $refreshTokenExpiration;

    public function __construct(
        string $id,
        string $name,
        string $email,
        string $refreshToken,
        int $refreshTokenExpiration,
        array $roles,
        string $username
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->email = $email;
        $this->refreshToken = $refreshToken;
        $this->refreshTokenExpiration = $refreshTokenExpiration;
        $this->roles = $roles;
        $this->username = $username;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getRefreshToken(): string
    {
        return $this->refreshToken;
    }

    public function getRefreshTokenExpiration(): int
    {
        return $this->refreshTokenExpiration;
    }

    /**
     * @return string[]
     */
    public function getRoles(): array
    {
        return $this->roles;
    }
}
