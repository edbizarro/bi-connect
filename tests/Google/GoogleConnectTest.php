<?php

namespace Edbizarro\BiConnect\Tests\Google;

use Bi\Connect\Base\Oauth2Connect;
use Bi\Connect\Exceptions\GoogleConnectException;
use Bi\Connect\Google\Auth\CredentialsFileAuth;
use Bi\Connect\Google\GoogleConnect;
use PHPUnit\Framework\TestCase;

class GoogleConnectTest extends TestCase
{
    /**
     * @var GoogleConnect
     */
    protected $googleConnect;

    public function setUp(): void
    {
        parent::setUp();

        $this->googleConnect = new GoogleConnect(
            new CredentialsFileAuth(
                dirname(__DIR__).'/'.getenv('GOOGLE_CREDENTIALS')
            )
        );
    }

    /** @test */
    public function it_can_instantiate(): void
    {
        $this->assertInstanceOf(Oauth2Connect::class, $this->googleConnect);
    }

    /** @test */
    public function it_cant_get_login_url_without_scope(): void
    {
        $this->expectException(GoogleConnectException::class);

        $this->googleConnect->getLoginUrl();
    }

    /** @test */
    public function it_cant_get_login_url_without_redirect(): void
    {
        $this->expectException(GoogleConnectException::class);

        $this->googleConnect->addScope('analytics');
        $this->googleConnect->getLoginUrl();
    }

    /** @test */
    public function it_can_generate_login_url(): void
    {
        $this->googleConnect->addScope('analytics');
        $this->googleConnect->setRedirectUrl('http://localhost/');

        $loginUrl = $this->googleConnect->getLoginUrl();

        $this->assertStringContainsString(
            'https://accounts.google.com/o/oauth2/auth',
            $loginUrl
        );
    }

    /** @test */
    public function it_can_generate_offline_login_url(): void
    {
        $this->googleConnect->addScope('analytics');
        $this->googleConnect->setRedirectUrl('http://localhost/');
        $this->googleConnect->forOffline();

        $loginUrl = $this->googleConnect->getLoginUrl();

        $this->assertStringContainsString(
            'https://accounts.google.com/o/oauth2/auth',
            $loginUrl
        );

        $this->assertStringContainsString(
            'access_type=offline',
            $loginUrl
        );
    }
}
