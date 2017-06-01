<?php
namespace app\controllers;

use Adapter\BillingPlanAdapter;
use Adapter\BranchAdapter;
use Adapter\CodTellerAdapter;
use Adapter\CompanyAdapter;
use Adapter\CreditNoteAdapter;
use Adapter\InvoiceAdapter;
use Adapter\RefAdapter;
use Adapter\TellerAdapter;
use Adapter\Util\Util;
use Yii;
use Adapter\AdminAdapter;
use Adapter\Globals\ServiceConstant;
use Adapter\ParcelAdapter;
use Adapter\UserAdapter;
use Adapter\RequestHelper;
use Adapter\ResponseHandler;
use Adapter\Util\Calypso;
use app\services\HubService;
use yii\data\Pagination;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

class FinanceController extends BaseController
{
    public $userData = null;

    public function beforeAction($action)
    {
        $this->enableCsrfValidation = false;
        $this->userData = (Calypso::getInstance()->session('user_session'));
        return parent::beforeAction($action);
    }

    public function actionIndex()
    {
        //redirect to the appropriate 'default' page
        return $this->redirect('finance/customersall');
    }

    public function actionCustomersall($page = 1)
    {
        $from_date = Calypso::getValue(Calypso::getInstance()->get(), 'from', date('Y/m/d'));
        $to_date = Calypso::getValue(Calypso::getInstance()->get(), 'to', date('Y/m/d'));
        $method = Calypso::getValue(Calypso::getInstance()->get(), 'payment_type', null);
        $method = $method === '' ? null : $method;
        $waybillnumber = Calypso::getValue(Calypso::getInstance()->get(), 'waybillnumber', null);

        $page_width = 50;//Calypso::getValue(Calypso::getInstance()->get(),'page_width',40);
        $offset = $page_width * ($page - 1);

        $parcel = new ParcelAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
        $response = $parcel->getParcelsByPayment($waybillnumber, $method, $from_date . '%2000:00:00', $to_date . '%2023:59:59', $offset, $page_width, 1, null, 1);
        $response = new ResponseHandler($response);
        $data = [];
        $total_count = 0;
        if ($response->getStatus() == ResponseHandler::STATUS_OK) {
            $data = $response->getData();
            $total_count = $data['total_count'];
            $data = $data['parcels'];
        }
        return $this->render('customers_all', array('search' => $waybillnumber, 'parcels' => $data, 'payment_type' => $method, 'from_date' => $from_date, 'to_date' => $to_date, 'offset' => $offset, 'page_width' => $page_width, 'total_count' => $total_count));
    }

    /**
     * List Corporate Shipments Action
     * @author Adegoke Obasa <goke@cottacush.com>
     * @author Bolade Oye <bolade@cottacush.com>
     * @param int $page
     * @param int $page_width
     * @return string
     */
    public function actionCorporateshipment($page = 1, $page_width = null)
    {
        $page_width = is_null($page_width) ? $this->page_width : $page_width;
        $offset = ($page - 1) * $page_width;

        $fromDate = Yii::$app->request->get('from', Util::getToday('/'));
        $toDate = Yii::$app->request->get('to', Util::getToday('/'));
        $filters['start_created_date'] = $fromDate . ' 00:00:00';
        $filters['end_created_date'] = $toDate . ' 23:59:59';
        $filters['company_id'] = Yii::$app->request->get('company');
        $filters['status'] = Yii::$app->request->get('status');
        $filters['remove_cancelled_shipments'] = 1;
        $filters['with_bank_account'] = true;

        $parcelAdapter = new ParcelAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
        $corporateParcelsResponse = $parcelAdapter->getCorporateParcels($offset, $page_width, $filters);
        $corporateParcels = Calypso::getValue($corporateParcelsResponse, 'parcels');
        $totalCount = Calypso::getValue($corporateParcelsResponse, 'total_count');

        $companies = (new CompanyAdapter())->getAllCompanies([]);
        $statuses = ServiceConstant::getStatusRef();

        return $this->render('corporateshipment', [
            'fromDate' => $fromDate,
            'toDate' => $toDate,
            'statuses' => $statuses,
            'companies' => $companies,
            'corporateParcels' => $corporateParcels,
            'offset' => $offset,
            'total_count' => $totalCount,
            'selectedCompany' => $filters['company_id'],
            'selectedStatus' => $filters['status'],
            'page_width' => $page_width
        ]);
    }

