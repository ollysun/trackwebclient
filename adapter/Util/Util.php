<?php
/**
 * User: Adeyemi Olaoye <yemi@cottacush.com>
 * Date: 9/19/15
 * Time: 4:41 PM
 */

namespace Adapter\Util;


/**
 * Class Util
 * @author Adeyemi Olaoye <yemi@cottacush.com>
 * @package Adapter\Util
 */
class Util
{

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

    /**
     * @author Adeyemi Olaoye <yemi@cottacush.com>
     * @param $datetime
     * @return bool|string
     */
    public static function convertDateTimeToTime($datetime)
    {
        return self::formatDate('h:iA', $datetime);
    }

    public static function convertToTrackingDateFormat($datetime)
    {
        return self::formatDate('d M. Y', $datetime);
    }

    /**
     * Removes underscores from string
     * @author Adegoke Obasa <goke@cottacush.com>
     * @param $string
     * @return mixed
     */
    public static function removeUnderscore($string)
    {
        if(!is_string($string)) {
            return '';
        }
        return preg_replace("/\_+/", " ", $string);
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
     * Returns the current date
     * @author Adegoke Obasa <goke@cottacush.com>
     * @param $string
     * @return string
     */
    public static function getToday(){
        return date('Y-m-d');
    }
}
