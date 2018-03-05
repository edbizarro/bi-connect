<?php

namespace Bi\Connect\Facebook;

use Facebook\Facebook;

class InstagramAdsService
{
    /**
     * @var Facebook
     */
    protected $facebookClient;

    /**
     * @var \Bi\Connect\Facebook\FacebookConnect
     */
    protected $facebookConnect;

    public function __construct(FacebookConnect $facebookConnect)
    {
        $this->facebookClient = $facebookConnect->getClient();
        $this->facebookConnect = $facebookConnect;
    }

    /**
     * @param $endPoint
     * @param string $method
     *
     * @throws \Facebook\Exceptions\FacebookSDKException
     *
     * @return array
     */
    protected function doRequest($endPoint, $method = 'GET')
    {
        $request = new \Facebook\FacebookRequest(
            $this->facebookClient->getApp(),
            $this->facebookClient->getDefaultAccessToken(),
            $method,
            $endPoint
        );
        $response = $this->facebookClient->getClient()->sendRequest($request)->getDecodedBody();

        return $response;
    }

    /**
     * @param $endPoint
     *
     * @return string
     */
    public function get($endPoint)
    {
        return $this->facebookConnect->response($this->doRequest($endPoint, 'GET'));
    }

    /**
     * @param $endPoint
     *
     * @return string
     */
    public function post($endPoint)
    {
        return $this->facebookConnect->response($this->doRequest($endPoint, 'POST'));
    }

    /**
     * @param $userId
     *
     * @return \Bi\Connect\Interfaces\ConnectResponseInterface
     */
    public function getAccounts($userId)
    {
        $response = $this->doRequest('/'.$userId.'/adaccounts', 'GET');

        return $this->facebookConnect->response($response['data']);
    }

    /**
     * @param $accountId
     *
     * @return \Bi\Connect\Interfaces\ConnectResponseInterface
     */
    public function getCampaigns($accountId)
    {
        $response = $this->doRequest('/'.$accountId.'/campaigns', 'GET');

        return $this->facebookConnect->response($response['data']);
    }

    /**
     * @param $campaignId
     *
     * @return \Bi\Connect\Interfaces\ConnectResponseInterface
     */
    public function insights($campaignId)
    {
        $response = $this->doRequest('/'.$campaignId.'/insights', 'GET');

        return $this->facebookConnect->response($response['data']);
    }
}
