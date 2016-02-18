<?php
namespace app\controllers;

use Adapter\CompanyAdapter;
use Adapter\CreditNoteAdapter;
use Adapter\InvoiceAdapter;
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
use yii\helpers\Url;

class FinanceController extends BaseController
{

    public function beforeAction($action)
    {
        $this->enableCsrfValidation = false;
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
     * @return string
     */
    public function actionCorporateshipment($page = 1)
    {
        $offset = ($page - 1) * $this->page_width;

        $fromDate = Yii::$app->request->get('from', Util::getToday('/'));
        $toDate = Yii::$app->request->get('to', Util::getToday('/'));
        $filters['start_created_date'] = $fromDate . ' 00:00:00';
        $filters['end_created_date'] = $toDate . ' 23:59:59';
        $filters['company_id'] = Yii::$app->request->get('company');
        $filters['status'] = Yii::$app->request->get('status');
        $filters['remove_cancelled_shipments'] = 1;
        $filters['with_bank_account'] = true;

        $parcelAdapter = new ParcelAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
        $corporateParcelsResponse = $parcelAdapter->getCorporateParcels($offset, $this->page_width, $filters);
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
            'page_width' => $this->page_width
        ]);
    }

    /**
     * Creates an invoice
     * @author Adegoke Obasa <goke@cottacush.com>
     */
    public function actionCreateinvoice()
    {
        if (Yii::$app->request->isPost) {
            $data = Yii::$app->getRequest()->post();

            $parcels = [];
            for ($i = 0; $i < count(Calypso::getValue($data, 'waybill_number', [])); $i++) {
                $parcel = [];
                $parcel['waybill_number'] = Calypso::getValue($data, "waybill_number.$i");
                $parcel['discount'] = floatval(((int)Calypso::getValue($data, "discount.$i")) / 100);
                $parcel['net_amount'] = Calypso::getValue($data, "net_amount.$i");

                $parcels[] = $parcel;
            }

            $data['parcels'] = $parcels;

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

        $fromDate = Yii::$app->request->get('from', Util::getToday('/'));
        $toDate = Yii::$app->request->get('to', Util::getToday('/'));
        $filters['from_created_at'] = $fromDate;
        $filters['to_created_at'] = $toDate;
        $filters['company_id'] = Yii::$app->request->get('company');
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
        if (is_null($invoice_number)) {
            return $this->redirect(Url::to("/finance/invoice"));
        }

        $invoiceAdapter = new InvoiceAdapter();
        $filters = ['invoice_number' => $invoice_number];
        $invoice = $invoiceAdapter->getInvoice($filters);
        $invoiceParcels = $invoiceAdapter->getInvoiceParcels(array_merge($filters, [
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

        $this->layout = 'print';
        return $this->render('print_invoice', ['invoice' => $invoice, 'invoiceParcels' => $invoiceParcels]);
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
        return $this->render('print_credit_note',['company_name' => $company_name, 'credit_note_details' => $creditNoteDetails, 'credit_note_parcels' =>  $creditNoteParcels]);
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
        return $this->renderPartial('partial_credit_note_parcels', ['credit_note_parcels' => $creditNoteParcels,'company_name' => $companyName ,'credit_note_no' => $creditNoteNo]);
    }
}
