<?php

namespace app\models;

use Adapter\CompanyAdapter;
use Adapter\Globals\ServiceConstant;
use Adapter\RefAdapter;
use Adapter\RequestHelper;
use Adapter\Util\Calypso;
use Adapter\Util\Util;
use PHPExcel_IOFactory;
use yii\base\Model;
use yii\web\UploadedFile;

/**
 * Class BulkShipmentModel
 * @author Adeyemi Olaoye <yemi@cottacush.com>
 * @package app\models
 */
class BulkShipmentModel extends Model
{
    const MIN_ROWS = 1;
    /** @var  UploadedFile */
    public $dataFile;
    public $company_id;
    public $billing_plan_id;
    private $shipmentData = [];
    private $cities;
    private $company;
    private $currentRowNumber;
    private $currentRow;
    private $map;
    private $reverse_map;
    private $parcel_types;

    /**
     * @author Adeyemi Olaoye <yemi@cottacush.com>
     * @return array
     */
    public function rules()
    {
        return [
            ['dataFile', 'file', 'skipOnEmpty' => false, 'extensions' => ['xls', 'xlsx'], 'maxSize' => 1000000, 'checkExtensionByMimeType' => false, 'wrongExtension' => 'Invalid File uploaded. Please upload a valid XLS or XLSX file.'],
            ['dataFile', 'validateRows'],
            [['billing_plan_id', 'company_id'], 'safe']
        ];
    }

    /**
     * @author Adeyemi Olaoye <yemi@cottacush.com>
     * @return bool
     */
    public function process()
    {
        $adapter = new CompanyAdapter();
        $this->company = $adapter->getCompany($this->company_id);

        $this->map = $this->getShipmentFieldsMap();
        $this->reverse_map = array_flip($this->map);

        $refData = new RefAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
        $this->parcel_types = Calypso::getValue($refData->getparcelType(), 'data', []);

        return $this->validate();
    }

    /**
     * Get Shipment fields map
     * @author Adeyemi Olaoye <yemi@cottacush.com>
     * @return array
     */
    private function getShipmentFieldsMap()
    {
        return [
            Util::getExcelColumnNumberFromName('G') => 'reference',
            Util::getExcelColumnNumberFromName('H') => 'receiver_name',
            Util::getExcelColumnNumberFromName('I') => 'receiver_address_1',
            Util::getExcelColumnNumberFromName('J') => 'receiver_address_2',
            Util::getExcelColumnNumberFromName('K') => 'receiver_address_3',
            Util::getExcelColumnNumberFromName('L') => 'receiver_address_4',
            Util::getExcelColumnNumberFromName('N') => 'receiver_city',
            Util::getExcelColumnNumberFromName('O') => 'receiver_country',
            Util::getExcelColumnNumberFromName('P') => 'receiver_email',
            Util::getExcelColumnNumberFromName('Q') => 'receiver_phone_number',
            Util::getExcelColumnNumberFromName('R') => 'weight',
            Util::getExcelColumnNumberFromName('T') => 'no_of_packages',
            Util::getExcelColumnNumberFromName('U') => 'parcel_type',
            Util::getExcelColumnNumberFromName('X') => 'description_1',
            Util::getExcelColumnNumberFromName('Y') => 'description_2',
            Util::getExcelColumnNumberFromName('AF') => 'sender_name',
            Util::getExcelColumnNumberFromName('AG') => 'sender_country',
            Util::getExcelColumnNumberFromName('AI') => 'sender_city',
            Util::getExcelColumnNumberFromName('AJ') => 'sender_address_1',
            Util::getExcelColumnNumberFromName('AK') => 'sender_address_2',
            Util::getExcelColumnNumberFromName('AL') => 'sender_address_3',
            Util::getExcelColumnNumberFromName('AM') => 'sender_address_3',
        ];
    }

    /**
     * @author Adeyemi Olaoye <yemi@cottacush.com>
     * @param $attribute
     * @param $params
     * @return bool
     */
    public function validateRows($attribute, $params)
    {
        $contents = $this->getFileContents();

        if ($this->hasErrors()) {
            return false;
        }

        if (count($contents) < self::MIN_ROWS) {
            $this->addError($attribute, 'No requests in data file. Please add shipments');
            return false;
        }

        $refAdapter = new RefAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
        $this->cities = $refAdapter->getAllCities();

        $this->currentRowNumber = 2;
        foreach ($contents as $content) {
            $this->currentRow = $content;
            if (!$this->refineShipmentData()) {
                return false;
            }
            $this->shipmentData[] = $this->currentRow;
            $this->currentRowNumber++;
        }
        return true;
    }

