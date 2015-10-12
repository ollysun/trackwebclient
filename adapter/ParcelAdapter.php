<?php
namespace Adapter;

use Adapter\Globals;
use Adapter\Globals\ServiceConstant;
use Adapter\Util\Response;

/**
 * Class ParcelAdapter
 * @author Adeyemi Olaoye <yemi@cottacush.com>
 * @author Richard Boyewa <boye@cottacush.com>
 * @author Rotimi Akintewe <akintewe.rotimi@gmail.com>
 * @package Adapter
 */
class ParcelAdapter extends BaseAdapter
{

    /**
     * @author Adeyemi Olaoye <yemi@cottacush.com>
     * @param $waybill_number
     * @return int
     */
    public static function isBag($waybill_number)
    {
        return (preg_match('/^B[\w]+/i', $waybill_number));
    }

    public function createNewParcel($postData)
    {
        return $this->request(ServiceConstant::URL_ADD_PARCEL, $postData, self::HTTP_POST);
    }

    public function getOneParcel($id)
    {
        return $this->request(ServiceConstant::URL_GET_ONE_PARCEL, array('id' => $id), self::HTTP_GET);
    }

    /**
     * @author Adeyemi Olaoye <yemi@cottacush.com>
     * @param $waybill_number
     * @return array|mixed|string
     */
    public function getParcelByWayBillNumber($waybill_number)
    {
        return $this->request(ServiceConstant::URL_GET_ONE_PARCEL, array('waybill_number' => $waybill_number), self::HTTP_GET);
    }

    public function getParcel($staff_id, $status, $branch_id = null)
    {
        $filter = 'held_by_staff_id=' . $staff_id;
        $filter .= '&status=' . $status;
        $filter .= empty($branch_id) ? '' : '&to_branch_id=' . $branch_id;
        return $this->request(ServiceConstant::URL_GET_ALL_PARCEL . '?' . $filter, array(), self::HTTP_GET);
    }
    public function getParcels($start_created_date,$end_created_date,$status,$branch_id=null,$offset=0, $count=50, $with_from=null, $with_total=null, $only_parents=null, $with_created_branch=null){
        $filter = !is_null($status) ? '&status='.$status : '';
        $filter .= !is_null($with_total) ? '&with_total_count=1' : '';
        $filter .= !is_null($start_created_date) ? '&start_created_date=' . $start_created_date : '';
        $filter .= !is_null($end_created_date) ? '&end_created_date=' . $end_created_date : '';
        $filter .= !is_null($branch_id) ? '&from_branch_id=' . $branch_id : '';
        $filter .= !is_null($with_from) ? '&with_from_branch=1' : '';
        $filter .= !is_null($only_parents) ? '&show_parents=1' : '';
        $filter .= !is_null($with_created_branch) ? '&with_created_branch' : '';
        $url = ServiceConstant::URL_GET_ALL_PARCEL.'?with_sender=1&with_receiver=1&with_receiver_address=1&with_to_branch=1&offset='.$offset.'&count='.$count.$filter;
        return $this->request($url,array(),self::HTTP_GET);
    }

    public function getParcelsForDelivery($start_created_date,$end_created_date,$status,$branch_id=null,$offset=0, $count=50, $with_from=null, $with_total=null, $only_parents=null, $route_id=null, $with_route=null){
        $filter = !is_null($status) ? '&status='.$status : '';
        $filter .= !is_null($with_total) ? '&with_total_count=1' : '';
        $filter .= !is_null($start_created_date) ? '&start_created_date=' . $start_created_date : '';
        $filter .= !is_null($end_created_date) ? '&end_created_date=' . $end_created_date : '';
        $filter .= !is_null($branch_id) ? '&to_branch_id=' . $branch_id : '';
        $filter .= !is_null($with_from) ? '&with_from_branch=1' : '';
        $filter .= !is_null($only_parents) ? '&show_parents=1' : '';
        $filter .= !is_null($route_id) ? '&route_id='.$route_id : '';
        $filter .= !is_null($with_route) ? '&with_route' : '';
        $url = ServiceConstant::URL_GET_ALL_PARCEL.'?with_sender=1&with_receiver=1&with_receiver_address=1&with_to_branch=1&offset='.$offset.'&count='.$count.$filter;
        return $this->request($url,array(),self::HTTP_GET);
    }

