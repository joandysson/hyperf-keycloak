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
namespace Joandysson\Keycloak;

use Hyperf\Contract\ConfigInterface;
use Psr\Container\ContainerInterface;

class KeycloakFactory
{
    public function __construct(private ContainerInterface $container, private ConfigInterface $config)
    {
    }

    public function get(): string
    {
        return $this->config->get('keycloak', [
            'test' => 'test'
        ]);
    }

    public function set(): string
    {
        return $this->config->get('keycloak', [
            'test' => 'test'
        ]);
    }
}
