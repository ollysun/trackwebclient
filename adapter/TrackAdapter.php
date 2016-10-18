<?php
/**
 * Created by PhpStorm.
 * User: Icreatechub
 * Date: 7/30/2015
 * Time: 8:50 PM
 */

namespace Adapter;


use Adapter\Globals\ServiceConstant;

/**
 * Class TrackAdapter
 * @author Adeyemi Olaoye <yemi@cottacush.com>
 * @package Adapter
 */
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
        $response = $this->request(ServiceConstant::URL_PARCEL_HISTORY, ['waybill_number' => $tracking_number, 'reference_number' => $tracking_number,
            'order_number' => $tracking_number, 'with_parcel' => 1], self::HTTP_GET);
        return $this->decodeResponse($response);
    }

    /**
     * Get Tracking Info
     * @author Adeyemi Olaoye <yemi@cottacush.com>
     * @param $tracking_number
     * @return array|mixed|string
     */
    public function getImportedParcelTrackingInfo($tracking_number)
    {
        $response = $this->request(ServiceConstant::URL_IMPORTED_PARCEL_HISTORY, ['tracking_number' => $tracking_number], self::HTTP_GET);
        return $this->decodeResponse($response);
    }

    /**
     * Process tracking history
     * @author Adeyemi Olaoye <yemi@cottacush.com>
     * @param $history
     * @return array
     */
    public static function processHistory($history)
    {
        $connectedTerminalStatuses = [ServiceConstant::FOR_ARRIVAL, ServiceConstant::FOR_DELIVERY, ServiceConstant::BEING_DELIVERED];
        $unConnectedTerminalStatuses = [ServiceConstant::FOR_SWEEPER];
        $transitionalStatuses = [ServiceConstant::IN_TRANSIT];

        $processedHistory = [];

        foreach ($history as $his) {
            if (in_array($his['status'], $connectedTerminalStatuses)) {
                $his['type'] = 'connected_terminal';
            }

            if (in_array($his['status'], $unConnectedTerminalStatuses)) {
                $his['type'] = 'unconnected_terminal';
            }

            if (in_array($his['status'], $transitionalStatuses)) {
                $his['type'] = 'transitional';
            }
            $processedHistory[$his['from_branch']['id'] . '-' . $his['to_branch']['id']] = $his;
        }

        return array_values($processedHistory);
    }

}