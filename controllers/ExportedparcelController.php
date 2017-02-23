<?php
/**
 * Created by PhpStorm.
 * User: RotelandO
 * Date: 7/25/15
 * Time: 1:06 PM
 */

namespace app\controllers;


use Adapter\AdminAdapter;
use Adapter\BankAdapter;
use Adapter\BillingPlanAdapter;
use Adapter\BranchAdapter;
use Adapter\CodTellerAdapter;
use Adapter\CompanyAdapter;
use Adapter\ExportedParcelAdapter;
use Adapter\Globals\HttpStatusCodes;
use Adapter\Globals\ServiceConstant;
use Adapter\ParcelAdapter;
use Adapter\RtdTellerAdapter;
use Adapter\UserAdapter;
use Adapter\RefAdapter;
use Adapter\RegionAdapter;
use Adapter\RequestHelper;
use Adapter\ResponseHandler;
use Adapter\Util\Calypso;
use Adapter\Util\Util;
use app\models\BulkShipmentModel;
use app\services\HubService;
use Adapter\TellerAdapter;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\helpers\VarDumper;
use yii\web\Response;
use Adapter\RouteAdapter;
use yii\web\UploadedFile;

/**
 * Class ShipmentsController
 * @package app\controllers
 */
class ExportedparcelController extends BaseController
{
    public $userData = null;
    public $branch_to_view = null;

    public function beforeAction($action)
    {
        $this->userData = (Calypso::getInstance()->session('user_session'));
        $this->branch_to_view = ($this->userData['role_id'] == ServiceConstant::USER_TYPE_SUPER_ADMIN) ? null :
            ($this->userData['role_id'] == ServiceConstant::USER_TYPE_ADMIN) ? null : Calypso::getValue($this->userData, 'branch_id'); //displays all when null
        //print_r($this->userData);
        if (empty($this->userData)) {
            return false;
        }
        return parent::beforeAction($action);
    }

    public function actionIndex(){

        return $this->render('allparcel', array());
    }

    public function actionAllparcel($page = 1, $search = false, $page_width = null)
    {
        $from_date = date('Y-m-d');
        $to_date = date('Y-m-d');
        $agent_id = Yii::$app->request->get('agent_id');
        $agent_assignment = Yii::$app->request->get('agent_assignment');

        if ($page_width != null) {
            $this->page_width = $page_width;
            Calypso::getInstance()->cookie('page_width', $page_width);
        }

        if(Yii::$app->request->isPost){
            $records = \Yii::$app->request->post();
             if ($records['task'] == 'assign_agent') {
                if (!isset($records['agent_id'], $records['agent_tracking_number'], $records['parcel_id'])) {
                    $this->flashError("Invalid parameter(s) sent!");
                } else {
                    $agent_adapter  = new ExportedParcelAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
                    $agent_adapter = $agent_adapter->addAgentAssigned($records);
                    $response = new ResponseHandler($agent_adapter);
                  //  dd($response->getData());
                    if ($response->getStatus() == ResponseHandler::STATUS_OK) {
                        $this->flashSuccess('Agent successfully assigned');
                    } else {
                        $messages = '';
                        $errors = $response->getError();
                        if(is_array($errors)){
                            foreach ($errors as $key => $message) {
                                $messages .= "$key: $message<br/>";
                            }
                        }else $messages = $errors;
                        $this->flashError($messages);
                    }
                }
            }
        }

        $page_width = is_null($page_width) ? $this->page_width : $page_width;
        $offset = ($page - 1) * $page_width;
        $adapter = new ExportedParcelAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
        $response = $agent_assignment == 'Unassigned'?
            $adapter->getAllUnassigned(['from' => $from_date, 'to' => $to_date, 'with_total_count' => 1]):
            $adapter->getAll(['from' => $from_date, 'to' => $to_date,
                'with_total_count' => 1, 'agent_id' => $agent_id])
        ;

        $response = new ResponseHandler($response);
        if($response->isSuccess()){
            $data = $response->getData();

            $total_count = $data['total_count'];
            $data = $data['parcels'];

        }else{
            $this->flashError('Error in loading parcel');
            $data = [];
            $total_count = null;
        }

        //GetAllAgent
        $adapter = new ExportedParcelAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
        $response = $adapter->getAllAgents();

        $response = new ResponseHandler($response);
        if($response->isSuccess()){
            $agents = $response->getData();
        }else{
            $this->flashError('Error in loading parcel');
            $agents = [];
            $total_count = null;
        }

        return $this->render('allparcel',
            array('agent_id' => $agent_id, 'parcels' => $data,'agents' => $agents,
                'from_date' => $from_date, 'to_date' => $to_date,
                'offset' => $offset, 'page_width' => $this->page_width,
                'total_count' => $total_count, 'agent_assignment' => $agent_assignment));
    }

  }