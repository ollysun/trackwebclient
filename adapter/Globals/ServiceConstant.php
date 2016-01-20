<?php
namespace Adapter\Globals;

use Adapter\Util\Calypso;

class ServiceConstant
{

    const BASE_PATH = "http://local.courierplus.com";

    const USER_TYPE_SUPER_ADMIN = -1;
    const USER_TYPE_ADMIN = 1;
    const USER_TYPE_OFFICER = 2;
    const USER_TYPE_SWEEPER = 3;
    const USER_TYPE_DISPATCHER = 4;
    const USER_TYPE_GROUNDSMAN = 5;
    const USER_TYPE_COMPANY_ADMIN = 6;
    const USER_TYPE_COMPANY_OFFICER = 7;

    const ENTITY_TYPE_NORMAL = 1;
    const ENTITY_TYPE_BAG = 2;
    const ENTITY_TYPE_SUB = 3;
    const ENTITY_TYPE_PARENT = 4;

    const ACTIVE = 1;
    const INACTIVE = 2;
    const REMOVED = 3;
    const COLLECTED = 4;
    const IN_TRANSIT = 5;
    const DELIVERED = 6;
    const CANCELLED = 7;
    const FOR_SWEEPER = 8;
    const FOR_ARRIVAL = 9;
    const FOR_DELIVERY = 10;
    const UNCLEARED = 11;
    const CLEARED = 12;
    const BEING_DELIVERED = 13;
    const ASSIGNED_TO_GROUNDSMAN = 17;
    const MANIFEST_PENDING = 18;
    const MANIFEST_IN_TRANSIT = 19;
    const MANIFEST_RESOLVED = 20;
    const MANIFEST_CANCELLED = 21;
    const MANIFEST_HAS_ISSUE = 22;
    const RETURNED = 23;
    const URL_ADD_PARCEL = 'parcel/add/';
    const URL_GET_ONE_PARCEL = 'parcel/getone/';
    const URL_GET_ALL_PARCEL = 'parcel/getAll';
    const URL_MOVE_TO_FOR_SWEEPER = '/parcel/moveToForSweeper/';
    const URL_ASSIGN_TO_GROUNDSMAN = '/parcel/assignToGroundsMan/';
    const URL_MOVE_TO_IN_TRANSIT = '/parcel/moveToInTransit/';
    const URL_MOVE_TO_ARRIVAL = '/parcel/moveToArrival/';
    const URL_MOVE_FOR_DELIVERY = '/parcel/moveToForDelivery/';
    const URL_PARCEL_HISTORY = '/parcel/history/';
    const URL_CALC_BILLING = 'zone/calcBilling';
    const URL_MOVE_TO_BEING_DELIVERED = '/parcel/moveToBeingDelivered/';
    const URL_MOVE_TO_DELIVERED = '/parcel/moveToDelivered/';
    const URL_RECEIVE_RETURN = '/parcel/receiveFromDispatcher/';
    const URL_CREATE_BAG = '/parcel/bag';
    const URL_CANCEL_PARCEL = '/parcel/cancel';
    const URL_PARCEL_COUNT = 'parcel/count/';
    const DEFAULT_UNBAG_REFERRER = '/shipments/processed';
    const URL_OPEN_BAG = '/parcel/openbag';
    const URL_MARK_AS_RETURNED = 'parcel/markAsReturned';
    const URL_SET_RETURN_FLAG = 'parcel/setReturnFlag';
    const URL_REMOVE_FROM_BAG = '/parcel/removefrombag';
    const URL_UNSORT_PARCEL = '/parcel/unsort';
    const URL_RETURN_REASONS = '/parcel/getreturnreasons';
    const URL_AUDIT_TRAIL_LOG = '/admin/getaudittrail';
    const URL_AUDIT_ADDITIONAL_DATA = '/admin/getauditdetails';
    const URL_GET_ALL_BILLING_PLAN_NAMES = '/billingPlan/getallbillingplannames';
    const URL_DRAFT_SORT = '/parcel/draftsort';
    const URL_DISCARD_SORT = '/parcel/discardsort';
    const URL_CONFIRM_SORT = '/parcel/confirmsort';
    const URL_GET_DRAFT_SORTS = '/parcel/getdraftsorts';
    const URL_CREATE_DRAFT_BAG = '/parcel/createdraftbag';
    const URL_CONFIRM_DRAFT_BAG = '/parcel/confirmdraftbag';


