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
use Joandysson\Keycloak\Exceptions\KeycloakException;

/**
 * Class AdapterConfig.
 */
class AdapterConfig
{
    public const CONFIG_FILE = 'keycloak';

    private string $oidcConfig;

    /**
     * @param ConfigInterface $config
     * @throws KeycloakException
     */
    public function __construct(
        private ConfigInterface $config
    ) {
        if (! filter_var($this->redirectUri(), FILTER_VALIDATE_URL)) {
            throw new KeycloakException('Invalid redirect Uri');
        }

        $this->oidcConfig = $this->configFileValue($this->getDefault());
    }

    private function getDefault(): string
    {
        $default = $this->configFileValue('default');
        return $this->config->get($default);
    }

    private function configFileValue(string $value): string
    {
        return sprintf('%s.%s', self::CONFIG_FILE, 'default');
    }

    public function host(): string
    {
        return $this->config->get($this->key('oidc_host'));
    }

    public function clientId(): string
    {
        return $this->config->get($this->key('oidc_client_id'));
    }

    public function secret(): string
    {
        return $this->config->get($this->key('oidc_client_secret'));
    }

    public function redirectUri(): string
    {
        return $this->config->get($this->key('oidc_redirect_url'));
    }

    public function scope(): string
    {
        return $this->config->get($this->key('oidc_scope'));
    }

    public function timeout(): string
    {
        return $this->config->get($this->key('oidc_timeout'));
    }

    private function key(string $key): string
    {
        return sprintf('%s.%s', $this->oidcConfig, $key);
    }
}
