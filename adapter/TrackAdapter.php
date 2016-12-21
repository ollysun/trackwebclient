<?php
/**
 * Created by PhpStorm.
 * User: Icreatechub
 * Date: 7/30/2015
 * Time: 8:50 PM
 */

namespace Adapter;


use Adapter\Globals\ServiceConstant;
use Adapter\Util\Calypso;

/**
 * Class TrackAdapter
 * @author Adeyemi Olaoye <yemi@cottacush.com>
 * @package Adapter
 */
class TrackAdapter extends BaseAdapter
{
    const TRACKPLUS = 0;
    const KANGAROO = 1;
    const ARAMEX = 2;
    const UNIVERSAL = 3;

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
        $response = $this->request(ServiceConstant::URL_PARCEL_HISTORY, ['imported_parcel' => 1, 'tracking_number' => $tracking_number], self::HTTP_GET);
        return $this->decodeResponse($response);
    }

    public function isExportedParcel($waybill_number){
        $parcelAdapter = new ParcelAdapter();
        $response = $parcelAdapter->getOneParcel($waybill_number);
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


    public static function getHistoryProvider($waybill_number){
        if(preg_match('/^\d[A-Z](\d|\-)+[\d]$/i', $waybill_number)) return self::TRACKPLUS;
        if(strlen($waybill_number) == 5 || strlen($waybill_number) == 6) return self::KANGAROO;
        if(preg_match('/^[0-9]{10}$/', $waybill_number)) return self::ARAMEX;

        return self::TRACKPLUS;
    }



    public function trackAramex($tracking_numbers){
        $soapClient = new \SoapClient('http://ws.aramex.net/shippingapi/tracking/service_1_0.svc?WSDL');
        /*echo '<pre>';
        // shows the methods coming from the service
        print_r($soapClient->__getFunctions());*/

        /*
            parameters needed for the trackShipments method , client info, Transaction, and Shipments' Numbers.
            Note: Shipments array can be more than one shipment.
        */
        $params = array(
            'ClientInfo'  			=> array(
                'AccountCountryCode'	=> 'NG',
                'AccountEntity'		 	=> 'LOS',
                'AccountNumber'		 	=> '118602',
                'AccountPin'		 	=> '543643',
                'UserName'			 	=> 'itsupport@courierplus-ng.com',
                'Password'			 	=> 'Courierplus1',
                'Version'			 	=> 'v1.0'
            ),

            'Transaction' 			=> array(
                'Reference1'			=> '001'
            ),
            'Shipments'				=> [$tracking_numbers]
        );

        // calling the method and printing results
        try {
            $result = $soapClient->TrackShipments($params);
            $histories = Calypso::getValue($result, 'TrackingResults.KeyValueOfstringArrayOfTrackingResultmFAkxlpY.Value.TrackingResult');
            return $histories;
        } catch (\SoapFault $fault) {
            return false;
        }
    }

    public function trackUniversal($tracking_number){
        $soapClient = new \SoapClient('http://www.wsdl.integraontrack.com/ServiceUniversal.asmx?WSDL');
        echo '<pre>';
        // shows the methods coming from the service
        print_r($soapClient->__getFunctions());

        /*
            parameters needed for the trackShipments method , client info, Transaction, and Shipments' Numbers.
            Note: Shipments array can be more than one shipment.
        */
        $params = array(
            'argAwbNo'				=> $tracking_number
        );

        // calling the method and printing results
        try {
            $auth_call = $soapClient->TrackDetailsList($params);
            print_r($auth_call);
        } catch (\SoapFault $fault) {
            die('Error : ' . $fault->faultstring);
        }
    }

}