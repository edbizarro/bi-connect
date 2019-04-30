<?php

namespace Bi\Connect\Facebook;

use Bi\Connect\Base\Oauth2Connect;
use Bi\Connect\Exceptions\FacebookConnectException;
use Facebook\Authentication\AccessToken;
use Facebook\Exceptions\FacebookSDKException;
use Facebook\Facebook;
use FacebookAds\Api;
use InvalidArgumentException;

/**
 * Class FacebookConnect.
 */
class FacebookConnect extends Oauth2Connect
{
    /**
     * @var FacebookAdsService
     */
    protected $business;

    /**
     * @var Facebook
     */
    protected $facebookClient;

    /**
     * @var string
     */
    protected $apiVersion = 'v3.2';

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
            'default_graph_version' => $this->apiVersion,
            'enable_beta_mode' => false,
        ], $config);

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $this->facebookClient = new Facebook($config);
        $this->business       = new FacebookAdsService($this);
    }

    /**
     * @return FacebookAdsService
     */
    public function adsService(): FacebookAdsService
    {
        Api::init(
            $this->facebookClient->getApp()->getId(),
            $this->facebookClient->getApp()->getSecret(),
            $this->facebookClient->getDefaultAccessToken()->getValue()
        );

        return $this->business;
    }

    /**
     * Returns a long-lived access token.
     *
     * @param string $code
     *
     * @return AccessToken
     * @throws FacebookConnectException
     */
    public function getAccess($code): AccessToken
    {
        try {
            $accessToken = $this->facebookClient
                ->getRedirectLoginHelper()
                ->getAccessToken($this->getRedirectUrl());

            try {
                if (! $accessToken->isLongLived()) {
                    $accessToken = $this->facebookClient->getOAuth2Client()->getLongLivedAccessToken($accessToken);
                    $this->facebookClient->setDefaultAccessToken($accessToken);
                }
            } catch (FacebookSDKException $e) {
                throw new FacebookConnectException('Error getting long-lived access token:' . $e->getMessage());
            }
        } catch (FacebookSDKException $e) {
            throw new FacebookConnectException('Facebook SDK returned an error: ' . $e->getMessage());
        }

        return $accessToken;
    }

    /**
     * Set the access token.
     *
     * @param string $token
     *
     * @throws InvalidArgumentException
     */
    public function setAccessToken($token)
    {
        $this->facebookClient->setDefaultAccessToken($token);
    }

    /**
     * @param $state
     *
     * @return $this
     */
    public function setState($state): self
    {
        if (is_array($state)) {
            $state = json_encode($state);
        }

        $this->facebookClient
            ->getRedirectLoginHelper()
            ->getPersistentDataHandler()
            ->set('state', $state);

        return $this;
    }

    /**
     * @return mixed
     */
    public function getState()
    {
        return $this->facebookClient
            ->getRedirectLoginHelper()
            ->getPersistentDataHandler()
            ->get('state');
    }

    /**
     * Get login url.
     *
     * @param null $scope
     *
     * @return string
     * @throws FacebookConnectException
     */
    public function getLoginUrl($scope = null): string
    {
        if ($scope !== null) {
            $this->addScope($scope);
        }

        $this->validateScope();

        if ($this->getRedirectUrl() === null) {
            throw new FacebookConnectException(
                'You must provide a redirectUrl with setRedirectUrl() before calling this method.'
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

    /**
     * @throws FacebookConnectException
     */
    protected function validateScope(): void
    {
        if (is_array($this->scope) && count($this->scope) === 0) {
            throw new FacebookConnectException(
                'You must provide a scope to get a login URL.'
            );
        }

        if ($this->scope === null) {
            throw new FacebookConnectException(
                'You must provide a scope to get a login URL.'
            );
        }
    }
}
