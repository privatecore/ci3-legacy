<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="row">
	<div class="col-md-4">
		&nbsp;
	</div><!-- /dummy -->

	<div class="col-md-2 text-left">
		<?php if (isset($order) && $order) : ?>
			<?= $order; ?>
		<?php endif; ?>
	</div><!-- /order -->

	<div class="col-md-2 text-left">
		<?php if (isset($limit) && $limit) : ?>
			<?= $limit; ?>
		<?php endif; ?>
	</div><!-- /limit -->

	<div class="col-md-4 text-right">
		<?php if (isset($pagination) && $pagination) : ?>
			<?= $pagination; ?>
		<?php endif; ?>
	</div><!-- /pagination -->
</div><!-- /.row -->
