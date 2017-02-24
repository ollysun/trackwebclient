<?php

namespace Adapter;

use Adapter\Globals;
use Adapter\Globals\ServiceConstant;
use Adapter\Util\Calypso;
use Adapter\Util\Response;
use yii\helpers\Json;
use Adapter\Util\Util;

/**
 * Created by PhpStorm.
 * User: Raphael Ikediashi
 * Date: 2/20/2017
 * Time: 1:43 PM
 */

class ExportedParcelAdapter extends BaseAdapter
{
    public function getAll(array $data)
    {
        return $this->request(ServiceConstant::URL_EXPORTED_GET_ALL, $data, self::HTTP_GET);
    }

    public function getAllUnassigned(array $data)
    {
        return $this->request(ServiceConstant::URL_EXPORTED_GET_ALL_UNASSIGNED, $data, self::HTTP_GET);
    }

    public function addAgentAssigned(array $data)
    {
        return $this->request(ServiceConstant::URL_EXPORTED_ASSIGN_AGENT, $data, self::HTTP_POST);
    }

    public function getAllAgents()
    {
        return $this->request(ServiceConstant::URL_EXPORTED_GET_ALL_AGENT, [], self::HTTP_GET);
    }

}
