<?php
namespace Adapter\Globals;

use Adapter\Util\Calypso;

class ServiceConstant
{

    const BASE_PATH = "http://local.trackplus.server";

    const USER_TYPE_SUPER_ADMIN = -1;
    const USER_TYPE_ADMIN = 1;
    const USER_TYPE_OFFICER = 2;
    const USER_TYPE_SWEEPER = 3;
    const USER_TYPE_DISPATCHER = 4;
    const USER_TYPE_GROUNDSMAN = 5;
    const USER_TYPE_COMPANY_ADMIN = 6;
    const USER_TYPE_COMPANY_OFFICER = 7;
    const USER_TYPE_SALES_AGENT = 9;
    const USER_TYPE_BUSINESS_MANAGER = 10;
    const USER_TYPE_REGIONAL_MANAGER = 11;
    const USER_TYPE_FINANCE = 12;
    const USER_TYPE_BILLING = 13;

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
    const TELLER_AWAITING_APPROVAL = 14;
    const TELLER_APPROVED = 15;
    const TELLER_DECLINED = 16;
    const ASSIGNED_TO_GROUNDSMAN = 17;
    const MANIFEST_PENDING = 18;
    const MANIFEST_IN_TRANSIT = 19;
    const MANIFEST_RESOLVED = 20;
    const MANIFEST_CANCELLED = 21;
    const MANIFEST_HAS_ISSUE = 22;
    const RETURNED = 23;
    const CREATED_BUT_WITH_CUSTOMER = 24;

    const URL_ADD_PARCEL = 'parcel/add/';
    const URL_ADD_PARCEL_From_API = 'parcel/addFromApi/';
    const URL_GET_ONE_PARCEL = 'parcel/getone/';
    const URL_GET_BAG = 'parcel/getbag/';
    const URL_GET_ALL_PARCEL = 'parcel/getAll';
    const URL_MOVE_TO_FOR_SWEEPER = '/parcel/moveToForSweeper/';
    const URL_ASSIGN_TO_GROUNDSMAN = '/parcel/assignToGroundsMan/';
    const URL_MOVE_TO_IN_TRANSIT = '/parcel/moveToInTransit/';
    const URL_CREATE_DIRECT_MANIFEST = '/parcel/createDirectManifest/';
    const URL_MOVE_TO_ARRIVAL = '/parcel/moveToArrival/';
    const URL_MOVE_FOR_DELIVERY = '/parcel/moveToForDelivery/';
    const URL_PARCEL_HISTORY = '/parcel/history/';
    const URL_IMPORTED_PARCEL_HISTORY = 'parcel/importedParcelHistory';
    const URL_CALC_BILLING = 'zone/calcBilling';
    const URL_GET_QUOTE = 'zone/getQuote';
    const URL_MOVE_TO_BEING_DELIVERED = '/parcel/moveToBeingDelivered/';
    const URL_MOVE_TO_DELIVERED = '/parcel/moveToDelivered/';
    const URL_RECEIVE_RETURN = '/parcel/receiveFromDispatcher/';
    const URL_CREATE_BAG = '/parcel/bag';
    const URL_CANCEL_PARCEL = '/parcel/cancel';
    const URL_PARCEL_COUNT = '/parcel/count/';
    const URL_PARCEL_VALIDATE_NUMBERS = 'parcel/validateNumbers/';
    const URL_PARCEL_GROUP_COUNT = 'parcel/groupCount/';
    const DEFAULT_UNBAG_REFERRER = '/shipments/processed';
    const URL_OPEN_BAG = '/parcel/openbag';
    const URL_MARK_AS_RETURNED = 'parcel/markAsReturned';
    const URL_SET_RETURN_FLAG = 'parcel/setReturnFlag';
    const URL_REMOVE_NEGATIVE_FLAG = 'parcel/removeNegativeFlag';
    const URL_REMOVE_FROM_BAG = '/parcel/removefrombag';
    const URL_UNSORT_PARCEL = '/parcel/unsort';
    const URL_RETURN_REASONS = '/parcel/getreturnreasons';
    const URL_GET_SHIPMENT_EXCEPTIONS = 'parcel/getShipmentExceptions';
    const URL_GET_PARCEL_HISTORIES = 'parcel/getHistories';
    const URL_GET_PARCEL_HISTORIES_FOR_API = 'parcel/getHistoryForApi';
    const URL_GET_PARCEL_LAST_STATUS_FOR_API = 'parcel/getParcelLastStatusForApi';
    const URL_GET_DELAYED_SHIPMENTS = 'parcel/getDelayedShipments';
    const URL_AUDIT_TRAIL_LOG = '/admin/getaudittrail';
    const URL_AUDIT_ADDITIONAL_DATA = '/admin/getauditdetails';
    const URL_GET_ALL_BILLING_PLAN_NAMES = '/billingPlan/getallbillingplannames';
    const URL_DRAFT_SORT = '/parcel/draftsort';
    const URL_DISCARD_SORT = '/parcel/discardsort';
    const URL_CONFIRM_SORT = '/parcel/confirmsort';
    const URL_GET_DRAFT_SORTS = '/parcel/getdraftsorts';
    const URL_CREATE_DRAFT_BAG = '/parcel/createdraftbag';
    const URL_CONFIRM_DRAFT_BAG = '/parcel/confirmdraftbag';
    const URL_CREATE_BULK_SHIPMENT_TASK = '/parcel/createbulkshipmenttask';
    const URL_GET_BULK_SHIPMENT_TASKS = '/parcel/getbulkshipmenttasks';
    const URL_GET_BULK_SHIPMENT_TASK = '/parcel/getbulkshipmenttask';
    const URL_CREATE_BULK_WAYBILL_PRINTING_TASK = '/parcel/createbulkwaybillprintingtask';
    const URL_MOVE_PARCEL = '/parcel/moveParcel';
    const URL_REPRICE_COMPANY = '/parcel/repriceByCompany';

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