    public function getParcelsForNextDestination($status = null, $branch_id = null, $to_branch_id = null, $held_by_id = null, $offset = 0, $count = 50, $with_total = null)
    {
        $filter = is_null($status) ? '' : '&status=' . $status;
        $filter .= is_null($branch_id) ? '' : '&from_branch_id=' . $branch_id;
        $filter .= is_null($to_branch_id) ? '' : '&to_branch_id=' . $to_branch_id;
        $filter .= is_null($held_by_id) ? '' : '&held_by_id=' . $held_by_id;
        $filter .= is_null($with_total) ? '' : '&with_total_count=1';
        return $this->request(ServiceConstant::URL_GET_ALL_PARCEL . '?with_to_branch=1&with_city=1&with_sender_address=1&with_receiver_address=1&offset=' . $offset . '&count=' . $count . $filter, array(), self::HTTP_GET);
    }
    public function getSearchParcels($status,$waybill_number,$offset=0, $count=50, $with_total=null,$branch_id=null, $only_parents=null, $with_created_branch=null){
        $parcel_status = $status == '-1'?'': '&status='.$status;
        $filter = $parcel_status.'&waybill_number='.$waybill_number;
        $filter .= !is_null($with_total) ? '&with_total_count=1' : '';
        $filter .= !is_null($branch_id) ? '&branch_id=' . $branch_id : '';
        $filter .= !is_null($only_parents) ? '&show_parents=1' : '';
        $filter .= !is_null($with_created_branch) ? '&with_created_branch' : '';
        return $this->request(ServiceConstant::URL_GET_ALL_PARCEL.'?with_sender=1&with_receiver=1&with_receiver_address=1&with_to_branch=1&offset='.$offset.'&count='.$count.$filter,array(),self::HTTP_GET);
    }
    public function getFilterParcelsByDateAndStatus($start_created_date,$end_created_date,$status,$offset=0, $count=50, $with_total=null,$branch_id=null, $only_parents=null, $with_created_branch=null){
        $parcel_status = $status == '-1'?'': '&status='.$status;
        $filter = !is_null($with_total) ? '&with_total_count=1' : '';
        $filter .= !is_null($only_parents) ? '&show_parents=1' : '';
        $filter .= $parcel_status.'&start_created_date='.$start_created_date;
        $filter .= $parcel_status.'&end_created_date='.$end_created_date;
        $filter .= !is_null($branch_id) ? '&from_branch_id='.$branch_id : '';
        $filter .= !is_null($with_created_branch) ? '&with_created_branch' : '';
        return $this->request(ServiceConstant::URL_GET_ALL_PARCEL.'?with_sender=1&with_receiver=1&with_receiver_address=1&with_to_branch=1&offset='.$offset.'&count='.$count.$filter,array(),self::HTTP_GET);
    }

    public function getNewParcelsByDate($start_created_date, $offset = 0, $count = 500, $with_total = null, $branch_id = null, $only_parents = null)
    {
        $filter = '&start_created_date=' . $start_created_date;
        $filter .= !is_null($with_total) ? '&with_total_count=1' : '';
        $filter .= !is_null($only_parents) ? '&show_parents=1' : '';
        $filter .= !is_null($branch_id) ? '&from_branch_id=' . $branch_id : '';
        return $this->request(ServiceConstant::URL_GET_ALL_PARCEL . '?with_sender=1&with_receiver=1&with_receiver_address=1&offset=' . $offset . '&count=' . $count . $filter, array(), self::HTTP_GET);
    }

