<?php
namespace Adapter;
use Adapter\BaseAdapter;
use Adapter\Globals;
use Adapter\Globals\ServiceConstant;

class ParcelAdapter extends BaseAdapter{


    public function createNewParcel($sender,$receiver,$sender_address,$receiver_address,$bank_account,$parcel){
        return $this->request(ServiceConstant::URL_ADD_PARCEL,array(
            'sender' => $sender,
            'receiver' => $receiver,
            'sender_address' => $sender_address,
            'receiver_address' => $receiver_address,
            'bank_account' => $bank_account,
            'parcel' => $parcel
        ),self::HTTP_POST);
    }
    public function getOneParcel($id){
        return $this->request(ServiceConstant::URL_GET_ONE_PARCEL,array('id'=>$id),self::HTTP_GET);
    }
    public function getOneParcelBySender($id){
        return $this->request(ServiceConstant::URL_GET_ONE_PARCEL,array('id'=>$id),self::HTTP_GET);
    }
    public function getParcels($type=null){
        return $this->request(ServiceConstant::URL_GET_ALL_PARCEL.'?with_sender=1&with_receiver=1&with_receiver_address=1',array(),self::HTTP_GET);
        //return $this->request(ServiceConstant::URL_GET_ALL_PARCEL.'?shipping_type=1',array(),self::HTTP_GET);
    }
}