    /**
     * @author Adeyemi Olaoye <yemi@cottacush.com>
     * @return array|string
     */
    private function getFileContents()
    {
        try {
            $inputFileType = PHPExcel_IOFactory::identify($this->dataFile->tempName);
            $objReader = PHPExcel_IOFactory::createReader($inputFileType);
            $objPHPExcel = $objReader->load($this->dataFile->tempName);
        } catch (\Exception $ex) {
            $this->addError('dataFile', $ex->getMessage());
            return false;
        }

        $sheet = $objPHPExcel->getSheet(0);
        $highestRow = $sheet->getHighestRow();
        $highestColumn = 'AZ';

        $contents = [];
        for ($row = 2; $row <= $highestRow; $row++) {
            $range = 'A' . $row . ':' . $highestColumn . $row;
            $rowData = $sheet->rangeToArray($range, null, true, false);
            if (is_array($rowData)) {
                $rowData = $rowData[0];
            } else {
                continue;
            }
            if (!$this->isRowEmpty($rowData)) {
                $contents[] = $rowData;
            }
        }
        return $contents;
    }

    /**
     * @author Adeyemi Olaoye <yemi@cottacush.com>
     * @param $row
     * @return bool
     */
    private function isRowEmpty($row)
    {
        foreach ($row as $index => $column) {
            if (!is_null($column) && strlen(trim($column)) != 0) {
                return false;
            }
        }
        return true;
    }

    /**
     * Refine Shipment Data
     * @author Adeyemi Olaoye <yemi@cottacush.com>
     * @return bool
     */
    private function refineShipmentData()
    {
        $this->currentRow = Util::swapKeys($this->currentRow, $this->map);
        if (!$this->setReceiverLocationDetails()) {
            return false;
        }

        if (!$this->setSenderLocationDetails()) {
            return false;
        }

        $this->setSenderName();

        if (!$this->setReceiverName()) {
            return false;
        }

        $this->currentRow['sender_country'] = ServiceConstant::COUNTRY_NIGERIA;
        $this->currentRow['receiver_country'] = ServiceConstant::COUNTRY_NIGERIA;

        $this->setSenderAddress();

        if (!$this->setReceiverAddress()) {
            return false;
        }

        $this->setDescription();

        if (!$this->setParcelType()) {
            return false;
        }

        return true;
    }

    /**
     * Set receiver location details
     * @author Adeyemi Olaoye <yemi@cottacush.com>
     * @return bool
     */
    private function setReceiverLocationDetails()
    {
        if (is_null($this->currentRow['receiver_city'])) {
            $this->addError('dataFile', 'Please enter receiver city ' . $this->getCellInformation('receiver_city'));
            return false;
        }

        $city = $this->getCityByName($this->currentRow['receiver_city']);
        if (!$city) {
            $this->addError('dataFile', 'Invalid Receiver City *' . $this->currentRow['receiver_city'] . '* ' . $this->getCellInformation('receiver_city'));
            return false;
        }
        $this->currentRow['receiver_city'] = $city['id'];
        $this->currentRow['receiver_state'] = $city['state_id'];
        return true;
    }

    /**
     * @author Adeyemi Olaoye <yemi@cottacush.com>
     * @param $key
     * @return string
     */
    private function getCellInformation($key)
    {
        return 'on row ' . $this->currentRowNumber . ', column ' . Util::getExcelColumnNameFromNumber($this->reverse_map[$key] + 1);
    }

    private function getCityByName($cityName)
    {
        $city = array_filter($this->cities, function ($value) use ($cityName) {
            return ($value['name'] == strtolower($cityName));
        });

        if (is_array($city) && count($city) > 0) {
            $city = array_values($city);
            $city = $city[0];
        }

        return $city;
    }

    /**
     * Set sender location details
     * @author Adeyemi Olaoye <yemi@cottacush.com>
     * @return bool
     */
    private function setSenderLocationDetails()
    {
        if (is_null($this->currentRow['sender_city'])) {
            $this->currentRow['sender_city'] = $this->company['city_id'];
            $this->currentRow['sender_state'] = $this->company['city']['state_id'];
        } else {
            $city = $this->getCityByName($this->currentRow['sender_city']);
            if (!$city) {
                $this->addError('dataFile', 'Invalid Sender City *' . $this->currentRow['receiver_city'] . '* on row ' . $this->currentRowNumber);
                return false;
            }
            $this->currentRow['sender_city'] = $city['id'];
            $this->currentRow['sender_state'] = $city['state_id'];
        }
        return true;
    }

