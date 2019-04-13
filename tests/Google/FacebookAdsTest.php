<?php

namespace Edbizarro\BiConnect\Tests\Google;

use Bi\Connect\Base\Oauth2Connect;
use Bi\Connect\Facebook\FacebookConnect;
use PHPUnit\Framework\TestCase;

class FacebookAdsTest extends TestCase
{
    /** @test */
    public function it_can_instantiate(): void
    {
        $fbConnect = new FacebookConnect([
            'app_id' => getenv('FB_APP_ID'),
            'app_secret' => getenv('FB_APP_SECRET'),
        ]);

        $this->assertInstanceOf(Oauth2Connect::class, $fbConnect);
    }
}
