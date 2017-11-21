<?php
use Adapter\Util\Calypso;

?>

<?php if (isset($stats)): ?>

<ul class="graph-stats content-header-graph-stats">
	<?php
		$stat = $stats; // could be later changed to $stats[$i] in the case of an array
	?>
	<li>
		<?php
			$used = $stat['used'];
			$total = $stat['total'];
			$per = number_format(($used * 100  / $total) , 2);
			$class = !empty($stat['class']) ? ' progress-bar-'.$stat['class'] : '';
			// form used and total
			$used_fmt = Calypso::getInstance()->formatCurrency($used);
			$used_fmt_0 = Calypso::getInstance()->formatCurrency($used,0);
			$total_fmt = Calypso::getInstance()->formatCurrency($total);
		?>
		<div class="clearfix progress-width">
			<div class="title pull-left">
				Credit Limit
			</div>
			<div class="value pull-right">
				<span class="currency naira"><?= $used_fmt_0; ?></span>
			</div>
		</div>
		<div class="progress progress-width">
			<div style="width: <?= $per; ?>%;" aria-valuemax="100" aria-valuemin="0" aria-valuenow="<?= $per; ?>" role="progressbar" class="progress-bar<?= $class; ?>">
				<span class="sr-only"><?= $per; ?>% Used</span>
			</div>
		</div>
		<div>
			<small class="text-muted">
				<strong class="currency naira"><?= $used_fmt; ?></strong> (<?= $per; ?>%) of <strong class="currency naira"><?= $total_fmt; ?></strong> limit used.
			</small>
		</div>
	</li>
</ul>

<?php endif ?>