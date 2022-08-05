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

/**
 * Class Response
 * @package Easy\Utils
 * @property-read \stdClass $body
 * @property-read mixed     $error
 * @property-read int       $code
 */
class Response
{
    /** @var int */
    protected int $code;

    /**
     * @var mixed|null
     */

    protected mixed $body;
    /**
     * @var mixed|null
     */
    protected mixed $error;

    /**
     * @param int $code
     * @param $body
     * @param $error
     */
    public function __construct(int $code, $body = null, $error = null)
    {
        $this->body = $body;
        $this->code = $code;
        $this->error = $error;
    }

    /**
     * @return mixed
     */
    public function getBody(): mixed
    {
        return $this->body;
    }

    /**
     * @return mixed
     */
    public function getError(): mixed
    {
        return $this->error;
    }

    /**
     * @return int
     */
    public function getCode(): int
    {
        return $this->code;
    }
}
