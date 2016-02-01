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
     * Get's current date in specified format
     * @author Adegoke Obasa <goke@cottacush.com>
     * @param string $format
     * @return bool|string
     */
    public static function getCurrentDate($format = 'd/m/Y') {
        return date($format);
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
            if (isset($keys[$key])) {
                $result[$keys[$key]] = $value;
            }
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
        exit;
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
     * @return string
     */
    public static function ago($past_time, $base_time = 'now')
    {
        $old_time = new Moment($base_time);
        $new_time = new Moment($past_time);
        return $new_time->subtractHours(1)->from($old_time)->getRelative();
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

    /**
     * Converts a number to words
     * @credit http://www.karlrixon.co.uk/writing/convert-numbers-to-words-with-php/
     * @param $number
     * @return bool|null|string
     */
    public static function convert_number_to_words($number) {

        $hyphen      = '-';
        $conjunction = ' and ';
        $separator   = ', ';
        $negative    = 'negative ';
        $decimal     = ' point ';
        $dictionary  = array(
            0                   => 'zero',
            1                   => 'one',
            2                   => 'two',
            3                   => 'three',
            4                   => 'four',
            5                   => 'five',
            6                   => 'six',
            7                   => 'seven',
            8                   => 'eight',
            9                   => 'nine',
            10                  => 'ten',
            11                  => 'eleven',
            12                  => 'twelve',
            13                  => 'thirteen',
            14                  => 'fourteen',
            15                  => 'fifteen',
            16                  => 'sixteen',
            17                  => 'seventeen',
            18                  => 'eighteen',
            19                  => 'nineteen',
            20                  => 'twenty',
            30                  => 'thirty',
            40                  => 'fourty',
            50                  => 'fifty',
            60                  => 'sixty',
            70                  => 'seventy',
            80                  => 'eighty',
            90                  => 'ninety',
            100                 => 'hundred',
            1000                => 'thousand',
            1000000             => 'million',
            1000000000          => 'billion',
            1000000000000       => 'trillion',
            1000000000000000    => 'quadrillion',
            1000000000000000000 => 'quintillion'
        );

        if (!is_numeric($number)) {
            return false;
        }

        if (($number >= 0 && (int) $number < 0) || (int) $number < 0 - PHP_INT_MAX) {
            // overflow
            trigger_error(
                'convert_number_to_words only accepts numbers between -' . PHP_INT_MAX . ' and ' . PHP_INT_MAX,
                E_USER_WARNING
            );
            return false;
        }

        if ($number < 0) {
            return $negative . self::convert_number_to_words(abs($number));
        }

        $string = $fraction = null;

        if (strpos($number, '.') !== false) {
            list($number, $fraction) = explode('.', $number);
        }

        switch (true) {
            case $number < 21:
                $string = $dictionary[$number];
                break;
            case $number < 100:
                $tens   = ((int) ($number / 10)) * 10;
                $units  = $number % 10;
                $string = $dictionary[$tens];
                if ($units) {
                    $string .= $hyphen . $dictionary[$units];
                }
                break;
            case $number < 1000:
                $hundreds  = $number / 100;
                $remainder = $number % 100;
                $string = $dictionary[$hundreds] . ' ' . $dictionary[100];
                if ($remainder) {
                    $string .= $conjunction . self::convert_number_to_words($remainder);
                }
                break;
            default:
                $baseUnit = pow(1000, floor(log($number, 1000)));
                $numBaseUnits = (int) ($number / $baseUnit);
                $remainder = $number % $baseUnit;
                $string = self::convert_number_to_words($numBaseUnits) . ' ' . $dictionary[$baseUnit];
                if ($remainder) {
                    $string .= $remainder < 100 ? $conjunction : $separator;
                    $string .= self::convert_number_to_words($remainder);
                }
                break;
        }

        if (null !== $fraction && is_numeric($fraction)) {
            $string .= $decimal;
            $words = array();
            foreach (str_split((string) $fraction) as $number) {
                $words[] = $dictionary[$number];
            }
            $string .= implode(' ', $words);
        }

        return $string;
    }

    /**
     * Get Excel column name from number - 1 == A
     * @author Adeyemi Olaoye <yemi@cottacush.com>
     * @credits http://stackoverflow.com/questions/3302857/algorithm-to-get-the-excel-like-column-name-of-a-number
     * @param $num
     * @return string
     */
    public static function getExcelColumnNameFromNumber($num)
    {
        $numeric = ($num - 1) % 26;
        $letter = chr(65 + $numeric);
        $num2 = intval(($num - 1) / 26);
        if ($num2 > 0) {
            return self::getExcelColumnNameFromNumber($num2) . $letter;
        } else {
            return $letter;
        }
    }

    /**
     * Get Excel Column Number from Name
     * @author Adeyemi Olaoye <yemi@cottacush.com>
     * @credits http://www.bradino.com/php/excel-column-convert-letters-to-numbers/
     * @param $col
     * @param int $start
     * @return int
     */
    public static function getExcelColumnNumberFromName($col, $start = 0)
    {
        $col = str_pad($col, 2, '0', STR_PAD_LEFT);
        $i = ($col{0} == '0') ? 0 : (ord($col{0}) - 64) * 26;
        $i += ord($col{1}) - 64;
        return $i - (1 - $start);
    }

}
