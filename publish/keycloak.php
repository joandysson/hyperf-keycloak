<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf Fakie.
 *
 * @link     https://github.com/jainec
 * @document https://github.com/jainec/hyperf-fakie/blob/master/README.md
 * @contact  @jaineccs
 * @license  https://github.com/jainec/hyperf-fakie/blob/master/LICENSE
 */

return [
    'oidc_host' => 'http://localhost/realms/<your-realm>', // The host of your Keycloak server
    'oidc_client_id' => 'client', // client id
    'oidc_client_secret' => 'scret', // client secret
    'oidc_redirect_url' => 'http://localhost/callback', // callback url
    'oidc_scope' => 'openid', // global scope
];