    /**
     * Creates an invoice
     * @author Adegoke Obasa <goke@cottacush.com>
     */
    public function actionCreateinvoice()
    {
        if (Yii::$app->request->isPost) {
            $data = Yii::$app->getRequest()->post('data');

            $invoiceAdapter = new InvoiceAdapter();
            $response = $invoiceAdapter->createInvoice($data);

            if ($response) {
                $this->flashSuccess('Invoice created successfully');
            } else {
                $this->flashError($invoiceAdapter->getLastErrorMessage());
                return $this->redirect(Yii::$app->request->referrer);
            }
        }

        return $this->redirect('/finance/invoice');
    }

    public function actionRecreateinvoice(){
        $invoice_number = Yii::$app->request->get('invoice_number');
        $adapter = new InvoiceAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
        $success = $adapter->recreateInvoice($invoice_number);
        if(!$success){
            $this->flashError($adapter->getLastErrorMessage());
        }else{
            $this->flashSuccess('Invoice regenerated');
        }
        return $this->back();
    }

    public function actionCreatebulkinvoice()
    {
        if (Yii::$app->request->isPost) {
            $data = Yii::$app->getRequest()->post();

            $invoiceAdapter = new InvoiceAdapter();
            $response = $invoiceAdapter->createBulkInvoice($data);
            $jsonResponse = ['status'=>'error', 'message' => 'Unable to reach Trackplus service. Please contact support if this persists.'];
            if ($response) {
               $this->flashSuccess('Bulk Invoice has been queued successfully');
            } else {
                $jsonResponse['status'] = 'error';
                $jsonResponse['message'] = $invoiceAdapter->getLastErrorMessage();
                exit(json_encode($jsonResponse));
            }

        }

        return $this->redirect('/finance/bulkinvoicetasks');
    }

    /**
     * Generates a credit note
     * @author Adegoke Obasa <goke@cottacush.com>
     */
    public function actionGeneratecreditnote()
    {
        if (Yii::$app->request->isPost) {
            $data = Yii::$app->getRequest()->post();

            $parcels = [];
            for ($i = 0; $i < count(Calypso::getValue($data, 'invoice_parcel', [])); $i++) {
                $parcel = [];
                $parcel['invoice_parcel_id'] = Calypso::getValue($data, "invoice_parcel.$i");
                $parcel['deducted_amount'] = floatval(((int)Calypso::getValue($data, "deducted_amount.$i")));
                $parcel['new_net_amount'] = floatval(Calypso::getValue($data, "new_net_amount.$i"));

                $parcels[] = $parcel;
            }

            $data['parcels'] = $parcels;

            $invoiceAdapter = new CreditNoteAdapter();
            $response = $invoiceAdapter->generateCreditNote($data);

            if ($response) {
                $this->flashSuccess('Credit Note created successfully');
            } else {
                $this->flashError($invoiceAdapter->getLastErrorMessage());
                return $this->redirect(Yii::$app->request->referrer);
            }
        }

        return $this->redirect('/finance/creditnote');
    }

