<?php

namespace Bi\Connect\Interfaces;

use Tightenco\Collect\Support\Collection;

/**
 * Interface ResponseInterface.
 */
interface ResponseInterface
{
    /**
     * Get response header.
     *
     * @return \Tightenco\Collect\Support\Collection
     */
    public function getHeader();

    /**
     * Get response body.
     *
     * @return \Tightenco\Collect\Support\Collection
     */
    public function getBody();

    /**
     * Get raw response from API call.
     *
     * @return mixed
     */
    public function getRawResponse();

    /**
     * Format the response from sources.
     *
     * @param $originalResponse
     *
     * @return mixed
     */
    public function formatResponse($originalResponse): Collection;
}
