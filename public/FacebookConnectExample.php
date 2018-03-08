<?php

include dirname(__DIR__).'/vendor/autoload.php';

use Bi\Connect\Facebook\FacebookConnect;

$fb = new FacebookConnect(
    [
        'app_id'     => 'APP_ID',
        'app_secret' => 'APP_SECRET',
    ]
);

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
    $user = $fb->instagramAds()->get('/me');
    var_dump($user->getBody()->all());

    echo '<h1> Accounts </h1>';
    $accounts = $fb->instagramAds()->getAccounts($user->getBody()->all()['id']);

    foreach ($accounts->getBody()->all() as $account) {
        echo '<h2>Campanha de account id '.$account['id'].'</h2>';
        $campaigns = $fb->instagramAds()->getCampaigns($account['id']);
        var_dump($campaigns->getBody()->all());
    }
}
