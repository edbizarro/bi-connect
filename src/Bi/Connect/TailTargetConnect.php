<?php

namespace Bi\Connect;

use Bi\Connect\Base\BaseConnect;
use Bi\Connect\Traits\GuzzleHttpClient;

/**
 * Class TailTargetConnect.
 */
class TailTargetConnect extends BaseConnect
{
    use GuzzleHttpClient;

    const API_BASE_URL = 'https://api.tailtarget.com/api/v1/';

    protected $apiAppId;
    protected $apiSecret;
    protected $apiUsername;
    protected $apiPassword;

    protected $sha512Password;
    protected $saltedSecret;

    /**
     * TailTargetConnect constructor.
     *
     * @param $username
     * @param $password
     * @param $appId
     * @param $secret
     */
    public function __construct($username, $password, $appId, $secret)
    {
        $this->apiUsername = $username;
        $this->apiPassword = $password;
        $this->apiAppId    = $appId;
        $this->apiSecret   = $secret;

        $this->sha512Password = hash('sha512', $this->apiPassword);
        $this->saltedSecret   = $this->sha512Password . $this->apiSecret;
    }

    /**
     * getMetrics by services.
     *
     * @param $endPoint
     * @param array $params
     *
     * @return \Bi\Connect\Interfaces\ResponseInterface
     */
    public function getMetrics($endPoint, $params = [])
    {
        $defaultOptions = [
            'credential' => [
                'applicationId' => $this->apiAppId,
                'username' => $this->apiUsername,
                'utcTimestamp' => gmdate('U'),
            ],
        ];

        $defaultHeader = [
            'headers' => [
                'Content-type' => 'application/json',
                'Accept' => 'application/json',
            ],
        ];

        $params = array_merge($params, $defaultOptions);

        $hmac = $this->hmac($params);

        $response = $this->call(
            self::API_BASE_URL . $endPoint . '?hmac=' . $hmac,
            'post_json',
            $params,
            $defaultHeader
        );

        return $this->response($response->getBody()->getContents());
    }

    /**
     * @param $username
     * @param $password
     * @param $appId
     * @param $secret
     */
    public function setCredentials($username, $password, $appId, $secret)
    {
        $this->apiAppId    = $appId;
        $this->apiSecret   = $secret;
        $this->apiUsername = $username;
        $this->apiPassword = $password;
    }

    /**
     * Convert param to hash hmac.
     *
     * @param array $params
     *
     * @return string a string containing the calculated message digest as lowercase hexits
     */
    protected function hmac($params = [])
    {
        $postString = json_encode($params);
        $hmac       = hash_hmac('sha1', $postString, $this->saltedSecret);

        return $hmac;
    }

    /**
     * @param $response
     *
     * @return ConnectResponse
     */
    protected function formatResponse($response)
    {
        $header = $body = [];
        $body   = $rawBody   = json_decode($response, true);

        return new ConnectResponse(
            $header,
            $body,
            $rawBody
        );
    }
}