    public function getDispatchedParcels($branch_id, $to_branch = null, $start_created_date = null, $end_created_date = null, $status = null)
    {
        $filter = "history_from_branch_id={$branch_id}&with_to_branch=1&with_from_branch=1&with_holder=1";
        $filter .= ($to_branch == null ? '' : '&history_to_branch_id=' . $to_branch);
        $filter .= ($start_created_date == null ? '' : '&history_start_created_date=' . $start_created_date);
        $filter .= ($end_created_date == null ? '' : '&history_end_created_date=' . $end_created_date);
        $filter .= ($status == null ? '' : '&history_status=' . $status);

        return $this->request(ServiceConstant::URL_GET_ALL_PARCEL . '?' . $filter, array(), self::HTTP_POST);
    }

    public function moveToForSweeper($postData)
    {
        return $this->request(ServiceConstant::URL_MOVE_TO_FOR_SWEEPER, $postData, self::HTTP_POST);
    }

    public function assignToGroundsMan($postData)
    {
        return $this->request(ServiceConstant::URL_ASSIGN_TO_GROUNDSMAN, $postData, self::HTTP_POST);
    }

    public function generateManifest($postData)
    {
        return $this->request(ServiceConstant::URL_MOVE_TO_IN_TRANSIT, $postData, self::HTTP_POST);
    }

    public function moveToArrival($postData)
    {
        return $this->request(ServiceConstant::URL_MOVE_TO_ARRIVAL, $postData, self::HTTP_POST);
    }

    public function moveForDelivery($postData)
    {
        return $this->request(ServiceConstant::URL_MOVE_FOR_DELIVERY, $postData, self::HTTP_POST);
    }

    public function moveToBeingDelivered($postData)
    {
        return $this->request(ServiceConstant::URL_MOVE_TO_BEING_DELIVERED, $postData, self::HTTP_POST);
    }

    public function moveToDelivered($postData)
    {
        return $this->request(ServiceConstant::URL_MOVE_TO_DELIVERED, $postData, self::HTTP_POST);
    }

    public function receiveFromBeingDelivered($postData)
    {
        return $this->request(ServiceConstant::URL_RECEIVE_RETURN, $postData, self::HTTP_POST);
    }

    public function getParcelsByPayment($waybill_number = null, $payment_type = null, $start_created_date, $end_created_date, $offset = 0, $count = 50, $with_total = null, $branch_id = null, $only_parents = null)
    {
        $filter = !is_null($waybill_number) ? '&waybill_number=' . $waybill_number : '';
        if (is_null($waybill_number)) {
            $filter = !is_null($payment_type) ? '&payment_type=' . $payment_type : '';
            $filter .= !is_null($start_created_date) ? '&start_created_date=' . $start_created_date : '';
            $filter .= !is_null($end_created_date) ? '&end_created_date=' . $end_created_date : '';
        }
        $filter .= !is_null($with_total) ? '&with_total_count=1' : '';
        $filter .= !is_null($branch_id) ? '&branch_id=' . $branch_id : '';
        $filter .= !is_null($only_parents) ? '&show_parents=1' : '';
        return $this->request(ServiceConstant::URL_GET_ALL_PARCEL . '?with_from_branch=1&offset=' . $offset . '&count=' . $count . $filter, array(), self::HTTP_GET);
    }

    public function getParcelsByUser($user_id, $start_created_date, $end_created_date, $offset = 0, $count = 100)
    {
        $filter = !is_null($user_id) ? '&user_id=' . $user_id : '';
        $filter .= '&with_total_count=1';
        $filter .= !is_null($start_created_date) ? '&start_created_date=' . $start_created_date : '';
        $filter .= !is_null($end_created_date) ? '&end_created_date=' . $end_created_date : '';
        $filter .= '&order_by=Parcel.created_date%20DESC';
        return $this->request(ServiceConstant::URL_GET_ALL_PARCEL . '?with_sender=1&with_receiver=1&offset=' . $offset . '&count=' . $count . $filter, array(), self::HTTP_GET);
    }

