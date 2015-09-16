
<?php
use \Adapter\Util\Calypso;

$pagination = Calypso::getValue($pagination, 'text', false);

?>

<div class="pull-left">
    <p class="form-control-static input-sm">Showing 1 to 49 of 49 shipments</p>
</div>

<div class="pull-right form-group form-group-sm form-inline">
    <label for="page_width">Records</label>
    <select name="page_width" id="page_width" class="form-control ">
        <?php
        $page_width = isset($page_width) ? $page_width : 50;
        for($i = 50; $i <= 500; $i+=50){
            ?>
            <option <?= $page_width==$i?'selected':'' ?> value="<?= $i ?>"><?= $i ?></option>
        <?php
        }
        ?>
    </select>
</div>

<div class="pull-right">
    <ul class="pagination">
        <li><a href="">&larr;</a></li>
        <li><a href="">1</a></li>
        <li><a href="">2</a></li>
        <li><a href="">3</a></li>
        <li><a href="">4</a></li>
        <li><a href="">&rarr;</a></li>
    </ul>
</div>