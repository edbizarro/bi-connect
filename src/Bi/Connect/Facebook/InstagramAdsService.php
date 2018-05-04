<?php

namespace Bi\Connect\Facebook;

use Facebook\Facebook;
use FacebookAds\Object\AdAccount;
use FacebookAds\Object\AdAccountUser;
use FacebookAds\Object\Campaign;
use FacebookAds\Object\Fields\AdAccountFields;
use FacebookAds\Object\Fields\CampaignFields;

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
     * @param string $accountUserId
     *
     * @return AdAccountUser
     */
    public function me($accountUserId = 'me')
    {
        return (new AdAccountUser())->setId($accountUserId);
    }

    /**
     * @param string $accountUserId
     *
     * @return array
     */
    public function getAccounts($accountUserId = 'me', $fields = [AdAccountFields::NAME])
    {
        return $this->me($accountUserId)->getAdAccounts(
            $fields
        )->getArrayCopy();
    }


    /**
     * @param $accountId
     * @param array $fields
     *
     * @return array
     */
    public function getCampaigns($accountId, $fields = [CampaignFields::NAME])
    {
        return (new Campaign($accountId))->read($fields)->getData();
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
