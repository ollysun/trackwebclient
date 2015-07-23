<?php
namespace Adapter\Globals;

class ServiceConstant {

    const BASE_PATH = "http://local.courierplus.com";

    const USER_TYPE_ADMIN = 1;
    const USER_TYPE_MANAGER = 2;
    const USER_TYPE_CASHIER = 3;
    const USER_TYPE_AGENT = 4;
    const USER_TYPE_CUSTOMER = 5;
    const USER_TYPE_SUPPORT = 6;
    const USER_TYPE_ACCOUNTANT = 7;
    const USER_TYPE_SUPER_ADMIN = 8;

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

    const URL_USER_BY_PHONE = '/user/getByPhone';
    const URL_CREATE_USER = 'admin/register';
    public static function getStatus($status){
        switch($status){
            case ServiceConstant::ACTIVE:
                return 'Active';
                break;
            case ServiceConstant::INACTIVE:
                return 'Inactive';
                break;
            case ServiceConstant::IN_TRANSIT:
                return 'In Transit';
                break;
            case ServiceConstant::REMOVED:
                return 'Removed';
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