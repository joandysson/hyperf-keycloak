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
namespace HyperfTest;


//use Joandysson\Keycloak\KeycloakAdapter;
use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversNothing
 */
class AuthTest extends TestCase
{
//    public function testOne(): void
//    {
////        $key = new KeycloakAdapter([
////            'host' => 'tes', 'realmId' => 'test', 'clientId' => 'test', 'redirectUri' => 11
////        ]);
////
////        print_r($key->getLoginUrl());
//
//        $this->assertEquals(1, 1);
//    }

    public function testGuzzle(): void
    {
        $options = [
            'base_uri' => 'http://opss.com:8000/casa/nova',
            'timeout' => '2'
        ];

        $ops = new Client($options);

        $data = $ops->get('oss');

        echo print_r($data);
        $this->assertEquals(1, 1);
    }
}
