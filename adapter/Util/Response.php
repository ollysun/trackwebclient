<?php


namespace Adapter\Util;


class Response {
    const P_STATUS = 'status';
    const P_DATA = 'data';
    const P_ERROR_DESC = 'error_description';
    const P_ERROR_NAME = 'error_name';
    const P_MESSAGE = 'msg';
    const P_HTTP_STATUS = 'http_status';

    const STATUS_OK = 1;
    const STATUS_ERROR = 2;
    const STATUS_UNKNOWN = 600;

    public static function success($data, $as_json=false){
        $package = array(
            Response::P_STATUS => Response::STATUS_OK,
            Response::P_DATA => $data
        );
        return ($as_json) ? json_encode($package) : $package;
    }

    public static function error($error_name, $error_desc, $as_json=false){
        $package = array(
            Response::P_STATUS => Response::STATUS_ERROR,
            Response::P_ERROR_NAME => $error_name,
            Response::P_ERROR_DESC => $error_desc,
        );
        return ($as_json) ? json_encode($package) : $package;
    }

    public static function custom($status, $data, $message, $as_json=false){
        $package = array(
            Response::P_STATUS => $status,
            Response::P_DATA => $data,
            Response::P_MESSAGE => $message,
        );
        return ($as_json) ? json_encode($package) : $package;
    }

    public static function direct($raw_response, $as_json=false){
        if ($as_json){
            return $raw_response;
        }
        return json_decode($raw_response, true);
    }

    public static function unknown($http_status, $raw_info, $as_json=false){
        $package = array(
            Response::P_STATUS => Response::STATUS_UNKNOWN,
            Response::P_HTTP_STATUS => $http_status,
            Response::P_DATA => $raw_info,
        );
        return ($as_json) ? json_encode($package) : $package;
    }
}