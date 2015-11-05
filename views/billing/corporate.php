<?php
/* @var $this yii\web\View */
use Adapter\Util\Calypso;

$this->title = 'Billing: Corporate';
$this->params['breadcrumbs'] = array(
    array(
        'label' => 'Billing',
        'url' => ['billing/']
    ),
    array('label' => 'Corporate')
);
$this->params['content_header_button'] = '';
?>

<?= Calypso::showFlashMessages(); ?>


<div class="main-box">
    <div class="main-box-header">
    </div>
    <div class="main-box-body">
        <table id="table" class="table table-bordered">
            <thead>
            <tr>
                <th>S/N</th>
                <th>Billing Name</th>
                <th>Associated Company</th>
                <th>Actions</th>
            </tr>
            </thead>
        </table>
    </div>
</div>
