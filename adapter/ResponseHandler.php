<?php
/**
 * Created by PhpStorm.
 * User: epapa
 * Date: 5/2/15
 * Time: 7:32 AM
 */

namespace Adapter;


class ResponseHandler
{

    private $status;
    private $data;
    private $error;
    public static $lastStatus;

    /**
     * Key placeholders
     */
    const P_STATUS = 'status';
    const P_DATA = 'data';
    const P_MESSAGE = 'message';
    const P_ACCESS_TOKEN = 'access_token';

    /**
     * Standard status codes
     */
    const STATUS_OK = 1;
    const STATUS_ERROR = 2;
    const STATUS_ACCESS_DENIED = 3;
    const STATUS_LOGIN_REQUIRED = 4;
    const STATUS_NOT_FOUND = 5;
    const STATUS_INVALID = 6;

    /**
     * Custom Error Message
     */
    const INVALID_JSON = "Invalid Json or Parse Error";
    const NO_MESSAGE = "No Message";

    public function __construct($response)
    {
        if (is_null($response) || !is_array($response)) {
            $this->status = self::STATUS_INVALID;
            $this->error = self::INVALID_JSON;
        } else {
            if (isset($response[self::P_STATUS])) {
                $this->status = $response[self::P_STATUS];
                ResponseHandler::$lastStatus = $response[self::P_STATUS];
                if ($this->status == self::STATUS_OK) {
                    RequestHelper::setAccessToken(isset($response[self::P_ACCESS_TOKEN]) ? $response[self::P_ACCESS_TOKEN] : null);

                    $this->data = isset($response[self::P_DATA]) ? $response[self::P_DATA] : null;
                } else {
                    $this->error = isset($response[self::P_MESSAGE]) ? $response[self::P_MESSAGE] : self::NO_MESSAGE;
                }
            }
        }
    }
    public static function isLoginRequired()
    {
        return ResponseHandler::$lastStatus == self::STATUS_LOGIN_REQUIRED || ResponseHandler::$lastStatus == self::STATUS_ACCESS_DENIED;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param mixed $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param mixed $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }

    /**
     * @return mixed
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * @param mixed $error
     */
    public function setError($error)
    {
        $this->error = $error;
    }

    public function getValue($key)
    {
        return (isset($this->data) && isset($this->data[$key])) ? $this->data[$key] : null;
    }

    /**
     * Checks if the request was successful
     * @author Adegoke Obasa <goke@cottacush.com>
     * @return bool
     */
    public function isSuccess()
    {
        return $this->status == self::STATUS_OK;
    }

}