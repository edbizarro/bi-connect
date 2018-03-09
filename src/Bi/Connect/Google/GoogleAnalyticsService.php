<?php

namespace Bi\Connect\Google;

use Carbon\Carbon;
use Google_Collection;
use Google_Service_Analytics;
use Bi\Connect\ConnectResponse;
use Google_Service_Analytics_GaData;
use function GuzzleHttp\Promise\all;

/**
 * Class GoogleAnalyticsService.
 */
class GoogleAnalyticsService extends Google_Service_Analytics
{
    /**
     * @return ConnectResponse
     */
    public function getAccounts(): ConnectResponse
    {
        return $this->formatSimpleResponse(
            $this->management_accounts->listManagementAccounts()->getItems()
        );
    }

    /**
     * @param string|int $accountId
     *
     * @return ConnectResponse
     */
    public function getProperties($accountId = '~all'): ConnectResponse
    {
        return $this->formatSimpleResponse(
            $this->management_webproperties->listManagementWebproperties($accountId)->getItems()
        );
    }

    /**
     * @param $accountId
     * @param $propertyId
     *
     * @return ConnectResponse
     */
    public function getProfiles($accountId = '~all', $propertyId = '~all'): ConnectResponse
    {
        return $this->formatSimpleResponse(
            $this->management_profiles->listManagementProfiles($accountId, $propertyId)->getItems()
        );
    }

    /**
     * @param string $accountId
     * @param string $propertyId
     * @param string $profileId
     *
     * @return ConnectResponse
     */
    public function getGoals($accountId = '~all', $propertyId = '~all', $profileId = '~all'): ConnectResponse
    {
        return $this->formatSimpleResponse(
            $this->management_goals->listManagementGoals($accountId, $propertyId, $profileId)->getItems()
        );
    }

    /**
     * @param $gaId
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @param $metrics
     * @param array $optOptions
     *
     * @return ConnectResponse
     */
    public function query(
        $gaId,
        Carbon $startDate,
        Carbon $endDate,
        $metrics,
        $optOptions = []
    ): ConnectResponse {
        $response = $this->data_ga->get(
            $this->formatQueryParams($gaId),
            $startDate->format('Y-m-d'),
            $endDate->format('Y-m-d'),
            $this->formatQueryParams($metrics),
            $this->formatOptParams($optOptions)
        );

        return $this->formatQueryResponse($response);
    }

    /**
     * @param $originalResponse
     *
     * @return ConnectResponse
     */
    protected function formatSimpleResponse($originalResponse): ConnectResponse
    {
        return new ConnectResponse(
            [],
            collect($originalResponse)->transform(function ($item, $key) {
                $item[$key]['id'] = $item->id;
                $item[$key]['name'] = $item->name;
                return $item;
            })->all(),
            $originalResponse
        );
    }

    /**
     * Transform the query response.
     *
     * @param $queryResponse
     *
     * @return ConnectResponse
     */
    protected function formatQueryResponse(Google_Service_Analytics_GaData $queryResponse): ConnectResponse
    {
        $responseHeaders = $this->extractHeaders($queryResponse);

        $result = collect($queryResponse->rows)->transform(function ($item) use ($responseHeaders) {
            return array_combine($responseHeaders, $item);
        });

        return new ConnectResponse(
            $responseHeaders,
            $result->all(),
            $queryResponse
        );
    }

    /**
     * @param mixed $params
     *
     * @return string
     */
    protected function formatQueryParams($params = null): string
    {
        if (\is_array($params) === false) {
            return 'ga:'.$params;
        }

        $formattedParams = [];

        foreach ($params as $k => $param) {
            if (strpos($param, 'ga:') !== false) {
                $formattedParams[$k] = $param;
                continue;
            }
            $formattedParams[$k] = 'ga:'.$param;
        }

        return implode(',', $formattedParams);
    }

    /**
     * @param mixed $params
     *
     * @return mixed
     */
    protected function formatOptParams($params = null)
    {
        if (isset($params['dimensions'])) {
            if (\is_array($params['dimensions']) === false
                && \strpos($params['dimensions'], 'ga:') === false
            ) {
                $params['dimensions'] = 'ga:'.$params['dimensions'];
            }

            if (\is_array($params['dimensions'])) {
                foreach ($params['dimensions'] as $k => $param) {
                    if (strpos($param, 'ga:') !== false) {
                        $params['dimensions'][$k] = $param;
                        continue;
                    }
                    $params['dimensions'][$k] = 'ga:'.$param;
                }
                $params['dimensions'] = implode(',', $params['dimensions']);
            }
        }

        return $params;
    }

    /**
     * @param array $headers
     *
     * @return array
     */
    protected function extractHeaders($headers = []): array
    {
        $originalHeaders = $headers['columnHeaders'];
        $formattedHeaders = [];
        foreach ($originalHeaders as $item) {
            $formattedHeaders[] = str_replace('ga:', '', $item['name']);
        }

        return $formattedHeaders;
    }

    /**
     * @param $returnedDate
     *
     * @return string
     */
    public function formatQueryReturnDate($returnedDate): string
    {
        return substr($returnedDate, 0, 4).'-'.substr($returnedDate, 4, 2).'-'.substr($returnedDate, 6, 2);
    }
}