    const URL_GET_ALL_BANKS = 'bank/getAll/';

    const URL_GET_ALL_BRANCH = 'branch/getall/';
    const URL_GET_ALL_EC_IN_HUB = 'branch/getallec/';

    const URL_GET_BANK_ACCOUNT = 'bankaccount/getAll/';

    const URL_ADMIN_LOGIN = 'auth/login/';
    const URL_REF_BANK = 'ref/banks/';
    const URL_REF_ROLE = 'ref/roles';
    const URL_REF_SHIPMENT = 'ref/shipmentType/';
    const URL_REF_deliveryType = 'ref/deliveryType/';
    const URL_REF_parcelType = 'ref/parcelType/';
    const URL_REF_COUNTRIES = 'ref/countries';

    const URL_REF_STATES = 'ref/states';
    const URL_REF_PAYMENT_METHODS = '/ref/paymentType';

    const URL_REF_REGIONS = '/ref/region';

    const URL_USER_BY_PHONE = '/user/getByPhone';

    const BRANCH_TYPE_EC = 4;
    const BRANCH_TYPE_HUB = 2;
    const BRANCH_TYPE_HQ = 1;

    const URL_BRANCH_ADD = '/branch/add';
    const URL_BRANCH_EDIT = '/branch/editDetails';
    const URL_BRANCH_CHANGE_STATUS = '/branch/changeStatus';
    const URL_BRANCH_RELINK = '/branch/relink';
    const URL_BRANCH_GET_ONE = '/branch/get';
    const URL_BRANCH_GET_ALL = '/branch/getAll';
    const URL_BRANCH_GET_ALL_EC = '/branch/getAllEC';
    const URL_BRANCH_GET_ALL_HUB = '/branch/getAllHub';

    const URL_REGION_CREATE = 'region/add';
    const URL_REGION_EDIT = 'region/edit';
    const URL_REGION_STATUS = 'region/changeActiveFg';
    const URL_REGION_STATE = 'region/changeStateRegion';
    const URL_REGION_CITY_ADD = 'region/addCity';
    const URL_REGION_CITY_STATUS = 'region/changeCityStatus';
    const URL_REGION_CITY_EDIT = 'region/editCity';
    const URL_REGION_CITY_GET_ONE = 'region/getOneCity';
    const URL_REGION_CITY_GET_ALL = 'region/getAllCity';

    const URL_ZONES_ADD = '/zone/add';
    const URL_ZONES_EDIT = '/zone/edit';
    const URL_ZONES_STATUS = '/zone/changeStatus';
    const URL_ZONES_GET_ALL = '/zone/fetchAll';
    const URL_ZONES_GET_BY_CODE = '/zone/fetchByCode';
    const URL_ZONES_GET_BY_ID = '/zone/fetchByID';

    const URL_ZONES_MATRIX_GET = 'zone/getMatrix';
    const URL_ZONES_MATRIX_SAVE = 'zone/saveMatrix';
    const URL_ZONES_MATRIX_REMOVE = 'zone/removeMatrix';

    const URL_WEIGHT_ADD = 'weightrange/add';
    const URL_WEIGHT_EDIT = 'weightrange/edit';
    const URL_WEIGHT_CHANGE_STATUS = 'weightrange/changeStatus';
    const URL_WEIGHT_FETCH_ID = 'weightrange/fetchById';
    const URL_WEIGHT_FETCH_ALL = 'weightrange/fetchAll';
    const URL_WEIGHT_DELETE = 'weightrange/delete';

    const URL_ONFORWARDING_ADD = 'onforwardingcharge/add';
    const URL_ONFORWARDING_EDIT = 'onforwardingcharge/edit';
    const URL_ONFORWARDING_CHANGE_STATUS = 'onforwardingcharge/changeStatus';
    const URL_ONFORWARDING_FETCH_ID = 'onforwardingcharge/fetchById';
    const URL_ONFORWARDING_FETCH_ALL = 'onforwardingcharge/fetchAll';
    const URL_ON_FORWARDING_LINK = 'onforwardingcharge/linkCity';
    const URL_ON_FORWARDING_UNLINK = 'onforwardingcharge/unlinkCity';

