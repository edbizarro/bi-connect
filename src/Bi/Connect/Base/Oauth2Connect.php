<?php

namespace Bi\Connect\Base;

/**
 * Class Oauth2Connect.
 */
abstract class Oauth2Connect extends BaseConnect
{
    public const CODE    = 'code';
    public const TOKEN   = 'token';
    public const ONLINE  = 'online';
    public const OFFLINE = 'offline';
    public const AUTO    = 'auto';
    public const FORCE   = 'force';
    public const TYPE    = 'Content-Type';
    public const REQUEST = 'application/x-www-form-urlencoded';

    public const RESPONSE_TYPE = 'response_type';
    public const CLIENT_ID     = 'client_id';
    public const REDIRECT_URL  = 'redirect_uri';
    public const ACCESS_TYPE   = 'access_type';
    public const APROVAL       = 'approval_prompt';
    public const CLIENT_SECRET = 'client_secret';
    public const GRANT_TYPE    = 'grant_type';
    public const AUTHORIZATION = 'authorization_code';
    public const REFRESH_TOKEN = 'refresh_token';

    protected $client;
    protected $secret;
    protected $scope;
    protected $display;
    protected $requestUrl;
    protected $accessUrl;
    protected $redirectUrl;

    protected $responseType   = self::CODE;
    protected $approvalPrompt = self::AUTO;

    /**
     * Returns the access token.
     *
     * @param string $code
     *
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
     */
    public function setRedirectUrl($url)
    {
        $this->redirectUrl = $url;
    }

    /**
     * @return string
     */
    public function getRedirectUrl(): string
    {
        return $this->redirectUrl;
    }

    /**
     * abstract function for getting login url.
     *
     * @param null $scope
     *
     * @return string
     */
    abstract public function getLoginUrl($scope = null): string;

    /**
     * Set scope.
     *
     * @param string|array
     * @return Oauth2Connect
     */
    public function setScope($scope): self
    {
        $this->scope = $scope;
    }

    /**
     * Add scope.
     *
     * @param string|array
     * @return Oauth2Connect
     */
    public function addScope($scope): self
    {
        $this->scope[] = $scope;
    }
}
