<?php
/**
 * Created by PhpStorm.
 * User: RotelandO
 * Date: 7/25/15
 * Time: 1:02 PM
 */

namespace app\controllers;

use Adapter\AuditAdapter;
use Adapter\BillingPlanAdapter;
use Adapter\BranchAdapter;
use Adapter\BusinessManagerAdapter;
use Adapter\BusinessZoneAdapter;
use Adapter\CompanyAdapter;
use Adapter\Globals\ServiceConstant;
use Adapter\RefAdapter;
use Adapter\RegionAdapter;
use Adapter\ResponseHandler;
use Adapter\Util\Calypso;
use Adapter\Util\ResponseCodes;
use Adapter\Util\ResponseMessages;
use Adapter\ZoneAdapter;
use Adapter\RequestHelper;
use app\services\AdminService;
use Yii;
use Adapter\Util\Response;
use Adapter\AdminAdapter;
use Adapter\UserAdapter;
use Adapter\RouteAdapter;
use yii\helpers\Url;


class AdminController extends BaseController
{
    public function beforeAction($action)
    {
        $this->enableCsrfValidation = false;
        return parent::beforeAction($action);
    }

    public function actionHubmapping()
    {
        if (Yii::$app->request->isPost) {
            $data = \Yii::$app->request->post();

            $from_id = Calypso::getValue($data, 'from_id');
            $to_ids = Calypso::getValue($data, 'branches');
            $zone_ids = Calypso::getValue($data, 'zones');

            $matrix_info = [];
            for ($k = 0; $k < count($to_ids); $k++) {
                $matrix_info[] = array('to_branch_id' => $to_ids[$k], 'from_branch_id' => $from_id, 'zone_id' => $zone_ids[$k]);
            }

            $zone = new ZoneAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
            $response = $zone->saveMatrix(json_encode($matrix_info));
            if ($response['status'] === Response::STATUS_OK) {
                return $this->redirect('/admin/managebranches');
            } else {
                Yii::$app->session->setFlash('danger', 'There was a problem creating the zone.' . $response['message']);
                return $this->refresh();
            }
        }

        $hub_id = \Yii::$app->request->get('hub');
        $hubAdp = new BranchAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
        $hub = $hubAdp->getOneHub($hub_id);
        $hub = new ResponseHandler($hub);
        $hub = $hub->getStatus() == ResponseHandler::STATUS_OK ? $hub->getData() : [];

        $hubAdp = new BranchAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
        $hubs = $hubAdp->getAllHubs(false);
        $hubs = new ResponseHandler($hubs);
        $hub_list = $hubs->getStatus() == ResponseHandler::STATUS_OK ? $hubs->getData() : [];

        $zAdp = new ZoneAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
        $zones = $zAdp->getZones();
        $zones = new ResponseHandler($zones);
        $zones_list = $zones->getStatus() == ResponseHandler::STATUS_OK ? $zones->getData() : [];

        return $this->render('new_hub_mapping', array('hub' => $hub, 'hubs' => $hub_list, 'zones' => $zones_list));
    }

    public function actionManagebranches($page = 1)
    {
        $offset = ($page - 1) * $this->page_width;
        if (Yii::$app->request->isPost) {
            $entry = Yii::$app->request->post();
            $error = [];

            $hub_data = [];
            $hub_data['name'] = Calypso::getValue($entry, 'name', null);
            $hub_data['address'] = Calypso::getValue($entry, 'address');
            $hub_data['branch_type'] = ServiceConstant::BRANCH_TYPE_HUB;
            $hub_data['state_id'] = Calypso::getValue($entry, 'state_id');
            $hub_data['status'] = Calypso::getValue($entry, 'status');
            $hub_data['branch_id'] = Calypso::getValue($entry, 'id', null);

            $task = Calypso::getValue(Yii::$app->request->post(), 'task');

            if (($task == 'create' || $task == 'edit') && (empty($hub_data['name']) || empty($hub_data['address']))) {
                $error[] = "All details are required!";
            }
            if (!empty($error)) {
                $errorMessages = implode('<br />', $error);
                Yii::$app->session->setFlash('danger', $errorMessages);
            } else {
                $hub = new BranchAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
                if ($task == 'create') {
                    $response = $hub->createNewHub($hub_data);
                    if ($response['status'] === Response::STATUS_OK) {
                        Yii::$app->session->setFlash('success', 'Hub has been created successfully.');
                        return $this->redirect("/admin/hubmapping?hub={$response['data']['id']}");
                    } else {
                        Yii::$app->session->setFlash('danger', 'There was a problem creating the hub. Please try again.');
                    }
                } elseif ($task != 'filter') {
                    $response = $hub->editOneHub($hub_data, $task);
                    if ($response['status'] === Response::STATUS_OK) {
                        Yii::$app->session->setFlash('success', 'Hub has been edited successfully.');
                    } else {
                        Yii::$app->session->setFlash('danger', 'There was a problem editing the hub. Please try again.' . $response['message']);
                    }
                }
            }
            return $this->refresh();
        }

        $refAdp = new RefAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
        $states = $refAdp->getStates(ServiceConstant::DEFAULT_COUNTRY); // Hardcoded Nigeria for now
        $states = new ResponseHandler($states);

        $filter_state_id = Yii::$app->request->get('filter_state_id', null);
        $hubAdp = new BranchAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
        $hubs = $hubAdp->getHubs($filter_state_id, $offset, $this->page_width, true);
        $hubs = new ResponseHandler($hubs);

        $state_list = $states->getStatus() == ResponseHandler::STATUS_OK ? $states->getData() : [];
        $hub_list = $hubs->getStatus() == ResponseHandler::STATUS_OK ? $hubs->getData() : [];

        $total_count = Calypso::getValue($hub_list, 'total_count', 0);
        $hub_list = Calypso::getValue($hub_list, 'hub_data', null);
        return $this->render('managehubs', array('States' => $state_list, 'filter_state_id' => $filter_state_id, 'hubs' => $hub_list, 'total_count' => $total_count, 'page_width' => $this->page_width, 'offset' => $offset));
    }

