<?php
use Phalcon\Exception;
use Phalcon\Mvc\Model\Resultset;
use PhalconUtils\Validation\RequestValidation;
use PhalconUtils\Validation\Validators\Model;

/**
 * Class ParcelController
 * @author Adeyemi Olaoye <yemi@cottacush.com>
 * @author Rahman Shitu <rahman@cottacush.com>
 * @author Olawale Lawal <wale@cottacush.com>
 */
class ParcelController extends ControllerBase
{
    /**
     * @author Babatunde Otaru <tunde@cottacush.com>
     * Returns Reasons for returning a parcel
     */
    public function getReturnReasonsAction()
    {
        $reasons = ReturnReasons::getAll();
        return $this->response->sendSuccess($reasons);
    }

    /**
     * Creates a new parcel
     * @author Adegoke Obasa <goke@cottacush.com>
     * @author Rahman Shitu <rahman@cottacush.com>
     * @return $this
     */
    public function addAction()
    {
        //todo: must be tied to an EC Officer only
        $this->auth->allowOnly([Role::ADMIN, Role::OFFICER, Role::SWEEPER, Role::DISPATCHER, Role::COMPANY_ADMIN, Role::SALES_AGENT, Role::COMPANY_OFFICER]);
        $payload = $this->request->getJsonRawBody(true);
        $sender = (isset($payload['sender'])) ? $payload['sender'] : null;
        $sender_address = (isset($payload['sender_address'])) ? $payload['sender_address'] : null;
        $receiver = (isset($payload['receiver'])) ? $payload['receiver'] : null;
        $receiver_address = (isset($payload['receiver_address'])) ? $payload['receiver_address'] : null;
        $bank_account = (isset($payload['bank_account'])) ? $payload['bank_account'] : null;
        $parcel = (isset($payload['parcel'])) ? $payload['parcel'] : null;
        $to_hub = (isset($payload['to_hub'])) ? $payload['to_hub'] : null;
        $is_corporate_lead = (isset($payload['is_corporate_lead'])) ? $payload['is_corporate_lead'] : null;
        $to_branch_id = (isset($payload['to_branch_id'])) ? $payload['to_branch_id'] : null;
        $nearest_branch_id = $this->auth->isCooperateUser()? City::findFirst($sender_address['city_id'])->getBranchId(): null;

        /** @Author if this is a cooperate user and the receiver is not in their address list */


        if (in_array(null, array($parcel, $sender, $sender_address, $receiver, $receiver_address)) or $to_hub === null) {
            return $this->response->sendError(ResponseMessage::ERROR_REQUIRED_FIELDS);
        }

        $auth_data = $this->auth->getData();

        //Ensuring the officer is an EC or HUB officer or an Admin or a cooperate officer
        if (!in_array($auth_data['branch']['branch_type'], [BranchType::HQ, BranchType::EC, BranchType::HUB])
            && !$this->auth->isCooperateUser()) {
            return $this->response->sendAccessDenied();
        }

        //determining destination branch
        if (!(isset($parcel['id']) AND $to_branch_id == null)) {
            if ($to_hub > 0) {
                if ($auth_data['branch']['branch_type'] == BranchType::EC) {
                    $to_branch = Branch::getParentById($auth_data['branch']['id']);
                    if ($to_branch == null) {
                        return $this->response->sendError(ResponseMessage::EC_NOT_LINKED_TO_HUB);
                    }
                    $to_branch_id = $to_branch->getId();
                } else if ($to_branch_id == null) {
                    return $this->response->sendError(ResponseMessage::ERROR_REQUIRED_FIELDS . ': Destination branch');
                }
            } else {
                if($this->auth->isCooperateUser()){
                    //$sender_state_id = City::fetchOne($sender_address['city_id'], [])['state_id'];
                    $to_branch_id = $nearest_branch_id;
                }else{
                    $to_branch_id = $auth_data['branch']['id'];
                }

            }
        } else {
            $to_branch_id = Parcel::findFirstById($parcel['id'])->toArray()['to_branch_id'];
        }

        //parcel no_of_package validation
        $parcel['no_of_package'] = intval($parcel['no_of_package']);
        if ($parcel['no_of_package'] < 1) {
            return $this->response->sendError(ResponseMessage::INVALID_PACKAGE_COUNT);
        }

        //parcel cash_amount and pos_amount sanitation
        switch ($parcel['payment_type']) {
            case PaymentType::CASH:
            case PaymentType::DEFERRED:
                $parcel['cash_amount'] = $parcel['amount_due'];
                $parcel['pos_amount'] = 0.0;
                break;
            case PaymentType::POS:
                $parcel['cash_amount'] = 0.0;
                $parcel['pos_amount'] = $parcel['amount_due'];
                break;
            case PaymentType::CASH_AND_POS:
                if (!isset($parcel['cash_amount']) or !isset($parcel['pos_amount'])) {
                    return $this->response->sendError(ResponseMessage::INVALID_AMOUNT);
                } else if ((bccomp($parcel['cash_amount'] + $parcel['pos_amount'], $parcel['amount_due'], 2) != 0)) {
                    return $this->response->sendError(ResponseMessage::INVALID_AMOUNT);
                }
                break;
            default:
                return $this->response->sendError(ResponseMessage::INVALID_PAYMENT_TYPE);
        }

        if (($parcel['payment_type'] != PaymentType::DEFERRED || !$parcel['cash_on_delivery']) && $parcel['is_freight_included']) {
            return $this->response->sendError(ResponseMessage::INVALID_FREIGHT_INCLUSION);
        }

        if (!in_array($parcel['qty_metrics'], [Parcel::QTY_METRICS_PIECES, Parcel::QTY_METRICS_WEIGHT])) {
            return $this->response->sendError(ResponseMessage::INVALID_QTY_METRICS);
        }

        $parcel_edit_history = new ParcelEditHistory();

        if (isset($parcel['id'])) {
            $parcel_obj = Parcel::findFirstById($parcel['id']);
            $parcel_edit_history->before_data = json_encode($parcel_obj->toArray());
        } else {
            //check reference number duplicate reference_number
            if(!empty($parcel['reference_number']) && Parcel::findFirstByReferenceNumber($parcel['reference_number']))
            {
                return $this->response->sendError('The reference number you entered has been used');
            }
            $parcel_obj = new Parcel();
        }

        //created by is an admin from the nearest branch for shipments created by cooperate
        $created_by = !$this->auth->isCooperateUser()? $this->auth->getPersonId():Admin::fetchOfficerForBranch($nearest_branch_id)['id'];

        // Check if edit branch is related to created branch
        if (isset($parcel['id']) && $auth_data['branch']['branch_type'] != BranchType::HQ) {
            $created_branch_id = $parcel_obj->created_branch_id;
            $branch_id = $auth_data['branch_id'];
            $can_edit_parcel = Parcel::isEditAccessGranted($created_branch_id,$branch_id);
            if (!$can_edit_parcel) {
                    return $this->response->sendError('Can only be edited by the parent hub or created branch');
            }
        }

        try {
            //check that the waybill number is valid if any is sent
            if(!empty($parcel['waybill_number'])){
                if(!Parcel::isWaybillNumber($parcel['waybill_number'])){
                    return $this->response->sendError('Invalid waybill number format');
                }
                if(!isset($parcel['id']) && Parcel::getByWaybillNumber($parcel['waybill_number'])){
                    return $this->response->sendError('The waybill number you entered is already in use');
                }
            }

            $waybill_numbers = $parcel_obj->saveForm($this->auth->isCooperateUser()?$nearest_branch_id:$auth_data['branch']['id'], $sender, $sender_address, $receiver, $receiver_address,
                $bank_account, $parcel, $to_branch_id, $created_by, $this->auth->isCooperateUser());
            if (isset($parcel['id'])) {
                $parcel_edit_history->parcel_id = $parcel['id'];
                $parcel_edit_history->after_data = json_encode($parcel_obj->toArray());
                $parcel_edit_history->changed_by = $auth_data['id'];
                $parcel_edit_history->modified_at = Util::getCurrentDateTime();
                $is_successful = $parcel_edit_history->save();
                if (!$is_successful) {
                    return $this->response->sendError('Could not save edit details');
                }
            }
            if ($waybill_numbers) {

                /**
                 * @author Adegoke Obasa <goke@cottacush.com>
                 * Link Parcel to Pickup Request
                 */
                if (isset($payload['pickup_request_id'])) {
                    PickupRequest::linkParcelAndChangeStatus($payload['pickup_request_id'], $parcel_obj->getWaybillNumber());
                }

                /**
                 * Link Parcel to Shipment Request
                 * @author Adegoke Obasa <goke@cottacush.com>
                 **/
                if (isset($payload['shipment_request_id'])) {
                    ShipmentRequest::linkParcelAndChangeStatus($payload['shipment_request_id'], $parcel_obj->getWaybillNumber());
                }

                if ($is_corporate_lead == 1) {
                    $city = City::fetchOne($sender_address['city_id'], ['with_state' => 1, 'with_country' => 1]);
                    if ($city != false) {
                        EmailMessage::send(
                            EmailMessage::CORPORATE_LEAD,
                            [
                                'firstname' => (isset($sender['firstname'])) ? ucfirst(strtolower($sender['firstname'])) : '',
                                'lastname' => (isset($sender['lastname'])) ? ucfirst(strtolower($sender['lastname'])) : '',
                                'street1' => (isset($sender_address['street1'])) ? $sender_address['street1'] : '',
                                'street2' => (isset($sender_address['street2'])) ? $sender_address['street2'] : '',
                                'city' => (isset($sender_address['city_id'])) ? ucwords(strtolower($city['name'])) : '',
                                'state' => (isset($sender_address['city_id'])) ? ucwords(strtolower($city['state']['name'])) : '',
                                'country' => (isset($sender_address['city_id'])) ? ucwords(strtolower($city['country']['name'])) : '',
                                'email' => (isset($sender['email'])) ? $sender['email'] : '',
                                'phone' => (isset($sender['phone'])) ? $sender['phone'] : '',
                                'year' => date('Y')
                            ],
                            'Courier Plus [' . strtoupper($auth_data['branch']['name']) . ']'
                        );
                    }
                }

                if($parcel_obj->getNotificationStatus() == Status::ACTIVE){
                    //send sms to sender and receiver
                    if($sender['phone']){
                        SmsMessage::send($sender['phone'], 'Courierplus',
                            'Your shipment have been created. You can track it using '. $parcel_obj->getWaybillNumber());
                    }

                    if($receiver['phone']){
                        SmsMessage::send($receiver['phone'], 'Courierplus',
                            'Your shipment have been created. You can track it using '. $parcel_obj->getWaybillNumber());
                    }
                }

                return $this->response->sendSuccess(['id' => $parcel_obj->getId(), 'waybill_number' => $waybill_numbers]);
            } else {
                return $this->response->sendError();
            }
        } catch (\Exception $ex) {
            return $this->response->sendError($ex->getMessage());
        }
    }

    public function getOneAction()
    {
        $this->auth->allowOnly([Role::DISPATCHER, Role::SWEEPER, Role::ADMIN, Role::OFFICER, Role::GROUNDSMAN, Role::COMPANY_ADMIN, Role::COMPANY_OFFICER, Role::SALES_AGENT]);

        $id = $this->request->getQuery('id');
        $waybill_number = $this->request->getQuery('waybill_number');
        $with_linked = $this->request->getQuery('with_linked');

        if (is_null($id) && is_null($waybill_number)) {
            return $this->response->sendError(ResponseMessage::ERROR_REQUIRED_FIELDS);
        }

        if (is_null($waybill_number)) {
            $value = $id;
            $fetch_with = 'id';
        } else {
            $value = $waybill_number;
            $fetch_with = 'waybill_number';
        }

        $with_linked = ($with_linked == 0 || $with_linked == 'false') ? false : true;

        $parcel = Parcel::fetchOne($value, $with_linked, $fetch_with);
        if ($parcel != false) {
            return $this->response->sendSuccess($parcel);
        }
        return $this->response->sendError(ResponseMessage::NO_RECORD_FOUND);
    }

