<?php

namespace HyperfTest;

use Easy\Keycloak\Exceptions\KeycloakException;
use Easy\Keycloak\KeycloakAdapter;
use PHPUnit\Framework\TestCase;

class AuthTest extends TestCase {

    /**
     * @return void
     */
    public function testOne(): void
    {
//        $key = new KeycloakAdapter([
//            'host' => 'tes', 'realmId' => 'test', 'clientId' => 'test', 'redirectUri' => 11
//        ]);
//
//        print_r($key->getLoginUrl());

        $this->assertEquals(1, 1);
    }
}