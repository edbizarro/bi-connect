<?php

namespace Bi\Connect\Base;

use Bi\Connect\Interfaces\ConnectInterface;

/**
 * Class BaseConnect.
 */
abstract class BaseConnect implements ConnectInterface
{
    /**
     * Get the http connector.
     *
     * @return mixed
     */
    public function getHttpClient()
    {
    }

    /**
     * @param $response
     *
     * @return \Bi\Connect\Interfaces\ResponseInterface
     */
    public function response($response)
    {
        return $this->formatResponse($response);
    }
}
