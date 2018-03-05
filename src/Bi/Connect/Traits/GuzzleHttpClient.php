<?php

namespace Bi\Connect\Traits;

use GuzzleHttp\Client as GuzzleClient;

/**
 * Class GuzzleHttpClient.
 */
trait GuzzleHttpClient
{
    /**
     * @var \GuzzleHttp\Client
     */
    protected $httpClient;

    /**
     * @param string $endpoint
     * @param string $method        Avaliable methods: get, post, put, delete
     * @param array  $params
     * @param array  $customHeaders
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function call($endpoint, $method = 'get', $params = [], $customHeaders = [])
    {
        switch ($method) {
            case 'post':
                $requestParams = ['form_params' => $params];
                break;
            case 'post_json':
                $method = 'post';
                $requestParams = ['json' => $params];
                break;
            case 'get':
            default:
                $requestParams = ['query' => $params];
                break;
        }

        $defaultHeader = [];
        $headers = array_merge($defaultHeader, $requestParams, $customHeaders);

        $response = $this->getHttpClient()->request(
            $method,
            $endpoint,
            $headers
        );

        return $response;
    }

    /**
     * Get the http client.
     *
     * @return \GuzzleHttp\Client
     */
    public function getHttpClient()
    {
        if (($this->httpClient instanceof GuzzleClient) == false) {
            $this->httpClient = new GuzzleClient(
                [
                    'debug'           => false,
                    'allow_redirects' => true,
                ]
            );
        }

        return $this->httpClient;
    }
}
