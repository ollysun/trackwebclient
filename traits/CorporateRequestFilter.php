<?php

namespace app\traits;


use Adapter\Util\Calypso;
use Adapter\Util\Util;

/**
 * Class CorporateRequestFilter
 * Used to filters requests to the corporate requests APIs
 * @package app\traits
 * @author Adegoke Obasa <goke@cottacush.com>
 */
trait CorporateRequestFilter
{
    /**
     * Returns an array with created_at filters
     * @author Adegoke Obasa <goke@cottacush.com>
     * @return array
     */
    public function getCreatedAtFilters()
    {
        $filters = [];
        $validFilters = ['from' => 'start_created_at', 'to' => 'end_created_at'];
        
        foreach ($validFilters as $clientFilter => $serverFilter) {
            $value = \Yii::$app->getRequest()->get($clientFilter, $this->getDefaultDate());
            if (preg_match('/\bstart\_\w+\_at\b/', $serverFilter)) {
                $filters[$serverFilter] = $value . " 00:00:00";
            } else if (preg_match('/\bend\_\w+\_at\b/', $serverFilter)) {
                $filters[$serverFilter] = $value . " 23:59:59";
            }
        }

        return $filters;
    }

    /**
     * Gets the default date
     * @author Adegoke Obasa <goke@cottacush.com>
     * @return string
     */
    public function getDefaultDate()
    {
        return Util::getToday('/');
    }

    /**
     * Get the start created date
     * @author Adegoke Obasa <goke@cottacush.com>
     * @param $filters
     * @return null
     */
    public function getFromCreatedAtDate($filters)
    {
        return Calypso::getValue($filters, 'start_created_at', $this->getDefaultDate());
    }

    /**
     * Get the end created date
     * @author Adegoke Obasa <goke@cottacush.com>
     * @param $filters
     * @return null
     */
    public function getToCreatedAtDate($filters)
    {
        return Calypso::getValue($filters, 'end_created_at', $this->getDefaultDate());
    }


}