    /**
     * Set Sender Name
     * @author Adeyemi Olaoye <yemi@cottacush.com>
     */
    private function setSenderName()
    {
        if (is_null($this->currentRow['sender_name'])) {
            $this->currentRow['sender_name'] = $this->company['name'];
        }
    }

    /**
     * Set Receiver Name
     * @author Adeyemi Olaoye <yemi@cottacush.com>
     */
    private function setReceiverName()
    {
        if (is_null($this->currentRow['receiver_name'])) {
            $this->addError('dataFile', 'Please set Receiver Name ' . $this->getCellInformation('receiver_name'));
            return false;
        }
        return true;
    }

    /**
     * Set Sender Address
     * @author Adeyemi Olaoye <yemi@cottacush.com>
     */
    private function setSenderAddress()
    {
        if (is_null($this->currentRow['sender_address_1']) && is_null($this->currentRow['sender_address_2']) && is_null($this->currentRow['sender_address_3'])) {
            $this->currentRow['sender_address'] = $this->company['address'];
        } else {
            $this->currentRow['sender_address'] = implode(', ', array_filter([$this->currentRow['sender_address_1'], $this->currentRow['sender_address_2'], $this->currentRow['sender_address_3']]));
        }
        unset($this->currentRow['sender_address_1'], $this->currentRow['sender_address_2'], $this->currentRow['sender_address_3']);
    }

    /**
     * Set Receiver Address
     * @author Adeyemi Olaoye <yemi@cottacush.com>
     */
    private function setReceiverAddress()
    {
        if (is_null($this->currentRow['receiver_address_1']) && is_null($this->currentRow['receiver_address_2']) && is_null($this->currentRow['receiver_address_3']) && is_null($this->currentRow['receiver_address_4'])) {
            $this->addError('dataFile', 'Please set Receiver Address ' . $this->getCellInformation('receiver_address_1'));
            return false;
        } else {
            $this->currentRow['receiver_address'] = implode(', ', array_filter([$this->currentRow['receiver_address_1'], $this->currentRow['receiver_address_2'], $this->currentRow['receiver_address_3'], $this->currentRow['receiver_address_4']]));
        }

        unset($this->currentRow['receiver_address_1'], $this->currentRow['receiver_address_2'], $this->currentRow['receiver_address_3'], $this->currentRow['receiver_address_4']);
        return true;
    }

    /**
     * Set Description
     * @author Adeyemi Olaoye <yemi@cottacush.com>
     */
    private function setDescription()
    {
        $this->currentRow['description'] = implode(', ', array_filter([$this->currentRow['description_1'], $this->currentRow['description_2']]));
        unset($this->currentRow['description_1'], $this->currentRow['description_2']);
    }

    /**
     * Set Parcel Type
     * @author Adeyemi Olaoye <yemi@cottacush.com>
     * @return bool
     */
    private function setParcelType()
    {
        if (is_null($this->currentRow['parcel_type'])) {
            $this->addError('dataFile', 'Please enter a Shipment Type ' .
                $this->getCellInformation('parcel_type').  'Shipment type should be one of ' . strtoupper(implode(', ', array_column($this->parcel_types, 'name'))));
            return false;
        }

        $parcel_type = $this->getParcelTypeByName($this->currentRow['parcel_type']);
        if (!$parcel_type) {
            $this->addError('dataFile', 'Invalid Shipment type *' . strtoupper($this->currentRow['parcel_type']) . '* ' . $this->getCellInformation('parcel_type') . '. Shipment type should be one of ' . strtoupper(implode(', ', array_column($this->parcel_types, 'name'))));
            return false;
        }
        $this->currentRow['parcel_type'] = $parcel_type['name'];
        return true;

    }

    private function getParcelTypeByName($name)
    {
        $parcel_type = array_filter($this->parcel_types, function ($value) use ($name) {
            return ($value['name'] == strtolower($name));
        });

        if (is_array($parcel_type) && count($parcel_type) > 0) {
            $parcel_type = array_values($parcel_type);
            $parcel_type = $parcel_type[0];
        }

        return $parcel_type;
    }

}