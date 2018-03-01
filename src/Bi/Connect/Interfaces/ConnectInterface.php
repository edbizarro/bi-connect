<?php

namespace Bi\Connect\Interfaces;

/**
 * Interface ConnectInterface.
 */
interface ConnectInterface
{
    /**
     * Get the http connector.
     *
     * @return mixed
     */
    public function getHttpClient();

    /**
     * @param $response
     *
     * @return \Bi\Connect\Interfaces\ConnectResponseInterface
     */
    public function response($response);
}
