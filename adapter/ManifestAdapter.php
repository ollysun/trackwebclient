<?php

namespace Adapter;
use Adapter\Globals\ServiceConstant;

/**
 * Class ManifestAdapter
 * @package adapter
 * @author Adegoke Obasa <goke@cottacush.com>
 */
class ManifestAdapter extends BaseAdapter
{
    /**
     * Gets all manifests
     * Includes Origin, Destination and Driver
     * @author Adegoke Obasa <goke@cottacush.com>
     * @param $allFilters
     * @return array|mixed|string
     */
    public function getManifests($allFilters)
    {
        $validFilters = ['status'];
        $filters = [];
        foreach($allFilters as $filterKey => $filterValue) {
            if(in_array($filterKey, $validFilters)){
                $filters[$filterKey] = $filterValue;
            }
        }

        $filters = array_merge($filters, array(
            'with_holder' => '',
            'with_from_branch' => '',
            'with_to_branch' => ''));

        return $this->request(ServiceConstant::URL_MANIFEST_ALL,
            $filters, self::HTTP_GET);
    }
}