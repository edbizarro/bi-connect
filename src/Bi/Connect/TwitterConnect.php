<?php

//
//namespace Bi\Connect;
//
//use Bi\Connect\Base\Oauth2WithoutRedirectConnect;
//use Bi\Connect\Exceptions\TwitterConnectException;
//use Bi\Connect\Traits\GuzzleHttpClient;
//
///**
// * Class TwitterConnect.
// */
//class TwitterConnect extends Oauth2WithoutRedirectConnect
//{
//    use GuzzleHttpClient;
//
//    const API_VERSION = '1.1';
//    const API_BASE_URL = 'https://api.twitter.com/';
//
//    protected $apiConsumerKey;
//    protected $apiConsumerSecret;
//    protected $apiBearerToken;
//
//    /**
//     * TwitterConnect constructor.
//     *
//     * @param string $consumerKey
//     * @param string $consumerSecret
//     */
//    public function __construct($consumerKey, $consumerSecret)
//    {
//        $this->apiConsumerKey = $consumerKey;
//        $this->apiConsumerSecret = $consumerSecret;
//    }
//
//    /**
//     * @throws TwitterConnectException
//     *
//     * @return mixed
//     */
//    public function auth()
//    {
//        if ($this->apiBearerToken != null) {
//            return $this->apiBearerToken;
//        }
//
//        $response = $this->post(
//            'oauth2/token',
//            ['grant_type' => 'client_credentials'],
//            ['auth'       => [$this->apiConsumerKey, $this->apiConsumerSecret]]
//        );
//
//        $this->apiBearerToken = $response->getBody()->all()['access_token'];
//
//        return $this->apiBearerToken;
//    }
//
//    /**
//     * @param $endPoint
//     * @param $method
//     * @param array $params
//     * @param array $headers
//     *
//     * @throws TwitterConnectException
//     *
//     * @return ConnectResponse
//     */
//    protected function doRequest($endPoint, $method, $params = [], $headers = [])
//    {
//        $endPoint = $this->formatEndPoint($endPoint);
//
//        $defaultHeader = [
//            'headers' => [
//                'Content-type' => 'application/x-www-form-urlencoded;charset=UTF-8',
//                'Accept'       => 'application/json',
//            ],
//        ];
//
//        if ($this->apiBearerToken != null) {
//            $defaultHeader['headers']['Authorization'] = 'Bearer '.$this->apiBearerToken;
//        }
//
//        $defaultHeader = array_merge($defaultHeader, $headers);
//
//        try {
//            $response = $this->call(
//                $endPoint,
//                $method,
//                $params,
//                $defaultHeader
//            );
//        } catch (\Exception $e) {
//            throw new TwitterConnectException($e);
//        }
//
//        return $this->response($response->getBody()->getContents());
//    }
//
//    /**
//     * @param $endPoint
//     * @param array $params
//     * @param array $headers
//     *
//     * @return ConnectResponse
//     */
//    public function get($endPoint, $params = [], $headers = [])
//    {
//        $response = $this->doRequest(
//            $endPoint,
//            'get',
//            $params,
//            $headers
//        );
//
//        return $response;
//    }
//
//    /**
//     * @param $endPoint
//     * @param array $params
//     * @param array $headers
//     *
//     * @return ConnectResponse
//     */
//    public function post($endPoint, $params = [], $headers = [])
//    {
//        $response = $this->doRequest(
//            $endPoint,
//            'post',
//            $params,
//            $headers
//        );
//
//        return $response;
//    }
//
//    /**
//     * @return string
//     */
//    protected function getBaseEndPoint()
//    {
//        return self::API_BASE_URL.'/';
//    }
//
//    /**
//     * @return string
//     */
//    protected function getBaseEndPointWithVersion()
//    {
//        return self::API_BASE_URL.self::API_VERSION.'/';
//    }
//
//    /**
//     * @return mixed
//     */
//    public function getAccessToken()
//    {
//        return $this->apiBearerToken;
//    }
//
//    /**
//     * @param $endPoint
//     *
//     * @return string
//     */
//    protected function formatEndPoint($endPoint)
//    {
//        if (strpos($endPoint, 'oauth2') === false) {
//            return $this->getBaseEndPointWithVersion().$endPoint;
//        }
//
//        return $this->getBaseEndPoint().$endPoint;
//    }
//
//    /**
//     * @param $response
//     *
//     * @return ConnectResponse
//     */
//    protected function formatResponse($response)
//    {
//        $header = $body = [];
//        $body = $rawBody = json_decode($response, true);
//
//        return new ConnectResponse(
//            $header,
//            $body,
//            $rawBody
//        );
//    }
//
//    /**
//     * @param $query
//     * @param $params
//     *
//     * @return ConnectResponse
//     */
//    public function search($query, $params)
//    {
//        $params = array_merge(
//            [
//                'q' => $query,
//            ],
//            $params
//        );
//
//        return $this->get('search/tweets', $params);
//    }
//}
