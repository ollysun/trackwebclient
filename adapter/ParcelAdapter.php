<?php
namespace Adapter;

use Adapter\Globals;
use Adapter\Globals\ServiceConstant;
use Adapter\Util\Calypso;
use Adapter\Util\Response;
use yii\helpers\Json;
use Adapter\Util\Util;

/**
 * Class ParcelAdapter
 * @author Adegoke Obasa <goke@cottacush.com>
 * @author Adeyemi Olaoye <yemi@cottacush.com>
 * @author Richard Boyewa <boye@cottacush.com>
 * @author Rotimi Akintewe <akintewe.rotimi@gmail.com>
 * @author Babatunde Otaru <tunde@cottacush.com>
 * @package Adapter
 */
class ParcelAdapter extends BaseAdapter
{

    public function __construct()
    {
        parent::__construct(RequestHelper::getClientID(), RequestHelper::getAccessToken());
    }

    const BILLING_METHOD_CORPORATE = 'corporate';

    /**
     * @author Adeyemi Olaoye <yemi@cottacush.com>
     * @param $waybill_number
     * @return int
     */
    public static function isBag($waybill_number)
    {
        return (preg_match('/^B[\w]+/i', $waybill_number));
    }

    /**
     * Returns the age analysis for the based on the status of the
     * @param $parcel
     * @return mixed
     */
    public static function getAgeAnalysis($parcel)
    {
        if (in_array($parcel['status'], [ServiceConstant::DELIVERED, ServiceConstant::CANCELLED, ServiceConstant::RETURNED])) {
            return Util::ago($parcel['created_date'], $parcel['modified_date']);
        } else {
            return Util::ago($parcel['created_date']);
        }
    }

    /**
     * @author Babatunde Otaru <tunde@cottacush.com>
     * @return Reasons[]
     */
    public function getParcelReturnReasons()
    {
        $request = $this->request(ServiceConstant::URL_RETURN_REASONS, [], self::HTTP_GET);
        $response = new ResponseHandler($request);
        return $reasons_list = $response->getStatus() == ResponseHandler::STATUS_OK ? $response->getData() : [];
    }

    public function createNewParcel($postData)
    {
        return $this->request(ServiceConstant::URL_ADD_PARCEL, $postData, self::HTTP_POST);
    }

