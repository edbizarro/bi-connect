<?php

namespace Bi\Connect\Google;

use Google_Service_Analytics;
use Bi\Connect\ConnectResponse;
use Google_Service_Analytics_GaData;

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
     * Returns Analytics data for a view (profile). (ga.get).
     *
     * @param string $gaId Unique table ID for retrieving Analytics data. Table ID is
     * of the form ga:XXXX, where XXXX is the Analytics view (profile) ID.
     * @param string $startDate Start date for fetching Analytics data. Requests can
     * specify a start date formatted as YYYY-MM-DD, or as a relative date (e.g.,
     * today, yesterday, or 7daysAgo). The default value is 7daysAgo.
     * @param string $endDate End date for fetching Analytics data. Request can
     * should specify an end date formatted as YYYY-MM-DD, or as a relative date
     * (e.g., today, yesterday, or 7daysAgo). The default value is yesterday.
     * @param string $metrics A comma-separated list of Analytics metrics. E.g.,
     * 'ga:sessions,ga:pageviews'. At least one metric must be specified.
     * @param array $optOptions Optional parameters.
     *
     * @optOptions array dimensions A comma-separated list of Analytics dimensions.
     * E.g., 'browser,city'.
     * @optOptions array filters A comma-separated list of dimension or metric
     * filters to be applied to Analytics data.
     * @optOptions bool include-empty-rows The response will include empty rows if
     * this parameter is set to true, the default is true
     * @optOptions int max-results The maximum number of entries to include in this
     * feed.
     * @optOptions string output The selected format for the response. Default format
     * is JSON.
     * @optOptions string samplingLevel The desired sampling level.
     * @optOptions string segment An Analytics segment to be applied to data.
     * @optOptions string sort A comma-separated list of dimensions or metrics that
     * determine the sort order for Analytics data.
     * @optOptions int start-index An index of the first entity to retrieve. Use this
     * parameter as a pagination mechanism along with the max-results parameter.
     *
     * @return ConnectResponse
     */
    public function query(
        string $gaId,
        string $startDate,
        string $endDate,
        $metrics,
        array $optOptions = []
    ): ConnectResponse {
        $response = $this->data_ga->get(
            $this->formatQueryParams($gaId),
            $startDate,
            $endDate,
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
