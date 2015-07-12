<?php
/**
 * Created by PhpStorm.
 * User: RotelandO
 * Date: 7/12/15
 * Time: 1:15 PM
 */

namespace Service;

class ParcelService {

    public function buildPostData($data) {

        $senderData = [];
        $receiverData = [];
        $addressData = [];
        $bankData = [];

        $payload = [];
        $senderData['firstname'] = $data['firstname']['shipper'];
        $senderData['lastname'] = $data['lastname']['shipper'];
        $senderData['phone'] = $data['phone']['shipper'];
        $senderData['email'] = $data['email']['shipper'];

        $receiverData['firstname'] = $data['firstname']['receiver'];
        $receiverData['lastname'] = $data['lastname']['receiver'];
        $receiverData['phone'] = $data['phone']['receiver'];
        $receiverData['email'] = $data['email']['receiver'];

        $receiverAddressData['id'] = null;
        $receiverAddressData['street1'] = $data['address']['shipper'][0];
        $receiverAddressData['street2'] = $data['address']['shipper'][1];
        $receiverAddressData['city'] = $data['city']['shipper'];
        $receiverAddressData['state_id'] = $data['state']['shipper'];
        $receiverAddressData['country_id'] = $data['country']['shipper'];

        $bankData['account_name'] = $data['account_name'];
        $bankData['bank_id'] = $data['account_name'];
        $bankData['account_no'] = $data['account_name'];
        $bankData['sort_code'] = $data['sort_code'];
        $bankData['id'] = null;



        $payload['sender'] = $senderData;
        $payload['receiver'] = $receiverData;
        $payload['sender_address'] = $addressData;
        $payload['receiver_address'] = $receiverAddressData;
    }
}