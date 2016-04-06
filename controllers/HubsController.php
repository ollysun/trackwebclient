<?php
/**
 * Created by PhpStorm.
 * User: RotelandO
 * Date: 7/25/15
 * Time: 8:31 AM
 */

namespace app\controllers;

use Adapter\AdminAdapter;
use Adapter\BranchAdapter;
use Adapter\facades\ParcelDraftSortConfirmFacade;
use Adapter\facades\ParcelDraftSortFacade;
use Adapter\facades\ParcelDraftSortDiscardFacade;
use Adapter\Globals\ServiceConstant;
use Adapter\ParcelAdapter;
use Adapter\RefAdapter;
use Adapter\RequestHelper;
use Adapter\ResponseHandler;
use Adapter\RouteAdapter;
use Adapter\Util\Calypso;
use app\services\HubService;
use Yii;
use yii\base\Exception;

class HubsController extends BaseController
{
    public $userData = null;
    public $branch_to_view = null;

    public function beforeAction($action)
    {
        $this->enableCsrfValidation = false;
        $this->userData = (Calypso::getInstance()->session('user_session'));
        $this->branch_to_view = ($this->userData['role_id'] == ServiceConstant::USER_TYPE_SUPER_ADMIN) ? null :
            ($this->userData['role_id'] == ServiceConstant::USER_TYPE_ADMIN) ? null : $this->userData['branch_id']; //displays all when null
        return parent::beforeAction($action);
    }

    /**
     * This action allows setting next destination for shipments
     * @author Adegoke Obasa <goke@cottacush.com>
     * @param int $page
     * @param null $page_width
     * @param null $type
     * @return string
     */
    public function actionDestination($page = 1, $page_width = null, $type = null)
    {
        $viewData['page_width'] = is_null($page_width) ? $this->page_width : $page_width;
        $viewData['offset'] = ($page - 1) * $viewData['page_width'];
        /**
         * This is to allow an hub officer perform the function of a groundsman
         */
        $allowGroundsManFunctions = !is_null($type) && $type == 'groundsman';
        $isGroundman = $this->userData['role_id'] == ServiceConstant::USER_TYPE_GROUNDSMAN || $allowGroundsManFunctions;

        $parcelsAdapter = new ParcelAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());

        if (\Yii::$app->request->isPost) {
            $branch = \Yii::$app->request->post('branch');
            $branch_type = \Yii::$app->request->post('branch_type');
            $waybill_numbers = \Yii::$app->request->post('waybills');
            $return_to_origin = \Yii::$app->request->post('return_to_origin');

            if (!isset($branch) || empty($waybill_numbers)) {
                $this->flashError('Please ensure you set destinations at least a (one) for the parcels');
            }

            $postParams['waybill_numbers'] = implode(',', $waybill_numbers);
            $postParams['return_to_origin'] = $return_to_origin;

            if ($branch == $this->userData['branch_id']) {
                $response = $parcelsAdapter->assignToGroundsMan($postParams);
            } else {
                if ($branch_type == 'route') {
                    $postParams['route_id'] = $branch;
                    $response = $parcelsAdapter->moveForDelivery($postParams);
                } else {
                    $postParams['to_branch_id'] = $branch;
                    $response = $parcelsAdapter->moveToForSweeper($postParams);
                }
            }

            if ($response['status'] === ResponseHandler::STATUS_OK) {

                if ($branch_type != 'route' && ($this->userData['branch_id'] == $branch)) {
                    $this->flashSuccess('Parcels have been successfully moved to the next destination.');
                } else {
                    $this->flashSuccess('Parcels have been successfully moved to the next destination. <a href="delivery">Generate Manifest</a>');
                }
            } else {
                $this->flashError('An error occurred while trying to move parcels to next destination. Please try again.');
            }
        }
        $parcelsAdapter = new ParcelAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
        $arrival_parcels = $parcelsAdapter->getParcelsForNextDestination($isGroundman ? ServiceConstant::ASSIGNED_TO_GROUNDSMAN : ServiceConstant::FOR_ARRIVAL, null, $isGroundman ? $this->userData['branch_id'] : $this->branch_to_view, null, $viewData['offset'], $viewData['page_width'], 1);


