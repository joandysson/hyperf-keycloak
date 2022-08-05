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

namespace Easy\Keycloak\Utils;

use Easy\Keycloak\Exceptions\CurlException;
use Easy\Keycloak\Exceptions\KeycloakException;
use Easy\Keycloak\KeycloakAdapter;
use Easy\Keycloak\KeycloakAdapterExtended;

/**
 * Class KeycloakAPI
 * @package Easy\Utils
 */
class KeycloakAPI
{
    /**
     * @param KeycloakAdapter $keycloak
     * @param string $authorizationCode
     * @return AuthorizationResponse
     * @throws CurlException
     * @throws KeycloakException
     */
    public static function getAuthorization(KeycloakAdapter $keycloak, string $authorizationCode): AuthorizationResponse
    {
        $request = [
            'grant_type'   => 'authorization_code',
            'code'         => $authorizationCode,
            'client_id'    => $keycloak->clientId,
            'redirect_uri' => $keycloak->getRedirectUri()
        ];

        $response = Curl::post('$keycloak->host/protocol/openid-connect/token', [
            'Content-Type' => 'application/x-www-form-urlencoded'
        ], $request);

        if (isset($response->body->error)) {
            throw new CurlException($response->body->error . ': ' . $response->body->error_description);
        }

        if (isset($response->body->access_token)) {
            return new AuthorizationResponse($response->body);
        }

        throw new KeycloakException('???');
    }

    /**
     * @param KeycloakAdapterExtended $keycloak
     * @return AuthorizationResponse
     * @throws CurlException
     * @throws KeycloakException
     */
    public static function getApiAuthorization(KeycloakAdapterExtended $keycloak): AuthorizationResponse
    {
        $url = sprintf('%s/protocol/openid-connect/token', $keycloak->getHost());

        $response = Curl::post($url, [
            'Content-Type' => 'application/x-www-form-urlencoded'
        ], [
            'grant_type'    => 'password',
            'client_id'     => $keycloak->apiClientId,
            'client_secret' => $keycloak->apiClientSecret,
            'username'      => $keycloak->apiUsername,
            'password'      => $keycloak->apiPassword
        ]);

        if (isset($response->body->error)) {
            throw new CurlException($response->body->error . ': ' . $response->body->error_description);
        }

        if (isset($response->body->access_token)) {
            return new AuthorizationResponse($response->body);
        }

        throw new KeycloakException('???');
    }

    /**
     * @param KeycloakAdapterExtended $keycloak
     * @param string           $username
     * @param string           $firstname
     * @param string           $lastname
     * @param string           $email
     * @param bool             $enabled
     * @param array            $groups
     * @param bool             $emailVerified
     * @return bool
     * @throws CurlException
     */
    public static function createUser(
        KeycloakAdapterExtended $keycloak,
        string                  $username,
        string                  $firstname,
        string                  $lastname,
        string                  $email,
        bool                    $enabled = true,
        array                   $groups = ['default-group'],
        bool                    $emailVerified = false
    ): bool {
        $request = [
            'username'  => $username,
            'firstName' => $firstname,
            'lastName'  => $lastname,
            'email'     => $email,
            'enabled'   => $enabled,
            'groups'    => $groups
        ];
        if ($emailVerified) {
            $request['emailVerified'] = true;
        }

        $url = sprintf('%s/users', $keycloak->getHost());
        $response = Curl::post($url, [
            'Content-Type'  => 'application/json',
            'Authorization' => 'Bearer ' . $keycloak->apiAccessToken->bearer
        ], $request);

        if ($response->code == 201) {
            return true;
        }

        throw new CurlException('User creation failed. HTTP response code: $response->code');
    }

    /**
     * @param KeycloakAdapterExtended $keycloak
     * @param string           $email
     * @return User
     * @throws CurlException
     */
    public static function getUserByEmail(
        KeycloakAdapterExtended $keycloak,
        string                  $email
    ): User {
        $url = sprintf('%s/users?email=%s', $keycloak->getHost(),  urlencode($email));
        $response = Curl::get($url, [
            'Content-Type'  => 'application/json',
            'Authorization' => 'Bearer ' . $keycloak->apiAccessToken->bearer
        ]);

        if ($response->code == 200) {
            foreach ($response->body as $user) {
                if ($user->email == $email) {
                    return new User($user->id, $user->firstName, $user->lastName, $user->email);
                }
            }

            throw new CurlException('An user identified by an email $email does not exist.');
        }

        throw new CurlException('Getting user by email failed. HTTP response code: $response->code ($response->error)');
    }

    /**
     * @param KeycloakAdapter $keycloak
     * @param RefreshToken $userRefreshToken
     * @return AuthorizationResponse
     * @throws CurlException
     * @throws KeycloakException
     */
    public static function reauthorize(KeycloakAdapter $keycloak, RefreshToken $userRefreshToken): AuthorizationResponse
    {
        $response = Curl::post('$keycloak->host/protocol/openid-connect/token', [
            'Content-Type' => 'application/x-www-form-urlencoded'
        ], [
            'grant_type'    => 'refresh_token',
            'refresh_token' => $userRefreshToken->refreshToken,
            'client_id'     => $keycloak->clientId,
            'client_secret' => $keycloak->clientSecret,
            'redirect_uri'  => $keycloak->getRedirectUri()
        ]);

        if (isset($response->body->error)) {
            throw new CurlException($response->body->error . ': ' . $response->body->error_description ?? 'no error description');
        }

        if (isset($response->body->access_token)) {
            return new AuthorizationResponse($response->body);
        }

        throw new KeycloakException('Unknown error');
    }

