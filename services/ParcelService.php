<?php
/**
 * Created by PhpStorm.
 * User: RotelandO
 * Date: 7/12/15
 * Time: 1:15 PM
 */

namespace app\services;

use Adapter\BankAdapter;
use Adapter\Globals\ServiceConstant;
use Adapter\ParcelAdapter;
use Adapter\RegionAdapter;
use Adapter\RequestHelper;
use Adapter\ResponseHandler;
use Adapter\Util\Calypso;

class ParcelService {

    public static function getParcelDetails($id)
    {
        $cloneParcels = [];
        $data = [];
        $parcel = new ParcelAdapter(RequestHelper::getClientID(),RequestHelper::getAccessToken());
        $response = $parcel->getOneParcel($id);
        $response = new ResponseHandler($response);
        if($response->getStatus() == ResponseHandler::STATUS_OK){
            $data = $response->getData();
            $cloneParcels['info'] = $data;
            if (isset($data['sender_address']) && isset($data['sender_address']['city_id'])) {
                $city_id = $data['sender_address']['city_id'];
                $regionAdp = new RegionAdapter(RequestHelper::getClientID(),RequestHelper::getAccessToken());
                $sender_location = $regionAdp->getCity($city_id);
                if($sender_location['status'] === ResponseHandler::STATUS_OK) {
                    $cloneParcels['sender_location'] = $sender_location['data'];
                } else {
                    $cloneParcels['sender_location'] = [];
                }
            }
            if (isset($data['receiver_address']) && isset($data['receiver_address']['city_id'])) {
                $city_id = $data['receiver_address']['city_id'];
                $regionAdp = new RegionAdapter(RequestHelper::getClientID(),RequestHelper::getAccessToken());
                $receiver_location = $regionAdp->getCity($city_id);
                if($receiver_location['status'] === ResponseHandler::STATUS_OK) {
                    $cloneParcels['receiver_location'] = $receiver_location['data'];
                } else {
                    $cloneParcels['receiver_location'] = [];
                }
            }
            $bankAdapter = new BankAdapter(RequestHelper::getClientID(),RequestHelper::getAccessToken());
            $bankInfo = $bankAdapter->getSenderBankAccout($data['sender']['id']);
            if ($bankInfo['status'] === ResponseHandler::STATUS_OK) {
                if(!empty($bankInfo['data'])) {
                    $sender_merchant = $bankInfo['data']['0'];
                    $cloneParcels['sender_merchant'] = $sender_merchant;
                }
            }
        }

        return $cloneParcels;
    }

    /**
     * Convert a pickup request to parcel
     * @author Adegoke Obasa <goke@cottacush.com>
     * @param $pickupRequest
     * @return array
     */
    public static function convertPickupRequest($pickupRequest)
    {
        /**
         * Pickup Request Mapping*
        `pickup_name` - Sender Firstname
        `pickup_address` - Sender Address
        `pickup_phone_number` - Sender Phone
        `pickup_state_id`- Sender State
        `pickup_city_id` - Sender City
        `destination_name` - Receiver Firstname
        `destination_address` -  Receiver Address
        `destination_phone_number` - Receiver Phone Number
        `destination_state_id` - Receiver State
        `destination_city_id` - Receiver City
        `shipment_description` - Parcel Description
        `request_detail` - ??
         */
        $parcel = [];
        $parcel['pickup_request_id'] = Calypso::getValue($pickupRequest, 'id');
        $parcel['info']['sender']['firstname'] = Calypso::getValue($pickupRequest, 'pickup_name');
        $parcel['info']['sender']['phone'] = Calypso::getValue($pickupRequest, 'pickup_phone_number');
        $parcel['sender_location']['country']['id'] = ServiceConstant::COUNTRY_NIGERIA;
        $parcel['sender_location']['state']['id'] = Calypso::getValue($pickupRequest, 'pickup_state_id');
        $parcel['sender_location']['id'] = Calypso::getValue($pickupRequest, 'pickup_city_id');
        $parcel['info']['sender_address']['street_address1'] = Calypso::getValue($pickupRequest, 'pickup_address');
        $parcel['info']['receiver']['firstname'] = Calypso::getValue($pickupRequest, 'destination_name');
        $parcel['info']['receiver']['phone'] = Calypso::getValue($pickupRequest, 'destination_phone_number');
        $parcel['receiver_location']['country']['id'] = ServiceConstant::COUNTRY_NIGERIA;
        $parcel['receiver_location']['state']['id'] = Calypso::getValue($pickupRequest, 'destination_state_id');
        $parcel['receiver_location']['id'] = Calypso::getValue($pickupRequest, 'destination_city_id');
        $parcel['info']['receiver_address']['street_address1'] = Calypso::getValue($pickupRequest, 'destination_address');
        $parcel['info']['other_info'] = Calypso::getValue($pickupRequest, 'shipment_description');

        return $parcel;
    }