    const BRANCH_TYPE_COMPANY = 5;
    const BRANCH_TYPE_EC = 4;
    const BRANCH_TYPE_HUB = 2;
    const BRANCH_TYPE_HQ = 1;
    const BRANCH_TYPE_REGIONAL_HQ = 6;

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

    const URL_BUSINESS_MANAGER_ADD = 'bm/add';
    const URL_BUSINESS_MANAGER_CENTRES_ADD = 'bmcentres/add';
    const URL_BUSINESS_MANAGER_CENTRES_LIST = 'bmcentres/centersForBm';
    const URL_BUSINESS_MANAGER_CHANGE_REGION = 'bm/changeregion';
    const URL_BUSINESS_MANAGER_GET_ALL = 'bm/getall';

    const URL_BUSINESS_ZONE_ADD = 'businesszone/add';
    const URL_BUSINESS_ZONE_GET_ALL = 'businesszone/getall';
    const URL_BUSINESS_ZONE_DELETE = 'businesszone/delete';

    const URL_ZONES_ADD = '/zone/add';
    const URL_ZONES_EDIT = '/zone/edit';
    const URL_ZONES_STATUS = '/zone/changeStatus';
    const URL_ZONES_GET_ALL = '/zone/fetchAll';
    const URL_ZONES_GET_BY_CODE = '/zone/fetchByCode';
    const URL_ZONES_GET_BY_ID = '/zone/fetchByID';

    //intl billing
    const URL_INTL_ZONES_GET_ALL = 'intl/getzones';

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

    //intl
    const URL_INTL_GET_ZONES = "intl/getZones";
    const URL_INTL_EDIT_ZONE = 'intl/updateZone';
    const URL_INTL_ADD_ZONE = 'intl/addZone';
    const URL_INTL_ADD_COUNTRY_TO_ZONE = 'intl/mapCountryToZone';
    const URL_INTL_GET_COUNTRIES_BY_ZONE = 'intl/getCountriesByZone';
    const URL_MAP_COUNTY_TO_ZONE = 'intl/mapCountryToZone';
    const URL_INTL_ADD_WEIGHT_RANGE = 'intl/addWeightRange';
    const URL_INTL_EDIT_WEIGHT_RANGE = 'intl/editWeightRange';
    const URL_INTL_GET_WEIGHT_RANGE = 'intl/getWeightRanges';
    const URL_INTL_SAVE_TARIFF = 'intl/saveTariff';
    const URL_INTL_GET_TARIFFS = 'intl/gettariffs';
    const URL_INTL_PRICING = 'intl/getTariffs';
    const URL_INTL_EDIT_PRICE = 'intl/editprice';
    const URL_INTL_ADD_PRICE = 'intl/saveTariff';
    const URL_INTL_DELETE_TARIFF = 'intl/deletetariff';


    //transit time
    const URL_GET_TRANSIT_TIME = 'zone/getTransitTime';
    const URL_SAVE_TRANSIT_TIME = 'zone/saveTransitTime';
    const URL_REMOVE_TRANSIT_TIME = 'zone/removeTransitTime';
    //distance
    const URL_GET_DISTANCE = 'zone/getDistance';
    const URL_SAVE_DISTANCE = 'zone/saveDistance';
    const URL_REMOVE_DISTANCE = 'zone/removeDistance';