    /**
     * @author Adeyemi Olaoye <yemi@cottacush.com>
     * @author Rahman Shitu <rahman@cottacush.com>
     * @author Olawale Lawal <wale@cottacush.com>
     */
    private function getFilterParams()
    {
        $filter_params = ['return_reason_comment', 'for_return', 'manifest_id', 'show_parents', 'parent_id', 'entity_type', 'is_visible',
            'created_by', 'user_id', 'held_by_staff_id', 'held_by_id', 'to_branch_id', 'from_branch_id', 'parcel_type',
            'sender_id', 'sender_address_id', 'receiver_id', 'receiver_address_id', 'status', 'min_weight', 'max_weight',
            'min_amount_due', 'max_amount_due', 'cash_on_delivery', 'min_delivery_amount', 'max_delivery_amount', 'delivery_type',
            'payment_type', 'shipping_type', 'min_cash_amount', 'max_cash_amount', 'min_pos_amount', 'max_pos_amount',
            'start_created_date', 'end_created_date', 'start_modified_date', 'end_modified_date', 'waybill_number', 'waybill_number_arr',
            'created_branch_id', 'route_id', 'history_status', 'history_start_created_date',
            'history_end_created_date', 'history_from_branch_id', 'history_to_branch_id', 'request_type', 'billing_type',
            'company_id', 'report', 'remove_cancelled_shipments', 'show_both_parent_and_splits', 'show_removed'
        ];

        $filter_by = [];
        foreach ($filter_params as $param) {
            $$param = $this->request->getQuery($param);
            if (!is_null($$param)) {
                $filter_by[$param] = $$param;
            }
        }

        return $filter_by;
    }

    /**
     * @author Adegoke Obasa <goke@cottacush.com>
     * @author Adeyemi Olaoye <yemi@cottacush.com>
     * @author Rahman Shitu <rahman@cottacush.com>
     * @author Olawale Lawal <wale@cottacush.com>
     */
    public function getAllAction()
    {
        $this->auth->allowOnly([Role::ADMIN, Role::OFFICER, Role::SWEEPER, Role::DISPATCHER, Role::GROUNDSMAN, Role::COMPANY_ADMIN, Role::COMPANY_OFFICER, Role::SALES_AGENT]);


        $offset = $this->request->getQuery('offset', null, DEFAULT_OFFSET);
        $count = $this->request->getQuery('count', null, DEFAULT_COUNT);

        $with_to_branch = $this->request->getQuery('with_to_branch');
        $with_from_branch = $this->request->getQuery('with_from_branch');
        $with_sender = $this->request->getQuery('with_sender');
        $with_sender_address = $this->request->getQuery('with_sender_address');
        $with_receiver = $this->request->getQuery('with_receiver');
        $with_receiver_address = $this->request->getQuery('with_receiver_address');
        $with_holder = $this->request->getQuery('with_holder');
        $with_bank_account = $this->request->getQuery('with_bank_account');
        $with_created_branch = $this->request->getQuery('with_created_branch');
        $with_route = $this->request->getQuery('with_route');
        $with_delivery_receipt = $this->request->getQuery('with_delivery_receipt');
        $with_created_by = $this->request->getQuery('with_created_by');
        $with_payment_type = $this->request->getQuery('with_payment_type');
        $with_company = $this->request->getQuery('with_company');
        $with_invoice_parcel = $this->request->getQuery('with_invoice_parcel');
        $with_parcel_comment = $this->request->getQuery('with_parcel_comment');
        $with_edit_access = 1;

        $with_total_count = $this->request->getQuery('with_total_count');
        $send_all = $this->request->getQuery('send_all');

        $order_by = $this->request->getQuery('order_by'); //'Parcel.created_date DESC'

        $report = $this->request->getQuery('report');

        $filter_by = $this->getFilterParams();

        $fetch_with = [];


        /**
         * @author Ademu Anthony
         * if the report is for delivered parcels,  then fetch delivery receipt and delivered by
         */
        if(isset($filter_by['status']) && $filter_by['status'] == Status::PARCEL_DELIVERED){
            $with_delivery_receipt = true;
        }

        if (!is_null($with_to_branch)) {
            $fetch_with['with_to_branch'] = true;
        }
        if (!is_null($with_from_branch)) {
            $fetch_with['with_from_branch'] = true;
        }
        if (!is_null($with_sender)) {
            $fetch_with['with_sender'] = true;
        }
        if (!is_null($with_receiver)) {
            $fetch_with['with_receiver'] = true;
        }
        if (!is_null($with_sender_address)) {
            $fetch_with['with_sender_address'] = true;
        }
        if (!is_null($with_receiver_address)) {
            $fetch_with['with_receiver_address'] = true;
        }
        if (!is_null($with_holder)) {
            $fetch_with['with_holder'] = true;
        }
        if (!is_null($with_bank_account)) {
            $fetch_with['with_bank_account'] = true;
        }
        if (!is_null($with_created_branch)) {
            $fetch_with['with_created_branch'] = true;
        }
        if (!is_null($with_route)) {
            $fetch_with['with_route'] = true;
        }
        if (!is_null($with_created_by)) {
            $fetch_with['with_created_by'] = true;
        }
        if (!is_null($with_delivery_receipt)) {
            $fetch_with['with_delivery_receipt'] = true;
        }
        if (!is_null($with_payment_type)) {
            $fetch_with['with_payment_type'] = true;
        }
        if (!is_null($with_company)) {
            $fetch_with['with_company'] = true;
        }
        if (!is_null($with_invoice_parcel)) {
            $fetch_with['with_invoice_parcel'] = true;
        }
        if (!is_null($with_parcel_comment)) {
            $fetch_with['with_parcel_comment'] = true;
        }

        if(!is_null($with_edit_access)){
            $fetch_with['with_edit_access'] = true;
        }

        ///*
        if($this->request->get('for_direct_delivery') == 'true'){
            unset($filter_by['status']);
            $waybill_numbers = Parcel::sanitizeWaybillNumbers($filter_by['waybill_number_arr']);
            return $this->response->sendSuccess(
                Parcel::getByWaybillNumberList($waybill_numbers, false, $fetch_with)
            );
        }
        //*/

        if (!is_null($report) && $report == 1) {
            $parcels = Parcel::getReportData($offset, $count, $filter_by, $fetch_with, $order_by);
        } else {
            $parcels = Parcel::fetchAll($offset, $count, $filter_by, $fetch_with, $order_by);
        }
        $result = [];
        if ($with_total_count != null) {
            $count = Parcel::parcelCount($filter_by);
            $result = [
                'total_count' => $count,
                'parcels' => $parcels
            ];
        } else {
            $result = $parcels;
        }

        return $this->response->sendSuccess($result);
    }

    public function getDeliverablePackagesAction(){
        $this->auth->allowOnly([Role::ADMIN, Role::OFFICER, Role::SWEEPER, Role::DISPATCHER, Role::GROUNDSMAN]);

        $from_branch_id = $this->request->getQuery("from_branch_id");
        $status = Status::PARCEL_ARRIVAL;

        $filter_by = [];
        $filter_by['to_branch_id'] = $from_branch_id;
        $filter_by['status'] = $status;
        $filter_by['send_all'] = true;

        $fetch_with = [];
        $fetch_with['with_receiver_address'] = true;

        $parcels = Parcel::fetchAll(0, 20, $filter_by, $fetch_with);

        $result = [];

        /** @var Branch $current_branch */
        $current_branch = Branch::fetchOne(['branch_id' => $from_branch_id]);

        $state_code = $current_branch['state']['code'];

        for($i = 0; $i < count($parcels); $i++){
            if($parcels[$i]['receiver_address']['state']['code'] == $state_code){
                $result[] = $parcels[$i];
            }
        }

        return $this->response->sendSuccess($result);
    }

    public function countAction()
    {
        $this->auth->allowOnly([Role::ADMIN, Role::OFFICER, Role::SWEEPER, Role::DISPATCHER, Role::GROUNDSMAN]);

        $filter_by = $this->getFilterParams();

        $count = Parcel::parcelCount($filter_by);
        if ($count === null) {
            return $this->response->sendError();
        }
        return $this->response->sendSuccess($count);
    }

    public function moveToForSweeperAction()
    {
        $this->auth->allowOnly([Role::OFFICER, Role::GROUNDSMAN]);

        $waybill_numbers = $this->request->getPost('waybill_numbers');
        $to_branch_id = $this->request->getPost('to_branch_id');
        $return_to_origin = $this->request->getPost('return_to_origin');

        if (in_array(null, [$waybill_numbers, $to_branch_id])) {
            return $this->response->sendError(ResponseMessage::ERROR_REQUIRED_FIELDS);
        }

        $waybill_number_arr = Parcel::sanitizeWaybillNumbers($waybill_numbers);

        $result = Parcel::bulkMoveToForSweeper($waybill_number_arr, $to_branch_id, $return_to_origin);

        return $this->response->sendSuccess($result);
    }

    public function bagAction()
    {
        $this->auth->allowOnly([Role::OFFICER]);

        $waybill_numbers = $this->request->getPost('waybill_numbers');
        $to_branch_id = $this->request->getPost('to_branch_id');
        $status = $this->request->getPost('status');
        $seal_id = $this->request->getPost('seal_id', null, '');

        if (in_array(null, [$waybill_numbers, $to_branch_id, $status])) {
            return $this->response->sendError(ResponseMessage::ERROR_REQUIRED_FIELDS);
        }

        $waybill_number_arr = Parcel::sanitizeWaybillNumbers($waybill_numbers);
        if (count($waybill_number_arr) == 0) {
            return $this->response->sendError(ResponseMessage::NO_PARCEL_TO_BAG);
        }

        //validate seal id
        if(Parcel::findFirst(["seal_id = '$seal_id'"])){
            return $this->response->sendError(ResponseMessage::SEAL_ID_IN_USE);
        }

        $auth_data = $this->auth->getData();

        try {
            $this->db->begin();
            $bag_info = Parcel::bagParcels($auth_data['branch']['id'], $to_branch_id, $this->auth->getPersonId(), $status, $waybill_number_arr, $seal_id);
        } catch (Exception $ex) {
            $this->db->rollback();
            return $this->response->sendError($ex->getMessage());
        }

        if ($bag_info != false) {
            $this->db->commit();
            return $this->response->sendSuccess($bag_info);
        }
        $this->db->rollback();
        return $this->response->sendError();
    }

    public function openBagAction()
    {
        $this->auth->allowOnly([Role::OFFICER]);

        $bag_waybill_number = $this->request->getPost('waybill_number');

        if (in_array(null, [$bag_waybill_number])) {
            return $this->response->sendError(ResponseMessage::ERROR_REQUIRED_FIELDS);
        }

        $check = Parcel::unbagParcels($bag_waybill_number);
        if ($check) {
            return $this->response->sendSuccess();
        }
        return $this->response->sendError();
    }