    /**
     * Converts a shipment request to a parcel
     * @author Adegoke Obasa <goke@cottacush.com>
     * @param $shipmentRequest
     * @return array
     */
    public static function convertShipmentRequest($shipmentRequest)
    {
        /**
         * Shipment Request Mapping
        `receiver_firstname` - Receiver Firstname
        `receiver_lastname` - Receiver Lastname
        `receiver_phone_number` - Receiver Phone number
        `receiver_email` - Receiver email
        `receiver_address` - Receiver address
        `receiver_state_id` - Receiver State
        `receiver_city_id` - Receiver City
        `receiver_company_name` - Add in bracket to parcel description
        `company.name` - Sender Name
        `company.email` - Sender Email
        `company.primary_contact.phone_number` - Sender Phone number
        `cash_on_delivery` - Cash On Delivery
        `reference_number` - Reference Number
        `estimated_weight` - Total Weight
        `no_of_packages` - No of packages
        `parcel_value` - Parcel Value
        `description`  - Parcel Description
         */
        $parcel = [];
        $parcel['shipment_request_id'] = Calypso::getValue($shipmentRequest, 'id');
        $parcel['info']['sender']['firstname'] = Calypso::getValue($shipmentRequest, 'company.name');
        $parcel['info']['sender']['phone'] = Calypso::getValue($shipmentRequest, 'company.phone_number');
        $parcel['sender_location']['country']['id'] = ServiceConstant::COUNTRY_NIGERIA;
        $parcel['info']['sender_address']['street_address1'] = Calypso::getValue($shipmentRequest, 'company.address');
        $parcel['sender_location']['id'] = Calypso::getValue($shipmentRequest, 'company.city_id');
        $parcel['sender_location']['state']['id'] = Calypso::getValue($shipmentRequest, 'company_city.state_id');

        $parcel['info']['receiver']['firstname'] = Calypso::getValue($shipmentRequest, 'receiver_firstname');
        $parcel['info']['receiver']['lastname'] = Calypso::getValue($shipmentRequest, 'receiver_lastname');
        $parcel['info']['receiver']['phone'] = Calypso::getValue($shipmentRequest, 'receiver_phone_number');
        $parcel['receiver_location']['country']['id'] = Calypso::getValue($shipmentRequest, 'receiver_state.country_id');
        $parcel['receiver_location']['state']['id'] = Calypso::getValue($shipmentRequest, 'receiver_state_id');
        $parcel['receiver_location']['id'] = Calypso::getValue($shipmentRequest, 'receiver_city_id');
        $parcel['info']['receiver_address']['street_address1'] = Calypso::getValue($shipmentRequest, 'receiver_address');

        $other_info = Calypso::getValue($shipmentRequest, 'description') . ' (' . Calypso::getValue($shipmentRequest, 'receiver_company_name', '') . ')';
        $parcel['info']['other_info'] = $other_info;
        $parcel['info']['package_value'] = Calypso::getValue($shipmentRequest, 'parcel_value');
        $parcel['info']['no_of_package'] = Calypso::getValue($shipmentRequest, 'no_of_packages');
        $parcel['info']['reference_number'] = Calypso::getValue($shipmentRequest, 'reference_number');
        $parcel['info']['weight'] = Calypso::getValue($shipmentRequest, 'estimated_weight');
        $parcel['info']['cash_on_delivery'] = Calypso::getValue($shipmentRequest, 'cash_on_delivery');

        return $parcel;
    }

