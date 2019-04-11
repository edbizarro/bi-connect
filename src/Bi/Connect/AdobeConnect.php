<?php

namespace Bi\Connect;

use Carbon\Carbon;
use Bi\Connect\Base\BaseConnect;
use AdobeMarketingCloud\Api\SuiteApi;
use Tightenco\Collect\Support\Collection;
use AdobeMarketingCloud\Client as AdobeClient;
use Bi\Connect\Exceptions\AdobeConnectException;
use AdobeMarketingCloud\HttpClient\Curl as AdobeCurl;

/**
 * Class AdobeConnect.
 */
class AdobeConnect extends BaseConnect
{
    private $apiUsername;
    private $apiPassword;
    private $apiReportSuiteId;

    /**
     * @var \AdobeMarketingCloud\Api\SuiteApi
     */
    protected $apiClient;

    /**
     * AdobeConnect constructor.
     *
     * @param $username
     * @param $password
     * @param $reportSuiteId
     */
    public function __construct($username, $password, $reportSuiteId)
    {
        $this->apiUsername = $username;
        $this->apiPassword = $password;
        $this->apiReportSuiteId = $reportSuiteId;
    }

    /**
     * @return \AdobeMarketingCloud\Api\SuiteApi
     */
    public function auth()
    {
        $adobeClient = new AdobeClient(new AdobeCurl([
            'debug' => false,
        ]));

        $adobeClient->authenticate($this->apiUsername, $this->apiPassword);
        $adobeClient->getHttpClient()->setOption('api_version', '1.4');
        $adobeClient->setEndpoint('https://api5.omniture.com/admin/1.4/rest/');

        return $this->apiClient = $adobeClient->getSuiteApi();
    }

    /**
     * Put report in queue and get de ID for later use
     * for now $granularity accepts only 'day'.
     *
     * @param Carbon $dateFrom
     * @param Carbon $dateTo
     * @param array  $metrics
     * @param array  $elements
     * @param array  $segments
     * @param string $granularity
     *
     * @throws AdobeConnectException
     *
     * @return array|null
     */
    public function createReport(
        Carbon $dateFrom,
        Carbon $dateTo,
        array $metrics = [],
        array $elements = [],
        array $segments = [],
        $granularity = 'year'
    ) {
        if (($this->apiClient instanceof SuiteApi) == false) {
            throw new AdobeConnectException('You need to authenticate first with auth()');
        }

        $defaultOptions = [
            'reportDescription' => [
                'reportSuiteID' => $this->apiReportSuiteId,
                'dateFrom' => $dateFrom->format('Y-m-d'),
                'dateTo' => $dateTo->format('Y-m-d'),
                'dateGranularity' => $granularity,
                'metrics' => $this->formatApiMetricsOptions($metrics),
                'elements' => $this->formatApiElementOptions($elements),
                'segments' => $this->formatApiSegmentsOptions($segments),
            ],
        ];

        $response = $this->apiClient->post('Report.Queue', $defaultOptions);

        if (is_bool($response) === false && array_key_exists('error', $response) === true) {
            return $response;
        }

        return isset($response['reportID']) ? $response['reportID'] : null;
    }

    /**
     * @param $array
     *
     * @return array
     */
    protected function convertSimpleResponseToObject(array $array)
    {
        $arr = [];

        foreach ($array as $item) {
            $obj = new \stdClass();
            foreach ($item as $k => $v) {
                $obj->{$k} = $v;
            }
            $arr[] = $obj;
        }

        return $arr;
    }

    /**
     * Format the options to API defaults.
     *
     * @param array $option
     *
     * @return array
     */
    protected function formatApiMetricsOptions(array $option = [])
    {
        $formattedValue = [];

        foreach ($option as $k => $item) {
            $formattedValue[$k]['id'] = $item;
        }

        return $formattedValue;
    }

    /**
     * @param array $option
     *
     * @return array
     */
    protected function formatApiSegmentsOptions(array $option = [])
    {
        $formattedValue = [];
        $iterator = 0;
        foreach ($option as $k => $item) {
            if (\is_array($item)) {
                $formattedValue[$iterator]['id'] = $k;
                $formattedValue[$iterator] = array_merge($formattedValue[$iterator], $item);
                $iterator++;
                continue;
            }

            $formattedValue[$iterator]['id'] = $item;
            $iterator++;
        }

        return $formattedValue;
    }

    /**
     * Format the options to API defaults.
     *
     * @param array $option
     *
     * @return array
     */
    protected function formatApiElementOptions($option = [])
    {
        $formattedValue = [];
        $iterator = 0;
        foreach ($option as $k => $item) {
            if (\is_array($item)) {
                $formattedValue[$iterator]['id'] = $k;
                $formattedValue[$iterator] = array_merge($formattedValue[$iterator], $item);
                $iterator++;
                continue;
            }

            $formattedValue[$iterator]['id'] = $item;
            $iterator++;
        }

        return $formattedValue;
    }

    /**
     * @param $response
     *
     * @throws AdobeConnectException
     *
     * @return ConnectResponse
     */
    protected function formatResponse($response)
    {
        if (isset($response['error'])) {
            return new ConnectResponse(
                [],
                ['error' => $response['error']],
                $response
            );
        }

        $header = $this->formatResponseHeader($response['report']);
        $body = $this->formatResponseBody($response['report']['data'], $header, $response);

        return new ConnectResponse(
            $header,
            $body,
            $response
        );
    }

    /**
     * @param $response
     *
     * @return array
     */
    protected function formatResponseHeader($response)
    {
        $header = [];
        $metrics = $response['metrics'];

        if (isset($response['segments'])) {
            $metrics = $response['segments'];
        }

        $elements = $response['elements'];

        $header['elements'] = $elements;
        $header['metrics'] = $metrics;

        return $header;
    }

