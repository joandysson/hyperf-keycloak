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
return [
    'keycloak' => [
        'oidc_host' => env('OIDC_HOST'), // The host of your Keycloak server
        'oidc_client_id' => env('OIDC_CLIENT_ID'), // client id
        'oidc_client_secret' => env('OIDC_CLIENT_SECRET'), // client secret
        'oidc_redirect_url' => env('OIDC_REDIRECT_URL'), // callback url
        'oidc_timeout' => env('OIDC_TIMEOUT', '0'), // timeout
        'oidc_scope' => 'openid', // global scope
    ],
    'default' => 'keycloak'
];