    public function buildPostData($data) {

        $error = [];
        $senderInfo = [];
        $senderAddress = [];
        $receiverInfo = [];
        $receiverAddress = [];
        $bankData = [];
        $parcel = [];
        $payload = [];

        $senderInfo['firstname'] = Calypso::getValue($data, 'firstname.shipper');
        $senderInfo['lastname'] = Calypso::getDisplayValue($data, 'lastname.shipper', '');
        $senderInfo['phone'] = Calypso::getValue($data, 'phone.shipper');
        $senderInfo['email'] = Calypso::getDisplayValue($data, 'email.shipper', '');

        $senderAddress['id'] = Calypso::getValue($data, 'address.shipper.id');
        $senderAddress['street1'] = Calypso::getValue($data, 'address.shipper.0');
        $senderAddress['street2'] = Calypso::getValue($data, 'address.shipper.1');
        $senderAddress['city_id'] = Calypso::getValue($data, 'city.shipper');
        $senderAddress['state_id'] = Calypso::getValue($data, 'state.shipper');
        $senderAddress['country_id'] = Calypso::getValue($data, 'country.shipper');

        $receiverInfo['firstname'] = Calypso::getValue($data, 'firstname.receiver');
        $receiverInfo['lastname'] = Calypso::getDisplayValue($data, 'lastname.receiver', '');
        $receiverInfo['phone'] = Calypso::getDisplayValue($data, 'phone.receiver', 'N/A');
        $receiverInfo['email'] = Calypso::getDisplayValue($data, 'email.receiver', '');

        $receiverAddress['id'] = Calypso::getValue($data, 'address.receiver.id');
        $receiverAddress['street1'] = Calypso::getValue($data, 'address.receiver.0');
        $receiverAddress['street2'] = Calypso::getValue($data, 'address.receiver.1');
        $receiverAddress['city_id'] = Calypso::getValue($data, 'city.receiver');
        $receiverAddress['state_id'] = Calypso::getValue($data, 'state.receiver');
        $receiverAddress['country_id'] = Calypso::getValue($data, 'country.receiver');

        $bankData = null;

        // Add Merchant Order Number
        $parcel['reference_number'] = Calypso::getValue($data, 'reference_number', null);

        $parcel['parcel_type'] = Calypso::getValue($data, 'parcel_type');
        $parcel['no_of_package'] = Calypso::getValue($data, 'no_of_packages');
        if(!is_numeric($parcel['no_of_package'])) {
            $error[] = "Number of packages must be an integer";
        }
        $parcel['weight'] = Calypso::getValue($data, 'parcel_weight');
        if(!isset($parcel['weight']) || !is_numeric($parcel['weight'])) {
            $error[] = "Weight cannot be empty and must be numeric";
        }

        $parcel['billing_method'] = Calypso::getValue($data, 'billing_method', 'auto');
        $parcel['package_value'] = Calypso::getValue($data, 'parcel_value',0);

        // Manual Billing Amount
        $parcel['amount_due'] = Calypso::getValue($data, 'amount');
        $manualAmount = Calypso::getValue($data, 'manual_amount');
        $parcel['amount_due'] = $parcel['billing_method'] == 'manual' ? $manualAmount : $parcel['amount_due'] ;
        $parcel['is_billing_overridden'] = $parcel['billing_method'] == 'manual' ? 1 : 0;
        if(is_null($parcel['amount_due'])) {
            $error[] = "Amount must be calculated. Please ensure all zone billing and mapping are set.";
        }
        $parcel['cash_on_delivery'] = ($data['cash_on_delivery'] === 'true') ? 1 : 0;
        $parcel['cash_on_delivery_amount'] = Calypso::getValue($data, 'CODAmount');
        $parcel['delivery_type'] = Calypso::getValue($data, 'delivery_type');
        $parcel['payment_type'] = Calypso::getValue($data, 'payment_method');
        $parcel['shipping_type'] = Calypso::getValue($data, 'shipping_type');
        $parcel['other_info'] = Calypso::getDisplayValue($data, 'other_info', '');
        $parcel['cash_amount'] = Calypso::getValue($data, 'amount_in_cash', null);
        $parcel['pos_amount'] = Calypso::getValue($data, 'amount_in_pos', null);
        $parcel['pos_trans_id'] = Calypso::getValue($data, 'pos_transaction_id', null);

        /**
         * Set Pickup Request Id
         */
        if(isset($data['pickup_request_id'])) {
            $payload['pickup_request_id'] = Calypso::getValue($data, 'pickup_request_id', null);
        }

        /**
         * Set Shipment Request Id
         */
        if(isset($data['shipment_request_id'])) {
            $payload['shipment_request_id'] = Calypso::getValue($data, 'shipment_request_id', null);
        }

        if($parcel['payment_type'] == '3' && (!is_null($parcel['cash_amount']) && !is_null($parcel['pos_amount']))) {
            $cash_amount = (int) $parcel['cash_amount'];
            $pos_amount = (int) $parcel['pos_amount'];
            $amount_due = (int) $parcel['amount_due'];
            if($cash_amount + $pos_amount !== $amount_due) {
                $error[] = "POS and cash amount must sum up to the amount due.";
            }
        }
        $parcel['request_type'] = (Calypso::getValue($data, 'merchant') === 'yes') ? ServiceConstant::REQUEST_ECOMMERCE : ServiceConstant::REQUEST_OTHERS;

        $payload['sender'] = $senderInfo;
        $payload['receiver'] = $receiverInfo;
        $payload['sender_address'] = $senderAddress;
        $payload['receiver_address'] = $receiverAddress;
        $payload['parcel'] = $parcel;
        $payload['bank_account'] = $bankData;
        $payload['to_branch_id'] = Calypso::getValue($data, 'to_branch_id');
        $payload['is_corporate_lead'] = (Calypso::getValue($data, 'corporate_lead') === 'true') ? 1 : 0;
        $payload['to_hub'] = (Calypso::getValue($data, 'send_to_hub') === '1') ? 1 : 0;

        if(!empty($error)) {
            return [ 'status' => false, 'messages' => $error ];
        }

        return $payload;
    }

    public function buildBillingCalculationData($data) {

        $response['payload']['from_branch_id'] = $data['from_branch_id'];
        $response['payload']['to_branch_id'] = $data['to_branch_id'];
        $response['payload']['onforwarding_charge_id'] = $data['charge_id'];
        $response['payload']['weight'] = $data['weight'];
        return $response;
    }
}