    public function actionManageecs($page = 1)
    {
        $offset = ($page - 1) * $this->page_width;

        if (Yii::$app->request->isPost) {
            $entry = Yii::$app->request->post();
            $task = Calypso::getValue($entry, 'task', '');
            $error = [];

            $data = [];
            $data['name'] = Calypso::getValue($entry, 'name', null);
            $data['address'] = Calypso::getValue($entry, 'address');
            $data['branch_type'] = ServiceConstant::BRANCH_TYPE_EC;
            $data['status'] = Calypso::getValue($entry, 'status');
            $data['hub_id'] = Calypso::getValue($entry, 'hub_id', null);
            $data['branch_id'] = Calypso::getValue($entry, 'id', null);
            $data['ec_id'] = Calypso::getValue($entry, 'id', null);
            $data['state_id'] = Calypso::getValue($entry, 'state_id', null);

            if (($task == 'create' || $task == 'edit') && (empty($data['name']) || empty($data['address']))) {
                $error[] = "All details are required!";
            }
            if (!empty($error)) {
                $errorMessages = implode('<br />', $error);
                Yii::$app->session->setFlash('danger', $errorMessages);
            } else {
                $center = new BranchAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
                if ($task == 'create') {
                    $response = $center->createNewCentre($data);
                    if ($response['status'] === Response::STATUS_OK) {
                        Yii::$app->session->setFlash('success', 'Centre has been created successfully.');
                    } else {
                        Yii::$app->session->setFlash('danger', 'There was a problem creating the centre. Please try again.');
                    }
                } elseif ($task != 'filter') {
                    $response = $center->editOneCentre($data, $task);
                    if ($response['status'] === Response::STATUS_OK) {
                        Yii::$app->session->setFlash('success', 'Centre has been edited successfully.');
                    } else {
                        Yii::$app->session->setFlash('danger', 'There was a problem editing the hub. Please try again.');
                    }
                }
            }
            return $this->refresh();
        }
        $refAdp = new RefAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
        $states = $refAdp->getStates(ServiceConstant::DEFAULT_COUNTRY); // Hardcoded Nigeria for now
        $states = new ResponseHandler($states);

        $branchAdapter = new BranchAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
        $hubs = $branchAdapter->getHubs();
        $hubs = new ResponseHandler($hubs);
        $filter_hub_id = Yii::$app->request->get('filter_hub_id', null);

        $centres = $branchAdapter->getCentres($filter_hub_id, $offset, $this->page_width, true, true);
        $centres = new ResponseHandler($centres);

        $state_list = $states->getStatus() == ResponseHandler::STATUS_OK ? $states->getData() : [];
        $hub_list = $hubs->getStatus() == ResponseHandler::STATUS_OK ? $hubs->getData() : [];

        $centres_list_and_total_count = $centres->getStatus() == ResponseHandler::STATUS_OK ? $centres->getData() : [];
        $centres_list = $centres_list_and_total_count['branch_data'];
        $total_count = $centres_list_and_total_count['total_count'];
        return $this->render('manageecs', array('total_count' => $total_count, 'page_width' => $this->page_width, 'offset' => $offset, 'States' => $state_list, 'hubs' => $hub_list, 'centres' => $centres_list, 'filter_hub_id' => $filter_hub_id));
    }

    //transit time
    public function actionManagetransittime(){
        $branchAdp = new BranchAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
        $response = new ResponseHandler($branchAdp->getAllHubs(false));
        $zoneAdapter = new ZoneAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
        $responseMatrix = new ResponseHandler($zoneAdapter->getTransitTime());
        $hubs = [];
        $hubsMatrix = [];
        if ($response->getStatus() == ResponseHandler::STATUS_OK) {
            $hubs = $response->getData();
        }
        if ($responseMatrix->getStatus() == ResponseHandler::STATUS_OK) {
            $hubsMatrix = $responseMatrix->getData();
        }
        $mapList = [];
        foreach ($hubsMatrix as $mapping) {
            $mapList[$mapping['from_branch_id'] . '_' . $mapping['to_branch_id']] = $mapping;
        }
        return $this->render('manageTransitTime', ["hubs" => $hubs, "hubsMatrix" => $hubsMatrix, "matrixMap" => $mapList]);

    }

    public function actionUpdatemapping()
    {
        $entry = Yii::$app->request->post();
        if (!empty($entry)) {
            $zAdp = new ZoneAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
            $zones = $zAdp->saveTransitTime(json_encode([$entry]));
            $zones = new ResponseHandler($zones);

            if ($zones->getStatus() == ResponseHandler::STATUS_OK) {
                $d = $zones->getData();
                if (empty($d['bad_matrix_info'])) {
                    Yii::$app->session->setFlash('success', 'Transit time has been edited successfully.');
                } else {
                    Yii::$app->session->setFlash('danger', 'There was a problem editing the transit time. Please ensure these hubs have been mapped');
                }
            } else {
                Yii::$app->session->setFlash('danger', 'There was a problem editing the transit time. #Reason: Service refused request');
            }
            return $zones->getStatus() == ResponseHandler::STATUS_OK ? 1 : 0;
        }
        return 0;
    }