    const URL_BILLING_FETCH_ALL = 'zone/fetchbilling';
    const URL_BILLING_ADD = 'zone/addbilling';
    const URL_BILLING_EDIT = 'zone/editbilling';
    const URL_BILLING_DELETE = 'zone/removebilling';
    const URL_BILLING_FETCH_BY_ID = 'zone/fetchBillingById';

    const URL_TELLER_ADD = 'teller/add';

    const URL_ROUTE_ADD = 'route/create';
    const URL_ROUTE_GET_ALL = 'route/getAll';
    const URL_ROUTE_EDIT = 'route/edit';

    const URL_MANIFEST_ALL = 'manifest/getAll';
    const URL_MANIFEST_ONE = 'manifest/getOne';

    const URL_CREATE_USER = 'admin/register';
    const URL_EDIT_USER = 'admin/edit';
    const URL_GET_USERS = '/admin/getAll';
    const URL_GET_USER = '/admin/getone';
    const URL_USER_VALIDATE = '/auth/validate';
    const URL_USER_CHANGE_PASSWORD = 'auth/changePassword';
    const URL_USER_CHANGE_STATUS = 'auth/changeStatus';
    const URL_USER_FORGOT_PASSWORD = 'auth/forgotPassword';
    const URL_USER_RESET_PASSWORD = 'auth/resetPassword';
    const URL_USER_VALIDATE_PASSWORD_RESET_TOKEN = 'auth/validatePasswordResetToken';

    const URL_COMPANY_ADD = 'company/createCompany';
    const URL_COMPANY_EDIT = 'company/editCompany';
    const URL_GET_COMPANY = 'company/getCompany';
    const URL_COMPANY_ALL = 'company/getAllCompany';
    const URL_COMPANY_USERS = 'company/getAllUsers';
    const URL_USER_ADD = 'company/createUser';
    const URL_USER_EDIT = 'company/editUser';
    const URL_COMPANY_REQUESTS = 'company/getRequests';
    const URL_MAKE_SHIPMENT_REQUEST = 'company/makeShipmentRequest';
    const URL_MAKE_BULK_SHIPMENT_REQUEST = 'company/makeBulkShipmentRequest';
    const URL_MAKE_PICKUP_REQUEST = 'company/makePickupRequest';
    const URL_CANCEL_PICKUP_REQUEST = 'company/cancelPickupRequest';
    const URL_CANCEL_SHIPMENT_REQUEST = 'company/cancelShipmentRequest';
    const URL_DECLINE_SHIPMENT_REQUEST = 'company/declineShipmentRequest';
    const URL_DECLINE_PICKUP_REQUEST = 'company/declinePickupRequest';
    const URL_SHIPMENT_REQUEST = 'company/getShipmentRequest';
    const URL_PICKUP_REQUEST = 'company/getPickupRequest';
    const URL_GET_ALL_CORPORATE_ECS = 'company/getAllEcs';
    const URL_LINK_EC_TO_COMPANY = 'company/linkEc';
    const URL_RELINK_EC_TO_COMPANY = 'company/relinkEc';

    const URL_BILLING_PLAN_GET_CITIES_WITH_CHARGE = 'billingPlan/getCitiesWithCharge';
    const URL_BILLING_PLAN_ADD = 'billingPlan/add';
    const URL_BILLING_PLAN_GET_ALL = 'billingPlan/getAll';
    const URL_RESET_ONFORWARDING_CHARGES = 'billingPlan/resetOnforwardingChargesToZero';
    const URL_CLONE_BILLING_PLAN = 'billingPlan/clonebillingplan';

    const URL_INVOICE_ADD = 'invoice/add';
    const URL_INVOICE_ALL = 'invoice/getAll';
    const URL_INVOICE_GET = 'invoice/get';
    const URL_INVOICE_PARCELS = 'invoice/getInvoiceParcels';

    const URL_CREDIT_NOTE_ADD = 'creditNote/add';
    const URL_CREDIT_NOTE_ALL = 'creditNote/getAll';
    const URL_CREDIT_NOTE_PARCELS = 'creditnote/getparcels';
    const URL_CREDIT_NOTE_PRINTOUT_DETAILS = 'creditnote/getprintoutdetails';

    const DATE_TIME_FORMAT = 'd M Y H:i';
    const DATE_FORMAT = 'd M Y';
    const TIME_FORMAT = 'g:i A';

    const URL_GET_STAFF_BY_ID = '/admin/getOne';

