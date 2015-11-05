<?php

namespace app\modules\corporate\models;

use Adapter\Globals\ServiceConstant;
use Adapter\RefAdapter;
use Adapter\RequestHelper;
use Adapter\ResponseHandler;
use PHPExcel;
use PHPExcel_Cell_DataValidation;
use PHPExcel_IOFactory;
use PHPExcel_NamedRange;
use PHPExcel_Worksheet;
use ZipArchive;

/**
 * Class BulkShipment
 * @author Adeyemi Olaoye <yemi@cottacush.com>
 * @package app\modules\corporate\models
 */
class BulkShipment
{
    /**
     * Generate template file
     * @author Adeyemi Olaoye <yemi@cottacush.com>
     * @throws \PHPExcel_Exception
     * @throws \PHPExcel_Reader_Exception
     */
    public static function generateTemplateFile()
    {
        $refAdapter = new RefAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
        $phpExcelObj = $objPHPExcel = new PHPExcel();
        self::generateSheetHeader($phpExcelObj);


        $phpExcelObj->createSheet(1);
        $dataSheet = $phpExcelObj->setActiveSheetIndex(1);
        $dataSheet->setTitle('Data Sheet');

        $response = $refAdapter->getStates(ServiceConstant::COUNTRY_NIGERIA);
        $response = new ResponseHandler($response);
        if ($response->isSuccess()) {
            $states = $response->getData();
        } else {
            $states = [];
        }

        self::addStatesData($states, $dataSheet, $phpExcelObj);
        self::addCitiesData($states, $dataSheet, $phpExcelObj);

        $shipmentsSheet = $phpExcelObj->setActiveSheetIndex(0);
        self::setValidationForCell('F2', 'states', $shipmentsSheet);

        for ($i = 2; $i < 1002; $i++) {
            self::setValidationForCell('G' . $i, 'INDIRECT($F$' . $i . ')', $shipmentsSheet);
        }

        $dataSheet = $phpExcelObj->setActiveSheetIndex(1);
        $dataSheet->setSheetState(PHPExcel_Worksheet::SHEETSTATE_VERYHIDDEN);


        $writer = PHPExcel_IOFactory::createWriter($phpExcelObj, 'Excel2007');
        $writer->save(self::getTemplateFilePath());
        self::extendCellValidationsToWholeColumn(['F'], self::getTemplateFilePath(), 'sheet1');
    }

    /**
     * @author Adeyemi Olaoye <yemi@cottacush.com>
     * @param $phpExcelObj PHPExcel
     * @return mixed
     */
    private static function generateSheetHeader($phpExcelObj)
    {
        $columnHeaders = ['Receiver Firstname*', 'Receiver LastName', 'Receiver Phone Number', 'Receiver Email', 'Receiver Address', 'Receiver State*', 'Receiver City*',
            'Receiver Company Name', 'Number of Packages', 'Estimated Weight', 'Parcel Value', 'Cash on Delivery', 'Reference Number', 'Parcel Description'];

        $sheet = $phpExcelObj->setActiveSheetIndex(0);
        $sheet->setTitle('Shipments');
        for ($i = 1; $i <= count($columnHeaders); $i++) {
            $sheet->setCellValue(self::getColumnNameFromNumber($i) . '1', $columnHeaders[$i - 1]);
        }
        return $phpExcelObj;
    }

    /**
     * Get Excel column name from number - 1 == A
     * @author Adeyemi Olaoye <yemi@cottacush.com>
     * @credits http://stackoverflow.com/questions/3302857/algorithm-to-get-the-excel-like-column-name-of-a-number
     * @param $num
     * @return string
     */
    private static function getColumnNameFromNumber($num)
    {
        $numeric = ($num - 1) % 26;
        $letter = chr(65 + $numeric);
        $num2 = intval(($num - 1) / 26);
        if ($num2 > 0) {
            return self::getColumnNameFromNumber($num2) . $letter;
        } else {
            return $letter;
        }
    }

    /**
     * @author Adeyemi Olaoye <yemi@cottacush.com>
     * @param $states
     * @param $dataSheet PHPExcel_Worksheet
     * @param $phpExcelObj PHPExcel
     * @return PHPExcel_Worksheet
     */
    private static function addStatesData($states, $dataSheet, $phpExcelObj)
    {
        for ($i = 1; $i <= count($states); $i++) {
            $dataSheet->setCellValue('A' . $i, str_replace(' ', '', ucfirst($states[$i - 1]['name'])));
        }
        $phpExcelObj->addNamedRange(new PHPExcel_NamedRange('states', $dataSheet, 'A1:A' . count($states)));
        return $dataSheet;
    }