    /**
     * @param KeycloakAdapter     $keycloak
     * @param RefreshToken $userRefreshToken
     * @return bool
     * @throws CurlException
     */
    public static function logout(KeycloakAdapter $keycloak, RefreshToken $userRefreshToken): bool
    {
        $data = [
            'refresh_token' => $userRefreshToken->refreshToken,
            'client_id'     => $keycloak->clientId
        ];

        if (!empty($keycloak->clientSecret)) {
            $data['client_secret'] = $keycloak->clientSecret;
        }

        $response = Curl::post('$keycloak->host/protocol/openid-connect/logout', [
            'Content-Type' => 'application/x-www-form-urlencoded'
        ], $data);

        if ($response->code == 200 || $response->code == 204) {
            return true;
        }

        if (isset($response->body->error)) {
            throw new CurlException('HTTP $response->code: ' . $response->body->error . ': ' .
                $response->body->error_description);
        } else {
            throw new CurlException('HTTP $response->code: ' . $response->error);
        }
    }

    /**
     * @throws CurlException
     */
    public static function userExists(KeycloakAdapterExtended $keycloak, string $email): bool
    {
        $url = sprintf('%s/users?email=%s', $keycloak->getHost(), urlencode($email));
        $response = Curl::get($url, [
            'Authorization' => 'Bearer ' . $keycloak->apiAccessToken->bearer
        ]);

        if (is_array($response->body)) {
            foreach ($response->body as $user) {
                if ((isset($user->username)) && ($email == $user->username) ||
                    (isset($user->email)) && ($email == $user->email)
                ) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @throws CurlException
     */
    public static function getUsernameByEmail(KeycloakAdapterExtended $keycloak, string $email): ?string
    {
        $url = sprintf('%s/users?email=%s', $keycloak->getHost(), urlencode($email));
        $response = Curl::get($url, [
            'Authorization' => 'Bearer ' . $keycloak->apiAccessToken->bearer
        ]);

        return (isset($response->body[0]->username) && isset($response->body[0]->email) &&
            ($email == $response->body[0]->email)) ? $response->body[0]->username : null;
    }

    /**
     * @param KeycloakAdapterExtended $keycloak
     * @param string           $keycloakId
     * @param string           $password
     * @param bool             $temporary
     * @return bool
     * @throws CurlException
     */
    public static function setPassword(
        KeycloakAdapterExtended $keycloak,
        string                  $keycloakId,
        string                  $password,
        bool                    $temporary = false
    ): bool
    {
        $url = sprintf('%s/users/%s/reset-password',$keycloak->getHost(), $keycloakId);

        $response = Curl::put($url, [
                'Content-Type'  => 'application/json',
                'Authorization' => 'Bearer ' . $keycloak->apiAccessToken->bearer,
            ],
            [
                'temporary' => $temporary,
                'type'      => 'password',
                'value'     => $password
            ]
        );

        if ($response->code == 204) {
            return true;
        }

        if (isset($response->body->error)) {
            throw new CurlException('HTTP $response->code: ' . $response->body->error . ': ' .
                $response->body->error_description);
        } else {
            throw new CurlException('HTTP $response->code: ' . $response->error);
        }
    }

    /**
     * @param KeycloakAdapter $keycloak
     * @param string $username
     * @param string $password
     * @return UserProfile
     * @throws CurlException
     */
//    public static function logIn(
//        KeycloakAdapter $keycloak,
//        string          $username,
//        string          $password
//    ): UserProfile {
//        $response = Curl::post('$keycloak->host/protocol/openid-connect/token', [
//            'Content-Type' => 'application/x-www-form-urlencoded',
//        ], [
//            'grant_type'    => 'password',
//            'client_id'     => $keycloak->clientId,
//            'client_secret' => $keycloak->clientSecret,
//            'username'      => $username,
//            'password'      => $password,
//            'scope'         => 'openid'
//        ]);
//
//        if ($response->code == 200) {
//            $response = new AuthorizationResponse($response->body);
//            $userIdentity = $response->accessToken->getUserIdentity();
//
//            return new UserProfile(
//                $userIdentity->getId(),
//                $userIdentity->getName(),
//                $userIdentity->getEmail(),
//                $response->refreshToken->refreshToken,
//                $response->refreshToken->expiration,
//                $userIdentity->getRoles($keycloak->clientId),
//                $userIdentity->username
//            );
//        }
//
//        if (isset($response->body->error)) {
//            $error = sprintf('HTTP %s: %s : %s',
//                $response->code,
//                $response->body->error,
//                $response->body->error_description
//            );
//
//            throw new CurlException($error);
//        }
//
//        $error = sprintf('HTTP %s: %s', $response->code, $response->error);
//        throw new CurlException($error);
//    }

    public function bearerAuthorization(string $token): array
    {
        return [
            'Authorization' => sprintf('Bearer %s', $token)
        ];
    }
}