    public function actionRemovemapping()
    {
        $entry = Yii::$app->request->post();
        if (!empty($entry)) {
            $zAdp = new ZoneAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
            $response = $zAdp->removeTransitTime($entry);
            $response = new ResponseHandler($response);
            if ($response->getStatus() == ResponseHandler::STATUS_OK) {
                $d = $response->getData();
                if (empty($d['bad_matrix_info'])) {
                    Yii::$app->session->setFlash('success', 'Transit time mapping removed successfully.');
                } else {
                    Yii::$app->session->setFlash('danger', 'There was a problem removing the transit time mapping. Please ensure these hubs have been mapped');
                }
            } else {
                Yii::$app->session->setFlash('danger', 'There was a problem removing the transit time mapping. #Reason: Service refused request');
            }
            return $response->getStatus() == ResponseHandler::STATUS_OK ? 1 : 0;
        }
        return 0;
    }

    //distance
    public function actionDistancetable(){
        $branchAdp = new BranchAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
        $response = new ResponseHandler($branchAdp->getAllHubs(false));
        $zoneAdapter = new ZoneAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
        $responseMatrix = new ResponseHandler($zoneAdapter->getDistanceTable());
        $hubs = [];
        $hubsMatrix = [];
        if ($response->getStatus() == ResponseHandler::STATUS_OK) {
            $hubs = $response->getData();
        }
        if ($responseMatrix->getStatus() == ResponseHandler::STATUS_OK) {
            $hubsMatrix = $responseMatrix->getData();
        }
        $mapList = [];
        foreach ($hubsMatrix as $mapping) {
            $mapList[$mapping['from_branch_id'] . '_' . $mapping['to_branch_id']] = $mapping;
        }

        return $this->render('distanceTable', ["hubs" => $hubs, "hubsMatrix" => $hubsMatrix, "matrixMap" => $mapList]);

    }

    public function actionUpdatedistancemapping()
    {
        $entry = Yii::$app->request->post();
        if (!empty($entry)) {
            $zAdp = new ZoneAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
            $zones = $zAdp->saveDistance(json_encode([$entry]));
            $zones = new ResponseHandler($zones);

            if ($zones->getStatus() == ResponseHandler::STATUS_OK) {
                $d = $zones->getData();
                if (empty($d['bad_matrix_info'])) {
                    Yii::$app->session->setFlash('success', 'Distance table has been edited successfully.');
                } else {
                    Yii::$app->session->setFlash('danger', 'There was a problem editing the distance table. Please ensure these hubs have been mapped');
                }
            } else {
                Yii::$app->session->setFlash('danger', 'There was a problem editing the distance table. #Reason: Service refused request');
            }
            return $zones->getStatus() == ResponseHandler::STATUS_OK ? 1 : 0;
        }
        return 0;
    }

    public function actionRemovedistancemapping()
    {
        $entry = Yii::$app->request->post();
        if (!empty($entry)) {
            $zAdp = new ZoneAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
            $response = $zAdp->removeDistance($entry);
            $response = new ResponseHandler($response);
            if ($response->getStatus() == ResponseHandler::STATUS_OK) {
                $d = $response->getData();
                if (empty($d['bad_matrix_info'])) {
                    Yii::$app->session->setFlash('success', 'Distance mapping removed successfully.');
                } else {
                    Yii::$app->session->setFlash('danger', 'There was a problem removing the distance mapping. Please ensure these hubs have been mapped');
                }
            } else {
                Yii::$app->session->setFlash('danger', 'There was a problem removing the transit distance. #Reason: Service refused request');
            }
            return $response->getStatus() == ResponseHandler::STATUS_OK ? 1 : 0;
        }
        return 0;
    }


