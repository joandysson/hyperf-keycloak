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
namespace Joandysson\Keycloak;

use Hyperf\Contract\ConfigInterface;
use Psr\Container\ContainerInterface;

/**
 * Class KeycloakFactory.
 */
class KeycloakFactory
{
    public function __construct(private ContainerInterface $container, private ConfigInterface $config)
    {
    }

    public function get(): string
    {
        return $this->config->get('keycloak', [
            'test' => 'test',
        ]);
    }

    public function set(): string
    {
        return $this->config->get('keycloak', [
            'test' => 'test',
        ]);
    }
}
