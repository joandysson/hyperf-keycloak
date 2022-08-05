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
 * Class UserProfile
 * @package Easy\Adapters\Utils
 * @property-read string   $id
 * @property-read string   $name
 * @property-read string   $email
 * @property-read string   $refreshToken
 * @property-read int      $refreshTokenExpiration
 * @property-read string[] $roles
 * @property-read string   $username
 */
class UserProfile
{
    /** @var string */
    protected string $username;

    /**
     * @var string
     */
    protected string $refreshToken;

    /**
     * @var string
     */
    protected string $email;

    /**
     * @var string
     */
    protected string $name;

    /**
     * @var string
     */
    protected string $id;

    /** @var string[] */
    protected array $roles;

    /** @var int */
    protected int $refreshTokenExpiration;

    /**
     * @param string $id
     * @param string $name
     * @param string $email
     * @param string $refreshToken
     * @param int $refreshTokenExpiration
     * @param array $roles
     * @param string $username
     */
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

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @return string
     */
    public function getRefreshToken(): string
    {
        return $this->refreshToken;
    }

    /**
     * @return int
     */
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