    /**
     * Used for removing parcel from a bag that has not been added to a manifest
     * @author Rahman Shitu <rahman@cottacush.com>
     * @return $this
     */
    public function removeFromBagAction()
    {
        /**
         * @var Parcel $parcel
         */

        $this->auth->allowOnly([Role::OFFICER, Role::ADMIN]);

        $bag_waybill_number = $this->request->getPost('bag_waybill_number');
        $parcel_waybill_number_arr = $this->request->getPost('parcel_waybill_number_list');

        if (in_array(null, [$bag_waybill_number, $parcel_waybill_number_arr])) {
            return $this->response->sendError(ResponseMessage::ERROR_REQUIRED_FIELDS);
        }

        $parcel_waybill_number_arr = Parcel::sanitizeWaybillNumbers($parcel_waybill_number_arr);
        if (empty($parcel_waybill_number_arr)) {
            return $this->response->sendError(ResponseMessage::NO_PARCEL_TO_REMOVE_FROM_BAG);
        }

        $bag = Parcel::findFirst([
            'waybill_number = :waybill_number: AND entity_type = :entity_type:',
            'bind' => ['waybill_number' => $bag_waybill_number, 'entity_type' => Parcel::ENTITY_TYPE_BAG]
        ]);

        if (empty($bag)) {
            return $this->response->sendError(ResponseMessage::BAG_DOES_NOT_EXIST);
        }

        //check if the bag has been added to a manifest
        $held_parcel = HeldParcel::findFirst([
            'parcel_id = :parcel_id:',
            'bind' => ['parcel_id' => $bag->getId()]
        ]);
        // return error if in a manifest
        if (!empty($held_parcel)) {
            return $this->response->sendError(ResponseMessage::BAG_IN_MANIFEST);
        }

        $parcel_arr = Parcel::getByWaybillNumberList($parcel_waybill_number_arr, true);
        $parcel_id_arr = [];
        foreach ($parcel_arr as $parcel) {
            $parcel_id_arr[] = $parcel->getId();
        }
        $link_arr = LinkedParcel::getByParentId($bag->getId(), $parcel_id_arr, true);

        $bad_parcel = [];
        $valid_parcel_id_arr = [];
        foreach ($parcel_waybill_number_arr as $waybill_number) {
            if (!isset($parcel_arr[$waybill_number])) {
                $bad_parcel[$waybill_number] = ResponseMessage::PARCEL_NOT_EXISTING;
                continue;
            }

            $parcel = $parcel_arr[$waybill_number];

            if (!isset($link_arr[$parcel->getId()])) {
                $bad_parcel[$waybill_number] = ResponseMessage::PARCEL_NOT_IN_BAG;
                continue;
            }

            $valid_parcel_id_arr[] = $parcel->getId();
        }

        $check = Parcel::removeFromBag($bag->getId(), $valid_parcel_id_arr);
        if ($check) {
            return $this->response->sendSuccess(['bad_parcels' => $bad_parcel]);
        }
        return $this->response->sendError(ResponseMessage::COULD_NOT_REMOVE_FROM_BAG);
    }

    public function moveToArrivalAction()
    {
        $this->auth->allowOnly([Role::OFFICER, Role::GROUNDSMAN]);

        $waybill_numbers = $this->request->getPost('waybill_numbers');
        $held_by_id = $this->request->getPost('held_by_id');


        $force_receive = $this->request->getPost('force_receive') === "true"?true:false;
        $previous_branch = $this->request->getPost('previous_branch');

        if (in_array(null, [$waybill_numbers, $held_by_id])) {
            return $this->response->sendError(ResponseMessage::ERROR_REQUIRED_FIELDS);
        }


        $waybill_number_arr = Parcel::sanitizeWaybillNumbers($waybill_numbers);
        $auth_data = $this->auth->getData();
        $current_branch_id = $auth_data['branch']['id'];


        /** @author Ademu Anthony */
        foreach (Parcel::getByWaybillNumberList($waybill_number_arr, true) as $parcel) {
            if($parcel->getEntityType() == Parcel::ENTITY_TYPE_BAG){
                $bagged_parcels = Parcel::getBag($parcel->getWaybillNumber())['parcels'];
                foreach ($bagged_parcels as $bagged_parcel) {
                    $waybill_number_arr[] = $bagged_parcel['waybill_number'];
                }
            }
        }

        /** @author Ademu Anthony */
        $bad_parcel = [];
        foreach ($waybill_number_arr as $waybill_number) {
            $parcel = Parcel::getByWaybillNumber($waybill_number);
            $will_receive_to_first_hub = false;

            if ($parcel === false) {
                $bad_parcel[$waybill_number] = ResponseMessage::PARCEL_NOT_EXISTING;
                continue;
            }

            //if the parcel have arrived and its currently in this branch
            if ($parcel->getStatus() == Status::PARCEL_ARRIVAL && $parcel->getToBranchId() == $current_branch_id) {
                $bad_parcel[$waybill_number] = ResponseMessage::PARCEL_ALREADY_IN_ARRIVAL;
                continue;
            }

            //do not check if force receiving
            if($force_receive == false){
                if ($parcel->getStatus() != Status::PARCEL_IN_TRANSIT) {
                    $bad_parcel[$waybill_number] = ResponseMessage::PARCEL_NOT_IN_TRANSIT;
                    continue;
                } else if ($parcel->getToBranchId() != $current_branch_id) {
                    $bad_parcel[$waybill_number] = ResponseMessage::PARCEL_WRONG_DESTINATION;
                    continue;
                }
            }else{
                if ($parcel->getToBranchId() == $current_branch_id && $parcel->getStatus() != Status::MANIFEST_IN_TRANSIT) {
                    $bad_parcel[$waybill_number] = ResponseMessage::PARCEL_ALREADY_IN_ARRIVAL;
                    continue;
                }else{
                    /** @Author Ademu Anthony If created but with customer*/
                    if($parcel->getStatus() == Status::PARCEL_CREATED_BUT_WITH_CUSTOMER){
                        $parcel->setCreatedBranchId($current_branch_id);
                        $parcel->setCreatedBy($this->auth->getPersonId());
                        $parcel->setIsVisible(1);
                        $parcel->setStatus(Status::PARCEL_FOR_SWEEPER);
                        $parcel->setFromBranchId($current_branch_id);
                        $parcel->save();
                        continue;
                    }
                }
            }

            //checking if the parcel is held by the correct person
            $held_parcel_record = HeldParcel::fetchUncleared($parcel->getId(), $held_by_id);

            if ($held_parcel_record == false) {
                if(!$force_receive){
                    $bad_parcel[$waybill_number] = ResponseMessage::PARCEL_HELD_BY_WRONG_OFFICIAL;
                    continue;
                }


                //get last manifest for this parcel
                $lastHistory = ParcelHistory::getLastHistoryForParcel($parcel->getId());
                //if parcel is in transit then it was not received into the first defaulter's hub. Receive it
                if($parcel->getStatus() == Status::PARCEL_IN_TRANSIT){
                    //get the manifest for the parcel
                    $last_held_record = HeldParcel::fetchUncleared($parcel->getId());

                    if($last_held_record){
                        if(!($parcel->checkIn($last_held_record, $this->auth->getPersonId()))){
                            $bad_parcel[$waybill_number] = ResponseMessage::PARCEL_NOT_CLEARED_FOR_TRANSIT.' In Previous Hub';
                            continue;
                        }
                    }
                }


                $lastHistoryToBranchId = $lastHistory['to_branch_id'];
                if(empty($previous_branch)){
                    $previous_branch = $lastHistoryToBranchId;
                }

                /**
                 *
                 */

                //move the parcel to the hand on the current held by
                $parcel->setToBranchId($previous_branch); // -- we can check here to see if the previous branch is the only defaulter
                $changed = $parcel->changeDestination(Status::PARCEL_FOR_SWEEPER, $current_branch_id, $auth_data['id'], ParcelHistory::MSG_FOR_SWEEPER);

                if($changed){
                    //-- create a manifest for the parcel
                    $manifested = Manifest::createOne([$parcel], '', $previous_branch,
                        $current_branch_id, $auth_data['id'], $held_by_id, Manifest::TYPE_SWEEP, true);

                    if($manifested){
                        $held_parcel_record = HeldParcel::fetchUncleared($parcel->getId(), $held_by_id);
                        //record defaulter
                        if($lastHistoryToBranchId != $previous_branch){
                            $shipmentExceptionDate =  array(
                                'defaulter_branch_id' => $lastHistoryToBranchId,
                                'detector_branch_id' => $current_branch_id,
                                'action_description' => ShipmentException::ACTION_DESCRIPTION_NOT_SENT,
                                'held_by_id' => $held_by_id,
                                'admin_id' => $auth_data['id'],
                                'creation_date' => date('Y-m-d H:i:s'),
                                'modification_date' => date('Y-m-d H:i:s'),
                                'parcel_id' => $parcel->getId()
                            );
                            ShipmentException::createOne($shipmentExceptionDate);
                        }
                        $shipmentExceptionDate =  array(
                            'defaulter_branch_id' => $previous_branch,
                            'detector_branch_id' => $current_branch_id,
                            'action_description' => ShipmentException::ACTION_DESCRIPTION_NOT_SENT,
                            'held_by_id' => $held_by_id,
                            'admin_id' => $auth_data['id'],
                            'creation_date' => date('Y-m-d H:i:s'),
                            'modification_date' => date('Y-m-d H:i:s'),
                            'parcel_id' => $parcel->getId()
                        );

                        ShipmentException::createOne($shipmentExceptionDate);
                    }
                }

            }

            $check = $parcel->checkIn($held_parcel_record, $this->auth->getPersonId());
            if (!$check) {
                $bad_parcel[$waybill_number] = ResponseMessage::PARCEL_CANNOT_BE_CLEARED;
                continue;
            }

        }

        return $this->response->sendSuccess(['bad_parcels' => $bad_parcel]);
    }

    private function moveToArrival($waybill_number, $force_receive, $held_by_id){
        $parcel = Parcel::getByWaybillNumber($waybill_number);
        $auth_data = $this->auth->getData();
        $current_branch_id = $auth_data['branch']['id'];
        $bad_parcel = [];

        if ($parcel === false) {
            $bad_parcel[$waybill_number] = ResponseMessage::PARCEL_NOT_EXISTING;
            return $bad_parcel;
        }

        //if the parcel have arrived and its currently in this branch
        if ($parcel->getStatus() == Status::PARCEL_ARRIVAL && $parcel->getToBranchId() == $current_branch_id) {
            $bad_parcel[$waybill_number] = ResponseMessage::PARCEL_ALREADY_IN_ARRIVAL;
            return $bad_parcel;
        }

        //do not check if force receiving
        if($force_receive == false){
            if ($parcel->getStatus() != Status::PARCEL_IN_TRANSIT) {
                $bad_parcel[$waybill_number] = ResponseMessage::PARCEL_NOT_IN_TRANSIT;
                return $bad_parcel;
            } else if ($parcel->getToBranchId() != $current_branch_id) {
                $bad_parcel[$waybill_number] = ResponseMessage::PARCEL_WRONG_DESTINATION;
                return $bad_parcel;
            }
        }else if ($parcel->getToBranchId() == $current_branch_id && $parcel->getStatus() != Status::MANIFEST_IN_TRANSIT) {
            $bad_parcel[$waybill_number] = ResponseMessage::PARCEL_ALREADY_IN_ARRIVAL;
            return $bad_parcel;
        }

        //checking if the parcel is held by the correct person
        $held_parcel_record = HeldParcel::fetchUncleared($parcel->getId(), $held_by_id);

        if ($held_parcel_record == false) {
            if(!$force_receive){
                $bad_parcel[$waybill_number] = ResponseMessage::PARCEL_HELD_BY_WRONG_OFFICIAL;
                return $bad_parcel;
            }


            //get last manifest for this parcel
            $lastHistory = ParcelHistory::getLastHistoryForParcel($parcel->getId());
            //if parcel is in transit then it was not received into the first defaulter's hub. Receive it
            if($parcel->getStatus() == Status::PARCEL_IN_TRANSIT){
                //get the manifest for the parcel
                $last_held_record = HeldParcel::fetchUncleared($parcel->getId());

                if($last_held_record){
                    if(!($parcel->checkIn($last_held_record, $this->auth->getPersonId()))){
                        $bad_parcel[$waybill_number] = ResponseMessage::PARCEL_NOT_CLEARED_FOR_TRANSIT.' In Previous Hub';
                        return $bad_parcel;
                    }
                }
            }


            $lastHistoryToBranchId = $lastHistory['to_branch_id'];
            if(empty($previous_branch)){
                $previous_branch = $lastHistoryToBranchId;
            }

            /**
             *
             */

            //move the parcel to the hand on the current held by
            $parcel->setToBranchId($previous_branch); // -- we can check here to see if the previous branch is the only defaulter
            $changed = $parcel->changeDestination(Status::PARCEL_FOR_SWEEPER, $current_branch_id, $auth_data['id'], ParcelHistory::MSG_FOR_SWEEPER);

            if($changed){
                //-- create a manifest for the parcel
                $manifested = Manifest::createOne([$parcel], '', $previous_branch,
                    $current_branch_id, $auth_data['id'], $held_by_id, Manifest::TYPE_SWEEP, true);

                if($manifested){
                    $held_parcel_record = HeldParcel::fetchUncleared($parcel->getId(), $held_by_id);
                    //record defaulter
                    if($lastHistoryToBranchId != $previous_branch){
                        $shipmentExceptionDate =  array(
                            'defaulter_branch_id' => $lastHistoryToBranchId,
                            'detector_branch_id' => $current_branch_id,
                            'action_description' => ShipmentException::ACTION_DESCRIPTION_NOT_SENT,
                            'held_by_id' => $held_by_id,
                            'admin_id' => $auth_data['id'],
                            'creation_date' => date('Y-m-d H:i:s'),
                            'modification_date' => date('Y-m-d H:i:s'),
                            'parcel_id' => $parcel->getId()
                        );
                        ShipmentException::createOne($shipmentExceptionDate);
                    }
                    $shipmentExceptionDate =  array(
                        'defaulter_branch_id' => $previous_branch,
                        'detector_branch_id' => $current_branch_id,
                        'action_description' => ShipmentException::ACTION_DESCRIPTION_NOT_SENT,
                        'held_by_id' => $held_by_id,
                        'admin_id' => $auth_data['id'],
                        'creation_date' => date('Y-m-d H:i:s'),
                        'modification_date' => date('Y-m-d H:i:s'),
                        'parcel_id' => $parcel->getId()
                    );

                    ShipmentException::createOne($shipmentExceptionDate);
                }
            }

        }

        $check = $parcel->checkIn($held_parcel_record, $this->auth->getPersonId());
        if (!$check) {
            $bad_parcel[$waybill_number] = ResponseMessage::PARCEL_CANNOT_BE_CLEARED;
        }
        return $bad_parcel;
    }

