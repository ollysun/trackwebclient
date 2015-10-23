<?php
/**
 * Created by PhpStorm.
 * User: RotelandO
 * Date: 7/25/15
 * Time: 1:02 PM
 */

namespace app\controllers;

use Adapter\BranchAdapter;
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

    public function actionManagebranches()
    {
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
                        Yii::$app->response->redirect("/admin/hubmapping?hub={$response['data']['id']}");
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
        $states = $refAdp->getStates(1); // Hardcoded Nigeria for now
        $states = new ResponseHandler($states);

        $filter_state_id = Calypso::getValue(Yii::$app->request->post(), 'filter_state_id', null);
        $hubAdp = new BranchAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
        $hubs = $hubAdp->getHubs($filter_state_id);
        $hubs = new ResponseHandler($hubs);

        $state_list = $states->getStatus() == ResponseHandler::STATUS_OK ? $states->getData() : [];
        $hub_list = $hubs->getStatus() == ResponseHandler::STATUS_OK ? $hubs->getData() : [];
        return $this->render('managehubs', array('States' => $state_list, 'filter_state_id' => $filter_state_id, 'hubs' => $hub_list));
    }

    public function actionManageecs()
    {
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
        $states = $refAdp->getStates(1); // Hardcoded Nigeria for now
        $states = new ResponseHandler($states);

        $hubAdp = new BranchAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
        $hubs = $hubAdp->getHubs();
        $hubs = new ResponseHandler($hubs);

        $filter_hub_id = Calypso::getValue(Yii::$app->request->post(), 'filter_hub_id', null);
        $hubAdp = new BranchAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
        $centres = $hubAdp->getCentres($filter_hub_id);
        $centres = new ResponseHandler($centres);

        $state_list = $states->getStatus() == ResponseHandler::STATUS_OK ? $states->getData() : [];
        $hub_list = $hubs->getStatus() == ResponseHandler::STATUS_OK ? $hubs->getData() : [];
        $centres_list = $centres->getStatus() == ResponseHandler::STATUS_OK ? $centres->getData() : [];
        return $this->render('manageecs', array('States' => $state_list, 'hubs' => $hub_list, 'centres' => $centres_list, 'filter_hub_id' => $filter_hub_id));
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
        $companyAdapter = new CompanyAdapter();
        if (Yii::$app->request->isPost) {
            $data = Yii::$app->request->post();

            // Create Company
            $status = $companyAdapter->createCompany($data);

            if ($status) {
                $this->flashSuccess("Company created successfully");
            } else {
                $this->flashError($companyAdapter->getLastErrorMessage());
            }
            return $this->refresh();
        }

        $refAdapter = new RefAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
        $states = (new ResponseHandler($refAdapter->getStates(1)))->getData();

        $filters = [];

        $page = \Yii::$app->getRequest()->get('page', 1);

        $query = \Yii::$app->getRequest()->get('search');
        if(!is_null($query)) {
            $filters = ['name' => $query];
            $page = 1; // Reset page
        }

        // Add Offset and Count
        $offset = ($page - 1) * $this->page_width;
        $filters['offset'] = $offset;
        $filters['count'] = $this->page_width;

        $companiesData = $companyAdapter->getCompanies($filters);
        $companies = Calypso::getValue($companiesData, 'companies', []);
        $totalCount = Calypso::getValue($companiesData, 'total_count', 0);

        return $this->render('companies', [
            'locations' => ['states' => $states],
            'companies' => $companies,
            'offset' => $offset,
            'total_count' => $totalCount,
            'page_width' => $this->page_width]);
    }

    /**
     * View Company Action
     * @author Adegoke Obasa <goke@cottacush.com>
     */
    public function actionViewcompany()
    {
        $company_id = Yii::$app->request->get('id');

        $company = (new CompanyAdapter())->getCompany($company_id);
        return $this->render('viewcompany', ['company' => $company]);
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
        $cities = (new ResponseHandler($regionAdapter->getAllCity(1, 0, $stateId, 0, 0)))->getData();

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

    public function actionManageroutes($page=1)
    {
        $offset = ($page - 1) * $this->page_width;

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
        $routes = $routeAdp->getRoutes($branch_to_view, $offset, $this->page_width, true);
        $routes = new ResponseHandler($routes);

        $route_list = [];
        $total_count = 0;
        if ($routes->getStatus() == ResponseHandler::STATUS_OK) {
            $data = $routes->getData();
            $total_count = empty($data['total_count']) ? 0 : $data['total_count'];
            $route_list = empty($data['routes']) ? 0 : $data['routes'];
        }
        return $this->render('manageroutes', ['routes' => $route_list, 'hubs' => $hub_list, 'offset'=>$offset, 'total_count'=>$total_count, 'page_width'=>$this->page_width]);
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
        if(Yii::$app->request->isPost) {
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
        if(Yii::$app->request->isPost) {
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
}