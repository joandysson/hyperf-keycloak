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

use stdClass;

/**
 * @property-read string   $email
 * @property-read string   $name
 * @property-read string   $id
 * @property-read string   $username
 * @property-read string[] $roles
 */
class UserIdentity
{
    /** @var string[] */
    private array $roles;

    /** @var string */
    protected string $username;
    protected string $id;
    protected string $name;
    protected string $email;

    /**
     * UserIdentity constructor.
     * @param stdClass $userIdentity
     */
    public function __construct(stdClass $userIdentity)
    {
        $this->id = $userIdentity->sub;
        $this->email = $userIdentity->email;
        $this->name = $userIdentity->name;
        $this->username = $userIdentity->preferred_username;
        $this->roles = $userIdentity->resource_access;
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
    public function getName(): string
    {
        return $this->name;
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
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @param string $clientId
     * @return string[]
     */
    public function getRoles(string $clientId): array
    {
        if (isset($this->roles->{$clientId})) {
            $roles = [];
            foreach ($this->roles->{$clientId}->roles as $role) {
                $roles[] = $role;
            }

            return $roles;
        }

        return [];
    }
}
