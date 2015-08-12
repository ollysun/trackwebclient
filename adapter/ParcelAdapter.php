<?php
namespace Adapter;
use Adapter\BaseAdapter;
use Adapter\Globals;
use Adapter\Globals\ServiceConstant;

class ParcelAdapter extends BaseAdapter{

    public function createNewParcel($postData){
        return $this->request(ServiceConstant::URL_ADD_PARCEL, $postData, self::HTTP_POST);
    }
    public function getOneParcel($id){
        return $this->request(ServiceConstant::URL_GET_ONE_PARCEL,array('id'=>$id),self::HTTP_GET);
    }
    public function getParcel($staff_id,$status){
        return $this->request(ServiceConstant::URL_GET_ALL_PARCEL,array('held_by_staff_id'=>$staff_id,'status'=>$status),self::HTTP_GET);
    }
    public function getOneParcelBySender($id){
        return $this->request(ServiceConstant::URL_GET_ONE_PARCEL,array('id'=>$id),self::HTTP_GET);
    }
    public function getParcels($start_created_date,$end_created_date,$status,$branch_id=null,$offset=0, $count=50, $with_from=null, $with_total=null){
        $filter = !is_null($status) ? '&status='.$status : '';
        $filter .= !is_null($start_created_date) ? '&start_created_date='.$start_created_date : '';
        $filter .= !is_null($end_created_date) ? '&end_created_date='.$end_created_date : '';
        $filter .= !is_null($branch_id) ? '&branch_id='.$branch_id : '';
        $filter .= !is_null($with_from) ? '&with_from_branch=1' : '';
        $filter .= !is_null($with_total) ? '&with_total_count=1' : '';
        return $this->request(ServiceConstant::URL_GET_ALL_PARCEL.'?with_sender=1&with_receiver=1&with_receiver_address=1&with_to_branch=1&offset='.$offset.'&count='.$count.$filter,array(),self::HTTP_GET);
    }

    public function getParcelsForNextDestination($type=null,$branch_id=null, $to_branch_id=null, $held_by_id=null, $offset=0, $count=50){
        $filter = ($type != null ? '&status='.$type:'');
        $filter .= ($branch_id == null ? '':'&branch_id='.$branch_id);
        $filter .= ($to_branch_id == null ? '':'&to_branch_id='.$to_branch_id);
        $filter .= ($held_by_id == null ? '':'&held_by_id='.$held_by_id);
        return $this->request(ServiceConstant::URL_GET_ALL_PARCEL.'?with_to_branch=1&with_sender_address=1&with_receiver_address=1&offset='.$offset.'&count='.$count.$filter,array(),self::HTTP_GET);
    }
    public function getSearchParcels($status,$waybill_number,$offset=0, $count=50){
        $parcel_status = $status == '-1'?'': '&status='.$status;
        $filter = $parcel_status.'&waybill_number='.$waybill_number;
        return $this->request(ServiceConstant::URL_GET_ALL_PARCEL.'?with_sender=1&with_receiver=1&with_receiver_address=1&with_to_branch=1&offset='.$offset.'&count='.$count.$filter,array(),self::HTTP_GET);

    }
    public function getFilterParcelsByDateAndStatus($start_created_date,$end_created_date,$status,$offset=0, $count=50){
        $parcel_status = $status == '-1'?'': '&status='.$status;
        $filter = $parcel_status.'&start_created_date='.$start_created_date;
        $filter .= $parcel_status.'&end_created_date='.$end_created_date;
        return $this->request(ServiceConstant::URL_GET_ALL_PARCEL.'?with_sender=1&with_receiver=1&with_receiver_address=1&with_to_branch=1&offset='.$offset.'&count='.$count.$filter,array(),self::HTTP_GET);

    }
    public function getNewParcelsByDate($start_created_date,$offset=0, $count=50){
        $filter = '&start_created_date='.$start_created_date;
        return $this->request(ServiceConstant::URL_GET_ALL_PARCEL.'?with_sender=1&with_receiver=1&with_receiver_address=1&offset='.$offset.'&count='.$count.$filter,array(),self::HTTP_GET);
    }