    public function createNewParcelFromApi($postData){
        //echo json_encode($postData);die();
        return $this->request(ServiceConstant::URL_ADD_PARCEL_From_API, $postData, self::HTTP_POST);
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

    public function getParcel($staff_id, $status, $branch_id = null, $send_all = null)
    {
        $filter = array('held_by_staff_id' => $staff_id, 'status' => $status, 'to_branch_id' => $branch_id, 'send_all' => $send_all);
        return $this->request(ServiceConstant::URL_GET_ALL_PARCEL, $filter, self::HTTP_GET);
    }

    public function getParcels($start_created_date, $end_created_date, $status, $branch_id = null, $offset = 0, $count = 50, $with_from = null, $with_total = null, $only_parents = null, $with_created_branch = null, $cash_on_delivery = ServiceConstant::FALSE)
    {
        $filters = array(
            'status' => $status,
            'with_total_count' => $with_total,
            'start_created_date' => $start_created_date,
            'end_created_date' => $end_created_date,
            'from_branch_id' => $branch_id,
            'with_from_branch' => $with_from,
            'show_parents' => $only_parents,
            'with_created_branch' => $with_created_branch,
            'with_sender' => 1,
            'with_receiver' => 1,
            'with_receiver_address' => 1,
            'with_to_branch' => 1,
            'with_parcel_comment' => 1,
            'offset' => $offset,
            'count' => $count
        );
        if($cash_on_delivery){
            $filters['cash_on_delivery'] = $cash_on_delivery;
        }
        $params = http_build_query($filters);
        return $this->request(ServiceConstant::URL_GET_ALL_PARCEL . '?' . $params, array(), self::HTTP_GET);
    }

    public function getParcelsForDelivery($start_created_date, $end_created_date, $status, $branch_id = null, $offset = 0, $count = 50, $with_from = null, $with_total = null, $only_parents = null, $route_id = null, $with_route = null)
    {
        $filters = array(
            'status' => $status,
            'with_total_count' => $with_total,
            'show_parents' => $only_parents,
            'start_created_date' => $start_created_date,
            'end_created_date' => $end_created_date,
            'from_branch_id' => $branch_id,
            'with_sender' => 1,
            'with_created_branch' => 1,
            'with_receiver' => 1,
            'with_receiver_address' => 1,
            'with_to_branch' => 1,
            'offset' => $offset,
            'count' => $count,
            'to_branch_id' => $branch_id,
            'with_from_branch' => $with_from,
            'route_id=' => $route_id,
            'with_parcel_comment' => 1,
            'with_route' => $with_route
        );
        $params = http_build_query($filters);
        return $this->request(ServiceConstant::URL_GET_ALL_PARCEL . '?' . $params, [], self::HTTP_GET);
    }

    public function getParcelsForNextDestination($status = null, $branch_id = null, $to_branch_id = null, $held_by_id = null, $offset = 0, $count = 50, $with_total = null)
    {
        $filter = array(
            'status' => $status,
            'from_branch_id' => $branch_id,
            'to_branch_id' => $to_branch_id,
            'held_by_id' => $held_by_id,
            'with_total_count' => $with_total,
            'with_from_branch' => 1,
            'with_to_branch' => 1,
            'with_city' => 1,
            'with_sender_address' => 1,
            'with_receiver_address' => 1,
            'with_created_branch' => 1,
            'with_parcel_comment' => 1,
            'offset' => $offset,
            'count' => $count);
        $params = http_build_query($filter);
        return $this->request(ServiceConstant::URL_GET_ALL_PARCEL . '?' . $params, array(), self::HTTP_GET);
    }


    public function getParcelsForDirectManifest($branch_id = null, $offset = 0, $count = 50, $with_total = null)
    {
        $filter = array(
            'status' => 9,
            'to_branch_id' => $branch_id,
            'with_total_count' => $with_total,
            'with_from_branch' => 1,
            'with_to_branch' => 1,
            'with_city' => 1,
            'with_sender_address' => 1,
            'with_receiver_address' => 1,
            'with_created_branch' => 1,
            'with_parcel_comment' => 1,
            'offset' => $offset,
            'count' => $count);
        $params = http_build_query($filter);
        return $this->request(ServiceConstant::URL_GET_ALL_PARCEL . '?' . $params, array(), self::HTTP_GET);
    }

    public function getSearchParcels($status, $waybill_number, $offset = 0, $count = 50, $with_total = null, $branch_id = null,
                                     $only_parents = null, $with_created_branch = true)
    {
        $filters = array(
            'with_parcel_comment' => 1,
            'status' => $status,
            'waybill_number' => $waybill_number,
            'with_total_count' => $with_total,
            'show_parents' => $only_parents,
            'branch_id' => $branch_id,
            'with_sender' => 1,
            'with_created_branch' => $with_created_branch,
            'with_receiver' => 1,
            'with_receiver_address' => 1,
            'with_to_branch' => 1,
            'with_route' => 1,
            'offset' => $offset,
            'count' => $count
        );
        return $this->request(ServiceConstant::URL_GET_ALL_PARCEL, array_filter($filters), self::HTTP_GET);
    }

    public function getFilterParcelsByDateAndStatus($start_created_date,
                                                    $end_created_date,
                                                    $status, $offset = 0, $count = 50,
                                                    $with_total = null, $branch_id = null,
                                                    $only_parents = null, $with_created_branch = true,
                                                    $cash_on_delivery = ServiceConstant::FALSE)
    {
        $filters = array(
            'status' => $status,
            'with_total_count' => $with_total,
            'show_parents' => $only_parents,
            'start_created_date' => $start_created_date,
            'end_created_date' => $end_created_date,
            'from_branch_id' => $branch_id,
            'with_sender' => 1,
            'with_created_branch' => $with_created_branch,
            'with_receiver' => 1,
            'with_receiver_address' => 1,
            'with_to_branch' => 1,
            'with_from_branch' => 1,
            'with_parcel_comment' => 1,
            'offset' => $offset,
            'count' => $count
        );
        if($cash_on_delivery){
            $filters['cash_on_delivery'] = $cash_on_delivery;
        }
        $params = http_build_query($filters);
        return $this->request(ServiceConstant::URL_GET_ALL_PARCEL . '?' . $params, [], self::HTTP_GET);
    }

    public function getNewParcelsByDate($start_created_date, $offset = 0, $count = 500, $with_total = null, $branch_id = null, $only_parents = null)
    {
        $filter = array(
            'created_branch_id' => $branch_id,
            'start_created_date' => $start_created_date,
            'with_sender' => 1,
            'with_receiver' => 1,
            'with_receiver_address' => 1,
            'with_from_branch' => 1,
            'with_to_branch' => 1,
            'show_parents' => $only_parents,
            'with_total_count' => $with_total,
            'with_created_branch' => 1,
            'with_parcel_comment' => 1,
            'offset' => $offset,
            'count' => $count);
        $params = http_build_query($filter);

        return $this->request(ServiceConstant::URL_GET_ALL_PARCEL . '?' . $params, [], self::HTTP_GET);
    }

    public function getDispatchedParcels($branch_id, $to_branch = null, $start_created_date = null, $end_created_date = null, $status = null)
    {
        $filter = array(
            'history_from_branch_id' => $branch_id,
            'history_to_branch_id' => $to_branch,
            'history_start_created_date' => $start_created_date,
            'history_end_created_date' => $end_created_date,
            'with_to_branch' => 1,
            'with_from_branch' => 1,
            'with_holder' => 1,
            'with_created_branch' => 1,
            'with_parcel_comment' => 1,
            'history_status' => $status);
        $params = http_build_query($filter);
        return $this->request(ServiceConstant::URL_GET_ALL_PARCEL . '?' . $params, [], self::HTTP_GET);
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

    public function createDirectManifest($postData)
    {
        return $this->request(ServiceConstant::URL_CREATE_DIRECT_MANIFEST, $postData, self::HTTP_POST);
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

    public function markAsReturned($data)
    {
        return $this->request(ServiceConstant::URL_MARK_AS_RETURNED, $data, self::HTTP_POST);
    }

    public function moveParcel($postData)
    {
        return $this->request(ServiceConstant::URL_MOVE_PARCEL, $postData, self::HTTP_POST);
    }

    public function repriceCompany($data)
    {
//        $filter = '?reg_no='.$data['regNo'];
//        $filter .= '&start_created_date=' . $data['from'];
//        $filter .= '&end_created_date='. $data['to'];
        return $this->request(ServiceConstant::URL_REPRICE_COMPANY , $data,self::HTTP_POST);
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

    public function getECDispatchedParcels($branch_id, $offset = 0, $count = 50, $search = null)
    {
        $filter = (Calypso::userIsInRole(ServiceConstant::USER_TYPE_ADMIN) ||
            Calypso::userIsInRole(ServiceConstant::USER_TYPE_OFFICER)) && !empty($search)?
            array(
                'to_branch_id' => null,
                'with_total_count' => 1,
                'status' => null,
                'waybill_number' => $search,
                'with_receiver' => 1,
                //'with_holder' => 1,
                'with_to_branch' => 1,
                'with_created_branch' => 1,
                'with_parcel_comment' => 1,
                'offset' => $offset,
                'count' => $count
            ) :
            array(
                'to_branch_id' => $branch_id,
                'with_total_count' => 1,
                'status' => ServiceConstant::BEING_DELIVERED,
                'waybill_number' => $search,
                'with_receiver' => 1,
                'with_holder' => 1,
                'with_to_branch' => 1,
                'with_created_branch' => 1,
                'with_parcel_comment' => 1,
                'offset' => $offset,
                'count' => $count
            );
        $filter = array_filter($filter, 'strlen');
        return $this->request(ServiceConstant::URL_GET_ALL_PARCEL, $filter, self::HTTP_GET);
    }

    public function getDeliveredParcels($branch_id, $offset = 0, $count = 50, $start_modified_date = null, $end_modified_date = null)
    {

        $filter = array(
            'to_branch_id' => $branch_id,
            'status' => ServiceConstant::DELIVERED,
            'with_total_count' => 1,
            'with_receiver' => 1,
            'with_sender' => 1,
            'start_modified_date' => $start_modified_date,
            'end_modified_date' => $end_modified_date,
            'with_created_branch' => 1,
            'with_from_branch' => 1,
            'with_to_branch' => 1,
            'with_delivery_receipt' => 1,
            'offset' => $offset,
            'count' => $count
        );
        $filter = array_filter($filter, 'strlen');
        return $this->request(ServiceConstant::URL_GET_ALL_PARCEL, $filter, self::HTTP_GET);
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

    public function getQuote($postData)
    {
        return $this->request(ServiceConstant::URL_GET_QUOTE, $postData, self::HTTP_POST);
    }

    public function cancel($postData)
    {
        return $this->request(ServiceConstant::URL_CANCEL_PARCEL, json_encode($postData), self::HTTP_POST);
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
        $response = $this->request(ServiceConstant::URL_GET_BAG, array('waybill_number' => $waybill_number), self::HTTP_GET);
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

        $filter_array = array_filter($filter_array);
        $filters = '?' . http_build_query($filter_array);
        $response = $this->request(ServiceConstant::URL_PARCEL_COUNT . $filters, [], self::HTTP_GET);
        $response = new ResponseHandler($response);
        if ($response->getStatus() == Response::STATUS_OK) {
            return $response->getData();
        } else {
            return 0;
        }
    }

    public function groupCount($filter_collection){
        $response = $this->request(ServiceConstant::URL_PARCEL_GROUP_COUNT, $filter_collection, self::HTTP_POST);
        $response = new ResponseHandler($response);
        return $response;
    }

    /**
     * Send the return request
     * @author Olawale Lawal <wale@cottacush.com>
     * @param $waybill_numbers
     * @param $comment
     * @param int $attempted_delivery
     * @param string $extra_note
     * @param int $claim
     * @return array|mixed|string
     */
    public function sendReturnRequest($waybill_numbers, $comment, $attempted_delivery = 0, $extra_note = '', $claim = 0)
    {
        return $this->request(ServiceConstant::URL_SET_RETURN_FLAG, ['waybill_numbers' => $waybill_numbers,
            'comment' => $comment, 'attempted_delivery' => $attempted_delivery,
            'extra_note' => $extra_note, 'claim' => $claim], self::HTTP_POST);
    }

    public function removeNegativeStatus($waybill_number){
        return $this->request(ServiceConstant::URL_REMOVE_NEGATIVE_FLAG, ['waybill_number' => $waybill_number], self::HTTP_POST);
    }

    public function openBag($postData)
    {
        return $this->request(ServiceConstant::URL_OPEN_BAG, $postData, self::HTTP_POST);
    }

    public function removeFromBag($postData)
    {
        return $this->request(ServiceConstant::URL_REMOVE_FROM_BAG, $postData, self::HTTP_POST);
    }

    /**
     * @author Adeyemi Olaoye <yemi@cottacush.com>
     * @param $waybill_numbers
     * @return array|mixed|string
     */
    public function unsort($waybill_numbers)
    {
        $rawResponse = $this->request(ServiceConstant::URL_UNSORT_PARCEL, Json::encode(['waybill_numbers' => $waybill_numbers]), self::HTTP_POST);
        $response = new ResponseHandler($rawResponse);
        if (!$response->isSuccess()) {
            $this->lastErrorMessage = $response->getError();
        }
        $this->setResponseHandler($response);
        return $response->isSuccess();
    }

    /**
     * @author Adeyemi Olaoye <yemi@cottacush.com>
     * @param $comment
     * @param $type
     * @param $waybill_number
     * @return bool
     */
    public function comment($comment, $type, $waybill_number)
    {
        $data = ['comment' => $comment, 'type' => $type, 'waybill_number' => $waybill_number];
        $rawResponse = $this->request(ServiceConstant::URL_UNSORT_PARCEL, Json::encode($data), self::HTTP_POST);
        $response = new ResponseHandler($rawResponse);
        if (!$response->isSuccess()) {
            $this->lastErrorMessage = $response->getError();
        }
        $this->setResponseHandler($response);
        return $response->isSuccess();
    }

    /**
     * Get expected parcels for a branch
     * @author Adeyemi Olaoye <yemi@cottacush.com>
     * @param $offset
     * @param $page_width
     * @param $branch
     * @return array|mixed|string
     */
    public function getExpectedParcels($offset, $page_width, $branch)
    {
        $filters = [
            'status' => ServiceConstant::IN_TRANSIT,
            'to_branch_id' => $branch,
            'with_total_count' => 1,
            'with_to_branch' => 1,
            'with_city' => 1,
            'with_sender_address' => 1,
            'with_receiver_address' => 1,
            'with_created_branch' => 1,
            'offset' => $offset,
            'count' => $page_width
        ];

        $response = $this->getParcelsByFilters($filters);
        $responseHandler = new ResponseHandler($response);
        if ($responseHandler->getStatus() == ResponseHandler::STATUS_OK) {
            $responseData = $responseHandler->getData();
            $parcels = Calypso::getValue($responseData, 'parcels', []);
            $draftSorts = $this->getDraftSorts();
            $sortedWaybillNumbers = array_column($draftSorts, 'waybill_number');
            $expectedParcels = [];
            foreach ($parcels as $parcel) {
                if (!in_array(Calypso::getValue($parcel, 'waybill_number'), $sortedWaybillNumbers)) {
                    $expectedParcels[] = $parcel;
                }
            }
            $responseData['parcels'] = $expectedParcels;
            return $responseData;
        }
        return false;
    }

    /**
     * Returns parcels based on the filters
     * @author Olawale Lawal <wale@cottacush.com>
     * @param $filters
     * @return array|mixed|string
     */
    public function getParcelsByFilters($filters)
    {
        $filters = array_merge($filters, [
            'with_company' => 1,
            'with_parcel_comment' => 1,
        ]);

        //dd($filters);

        return $this->request(ServiceConstant::URL_GET_ALL_PARCEL, $filters, self::HTTP_GET);
    }

    /**
     * Get draft sort parcels
     * @author Adeyemi Olaoye <yemi@cottacush.com>
     * @param $offset
     * @param $page_width
     * @param bool $paginate
     * @return array|mixed|string
     */
    public function getDraftSorts($offset = null, $page_width = null, $paginate = false)
    {
        $filters = ['offset' => $offset, 'count' => $page_width, 'paginate' => (($paginate) ? 1 : 0)];
        $response = $this->request(ServiceConstant::URL_GET_DRAFT_SORTS, $filters, self::HTTP_GET);
        $responseHandler = new ResponseHandler($response);
        if ($responseHandler->getStatus() == ResponseHandler::STATUS_OK) {
            return $responseHandler->getData();
        }
        return [];
    }


    /**
     * Get parcels in a draft bag
     * @author Adeyemi Olaoye <yemi@cottacush.com>
     * @param $bag_sort_number
     * @return array|mixed
     */
    public function getDraftBagParcels($bag_sort_number)
    {
        $filters = ['bag_number' => $bag_sort_number, 'is_visible' => 0];
        $response = $this->request(ServiceConstant::URL_GET_DRAFT_SORTS, $filters, self::HTTP_GET);
        $responseHandler = new ResponseHandler($response);
        if ($responseHandler->getStatus() == ResponseHandler::STATUS_OK) {
            return $responseHandler->getData();
        }
        return [];
    }

    /**
     * Create draft sortings
     * @author Adeyemi Olaoye <yemi@cottacush.com>
     * @param $data array
     * @return ResponseHandler
     */
    public function createDraftSort($data)
    {
        $rawResponse = $this->request(ServiceConstant::URL_DRAFT_SORT, Json::encode($data), self::HTTP_POST);
        $response = new ResponseHandler($rawResponse);
        if (!$response->isSuccess()) {
            $this->lastErrorMessage = $response->getError();
        }

        return $response;
    }

    /**
     * Discard draft sortings
     * @author Adeyemi Olaoye <yemi@cottacush.com>
     * @param $data
     * @return ResponseHandler
     */
    public function discardDraftSort($data)
    {
        $rawResponse = $this->request(ServiceConstant::URL_DISCARD_SORT, Json::encode($data), self::HTTP_POST);
        $response = new ResponseHandler($rawResponse);
        if (!$response->isSuccess()) {
            $this->lastErrorMessage = $response->getError();
        }
        return $response;
    }

    /**
     * confirm draft sortings
     * @author Adeyemi Olaoye <yemi@cottacush.com>
     * @param $data
     * @return ResponseHandler
     */
    public function confirmDraftSort($data)
    {
        $rawResponse = $this->request(ServiceConstant::URL_CONFIRM_SORT, Json::encode($data), self::HTTP_POST);
        $response = new ResponseHandler($rawResponse);
        if (!$response->isSuccess()) {
            $this->lastErrorMessage = $response->getError();
        }
        return $response;
    }

    /**
     * create draft bag
     * @author Adeyemi Olaoye <yemi@cottacush.com>
     * @param $data
     * @return ResponseHandler
     */
    public function createDraftBag($data)
    {
        $rawResponse = $this->request(ServiceConstant::URL_CREATE_DRAFT_BAG, Json::encode($data), self::HTTP_POST);
        $response = new ResponseHandler($rawResponse);
        if (!$response->isSuccess()) {
            $this->lastErrorMessage = $response->getError();
        }
        return $response;
    }

    /**
     * create draft bag
     * @author Adeyemi Olaoye <yemi@cottacush.com>
     * @param $data
     * @return ResponseHandler
     */
    public function confirmDraftBag($data)
    {
        $rawResponse = $this->request(ServiceConstant::URL_CONFIRM_DRAFT_BAG, Json::encode($data), self::HTTP_POST);
        $response = new ResponseHandler($rawResponse);
        if (!$response->isSuccess()) {
            $this->lastErrorMessage = $response->getError();
        }
        return $response;
    }

    /**
     * Get's corporate parcels
     * @author Adegoke Obasa <goke@cottacush.com>
     * @param $offset
     * @param $count
     * @param array $filters
     * @return array|mixed|string
     */
    public function getCorporateParcels($offset, $count, $filters = [])
    {
        $filters = array_merge($filters, [
            'billing_type' => self::BILLING_METHOD_CORPORATE,
            'offset' => $offset,
            'count' => $count,
            'with_total_count' => 1,
            'with_payment_type' => 1,
            'with_company' => 1,
            'with_invoice_parcel' => 1
        ]);
        $filters = array_filter($filters);

        $response = $this->request(ServiceConstant::URL_GET_ALL_PARCEL, $filters, self::HTTP_GET);
        $response = new ResponseHandler($response);


        if ($response->isSuccess()) {
            return $response->getData();
        }
        return [];
    }

    /**
     * Returns the current location of the parcel
     * @param $parcel
     * @return string
     */
    public static function getCurrentLocation($parcel)
    {
        switch ($parcel['status']) {
            case ServiceConstant::FOR_ARRIVAL:
                return strtoupper(Calypso::getDisplayValue($parcel, 'to_branch.name'));
            case ServiceConstant::IN_TRANSIT:
                return 'In transit to ' . ucwords(Calypso::getDisplayValue($parcel, 'to_branch.name'));
            case ServiceConstant::BEING_DELIVERED:
                return 'In transit to Customer from ' . ucwords(Calypso::getDisplayValue($parcel, 'to_branch.name'));
            case ServiceConstant::ASSIGNED_TO_GROUNDSMAN:
                return 'Being sorted at ' . ucwords(Calypso::getDisplayValue($parcel, 'to_branch.name'));
            case ServiceConstant::DELIVERED:
                return 'Delivered from ' . ucwords(Calypso::getDisplayValue($parcel, 'to_branch.name'));
            default:
                return strtoupper(Calypso::getDisplayValue($parcel, 'from_branch.name'));
        }
    }

    /**
     * @author Adeyemi Olaoye <yemi@cottacush.com>
     * @param $parcelsData
     * @param $company_id
     * @param $billing_plan_id
     * @return bool
     */
    public function createBulkShipmentTask($parcelsData, $company_id, $billing_plan_id)
    {
        $data = ['data' => $parcelsData, 'company_id' => $company_id, 'billing_plan_id' => $billing_plan_id];
        $rawResponse = $this->request(ServiceConstant::URL_CREATE_BULK_SHIPMENT_TASK, Json::encode($data), self::HTTP_POST);

        $response = new ResponseHandler($rawResponse);
        if (!$response->isSuccess()) {
            $this->lastErrorMessage = $response->getError();
        }
        $this->setResponseHandler($response);
        return $response;
    }

    /**
     * Get bulk shipment tasks
     * @author Adeyemi Olaoye <yemi@cottacush.com>
     * @param $offset
     * @param $count
     * @return array|mixed|string
     */
    public function getBulkShipmentTasks($offset, $count)
    {
        $response = $this->request(ServiceConstant::URL_GET_BULK_SHIPMENT_TASKS, ['offset' => $offset, 'count' => $count], self::HTTP_GET);

        $response = new ResponseHandler($response);

        if ($response->isSuccess()) {
            return $response->getData();
        }
        return [];
    }

    /**
     * Get bulk shipment task
     * @author Adeyemi Olaoye <yemi@cottacush.com>
     * @param $task_id
     * @return array|mixed|string
     */
    public function getBulkShipmentTask($task_id)
    {
        $response = $this->request(ServiceConstant::URL_GET_BULK_SHIPMENT_TASK, ['task_id' => $task_id], self::HTTP_GET);
        $response = new ResponseHandler($response);

        if ($response->isSuccess()) {
            return $response->getData();
        }
        return [];
    }

    /**
     * Get bulk shipment task
     * @author Adeyemi Olaoye <yemi@cottacush.com>
     * @param $task_id
     * @return ResponseHandler
     */
    public function createBulkWaybillPrintingTask($task_id)
    {
        $response = $this->request(ServiceConstant::URL_CREATE_BULK_WAYBILL_PRINTING_TASK, Json::encode(['bulk_shipment_task_id' => $task_id]), self::HTTP_POST);
        $response = new ResponseHandler($response);

        if (!$response->isSuccess()) {
            $this->lastErrorMessage = $response->getError();
        }

        return $response;
    }

    /* Tony */

    public function getParcelHistories($waybill_number){
        return $this->request(ServiceConstant::URL_GET_PARCEL_HISTORIES, array('waybill_number' => $waybill_number), self::HTTP_GET);
    }

    public function getParcelHistoriesForApi($waybill_number){
        return $this->request(ServiceConstant::URL_GET_PARCEL_HISTORIES_FOR_API, array('waybill_number' => $waybill_number), self::HTTP_GET);
    }

    public function getParcelStatusForApi($waybill_number){
        return $this->request(ServiceConstant::URL_GET_PARCEL_LAST_STATUS_FOR_API, array('waybill_number' => $waybill_number), self::HTTP_GET);
    }



    public function getShipmentExceptions($filter){
        $response = $this->request(ServiceConstant::URL_GET_SHIPMENT_EXCEPTIONS, $filter, self::HTTP_GET);

        $response = new ResponseHandler($response);

        if(!$response->isSuccess()){
            $this->lastErrorMessage = $response->getError();
        }

        return $response->getData();
    }

    public function getDelayedShipments($filter){
        $response = $this->request(ServiceConstant::URL_GET_DELAYED_SHIPMENTS, $filter, self::HTTP_GET);

        $response = new ResponseHandler($response);

        if(!$response->isSuccess()){
            $this->lastErrorMessage = $response->getError();
        }

        return $response->getData();
    }

    public function validateNumbers($csvNumbers, $by){
        return $this->request(ServiceConstant::URL_PARCEL_VALIDATE_NUMBERS,
            ['numbers' => $csvNumbers, 'by' => $by], self::HTTP_POST);
    }

    public function batchDiscount($params){
        $rawResponse = $this->request(ServiceConstant::URL_BATCH_DISCOUNT, Json::encode($params), self::HTTP_POST);
        $response = new ResponseHandler($rawResponse);

        if (!$response->isSuccess()) {
            $this->lastErrorMessage = $response->getError();
        }

        return $response->isSuccess();
    }

    /**Get waybill array from comma seperated list of waybill numbers
     * @author Adeyemi Olaoye <yemi@cottacush.com>
     * @param $waybill_numbers
     * @return array
     */
    public static function sanitizeWaybillNumbers($waybill_numbers)
    {
        $waybill_number_arr = explode(',', $waybill_numbers);
        $clean_arr = [];
        foreach ($waybill_number_arr as $number) {
            $clean_arr[trim(strtoupper($number))] = true;
        }

        return array_keys($clean_arr);
    }


}
