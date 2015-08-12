
<?php
use yii\data\Pagination;
use yii\widgets\LinkPager;
$pagination = new Pagination(['totalCount'=>$total_count,'defaultPageSize'=>$page_width]);
?>


<div class="clearfix">
    <div class="pull-left">
        <div class="form-control-static">
            Showing <?= $first+1;?> &ndash; <?= $last; ?> of <?= $total_count; ?>
        </div>
    </div>
    <div class="pull-right">
        <?= LinkPager::widget(['pagination'=>$pagination]) ?>
    </div>
</div>