    const REF_PAYMENT_METHOD_CASH = 1;
    const REF_PAYMENT_METHOD_POS = 2;
    const REF_PAYMENT_METHOD_CASH_POS = 3;
    const REF_PAYMENT_METHOD_DEFERRED = 4;

    const REF_MANIFEST_TYPE_SWEEP = 1;
    const REF_MANIFEST_TYPE_DELIVERY = 2;

    const DELIVERY_DISPATCH = 2;
    const DELIVERY_PICKUP = 1;
    const COUNTRY_NIGERIA = 1;

    const REQUEST_OTHERS = 1;
    const REQUEST_ECOMMERCE = 2;

    const SHIPPING_TYPE_EXPRESS = 1;
    const SHIPPING_TYPE_SPECIAL_PROJECTS = 2;
    const SHIPPING_TYPE_LOGISTICS = 3;
    const SHIPPING_TYPE_BULK_MAIL = 4;

    const PARCEL_DOCUMENTS = 1;
    const PARCEL_NON_DOCUMENTS = 2;
    const PARCEL_HIGH_VALUE = 3;

    const RETURN_REQUEST_SENT = 1;

    const TRUE = 1;
    const FALSE = 0;
    const DEFAULT_VAT_RATE = 5;

    const QTY_METRICS_WEIGHT = 'weight';
    const QTY_METRICS_PIECES = 'pieces';

    public static function getStatus($status)
    {
        switch ($status) {
            case ServiceConstant::ACTIVE:
                return '<span class="label label-success">Active</span>';
                break;
            case ServiceConstant::INACTIVE:
                return '<span class="label label-danger">Inactive</span>';
                break;
            case ServiceConstant::IN_TRANSIT:
                return '<span class="label label-info">In Transit</span>';
                break;
            case ServiceConstant::REMOVED:
                return '<span class="label label-danger">Removed</span>';
                break;
            case ServiceConstant::COLLECTED:
                return 'Collected';
                break;
            case ServiceConstant::DELIVERED:
                return 'Delivered';
                break;
            case ServiceConstant::CANCELLED:
                return 'Cancelled';
                break;
            case ServiceConstant::FOR_SWEEPER:
                return 'For Sweeper';
                break;
            case ServiceConstant::FOR_ARRIVAL:
                return 'For Arrival';
                break;
            case ServiceConstant::FOR_DELIVERY:
                return 'For Delivery';
                break;
            case ServiceConstant::BEING_DELIVERED:
                return 'In Transit to Customer';
                break;
            case ServiceConstant::ASSIGNED_TO_GROUNDSMAN:
                return 'Assigned to Groundsman';
                break;
            case ServiceConstant::MANIFEST_PENDING:
                return 'Pending';
                break;
            case ServiceConstant::MANIFEST_IN_TRANSIT:
                return 'In Transit';
                break;
            case ServiceConstant::MANIFEST_HAS_ISSUE:
                return 'Has Issue';
                break;
            case ServiceConstant::MANIFEST_RESOLVED:
                return 'Resolved';
                break;
            case ServiceConstant::MANIFEST_CANCELLED:
                return 'Cancelled';
                break;
            case ServiceConstant::RETURNED:
                return 'Returned to Shipper';
                break;
        }
    }

    public static function getStatusRef()
    {
        return [ServiceConstant::IN_TRANSIT, ServiceConstant::DELIVERED, ServiceConstant::CANCELLED, ServiceConstant::FOR_ARRIVAL
            , ServiceConstant::FOR_DELIVERY, ServiceConstant::FOR_SWEEPER, ServiceConstant::COLLECTED, ServiceConstant::BEING_DELIVERED, ServiceConstant::RETURNED];
    }

    /**
     * Returns the payment methods
     * @author Olawale Lawal <wale@cottacush.com>
     * @return array
     */
    public static function getPaymentMethods()
    {
        return [ServiceConstant::REF_PAYMENT_METHOD_CASH, ServiceConstant::REF_PAYMENT_METHOD_POS, ServiceConstant::REF_PAYMENT_METHOD_CASH_POS, ServiceConstant::REF_PAYMENT_METHOD_DEFERRED];
    }

    public static function getPaymentMethod($method)
    {
        switch ($method) {
            case ServiceConstant::REF_PAYMENT_METHOD_CASH:
                return 'Cash';
                break;

            case ServiceConstant::REF_PAYMENT_METHOD_POS:
                return 'POS';
                break;

            case ServiceConstant::REF_PAYMENT_METHOD_CASH_POS:
                return 'Cash & POS';
                break;

            case ServiceConstant::REF_PAYMENT_METHOD_DEFERRED:
                return 'Deferred Payment';
                break;

            default:
                return $method; // return id
                break;
        }
    }

