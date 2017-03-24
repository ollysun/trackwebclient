<?php

namespace app\modules\corporate\models;

use Adapter\CompanyAdapter;
use Adapter\Globals\ServiceConstant;
use Adapter\RefAdapter;
use Adapter\RequestHelper;
use Adapter\ResponseHandler;
use Adapter\Util\Calypso;
use Adapter\Util\Util;
use yii\base\Model;
use yii\web\UploadedFile;

/**
 * Class BulkShipmentRequestForm
 * @author Adeyemi Olaoye <yemi@cottacush.com>
 * @package app\models
 */
class BulkShipmentRequestForm extends Model
{

    const MAX_ROWS = 1000;
    const MIN_ROWS = 1;
    /** @var  UploadedFile */
    public $dataFile;

    /**
     * @author Adeyemi Olaoye <yemi@cottacush.com>
     * @return array
     */
    public function rules()
    {
        return [
            ['dataFile', 'file', 'skipOnEmpty' => false, 'extensions' => 'csv', 'maxSize' => 1000000, 'checkExtensionByMimeType' => false, 'wrongExtension' => 'Invalid File uploaded. Please upload a valid CSV file.'],
            ['dataFile', 'validateRows']
        ];
    }

    /**
     * @author Adeyemi Olaoye <yemi@cottacush.com>
     * @return bool
     */
    public function process()
    {
        if ($this->validate()) {
            $rows = $this->getFileContents();

            $batchData = [];
            $keys = ['receiver_firstname', 'receiver_lastname', 'receiver_phone_number', 'receiver_email',
                'receiver_address', 'receiver_state_id', 'receiver_city_id', 'receiver_company_name', 'no_of_packages',
                'estimated_weight', 'parcel_value', 'cash_on_delivery', 'reference_number', 'description'];

            foreach ($rows as $row) {
                $row = Util::swapKeys($row, $keys);
                $row = $this->substituteStateAndCityWithIds($row);
                $row['company_id'] = Calypso::getValue(Calypso::getInstance()->session("user_session"), 'company_id');
                if (strlen(trim($row['parcel_value'])) == 0) {
                    $row['parcel_value'] = null;
                }
                $row = (object)$row;
                $batchData[] = $row;
            }

            $companyAdapter = new CompanyAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
            $response = $companyAdapter->makeBulkShipmentRequest($batchData);
            if ($response) {
                return true;
            } else {
                $this->addError('dataFile', $companyAdapter->getLastErrorMessage());
            }
        }
        return false;
    }

    /**
     * @author Adeyemi Olaoye <yemi@cottacush.com>
     * @return array|string
     */
    public function getFileContents()
    {
        $contents = Util::readCSV($this->dataFile->tempName);
        $count = 0;
        $result = [];
        foreach ($contents as $rowData) {
            if (++$count == 1) {
                continue;
            }
            if (!$this->isRowEmpty($rowData)) {
                $result[] = $rowData;
            }
        }
        return $result;
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

    private function substituteStateAndCityWithIds($row)
    {
        $refAdapter = new RefAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
        $all_cities = $refAdapter->getAllCities();
        $states = $this->getStates($refAdapter);

        $state_name = $row['receiver_state_id'];
        $state = array_filter($states, function ($value) use ($state_name, $states) {
            $subject_state_name = str_replace(' ', '', $value['name']);
            return ($subject_state_name == strtolower($state_name));
        });
        if ($state) {
            $state = array_values($state);
            $state = $state[0];
            $row['receiver_state_id'] = $state['id'];
            $city_name = $row['receiver_city_id'];
            $cities = array_filter($all_cities, function ($value) use ($state) {
                return ($state['id'] == $value['state_id']);
            });

            if ($cities) {
                $city = array_filter($cities, function ($value) use ($city_name) {
                    return ($value['name'] == strtolower($city_name));
                });
                if ($city) {
                    $city = array_values($city);
                    $city = $city[0];
                    $row['receiver_city_id'] = $city['id'];
                } else {
                    $row['receiver_city_id'] = null;
                }
            } else {
                $row['receiver_city_id'] = null;
            }
        } else {
            $row['receiver_state_id'] = null;
            $row['receiver_city_id'] = null;
        }

        return $row;
    }

    /**
     * @author Adeyemi Olaoye <yemi@cottacush.com>
     * @param $refAdapter RefAdapter
     * @return array
     */
    private function getStates($refAdapter)
    {
        $response = $refAdapter->getStates(ServiceConstant::DEFAULT_COUNTRY);
        $response = new ResponseHandler($response);
        if ($response->isSuccess()) {
            $states = $response->getData();
        } else {
            $states = [];
        }
        return $states;
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
        if (count($contents) > self::MAX_ROWS) {
            $this->addError($attribute, 'Too many requests. Please add shipment requests in batches of ' . self::MAX_ROWS);
            return false;
        }

        if (count($contents) < self::MIN_ROWS) {
            $this->addError($attribute, 'No requests in data file. Please add shipment requests');
            return false;
        }

        $valid = true;
        $row_number = 2;
        foreach ($contents as $content) {
            $valid = $valid && $this->validateRow($content, $row_number);
            $row_number++;
        }
        return $valid;
    }

    /**
     * @author Adeyemi Olaoye <yemi@cottacush.com>
     * @param $row
     * @param $rowNumber
     * @return bool
     */
    private function validateRow($row, $rowNumber)
    {
        if (count($row) != 14 && count($row) > 0) {
            $this->addError('dataFile', 'Invalid shipment request on row ' . $rowNumber . ' in ' . $this->dataFile->name . '. Please check and correct');
            return false;
        }
        return true;
    }
}