    public function createDirectManifestAction(){
        $waybill_numbers = $this->request->getPost('waybill_numbers');
        $to_branch_id = $this->request->getPost('to_branch_id');
        $label = $this->request->getPost('label');
        $admin_id = $this->request->getPost('admin_id');
        $held_by = $this->request->getPost('held_by_id');

        if(in_array(null, [$waybill_numbers, $to_branch_id, $admin_id, $held_by])){
            return $this->response->sendError(ResponseMessage::ERROR_REQUIRED_FIELDS);
        }

        //sort the parcels
        $waybill_number_arr = Parcel::sanitizeWaybillNumbers($waybill_numbers);

        $soft_result = Parcel::bulkMoveToForSweeper($waybill_number_arr, $to_branch_id);

        if(count($soft_result['bad_parcels'])){
            foreach ($soft_result as $key => $value) {
                unset($waybill_number_arr[$key]);
            }
        }


        $held_by_id = (in_array($this->auth->getUserType(), [Role::SWEEPER, Role::DISPATCHER])) ? $this->auth->getPersonId() : $this->request->getPost('held_by_id');
        $admin_id = (in_array($this->auth->getUserType(), [Role::SWEEPER, Role::OFFICER, Role::GROUNDSMAN])) ? $this->auth->getPersonId() : $this->request->getPost('admin_id');
        $label = $this->request->getPost('label', null, '');

        $other_id = (in_array($this->auth->getUserType(), [Role::OFFICER, Role::GROUNDSMAN])) ? $held_by_id : $admin_id;
        $other = Admin::getById($other_id);

        if ($other != false) {
            //check if officer is valid
            if (in_array($this->auth->getUserType(), [Role::DISPATCHER]) && !in_array($other->getRoleId(), [Role::SWEEPER, Role::OFFICER, Role::GROUNDSMAN])) {
                return $this->response->sendError(ResponseMessage::INVALID_OFFICER);
            }

            //check if sweeper or dispatcher is valid
            if (!in_array($other->getRoleId(), [Role::SWEEPER, Role::DISPATCHER]) && in_array($this->auth->getUserType(), [Role::OFFICER, Role::GROUNDSMAN])) {
                return $this->response->sendError(ResponseMessage::INVALID_SWEEPER_OR_DISPATCHER);
            }

        } else {
            //if currently logged in user is a dispatcher or sweeper the officer is invalid
            if (in_array($this->auth->getUserType(), [Role::SWEEPER, Role::DISPATCHER])) {
                return $this->response->sendError(ResponseMessage::INVALID_OFFICER);

                //if currently logged in user is an officer or groundsman the sweeper or dispatcher is invalid
            } else if (in_array($this->auth->getUserType(), [Role::OFFICER, Role::GROUNDSMAN])) {
                return $this->response->sendError(ResponseMessage::INVALID_SWEEPER_OR_DISPATCHER);
            }
        }

        $auth_data = $this->auth->getData();
        $user_branch_id = $auth_data['branch_id']; // logged on user's branch

        $parcel_arr = Parcel::getByWaybillNumberList($waybill_number_arr, true);
        $bad_parcel = [];

        $from_branch_id = (in_array($this->auth->getUserType(), [Role::OFFICER, Role::GROUNDSMAN])) ? $user_branch_id : $other->getBranchId();

        /**
         * @var Parcel $parcel
         */
        foreach ($waybill_number_arr as $waybill_number) {
            if (!isset($parcel_arr[$waybill_number])) {
                $bad_parcel[$waybill_number] = ResponseMessage::PARCEL_NOT_EXISTING;
                continue;
            }

            $parcel = $parcel_arr[$waybill_number];

            if ($parcel->getStatus() == Status::PARCEL_IN_TRANSIT) {
                $bad_parcel[$waybill_number] = ResponseMessage::PARCEL_ALREADY_IN_TRANSIT;
            } else if ($parcel->getFromBranchId() != $from_branch_id) {
                //if the from branch is not this branch, then the shipment is yet to be received. So receive it first

                $bad_parcel[$waybill_number] = ResponseMessage::PARCEL_NOT_IN_OFFICER_BRANCH;
            } else if ($parcel->getStatus() != Status::PARCEL_FOR_SWEEPER) {
                $bad_parcel[$waybill_number] = ResponseMessage::PARCEL_NOT_FOR_SWEEPING;
            } else if ($parcel->getToBranchId() != $to_branch_id) {
                $bad_parcel[$waybill_number] = ResponseMessage::PARCEL_NOT_HEADING_TO_DESTINATION;
            } else if (!HeldParcel::clearedForMovement($parcel->getId())) {
                $bad_parcel[$waybill_number] = ResponseMessage::PARCEL_NOT_CLEARED_FOR_TRANSIT;
            }

            //Removing bad parcel before adding to manifest
            if (isset($bad_parcel[$waybill_number])) {
                unset($parcel_arr[$waybill_number]);
            }

            //if it is a bag, get content and add then to the parcel arr
            if($parcel->getEntityType() == Parcel::ENTITY_TYPE_BAG){
                $bagged_parcels = Parcel::getBag($parcel->getWaybillNumber())['parcels'];
                $waybill_number2s = [];
                foreach ($bagged_parcels as $item) {
                    $waybill_number2s[] =  $item['waybill_number'];
                }
                $parcel_arr = array_merge($parcel_arr, Parcel::getByWaybillNumberList($waybill_number2s, true));
            }

        }


        $check = Manifest::createOne($parcel_arr, $label, $from_branch_id, $to_branch_id, $admin_id, $held_by_id, Manifest::TYPE_SWEEP);
        if ($check == false) {
            return $this->response->sendError();
        }


        /** @var Manifest $manifest */
        $manifest = isset($check['manifest']) ? $check['manifest'] : null;
        $bad_parcels = array_merge($check['bad_parcels'], $bad_parcel);

        //send out notification for parcels in transit that are not bad
        $admin = Admin::findFirst($admin_id);

        if ($admin && !is_null($manifest)) {
            /** @var  $originBranch  Branch */
            $originBranch = Branch::findFirst($from_branch_id);
            /** @var  $destinationBranch Branch */
            $destinationBranch = Branch::findFirst($to_branch_id);
            EmailMessage::send(
                EmailMessage::PARCEL_IN_TRANSIT,
                [
                    'fullname' => ucwords($admin->getFullname()),
                    'manifest_label' => $manifest->getLabel(),
                    'origin' => $originBranch->getName(),
                    'destination' => $destinationBranch->getName()
                ],
                'Courier Plus',
                $admin->getEmail()
            );
        }

        //set response data
        $check['manifest_id'] = null;
        if (!is_null($manifest)) {
            $check['manifest'] = $manifest->getData();
            $check['manifest_id'] = $manifest->getId();
        }
        $check['bad_parcels'] = $bad_parcels;
        return $this->response->sendSuccess($check);




    }