    /**
     * Manage Staff action
     * @author Adegoke Obasa <goke@cottacush.com>
     * @author Olawale Lawal <wale@cottacush.com>
     * @param int $offset
     * @param string $role
     * @return string
     */
    public function actionManagestaff($offset = 0, $role = '-1')
    {
        if (Yii::$app->request->isPost) {
            $data = Yii::$app->request->post();
            $user = new UserAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
            $id = Calypso::getInstance()->getValue($data, 'id', '');

            if ($id === '') {
                // Create
                $resp = $user->createNewUser(Calypso::getInstance()->getValue($data, 'role'),
                    Calypso::getInstance()->getValue($data, 'branch'), Calypso::getInstance()->getValue($data, 'staff_id'),
                    Calypso::getInstance()->getValue($data, 'email'), Calypso::getInstance()->getValue($data, 'firstname') . ' ' . Calypso::getInstance()->getValue($data, 'lastname'),
                    Calypso::getInstance()->getValue($data, 'phone'));

                $creationResponse = new ResponseHandler($resp);
                if ($creationResponse->getStatus() == ResponseHandler::STATUS_OK) {
                    Yii::$app->session->setFlash('success', 'User has been created successfully.');
                } else {
                    if (!is_null($creationResponse->getError()) && $creationResponse->getError() != ResponseHandler::NO_MESSAGE) {
                        Yii::$app->session->setFlash('danger', "An error occurred while trying to create user. Reason:" . $creationResponse->getError());
                    } else {
                        Yii::$app->session->setFlash('danger', 'There was a problem creating this User. Please try again.');
                    }
                }
            } else {
                // Update
                $resp = $user->updateUser($id,
                    Calypso::getInstance()->getValue($data, 'role'),
                    Calypso::getInstance()->getValue($data, 'branch'),
                    Calypso::getInstance()->getValue($data, 'staff_id'),
                    Calypso::getInstance()->getValue($data, 'email'),
                    Calypso::getInstance()->getValue($data, 'firstname') . ' ' . Calypso::getInstance()->getValue($data, 'lastname'),
                    Calypso::getInstance()->getValue($data, 'phone'),
                    Calypso::getInstance()->getValue($data, 'status')
                );

                $updateResponse = new ResponseHandler($resp);
                if ($updateResponse->getStatus() == ResponseHandler::STATUS_OK) {
                    Yii::$app->session->setFlash('success', 'User has been updated successfully.');
                } else {
                    if (!is_null($updateResponse->getError()) && $updateResponse->getError() != ResponseHandler::NO_MESSAGE) {
                        Yii::$app->session->setFlash('danger', "An error occurred while trying to update user. Reason:" . $updateResponse->getError());
                    } else {
                        Yii::$app->session->setFlash('danger', 'There was a problem updating this User. Please try again.');
                    }
                }
            }

            return $this->refresh();
        }

        $refAdp = new RefAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
        $states = $refAdp->getStates(1);//Nigeria hardcoded for now ... No offense please.
        $states = new ResponseHandler($states);
        $rolesAdp = new RefAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
        $roles = $rolesAdp->getRoles();
        $roles = new ResponseHandler($roles);
        $state_list = $states->getStatus() == ResponseHandler::STATUS_OK ? $states->getData() : [];
        $role_list = $roles->getStatus() == ResponseHandler::STATUS_OK ? $roles->getData() : [];


        $staffAdp = new AdminAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
        if (isset(Calypso::getInstance()->get()->search) && strlen(Calypso::getInstance()->get()->search) > 0) {
            $is_email = !(filter_var(Calypso::getInstance()->get()->search, FILTER_VALIDATE_EMAIL) === false);
            $staff_data = $staffAdp->searchStaffMembers(Calypso::getInstance()->get()->search, $is_email, $offset, $this->page_width);
        } else {
            $staff_data = $staffAdp->getStaffMembers($offset, $this->page_width, $role);
        }
        $resp = new ResponseHandler($staff_data);
        $staffMembers = $resp->getData();


        return $this->render('managestaff', ['states' => $state_list, 'roles' => $role_list, 'staffMembers' => $staffMembers, 'offset' => $offset, 'role' => $role, 'page_width' => $this->page_width]);
    }


    public function actionBusineszones($page = 1, $page_width = null){
        $adapter = new BusinessZoneAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());

        if(Yii::$app->request->isPost){
            $entry = Yii::$app->request->post();
            $task = Calypso::getValue(Yii::$app->request->post(), 'task', '');
            $error = [];

            $data = [];
            $data['region_id'] = Calypso::getValue($entry, 'region_id', null);
            $data['name'] = Calypso::getValue($entry, 'name');
            $data['description'] = Calypso::getValue($entry, 'description');

            if (($task == 'create' || $task == 'edit') && (empty($data['region_id']) || empty($data['name']))) {
                $error[] = "All details are required!";
            }else{
                if($task == 'create'){
                    $response = $adapter->addBusinessZone($data['name'], $data['region_id'], $data['description']);
                    if($response['status'] == ResponseHandler::STATUS_OK){
                        $this->flashSuccess('Zone added successfully');
                    }else{
                        Yii::$app->session->setFlash('danger', 'There was a problem creating the Zone: ' . $response['message']);
                    }
                }
            }
        }


        if ($page_width != null) {
            $this->page_width = $page_width;
            Calypso::getInstance()->cookie('page_width', $page_width);
        }

        $page_width = is_null($page_width) ? $this->page_width : $page_width;
        $offset = ($page - 1) * $page_width;

        $filter = ['offset' => $offset, 'count' => $page_width, 'paginate' => true, 'with_region' => 1];
        if(isset(Calypso::getInstance()->get()->region_id)){
            $filter['region_id'] = Calypso::getInstance()->get()->region_id;
        }


        $filter_country = Calypso::getValue(Yii::$app->request->post(), 'filter_country', 1);
        $refAdp = new RefAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
        $regions = $refAdp->getRegions($filter_country);
        $regions = new ResponseHandler($regions);
        $region_list = $regions->getStatus() == ResponseHandler::STATUS_OK ? $regions->getData() : [];

        $result = $adapter->getAll($filter);

        if($result['status'] == ResponseHandler::STATUS_OK){
            $business_zones = $result['data']['business_zones'];
            $total_count = $result['data']['total_count'];
        }else{
            $business_zones = [];
            $total_count = 0;

        }

