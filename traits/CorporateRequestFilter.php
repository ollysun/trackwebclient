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
        $validFilters = ['from' => 'from_created_at', 'to' => 'to_created_at'];
        
        foreach ($validFilters as $clientFilter => $serverFilter) {
            $value = \Yii::$app->getRequest()->get($clientFilter, $this->getDefaultDate());
            $filters[$serverFilter] = $value;
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
        return Calypso::getValue($filters, 'from_created_at', $this->getDefaultDate());
    }

    /**
     * Get the end created date
     * @author Adegoke Obasa <goke@cottacush.com>
     * @param $filters
     * @return null
     */
    public function getToCreatedAtDate($filters)
    {
        return Calypso::getValue($filters, 'to_created_at', $this->getDefaultDate());
    }


}