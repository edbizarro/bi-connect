<?php

include dirname(__DIR__) . '/vendor/autoload.php';

use Bi\Connect\TwitterConnect;

$twitterClient = new TwitterConnect(
    'CONSUMER_KEY',
    'CONSUMER_SECRET'
);

$accessTOken = $twitterClient->auth();

$params = [
    'screen_name' => 'edbizarro',
    'count' => 1,
    'exclude_replies' => true,
];

$response = $twitterClient->get('statuses/user_timeline', $params)->getBody();

echo "<h1> Ed Bizarro tweet's</h1>";
dd($response->pull(0));

if (is_null($response)) {
    echo 'Error getting the report' . PHP_EOL;
}
