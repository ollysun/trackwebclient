<?php
use yii\widgets\Breadcrumbs;
?>
<div id="content-header" class="clearfix">
  <?= Breadcrumbs::widget([
    'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
  ]) ?>

  <div class="pull-left">
    <?php
      if ( !isset($this->params['page_title']) ) {
        $this->params['page_title'] = $this->title;
      }
    ?>
    <h1><?= $this->params['page_title']; ?></h1>
  </div>

  <?php if (isset($this->params['content_header_button'])): ?>
    <div class="pull-right">
      <?php echo $this->params['content_header_button']; ?>
    </div>
  <?php endif; ?>
</div> <!-- /#content-header -->