<?php

namespace Bi\Connect\Google;

use Bi\Connect\Base\Oauth2Connect;

/**
 * Class GoogleOauth2.
 */
class GoogleOauth2 extends Oauth2Connect
{
    const SCOPE_ANALYTICS = 'https://www.googleapis.com/auth/analytics.readonly';
    const SCOPE_BASE = 'https://www.google.com/base/feeds/';
    const SCOPE_BUZZ = 'https://www.googleapis.com/auth/buzz';
    const SCOPE_BOOK = 'https://www.google.com/books/feeds/';
    const SCOPE_BLOGGER = 'https://www.blogger.com/feeds/';
    const SCOPE_CALENDAR = 'https://www.google.com/calendar/feeds/';
    const SCOPE_CONTACTS = 'https://www.google.com/m8/feeds/';
    const SCOPE_CHROME = 'https://www.googleapis.com/auth/chromewebstore.readonly';
    const SCOPE_DOCUMENTS = 'https://docs.google.com/feeds/';
    const SCOPE_DRIVE = 'https://www.googleapis.com/auth/drive';
    const SCOPE_FINANCE = 'https://finance.google.com/finance/feeds/';
    const SCOPE_GMAIL = 'https://mail.google.com/';
    const SCOPE_HEALTH = 'https://www.google.com/health/feeds/';
    const SCOPE_H9 = 'https://www.google.com/h9/feeds/';
    const SCOPE_MAPS = 'https://maps.google.com/maps/feeds/';
    const SCOPE_MODERATOR = 'https://www.googleapis.com/auth/moderator';
    const SCOPE_OPENSOCIAL = 'https://www-opensocial.googleusercontent.com/api/people/';
    const SCOPE_ORKUT = 'https://www.googleapis.com/auth/orkut';
    const SCOPE_PLUS = 'https://www.googleapis.com/auth/plus.me';
    const SCOPE_PICASA = 'https://picasaweb.google.com/data/';
    const SCOPE_SIDEWIKI = 'https://www.google.com/sidewiki/feeds/';
    const SCOPE_SITES = 'https://sites.google.com/feeds/';
    const SCOPE_SREADSHEETS = 'https://spreadsheets.google.com/feeds/';
    const SCOPE_TASKS = 'https://www.googleapis.com/auth/tasks';
    const SCOPE_SHORTENER = 'https://www.googleapis.com/auth/urlshortener';
    const SCOPE_WAVE = 'http://wave.googleusercontent.com/api/rpc';
    const SCOPE_WEBMASTER = 'https://www.googleapis.com/auth/webmasters';
    const SCOPE_YOUTUBE = 'https://gdata.youtube.com';

    protected $googleScopes = [
        'analytics'    => self::SCOPE_ANALYTICS,
        'base'         => self::SCOPE_BASE,
        'buzz'         => self::SCOPE_BUZZ,
        'book'         => self::SCOPE_BOOK,
        'blogger'      => self::SCOPE_BLOGGER,
        'calendar'     => self::SCOPE_CALENDAR,
        'contacts'     => self::SCOPE_CONTACTS,
        'chrome'       => self::SCOPE_CHROME,
        'documents'    => self::SCOPE_DOCUMENTS,
        'drive'        => self::SCOPE_DRIVE,
        'finance'      => self::SCOPE_FINANCE,
        'gmail'        => self::SCOPE_GMAIL,
        'health'       => self::SCOPE_HEALTH,
        'h9'           => self::SCOPE_H9,
        'maps'         => self::SCOPE_MAPS,
        'moderator'    => self::SCOPE_MODERATOR,
        'opensocial'   => self::SCOPE_OPENSOCIAL,
        'orkut'        => self::SCOPE_ORKUT,
        'plus'         => self::SCOPE_PLUS,
        'picasa'       => self::SCOPE_PICASA,
        'sidewiki'     => self::SCOPE_SIDEWIKI,
        'sites'        => self::SCOPE_SITES,
        'spreadsheets' => self::SCOPE_SREADSHEETS,
        'tasks'        => self::SCOPE_TASKS,
        'shortener'    => self::SCOPE_SHORTENER,
        'wave'         => self::SCOPE_WAVE,
        'webmaster'    => self::SCOPE_WEBMASTER,
        'youtube'      => self::SCOPE_YOUTUBE,
    ];

    /**
     * @var \Google_Client
     */
    protected $googleClient;

    /**
     * Returns the access token.
     *
     * @param string $code
     *
     * @return array
     */
    public function getAccess($code)
    {
        $token = $this->googleClient->fetchAccessTokenWithAuthCode($code);
        $this->googleClient->setAccessToken($token);

        return $token;
    }

    /**
     * @param string $url
     *
     * @return string
     */
    public function setRedirectUrl($url)
    {
        $this->googleClient->setRedirectUri($url);
    }

    /**
     * function for getting login url.
     *
     * @param null $scope
     * @param null $display
     *
     * @return string
     */
    public function getLoginUrl($scope = null, $display = null)
    {
        $this->addScopesToClient();

        return $this->googleClient->createAuthUrl();
    }

    protected function addScopesToClient()
    {
        if (is_string($this->scope) && isset($this->googleScopes[$this->scope])) {
            $this->googleClient->addScope($this->googleScopes[$this->scope]);

            return true;
        }

        if (\is_array($this->scope)) {
            foreach ($this->scope as $key) {
                if (is_string($key) && isset($this->googleScopes[$key])) {
                    $this->googleClient->addScope($this->googleScopes[$key]);
                }
            }
        }

        return true;
    }

    /**
     * Set auth for offline access.
     */
    public function forOffline()
    {
        $this->googleClient->setAccessType(self::OFFLINE);
    }

    /**
     * Set auth for online access.
     */
    public function forOnline()
    {
        $this->googleClient->setAccessType(self::ONLINE);
    }

    /**
     * Set auth for force approve.
     */
    public function forceApprove()
    {
        $this->googleClient->setApprovalPrompt(self::FORCE);
    }

    /**
     * @param string|array $token
     *
     * @return mixed
     */
    public function setAccessToken($token)
    {
        return $this->googleClient->setAccessToken($token);
    }
}
