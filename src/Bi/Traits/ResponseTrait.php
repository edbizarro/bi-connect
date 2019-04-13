<?php

namespace Bi\Connect\Traits;

use Tightenco\Collect\Support\Collection;

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

    /**
     * @var
     */
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

    /**
     * Format the response from sources.
     *
     * @param $originalResponse
     * @return mixed
     */
    public function formatResponse($originalResponse): Collection
    {
    }
}
