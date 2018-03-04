<?php

namespace Bi\Connect\Interfaces;

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
}
