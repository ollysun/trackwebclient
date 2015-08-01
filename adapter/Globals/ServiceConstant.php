<?php
namespace Adapter\Globals;

class ServiceConstant {

    const BASE_PATH = "http://local.courierplus.com";

    const USER_TYPE_SUPER_ADMIN = -1;
    const USER_TYPE_ADMIN = 1;
    const USER_TYPE_OFFICER = 2;
    const USER_TYPE_SWEEPER = 3;
    const USER_TYPE_DISPATCHER = 4;
    /*const USER_TYPE_MANAGER = 2;
    const USER_TYPE_CASHIER = 3;
    const USER_TYPE_AGENT = 4;
    const USER_TYPE_CUSTOMER = 5;
    const USER_TYPE_SUPPORT = 6;
    const USER_TYPE_ACCOUNTANT = 7;*/

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

    const URL_ADD_PARCEL = 'parcel/add/';
    const URL_GET_ONE_PARCEL = 'parcel/getone/';
    const URL_GET_ALL_PARCEL = 'parcel/getAll/';
    const URL_MOVE_TO_FOR_SWEEPER = '/parcel/moveToForSweeper/';
    const URL_MOVE_TO_IN_TRANSIT = '/parcel/moveToInTransit/';
    const URL_MOVE_TO_ARRIVAL = '/parcel/moveToArrival/';
    const URL_MOVE_FOR_DELIVERY = '/parcel/moveToForDelivery/';

    const URL_GET_ALL_BANKS = 'bank/getAll/';

    const URL_GET_ALL_BRANCH = 'branch/getall/';
    const URL_GET_ALL_EC_IN_HUB = 'branch/getallec/';

    const URL_GET_BANK_ACCOUNT = 'bankaccount/getAll/';

    const URL_ADMIN_LOGIN = 'admin/login/';
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

    const URL_WEIGHT_ADD = 'weightrange/add';
    const URL_WEIGHT_EDIT = 'weightrange/edit';
    const URL_WEIGHT_CHANGE_STATUS = 'weightrange/changeStatus';
    const URL_WEIGHT_FETCH_ID = 'weightrange/fetchById';
    const URL_WEIGHT_FETCH_ALL = 'weightrange/fetchAll';

    const URL_ONFORWARDING_ADD = 'onforwardingcharge/add';
    const URL_ONFORWARDING_EDIT = 'onforwardingcharge/edit';
    const URL_ONFORWARDING_CHANGE_STATUS = 'onforwardingcharge/changeStatus';
    const URL_ONFORWARDING_FETCH_ID = 'onforwardingcharge/fetchById';
    const URL_ONFORWARDING_FETCH_ALL = 'onforwardingcharge/fetchAll';


    const URL_CREATE_USER = 'admin/register';
    const URL_GET_USERS = '/admin/getAll';
    const URL_GET_USER = '/admin/getone';

    const URL_GET_STAFF_BY_ID = '/admin/getOne';
    public static function getStatus($status){
        switch($status){
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

        }
    }
    public static function getStatusRef(){
        return [ServiceConstant::IN_TRANSIT,ServiceConstant::DELIVERED,ServiceConstant::CANCELLED,ServiceConstant::FOR_ARRIVAL
        ,ServiceConstant::FOR_DELIVERY,ServiceConstant::FOR_SWEEPER,ServiceConstant::COLLECTED];
    }

}