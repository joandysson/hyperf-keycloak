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
    const CONFIG_FILE = 'keycloak';

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

        $this->oidcConfig = sprintf('%s.%s', self::CONFIG_FILE, 'default');
    }

    /**
     * @return string
     */
    public function host(): string
    {
        return $this->config->get($this->key('oidc_host'));
    }

    /**
     * @return string
     */
    public function clientId(): string
    {
        return $this->config->get($this->key('oidc_client_id'));
    }

    /**
     * @return string
     */
    public function secret(): string
    {
        return $this->config->get($this->key('oidc_client_secret'));
    }

    /**
     * @return string
     */
    public function redirectUri(): string
    {
        return $this->config->get($this->key('oidc_redirect_url'));
    }

    /**
     * @return string
     */
    public function scope(): string
    {
        return $this->config->get($this->key('oidc_scope'));
    }

    /**
     * @return string
     */
    public function timeout(): string
    {
        return $this->config->get($this->key('oidc_timeout'));
    }

    /**
     * @param string $key
     * @return string
     */
    private function key(string $key): string
    {
        return sprintf('%s.%s', $this->oidcConfig, $key);
    }
}
