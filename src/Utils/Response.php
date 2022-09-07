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
namespace Joandysson\Keycloak\Utils;

/**
 * Class Response.
 */
class Response
{
    /**
     * @param array $header
     * @param array $body
     * @param int $code
     */
    public function __construct(
        private array $header,
        private array $body,
        private int $code
    ) {
    }

    public function header(): ?array
    {
        return $this->header;
    }

    public function body(): ?array
    {
        return $this->body;
    }

    public function code(): int
    {
        return $this->code;
    }
}
