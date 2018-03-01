<?php

include dirname(__DIR__).'/vendor/autoload.php';

use Bi\Connect\AdobeConnect;
use Carbon\Carbon;

$adobe = new AdobeConnect(
    'username',
    'password',
    'reportSuiteId'
);

$adobe->auth();

try {
    $reportId = $adobe->createReport(
        (new Carbon('2018-01-01'))->startOfDay(),
        (new Carbon('2018-01-02'))->endOfDay(),
        ['visits']
    );
} catch (\Bi\Connect\Exceptions\AdobeConnectException $e) {
}

echo "<h1> Report ID </h1>";
var_dump($reportId);

if (!is_null($reportId)) {
    echo "<h1> Report </h1>";
    do {
        $report = $adobe->getQueuedReport($reportId);

        if (!isset($report->getRawResponse()['error'])) {
            print_r($report->getBody()->all());
        }
        sleep(2);
    } while (isset($report->getRawResponse()['error']) && $report->getRawResponse()['error'] == 'report_not_ready');
}

if (is_null($reportId)) {
    echo 'Error getting the report'.PHP_EOL;
}


echo "<h1> Metrics</h1>";

$metrics = $adobe->getAllMetrics();

var_dump($metrics);
