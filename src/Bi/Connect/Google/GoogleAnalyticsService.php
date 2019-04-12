<?php

namespace Bi\Connect\Google;

use Bi\Connect\ConnectResponse;
use Google_Service_Analytics_GaData;
use Tightenco\Collect\Support\Collection;

/**
 * Class GoogleAnalyticsService.
 */
class GoogleAnalyticsService extends ConnectResponse
{
    /**
     * @return Collection
     */
    public function getAccounts(): Collection
    {
        return $this->formatResponse(
            $this->management_accounts->listManagementAccounts()->getItems()
        );
    }

    /**
     * @param string|int $accountId
     *
     * @return Collection
     */
    public function getProperties($accountId = '~all'): Collection
    {
        return $this->formatResponse(
            $this->management_webproperties->listManagementWebproperties($accountId)->getItems()
        );
    }

    /**
     * @param $accountId
     * @param $propertyId
     *
     * @return Collection
     */
    public function getProfiles($accountId = '~all', $propertyId = '~all'): Collection
    {
        return $this->formatResponse(
            $this->management_profiles->listManagementProfiles($accountId, $propertyId)->getItems()
        );
    }

    /**
     * @param string $accountId
     * @param string $propertyId
     * @param string $profileId
     *
     * @return Collection
     */
    public function getGoals($accountId = '~all', $propertyId = '~all', $profileId = '~all'): Collection
    {
        return $this->formatResponse(
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
     * @param string|array $metrics A comma-separated list of Analytics metrics. E.g.,
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
     * @return Collection
     * @throws \Google_Exception
     */
    public function query(
        string $gaId,
        string $startDate,
        string $endDate,
        $metrics,
        array $optOptions = []
    ): Collection {
        $result = $this->data_ga->get(
            $this->formatQueryParams($gaId),
            $startDate,
            $endDate,
            $this->formatQueryParams($metrics),
            $this->formatOptParams($optOptions)
        );

        while ($nextLink = $result->getNextLink()) {
            if (isset($others['max-results']) && count($result->rows) >= $optOptions['max-results']) {
                break;
            }

            $options = [];

            parse_str(substr($nextLink, strpos($nextLink, '?') + 1), $options);

            $response = $this->data_ga->call('get', [$options], 'Google_Service_Analytics_GaData');

            if ($response->rows) {
                $result->rows = \array_merge($result->rows, $response->rows);
            }

            $result->nextLink = $response->nextLink;
        }

        return $this->formatQueryResponse($result);
    }

    /**
     * @param $originalResponse
     *
     * @return Collection
     */
    public function formatResponse($originalResponse): Collection
    {
        return (new ConnectResponse(
            [],
            collect($originalResponse)->transform(function ($item) {
                return [$item->id => $item->name];
            })->all(),
            $originalResponse
        ))->getBody();
    }

    /**
     * Transform the query response.
     *
     * @param $queryResponse
     *
     * @return Collection
     */
    protected function formatQueryResponse(Google_Service_Analytics_GaData $queryResponse): Collection
    {
        $responseHeaders = $this->extractHeaders($queryResponse);

        $result = collect($queryResponse->rows)->transform(function ($item) use ($responseHeaders) {
            return array_combine($responseHeaders, $item);
        });

        return (new ConnectResponse(
            $responseHeaders,
            $result->all(),
            $queryResponse
        ))->getBody();
    }

    /**
     * @param mixed $params
     *
     * @return string
     */
    protected function formatQueryParams($params = null): string
    {
        if (\is_array($params) === false) {
            return 'ga:' . $params;
        }

        $formattedParams = [];

        foreach ($params as $k => $param) {
            if (strpos($param, 'ga:') !== false) {
                $formattedParams[$k] = $param;
                continue;
            }
            $formattedParams[$k] = 'ga:' . $param;
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
                $params['dimensions'] = 'ga:' . $params['dimensions'];
            }

            if (\is_array($params['dimensions'])) {
                foreach ($params['dimensions'] as $k => $param) {
                    if (strpos($param, 'ga:') !== false) {
                        $params['dimensions'][$k] = $param;
                        continue;
                    }
                    $params['dimensions'][$k] = 'ga:' . $param;
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
        $originalHeaders  = $headers['columnHeaders'];
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
        return substr($returnedDate, 0, 4) . '-' . substr($returnedDate, 4, 2) . '-' . substr($returnedDate, 6, 2);
    }
}
