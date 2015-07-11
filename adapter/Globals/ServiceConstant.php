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


    const URL_ADD_PARCEL = 'parcel/add/';
    const URL_GET_ONE_PARCEL = 'parcel/getone/';
    const URL_GET_ALL_PARCEL = 'parcel/getAll/';

    const URL_GET_ALL_BANKS = 'bank/getAll/';
    const URL_ADMIN_LOGIN = 'admin/login/';
    const URL_REF_BANK = 'ref/banks/';
    const URL_REF_SHIPMENT = 'ref/shipmentType/';
    const URL_REF_deliveryType = 'ref/deliveryType/';
    const URL_REF_parcelType = 'ref/parcelType/';

}