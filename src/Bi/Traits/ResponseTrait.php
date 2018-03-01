<?php

namespace Bi\Connect\Traits;

/**
 * Class ResponseTrait.
 */
trait ResponseTrait
{
    /**
     * @var \Tightenco\Collect\Support\Collection
     */
    protected $header;

    /**
     * @var \Tightenco\Collect\Support\Collection
     */
    protected $body;
    protected $rawBody;

    /**
     * Get response header.
     *
     * @return \Tightenco\Collect\Support\Collection
     */
    public function getHeader()
    {
        return $this->header;
    }

    /**
     * Get response body.
     *
     * @return \Tightenco\Collect\Support\Collection
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Get raw response from API call.
     *
     * @return mixed
     */
    public function getRawResponse()
    {
        return $this->rawBody;
    }
}