    /**
     * Move package to in transit
     * @author Adeyemi Olaoye <yemi@cottacush.com>
     * @author Rahman Shitu <rahman@cottacush.com>
     * @author Olawale Lawal <wale@cottacush.com>
     * @return $this
     */
    public function moveToInTransitAction()
    {
        $this->auth->allowOnly([Role::SWEEPER, Role::OFFICER, Role::DISPATCHER, Role::GROUNDSMAN]);

        $waybill_numbers = $this->request->getPost('waybill_numbers');
        $to_branch_id = $this->request->getPost('to_branch_id');
        $held_by_id = (in_array($this->auth->getUserType(), [Role::SWEEPER, Role::DISPATCHER])) ? $this->auth->getPersonId() : $this->request->getPost('held_by_id');
        $admin_id = (in_array($this->auth->getUserType(), [Role::SWEEPER, Role::OFFICER, Role::GROUNDSMAN])) ? $this->auth->getPersonId() : $this->request->getPost('admin_id');
        $label = $this->request->getPost('label', null, '');

        if (!isset($waybill_numbers, $to_branch_id, $held_by_id, $admin_id)) {
            return $this->response->sendError(ResponseMessage::ERROR_REQUIRED_FIELDS);
        }

        $other_id = (in_array($this->auth->getUserType(), [Role::OFFICER, Role::GROUNDSMAN])) ? $held_by_id : $admin_id;
        $other = Admin::getById($other_id);

        if ($other != false) {
            //check if officer is valid
            if (in_array($this->auth->getUserType(), [Role::DISPATCHER]) && !in_array($other->getRoleId(), [Role::SWEEPER, Role::OFFICER, Role::GROUNDSMAN])) {
                return $this->response->sendError(ResponseMessage::INVALID_OFFICER);
            }

            //check if sweeper or dispatcher is valid
            if (!in_array($other->getRoleId(), [Role::SWEEPER, Role::DISPATCHER]) && in_array($this->auth->getUserType(), [Role::OFFICER, Role::GROUNDSMAN])) {
                return $this->response->sendError(ResponseMessage::INVALID_SWEEPER_OR_DISPATCHER);
            }

        } else {
            //if currently logged in user is a dispatcher or sweeper the officer is invalid
            if (in_array($this->auth->getUserType(), [Role::SWEEPER, Role::DISPATCHER])) {
                return $this->response->sendError(ResponseMessage::INVALID_OFFICER);

                //if currently logged in user is an officer or groundsman the sweeper or dispatcher is invalid
            } else if (in_array($this->auth->getUserType(), [Role::OFFICER, Role::GROUNDSMAN])) {
                return $this->response->sendError(ResponseMessage::INVALID_SWEEPER_OR_DISPATCHER);
            }
        }

        $waybill_number_arr = Parcel::sanitizeWaybillNumbers($waybill_numbers);
        $auth_data = $this->auth->getData();
        $user_branch_id = $auth_data['branch_id']; // logged on user's branch

        $parcel_arr = Parcel::getByWaybillNumberList($waybill_number_arr, true);
        $bad_parcel = [];

        $from_branch_id = (in_array($this->auth->getUserType(), [Role::OFFICER, Role::GROUNDSMAN])) ? $user_branch_id : $other->getBranchId();

        /**
         * @var Parcel $parcel
         */
        foreach ($waybill_number_arr as $waybill_number) {
            if (!isset($parcel_arr[$waybill_number])) {
                $bad_parcel[$waybill_number] = ResponseMessage::PARCEL_NOT_EXISTING;
                continue;
            }

            $parcel = $parcel_arr[$waybill_number];

            if ($parcel->getStatus() == Status::PARCEL_IN_TRANSIT) {
                $bad_parcel[$waybill_number] = ResponseMessage::PARCEL_ALREADY_IN_TRANSIT;
            } else if ($parcel->getFromBranchId() != $from_branch_id) {
                $bad_parcel[$waybill_number] = ResponseMessage::PARCEL_NOT_IN_OFFICER_BRANCH;
            } else if ($parcel->getStatus() != Status::PARCEL_FOR_SWEEPER) {
                $bad_parcel[$waybill_number] = ResponseMessage::PARCEL_NOT_FOR_SWEEPING;
            } else if ($parcel->getToBranchId() != $to_branch_id) {
                $bad_parcel[$waybill_number] = ResponseMessage::PARCEL_NOT_HEADING_TO_DESTINATION;
            } else if (!HeldParcel::clearedForMovement($parcel->getId())) {
                $bad_parcel[$waybill_number] = ResponseMessage::PARCEL_NOT_CLEARED_FOR_TRANSIT;
            }

            //Removing bad parcel before adding to manifest
            if (isset($bad_parcel[$waybill_number])) {
                unset($parcel_arr[$waybill_number]);
            }

            //if it is a bag, get content and add then to the parcel arr
            if($parcel->getEntityType() == Parcel::ENTITY_TYPE_BAG){
                $bagged_parcels = Parcel::getBag($parcel->getWaybillNumber())['parcels'];
                $waybill_number2s = [];
                foreach ($bagged_parcels as $item) {
                    $waybill_number2s[] =  $item['waybill_number'];
                }
                $parcel_arr = array_merge($parcel_arr, Parcel::getByWaybillNumberList($waybill_number2s, true));
            }

        }


        $check = Manifest::createOne($parcel_arr, $label, $from_branch_id, $to_branch_id, $admin_id, $held_by_id, Manifest::TYPE_SWEEP);
        if ($check == false) {
            return $this->response->sendError();
        }


        /** @var Manifest $manifest */
        $manifest = isset($check['manifest']) ? $check['manifest'] : null;
        $bad_parcels = array_merge($check['bad_parcels'], $bad_parcel);

        //send out notification for parcels in transit that are not bad
        $admin = Admin::findFirst($admin_id);

        if ($admin && !is_null($manifest)) {
            /** @var  $originBranch  Branch */
            $originBranch = Branch::findFirst($from_branch_id);
            /** @var  $destinationBranch Branch */
            $destinationBranch = Branch::findFirst($to_branch_id);
            EmailMessage::send(
                EmailMessage::PARCEL_IN_TRANSIT,
                [
                    'fullname' => ucwords($admin->getFullname()),
                    'manifest_label' => $manifest->getLabel(),
                    'origin' => $originBranch->getName(),
                    'destination' => $destinationBranch->getName()
                ],
                'Courier Plus',
                $admin->getEmail()
            );
        }

        //set response data
        $check['manifest_id'] = null;
        if (!is_null($manifest)) {
            $check['manifest'] = $manifest->getData();
            $check['manifest_id'] = $manifest->getId();
        }
        $check['bad_parcels'] = $bad_parcels;
        return $this->response->sendSuccess($check);
    }

    public function moveToForDeliveryAction()
    {
        $this->auth->allowOnly([Role::OFFICER, Role::GROUNDSMAN]);

        $waybill_numbers = $this->request->getPost('waybill_numbers');
        $route_id = $this->request->getPost('route_id');
        //$enforce_action = $this->request->getPost('enforce_action');
        $admin_id = $this->auth->getPersonId();


        $auth_data = $this->auth->getData();

        if (is_null($waybill_numbers)) {
            return $this->response->sendError(ResponseMessage::ERROR_REQUIRED_FIELDS);
        }

        if (!is_null($route_id)) {
            $route = Route::findFirst($route_id);
            if (!$route) {
                return $this->response->sendError(ResponseMessage::INVALID_ROUTE);
            } else if ($route->getData()['branch_id'] != $auth_data['branch']['id']) {
                return $this->response->sendError(ResponseMessage::WRONG_ROUTE);
            }
        }

        $waybill_number_arr = Parcel::sanitizeWaybillNumbers($waybill_numbers);

        $bad_parcel = [];
        foreach ($waybill_number_arr as $waybill_number) {
            $parcel = Parcel::getByWaybillNumber($waybill_number);
            if ($parcel === false) {
                $bad_parcel[$waybill_number] = ResponseMessage::PARCEL_NOT_EXISTING;
                continue;
            } else if ($parcel->getStatus() == Status::PARCEL_FOR_DELIVERY) {
                $bad_parcel[$waybill_number] = ResponseMessage::PARCEL_ALREADY_FOR_DELIVERY;
                continue;

                //ensure parcel is in arrival or for groundsman or parcel is for return and is in transit.. And not enforcing
            } else if ((!in_array($parcel->getStatus(), [Status::PARCEL_ARRIVAL, Status::PARCEL_FOR_GROUNDSMAN]) && !($parcel->getForReturn() == '1' && $parcel->getStatus() == Status::PARCEL_IN_TRANSIT))) {
                $bad_parcel[$waybill_number] = ResponseMessage::PARCEL_NOT_FROM_ARRIVAL;
                continue;
            } else if (($parcel->getToBranchId() != $auth_data['branch']['id'])) {
                $bad_parcel[$waybill_number] = ResponseMessage::PARCEL_WRONG_DESTINATION;
                continue;
            }

            if (!is_null($route_id)) {
                $parcel->setRouteId($route_id);
            }

            $status = Status::PARCEL_FOR_DELIVERY;
            if($parcel->getReturnStatus() == Status::RETURNING_TO_ORIGIN){
                $parcel->setReturnStatus(Status::RETURN_READY_FOR_PICKUP);
                $status = Status::PARCEL_BEING_DELIVERED;
            }
            $check = $parcel->changeStatus($status, $admin_id, ParcelHistory::MSG_FOR_DELIVERY, $auth_data['branch_id']);
            if (!$check) {
                $bad_parcel[$waybill_number] = ResponseMessage::CANNOT_MOVE_PARCEL;
                continue;
            }
        }

        return $this->response->sendSuccess(['bad_parcels' => $bad_parcel]);
    }

    /**
     * @author Adeyemi Olaoye <yemi@cottacush.com>
     * @author Rahman Shitu <rahman@cottacush.com>
     * @author Olawale Lawal <wale@cottacush.com>
     * @return $this
     */
    public function moveToBeingDeliveredAction()
    {
        $this->auth->allowOnly([Role::OFFICER, Role::DISPATCHER, Role::SWEEPER, Role::GROUNDSMAN]);

        $waybill_numbers = $this->request->getPost('waybill_numbers');
        $held_by_id = (in_array($this->auth->getUserType(), [Role::SWEEPER, Role::DISPATCHER])) ? $this->auth->getPersonId() : $this->request->getPost('held_by_id');
        $admin_id = (in_array($this->auth->getUserType(), [Role::OFFICER, Role::GROUNDSMAN])) ? $this->auth->getPersonId() : $this->request->getPost('admin_id');
        $label = $this->request->getPost('label', null, '');

        if (in_array(null, [$waybill_numbers, $held_by_id, $admin_id])) {
            return $this->response->sendError(ResponseMessage::ERROR_REQUIRED_FIELDS);
        }

        $other_id = (in_array($this->auth->getUserType(), [Role::OFFICER, Role::GROUNDSMAN])) ? $held_by_id : $admin_id;
        $other = Admin::getById($other_id);

        if ($other != false) {
            //check if officer is valid
            if (in_array($this->auth->getUserType(), [Role::SWEEPER, Role::DISPATCHER]) && !in_array($other->getRoleId(), [Role::OFFICER, Role::GROUNDSMAN])) {
                return $this->response->sendError(ResponseMessage::INVALID_OFFICER);
            }

            //check if sweeper or dispatcher is valid
            if (!in_array($other->getRoleId(), [Role::SWEEPER, Role::DISPATCHER]) && in_array($this->auth->getUserType(), [Role::OFFICER, Role::GROUNDSMAN])) {
                return $this->response->sendError(ResponseMessage::INVALID_SWEEPER_OR_DISPATCHER);
            }

        } else {
            //if currently logged in user is a dispatcher or sweeper the officer is invalid
            if (in_array($this->auth->getUserType(), [Role::SWEEPER, Role::DISPATCHER])) {
                return $this->response->sendError(ResponseMessage::INVALID_OFFICER);

                //if currently logged in user is an officer or groundsman the sweeper or dispatcher is invalid
            } else if (in_array($this->auth->getUserType(), [Role::OFFICER, Role::GROUNDSMAN])) {
                return $this->response->sendError(ResponseMessage::INVALID_SWEEPER_OR_DISPATCHER);
            }
        }

        $waybill_number_arr = Parcel::sanitizeWaybillNumbers($waybill_numbers);
        $auth_data = $this->auth->getData();
        $user_branch_id = $auth_data['branch_id']; // logged on user's branch

        $parcel_arr = Parcel::getByWaybillNumberList($waybill_number_arr, true);
        $bad_parcel = [];

        /**
         * @var Parcel $parcel
         */
        foreach ($waybill_number_arr as $waybill_number) {
            if (!isset($parcel_arr[$waybill_number])) {
                $bad_parcel[$waybill_number] = ResponseMessage::PARCEL_NOT_EXISTING;
                continue;
            }

            $parcel = $parcel_arr[$waybill_number];
            //set both from and to beranch to the current branch
            $parcel->setFromBranchId($parcel->getToBranchId());

            if ($parcel->getStatus() == Status::PARCEL_BEING_DELIVERED) {
                $bad_parcel[$waybill_number] = ResponseMessage::PARCEL_ALREADY_FOR_BEING_DELIVERED;
            } else if ($parcel->getStatus() != Status::PARCEL_FOR_DELIVERY) {
                //$bad_parcel[$waybill_number] = ResponseMessage::PARCEL_NOT_FOR_DELIVERY;
            } else if ($parcel->getToBranchId() != $user_branch_id) {
                //$bad_parcel[$waybill_number] = ResponseMessage::PARCEL_NOT_IN_OFFICE;
            }

            //Removing bad parcel before adding to manifest
            if (isset($bad_parcel[$waybill_number])) {
                unset($parcel_arr[$waybill_number]);
            }
        }

        $check = Manifest::createOne($parcel_arr, $label, $user_branch_id, $user_branch_id, $admin_id, $held_by_id, Manifest::TYPE_DELIVERY);
        if ($check == false) {
            return $this->response->sendError();
        }

        /** @var Manifest $manifest */
        $manifest = isset($check['manifest']) ? $check['manifest'] : null;
        $bad_parcels = array_merge($check['bad_parcels'], $bad_parcel);

        $admin = Admin::findFirst($admin_id);


        //send notification if manifest was created
        if ($admin && !is_null($manifest)) {
            /** @var  $originBranch  Branch */
            $originBranch = Branch::findFirst($user_branch_id);
            /** @var  $destinationBranch Branch */
            $destinationBranch = Branch::findFirst($user_branch_id);
            EmailMessage::send(
                EmailMessage::PARCEL_IN_TRANSIT,
                [
                    'fullname' => ucwords($admin->getFullname()),
                    'manifest_label' => $manifest->getLabel(),
                    'origin' => $originBranch->getName(),
                    'destination' => $destinationBranch->getName()
                ],
                'Courier Plus',
                $admin->getEmail()
            );
        }

        //set response data
        $check['manifest_id'] = null;
        if (!is_null($manifest)) {
            $check['manifest'] = $manifest->getData();
            $check['manifest_id'] = $manifest->getId();
        }
        $check['bad_parcels'] = $bad_parcels;
        return $this->response->sendSuccess($check);
    }