    /**
     * @param $response
     * @param $header
     *
     * @throws AdobeConnectException
     *
     * @return array
     */
    protected function formatResponseBody($response, $header, $fullResponse)
    {
        $body = [];
        $bodyKey = 0;

        foreach ($response as $responseKey => $responseRow) {
            $body[$bodyKey] = '';

            try {
                if (strpos($responseRow['name'], '-') === false) {
                    $reportStartDate = new Carbon($responseRow['name']);
                    $reportEndDate = new Carbon($responseRow['name']);

                    /*
                     * If MONTH granularity
                     */
                    if (strpos($responseRow['name'], ' ') === false) {
                        $reportStartDate->startOfYear();
                        $reportEndDate->endOfYear();
                    }

                    /*
                     * If YEAR granularity
                     */
                    if (strpos($responseRow['name'], ' ') !== false) {
                        $reportStartDate->startOfMonth();
                        $reportEndDate->endOfMonth();
                    }

                    /*
                     * If DAY granularity
                     */
                    if (count(explode(' ', $responseRow['name'])) > 2) {
                        $reportStartDate = new Carbon($responseRow['name']);
                        $reportEndDate = new Carbon($responseRow['name']);
                    }
                }

                if (strpos($responseRow['name'], '-') !== false) {
                    list($reportStartDate, $reportEndDate) = explode(' - ', $responseRow['name']);
                    $reportStartDate = new Carbon($reportStartDate);
                    $reportEndDate = new Carbon($reportEndDate);
                }

                $reportStartDate->startOfDay();
                $reportEndDate->startOfDay();

                $body[$bodyKey]['startDate'] = $reportStartDate;
                $body[$bodyKey]['endDate'] = $reportEndDate;
            } catch (\Exception $e) {
                throw new AdobeConnectException('Error parsing returned date interval from Adobe');
            }

            if (isset($responseRow['breakdown'])) {
                $body[$bodyKey]['type'] = 'breakdown';
                $metrics = $this->formatResponseBodyBreakdown(
                    $responseRow['breakdown'],
                    $header
                );

                $body[$bodyKey] = array_merge(
                    $body[$bodyKey],
                    $metrics
                );
            }

            if (! isset($responseRow['breakdown'])) {
                $body[$bodyKey]['type'] = 'simple';
                $metrics = $this->formatResponseBodyWithoutBreakdown(
                    $responseRow,
                    $header['metrics']
                );
                $body[$bodyKey] = array_merge(
                    $body[$bodyKey],
                    $metrics
                );
            }

            $bodyKey++;
        }

        return $body;
    }

    /**
     * @param $bodyBreakdown
     * @param $header
     * @param int $level
     *
     * @return array
     */
    protected function formatResponseBodyBreakdown($bodyBreakdown, $header, $level = 0)
    {
        $body = [];
        $elements = (new Collection($header['elements']))->pluck('id')->all();

        foreach ($bodyBreakdown as $itemKey => $item) {
            if (isset($item['breakdown'])) {
                $body[$elements[$level]][$itemKey] = $this->formatResponseBodyBreakdown(
                    $item['breakdown'],
                    $header,
                    $level + 1
                );
                $body[$elements[$level]][$itemKey]['name'] = $item['name'];

                foreach ($header['metrics'] as $headerKey1 => $headerItem1) {
                    $body[$elements[$level]][$itemKey][$headerItem1['id']] = $item['counts'][$headerKey1];
                }
            }

            if (! isset($item['breakdown'])) {
                $body[$elements[$level]][$itemKey]['name'] = $item['name'];

                foreach ($header['metrics'] as $headerKey2 => $headerItem2) {
                    $body[$elements[$level]][$itemKey][$headerItem2['id']] = $item['counts'][$headerKey2];
                }
            }
        }

        return $body;
    }

    /**
     * @param array $responseRow
     * @param array $responseHeader
     *
     * @return array
     */
    protected function formatResponseBodyWithoutBreakdown(array $responseRow, array $responseHeader)
    {
        $formattedResponseRow = [];

        foreach ($responseHeader as $headerKey => $responseHeader) {
            $formattedResponseRow[$responseHeader['id']] = $responseRow['counts'][$headerKey];
        }

        return $formattedResponseRow;
    }

    /**
     * Return all metrics.
     *
     * @return array
     */
    public function getAllMetrics()
    {
        return $this->convertSimpleResponseToObject(
            $this->apiClient->post(
                'Report.GetMetrics',
                [
                    'reportSuiteID' => $this->apiReportSuiteId,
                ]
            )
        );
    }

    /**
     * Return all elements.
     *
     * @return array
     */
    public function getAllElements()
    {
        return $this->convertSimpleResponseToObject(
            $this->apiClient->post(
                'Report.GetElements',
                [
                    'reportSuiteID' => $this->apiReportSuiteId,
                ]
            )
        );
    }

    /**
     * Get the report from Adobe Qqueue.
     *
     * @param int $reportId
     *
     * @throws AdobeConnectException
     *
     * @return ConnectResponse
     */
    public function getQueuedReport($reportId)
    {
        if (is_int($reportId) === false) {
            throw new AdobeConnectException('Report ID must be integer');
        }

        return $this->response(
            $this->apiClient->post(
                'Report.Get',
                [
                    'reportID' => $reportId,
                ]
            )
        );
    }

    /**
     * @param $username
     * @param $password
     * @param $reportSuiteId
     */
    public function setCredentials($username, $password, $reportSuiteId)
    {
        $this->apiUsername = $username;
        $this->apiPassword = $password;
        $this->apiReportSuiteId = $reportSuiteId;
    }
}