        if ($arrival_parcels['status'] === ResponseHandler::STATUS_OK) {
            $viewData['parcel_next'] = $arrival_parcels['data']['parcels'];
            $viewData['total_count'] = $arrival_parcels['data']['total_count'];
        } else {
            $this->flashError('An error occurred while trying to fetch parcels. Please try again.');
            $viewData['parcel_next'] = [];
        }
        $viewData['isGroundsman'] = $isGroundman;
        $viewData['reasons_list'] = $parcelsAdapter->getParcelReturnReasons();
        return $this->render('destination', $viewData);
    }

    public function actionHubarrival($page = 1, $page_width = null)
    {
        $parcelsAdapter = new ParcelAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());

        if (\Yii::$app->request->isPost) {
            $branch = \Yii::$app->request->post('branch');
            $waybill_numbers = \Yii::$app->request->post('waybills');
            if (!isset($branch) || empty($waybill_numbers)) {
                $this->flashError('Please ensure you set destinations at least a (one) for the parcels');
            }

            $postParams['waybill_numbers'] = implode(',', $waybill_numbers);
            $postParams['to_branch_id'] = $branch;
            $response = $parcelsAdapter->moveToForSweeper($postParams);
            if ($response['status'] === ResponseHandler::STATUS_OK) {
                $this->flashSuccess('Parcels have been successfully moved to the next destination. <a href="hubmovetodelivery">Generate Manifest</a>');
            } else {
                $this->flashError('An error occurred while trying to move parcels to next destination. Please try again.');
            }
        }

        $viewData = [];
        $viewData['page_width'] = is_null($page_width) ? $this->page_width : $page_width;
        $viewData['offset'] = ($page - 1) * $viewData['page_width'];

        $user_session = Calypso::getInstance()->session("user_session");
        $parcelsAdapter = new ParcelAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
        $arrival_parcels = $parcelsAdapter->getParcelsForNextDestination(ServiceConstant::FOR_ARRIVAL, null, $this->branch_to_view, null, $viewData['offset'], $viewData['page_width'], 1);
        if ($arrival_parcels['status'] === ResponseHandler::STATUS_OK) {
            $viewData['parcel_next'] = $arrival_parcels['data']['parcels'];
            $viewData['total_count'] = $arrival_parcels['data']['total_count'];
        } else {
            $this->flashError('An error occurred while trying to fetch parcels. Please try again.');
            $viewData['parcel_next'] = [];
            $viewData['total_count'] = 0;
        }
        return $this->render('hub_arrival', $viewData);
    }

    public function actionHubdispatch()
    {
        $user_session = Calypso::getInstance()->session("user_session");
        $from_branch_id = $user_session['branch_id'];
        $to_branch_id = Calypso::getValue(Yii::$app->request->post(), 'to_branch_id', null);

        $from_date = Calypso::getValue(Yii::$app->request->get(), 'from', date('Y/m/d'));
        $to_date = Calypso::getValue(Yii::$app->request->get(), 'to', date('Y/m/d'));

        $hubAdp = new BranchAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
        $hubs = $hubAdp->getAll();
        $hubs = new ResponseHandler($hubs);
        $hub_list = $hubs->getStatus() == ResponseHandler::STATUS_OK ? $hubs->getData() : [];

        $parcelsAdapter = new ParcelAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
        $dispatch_parcels = $parcelsAdapter->getDispatchedParcels($this->branch_to_view, $to_branch_id, $from_date . ' 00:00:00', $to_date . ' 23:59:59', ServiceConstant::IN_TRANSIT);
        $parcels = new ResponseHandler($dispatch_parcels);
        $reasons_list = $parcelsAdapter->getParcelReturnReasons(); // get all reason
        $parcel_list = $parcels->getStatus() == ResponseHandler::STATUS_OK ? $parcels->getData() : [];

        return $this->render('hub_dispatch', array('reasons_list' => $reasons_list, 'sweeper' => [], 'hubs' => $hub_list, 'parcels' => $parcel_list, 'branch_id' => $to_branch_id, 'from_date' => $from_date, 'to_date' => $to_date));
    }

    /**
     * Ajax calls to get all hubs
     */
    public function actionAllhubs()
    {

        $branchAdapter = new BranchAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
        $allHubs = $branchAdapter->getAllHubs(false);
        if ($allHubs['status'] === ResponseHandler::STATUS_OK) {
            return $this->sendSuccessResponse($allHubs['data']);
        } else {
            return $this->sendErrorResponse($allHubs['message'], null);
        }
    }

    /**
     * Ajax calls to get all routes in the present hub
     */
    public function actionAllroutesforhub()
    {
        $routeAdp = new RouteAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
        $user_session = Calypso::getInstance()->session("user_session");
        $routes = $routeAdp->getRoutes($user_session['branch_id'], null, null, null, true, null);

        if ($routes['status'] === ResponseHandler::STATUS_OK) {
            return $this->sendSuccessResponse($routes['data']);
        } else {
            return $this->sendErrorResponse($routes['message'], null);
        }
    }

    /**
     * Ajax calls to get all ec in the present hub
     */
    public function actionAllecforhubs()
    {

        $branchAdapter = new BranchAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
        $user_session = Calypso::getInstance()->session("user_session");
        $allEcsInHubs = $branchAdapter->listECForHub($user_session['branch_id']);
        if ($allEcsInHubs['status'] === ResponseHandler::STATUS_OK) {
            return $this->sendSuccessResponse($allEcsInHubs['data']);
        } else {
            return $this->sendErrorResponse($allEcsInHubs['message'], null);
        }
    }

    /**
     * Ajax calls to get Branch details
     */
    public function actionBranchdetails()
    {
        $branch_id = \Yii::$app->request->get('id');
        if (!isset($branch_id)) {
            return $this->sendErrorResponse("Invalid parameter(s) sent!", null);
        }
        $refData = new RefAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
        $branch = $refData->getBranchbyId($branch_id);
        if ($branch['status'] === ResponseHandler::STATUS_OK) {
            return $this->sendSuccessResponse($branch['data']);
        } else {
            return $this->sendErrorResponse($branch['message'], null);
        }
    }

    /**
     * Ajax calls to get Branch details
     */
    public function actionStaffdetails()
    {
        $code = \Yii::$app->request->get('code');
        if (!isset($code)) {
            return $this->sendErrorResponse("Invalid parameter(s) sent!", null);
        }
        $adminData = new AdminAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
        $staff = $adminData->getStaff($code);
        if ($staff['status'] === ResponseHandler::STATUS_OK) {
            return $this->sendSuccessResponse($staff['data']);
        } else {
            return $this->sendErrorResponse($staff['message'], null);
        }
    }

    /**
     * Ajax calls to get Branch details
     */
    public function actionGeneratemanifest()
    {

    }

    /**
     * This action moves shipment to in transit.
     * Shipments are also assigned to sweepers for delivery
     * @return string
     */
    public function actionDelivery($page = 1, $page_width = null)
    {
        //Move to In Transit (waybill_numbers, to_branch_id.
        //and staff_id (not the code)

        Calypso::getInstance()->makeAnUnbagReferrer();

        if (\Yii::$app->request->isPost) {
            $rawData = \Yii::$app->request->post('payload');
            $data = json_decode($rawData, true);
            $service = new HubService();
            $payloadData = $service->buildPostData($data);
            if (!isset($payloadData['waybill_numbers'], $payloadData['to_branch_id'], $payloadData['held_by_id'])) {
                $this->flashError("Invalid parameter(s) sent!");
            } else {
                $parcelData = new ParcelAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
                $response = $parcelData->generateManifest($payloadData);
                if ($response['status'] === ResponseHandler::STATUS_OK) {
                    //Forward to manifest page
                    return $this->redirect('/manifest/view?id=' . Calypso::getValue($response, 'data.manifest.id', ''));
                } else {
                    //Flash error message
                    $this->flashError($response['message']);
                }
            }
        }
        $viewData = [];
        $to_branch_id = \Yii::$app->request->get('bid');
        $btype = \Yii::$app->request->get('btype');
        if (!isset($to_branch_id, $btype)) {
            $to_branch_id = null;
        }

        $viewData['to_branch_id'] = $to_branch_id;
        $viewData['btype'] = $btype;
        $viewData['page_width'] = is_null($page_width) ? $this->page_width : $page_width;
        $viewData['offset'] = ($page - 1) * $viewData['page_width'];

        $parcelsAdapter = new ParcelAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
        $for_delivery_parcels = $parcelsAdapter->getParcelsForNextDestination(ServiceConstant::FOR_SWEEPER, $this->branch_to_view, $to_branch_id, null, $viewData['offset'], $viewData['page_width'], 1);
        if ($for_delivery_parcels['status'] === ResponseHandler::STATUS_OK) {
            $viewData['parcel_delivery'] = $for_delivery_parcels['data']['parcels'];
            $viewData['total_count'] = $for_delivery_parcels['data']['total_count'];
        } else {
            $this->flashError('An error occurred while trying to fetch parcels. Please try again.');
            $viewData['parcel_delivery'] = [];
            $viewData['total_count'] = 0;
        }


        return $this->render('delivery', $viewData);
    }

    /**
     * This is a method to render the view for generating manifest
     * @param $data
     * @return string
     */
    public function viewManifest($data)
    {

        $user_session = Calypso::getInstance()->session("user_session");
        $parcelsAdapter = new ParcelAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
        $in_transit_parcels = $parcelsAdapter->getParcelsForNextDestination(ServiceConstant::IN_TRANSIT, $this->branch_to_view, $data['to_branch_id'], $data['held_by_id']);
        if ($in_transit_parcels['status'] === ResponseHandler::STATUS_OK) {
            $viewData['parcel_delivery'] = $in_transit_parcels['data'];

            $adminData = new AdminAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
            $staff = $adminData->getStaff($data['staff_code']);
            if ($staff['status'] === ResponseHandler::STATUS_OK) {
                $viewData['staff'] = $staff['data'];
            } else {
                $viewData['staff'] = [];
            }

        } else {
            $this->flashError('An error occurred while trying to fetch parcels. Please try again.');
            $viewData['parcel_delivery'] = [];
        }

        return $this->render('manifest', $viewData);
    }

    public function actionCreatebag()
    {
        $rawData = \Yii::$app->request->getRawBody();
        $data = json_decode($rawData, true);
        $paramWaybills = [];

        if (!isset($data['waybills'])) {
            return $this->sendErrorResponse("Invalid parameter(s) sent!", null);
        }

        foreach ($data['waybills'] as $wb) {
            $paramWaybills[] = $wb['number'];
        }

        $waybills = implode(",", $paramWaybills);
        $payload = [
            'waybill_numbers' => $waybills,
            'to_branch_id' => Calypso::getValue($data, 'to_branch_id', null),
            'seal_id' => Calypso::getValue($data, 'seal_id', ''),
            'status' => ServiceConstant::FOR_SWEEPER
        ];

        $parcelData = new ParcelAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
        $response = $parcelData->createBag($payload);
        $responseHandler = new ResponseHandler($response);
        $data = $responseHandler->getData();

        if ($responseHandler->getStatus() === ResponseHandler::STATUS_OK) {
            if (empty($data['bad_parcels']))
                return $this->sendSuccessResponse($data);
            else {
                $bad_parcels = $data['bad_parcels'];
                $error_message = 'The following parcels cannot be bagged: ';
                foreach ($bad_parcels as $key => $bad_parcel) {
                    $error_message .= '[' . $key . '#' . $bad_parcel . ']';
                }
                return $this->sendErrorResponse($error_message, null);
            }
        } else {
            return $this->sendErrorResponse($responseHandler->getError(), null);
        }
    }

    public function actionViewbag()
    {
        return $this->render('view_bag');
    }

    /**
     * Unsort parcels
     * @author Adeyemi Olaoye <yemi@cottacush.com>
     */
    public function actionUnsort()
    {
        if (!Yii::$app->request->isPost) {
            return $this->redirect(Yii::$app->request->referrer);
        }

        $waybill_numbers = Yii::$app->request->post('waybill_numbers');
        if (is_null($waybill_numbers)) {
            $this->flashError('No Parcels selected');
            return $this->redirect(Yii::$app->request->referrer);
        }

        $waybill_numbers = explode(',', $waybill_numbers);

        $parcelAdapter = new ParcelAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
        $status = $parcelAdapter->unsort($waybill_numbers);
        if (!$status) {
            $this->flashError($parcelAdapter->getLastErrorMessage());
        } else {
            $responseData = $parcelAdapter->getResponseHandler()->getData();
            if ($failed_parcels = Calypso::getValue($responseData, 'failed', [])) {

                $successful_parcels = Calypso::getValue($responseData, 'successful', []);
                $failed_message = ($successful_parcels) ? ('<strong>Unsorted Parcels: ' . implode(', ', $successful_parcels) . '</strong><br/></br>') : '';
                $failed_message .= '<strong>Failed to unsort some parcels:</strong> <br/>';

                foreach ($failed_parcels as $waybill_number => $message) {
                    $failed_message .= '#<strong>' . $waybill_number . '</strong> - Reason: ' . $message . '<br/>';
                }

                $flash_type = ($successful_parcels) ? 'warning' : 'danger';
                Yii::$app->session->setFlash($flash_type, $failed_message);
            } else {
                $this->flashSuccess('Parcels successfully unsorted');
            }
        }

        return $this->redirect(Yii::$app->request->referrer);
    }


    /**
     * Shows parcels expected in the branch
     * @author Adeyemi Olaoye <yemi@cottacush.com>
     * @param int $page
     * @param null $page_width
     * @return string
     */
    public function actionExpected($page = 1, $page_width = null)
    {
        $page_width = is_null($page_width) ? $this->page_width : $page_width;
        $offset = ($page - 1) * $page_width;
        $parcelsAdapter = new ParcelAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
        $data = $parcelsAdapter->getExpectedParcels($offset, $page_width, $this->branch_to_view);
        $expectedParcels = Calypso::getValue($data, 'parcels', []);
        $total_count = Calypso::getValue($data, 'total_count', 0);
        return $this->render('expected', ['parcels' => $expectedParcels, 'offset' => $offset, 'page_width' => $page_width, 'total_count' => $total_count]);
    }

    /**
     * Draft sort parcels
     * @author Adeyemi Olaoye <yemi@cottacush.com>
     */
    public function actionDraftsortparcels()
    {
        if (!Yii::$app->getRequest()->isPost) {
            return $this->redirect(Yii::$app->request->referrer);
        }

        $parcelDraftSortFacade = new ParcelDraftSortFacade();
        $data = Yii::$app->getRequest()->post();
        try {
            $parcelDraftSortFacade->process($data);
            Yii::$app->session->setFlash($parcelDraftSortFacade->getMessageFlashType(), $parcelDraftSortFacade->getMessage());
        } catch (Exception $ex) {
            $this->flashError($ex->getMessage());
        }

        return $this->redirect(Yii::$app->request->referrer);
    }

    /**
     * discard draft sort parcels
     * @author Adeyemi Olaoye <yemi@cottacush.com>
     * @return \yii\web\Response
     */
    public function actionDiscarddraftsort()
    {
        if (!Yii::$app->getRequest()->isPost) {
            return $this->redirect(Yii::$app->request->referrer);
        }

        $parcelDraftSortDiscardFacade = new ParcelDraftSortDiscardFacade();
        $data = Yii::$app->getRequest()->post();
        try {
            $parcelDraftSortDiscardFacade->process($data);
            Yii::$app->session->setFlash($parcelDraftSortDiscardFacade->getMessageFlashType(), $parcelDraftSortDiscardFacade->getMessage());
        } catch (Exception $ex) {
            $this->flashError($ex->getMessage());
        }

        return $this->redirect(Yii::$app->request->referrer);
    }

    /**
     * confirm draft sort parcels
     * @author Adeyemi Olaoye <yemi@cottacush.com>
     * @return \yii\web\Response
     */
    public function actionConfirmdraftsort()
    {
        if (!Yii::$app->getRequest()->isPost) {
            return $this->redirect(Yii::$app->request->referrer);
        }

        $parcelDraftSortConfirmFacade = new ParcelDraftSortConfirmFacade();
        $data = Yii::$app->getRequest()->post();
        try {
            $parcelDraftSortConfirmFacade->process($data);
            Yii::$app->session->setFlash($parcelDraftSortConfirmFacade->getMessageFlashType(), $parcelDraftSortConfirmFacade->getMessage());
        } catch (Exception $ex) {
            $this->flashError($ex->getMessage());
        }

        return $this->redirect(Yii::$app->request->referrer);
    }

    /**
     * Shows draft sortings
     * @author Adeyemi Olaoye <yemi@cottacush.com>
     * @param int $page
     * @param null $page_width
     * @return string
     */
    public function actionDraftsortings($page = 1, $page_width = null)
    {
        $page_width = is_null($page_width) ? $this->page_width : $page_width;
        $offset = ($page - 1) * $page_width;
        $parcelsAdapter = new ParcelAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
        $data = $parcelsAdapter->getDraftSorts($offset, $page_width, true);
        $draftSorts = Calypso::getValue($data, 'draft_sorts', []);
        $total_count = Calypso::getValue($data, 'total_count', 0);
        return $this->render('draft_sorts', ['draft_sorts' => $draftSorts, 'offset' => $offset, 'page_width' => $page_width, 'total_count' => $total_count]);
    }

    /**
     * Create draft bag
     * @author Adeyemi Olaoye <yemi@cottacush.com>
     */
    public function actionCreatedraftbag()
    {
        if (!Yii::$app->getRequest()->isPost) {
            return $this->redirect(Yii::$app->request->referrer);
        }

        $data = Yii::$app->request->post();
        $parcelsAdapter = new ParcelAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
        $response = $parcelsAdapter->createDraftBag($data);
        if ($response->isSuccess()) {
            $this->flashSuccess('Draft Bag successfully created');
        } else {
            $this->flashError($response->getError());
        }

        return $this->redirect(Yii::$app->request->referrer);
    }

    /**
     * Get draft bag parcels
     * @author Adeyemi Olaoye <yemi@cottacush.com>
     */
    public function actionGetdraftbagparcels()
    {
        if (!Yii::$app->getRequest()->isAjax) {
            return $this->redirect(Yii::$app->request->referrer);
        }

        $bag_number = Yii::$app->getRequest()->get('bag_number');
        $parcelsAdapter = new ParcelAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
        $response = $parcelsAdapter->getDraftBagParcels($bag_number);
        return $this->sendSuccessResponse($response);
    }

    /**
     * Confirm a draft bag
     * @author Adeyemi Olaoye <yemi@cottacush.com>
     */
    public function actionConfirmdraftbag()
    {
        if (!Yii::$app->getRequest()->isPost) {
            return $this->redirect(Yii::$app->request->referrer);
        }

        $data = Yii::$app->request->post();
        $parcelsAdapter = new ParcelAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
        $response = $parcelsAdapter->confirmDraftBag($data);
        if ($response->isSuccess()) {
            $this->flashSuccess('Draft Bag successfully confirmed');
        } else {
            $this->flashError($response->getError());
        }

        return $this->redirect(Yii::$app->request->referrer);
    }
}