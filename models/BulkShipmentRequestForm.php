<?php

namespace app\models;

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
    /** @var  UploadedFile */
    public $dataFile;

    /**
     * @author Adeyemi Olaoye <yemi@cottacush.com>
     * @return array
     */
    public function rules()
    {
        return [
            ['dataFile', 'file', 'skipOnEmpty' => false, 'extensions' => 'csv', 'maxSize' => 1000000, 'mimeTypes' => ['text/plain', 'text/csv'], 'checkExtensionByMimeType' => false],
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
            return true;
        } else {
            return false;
        }
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
        if (count($contents) > (self::MAX_ROWS + 1)) {
            $this->addError($attribute, 'Too many requests. Please add shipment requests in batches of ' . self::MAX_ROWS);
            return false;
        }

        if (count($contents) < 2) {
            $this->addError($attribute, 'No requests in data file. Please add shipment requests');
            return false;
        }

        $count = 1;
        $valid = true;
        foreach ($contents as $content) {
            $count++;
            if ($count == 1) {
                continue;
            }
            $valid = $valid && $this->validateRow($content, $count);
        }
        return $valid;
    }

    /**
     * @author Adeyemi Olaoye <yemi@cottacush.com>
     * @return array|string
     */
    public function getFileContents()
    {
        $contents = file_get_contents($this->dataFile->tempName);
        $contents = explode("\r", $contents);
        foreach ($contents as &$content) {
            $content = str_getcsv($content);
        }
        return $contents;
    }

    /**
     * @author Adeyemi Olaoye <yemi@cottacush.com>
     * @param $row
     * @param $rowNumber
     * @return bool
     */
    public function validateRow($row, $rowNumber)
    {
        if (count($row) != 21 && count($row) > 0) {
            $this->addError('dataFile', 'Invalid shipment request on row ' . $rowNumber . ' in ' . $this->dataFile->name . '. Please check and correct');
            return false;
        }
        return true;
    }
}