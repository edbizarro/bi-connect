<?php

namespace Bi\Connect\Base;

/**
 * Class Oauth2Connect.
 */
abstract class Oauth2Connect extends BaseConnect
{
    const CODE = 'code';
    const TOKEN = 'token';
    const ONLINE = 'online';
    const OFFLINE = 'offline';
    const AUTO = 'auto';
    const FORCE = 'force';
    const TYPE = 'Content-Type';
    const REQUEST = 'application/x-www-form-urlencoded';

    const RESPONSE_TYPE = 'response_type';
    const CLIENT_ID = 'client_id';
    const REDIRECT_URL = 'redirect_uri';
    const ACCESS_TYPE = 'access_type';
    const APROVAL = 'approval_prompt';
    const CLIENT_SECRET = 'client_secret';
    const GRANT_TYPE = 'grant_type';
    const AUTHORIZATION = 'authorization_code';
    const REFRESH_TOKEN = 'refresh_token';

    protected $client = null;
    protected $secret = null;
    protected $scope = null;
    protected $display = null;
    protected $requestUrl = null;
    protected $accessUrl = null;
    protected $redirectUrl = null;

    protected $responseType = self::CODE;
    protected $approvalPrompt = self::AUTO;

    /**
     * Returns the access token.
     *
     * @param string $code
     *
     * @return array
     */
    abstract public function getAccess($code);

    /**
     * Set the access token.
     *
     * @param string $token
     *
     * @return mixed
     */
    abstract public function setAccessToken($token);

    /**
     * @param string $url
     *
     * @return string
     */
    public function setRedirectUrl($url)
    {
        $this->redirectUrl = $url;
    }

    /**
     * @return string
     */
    public function getRedirectUrl()
    {
        return $this->redirectUrl;
    }

    /**
     * abstract function for getting login url.
     *
     * @param null $scope
     * @param null $display
     *
     * @return string
     */
    abstract public function getLoginUrl($scope = null, $display = null);

    /**
     * Set scope.
     *
     * @param string|array
     */
    public function setScope($scope)
    {
        $this->scope = $scope;
    }

    /**
     * Add scope.
     *
     * @param string|array
     */
    public function addScope($scope)
    {
        $this->scope[] = $scope;
    }
}