    /**
     * Credit Notes Page
     * @author Adegoke Obasa <goke@cottacush.com>
     * @param int $page
     * @return string
     */
    public function actionCreditnote($page = 1)
    {
        $offset = ($page - 1) * $this->page_width;

        $fromDate = Yii::$app->request->get('from', Util::getToday('/'));
        $toDate = Yii::$app->request->get('to', Util::getToday('/'));
        $filters['from_created_at'] = $fromDate;
        $filters['to_created_at'] = $toDate;
        $filters['company_id'] = Yii::$app->request->get('company');
        $filters['status'] = Yii::$app->request->get('status');
        $filters['offset'] = $offset;
        $filters['count'] = $this->page_width;

        $creditNoteAdapter = new CreditNoteAdapter();
        $creditNotesResponse = $creditNoteAdapter->getCreditNotes($filters);
        $creditNotes = Calypso::getValue($creditNotesResponse, 'credit_notes');
        $totalCount = Calypso::getValue($creditNotesResponse, 'total_count');

        $companies = (new CompanyAdapter())->getAllCompanies([]);
        $statuses = ServiceConstant::getStatusRef();

        return $this->render('creditnote', [
            'fromDate' => $fromDate,
            'toDate' => $toDate,
            'statuses' => $statuses,
            'companies' => $companies,
            'creditNotes' => $creditNotes,
            'offset' => $offset,
            'total_count' => $totalCount,
            'selectedCompany' => $filters['company_id'],
            'selectedStatus' => $filters['status'],
            'page_width' => $this->page_width
        ]);
    }

    /**
     * @author Adegoke Obasa <goke@cottacush.com>
     * @param int $page
     * @return string
     */
    public function actionInvoice($page = 1)
    {
        $offset = ($page - 1) * $this->page_width;
		
		$fromDate = Yii::$app->request->get('from', Util::getFirstOfThisMonth('/'));
        $toDate = Yii::$app->request->get('to', Util::getToday('/'));
        $filters['from_created_at'] =$fromDate;
        $filters['to_created_at'] = $toDate;
        $filters['company_id'] = Yii::$app->request->get('company');
        if($filters['company_id'] == 296){
            $fromDate = "2017/02/01";
        }
        $filters['status'] = Yii::$app->request->get('status');
        $filters['offset'] = $offset;
        $filters['count'] = $this->page_width;


        $invoiceAdapter = new InvoiceAdapter();
        $invoicesResponse = $invoiceAdapter->getInvoices($filters);
        $invoices = Calypso::getValue($invoicesResponse, 'invoices');
        $totalCount = Calypso::getValue($invoicesResponse, 'total_count');

        $companies = (new CompanyAdapter())->getAllCompanies([]);
        $statuses = ServiceConstant::getStatusRef();

        return $this->render('invoice', [
            'fromDate' => $fromDate,
            'toDate' => $toDate,
            'statuses' => $statuses,
            'companies' => $companies,
            'invoices' => $invoices,
            'offset' => $offset,
            'total_count' => $totalCount,
            'selectedCompany' => $filters['company_id'],
            'selectedStatus' => $filters['status'],
            'page_width' => $this->page_width
        ]);
    }

    /**
     * View All Bulk Invoice Tasks
     * @author Adegoke Obasa <goke@cottacush.com>
     * @return string
     */
    public function actionBulkinvoicetasks()
    {
        $invoiceAdapter = new InvoiceAdapter();
        $tasks = $invoiceAdapter->getBulkInvoiceTasks();
        return $this->render('bulk_invoice_tasks', ['tasks' => $tasks]);
    }

    /**
     * View Details of Bulk Invoice Task
     * @author Adegoke Obasa <goke@cottacush.com>
     */
    public function actionViewbulkinvoice()
    {
        $invoiceAdapter = new InvoiceAdapter();
        $taskId = Yii::$app->getRequest()->get('task_id', false);
        $task = $invoiceAdapter->getBulkInvoiceTask($taskId);
        return $this->render('bulk_invoice_task_details', ['task_id' => $taskId, 'task' => $task]);
    }


    public function actionMerchantspending()
    {
        return $this->render('merchants_pending');
    }

