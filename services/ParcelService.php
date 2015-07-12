<?php
/**
 * Created by PhpStorm.
 * User: RotelandO
 * Date: 7/12/15
 * Time: 1:15 PM
 */

namespace app\services;

use Adapter\Util\Calypso;

class ParcelService {

    public function buildPostData($data) {

        $senderInfo = [];
        $senderAddress = [];
        $receiverInfo = [];
        $receiverAddress = [];
        $bankData = [];
        $parcel = [];
        $payload = [];
        $senderInfo['firstname'] = Calypso::getValue($data, 'firstname.shipper');
        $senderInfo['lastname'] = Calypso::getValue($data, 'lastname.shipper');
        $senderInfo['phone'] = Calypso::getValue($data, 'phone.shipper');
        $senderInfo['email'] = Calypso::getValue($data, 'email.shipper');

        $senderAddress['id'] = Calypso::getValue($data, 'address.shipper.id');
        $senderAddress['street1'] = Calypso::getValue($data, 'address.shipper.0');
        $senderAddress['street2'] = Calypso::getValue($data, 'address.shipper.1');
        $senderAddress['city'] = Calypso::getValue($data, 'city.shipper');
        $senderAddress['state_id'] = Calypso::getValue($data, 'state.shipper');
        $senderAddress['country_id'] = Calypso::getValue($data, 'country.shipper');

        $receiverInfo['firstname'] = Calypso::getValue($data, 'firstname.receiver');
        $receiverInfo['lastname'] = Calypso::getValue($data, 'lastname.receiver');
        $receiverInfo['phone'] = Calypso::getValue($data, 'phone.receiver');
        $receiverInfo['email'] = Calypso::getValue($data, 'email.receiver');

        $receiverAddress['id'] = Calypso::getValue($data, 'address.receiver.id');
        $receiverAddress['street1'] = Calypso::getValue($data, 'address.receiver.0');
        $receiverAddress['street2'] = Calypso::getValue($data, 'address.receiver.1');
        $receiverAddress['city'] = Calypso::getValue($data, 'city.receiver');
        $receiverAddress['state_id'] = Calypso::getValue($data, 'state.receiver');
        $receiverAddress['country_id'] = Calypso::getValue($data, 'country.receiver');

        $bankData['id'] = Calypso::getValue($data, 'account.id');
        $bankData['account_name'] = Calypso::getValue($data, 'account_name');
        $bankData['bank_id'] = Calypso::getValue($data, 'account_name');
        $bankData['account_no'] = Calypso::getValue($data, 'account_name');
        $bankData['sort_code'] = Calypso::getValue($data, 'sort_code');

        $parcel['to_hub'] = ($data['send_to_hub'] === 'true') ? 1 : 0;
        $parcel['parcel_type'] = Calypso::getValue($data, 'parcel_type');
        $parcel['no_of_package'] = Calypso::getValue($data, 'no_of_packages');
        $parcel['weight'] = Calypso::getValue($data, 'parcel_weight');
        $parcel['parcel_value'] = Calypso::getValue($data, 'parcel_value');
        //@Todo To be calculated by the settings in the backend
        $parcel['amount_due'] = Calypso::getValue($data, 'parcel_value');

        $parcel['cash_on_delivery'] = ($data['cash_on_delivery'] === 'true') ? 1 : 0;
        $parcel['cash_on_delivery_amount'] = Calypso::getValue($data, 'CODAmount');
        $parcel['delivery_type'] = Calypso::getValue($data, 'delivery_type');
        $parcel['payment_type'] = Calypso::getValue($data, 'payment_method');
        $parcel['shipping_type'] = Calypso::getValue($data, 'shipping_type');
        $parcel['other_info'] = Calypso::getValue($data, 'other_info');
        $parcel['cash_amount'] = Calypso::getValue($data, 'cash_amount');
        $parcel['pos_amount'] = Calypso::getValue($data, 'pos_amount');

        $payload['sender'] = $senderInfo;
        $payload['receiver'] = $receiverInfo;
        $payload['sender_address'] = $receiverAddress;
        $payload['receiver_address'] = $senderAddress;
        $payload['parcel'] = $parcel;

        $payload['is_corporate_lead'] = Calypso::getValue($data, 'corporate_lead');

        return $payload;
    }
}