        return $this->render('business_zones', array('regions' => $region_list, 'business_zones' => $business_zones, 'total_count' => $total_count));
    }


    public function actionBusinessmanagers($page = 1, $page_width = null){
        $bmAdapter = new BusinessManagerAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());

        if(Yii::$app->request->isPost){
            $entry = Yii::$app->request->post();
            $task = Calypso::getValue(Yii::$app->request->post(), 'task', '');
            $error = [];

            $data = [];
            $data['region_id'] = Calypso::getValue($entry, 'region_id', null);
            $data['status'] = Calypso::getValue($entry, 'status');
            $data['staff_id'] = Calypso::getValue($entry, 'staff_id');

            if (($task == 'create' || $task == 'edit') && (empty($data['region_id']) || empty($data['staff_id']))) {
                $error[] = "All details are required!";
            }else{
                if($task == 'create'){
                    $response = $bmAdapter->addBusinessManager($data['region_id'], $data['staff_id']);
                    if($response['status'] == ResponseHandler::STATUS_OK){
                        $this->flashSuccess('BM added successfully');
                    }else{
                        Yii::$app->session->setFlash('danger', 'There was a problem creating the BM. ' . $response['message']);
                    }
                }elseif($task == 'edit'){
                    $response = $bmAdapter->changeRegion($data['staff_id'], $data['region_id']);
                    if($response['status'] == ResponseHandler::STATUS_OK){
                        $this->flashSuccess('BM updated successfully');
                    }else{
                        $this->flashError('There was a problem updating the BM. ' . $response['message']);
                    }
                }
            }
        }


        if ($page_width != null) {
            $this->page_width = $page_width;
            Calypso::getInstance()->cookie('page_width', $page_width);
        }

        $page_width = is_null($page_width) ? $this->page_width : $page_width;
        $offset = ($page - 1) * $page_width;

        $filter = ['offset' => $offset, 'count' => $page_width, 'paginate' => true];
        if(isset(Calypso::getInstance()->get()->region_id)){
            $filter['region_id'] = Calypso::getInstance()->get()->region_id;
        }


        $filter_country = Calypso::getValue(Yii::$app->request->post(), 'filter_country', 1);
        $refAdp = new RefAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
        $regions = $refAdp->getRegions($filter_country);
        $regions = new ResponseHandler($regions);
        $region_list = $regions->getStatus() == ResponseHandler::STATUS_OK ? $regions->getData() : [];

        $result = $bmAdapter->getAll($filter);

        if($result['status'] == ResponseHandler::STATUS_OK){
            $business_managers = $result['data']['business_managers'];
            $total_count = $result['data']['total_count'];
        }else{
            $business_managers = [];
            $total_count = 0;

        }

        return $this->render('business_managers', array('regions' => $region_list, 'business_managers' => $business_managers, 'total_count' => $total_count));
    }

    public function actionResetpassword()
    {
        if (Yii::$app->getRequest()->isPost) {
            $auth_id = Yii::$app->getRequest()->post('user_auth_id');
            $password = Yii::$app->getRequest()->post('password');

            if (!isset($auth_id, $password)) {
                $this->flashError('Required field(s) missing');
            }

            $user = new UserAdapter();
            $outcome = $user->resetPassword($auth_id, $password);
            if ($outcome === true) {
                $this->flashSuccess('Password has been changed');
            } else {
                $this->flashError($outcome);
            }
        }
        return $this->redirect('managestaff');
    }


    public function actionResetcompanyadminpassword()
    {
        if (Yii::$app->getRequest()->isPost) {
            $company_id = Yii::$app->getRequest()->post('company_id');
            $password = Yii::$app->getRequest()->post('password');

            if (!isset($company_id, $password)) {
                $this->flashError('Required field(s) missing');
            }

            $user = new UserAdapter();
            $outcome = $user->resetCompanyAdminPassword($company_id, $password);

            if($outcome->getStatus() == ResponseHandler::STATUS_OK) {
                $this->flashSuccess($outcome->getData());
            } else {
                $this->flashError($outcome->getError());
            }

        }
        return $this->back();
    }
    /**
     * Edit Company Action
     * @author Adegoke Obasa <goke@cottacush.com>
     * @author Olajide Oye <jide@cottacush.com>
     * @return string
     */
    public function actionEditcompany()
    {
        $companyAdapter = new CompanyAdapter();
        if (Yii::$app->request->isPost) {
            $data = Yii::$app->request->post();

            // Edit Company
            $status = $companyAdapter->editCompany($data);

            if ($status) {
                $this->flashSuccess("Company edited successfully");
            } else {
                $this->flashError($companyAdapter->getLastErrorMessage());
            }
        }

        return $this->redirect(Url::to("/admin/companies"));
    }

    /**
     * Manage Companies Action
     * @author Adegoke Obasa <goke@cottacush.com>
     * @author Olajide Oye <jide@cottacush.com>
     * @return string
     */
    public function actionCompanies()
    {
        $companyAdapter = new CompanyAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
        if (Yii::$app->request->isPost) {
            $data = Yii::$app->request->post();

            // Create Company
            $status = $companyAdapter->createCompany($data);

            if ($status) {
                $this->flashSuccess("Company created successfully");
            } else {
                Calypso::getInstance()->setPageData($data);
                $this->flashError($companyAdapter->getLastErrorMessage());
            }
            return $this->refresh();
        }

        $refAdapter = new RefAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
        $states = (new ResponseHandler($refAdapter->getStates(1)))->getData();

        $filters = [];

        $page = \Yii::$app->getRequest()->get('page', 1);

        $query = \Yii::$app->getRequest()->get('search');
        if (!is_null($query)) {
            $filters = ['name' => $query];
            $page = 1; // Reset page
        }

        // Add Offset and Count
        $offset = ($page - 1) * $this->page_width;
        $filters['offset'] = $offset;
        $filters['count'] = $this->page_width;

        $adapter = new CompanyAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
        $companiesData = $adapter->getCompanies($filters);
        $companies = Calypso::getValue($companiesData, 'companies', []);
        $totalCount = Calypso::getValue($companiesData, 'total_count', 0);

        $account_types = $companyAdapter->getAllAccountTypes();
        $this->decorateWithStatus($companies);


        $billingPlanAdapter = new BillingPlanAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
        $billingPlans = $billingPlanAdapter->getBillingPlans(['no_paginate' => 1, 'status' => ServiceConstant::ACTIVE]);


        $filter_country = Calypso::getValue(Yii::$app->request->post(), 'filter_country', 1);
        $refAdp = new RefAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
        $regions = $refAdp->getRegions($filter_country);
        $regions = new ResponseHandler($regions);
        $region_list = $regions->getStatus() == ResponseHandler::STATUS_OK ? $regions->getData() : [];


        $result = (new BusinessZoneAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken()))->getAll(['send_all']);
        if($result['status'] == ResponseHandler::STATUS_OK){
            $business_zones = $result['data']['business_zones'];
        }else{
            $business_zones = [];
        }

        $result = (new BusinessManagerAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken()))->getAll();

        if($result['status'] == ResponseHandler::STATUS_OK){
            $business_managers = $result['data']['business_managers'];
        }else{
            $business_managers = [];
        }

        return $this->render('companies', [
                'locations' => ['states' => $states],
                'companies' => $companies,
                'business_zones' => $business_zones,
                'business_managers' => $business_managers,
                'regions' => $region_list,
                'offset' => $offset,
                'total_count' => $totalCount,
                'page_width' => $this->page_width,
                'account_types' => $account_types,
                'billing_plans' => $billingPlans
            ]
        );
    }

    /**
     * View Company Action
     * @author Adegoke Obasa <goke@cottacush.com>
     */
    public function actionViewcompany()
    {
        $company_id = Yii::$app->request->get('id');

        $adapter = new CompanyAdapter();
        $company = $adapter->getCompany($company_id);

        $companyEcsResponse = $adapter->getAllEcs(['company_id' => $company_id]);


        $company_access = [];
        if(isset($company['reg_no'])){
            $company_access = $adapter->getCompanyAccess($company['reg_no']);

        }

        $billingAdapter = new BillingPlanAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
        $billingPlans = $billingAdapter->getCompanyBillingPlans(['company_id' => $company_id]);

        $all_billing_plans = $billingAdapter->getBillingPlans();

        return $this->render('viewcompany', ['company' => $company, 'billing_plans' => $billingPlans, 'all_billing_plans' => $all_billing_plans, 'company_access' => $company_access, 'ecs' => array_key_exists('ecs', $companyEcsResponse)? $companyEcsResponse['ecs']: []]);
    }


    public function actionManageapplicationaccess(){
        $entry = Yii::$app->request->post();
        $adapter = new CompanyAdapter();
        $response = $adapter->saveCompanyAccess($entry);
        if($response->isSuccess()){
            $data = $response->getData();
            $this->flashSuccess('Access information saved. Access Token: '.$data['token']);
        }

        else $this->flashError($response->getError());
        return $this->back();

    }

    /**
     * Returns JSON of cities
     * @author Adegoke Obasa <goke@cottacush.com>
     * @return \yii\web\Response
     */
    public function actionCities()
    {
        if (!Yii::$app->request->isAjax) {
            return $this->redirect(Url::toRoute("admin"));
        }

        $stateId = Yii::$app->request->get('state_id');

        if (is_null($stateId)) {
            $this->sendErrorResponse(ResponseMessages::INVALID_PARAMETERS, ResponseCodes::INVALID_PARAMETERS, null, 400);
        }

        $regionAdapter = new RegionAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
        $cities = (new ResponseHandler($regionAdapter->getAllCity(1, 0, $stateId, 0)))->getData();

        return $this->sendSuccessResponse($cities);
    }

    /**
     * Get Staff Details Ajax Action
     * @author Adegoke Obasa <goke@cottacush.com>
     * @return array|\yii\web\Response
     */
    public function actionGetstaff()
    {
        if (!Yii::$app->request->isAjax) {
            return $this->redirect(Url::toRoute("site"));
        }

        $staffId = Yii::$app->request->get('staff_id');

        if (is_null($staffId)) {
            $this->sendErrorResponse(ResponseMessages::INVALID_PARAMETERS, ResponseCodes::INVALID_PARAMETERS, null, 400);
        }

        $staff = (new ResponseHandler((new AdminAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken()))->getStaff($staffId)))->getData();
        return $this->sendSuccessResponse($staff);
    }

    public function actionManageroutes($page = 1)
    {
        $offset = ($page - 1) * $this->page_width;
        $search = Yii::$app->request->get('search',null);
        if (Yii::$app->request->isPost) {
            $entry = Yii::$app->request->post();
            $task = Calypso::getValue(Yii::$app->request->post(), 'task');

            if (($task == 'create' || $task == 'edit') && !isset($entry['route_name'], $entry['branch_id'])) {
                $error[] = "All details are required!";
            }
            if (!empty($error)) {
                $errorMessages = implode('<br />', $error);
                Yii::$app->session->setFlash('danger', $errorMessages);
            } else {
                $route = new RouteAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
                if ($task == 'create') {
                    $data = array('name' => $entry['route_name'], 'branch_id' => $entry['branch_id']);
                    $response = $route->createRoute($data);
                    $responseHandler = new ResponseHandler($response);

                    if ($responseHandler->getStatus() == ResponseHandler::STATUS_OK) {
                        Yii::$app->session->setFlash('success', 'Route has been created successfully.');
                    } else {
                        Yii::$app->session->setFlash('danger', 'There was a problem creating the route. Reason:' . $response['message']);
                    }
                } elseif ($task == 'edit') {
                    $data = array('route_id' => $entry['id'], 'name' => $entry['route_name'], 'branch_id' => $entry['branch_id']);
                    $response = $route->editRoute($data);
                    $responseHandler = new ResponseHandler($response);

                    if ($responseHandler->getStatus() == ResponseHandler::STATUS_OK) {
                        Yii::$app->session->setFlash('success', 'Route has been edited successfully.');
                    } else {
                        Yii::$app->session->setFlash('danger', 'There was a problem editing the route. Reason:' . $response['message']);
                    }
                }
            }

            return $this->refresh();
        }
        $hubAdp = new BranchAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
        $hubs = $hubAdp->getHubs();
        $hubs = new ResponseHandler($hubs);
        $hub_list = $hubs->getStatus() == ResponseHandler::STATUS_OK ? $hubs->getData() : [];

        $branch_to_view = null;
        $routeAdp = new RouteAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
        $routes = $routeAdp->getRoutes($branch_to_view, $offset, $this->page_width, true,null, $search);
        $routes = new ResponseHandler($routes);

        $route_list = [];
        $total_count = 0;
        if ($routes->getStatus() == ResponseHandler::STATUS_OK) {
            $data = $routes->getData();
            $total_count = empty($data['total_count']) ? 0 : $data['total_count'];
            $route_list = empty($data['routes']) ? 0 : $data['routes'];
        }
        return $this->render('manageroutes', ['routes' => $route_list, 'hubs' => $hub_list, 'offset' => $offset, 'total_count' => $total_count, 'page_width' => $this->page_width]);
    }

    /**
     * Company ECs mapping view
     * @author Adegoke Obasa <goke@cottacush.com>
     */
    public function actionCompanyecs()
    {
        $page = \Yii::$app->getRequest()->get('page', 1);
        $companyAdapter = new CompanyAdapter();
        $branchAdapter = new BranchAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());

        // Add Offset and Count
        $offset = ($page - 1) * $this->page_width;
        $filters['offset'] = $offset;
        $filters['count'] = $this->page_width;

        $companies = $companyAdapter->getAllCompanies([]);
        $ecs = $branchAdapter->getAllEcs();
        $companyEcsResponse = $companyAdapter->getAllEcs($filters);

        $totalCount = Calypso::getValue($companyEcsResponse, 'total_count');
        $companyEcs = Calypso::getValue($companyEcsResponse, 'ecs');

        return $this->render("companyecs", [
            'companyEcs' => $companyEcs,
            'companies' => $companies,
            'ecs' => $ecs,
            'offset' => $offset,
            'total_count' => $totalCount,
            'page_width' => $this->page_width
        ]);
    }

    /**
     * Links an EC to a company
     * @author Adegoke Obasa <goke@cottacush.com>
     */
    public function actionLinkectocompany()
    {
        $companyAdapter = new CompanyAdapter();
        if (Yii::$app->request->isPost) {
            $companyId = Yii::$app->request->post('company_id');
            $branchId = Yii::$app->request->post('branch_id');

            $status = $companyAdapter->linkEc($companyId, $branchId);

            if ($status) {
                $this->flashSuccess("Express Centre linked to company successfully");
            } else {
                $this->flashError($companyAdapter->getLastErrorMessage());
            }
        }
        return $this->redirect("/admin/companyecs");
    }

    /**
     * Relinks an EC to a company
     * @author Adegoke Obasa <goke@cottacush.com>
     */
    public function actionRelinkectocompany()
    {
        $companyAdapter = new CompanyAdapter();
        if (Yii::$app->request->isPost) {
            $id = Yii::$app->request->post('id');
            $companyId = Yii::$app->request->post('company_id');
            $branchId = Yii::$app->request->post('branch_id');

            $status = $companyAdapter->relinkEc($id, $companyId, $branchId);

            if ($status) {
                $this->flashSuccess("Express Centre re-linked to company successfully");
            } else {
                $this->flashError($companyAdapter->getLastErrorMessage());
            }
        }
        return $this->redirect("/admin/companyecs");
    }

    /**
     * Manage Cities View
     * @author Adegoke Obasa <goke@cottacush.com>
     */
    public function actionManagecities()
    {
        $refAdp = new RefAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
        $states = new ResponseHandler($refAdp->getStates(ServiceConstant::DEFAULT_COUNTRY)); // Hardcoded Nigeria for now
        $states_list = $states->getStatus() == ResponseHandler::STATUS_OK ? $states->getData() : [];

        $zoneAdapter = new RegionAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
        $cities = new ResponseHandler($zoneAdapter->getAllCity(1, 1));
        $cities = $cities->getStatus() == ResponseHandler::STATUS_OK ? $cities->getData() : [];


        $hubAdp = new BranchAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
        $hubs = new ResponseHandler($hubAdp->getAllHubs(false));
        $hub_list = $hubs->getStatus() == ResponseHandler::STATUS_OK ? $hubs->getData() : [];

        return $this->render('managecities', array('cities' => $cities, 'states' => $states_list, 'hubs' => $hub_list));
    }

    /**
     * Edit City
     * @author Adegoke Obasa <goke@cottacush.com>
     */
    public function actionEditcity()
    {
        if (Yii::$app->request->isPost) {
            $entry = Yii::$app->request->post();
            $error = [];
            $data = [];

            $data['name'] = Calypso::getValue($entry, 'city_name', null);
            $data['state_id'] = Calypso::getValue($entry, 'state');
            $data['transit_time'] = Calypso::getValue($entry, 'transit_time');
            $data['status'] = Calypso::getValue($entry, 'status');
            $data['branch_id'] = Calypso::getValue($entry, 'branch_id');
            $data['city_id'] = Calypso::getValue($entry, 'id', null);

            if (empty($data['name']) || !isset($data['transit_time'])) {
                $error[] = "All details are required!";
            }
            if (!empty($error)) {
                $errorMessages = implode('<br />', $error);
                Yii::$app->session->setFlash('danger', $errorMessages);
            } else {
                $city = new RegionAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
                $response = new ResponseHandler($city->editCity($data));
                if ($response->isSuccess()) {
                    Yii::$app->session->setFlash('success', 'City has been edited successfully.');
                } else {
                    Yii::$app->session->setFlash('danger', 'There was a problem editing the city. ' .$response->getData());
                }
            }
        }
        return $this->redirect(Url::to("/admin/managecities"));
    }

    /**
     * Save City
     * @author Adegoke Obasa <goke@cottacush.com>
     */
    public function actionSavecity()
    {
        if (Yii::$app->request->isPost) {
            $entry = Yii::$app->request->post();
            $error = [];
            $data = [];
            $data['name'] = Calypso::getValue($entry, 'city_name', null);
            $data['state_id'] = Calypso::getValue($entry, 'state');
            $data['transit_time'] = Calypso::getValue($entry, 'transit_time');
            $data['status'] = Calypso::getValue($entry, 'status');
            $data['branch_id'] = Calypso::getValue($entry, 'branch_id');
            $data['city_id'] = Calypso::getValue($entry, 'id', null);

            if (empty($data['name']) || !isset($data['transit_time'])) {
                $error[] = "All details are required!";
            }
            if (!empty($error)) {
                $errorMessages = implode('<br />', $error);
                Yii::$app->session->setFlash('danger', $errorMessages);
            } else {
                $city = new RegionAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
                $response = $city->addCity($data);
                if ($response['status'] === Response::STATUS_OK) {
                    $this->flashSuccess('City has been created successfully.');
                } else {
                    $this->flashError('There was a problem editing the city. ' . $response['messsage']);
                }
            }
        }
        return $this->redirect(Url::to("/admin/managecities"));
    }

    /**
     * Audit Trail
     * @author Olajide Oye <jide@cottacush.com>
     */
    public function actionAudittrail($page = 1, $page_width = null)
    {
        if ($page_width != null) {
            $this->page_width = $page_width;
            Calypso::getInstance()->cookie('page_width', $page_width);
        }

        $page_width = is_null($page_width) ? $this->page_width : $page_width;
        $offset = ($page - 1) * $page_width;

        $filter = ['offset' => $offset, 'count' => $page_width, 'paginate' => true, 'username' => isset(Calypso::getInstance()->get()->username)?Calypso::getInstance()->get()->username:'', 'service' => isset(Calypso::getInstance()->get()->service)?Calypso::getInstance()->get()->service:'', 'action' => isset(Calypso::getInstance()->get()->action)?Calypso::getInstance()->get()->action:'', 'page_width' => $page_width];

        $filter = array_merge($filter, Yii::$app->request->post());

        $from_date = date('Y-m-d') . ' 00:00:00';
        $to_date = date('Y-m-d') . ' 23:59:59';
        if (isset(Calypso::getInstance()->get()->from, Calypso::getInstance()->get()->to)) {
            $from_date = Calypso::getInstance()->get()->from . ' 00:00:00';
            $to_date = Calypso::getInstance()->get()->to . ' 23:59:59';
        }

        $filter['start_time'] = $from_date;
        $filter['end_time'] = $to_date;

        $regionAdapter = new AuditAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
        $result = (new ResponseHandler($regionAdapter->getAllAudit($filter)))->getData();
        return $this->render('audit_trail', ['logs' => $result['audit_trails'], 'total_count' => $result['total_count'], 'filter' => $filter]);
    }

    public function actionActivation() {

        $payload = json_decode(Yii::$app->request->getRawBody());
        $companyId = Calypso::getValue($payload, 'company_id');
        $status = Calypso::getValue($payload, 'status');
        $status = $status == ServiceConstant::ACTIVE ? ServiceConstant::INACTIVE : ServiceConstant::ACTIVE;

        if (is_null($companyId) || is_null($status)) {
            $this->sendErrorResponse(ResponseMessages::INVALID_PARAMETERS, ResponseCodes::INVALID_PARAMETERS, null, 400);
        }

        $companyAdapter = new CompanyAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
        $response = $companyAdapter->changeStatus(['company_id' => $companyId, 'status' => $status]);
        if(!is_null($response)) {
            return $this->sendSuccessResponse($status);
        } else {
            return $this->sendErrorResponse($companyAdapter->getLastErrorMessage(), 200);
        }
    }

    private function decorateWithStatus(&$companies)
    {
        $length = count($companies);
        for($i = 0; $i < $length; $i++) {
            $status = Calypso::getValue($companies[$i], 'status');
            $companies[$i]['status_details'] = self::getStatusDetails($status);
        }
    }

    private static function getStatusDetails($statusId)
    {
        if($statusId == ServiceConstant::ACTIVE) {

            return [
                'label' => 'Active',
                'action' => 'Deactivate',
                'class' => 'success',
                'icon' => 'lock',
            ];

        } else {

            return [
                'label' => 'Inactive',
                'action' => 'Activate',
                'class' => 'danger',
                'icon' => 'unlock',
            ];
        }
    }

    /**
     * @return string
     * Function to render notification settings page for each status along the parcel
     * tracking process.
     */
    public function actionNotification(){
        $data=Yii::$app->request->post();
        $notify = new AdminAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
        $statuses = $notify->getStatus($data);
        $statuses = new ResponseHandler($statuses);
        $statuses = $statuses->getStatus() == ResponseHandler::STATUS_OK ? $statuses->getData() : [];

        return $this->render('notification',['statuses'=>$statuses]);

    }
}