    public function actionMerchantsdue($page = 1)
    {
        $waybillnumber = Calypso::getValue(Calypso::getInstance()->get(), 'waybillnumber', null);

        $page_width = $this->page_width;
        $offset = $page_width * ($page - 1);

        $parcel = new ParcelAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
        $response = $parcel->getMerchantParcels(1, null, $offset, $page_width);
        $response = new ResponseHandler($response);
        $data = [];
        if ($response->getStatus() == ResponseHandler::STATUS_OK) {
            $data = $response->getData();
        }
        $total_count = empty($data['total_count']) ? 0 : $data['total_count'];
        return $this->render('merchants_due', array('parcels' => $data, 'offset' => $offset, 'total_count' => $total_count));
    }

    public function actionMerchantspaid()
    {
        return $this->render('merchants_paid');
    }

    /**
     * Print Invoice page
     * @author Olajide Oye <jide@cottacush.com>
     * @param null $invoice_number
     * @return string|\yii\web\Response
     */
    public function actionPrintinvoice($invoice_number = null)
    {
        if (empty($invoice_number)) {
            return $this->redirect(Url::to("/finance/invoice"));
        }

        $invoiceAdapter = new InvoiceAdapter();
        $filters = ['invoice_number' => $invoice_number];
        $invoice = $invoiceAdapter->getInvoice($filters);
        $invoiceParcels = $invoiceAdapter->getInvoiceParcels(array_merge($filters, [
            'with_delivery_receipt' => 1,
            'with_receiver_address' => 1,
            'with_receiver' => 1,
            'with_receiver_city' => 1,
            'with_sender_address' => 1,
            'with_sender_city' => 1
        ]));

        $totalWeight = 0;
        $totalPieces = 0;
        $base = 0;
        $discount = 0;
        foreach ($invoiceParcels as $invoiceParcel) {
            $totalWeight += (float)Calypso::getValue($invoiceParcel, 'parcel.weight');
            $totalPieces += (int)Calypso::getValue($invoiceParcel, 'parcel.no_of_package');
            $base += (float)Calypso::getValue($invoiceParcel, 'net_amount');
            $discount += floatval(Calypso::getValue($invoiceParcel, 'parcel.amount_due')) - floatval(Calypso::getValue($invoiceParcel, 'net_amount'));
        }

        $totalExcludingVat = $base - $discount;
        $newTotalNet = Calypso::getValue($invoice, 'stamp_duty', 0) + $totalExcludingVat;

        $invoice['current_date'] = Util::getCurrentDate();
        $invoice['total_weight'] = $totalWeight;
        $invoice['total_pieces'] = $totalPieces;
        $invoice['base'] = $base;
        $invoice['discount'] = $discount;
        $invoice['total_excluding_vat'] = $totalExcludingVat;
        $invoice['st_standard_vat'] = $totalExcludingVat * (ServiceConstant::DEFAULT_VAT_RATE / 100);
        $invoice['new_total_net'] = $newTotalNet;
        $invoice['total_shipments'] = count($invoiceParcels);
        $invoice['total_to_pay'] = $invoice['st_standard_vat'] + $newTotalNet;
        $invoice['total_to_pay_naira'] = (int)($invoice['st_standard_vat'] + $newTotalNet);
        $koboValue = $invoice['total_to_pay'] - floatval($invoice['total_to_pay_naira']);
        $invoice['total_to_pay_kobo'] = round($koboValue * 100);

        $number_of_sheets = 14;
        $invoice_extras = 6;
        $no_of_pages = $invoiceAdapter->getNumberOfSheets(count($invoiceParcels), $number_of_sheets, $invoice_extras);

        $page_height = $invoiceAdapter->getPageHeight($no_of_pages);
        $template_header_page_height = $invoiceAdapter->getPageHeight(2);

        $this->layout = 'print';
        return $this->render('print_invoice_v2', ['template_header_page_height' => $template_header_page_height, 'page_height' => $page_height, 'parcelPages' => $no_of_pages, 'invoice' => $invoice, 'invoiceParcels' => $invoiceParcels]);
    }