    public function getECDispatchedParcels($branch_id, $offset = 0, $count = 50)
    {
        $filter = '&from_branch_id=' . $branch_id;
        $filter .= '&with_total_count=1';
        $filter .= '&status=' . ServiceConstant::BEING_DELIVERED;
        $url = ServiceConstant::URL_GET_ALL_PARCEL . '?with_receiver=1&with_holder=1&offset=' . $offset . '&count=' . $count . $filter;
        return $this->request($url, array(), self::HTTP_GET);
    }

    public function getDeliveredParcels($branch_id, $offset = 0, $count = 50, $start_modified_date = null, $end_modified_date = null)
    {
        $filter = !is_null($branch_id) ? '&branch_id=' . $branch_id : '';
        $filter .= '&with_total_count=1';
        $filter .= '&status=' . ServiceConstant::DELIVERED;
        $filter .= !is_null($start_modified_date) ? '&start_modified_date=' . $start_modified_date : '';
        $filter .= !is_null($end_modified_date) ? '&end_modified_date=' . $end_modified_date : '';
        return $this->request(ServiceConstant::URL_GET_ALL_PARCEL . '?with_receiver=1&with_sender=1&offset=' . $offset . '&count=' . $count . $filter, array(), self::HTTP_GET);
    }

    public function getMerchantParcels($with_bank_account = 1, $payment_status = null, $offset = 0, $count = 50, $with_total = 1, $only_parents = 1)
    {
        $filter = !is_null($with_bank_account) ? '&with_bank_account=1' : '';
        $filter .= !is_null($with_total) ? '&with_total_count=1' : '';
        $filter .= !is_null($only_parents) ? '&show_parents=1' : '';
        $url = ServiceConstant::URL_GET_ALL_PARCEL . '?cash_on_delivery=1&with_sender=1&offset=' . $offset . '&count=' . $count . $filter;
        return $this->request($url, array(), self::HTTP_GET);
    }

    public function calcBilling($postData)
    {
        return $this->request(ServiceConstant::URL_CALC_BILLING, $postData, self::HTTP_POST);
    }

    public function cancel($postData)
    {
        return $this->request(ServiceConstant::URL_CANCEL_PARCEL, $postData, self::HTTP_POST);
    }

    public function createBag($postData)
    {
        return $this->request(ServiceConstant::URL_CREATE_BAG, $postData, self::HTTP_POST);
    }

    /**
     * Get details of a bag
     * @author Adeyemi Olaoye <yemi@cottacush.com>
     * @param $waybill_number
     * @return array|mixed|string
     */
    public function getBag($waybill_number)
    {
        $response = $this->request(ServiceConstant::URL_GET_ONE_PARCEL, array('waybill_number' => $waybill_number, 'with_linked' => var_export(true, true)), self::HTTP_GET);
        $response = new ResponseHandler($response);
        if ($response->getStatus() == Response::STATUS_OK) {
            return $response->getData();
        } else {
            return $response->getError();
        }
    }

    /**
     * Returns the number of parcels meeting a
     * @author Olawale Lawal <wale@cottacush.com>
     * @param $filter_array
     * @return array|mixed|string
     */
    public function getParcelCount($filter_array = null)
    {
        $filter_array = is_null($filter_array) ? [] : $filter_array;
        $url_params = [];
        $filter_array= array_filter($filter_array);
        $filters = '?'.http_build_query($filter_array);
        $response = $this->request(ServiceConstant::URL_PARCEL_COUNT.$filters, [] , self::HTTP_GET);
        $response = new ResponseHandler($response);
        if ($response->getStatus() == Response::STATUS_OK) {
            return $response->getData();
        } else {
            return 0;
        }
    }
}
