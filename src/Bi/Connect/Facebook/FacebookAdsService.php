<?php

namespace Bi\Connect\Facebook;

use Facebook\Facebook;
use FacebookAds\Object\AdAccount;
use FacebookAds\Object\AdAccountUser;
use FacebookAds\Object\Fields\AdAccountFields;
use FacebookAds\Object\Fields\CampaignFields;

class FacebookAdsService
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
        $this->facebookClient  = $facebookConnect->getClient();
        $this->facebookConnect = $facebookConnect;
    }

    /**
     * @param string $accountUserId
     *
     * @return AdAccountUser
     */
    public function me($accountUserId = 'me'): AdAccountUser
    {
        return (new AdAccountUser())->setId($accountUserId);
    }

    /**
     * @param string $accountUserId
     *
     * @param array $fields
     *
     * @return array
     */
    public function accounts($accountUserId = 'me', array $fields = [AdAccountFields::NAME]): array
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
        return (new AdAccount)->setId($accountId)->getCampaigns($fields);
    }

    public function insights($campaignId)
    {
    }
}