    /**
     * Print Credit Note page
     * @author Olajide Oye <jide@cottacush.com>
     */
    public function actionPrintcreditnote()
    {
        $credit_note_number = Yii::$app->request->get('credit_note_no');
        $company_name = Yii::$app->request->get('company_name');
        $creditNoteAdapter = new CreditNoteAdapter();
        $rawPrintOutDetails = $creditNoteAdapter->getPrintOutDetails($credit_note_number);
        $printOutDetails = (new ResponseHandler($rawPrintOutDetails))->getData();
        $creditNoteDetails = $printOutDetails['credit_note'];
        $creditNoteParcels = $printOutDetails['credit_note_parcels'];
        $this->layout = 'print';
        return $this->render('print_credit_note', ['company_name' => $company_name, 'credit_note_details' => $creditNoteDetails, 'credit_note_parcels' => $creditNoteParcels]);
    }

    /**
     * Get's the parcels attached to an invoice
     * @author Adegoke Obasa <goke@cottacush.com>
     * @param null $invoice_number
     * @return array|\yii\web\Response
     */
    public function actionGetinvoiceparcels($invoice_number = null)
    {
        if (is_null($invoice_number)) {
            return $this->sendErrorResponse("Required(s) fields not sent", 400, null, 400);
        }

        if (!Yii::$app->request->isAjax) {
            return $this->redirect(Url::to("/finance/invoice"));
        }

        $invoiceAdapter = new InvoiceAdapter();
        $invoiceParcels = $invoiceAdapter->getInvoiceParcels(['invoice_number' => $invoice_number]);

        return $this->sendSuccessResponse($invoiceParcels);
    }

    /**
     * @author Babatunde Otaru <tunde@cottacush.com>
     * @return string
     */
    public function actionGetcreditnoteparcels()
    {
        $creditNoteNo = Yii::$app->request->get('credit_note_no');
        $companyName = Yii::$app->request->get('company_name');
        $creditNoteAdapter = new CreditNoteAdapter();
        $creditNoteParcelsResources = $creditNoteAdapter->getCreditNoteParcels($creditNoteNo);
        $responseHandler = new ResponseHandler($creditNoteParcelsResources);
        $creditNoteParcels = $responseHandler->getData();
        return $this->renderPartial('partial_credit_note_parcels', ['credit_note_parcels' => $creditNoteParcels, 'company_name' => $companyName, 'credit_note_no' => $creditNoteNo]);
    }


    //tellers
    public function actionSalesteller($page = 1, $page_width = null){
        $page_width = is_null($page_width) ? $this->page_width : $page_width;
        $offset = ($page - 1) * $page_width;


        $adapter = new TellerAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());

        $id = Yii::$app->request->get('id');
        if($id){
            $teller = new ResponseHandler($adapter->getTeller($id));
            if($teller->isSuccess()){
                $data = $teller->getData();
            }else $data = [];

            return $this->render('teller_details', $data);
        }

        $filters = ['offset' => $offset, 'count' => $page_width, 'with_bank' => 1, 'with_payer' => 1, 'with_total_count' => 1];

        $from_date = Yii::$app->request->get('start_created_date', date('Y/m/d'));
        $end_date = Yii::$app->request->get('end_created_date', date('Y/m/d'));
        $filters['start_created_date'] = $from_date . ' 00:00:00';
        $filters['end_created_date'] = $end_date . ' 23:59:59';

        $bank_id = Yii::$app->request->get('bank_id');
        $branch_id = Yii::$app->request->get('branch_id');
        $teller_no = Yii::$app->request->get('teller_no');
        $status = Yii::$app->request->get('status');

        if($bank_id) $filters['bank_id'] = $bank_id;
        if($teller_no) $filters['teller_no'] = $teller_no;
        if($status) $filters['status'] = $status;
        if($branch_id) $filters['branch_id'] = $branch_id;

        $response = new ResponseHandler($adapter->getTellers($filters));

