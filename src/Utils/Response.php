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
    protected int $code;

    protected ?array $body;

    /**
     * @var null|mixed
     */
    protected mixed $error;

    public function __construct(int $code, ?array $body = null)
    {
        $this->body = $body;
        $this->code = $code;
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