    /**
     * @author Adeyemi Olaoye <yemi@cottacush.com>
     * @param $states
     * @param $dataSheet PHPExcel_Worksheet
     * @param $phpExcelObj PHPExcel
     * @return PHPExcel
     */
    private static function addCitiesData($states, $dataSheet, $phpExcelObj)
    {
        $columnIndex = 2;
        $refAdapter = new RefAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
        $all_cities = $refAdapter->getAllCities();
        for ($i = 0; $i < count($states); $i++) {
            $column = self::getColumnNameFromNumber($columnIndex);
            $cities = array_filter($all_cities, function ($value) use ($states, $i) {
                return $value['state_id'] == $states[$i]['id'];
            });
            $cities = array_values($cities);
            for ($j = 1; $j <= count($cities); $j++) {
                $city = $cities[$j - 1];
                $dataSheet->setCellValue($column . $j, ucwords($city['name']));
            }
            if (count($cities) > 0) {
                $phpExcelObj->addNamedRange(new PHPExcel_NamedRange(str_replace(' ', '', $states[$i]['name']), $dataSheet, $column . '1:' . $column . count($cities)));
                $columnIndex++;
            }
        }
        return $phpExcelObj;
    }

    /**
     * Set validation for cell
     * @author Adeyemi Olaoye <yemi@cottacush.com>
     * @param $column
     * @param $named_range
     * @param $sheet PHPExcel_Worksheet
     */
    private static function setValidationForCell($column, $named_range, $sheet)
    {
        $objValidation = $sheet->getCell($column)->getDataValidation();
        $objValidation->setType(PHPExcel_Cell_DataValidation::TYPE_LIST);
        $objValidation->setErrorStyle(PHPExcel_Cell_DataValidation::STYLE_INFORMATION);
        $objValidation->setAllowBlank(false);
        $objValidation->setShowInputMessage(true);
        $objValidation->setShowErrorMessage(true);
        $objValidation->setShowDropDown(true);
        $objValidation->setErrorTitle('Input error');
        $objValidation->setError('Value is not in list.');
        $objValidation->setPromptTitle('Pick from list');
        $objValidation->setPrompt('Please pick a value from the drop-down list');
        $objValidation->setFormula1("=" . $named_range);
    }

    /**
     * Get template file path
     * @author Adeyemi Olaoye <yemi@cottacush.com>
     * @return string
     */
    public static function getTemplateFilePath()
    {
        return dirname(__FILE__) . '/../../../runtime/template.xlsx';
    }

    /**
     * Extend validation of cell to whole column
     * @credits https://github.com/PHPOffice/PHPExcel/issues/325
     * @author Adeyemi Olaoye <yemi@cottacush.com>
     * @param $columns
     * @param $file_name
     * @param $sheet_name
     */
    private static function extendCellValidationsToWholeColumn($columns, $file_name, $sheet_name)
    {
        $zip = new ZipArchive();
        $zip->open($file_name);

        $sheet_file = $zip->getFromName("xl/worksheets/$sheet_name.xml");
        $sheet = simplexml_load_string($sheet_file);
        if (property_exists($sheet, 'dataValidations') && $sheet->dataValidations && $sheet->dataValidations->dataValidation) {
            foreach ($sheet->dataValidations->dataValidation as $validation) {
                $row = substr($validation['sqref'], 0, 1);
                if (in_array($row, $columns)) {
                    $validation['sqref'] .= ':' . $row . '1048576';
                }
            }
        }

        $zip->addFromString("xl/worksheets/$sheet_name.xml", $sheet->asXML());
        $zip->close();
    }

    /**
     * Push File to Client
     * @author Adeyemi Olaoye <yemi@cottacush.com>
     * @param $file_path
     * @param $mime_type
     * @param $file_name
     * @param bool $delete_file
     */
    public static function pushFileToClient($file_path, $mime_type, $file_name, $delete_file = false)
    {
        header("Content-Type: $mime_type");
        header("Content-Disposition: attachment;filename='$file_name'");
        header("Content-Transfer-Encoding: binary");
        echo file_get_contents($file_path);
        if ($delete_file) {
            unlink($file_path);
        }
        exit();
    }

}