    /**
     * @author Adeyemi Olaoye <yemi@cottacush.com>
     * @author Rahman Shitu <rahman@cottacush.com>
     * @author Olawale Lawal <wale@cottacush.com>
     * @return $this
     */
    public function moveToDeliveredAction()
    {
        $this->auth->allowOnly([Role::ADMIN, Role::OFFICER, Role::DISPATCHER, Role::SWEEPER, Role::GROUNDSMAN]);

        $waybill_numbers = $this->request->getPost('waybill_numbers');

        //get receipt information
        $receiver_name = $this->request->getPost('receiver_name');
        $receiver_phonenumber = $this->request->getPost('receiver_phone_number');
        $receiver_email = $this->request->getPost('receiver_email', null, null);
        $date_and_time_of_delivery = $this->request->getPost('date_and_time_of_delivery', null, Util::getCurrentDateTime());
        $admin_id = $this->auth->getPersonId();
        /** @author Ademu Anthony */
        $enforce_action = $this->request->getPost('enforce_action');

        if (in_array(null, [$waybill_numbers, $admin_id])) {
            return $this->response->sendError(ResponseMessage::ERROR_REQUIRED_FIELDS);
        }

        $waybill_number_arr = Parcel::sanitizeWaybillNumbers($waybill_numbers);
        $auth_data = $this->auth->getData();

        $bad_parcel = [];
        $admin = Admin::findFirst($admin_id);
        foreach ($waybill_number_arr as $waybill_number) {
            $parcel = Parcel::getByWaybillNumber($waybill_number);
            if ($parcel === false) {
                $bad_parcel[$waybill_number] = ResponseMessage::PARCEL_NOT_EXISTING;
                continue;
            }

            if ($parcel->getStatus() == Status::PARCEL_DELIVERED) {
                $bad_parcel[$waybill_number] = ResponseMessage::PARCEL_ALREADY_DELIVERED;
                continue;
            } else if ($parcel->getStatus() != Status::PARCEL_BEING_DELIVERED) {
                if($enforce_action){
                    if($parcel->getStatus() == Status::PARCEL_IN_TRANSIT){
                        //force receive
                    }
                }else{
                    $bad_parcel[$waybill_number] = ResponseMessage::PARCEL_NOT_FOR_DELIVERY;
                    continue;
                }

            }

            $check = $parcel->changeStatus(Status::PARCEL_DELIVERED, $admin_id, ParcelHistory::MSG_DELIVERED, $auth_data['branch_id']);
            if (!$check) {
                $bad_parcel[$waybill_number] = ResponseMessage::CANNOT_MOVE_PARCEL;
                continue;
            }

            //get recipients
            $recipients = [];
            if ($admin) {
                $recipients[$admin->getEmail()] = $admin->getFullname();
            }
            $sender = User::findFirst($parcel->getSenderId());
            if ($sender) {
                $recipients[$sender->getEmail()] = $sender->getFirstname();
            }

            $receiver = User::findFirst($parcel->getReceiverId());
            if ($receiver) {
                $recipients[$receiver->getEmail()] = $receiver->getFirstname();
            }

            foreach ($recipients as $email => $name) {
                EmailMessage::send(
                    EmailMessage::PARCEL_DELIVERED,
                    [
                        'firstname' => ucwords($name),
                        'waybill_number' => $waybill_number,
                    ],
                    'Courier Plus',
                    $email
                );
            }

            //create delivery receipt if receiver name and phone number was supplied
            if (isset($receiver_name)) {
                if (!DeliveryReceipt::doesReceiptExist($waybill_number, DeliveryReceipt::RECEIPT_TYPE_RECEIVER_DETAIL)) {
                    $data = [
                        'waybill_number' => $waybill_number,
                        'receipt_type' => DeliveryReceipt::RECEIPT_TYPE_RECEIVER_DETAIL,
                        'delivered_by' => $this->auth->getPersonId(),
                        'name' => $receiver_name,
                        'delivered_at' => $date_and_time_of_delivery
                    ];
                }
                if (isset($receiver_email)) {
                    $data['email'] = $receiver_email;
                }
                if (isset($receiver_phonenumber)) {
                    $data['phone_number'] = $receiver_phonenumber;
                }

                DeliveryReceipt::add($data);
            }

            //TODO send email to operations team
        }

        return $this->response->sendSuccess(['bad_parcels' => $bad_parcel]);
    }

    /**
     * Marks a shipment as CANCELLED
     *
     * @author  Olawale Lawal
     * @return array
     */
    public function cancelAction()
    {
        $this->auth->allowOnly([Role::OFFICER, Role::ADMIN, Role::COMPANY_ADMIN]);

        $waybill_numbers = $this->request->getPost('waybill_numbers');
        $admin_id = $this->auth->getPersonId();

        if (!isset($waybill_numbers, $admin_id)) {
            return $this->response->sendError(ResponseMessage::ERROR_REQUIRED_FIELDS);
        }

        $waybill_number_arr = Parcel::sanitizeWaybillNumbers($waybill_numbers);
        $auth_data = $this->auth->getData();

        $bad_parcel = [];
        foreach ($waybill_number_arr as $waybill_number) {
            $parcel = Parcel::getByWaybillNumber($waybill_number);
            if ($parcel === false) {
                $bad_parcel[$waybill_number] = ResponseMessage::PARCEL_NOT_EXISTING;
                continue;
            }

            if ($parcel->getStatus() == Status::PARCEL_CANCELLED) {
                $bad_parcel[$waybill_number] = ResponseMessage::PARCEL_ALREADY_CANCELLED;
                continue;
            } else if (!in_array($parcel->getStatus(), [Status::PARCEL_FOR_SWEEPER, Status::PARCEL_FOR_DELIVERY])) {
                $bad_parcel[$waybill_number] = ResponseMessage::PARCEL_CANNOT_BE_CANCELLED;
                continue;
            }

            if($this->auth->isCooperateUser()){
                $check = $parcel->changeStatus(Status::PARCEL_CANCELLED, $parcel->getCreatedBy(), ParcelHistory::MSG_CANCELLED, $parcel->getCreatedBranchId(), true);
            }else{
                $check = $parcel->changeStatus(Status::PARCEL_CANCELLED, $admin_id, ParcelHistory::MSG_CANCELLED, $auth_data['branch_id'], true);
            }



            if (!$check) {
                $bad_parcel[$waybill_number] = ResponseMessage::PARCEL_CANNOT_BE_CANCELLED;
                continue;
            }
        }
        return $this->response->sendSuccess(['bad_parcels' => $bad_parcel]);
    }

    /**
     * Marks a shipment for GROUNDSMAN
     *
     * @author  Olawale Lawal
     * @return array
     */
    public function assignToGroundsmanAction()
    {
        $this->auth->allowOnly([Role::OFFICER, Role::ADMIN]);

        $waybill_numbers = $this->request->getPost('waybill_numbers');

        if (!isset($waybill_numbers)) {
            return $this->response->sendError(ResponseMessage::ERROR_REQUIRED_FIELDS);
        }

        $waybill_number_arr = Parcel::sanitizeWaybillNumbers($waybill_numbers);

        $result = Parcel::bulkAssignToGroundsman($waybill_number_arr);
        return $this->response->sendSuccess($result);
    }

    /**
     * Return the history of parcel
     * @author Adeyemi Olaoye <yemi@cottacush.com>
     * @author Rahman Shitu <rahman@cottacush.com>
     * @author Olawale Lawal <wale@cottacush.com>
     * @return $this
     */


    public function historyAction()
    {
        $offset = $this->request->getQuery('offset', null, DEFAULT_OFFSET);
        $count = $this->request->getQuery('count', null, DEFAULT_COUNT);
        //$for_scanner = $this->request->getQuery('for_scanner', null, DEFAULT_COUNT);

        $filter_params = [
            'waybill_number', 'parcel_id', 'paginate', 'status', 'reference_number', 'order_number'
        ];

        $fetch_params = ['with_admin'];

        //todo: make this block a function
        //------------------------------------------
        $possible_params = array_merge($filter_params, $fetch_params);

        foreach ($possible_params as $param) {
            $$param = $this->request->getQuery($param);
        }

        $filter_by = [];
        foreach ($filter_params as $param) {
            if (!is_null($$param)) {
                $filter_by[$param] = $$param;
            }
        }

        $fetch_with = [];
        foreach ($fetch_params as $param) {
            if (!is_null($$param)) {
                $fetch_with[$param] = true;
            }
        }

        $parcelHistory = ParcelHistory::fetchAll($offset, $count, $filter_by, $fetch_with);

        $parcelHistory_for_scanner = [];
        /*
         * if($for_scanner == 1){
            foreach ($parcelHistory as $parcelHistory) {
                $parcelHistory_for_scanner[] = $parcelHistory;
            }
        }
        */
        if (!empty($parcelHistory)) {
            return $this->response->sendSuccess($parcelHistory);
        }
        return $this->response->sendError(ResponseMessage::PARCEL_NOT_EXISTING);
    }

    /**
     * Add a parcel delivery receipt
     * @author Adeyemi Olaoye <yemi@cottacush.com>
     */
    public function createParcelDeliveryReceiptAction()
    {
        $waybill_number = $this->request->getPost('waybill_number', null, null);
        $delivered_by = $this->request->getPost('delivered_by', null, null);
        $email = $this->request->getPost('email', null, null);
        $phone_number = $this->request->getPost('phone_number', null, null);
        $name = $this->request->getPost('name', null, null);

        $requiredFields = ['waybill_number', 'delivered_by', 'phone_number', 'name'];
        $data = ['waybill_number' => $waybill_number, 'delivered_by' => $delivered_by, 'phone_number' => $phone_number, 'name' => $name];

        $requestValidator = new RequestValidation($data);
        $requestValidator->setRequiredFields($requiredFields);
        if (!$requestValidator->validate()) {
            return $this->response->sendError($requestValidator->getMessages());
        }

        $data['email'] = $email;

        //check if parcel with waybill_number exists
        $parcel = Parcel::findFirstByWaybillNumber($waybill_number);
        if (!$parcel) {
            return $this->response->sendError('Parcel with waybill number does not exist');
        }

        //check if delivering user exists
        $user = Admin::findFirst($delivered_by);
        if (!$user) {
            return $this->response->sendError('Invalid delivery user');
        }

        //check if post has files
        if (!$this->request->hasFiles()) {
            return $this->response->sendError('signature or snapshot file required');
        }

        $allowedExtensions = ['png', 'jpg', 'jpeg'];
        $receipt_paths = [];

        /** @var \Phalcon\Http\Request\File $file */
        foreach ($this->request->getUploadedFiles() as $file) {
            if (in_array($file->getKey(), ['signature', 'snapshot'])) {
                if (in_array($file->getExtension(), $allowedExtensions)) {
                    if (DeliveryReceipt::doesReceiptExist($waybill_number, $file->getKey())) {
                        return $this->response->sendError($file->getKey() . ' receipt for parcel already exists');
                    }

                    $receipt_file_name = $waybill_number . "_" . $file->getKey() . "_receipt." . $file->getExtension();
                    $receipt_path = $this->s3Client->createObject($file->getTempName(), $receipt_file_name);
                    if (!$receipt_path) {
                        return $this->response->sendError('Could not upload ' . $file->getKey() . ' receipt to S3');
                    }

                    $receipt_paths[] = $receipt_path;

                    $data['receipt_path'] = $receipt_path;
                    $data['receipt_type'] = $file->getKey();
                    if (!DeliveryReceipt::add($data)) {
                        return $this->response->sendError('Could not save ' . $file->getKey() . ' receipt');
                    }

                } else {
                    return $this->response->sendError('Invalid extension for ' . $file->getKey() . '. Only ' . implode(', ', $allowedExtensions) . ' allowed');
                }
            }
        }

        return $this->response->sendSuccess(['receipts' => $receipt_paths]);
    }

