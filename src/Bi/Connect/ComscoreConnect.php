<?php

namespace Bi\Connect;

use PHPExcel_IOFactory;
use Bi\Connect\Base\ImportSheetConnect;

/**
 * Class ComscoreConnect.
 */
class ComscoreConnect extends ImportSheetConnect
{
    protected $apiType;
    /**
     * ComscoreConnect constructor.
     *
     * @param $type
     */
    public function __construct($type)
    {
        $this->apiType = $type;
    }

    /**
     * getInfos by services.
     *
     * @param $file
     * @param array $params
     *
     * @return \Bi\Connect\Interfaces\ResponseInterface
     */
    public function getInfosByFile(array $file, array $params)
    {
        $defaultHeader = [
            'headers' => [
                'Content-type' => 'application/json',
                'Accept' => 'application/json',
            ],
        ];

        $params = array_merge($params, $defaultHeader);

        $objPHPExcel = PHPExcel_IOFactory::load($file['tmp_name']);
        $objWorksheet = $objPHPExcel->getActiveSheet();

        $n_row = 0;
        $arrayHeader = [];

        foreach ($objWorksheet->getRowIterator() as $row) {
            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(false);
            $n_cell = 0;
            foreach ($cellIterator as $cell) {
                if ($n_row == 0) {
                    array_push($arrayHeader, $cell->getValue());
                    if ($n_cell == 0) {
                        $first_col = $cell->getValue();
                    }
                }
                $n_cell = $n_cell + 1;
            }
            $n_row = $n_row + 1;
        }
        $arrayData = [];
        $ant_row = '';
        $n_row = 0;

        $response = [];
        $ant_row = '';
        $n_row = 0;

        foreach ($objWorksheet->getRowIterator() as $row) {
            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(false);
            $n_cell = 0;
            if ($n_row > 0) {
                foreach ($cellIterator as $cell) {
                    if ($n_cell == 0) {
                        $ant_row = $n_row;//$cell->getValue();
                        $response[$ant_row] = [];
                    }
                    if (trim($cell->getValue())) {
                        $response[$ant_row][$arrayHeader[$n_cell]] = trim($cell->getValue());
                    }
                    $n_cell = $n_cell + 1;
                }
            }
            $n_row = $n_row + 1;
        }

        return $response;
    }

    /**
     * @param $response
     *
     * @return ConnectResponse
     */
    protected function formatResponse($response)
    {
        $header = $body = [];
        $body = $rawBody = json_decode($response, true);

        return new ConnectResponse(
            $header,
            $body,
            $rawBody
        );
    }
}
