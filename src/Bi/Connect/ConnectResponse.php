<?php

namespace Bi\Connect;

use Bi\Connect\Traits\ResponseTrait;
use Tightenco\Collect\Support\Collection;
use Bi\Connect\Interfaces\ResponseInterface;

/**
 * Class ConnectResponse.
 */
class ConnectResponse implements ResponseInterface
{
    use ResponseTrait;

    /**
     * ConnectResponse constructor.
     *
     * @param array $header
     * @param array $body
     * @param $rawBody
     */
    public function __construct(array $header, array $body, $rawBody)
    {
        $this->header = new Collection($header);
        $this->body = new Collection($body);
        $this->rawBody = $rawBody;

        return $this;
    }
}
