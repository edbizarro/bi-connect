<?php

namespace Edbizarro\BiConnect\Tests\FacebookAds;

use Bi\Connect\Base\Oauth2Connect;
use Bi\Connect\Exceptions\FBConnectException;
use Bi\Connect\Facebook\FacebookConnect;
use PHPUnit\Framework\TestCase;

class FacebookAdsTest extends TestCase
{
    /**
     * @var FacebookConnect
     */
    protected $fbConnect;

    public function setUp(): void
    {
        parent::setUp();

        $this->fbConnect = new FacebookConnect([
            'app_id' => getenv('FB_APP_ID'),
            'app_secret' => getenv('FB_APP_SECRET'),
        ]);
    }

    /** @test */
    public function it_can_instantiate(): void
    {
        $this->assertInstanceOf(Oauth2Connect::class, $this->fbConnect);
    }

    /** @test */
    public function it_cant_get_login_url_without_scope(): void
    {
        $this->fbConnect->getLoginUrl();
        $this->expectException(FBConnectException::class);
    }

    /** @test */
    public function it_cant_get_login_url_without_redirect(): void
    {
        $this->fbConnect->addScope('analytics');
        $this->fbConnect->getLoginUrl();
        $this->expectException(FBConnectException::class);
    }

    /** @test */
    public function it_can_generate_login_url(): void
    {
        $this->fbConnect->addScope('analytics');
        $this->fbConnect->setRedirectUrl('localhost');

        $loginUrl = $this->fbConnect->getLoginUrl();

        $this->assertStringContainsString(
            'https://www.facebook.com/v3.2/dialog/oauth',
            $loginUrl
        );

        $this->assertStringContainsString(
            'client_id',
            $loginUrl
        );

        $this->assertStringContainsString(
            'state',
            $loginUrl
        );

        $this->assertStringContainsString(
            'response_type',
            $loginUrl
        );

        $this->assertStringContainsString(
            'redirect_uri',
            $loginUrl
        );
    }
}