    /**
     * Used to set the return flag of a parcel
     * @author Rahman Shitu <rahman@cottacush.com>
     * @return $this
     */
    public function setReturnFlagAction()
    {
        $this->auth->allowOnly([Role::OFFICER, Role::ADMIN, Role::SWEEPER, Role::DISPATCHER]);

        $waybill_numbers = $this->request->getPost('waybill_numbers');
        $comment = $this->request->getPost('comment');
        $attempted_delivery = $this->request->getPost('attempted_delivery', null, 0);
        $return_flag = $this->request->getPost('return_flag', null, 1);

        if (!in_array($return_flag, [0, 1])) {
            return $this->response->sendError(ResponseMessage::INVALID_VALUES);
        }

        if (!isset($waybill_numbers)) {
            return $this->response->sendError(ResponseMessage::ERROR_REQUIRED_FIELDS);
        }

        $waybill_number_arr = Parcel::sanitizeWaybillNumbers($waybill_numbers);
        $auth_data = $this->auth->getData();

        $parcel_arr = Parcel::getByWaybillNumberList($waybill_number_arr, true);
        $bad_parcel = [];

        /**
         * @var Parcel $parcel
         */
        foreach ($waybill_number_arr as $waybill_number) {
            if (!isset($parcel_arr[$waybill_number])) {
                $bad_parcel[$waybill_number] = ResponseMessage::PARCEL_NOT_EXISTING;
                continue;
            }

            $parcel = $parcel_arr[$waybill_number];

            //cannot flag a return for a delivered parcel
            if ($parcel->getStatus() == Status::PARCEL_DELIVERED) {
                $bad_parcel[$waybill_number] = ResponseMessage::PARCEL_ALREADY_DELIVERED;
                continue;
            }

            //if it is an officer, his branch must be either the to or from branch id
            if ($this->auth->getUserType() == Role::OFFICER && !in_array($auth_data['branch_id'], [$parcel->getToBranchId(), $parcel->getFromBranchId()])) {
                $bad_parcel[$waybill_number] = ResponseMessage::PARCEL_NOT_ACCESSIBLE;
                continue;
            }

            //bags and split parcel parent can not be returned
            if (in_array($parcel->getEntityType(), [Parcel::ENTITY_TYPE_BAG, Parcel::ENTITY_TYPE_PARENT])) {
                $bad_parcel[$waybill_number] = ResponseMessage::PARCEL_CANNOT_CHANGE_RETURN_FLAG;
                continue;
            }

            $parcel->setReturnStatus($attempted_delivery);
            $parcel->setForReturn($return_flag);
            if (!$parcel->save()) {
                $bad_parcel[$waybill_number] = ResponseMessage::PARCEL_CANNOT_CHANGE_RETURN_FLAG;
                continue;
            }

            if (!is_null($comment)) {
                ParcelComment::add(['type' => ParcelComment::COMMENT_TYPE_RETURNED, 'comment' => $comment, 'created_by' => $this->auth->getPersonId(), 'waybill_number' => $parcel->getWaybillNumber()]);
            }
        }
        return $this->response->sendSuccess(['bad_parcels' => $bad_parcel]);
    }

    /**
     * Used to mark a shipment as returned to the shipper
     * @author Olawale Lawal <wale@cottacush.com>
     * @return $this
     */
    public function markAsReturnedAction()
    {
        $this->auth->allowOnly([Role::ADMIN, Role::OFFICER, Role::DISPATCHER, Role::SWEEPER]);

        $receiver_name = $this->request->getPost('receiver_name', null);
        $enforce_action = $this->request->getPost('enforce_action');

        if(empty($receiver_name)){
            return $this->response->sendError("Receiver's Name Missing");
        }

        $receiver_phonenumber = $this->request->getPost('receiver_phone_number');
        $date_and_time_of_delivery = $this->request->getPost('date_and_time_of_delivery', null, Util::getCurrentDateTime());
        $waybill_numbers = $this->request->getPost('waybill_numbers');
        $admin_id = $this->auth->getPersonId();

        if (in_array(null, [$waybill_numbers, $admin_id])) {
            return $this->response->sendError(ResponseMessage::ERROR_REQUIRED_FIELDS);
        }

        $waybill_number_arr = Parcel::sanitizeWaybillNumbers($waybill_numbers);
        $auth_data = $this->auth->getData();

        $bad_parcel = [];
        $admin = Admin::findFirst($admin_id);
        foreach ($waybill_number_arr as $waybill_number) {
            $parcel = Parcel::getByWaybillNumber($waybill_number);
            if ($parcel === false) {
                $bad_parcel[$waybill_number] = ResponseMessage::PARCEL_NOT_EXISTING;
                continue;
            }

            if ($parcel->getForReturn() == 0) {
                $bad_parcel[$waybill_number] = ResponseMessage::PARCEL_NOT_FOR_RETURN;
                continue;
            } else if ($parcel->getStatus() != Status::PARCEL_BEING_DELIVERED) {
                $bad_parcel[$waybill_number] = ResponseMessage::PARCEL_NOT_FOR_DELIVERY;
                continue;
            } else if ($parcel->getStatus() == Status::PARCEL_RETURNED) {
                $bad_parcel[$waybill_number] = ResponseMessage::PARCEL_ALREADY_DELIVERED;
                continue;
            } else if ($enforce_action != '1' && !Parcel::isBranchesRelated($parcel->getCreatedBranchId(), $admin->getBranchId())) {
                $bad_parcel[$waybill_number] = ResponseMessage::PARCEL_WRONG_DESTINATION;
                continue;
            }

            $parcel->setReturnStatus(0);
            $check = $parcel->changeStatus(Status::PARCEL_RETURNED, $admin_id, ParcelHistory::MSG_RETURNED, $auth_data['branch_id']);
            if (!$check) {
                $bad_parcel[$waybill_number] = ResponseMessage::CANNOT_MOVE_PARCEL;
                continue;
            }

            //create delivery receipt if receiver name and phone number was supplied
            if (isset($receiver_name)) {
                if (!DeliveryReceipt::doesReceiptExist($waybill_number, DeliveryReceipt::RECEIPT_TYPE_RETURNED)) {
                    $data = ['waybill_number' => $waybill_number,
                        'receipt_type' => DeliveryReceipt::RECEIPT_TYPE_RETURNED,
                        'delivered_by' => $this->auth->getPersonId(),
                        'name' => $receiver_name,
                        'delivered_at' => $date_and_time_of_delivery];
                }
                if (isset($receiver_email)) {
                    $data['email'] = $receiver_email;
                }
                if (isset($receiver_phonenumber)) {
                    $data['phone_number'] = $receiver_phonenumber;
                }

                DeliveryReceipt::add($data);
            }
        }


        return $this->response->sendSuccess(['bad_parcels' => $bad_parcel]);
    }

    /**
     * Unsort parcels
     * @author Adeyemi Olaoye <yemi@cottacush.com>
     */
    public function unsortAction()
    {
        $postData = $this->request->getJsonRawBody();

        if (property_exists($postData, 'waybill_numbers') && !is_array($postData->waybill_numbers)) {
            return $this->response->sendError(ResponseMessage::ERROR_REQUIRED_FIELDS);
        }

        $result = Parcel::unsortParcels($postData->waybill_numbers);

        return $this->response->sendSuccess($result);
    }

    /**
     * Comment on parcels
     * @author Adeyemi Olaoye <yemi@cottacush.com>
     */
    public function addCommentAction()
    {
        $postData = $this->request->getJsonRawBody();
        $parcelCommentValidation = new ParcelCommentValidation($postData);

        if (!$parcelCommentValidation->validate()) {
            return $this->response->sendError($parcelCommentValidation->getMessages());
        }

        $postData->created_by = $this->auth->getPersonId();

        $status = ParcelComment::add($postData);

        return (($status) ? $this->response->sendSuccess() : $this->response->sendError('Could not add comment'));
    }

    /**
     * Used to receive shipments from the dispatcher after an attempted delivery
     * @author Olawale Lawal <wale@cottacush.com>
     */
    public function receiveFromDispatcherAction()
    {
        $this->auth->allowOnly([Role::OFFICER]);

        $waybill_numbers = $this->request->getPost('waybill_numbers');
        $held_by_id = $this->request->getPost('held_by_id');
        $enforce_action = $this->request->getPost('enforce_action', 0);
        $admin_id = $this->auth->getPersonId();

        if (in_array(null, [$waybill_numbers, $admin_id, $held_by_id])) {
            return $this->response->sendError(ResponseMessage::ERROR_REQUIRED_FIELDS);
        }

        $admin = Admin::findFirst($admin_id);
        $held_by = Admin::findFirst($held_by_id);
        if (!$held_by) {
            return $this->response->sendError(ResponseMessage::INVALID_SWEEPER_OR_DISPATCHER);
        }

        $waybill_number_arr = Parcel::sanitizeWaybillNumbers($waybill_numbers);
        $auth_data = $this->auth->getData();

        $bad_parcel = [];
        foreach ($waybill_number_arr as $waybill_number) {
            $parcel = Parcel::getByWaybillNumber($waybill_number);
            if ($parcel == false) {
                $bad_parcel[$waybill_number] = ResponseMessage::PARCEL_NOT_EXISTING;
                continue;
            }

            if ($parcel->getToBranchId() != $admin->getBranchId() && $enforce_action != 1) {
                $bad_parcel[$waybill_number] = ResponseMessage::PARCEL_WRONG_DESTINATION;
                continue;
            } else if ($parcel->getStatus() != Status::PARCEL_BEING_DELIVERED) {
                $bad_parcel[$waybill_number] = ResponseMessage::PARCEL_NOT_FOR_DELIVERY;
                continue;
            }

            //checking if the parcel is held by the correct person
            $held_parcel_record = HeldParcel::fetchUncleared($parcel->getId(), $held_by_id);
            if (!$held_parcel_record) {
                $bad_parcel[$waybill_number] = ResponseMessage::PARCEL_HELD_BY_WRONG_OFFICIAL;
                continue;
            }

            $status = $parcel->getStatus();
            if ($auth_data['branch']['branch_type'] == BranchType::EC) {
                $hub = Branch::getParentById($auth_data['branch']['id']);
                if (!$hub) {
                    return $this->response->sendError(ResponseMessage::EC_NOT_LINKED_TO_HUB);
                } else if ($hub->getBranchType() != BranchType::HUB) {
                    return $this->response->sendError(ResponseMessage::INVALID_HUB_PROVIDED);
                }

                $parcel->setToBranchId($hub->getId());
                $status = Status::PARCEL_FOR_SWEEPER;
            } else if ($auth_data['branch']['branch_type'] == BranchType::HUB) {
                if ($parcel->getForReturn()) {
                    if ($parcel->getCreatedBranchId() == $auth_data['branch']['id']) {
                        $status = Status::PARCEL_FOR_GROUNDSMAN;
                    } else {
                        $status = Status::PARCEL_ARRIVAL;
                    }
                    $parcel->setRouteId(null);
                } else {
                    $status = Status::PARCEL_FOR_DELIVERY;
                }
            }

            $check = $parcel->checkIn($held_parcel_record, $this->auth->getPersonId(), $status, ParcelHistory::MSG_RECEIVED_FROM_DISPATCHER);
            if (!$check) {

                $bad_parcel[$waybill_number] = ResponseMessage::PARCEL_CANNOT_BE_RECEIVED;
                continue;
            }
        }

        return $this->response->sendSuccess(['bad_parcels' => $bad_parcel]);
    }

