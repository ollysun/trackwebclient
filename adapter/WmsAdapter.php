<?php
/**
 * Created by PhpStorm.
 * User: ELACHI
 * Date: 1/13/2017
 * Time: 9:18 AM
 */

namespace Adapter;


class WmsAdapter extends BaseAdapter
{
    public function __construct($client_id = null, $access_token = null, $response_as_json = false, $use_root_path = false)
    {
        parent::__construct($client_id, $access_token, $response_as_json, $use_root_path);
    }

    public function getStocks($key){
        return $this->request('http://superfluxgroup.com/wms/api/stocks/' . $key, [], self::HTTP_GET);
    }

    public function getStockBySku($key, $sku){
        return $this->request('http://superfluxgroup.com/wms/api/stocksbysku', ['key' => $key, 'sku' => $sku], self::HTTP_GET);
    }

    public function getStocksByLocation($key, $location){
        return $this->request('http://superfluxgroup.com/wms/api/stockByLoc', ['key' => $key, 'loc' => $location], self::HTTP_GET);
    }

    public function getStocksBySkuAndLocation($key, $sku, $location){
        return $this->request('http://superfluxgroup.com/wms/api/stockBySkuLoc',
            ['key' => $key, 'loc' => $location, 'sku' => $sku], self::HTTP_GET);
    }

    public function callApi($endpoint, array $filter, $http_method = self::HTTP_GET){
        return $this->request("http://superfluxgroup.com/wms/api/$endpoint", $filter, $http_method);
    }
}