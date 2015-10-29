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
     * @param $filters
     * @return array|mixed|string
     */
    public function getManifests($filters)
    {
        $filters = array_merge($filters, array(
            'with_holder' => '',
            'with_from_branch' => '',
            'with_sender_admin' => '',
            'with_total_count' => 'true',
            'with_to_branch' => ''));

        return $this->request(ServiceConstant::URL_MANIFEST_ALL,
            $filters, self::HTTP_GET);
    }

    /**
     * Gets the details of a manifest
     * @author Adegoke Obasa <goke@cottacush.com>
     * @param $manifestId
     * @return array|mixed|string
     */
    public function getManifest($manifestId)
    {
        return $this->request(ServiceConstant::URL_MANIFEST_ONE, array(
            'manifest_id' => $manifestId,
            'with_holder' => '',
            'with_from_branch' => '',
            'with_sender_admin' => '',
            'with_parcels' => '',
            'with_to_branch' => ''), self::HTTP_GET);
    }
}