    /***
     * Get parcels that have been draft sorted by user
     * @author Adeyemi Olaoye <yemi@cottacush.com>
     */
    public function getDraftSortsAction()
    {
        $created_by = $this->auth->getPersonId();
        $offset = $this->request->getQuery('offset', null, DEFAULT_OFFSET);
        $count = $this->request->getQuery('count', null, DEFAULT_COUNT);
        $paginate = $this->request->getQuery('paginate', null, 0);
        $bag_number = $this->request->getQuery('bag_number', null, false);
        $is_visible = $this->request->getQuery('is_visible', null, 1);

        $filter_by = ['ParcelDraftSort.created_by' => $created_by, 'ParcelDraftSort.is_visible' => $is_visible];
        if ($bag_number) {
            $filter_by['DraftBagParcel.bag_sort_number'] = $bag_number;
        }
        $draftSorts = ParcelDraftSort::getDraftSorts($count, $offset, $paginate, $filter_by);

        if ($paginate) {
            $total_count = ParcelDraftSort::getTotalCount($filter_by);
            return $this->response->sendSuccess(['draft_sorts' => $draftSorts, 'total_count' => $total_count]);
        } else {
            return $this->response->sendSuccess($draftSorts);
        }
    }

    /**
     * Draft sort parcels
     * @author Adeyemi Olaoye <yemi@cottacush.com>
     */
    public function draftSortAction()
    {
        $postData = $this->request->getJsonRawBody();
        $validation = new RequestValidation($postData);
        $validation->setRequiredFields(['waybill_numbers', 'to_branch'], ['cancelOnFail' => true]);
        $validation->add('to_branch', new Model(['model' => Branch::class, 'message' => 'Invalid branch supplied']));

        if (!$validation->validate()) {
            return $this->response->sendError($validation->getMessages());
        }

        if (!is_array($postData->waybill_numbers)) {
            return $this->response->sendError('waybill_numbers must be an array');
        }

        $result = ParcelDraftSort::createDraftParcelSorts($postData->waybill_numbers, $postData->to_branch, $this->auth->getPersonId());

        return $this->response->sendSuccess($result);
    }

    /**
     * discard draft sort parcels
     * @author Adeyemi Olaoye <yemi@cottacush.com>
     */
    public function discardSortAction()
    {
        $postData = $this->request->getJsonRawBody();
        $validation = new RequestValidation($postData);
        $validation->setRequiredFields(['sort_numbers'], ['cancelOnFail' => true]);

        if (!$validation->validate()) {
            return $this->response->sendError($validation->getMessages());
        }

        if (!is_array($postData->sort_numbers)) {
            return $this->response->sendError('sort_numbers must be an array');
        }

        $result = ParcelDraftSort::discardSortings($postData->sort_numbers);

        return $this->response->sendSuccess($result);
    }

    /**
     * confirm draft sort parcels
     * @author Adeyemi Olaoye <yemi@cottacush.com>
     */
    public function confirmSortAction()
    {
        $postData = $this->request->getJsonRawBody();
        $validation = new RequestValidation($postData);
        $validation->setRequiredFields(['sort_numbers'], ['cancelOnFail' => true]);

        if (!$validation->validate()) {
            return $this->response->sendError($validation->getMessages());
        }

        if (!is_array($postData->sort_numbers)) {
            return $this->response->sendError('sort_numbers must be an array');
        }

        $result = ParcelDraftSort::confirmSortings($postData->sort_numbers);

        return $this->response->sendSuccess($result);
    }

    /**
     * Create draft bag
     * @author Adeyemi Olaoye <yemi@cottacush.com>
     */
    public function createDraftBagAction()
    {
        $postData = $this->request->getJsonRawBody();
        $validation = new RequestValidation($postData);
        $validation->setRequiredFields(['sort_numbers', 'to_branch'], ['cancelOnFail' => true]);
        $validation->add('to_branch', new Model(['model' => Branch::class, 'message' => 'Invalid branch supplied']));

        if (!$validation->validate()) {
            return $this->response->sendError($validation->getMessages());
        }

        if (!is_array($postData->sort_numbers)) {
            return $this->response->sendError('sort_numbers must be an array');
        }

        try {
            $this->db->begin();
            ParcelDraftSort::createDraftBag($postData->sort_numbers, $postData->to_branch, $this->auth->getPersonId());
        } catch (Exception $ex) {
            $this->db->rollback();
            $this->response->sendError($ex->getMessage());
        }

        $this->db->commit();
        return $this->response->sendSuccess();
    }

    /**
     * Confirm draft bags
     * @author Adeyemi Olaoye <yemi@cottacush.com>
     */
    public function confirmDraftBagAction()
    {
        $postData = $this->request->getJsonRawBody();
        $validation = new RequestValidation($postData);
        $validation->setRequiredFields(['sort_number'], ['cancelOnFail' => true]);

        if (property_exists($postData, 'to_branch')) {
            $validation->add('to_branch', new Model(['model' => Branch::class, 'message' => 'Invalid branch supplied']));
            $to_branch = $postData->to_branch;
        } else {
            $to_branch = null;
        }

        if (!$validation->validate()) {
            return $this->response->sendError($validation->getMessages());
        }

        if (!property_exists($postData, 'seal_id')) {
            return $this->response->sendError('seal_id is required');
        }

        try {
            $this->db->begin();
            ParcelDraftSort::confirmBag($postData->sort_number, $postData->seal_id, $to_branch);
        } catch (Exception $ex) {
            $this->db->rollback();
            return $this->response->sendError($ex->getMessage());
        }

        $this->db->commit();
        return $this->response->sendSuccess();
    }

    /**
     * Create bulk shipment task
     * @author Adeyemi Olaoye <yemi@cottacush.com>
     */
    public function createBulkShipmentTaskAction()
    {
        $postData = $this->request->getJsonRawBody();

        $validation = new BulkShipmentCreationValidation($postData);

        if (!$validation->validate()) {
            return $this->response->sendError($validation->getMessages());
        }

        $company = Company::findFirst($postData->company_id);
        $billing_plan = BillingPlan::findFirst($postData->billing_plan_id);
        $postData->created_by = $this->auth->getPersonId();
        $postData->company = $company->toArray();
        $postData->billing_plan = $billing_plan->toArray();
        $postData->creator = $this->auth->getData();

        $worker = new ParcelCreationWorker();
        $job_id = $worker->addJob(json_encode($postData));
        if (!$job_id) {
            return $this->response->sendError('Could not create bulk shipment creation job. Please try again');
        }
        return $this->response->sendSuccess($job_id);
    }

    /**
     * Get bulk shipment tasks
     * @author Adeyemi Olaoye <yemi@cottacush.com>
     */
    public function getBulkShipmentTasksAction()
    {
        $offset = $this->request->getQuery('offset', null, DEFAULT_OFFSET);
        $count = $this->request->getQuery('count', null, DEFAULT_COUNT);
        
        /** @var Resultset $tasks */
        $tasks = Job::fetchAll(ParcelCreationWorker::QUEUE_BULK_SHIPMENT_CREATION, $offset, $count);
        return $this->response->sendSuccess(['tasks' => $tasks->toArray(), 'total_count' => Job::getTotalCount(ParcelCreationWorker::QUEUE_BULK_SHIPMENT_CREATION)]);
    }

    /**
     * Get Bulk Shipment Task
     * @author Adeyemi Olaoye <yemi@cottacush.com>
     */
    public function getBulkShipmentTaskAction()
    {
        $task_id = $this->request->get('task_id', null, false);
        if (!$task_id) {
            return $this->response->sendError(ResponseMessage::ERROR_REQUIRED_FIELDS);
        }

        /** @var Job $task */
        $task = Job::findFirst($task_id);
        if (!$task) {
            return $this->response->sendError('Task not found');
        }

        /** @var Resultset $taskDetails */
        $taskDetails = BulkShipmentJobDetail::findByJobId($task->id);
        $task = $task->toArray();
        $task['details'] = $taskDetails->toArray();
        return $this->response->sendSuccess($task);
    }

    /**
     * Create bulk waybill printing task
     * @author Adeyemi Olaoye <yemi@cottacush.com>
     */
    public function createBulkWaybillPrintingTaskAction()
    {
        $postData = $this->request->getJsonRawBody();
        $validation = new BulkWaybillPrintingValidation($postData);
        if (!$validation->validate()) {
            return $this->response->sendError($validation->getMessages());
        }

        $waybill_numbers = BulkShipmentJobDetail::getWaybillNumberByJobId($postData->bulk_shipment_task_id);
        if (!$waybill_numbers) {
            return $this->response->sendError('Could not create bulk waybill printing task. There are no waybills to be printed in this task');
        }

        $postData->waybill_numbers = array_column($waybill_numbers, 'waybill_number');
        $postData->created_by = $this->auth->getPersonId();
        $postData->creator = $this->auth->getData();

        $worker = new WaybillPrintingWorker();
        $job_id = $worker->addJob(json_encode($postData));
        if (!$job_id) {
            return $this->response->sendError('Could not create bulk waybill printing task. Please try again');
        }

        return $this->response->sendSuccess($job_id);
    }

    /**
     * Get details of bag
     * @author Adeyemi Olaoye <yemi@cottacush.com>
     * @return $this
     */
    public function getBagAction()
    {
        $waybill_number = $this->request->get('waybill_number', 'string', null);
        if (is_null($waybill_number)) {
            return $this->response->sendError(ResponseMessage::ERROR_REQUIRED_FIELDS);
        }

        if (!Parcel::isBagNumber($waybill_number)) {
            return $this->response->sendError('Invalid bag number supplied');
        }

        $bag = Parcel::getBag($waybill_number);
        if (!$bag) {
            return $this->response->sendError('Could not fetch bag');
        }
        return $this->response->sendSuccess($bag);
    }


    /** Get Shipment Exceptions */
    public function getShipmentExceptionsAction(){
        $this->auth->allowOnly([Role::OFFICER, Role::ADMIN]);
        $auth_data = $this->auth->getData();

        $offset = $this->request->getQuery('offset', null, DEFAULT_OFFSET);
        $count = $this->request->getQuery('count', null, DEFAULT_COUNT);
        $paginate = $this->request->getQuery('paginate', null, false);

        //if hub officer, limit result to his hub
        $user = Admin::getById($auth_data['id']);
        if($user->getRoleId() == Role::OFFICER){
            $defaulter_branch_id = $auth_data['branch']['id'];
        }else{

            $defaulter_branch_id = $this->request->getQuery('defaulter_branch_id');
            $detector_branch_id = $this->request->getQuery('detector_branch_id');
        }


        $start_date = $this->request->getQuery('start_date');
        $end_date = $this->request->getQuery('end_date');
        $held_by_id = $this->request->getQuery('held_by_id');

        $filter_by = [];
        if (!empty($defaulter_branch_id)) {
            $filter_by['defaulter_branch_id'] = $defaulter_branch_id;
        }
        if (!empty($detector_branch_id)) {
            $filter_by['detector_branch_id'] = $detector_branch_id;
        }
        if (!empty($start_date)) {
            $filter_by['start_date'] = $start_date;
        }
        if (!empty($end_date)) {
            $filter_by['end_date'] = $end_date;
        }
        if (!empty($held_by_id)) {
            $filter_by['held_by_id'] = $held_by_id;
        }

        //var_dump($_GET); die();
        $exceptions = ShipmentException::fetchAll($offset, $count, $filter_by,
            ['defaulter_branch' => true, 'detector_branch' => true, 'held_by' => true,
                'parcel' => true, 'admin' => true], $paginate);

        return $this->response->sendSuccess($exceptions->toArray());
    }

    public function countShipmentExceptionAction(){

    }

    public function getHistoriesAction(){
        $waybill_number = $this->request->getQuery('waybill_number');


        $parcel = Parcel::getByWaybillNumber($waybill_number);
        if(!$parcel){
            return $this->response->sendError('Parcel found for the supplied waybill number');
        }

        $histories = ParcelHistory::fetchAll(0, 0, array('parcel_id' => $parcel->getId()),
            array('with_admin' => true));


        if(!$histories){
            return $this->response->sendError('Could not load history for parcel');
        }

        return $this->response->sendSuccess($histories[$waybill_number]);
    }

    public function getDelayedShipmentsAction(){
        $param = $this->request->getQuery();
        $info = TransitInfo::fetchAll($param);
        return $this->response->sendSuccess($info);

    }
}


