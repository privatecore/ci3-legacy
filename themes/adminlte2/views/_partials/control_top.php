<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="row margin-bottom">
	<div class="col-md-4 text-left">
		<?php if (isset($add_url) && $add_url) : ?>
			<a href="<?= $add_url; ?>" class="btn btn-sm btn-success">
				<i class="fa fa-plus" aria-hidden="true"></i>
				<?= lang('admin_button_create'); ?>
			</a>
		<?php endif; ?>
	</div><!-- /buttons -->

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