    const URL_TELLER_ADD = 'teller/add';
    const URL_TELLER_GET_ALL = 'teller/getall';
    const URL_TELLER_GET = 'teller/getOne';
    const URL_TELLER_APPROVE = 'teller/approve';
    const URL_TELLER_DECLINE = 'teller/decline';

    const URL_COD_TELLER_ADD = 'codteller/add';
    const URL_COD_TELLER_GET_ALL = 'codteller/getall';
    const URL_COD_TELLER_GET = 'codteller/getOne';
    const URL_COD_TELLER_APPROVE = 'codteller/approve';
    const URL_COD_TELLER_DECLINE = 'codteller/decline';

    const URL_RTD_TELLER_ADD = 'rtdteller/add';
    const URL_RTD_TELLER_GET_ALL = 'rtdteller/getall';
    const URL_RTD_TELLER_GET = 'rtdteller/get';
    const URL_RTD_TELLER_APPROVE = 'rtdteller/approve';
    const URL_RTD_TELLER_DECLINE = 'rtdteller/decline';

    const URL_REMITTANCE_GET_ALL = 'remittance/getall';
    const URL_REMITTANCE_GET_DUE_PARCELS = 'remittance/getDueParcels';
    const URL_REMITTANCE_GET_ONE = 'remittance/getone';
    const URL_REMITTANCE_SAVE = 'remittance/save';
    const URL_REMITTANCE_PAYMENT_ADVICE = 'remittance/getPaymentAdvice';
    const URL_GET_PENDING_PAYMENTS = 'remittance/getPendingPayments';
    const URL_REMITTANCE_GET_ADVICE_FOR_DOWNLOAD = 'remittance/getPaymentAdviceForDownload';
    const URL_REMITTANCE_GET_PAYMENTS = 'remittance/getPayments';

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
    const URL_USER_RESET_COMPANY_ADMIN_PASSWORD = 'auth/resetPassword';
    const URL_USER_VALIDATE_PASSWORD_RESET_TOKEN = 'auth/validatePasswordResetToken';

    const URL_COMPANY_ADD = 'company/createCompany';
    const URL_COMPANY_EDIT = 'company/editCompany';
    const URL_GET_COMPANY = 'company/getCompany';
    const URL_GET_COMPANY_ACCESS = 'company/getCompanyAccess';
    const URL_SAVE_COMPANY_ACCESS = 'company/manageCompanyAccess';
    const URL_GET_COMPANY_MANAGE_ACCESS = 'company/manageCompanyAccess';
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
    const URL_COMPANY_GET_ALL_ACCOUNT_TYPES = 'company/getAllAccountTypes';
    const URL_COMPANY_CHANGE_STATUS = 'company/changeStatus';
    const URL_COMPANY_RESET_CREDIT = 'company/resetCredit';

    const URL_GET_STATUS = '/statusnotificationmessage/getStatus';
    const URL_SAVE_STATUSNOTIFICATION = '/statusnotificationmessage/savestatusNotification';
    const URL_GET_STATUSNOTIFICATION = '/statusnotificationmessage/get';

    const URL_SAVE_SETTING = '/setting/save';
    const URL_GET_SETTINGS = '/setting/get';

    const URL_BILLING_PLAN_GET_CITIES_WITH_CHARGE = 'billingPlan/getCitiesWithCharge';
    const URL_BILLING_PLAN_ADD = 'billingPlan/add';
    const URL_BILLING_PLAN_UPDATE_DISCOUNT = 'billingPlan/updatediscount';
    const URL_BILLING_PLAN_LINK_COMPANY = 'billingPlan/linkcompany';
    const URL_BILLING_PLAN_REMOVE_COMPANY = 'billingPlan/removecompany';
    const URL_BILLING_PLAN_MAKE_DEFAULT = 'billingPlan/makedefault';
    const URL_BILLING_PLAN_GET_COMPANIES = 'billingPlan/getcompanies';
    const URL_BILLING_PLAN_GET_COMPANY_PLANS = 'billingPlan/getCompanyPlans';
    const URL_BILLING_PLAN_GET_ALL = 'billingPlan/getAll';
    const URL_RESET_ONFORWARDING_CHARGES = 'billingPlan/resetOnforwardingChargesToZero';
    const URL_CLONE_BILLING_PLAN = 'billingPlan/clonebillingplan';

