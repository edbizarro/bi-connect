<?php

include dirname(__DIR__).'/vendor/autoload.php';

use Bi\Connect\TailTargetConnect;

$tail = new TailTargetConnect(
    'USERNAME',
    'PASSWORD',
    'APP_ID',
    'SECRET'
);

$params = [
    'trackingId' => 'TRACKING_ID',
];

$response = $tail->getMetrics(
    'audience/trackings/list',
    $params
);

echo '<h1> Response campaignsByAccount</h1>';
var_dump($response);

if (is_null($response)) {
    echo 'Error getting the report'.PHP_EOL;
}
