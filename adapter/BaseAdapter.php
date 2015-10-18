<?php

namespace Adapter;

use Adapter\Util\CurlAgent;
use Adapter\Util\Response;

abstract class BaseAdapter
{
    const HTTP_GET = 1;
    const HTTP_POST = 2;

    const HTTP_STATUS_OK = 200;
    const HTTP_STATUS_CREATED = 201;
    const HTTP_STATUS_BAD_REQUEST = 400;

    protected $_curlagent;
    protected $_client_id;
    protected $_access_token;
    protected $_response_as_json;
    protected $_use_root_path;
    protected $lastErrorMessage;

    public function __construct($client_id = null, $access_token = null, $response_as_json = false, $use_root_path = true)
    {
        $this->_client_id = $client_id;
        $this->_access_token = $access_token;
        $this->_response_as_json = $response_as_json;
        $this->_use_root_path = $use_root_path;
    }

    /**
     * @return boolean
     */
    public function isResponseAsJson()
    {
        return $this->_response_as_json;
    }

    /**
     * @param boolean $response_as_json
     */
    public function setResponseAsJson($response_as_json)
    {
        $this->_response_as_json = $response_as_json;
    }

    /**
     * @return boolean
     */
    public function isUseRootPath()
    {
        return $this->_use_root_path;
    }

    /**
     * @param boolean $use_root_path
     */
    public function setUseRootPath($use_root_path)
    {
        $this->_use_root_path = $use_root_path;
    }

    /**
     * @return string|null
     */
    public function getAccessToken()
    {
        return $this->_access_token;
    }

    /**
     * @param string $access_token
     */
    public function setAccessToken($access_token)
    {
        $this->_access_token = $access_token;
    }

    /**
     * @return string|null
     */
    public function getClientId()
    {
        return $this->_client_id;
    }

    /**
     * @param string $client_id
     */
    public function setClientId($client_id)
    {
        $this->_client_id = $client_id;
    }

    protected function request($url, $params, $http_method)
    {
        $this->_curlagent = new CurlAgent('', true);
        if ($this->_access_token != null) {
            $this->_curlagent->setHeader('i', $this->_client_id);
            $this->_curlagent->setHeader('a', $this->_access_token);
        }

        $url = trim($url);
        if ($this->_use_root_path) {
            $url = \Yii::$app->params['apiUrl'] . ltrim($url, '/');
        }

        if ($http_method == BaseAdapter::HTTP_POST) {
            $this->_curlagent->setPost($params);
        } else if ($http_method == BaseAdapter::HTTP_GET) {
            $this->injectUrlParams($url, $params);
        }
        $this->_curlagent->createCurl($url);
        if ($this->_curlagent->getHttpStatus() == BaseAdapter::HTTP_STATUS_OK) {
            return Response::direct($this->_curlagent->getResponse(), $this->_response_as_json);
        } else {
            return Response::unknown($this->_curlagent->getHttpStatus(), $this->_curlagent->getResponse());
        }
    }

    protected function injectUrlParams(&$url, $params)
    {
        $url = trim($url);
        $url_params = array();
        foreach ($params as $key => $value) {
            $url_params[] = $key . '=' . urlencode($value);
        }

        $url_query = parse_url($url, PHP_URL_QUERY);

        if ($url_query == null) {
            $url = rtrim($url, '?');
            $url .= '?';
        } else {
            $url = rtrim($url, '&');
            $url .= '&';
        }

        $url .= join('&', $url_params);
    }

    public function getHttpStatus()
    {
        return $this->_curlagent->getHttpStatus();
    }

    /**
     * Decode response
     * @author Adeyemi Olaoye <yemi@cottacush.com>
     * @param $response
     * @return bool | mixed
     */
    public function decodeResponse($response)
    {
        if ($response) {
            if ($response['status'] === Response::STATUS_OK) {
                return $response['data'];
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * Gets the last error message
     * @author Adegoke Obasa <goke@cottacush.com>
     * @return mixed
     */
    public function getLastErrorMessage()
    {
        return $this->lastErrorMessage;
    }

}