    const URL_INVOICE_ADD = 'invoice/add';
    const URL_RECREATE_INVOICE = 'invoice/recreateInvoice';
    const URL_BULK_INVOICE_ADD = 'invoice/createBulkInvoice';
    const URL_INVOICE_ALL = 'invoice/getAll';
    const URL_INVOICE_GET = 'invoice/get';
    const URL_INVOICE_PARCELS = 'invoice/getInvoiceParcels';
    const URL_INVOICE_GET_BULK_INVOICE_TASKS = 'invoice/getBulkInvoiceTasks';
    const URL_INVOICE_GET_BULK_INVOICE_TASK = 'invoice/getBulkInvoiceTask';

    const URL_CREDIT_NOTE_ADD = 'creditNote/add';
    const URL_CREDIT_NOTE_ALL = 'creditNote/getAll';
    const URL_CREDIT_NOTE_PARCELS = 'creditnote/getparcels';
    const URL_CREDIT_NOTE_PRINTOUT_DETAILS = 'creditnote/getprintoutdetails';

    const URL_BATCH_DISCOUNT = "parcel/applyDiscount";

    const URL_AUDIT_GET_ALL = 'audit/getAllAudit';
    const URL_AUDIT_GET_ONE = 'audit/getAudit';

    //ExportedParcel URL
    const URL_EXPORTED_GET_ALL = 'exportedparcel/getAll';
    const URL_EXPORTED_GET_ALL_UNASSIGNED = 'exportedparcel/getAllUnassigned';

    //ExportAgent URL
    const URL_EXPORTED_GET_ALL_AGENT = 'exportagent/getall';
    const URL_EXPORTED_ASSIGN_AGENT = 'exportedparcel/add';

    //ExportedParcelTracking URL
    const URL_EXPORTED_PARCEL_TRACKING = 'exportedparceltracking/add';

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
    const DEFAULT_COUNTRY = 1;

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

    const SHIPMENTS_SENDER_TYPE_CORPORATE = 'corporate';

    const DELIVERY_ATTEMPTED = 1;
    const RETURNING_TO_ORIGIN = 2;
    const RETURN_READY_FOR_PICKUP = 3;

    /**
     * @author Boyewa Richrad
     * @author Babatunde Otaru <tunde@cottacush.com>
     * @param $status
     * @param int $return_status
     * @return string
     */
    public static function getStatus($status, $return_status = 0)
    {
        if($return_status != 0){
            switch ($return_status){
                case ServiceConstant::DELIVERY_ATTEMPTED:
                    return 'Attempted Delivery';
                    break;
                case ServiceConstant::RETURNING_TO_ORIGIN:
                    return 'Returning to Origin';
                    break;
                case ServiceConstant::RETURN_READY_FOR_PICKUP:
                    return 'Return Ready for Pick up';
                    break;
            }
        }
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
            case ServiceConstant::CREATED_BUT_WITH_CUSTOMER:
                return 'Created But With Customer';
                break;
            case ServiceConstant::TELLER_APPROVED:
                return "Teller Approved";
            break;
            case ServiceConstant::TELLER_AWAITING_APPROVAL:
                return 'Teller Awaiting Approval';
            break;
            case ServiceConstant::TELLER_DECLINED:
                return 'Teller Declined';
            break;
        }
    }

    public static function getStatusRef()
    {
        return [ServiceConstant::IN_TRANSIT, ServiceConstant::DELIVERED, ServiceConstant::CANCELLED, ServiceConstant::FOR_ARRIVAL
            , ServiceConstant::FOR_DELIVERY, ServiceConstant::FOR_SWEEPER, ServiceConstant::BEING_DELIVERED, ServiceConstant::RETURNED];
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

    /**
     * Show waybill number in a user friendly format
     * @author Adeyemi Olaoye <yemi@cottacush.com>
     * @param $waybill_number
     * @return mixed
     */
    public static function humanizeWaybillNumber($waybill_number)
    {
        if (!is_string($waybill_number) && strlen($waybill_number) > 0) {
            return $waybill_number;
        }

        $splitPart = '';
        if (($pos = strpos($waybill_number, '-')) !== false) {
            $splitPart = substr($waybill_number, $pos, (strlen($waybill_number) - $pos));
            $waybill_number = substr($waybill_number, 0, $pos);
        }

        $parts = str_split($waybill_number, 3);
        $end = $parts[count($parts) - 1];
        if (strlen($end) < 3) {
            $parts[count($parts) - 2] = $parts[count($parts) - 2] . $parts[count($parts) - 1];
            unset($parts[count($parts) - 1]);
        }

        $parts[count($parts) - 1] = $parts[count($parts) - 1] . $splitPart;

        return implode(' ', $parts);
    }


}