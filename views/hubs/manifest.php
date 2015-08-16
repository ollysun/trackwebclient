<?php
use Adapter\Util\Calypso;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
$this->title = 'View Manifest: ';
$this->params['breadcrumbs'][] = 'Manifest';
?>

<?php
$this->params['content_header_button'] = '<span class="label label-success">CONFIRMED MANIFEST</span>';
?>

<div class="main-box">
    <div class="main-box-header">
        <h2>View Manifest</h2>
    </div>
    <div class="main-box-body" id="print_area">
        <div class="well">
            <div class="row">
                <div class="col-md-6">
                    <label>Name</label>
                    <p><?php echo ucwords(Calypso::getValue($staff, 'fullname', 'N/A')); ?></p>
                    <label>Email</label>
                    <p><?php echo Calypso::getValue($staff, 'email', 'N/A'); ?></p>
                    <label>Phone Number</label>
                    <p><?php echo Calypso::getValue($staff, 'phone', 'N/A'); ?></p>
                </div>
                <div class="col-md-6">
                    <label>Staff ID</label>
                    <p><?php echo Calypso::getValue($staff, 'staff_id', 'N/A'); ?></p>
                    <label>Role</label>
                    <p><?php echo strtoupper(Calypso::getValue($staff, 'role.name', 'N/A')); ?></p>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="main-box-body">
                <div class="table-responsive">

                    <?php if(!empty($parcel_delivery)) { ?>
                    <table id="next_dest" class="table table-hover next_dest">
                        <thead>
                        <tr>
                            <th style="width: 20px">S/N</th>
                            <th>Waybill No</th>
                            <th>Origin</th>
                            <th>Next Destination</th>
                            <th>Final Destination</th>
                            <th>Weight (Kg)</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                            $row = 1;
                            foreach ($parcel_delivery as $parcels) {

                                echo "<tr>";
                                echo "<td>{$row}</td>";
                                echo "<td>". Calypso::getValue($parcels, 'waybill_number')."</td>";
                                echo "<td>" . ucwords(Calypso::getValue($parcels, 'sender_address.city.name') . ', ' . Calypso::getValue($parcels, 'sender_address.state.name')) . "</td>";
                                echo "<td>" . strtoupper(Calypso::getValue($parcels, 'to_branch.name')) ."</td>";
                                echo "<td>" . ucwords(Calypso::getValue($parcels, 'receiver_address.city.name') . ', ' . Calypso::getValue($parcels, 'receiver_address.state.name')) . "</td>";
                                echo "<td>" . Calypso::getValue($parcels, 'weight') . "</td>";
                                echo "</tr>";
                                $row++;
                            }
                        ?>
                        </tbody>
                    </table>
                    <?php } else { ?>
                        <p>No record to display</p>
                    <?php }  ?>
                </div>
            </div>
        </div>

        <div class="pull-right">
            <button onclick="PrintElem('#print_area',this)" class="btn btn-primary">Print Manifest</button>
        </div>

        <div class="clearfix"></div>
    </div>
</div>
<!--<script type="text/javascript">
    var waybill = "<?/*= strtoupper($parcelData['waybill_number']); */?>";
</script>-->
<?php

?>
<?php $this->registerJsFile('@web/js/libs/jquery-barcode.min.js', ['depends' => [\yii\web\JqueryAsset::className()]]) ?>
<?php $this->registerJsFile('@web/js/barcode.js', ['depends' => [\yii\web\JqueryAsset::className()]]) ?>

<script>
    function PrintElem(elem,ref)
    {
        $(ref).addClass('hidden');
        $(ref).attr('style','display:none');
        Popup($(elem).html(),function(){
            $(ref).removeClass('hidden');
            $(ref).attr('style','');
        });
    }

    function Popup(data, callback)
    {
        /*var mywindow = window.open('', 'Manifest Generation', 'height=400,width=600');
     mywindow.document.write('<html><head><title>Manifest Generation</title>');
     /!*optional stylesheet*!/ //mywindow.document.write('<link rel="stylesheet" href="main.css" type="text/css" />');
     mywindow.document.write('</head><body >');
     mywindow.document.write(data);
     mywindow.document.write('</body></html>');

     mywindow.document.close(); // necessary for IE >= 10
     mywindow.focus(); // necessary for IE >= 10

     mywindow.print();
     mywindow.close();*/
        window.print();
        if(typeof callback == 'function'){
            callback();
        }
        return true;
    }
</script>