    /**
     * Returns the delivery types
     * @author Olawale Lawal <wale@cottacush.com>
     * @return array
     */
    public static function getDeliveryTypes()
    {
        return [ServiceConstant::DELIVERY_DISPATCH, ServiceConstant::DELIVERY_PICKUP];
    }

    public static function getDeliveryType($type)
    {
        switch ($type) {
            case ServiceConstant::DELIVERY_DISPATCH:
                return 'Dispatch';

            case ServiceConstant::DELIVERY_PICKUP:
                return 'Pickup';

            default:
                return false;
        }
    }

    /**
     * Returns the shipment request types
     * @author Olawale Lawal <wale@cottacush.com>
     * @return array
     */
    public static function getRequestTypes()
    {
        return [ServiceConstant::REQUEST_ECOMMERCE, ServiceConstant::REQUEST_OTHERS];
    }

    public static function getRequestType($type)
    {
        switch ($type) {
            case ServiceConstant::REQUEST_ECOMMERCE:
                return 'eCommerce';

            case ServiceConstant::REQUEST_OTHERS:
                return 'Others';

            default:
                return false;
        }
    }

    public static function getManifestStatuses()
    {
        return [ServiceConstant::MANIFEST_PENDING, ServiceConstant::MANIFEST_RESOLVED, ServiceConstant::MANIFEST_HAS_ISSUE,
            ServiceConstant::MANIFEST_IN_TRANSIT, ServiceConstant::MANIFEST_CANCELLED];
    }

    public static function getManifestType($type)
    {
        switch ($type) {
            case ServiceConstant::REF_MANIFEST_TYPE_SWEEP:
                return 'Sweep Manifest';
                break;
            case ServiceConstant::REF_MANIFEST_TYPE_DELIVERY:
                return 'Delivery Manifest';
                break;
            default:
                return false;
                break;
        }
    }

    public static function getReturnStatus($parcel)
    {
        if (isset($parcel['for_return']) && $parcel['for_return'] != 0) {
            $created_branch = Calypso::getDisplayValue($parcel, 'created_branch.name', null);
            if (!is_null($created_branch)) {
                return 'Return to ' . ucwords(Calypso::getDisplayValue($parcel, 'created_branch.name') . ', ' . Calypso::getDisplayValue($parcel, 'created_branch.state.name'));
            } else {
                return 'Return to originating branch';
            }
        } else {
            return false;
        }
    }

    /**
     * Returns the shipment request types
     * @author Olawale Lawal <wale@cottacush.com>
     * @return array
     */
    public static function getShippingTypes()
    {
        return [ServiceConstant::SHIPPING_TYPE_EXPRESS, ServiceConstant::SHIPPING_TYPE_SPECIAL_PROJECTS, ServiceConstant::SHIPPING_TYPE_LOGISTICS, ServiceConstant::SHIPPING_TYPE_BULK_MAIL];
    }

    /**
     * Returns the shipping type in text
     * @author Olawale Lawal <wale@cottacush.com>
     * @param $type
     * @return string
     */
    public static function getShippingType($type)
    {
        switch ($type) {
            case ServiceConstant::SHIPPING_TYPE_EXPRESS:
                return 'Express';

            case ServiceConstant::SHIPPING_TYPE_SPECIAL_PROJECTS:
                return 'Special Projects';

            case ServiceConstant::SHIPPING_TYPE_LOGISTICS:
                return 'Logistics';

            case ServiceConstant::SHIPPING_TYPE_BULK_MAIL:
                return 'Bulk Mail';

            default:
                return false;
                break;

        }
    }

    /**
     * Returns the parcel type
     * @author Olawale Lawal <wale@cottacush.com>
     * @param $type
     * @return string
     */
    public static function getParcelType($type)
    {
        switch ($type) {
            case ServiceConstant::PARCEL_DOCUMENTS:
                return 'Documents';

            case ServiceConstant::PARCEL_HIGH_VALUE:
                return 'High Value';

            case ServiceConstant::PARCEL_NON_DOCUMENTS:
                return 'Non Documents';

            default:
                return false;
                break;

        }
    }


}