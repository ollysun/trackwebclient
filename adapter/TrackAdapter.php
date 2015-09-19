<?php
/**
 * Created by PhpStorm.
 * User: Icreatechub
 * Date: 7/30/2015
 * Time: 8:50 PM
 */

namespace Adapter;


use Adapter\Globals\ServiceConstant;

class TrackAdapter extends BaseAdapter
{
    /**
     * Get Tracking Info
     * @author Adeyemi Olaoye <yemi@cottacush.com>
     * @param $tracking_number
     * @return array|mixed|string
     */
    public function getTrackingInfo($tracking_number)
    {
        $response = $this->request(ServiceConstant::URL_PARCEL_HISTORY, ['waybill_number' => $tracking_number, 'reference_number' => $tracking_number, 'with_parcel' => 1], self::HTTP_GET);
        return $this->decodeResponse($response);
    }

}