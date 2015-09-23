<?php
/**
 * Created by PhpStorm.
 * User: RotelandO
 * Date: 7/12/15
 * Time: 1:15 PM
 */

namespace app\services;

use Adapter\BankAdapter;
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
        $senderInfo['lastname'] = Calypso::getDisplayValue($data, 'lastname.shipper', 'N/A');
        $senderInfo['phone'] = Calypso::getValue($data, 'phone.shipper');
        $senderInfo['email'] = Calypso::getDisplayValue($data, 'email.shipper', 'N/A');

        $senderAddress['id'] = Calypso::getValue($data, 'address.shipper.id');
        $senderAddress['street1'] = Calypso::getValue($data, 'address.shipper.0');
        $senderAddress['street2'] = Calypso::getValue($data, 'address.shipper.1');
        $senderAddress['city_id'] = Calypso::getValue($data, 'city.shipper');
        $senderAddress['state_id'] = Calypso::getValue($data, 'state.shipper');
        $senderAddress['country_id'] = Calypso::getValue($data, 'country.shipper');

        $receiverInfo['firstname'] = Calypso::getValue($data, 'firstname.receiver');
        $receiverInfo['lastname'] = Calypso::getDisplayValue($data, 'lastname.receiver', 'N/A');
        $receiverInfo['phone'] = Calypso::getDisplayValue($data, 'phone.receiver', 'N/A');
        $receiverInfo['email'] = Calypso::getDisplayValue($data, 'email.receiver', 'N/A');

        $receiverAddress['id'] = Calypso::getValue($data, 'address.receiver.id');
        $receiverAddress['street1'] = Calypso::getValue($data, 'address.receiver.0');
        $receiverAddress['street2'] = Calypso::getValue($data, 'address.receiver.1');
        $receiverAddress['city_id'] = Calypso::getValue($data, 'city.receiver');
        $receiverAddress['state_id'] = Calypso::getValue($data, 'state.receiver');
        $receiverAddress['country_id'] = Calypso::getValue($data, 'country.receiver');

        $bankData['id'] = Calypso::getValue($data, 'account_id', null);
        $bankData['account_name'] = Calypso::getValue($data, 'account_name');
        $bankData['account_no'] = Calypso::getValue($data, 'account_no');
        $bankData['bank_id'] = Calypso::getValue($data, 'bank');
        $bankData['sort_code'] = Calypso::getValue($data, 'sort_code');

        $oldAccount = Calypso::getValue($data, 'merchant', null);
        if($oldAccount !== 'none') {
            if (empty($bankData['account_name']) || empty($bankData['bank_id']) || empty($bankData['account_name'])) {
                $error[] = "All Account Details are required!";
            }

        } else {
            $bankData = null;
        }

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
        if(!$parcel['amount_due'] && !$manualAmount) {
            $error[] = "Amount must be calculated. Please ensure all zone billing and mapping are set.";
        }
        $parcel['cash_on_delivery'] = ($data['cash_on_delivery'] === 'true') ? 1 : 0;
        $parcel['cash_on_delivery_amount'] = Calypso::getValue($data, 'CODAmount');
        $parcel['delivery_type'] = Calypso::getValue($data, 'delivery_type');
        $parcel['payment_type'] = Calypso::getValue($data, 'payment_method');
        $parcel['shipping_type'] = Calypso::getValue($data, 'shipping_type');
        $parcel['other_info'] = Calypso::getDisplayValue($data, 'other_info', 'N/A');
        $parcel['cash_amount'] = Calypso::getValue($data, 'amount_in_cash', null);
        $parcel['pos_amount'] = Calypso::getValue($data, 'amount_in_pos', null);
        $parcel['pos_trans_id'] = Calypso::getValue($data, 'pos_transaction_id', null);

        if($parcel['payment_type'] == '3' && (!is_null($parcel['cash_amount']) && !is_null($parcel['pos_amount']))) {
            $cash_amount = (int) $parcel['cash_amount'];
            $pos_amount = (int) $parcel['pos_amount'];
            $amount_due = (int) $parcel['amount_due'];
            if($cash_amount + $pos_amount !== $amount_due) {
                $error[] = "POS and cash amount must sum up to the amount due.";
            }
        }

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