    public function getDispatchedParcels($branch_id,$to_branch=null,$start_created_date=null,$end_created_date=null,$status='-1'){
        $filter = "branch_id={$branch_id}&with_to_branch=1&with_from_branch=1&with_holder=1";
        $filter .= ($to_branch == null ? '':'&to_branch_id='.$to_branch);
        $filter .= ($start_created_date == null ? '':'&start_created_date='.$start_created_date);
        $filter .= ($end_created_date == null ? '':'&end_created_date='.$end_created_date);

        return $this->request(ServiceConstant::URL_GET_ALL_PARCEL.'?'.$filter, array(), self::HTTP_POST);
    }

    public function moveToForSweeper($postData) {
        return $this->request(ServiceConstant::URL_MOVE_TO_FOR_SWEEPER, $postData, self::HTTP_POST);
    }

    public function generateManifest($postData) {
        return $this->request(ServiceConstant::URL_MOVE_TO_IN_TRANSIT, $postData, self::HTTP_POST);
    }
    public function moveToArrival($postData) {
        return $this->request(ServiceConstant::URL_MOVE_TO_ARRIVAL, $postData, self::HTTP_POST);
    }
    public function moveForDelivery($postData) {
        return $this->request(ServiceConstant::URL_MOVE_FOR_DELIVERY, $postData, self::HTTP_POST);
    }
    public function moveToBeingDelivered($postData) {
        return $this->request(ServiceConstant::URL_MOVE_TO_BEING_DELIVERED, $postData, self::HTTP_POST);
    }
    public function moveToDelivered($postData) {
        return $this->request(ServiceConstant::URL_MOVE_TO_DELIVERED, $postData, self::HTTP_POST);
    }

    public function getParcelsByPayment($waybill_number=null,$payment_type=null,$start_created_date,$end_created_date,$offset=0, $count=50, $with_total=null){
        $filter = !is_null($waybill_number) ? '&waybill_number='.$waybill_number : '';
        if(is_null($waybill_number)){
            $filter = !is_null($payment_type) ? '&payment_type='.$payment_type : '';
            $filter .= !is_null($start_created_date) ? '&start_created_date='.$start_created_date : '';
            $filter .= !is_null($end_created_date) ? '&end_created_date='.$end_created_date : '';
        }
        $filter .= !is_null($with_total) ? '&with_total_count=1' : '';
        return $this->request(ServiceConstant::URL_GET_ALL_PARCEL.'?with_from_branch=1&offset='.$offset.'&count='.$count.$filter,array(),self::HTTP_GET);
    }
    public function getParcelsByUser($user_id, $start_created_date,$end_created_date,$offset=0, $count=50){
        $filter = !is_null($user_id) ? '&user_id='.$user_id : '';
        $filter .= !is_null($start_created_date) ? '&start_created_date='.$start_created_date : '';
        $filter .= !is_null($end_created_date) ? '&end_created_date='.$end_created_date : '';
        $filter .= '&with_total_count=1';
        $filter .= '&order_by=Parcel.created_date%20DESC';
        return $this->request(ServiceConstant::URL_GET_ALL_PARCEL.'?with_sender=1&with_receiver=1&offset='.$offset.'&count='.$count.$filter,array(),self::HTTP_GET);
    }
    public function getECDispatchedParcels($branch_id,$offset=0, $count=50){
        $filter = '&from_branch_id='.$branch_id;
        $filter .= '&with_total_count=1';
        $filter .= '&status='.ServiceConstant::BEING_DELIVERED;
        return $this->request(ServiceConstant::URL_GET_ALL_PARCEL.'?with_receiver=1&with_holder=1&offset='.$offset.'&count='.$count.$filter,array(),self::HTTP_GET);
    }
    public function getDeliveredParcels($branch_id,$offset=0, $count=50){
        $filter = '&from_branch_id='.$branch_id;
        $filter .= '&with_total_count=1';
        $filter .= '&status='.ServiceConstant::DELIVERED;
        return $this->request(ServiceConstant::URL_GET_ALL_PARCEL.'?with_receiver=1&with_sender=1&offset='.$offset.'&count='.$count.$filter,array(),self::HTTP_GET);
    }

    public function calcBilling($postData) {
        return $this->request(ServiceConstant::URL_CALC_BILLING, $postData, self::HTTP_POST);
    }
}