<?php
/**
 * User: Adeyemi Olaoye <yemi@cottacush.com>
 * Date: 9/19/15
 * Time: 4:41 PM
 */

namespace Adapter\Util;

use Moment\Moment;

/**
 * Class Util
 * @author Adeyemi Olaoye <yemi@cottacush.com>
 * @package Adapter\Util
 */
class Util
{

    /**
     * @author Adeyemi Olaoye <yemi@cottacush.com>
     * @param $datetime
     * @return bool|string
     */
    public static function convertDateTimeToTime($datetime)
    {
        return self::formatDate('h:iA', $datetime);
    }

    /**
     * Format date
     * @author Adeyemi Olaoye <yemi@cottacush.com>
     * @param $format
     * @param $datetime
     * @return bool|string
     */
    public static function formatDate($format, $datetime)
    {
        $datetime = strtotime($datetime);
        if (!$datetime) {
            return '';
        }
        return date($format, $datetime);
    }

    public static function convertToTrackingDateFormat($datetime)
    {
        return self::formatDate('d M. Y', $datetime);
    }

    /**
     * Formats a role name to uppercase and removes underscores
     * @author Adegoke Obasa <goke@cottacush.com>
     * @param $string
     * @return string
     */
    public static function formatRoleName($string)
    {
        return ucwords(self::removeUnderscore($string));
    }

    /**
     * Removes underscores from string
     * @author Adegoke Obasa <goke@cottacush.com>
     * @param $string
     * @return mixed
     */
    public static function removeUnderscore($string)
    {
        if (!is_string($string)) {
            return '';
        }
        return preg_replace("/\_+/", " ", $string);
    }

    /**
     * Returns the current date
     * @author Adegoke Obasa <goke@cottacush.com>
     * @param string $separator
     * @return string
     */
    public static function getToday($separator = '-')
    {
        return date("Y{$separator}m{$separator}d");
    }

    /**
     * @author Adeyemi Olaoye <yemi@cottacush.com>
     * @param $array
     * @param $keys
     * @return mixed
     */
    public static function swapKeys($array, $keys)
    {
        $result = [];
        foreach ($array as $key => $value) {
            $result[$keys[$key]] = $value;
        }

        return $result;
    }

    /**
     * This function checks for multiple empty just like empty() works.
     * This function is not in good terms with variables not set being passed to the function
     * unlike empty and isset() that handles them. Improvement.
     * @author imkingdavid (stackoverflow)
     * @return bool
     */
    public static function mempty()
    {

        $arguments = func_get_args();

        foreach ($arguments as $arg) {
            if (empty($arg)) {
                return true;
            } else {
                continue;
            }
        }

        return false;
    }

    /**
     * Checks if a value is empty
     * Special check for number 0
     * @author Adegoke Obasa <goke@cottacush.com>
     * @param $value
     * @return bool
     */
    public static function checkEmpty($value)
    {
        if ($value === 0 || $value === '0') {
            return false;
        }
        return empty($value);
    }

    /**
     * Generates a CSV for download
     * @author Olawale Lawal <wale@cottacush.com>
     * @param $name
     * @param $header
     * @param $data
     */
    public static function exportToCSV($name, array $header, array $data)
    {
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename=' . $name);
        header('Pragma: no-cache');
        header("Expires: 0");

        $stream = fopen("php://output", "w");

        fputcsv($stream, $header);
        foreach ($data as $row) {
            fputcsv($stream, $row);
        }
        fclose($stream);
    }

    /**
     * @author Otaru Babatunde<tunde@cottacush.com>
     */
    public static function getDateTimeFormatFromDateTimeFields($date, $time)
    {
        $date_and_time = $date . " " . $time;
        list($year, $month, $day, $hour, $minute, $dayType) = preg_split('/[\/\s:]+/', $date_and_time);

        if ($hour == 12) {
            if ($dayType == 'AM') {
                $hour = 00;
            } else {
                $hour = 12;
            }
            return $year . '-' . $month . '-' . $day . ' ' . $hour . ":" . $minute . ":00";
        } else {
            return $year . '-' . $month . '-' . $day . ' ' . ($dayType == "PM" ? $hour + 12 : $hour) . ":" . $minute . ":00";
        }
    }

    /**
     * Return an english representation of the time difference
     * @author Olawale Lawal <wale@cottacush.com>
     * @param $past_time
     * @param int $reference
     * @return string
     */
    public static function ago($past_time, $base_time = 'now')
    {
        $old_time = new Moment($base_time);
        $new_time = new Moment($past_time);
        return $new_time->from($old_time)->getRelative();
    }

    /**
     * Reads a CSV file
     * @credit http://www.codedevelopr.com/articles/reading-csv-files-into-php-array/
     * @param $csvFile
     * @return array
     */
    public static function readCSV($csvFile)
    {
        $line_of_text = [];
        $file_handle = fopen($csvFile, 'r');
        while (!feof($file_handle)) {
            $rowData = fgetcsv($file_handle, 1024);
            if ($rowData) {
                $line_of_text[] = $rowData;
            }
        }
        fclose($file_handle);
        return $line_of_text;
    }

}
