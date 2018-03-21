<?php
namespace app\services;
use Adapter\ParcelAdapter;
use Adapter\RefAdapter;
use Adapter\RequestHelper;
use mikehaertl\wkhtmlto\Pdf;
use Picqer\Barcode\BarcodeGeneratorHTML;

/**
 * Created by PhpStorm.
 * User: Kalu
 * Date: 17/05/2017
 * Time: 11:31
 */

class BulkWaybillPrinting
{
    const QTY_METRICS_PIECES = 'pieces';
    const QTY_METRICS_WEIGHT = 'weight';
    public function createPdf(array $waybill_numbers)
    {
        //  $waybill_numbers=['1N22701012624','1N22701012625','2N14301012626'];
        $waybills_html = '';
        foreach ($waybill_numbers as $waybill_number) {
            //print 'Printing ' . $waybill_number . '...' . "\n";
            $waybills_html .= $this->getWaybillHtml($waybill_number);
        }

        echo $waybills_html;
        dd('');

        $pdf = new Pdf([
            'ignoreWarnings' => true,
            'commandOptions' => [
                'useExec' => true
            ]
        ]);

        $waybill_layout = file_get_contents(dirname(__DIR__) . '/html/bulk_waybill_layout.html');
        $html_content = Util::replaceTemplate($waybill_layout, ['content' => $waybills_html]);
        //echo $html_content;
        //return;
        $pdf->addPage($html_content);
        //dd($pdf);
        $result = $pdf->send('printout.pdf', true);
        /* if (!$pdf->saveAs('s3://' . self::S3_BUCKET_BULK_WAYBILLS . '/' . $namespace . '/waybills_task_' . $this->data->bulk_shipment_task_id . '.pdf')) {
             print $pdf->getError() . "\n";
             return false;
         }*/
        if(!$result){
            echo $pdf->getError();
        }
        print 'printing';
        return true;
    }
    private function getWaybillHtml($waybill_number)
    {
        $copies = ["Sender's Copy", "EC Copy"];//["Sender's Copy", "EC Copy", "Ack. Copy", "Recipient's Copy"];
        $waybill_html = '';
        $adapter = new ParcelAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
        $result = $adapter->getParcelByWayBillNumber($waybill_number);
        //$parcel = Parcel::fetchOne($waybill_number, false, 'waybill_number');
        $waybill_template = file_get_contents(dirname(__DIR__) . '/html/waybill_template.html');
        if ($result['status'] != 1) {
            return '';
        }
        $parcel = $result['data'];

        $placeholderValues = $this->getPlaceholderValues($parcel);
        $waybill_template = Util::replaceTemplate($waybill_template, $placeholderValues);
        foreach ($copies as $i => $copy) {
            $waybill_html .= Util::replaceTemplate($waybill_template, ['copy' => $copy]);
            if ($i == 0 || $i == 2) {
                $waybill_html .= '<div class="waybill-divider"></div>';
            }
        }

        return $waybill_html;
    }


    private function getPlaceholderValues($parcel)
    {
        $generator = new BarcodeGeneratorHTML();
        $barCodeData = $generator->getBarcode($parcel['waybill_number'], BarcodeGeneratorHTML::TYPE_CODE_128, 2, 78);

        return [
            'waybill_number' => Util::humanizeWaybillNumber($parcel['waybill_number']),
            'sender_name' => $parcel['sender']['firstname'] . ' ' . $parcel['sender']['lastname'],
            'sender_address' => $parcel['sender_address']['street_address1'] .
                '<br/>' . $parcel['sender_address']['street_address2'] . '<br/>',
            'sender_country' => $parcel['sender_country']['name'],
            'sender_telephone' => $parcel['sender']['phone'],
            'sender_state' => ucwords($parcel['sender_state']['name']),
            'sender_city' => ucwords($parcel['sender_city']['name']),
            'receiver_name' => $parcel['receiver']['firstname'] . ' ' . $parcel['receiver']['lastname'],
            'receiver_address' => $parcel['receiver_address']['street_address1'] .
                '<br/>' . $parcel['receiver_address']['street_address2'] . '<br/>',
            'receiver_country' => $parcel['receiver_country']['name'],
            'receiver_telephone' => $parcel['receiver']['phone'],
            'receiver_state' => ucwords($parcel['receiver_state']['name']),
            'receiver_city' => ucwords($parcel['receiver_city']['name']),
            'shipping_day' => date('d', strtotime($parcel['created_date'])),
            'shipping_month' => date('m', strtotime($parcel['created_date'])),
            'shipping_year' => date('y', strtotime($parcel['created_date'])),
            'reference_number' => (!empty($parcel['reference_number']) ? 'REF:' . $parcel['reference_number'] : ''),
            'sender_city_code' => $parcel['sender_state']['code'],
            'receiver_city_code' => $parcel['receiver_state']['code'],
            'no_of_package' => $parcel['no_of_package'],
            'weight' => ($parcel['qty_metrics'] == self::QTY_METRICS_WEIGHT) ? Util::formatWeight($parcel['weight']) . 'Kg' : '',
            'pieces' => ($parcel['qty_metrics'] == self::QTY_METRICS_PIECES) ? $parcel['weight'] : '',
            'service_types' => $this->getServiceTypeHtml($parcel['shipping_type']),
            'parcel_type' => (is_array($this->getShippingTypes()) && array_key_exists($parcel['parcel_type'], $this->getShippingTypes()))?
                $this->getShippingTypes()[$parcel['parcel_type']]:'Special Project',
            'cod_yes' => (($parcel['cash_on_delivery'] == '1') ? 'is-active' : ''),
            'cod_no' => (($parcel['cash_on_delivery'] == '1') ? '' : 'is-active'),
            'cod_amt' => Util::formatCurrency($parcel['delivery_amount']),
            'other_info' => $parcel['other_info'],
            'barcode_data' => $barCodeData
        ];
    }
    public function getServiceTypeHtml($service_type)
    {
        $serviceTypeHtml = '';
        foreach ($this->getShippingTypes() as $id => $name) {
            $class = ($service_type == $id) ? 'service-type__inner is-active' : 'service-type__inner';
            $serviceTypeHtml .= "<div class='{$class}'><span>" . ucwords($name) . "</span></div>";
        }
        return $serviceTypeHtml;
    }
    public function getShippingTypes()
    {
        if($this->serviceTypes != null) return $this->serviceTypes;
        $refData = new RefAdapter(RequestHelper::getClientID(), RequestHelper::getAccessToken());
        $result= $refData->getShipmentType();
        if($result['status'] == 1)
        {
            //$this->serviceTypes = $refData->getShipmentType()['data'];
            $this->serviceTypes = $this->getDataMap(['id' => 'name'], $refData->getShipmentType()['data']);
        }
        return $this->serviceTypes;
    }

    private function getDataMap($map, $array)
    {
        $dataMap = [];
        foreach ($map as $key => $value) {
            foreach ($array as $element) {
                $dataMap[$element[$key]] = $element[$value];
            }
        }

        return $dataMap;
    }

    private $serviceTypes = null;

}