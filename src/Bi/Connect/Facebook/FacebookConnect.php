<?php

namespace Bi\Connect\Facebook;

use Facebook\Facebook;
use Bi\Connect\ConnectResponse;
use Bi\Connect\Base\Oauth2Connect;
use Bi\Connect\Exceptions\FacebookException;

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
     */
    public function __construct(array $config = [])
    {
        $config = array_merge([
            'app_id'                         => '',
            'app_secret'                     => '',
            'default_graph_version'          => Facebook::DEFAULT_GRAPH_VERSION,
            'enable_beta_mode'               => false,
            'http_client_handler'            => null,
            'persistent_data_handler'        => null,
            'pseudo_random_string_generator' => null,
            'url_detection_handler'          => null,
        ], $config);

        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        $this->facebookClient = new Facebook($config);
        $this->instagramAds = new InstagramAdsService($this);
    }

    /**
     * @return InstagramAdsService
     */
    public function instagramAds()
    {
        return $this->instagramAds;
    }

    /**
     * Returns the access token.
     *
     * @param string $code
     *
     * @throws FacebookException
     *
     * @return array
     */
    public function getAccess($code)
    {
        try {
            $accessToken = $this->facebookClient->getRedirectLoginHelper()->getAccessToken();

            try {
                $accessToken = $this->facebookClient->getOAuth2Client()->getLongLivedAccessToken($accessToken);
            } catch (\Facebook\Exceptions\FacebookSDKException $e) {
                throw new FacebookException('Error getting long-lived access token:'.$e->getMessage());
            }
        } catch (\Facebook\Exceptions\FacebookResponseException $e) {
            throw new FacebookException('Graph returned an error: '.$e->getMessage());
        } catch (\Facebook\Exceptions\FacebookSDKException $e) {
            throw new FacebookException('Facebook SDK returned an error: '.$e->getMessage());
        }

        return $accessToken;
    }

    /**
     * Set the access token.
     *
     * @param string $token
     *
     * @return string
     */
    public function setAccessToken($token)
    {
        return $this->facebookClient->setDefaultAccessToken($token);
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
    public function getLoginUrl($scope = null, $display = null)
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
    public function getClient()
    {
        return $this->facebookClient;
    }

    /**
     * @param $response
     *
     * @return ConnectResponse
     */
    protected function formatResponse($response)
    {
        $body = $response;

        if (is_array($body) == false) {
            $body = json_decode($response, true);
            if ((json_last_error() == JSON_ERROR_NONE) === false) {
                $body = $response;
            }
        }

        return new ConnectResponse(
            [],
            $body,
            $response
        );
    }
}
