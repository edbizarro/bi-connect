<?php

namespace Bi\Connect\Google;

use Bi\Connect\Google\Auth\GoogleAuth;
use Google_Client;

/**
 * Class GoogleConnect.
 */
class GoogleConnect extends GoogleOauth2
{
    /**
     * @var GoogleAnalyticsService
     */
    protected $googleAnalyticsService;

    /**
     * @var GoogleWebmastersService
     */
    protected $googleWebmastersService;


    /**
     * @var YoutubeService
     */
    protected $youtubeService;

    /**
     * GoogleConnect constructor.
     *
     * @param GoogleAuth $authDriver
     *
     * @throws \Google_Exception
     */
    public function __construct(GoogleAuth $authDriver)
    {
        if (($this->googleClient instanceof Google_Client) === false) {
            $this->googleClient = new Google_Client();
        }

        if (($this->googleAnalyticsService instanceof GoogleAnalyticsService) === false) {
            $this->googleAnalyticsService = new GoogleAnalyticsService($this->googleClient);
        }

        if (($this->googleWebmastersService instanceof GoogleWebmastersService) === false) {
            $this->googleWebmastersService = new GoogleWebmastersService($this->googleClient);
        }

        if (($this->youtubeService instanceof YoutubeService) === false) {
            $this->youtubeService = new YoutubeService($this->googleClient);
        }

        $this->googleClient->setAuthConfig($authDriver->getCredentials());
    }

    /**
     * @return GoogleAnalyticsService
     */
    public function analytics(): GoogleAnalyticsService
    {
        return $this->googleAnalyticsService;
    }

    /**
     * @return GoogleWebmastersService
     */
    public function webmaster(): GoogleWebmastersService
    {
        return $this->googleWebmastersService;
    }

    /**
     * @return YoutubeService
     */
    public function youtube(): YoutubeService
    {
        return $this->youtubeService;
    }
}
