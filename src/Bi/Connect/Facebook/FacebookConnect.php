<?php

namespace Bi\Connect\Facebook;

use Facebook\Authentication\AccessToken;
use Facebook\Facebook;
use Bi\Connect\ConnectResponse;
use Bi\Connect\Base\Oauth2Connect;
use Bi\Connect\Exceptions\FacebookException;
use Facebook\Exceptions\FacebookSDKException;
use FacebookAds\Api;

/**
 * Class FacebookConnect.
 */
class FacebookConnect extends Oauth2Connect
{
    /**
     * @var InstagramAdsService
     */
    protected $instagramAds;

    /**
     * @var Facebook
     */
    protected $facebookClient;

    /**
     * FacebookConnect constructor.
     *
     * @param array $config
     *
     * @throws FacebookSDKException
     */
    public function __construct(array $config = [])
    {
        $config = array_merge([
            'default_graph_version'          => 'v3.0',
            'enable_beta_mode'               => false,
        ], $config);

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $this->facebookClient = new Facebook($config);
        $this->instagramAds = new InstagramAdsService($this);
    }

    /**
     * @return InstagramAdsService
     */
    public function instagramAds(): InstagramAdsService
    {
        Api::init(
            $this->facebookClient->getApp()->getId(),
            $this->facebookClient->getApp()->getSecret(),
            $this->facebookClient->getDefaultAccessToken()->getValue() ?? null
        );

        return $this->instagramAds;
    }

    /**
     * Returns the access token.
     *
     * @param string $code
     *
     * @throws FacebookException
     *
     * @return AccessToken
     */
    public function getAccess($code): AccessToken
    {
        try {
            $accessToken = $this->facebookClient
                ->getRedirectLoginHelper()
                ->getAccessToken($this->getRedirectUrl());

            try {
                $accessToken = $this->facebookClient->getOAuth2Client()->getLongLivedAccessToken($accessToken);
                $this->facebookClient->setDefaultAccessToken($accessToken);
            } catch (FacebookSDKException $e) {
                throw new FacebookException('Error getting long-lived access token:'.$e->getMessage());
            }
        } catch (FacebookSDKException $e) {
            throw new FacebookException('Facebook SDK returned an error: '.$e->getMessage());
        }

        return $accessToken;
    }

    /**
     * Set the access token.
     *
     * @param string $token
     *
     * @throws \InvalidArgumentException
     */
    public function setAccessToken($token)
    {
        $this->facebookClient->setDefaultAccessToken($token);
    }

    /**
     * function for getting login url.
     *
     * @param null $scope
     * @param null $display
     *
     * @throws FacebookException
     *
     * @return string
     */
    public function getLoginUrl($scope = null, $display = null): string
    {
        if ($scope != null) {
            $this->addScope($scope);
        }

        if ($this->getRedirectUrl() === null) {
            throw new FacebookException(
                'You must provide a redirectUrl with setRedirectUrl() before calling this method'
            );
        }

        return $this->facebookClient->getRedirectLoginHelper()->getLoginUrl(
            $this->getRedirectUrl(),
            $this->scope
        );
    }

    /**
     * @return Facebook
     */
    public function getClient(): Facebook
    {
        return $this->facebookClient;
    }
}