        $branch_adapter = new BranchAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
        $branches = Calypso::getValue($branch_adapter->getAll(), 'data', []);
        if (!$branches) {
            $branches = [];
        }

        $viewData = [
            'offset' => $offset,
            'page_width' => $page_width,
            'start_created_date' => $from_date,
            'end_created_date' => $end_date,
            'bank_id' => $bank_id,
            'teller_no' => $teller_no,
            'status' => $status, 'branches' => $branches, 'branch_id' => $branch_id,
            'statuses' => [ServiceConstant::TELLER_AWAITING_APPROVAL, ServiceConstant::TELLER_APPROVED, ServiceConstant::TELLER_DECLINED]
        ];

        if($response->isSuccess()){
            $data = $response->getData();
            $viewData['tellers'] = $data['tellers'];
            $viewData['total_count'] = $data['total_count'];
        }else{
            $viewData['tellers'] = [];
        }
        return $this->render('sales_teller', $viewData);
    }


    public function actionDownloadteller(){

        $adapter = new TellerAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());


        $filters = ['send_all' => 1, 'with_bank' => 1, 'with_payer' => 1, 'with_total_count' => 1];

        $from_date = Yii::$app->request->get('start_created_date', date('Y/m/d'));
        $end_date = Yii::$app->request->get('end_created_date', date('Y/m/d'));
        $filters['start_created_date'] = $from_date . ' 00:00:00';
        $filters['end_created_date'] = $end_date . ' 23:59:59';
        $branch_id = Yii::$app->request->get('branch_id');

        $bank_id = Yii::$app->request->get('bank_id');
        $teller_no = Yii::$app->request->get('teller_no');
        $status = Yii::$app->request->get('status');

        if($bank_id) $filters['bank_id'] = $bank_id;
        if($teller_no) $filters['teller_no'] = $teller_no;
        if($status) $filters['status'] = $status;
        if($branch_id) $filters['branch_id'] = $branch_id;


        $offset = 0;
        $count = 500;

        $filters['count'] = $count;
        $filters['offset'] = $offset;
        //$response = new ResponseHandler($adapter->getTellers($filters));
        //dd($response);

        $name = 'report_' . date(ServiceConstant::DATE_TIME_FORMAT) . '.csv';
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename=' . $name);
        header('Pragma: no-cache');
        header("Expires: 0");
        $stream = fopen("php://output", "w");


        $headers = array('SN',
            'Banks', 'Account No.', 'Teller No.', 'Teller Amount.', 'Teller Date', 'Payer', 'Status');

        /*if(array_key_exists('with_sales_teller', $filters) && $filters['with_sales_teller'] == '1'){
            $headers[] = 'Sales Banks';
            $headers[] = 'Sales Account No.';
            $headers[] = 'Sales Teller No.';
        }
        if(array_key_exists('with_cod_teller', $filters) && $filters['with_cod_teller'] == '1'){
            $headers[] = 'COD Banks';
            $headers[] = 'COD Account No.';
            $headers[] = 'COD Teller No.';
        }*/
        fputcsv($stream, $headers);


        $filters['count'] = $count;
        $total_count = 0;
        $serial_number = 1;
        while (true) {
            $filters['offset'] = $offset;
            $response = new ResponseHandler($adapter->getTellers($filters));
            if ($response->isSuccess()) {
                $data = $response->getData();
                $tellers = $data['tellers'];


                $exportData = [];
                foreach ($tellers as $key => $teller) {
                    $exportData[] = [
                        $serial_number++,

                        strtoupper(Calypso::getValue($teller, 'bank.name')),
                        strtoupper(Calypso::getValue($teller, 'account_no')),
                        strtoupper(Calypso::getValue($teller, 'teller_no')),
                        Calypso::getValue($teller, 'amount_paid'),
                        Util::formatDate(ServiceConstant::DATE_FORMAT, Calypso::getValue($teller, 'created_date')),
                        Calypso::getValue($teller, 'payer.fullname'),
                        ServiceConstant::getStatus(Calypso::getValue($teller, 'status'))
                    ];

                }


                foreach ($exportData as $row) {
                    fputcsv($stream, $row);
                }

                $total_count += count($tellers);
                if ($total_count >= $data['total_count'] || count($tellers) == 0) {
                    break;
                }
                $offset += $count;
            } else {
                $this->flashError('An error occurred while trying to download report: Reason: ' . $response->getError());
                return $this->redirect(Yii::$app->getRequest()->getReferrer());
            }
        }

        fclose($stream);
        exit;

    }

    public function actionApprovesalesteller($id){
        $adapter = new TellerAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
        $response = new ResponseHandler($adapter->approveTeller($id));
        if($response->isSuccess()){
            $this->flashSuccess('Teller Approved');
        }else{
            $this->flashError($response->getError());
        }
        return $this->back();
    }


    public function actionDelinesalesteller($id){
        $adapter = new TellerAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
        $response = new ResponseHandler($adapter->declineTeller($id));
        if($response->isSuccess()){
            $this->flashSuccess('Teller Declined');
        }else{
            $this->flashError($response->getError());
        }
        return $this->back();
    }


    //cod tellers
    public function actionCodteller($page = 1, $page_width = null){

        $page_width = is_null($page_width) ? $this->page_width : $page_width;
        $offset = ($page - 1) * $page_width;

        $adapter = new CodTellerAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());

        $id = Yii::$app->request->get('id');
        if($id){
            $teller = new ResponseHandler($adapter->getTeller($id));
            if($teller->isSuccess()){
                $data = $teller->getData();
            }else $data = [];


            return $this->render('cod_teller_detail', $data);
        }

        $filters = ['offset' => $offset, 'count' => $page_width, 'with_bank' => 1, 'with_payer' => 1, 'with_total_count' => 1];

        $from_date = Yii::$app->request->get('start_created_date', date('Y/m/d'));
        $end_date = Yii::$app->request->get('end_created_date', date('Y/m/d'));
        $filters['start_created_date'] = $from_date . ' 00:00:00';
        $filters['end_created_date'] = $end_date . ' 23:59:59';
        $branch_id = Yii::$app->request->get('branch_id');

        $bank_id = Yii::$app->request->get('bank_id');
        $teller_no = Yii::$app->request->get('teller_no');
        $status = Yii::$app->request->get('status');

        if($bank_id) $filters['bank_id'] = $bank_id;
        if($teller_no) $filters['teller_no'] = $teller_no;
        if($status) $filters['status'] = $status;
        if($branch_id) $filters['branch_id'] = $branch_id;

        $response = new ResponseHandler($adapter->getTellers($filters));

        $branch_adapter = new BranchAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
        $branches = Calypso::getValue($branch_adapter->getAll(), 'data', []);
        if (!$branches) {
            $branches = [];
        }

        $viewData = [
            'offset' => $offset,
            'page_width' => $page_width,
            'start_created_date' => $from_date,
            'end_created_date' => $end_date,
            'bank_id' => $bank_id,
            'teller_no' => $teller_no,
            'status' => $status,
            'branches' => $branches, 'branch_id' => $branch_id,
            'statuses' => [ServiceConstant::TELLER_AWAITING_APPROVAL, ServiceConstant::TELLER_APPROVED,
                ServiceConstant::TELLER_DECLINED]
        ];

        if($response->isSuccess()){
            $data = $response->getData();
            $viewData['tellers'] = $data['tellers'];
            $viewData['total_count'] = $data['total_count'];
        }else{
            $viewData['tellers'] = [];
        }
        return $this->render('cod_teller', $viewData);
    }

    public function actionDownloadcodteller(){

        $adapter = new CodTellerAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());


        $filters = ['send_all' => 1, 'with_bank' => 1, 'with_payer' => 1, 'with_total_count' => 1];

        $from_date = Yii::$app->request->get('start_created_date', date('Y/m/d'));
        $end_date = Yii::$app->request->get('end_created_date', date('Y/m/d'));
        $filters['start_created_date'] = $from_date . ' 00:00:00';
        $filters['end_created_date'] = $end_date . ' 23:59:59';
        $branch_id = Yii::$app->request->get('branch_id');

        $bank_id = Yii::$app->request->get('bank_id');
        $teller_no = Yii::$app->request->get('teller_no');
        $status = Yii::$app->request->get('status');

        if($bank_id) $filters['bank_id'] = $bank_id;
        if($teller_no) $filters['teller_no'] = $teller_no;
        if($status) $filters['status'] = $status;
        if($branch_id) $filters['branch_id'] = $branch_id;


        $offset = 0;
        $count = 500;

        $filters['count'] = $count;
        $filters['offset'] = $offset;
        //$response = new ResponseHandler($adapter->getTellers($filters));
        //dd($response);

        $name = 'report_' . date(ServiceConstant::DATE_TIME_FORMAT) . '.csv';
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename=' . $name);
        header('Pragma: no-cache');
        header("Expires: 0");
        $stream = fopen("php://output", "w");


        $headers = array('SN',
            'Banks', 'Account No.', 'Teller No.', 'Teller Amount.', 'Teller Date', 'Payer', 'Status');

        /*if(array_key_exists('with_sales_teller', $filters) && $filters['with_sales_teller'] == '1'){
            $headers[] = 'Sales Banks';
            $headers[] = 'Sales Account No.';
            $headers[] = 'Sales Teller No.';
        }
        if(array_key_exists('with_cod_teller', $filters) && $filters['with_cod_teller'] == '1'){
            $headers[] = 'COD Banks';
            $headers[] = 'COD Account No.';
            $headers[] = 'COD Teller No.';
        }*/
        fputcsv($stream, $headers);


        $filters['count'] = $count;
        $total_count = 0;
        $serial_number = 1;
        while (true) {
            $filters['offset'] = $offset;
            $response = new ResponseHandler($adapter->getTellers($filters));
            if ($response->isSuccess()) {
                $data = $response->getData();
                $tellers = $data['tellers'];


                $exportData = [];
                foreach ($tellers as $key => $teller) {
                    $exportData[] = [
                        $serial_number++,

                        strtoupper(Calypso::getValue($teller, 'bank.name')),
                        strtoupper(Calypso::getValue($teller, 'account_no')),
                        strtoupper(Calypso::getValue($teller, 'teller_no')),
                        Calypso::getValue($teller, 'amount_paid'),
                        Util::formatDate(ServiceConstant::DATE_FORMAT, Calypso::getValue($teller, 'created_date')),
                        Calypso::getValue($teller, 'payer.fullname'),
                        ServiceConstant::getStatus(Calypso::getValue($teller, 'status'))
                    ];

                }


                foreach ($exportData as $row) {
                    fputcsv($stream, $row);
                }

                $total_count += count($tellers);
                if ($total_count >= $data['total_count'] || count($tellers) == 0) {
                    break;
                }
                $offset += $count;
            } else {
                $this->flashError('An error occurred while trying to download report: Reason: ' . $response->getError());
                return $this->redirect(Yii::$app->getRequest()->getReferrer());
            }
        }

        fclose($stream);
        exit;

    }

    public function actionApprovecodteller($id){
        $adapter = new CodTellerAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
        $response = new ResponseHandler($adapter->approveTeller($id));
        if($response->isSuccess()){
            $this->flashSuccess('Teller Approved');
        }else{
            $this->flashError($response->getError());
        }
        return $this->back();
    }


    public function actionDelinecodteller($id){
        $adapter = new CodTellerAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
        $response = new ResponseHandler($adapter->declineTeller($id));
        if($response->isSuccess()){
            $this->flashSuccess('Teller Declined');
        }else{
            $this->flashError($response->getError());
        }
        return $this->back();
    }
}
