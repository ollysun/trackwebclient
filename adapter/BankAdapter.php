<?php
namespace Adapter;


use Adapter\BaseAdapter;
use Adapter\Globals\ServiceConstant;
class BankAdapter extends BaseAdapter{


    public function createBankAccount($owner_id,$owner_type,$bank_id,$account_name,$account_no,$sort_code){
        return $this->request(ServiceConstant::URL_ADD_PARCEL,array(
            'owner_id' => $owner_id,
            'owner_type' => $owner_type,
            'bank_id' => $bank_id,
            'account_name' => $account_name,
            'account_no' => $account_no,
            'sort_code' => $sort_code
        ),self::HTTP_POST);

        /*
         * $owner_id = $this->request->getPost('owner_id');
        $owner_type = $this->request->getPost('owner_type');
        $bank_id = $this->request->getPost('bank_id');
        $account_name = $this->request->getPost('account_name');
        $account_no = $this->request->getPost('account_no');
        $sort_code = $this->request->getPost('sort_code');
         */
    }
    public function getBank($id){
        return $this->request(ServiceConstant::URL_GET_ONE_PARCEL,array('id'=>$id),self::HTTP_GET);
    }
    public function getOneParcelBySender($id){
        return $this->request(ServiceConstant::URL_GET_ONE_PARCEL,array('id'=>$id),self::HTTP_GET);
        }
    public function createBank($sender,$receiver,$sender_address,$receiver_address,$bank_account,$parcel){
        return $this->request(ServiceConstant::URL_ADD_PARCEL,array(
            'sender' => $sender,
            'receiver' => $receiver,
            'sender_address' => $sender_address,
            'receiver_address' => $receiver_address,
            'bank_account' => $bank_account,
            'parcel' => $parcel
        ),self::HTTP_POST);
    }
}