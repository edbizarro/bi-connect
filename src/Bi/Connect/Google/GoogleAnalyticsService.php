<?php

namespace Bi\Connect\Google;

use Bi\Connect\ConnectResponse;
use Carbon\Carbon;

/**
 * Class GoogleAnalyticsService.
 */
class GoogleAnalyticsService extends \Google_Service_Analytics
{
    /**
     * @return ConnectResponse
     */
    public function getAccounts()
    {
        $response = $this->management_accounts->listManagementAccounts()->getItems();

        return $this->formatSimpleResponse($response);
    }

    /**
     * @param string|int $accountId
     *
     * @return ConnectResponse
     */
    public function getProperties($accountId = '~all')
    {
        $properties = $this->management_webproperties->listManagementWebproperties($accountId)->getItems();

        return $this->formatSimpleResponse($properties);
    }

    /**
     * @param $accountId
     * @param $propertyId
     *
     * @return ConnectResponse
     */
    public function getProfiles($accountId = '~all', $propertyId = '~all')
    {
        $profiles = $this->management_profiles->listManagementProfiles($accountId, $propertyId)->getItems();

        return $this->formatSimpleResponse($profiles);
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
    ) {
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
    protected function formatSimpleResponse($originalResponse)
    {
        $body = [];

        foreach ($originalResponse as $key => $item) {
            $body[$key]['id'] = $item->id;
            $body[$key]['name'] = $item->name;
        }

        return new ConnectResponse(
            [],
            $body,
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
    protected function formatQueryResponse($queryResponse)
    {
        $resultadoFormatado = [];

        $cabecalhosResposta = $this->extractHeaders($queryResponse);

        foreach ($queryResponse['rows'] as $kl => $linha) {
            foreach ($linha as $k => $valores) {
                if ($cabecalhosResposta[$k] == 'date') {
                    $valores = $this->formatQueryReturnDate($valores);
                }
                $resultadoFormatado[$kl][$cabecalhosResposta[$k]] = $valores;
            }
        }

        return new ConnectResponse(
            $cabecalhosResposta,
            $resultadoFormatado,
            $queryResponse
        );
    }

    /**
     * @param array $params
     *
     * @return string
     */
    protected function formatQueryParams($params = [])
    {
        if (is_array($params) == false) {
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
     * @param array $params
     *
     * @return mixed
     */
    protected function formatOptParams($params = [])
    {
        if (isset($params['dimensions'])) {
            if (is_array($params['dimensions']) == false) {
                if (strpos($params['dimensions'], 'ga:') === false) {
                    $params['dimensions'] = 'ga:'.$params['dimensions'];
                }
            }

            if (is_array($params['dimensions'])) {
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
    protected function extractHeaders($headers = [])
    {
        $originalHeaders = $headers['modelData']['columnHeaders'];
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
    public function formatQueryReturnDate($returnedDate)
    {
        return substr($returnedDate, 0, 4).'-'.substr($returnedDate, 4, 2).'-'.substr($returnedDate, 6, 2);
    }
}
