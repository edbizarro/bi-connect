<?php

ini_set('error_reporting', 1);
ini_set('display_errors', 1);

include dirname(__DIR__).'/vendor/autoload.php';

use Bi\Connect\Facebook\FacebookConnect;

try {
    $fb = new FacebookConnect(
        [
            'app_id' => 'APP_ID',
            'app_secret' => 'APP_SECRET',
        ]
    );
} catch (\Facebook\Exceptions\FacebookSDKException $e) {
    var_dump($e);
}

if (! isset($_GET['code'])) {
    $fb->setRedirectUrl('REDIRECT_URL');
    $redirectUrl = $fb->getLoginUrl('ads_management');

    echo '<a href="'.$redirectUrl.'"> Login with Facebook </a>';
}

if (isset($_GET['code'])) {
    $accessToken = $fb->getAccess($_GET['code']);
    $_SESSION['access_token'] = $accessToken;

    header('Location: FacebookConnectExample.php');
}

if ($_SESSION['access_token'] != '') {
    echo '<h1> Access Token </h1>';
    var_dump($_SESSION['access_token']);
    $fb->setAccessToken($_SESSION['access_token']);

    echo '<h1> User logged in </h1>';
    $user = $fb->adsService()->me();
    print_r($user->exportAllData());

    echo '<h1> Accounts </h1>';
    $accounts = $fb->adsService()->accounts();
    print_r($accounts);
    foreach ($accounts->getBody()->all() as $account) {
        echo '<h2>Campanha de account id '.$account['id'].'</h2>';
        $campaigns = $fb->instagramAds()->getCampaigns($account['id']);
        var_dump($campaigns->getBody()->all());
    }
}
