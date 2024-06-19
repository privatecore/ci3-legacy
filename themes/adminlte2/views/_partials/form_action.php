<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="box-footer">
	<div class="row">
		<div class="col-md-offset-3 col-md-9">
			<?php if (isset($cancel_url) && $cancel_url) : ?>
				<a href="<?= $cancel_url; ?>" class="btn btn-warning">
					<i class="fa fa-reply bigger-110" aria-hidden="true"></i>
					<?= lang('admin_button_cancel'); ?>
				</a>
				&nbsp;&nbsp;
			<?php endif; ?>
			<button class="btn btn-default" type="reset">
				<i class="fa fa-undo bigger-110" aria-hidden="true"></i>
				<?= lang('admin_button_reset'); ?>
			</button>
			&nbsp;&nbsp;
			<?php if (isset($clone_url) && $clone_url) : ?>
				<a class="btn btn-info" data-toggle="modal" data-target="#modal-clone" data-id="<?= $item['id']; ?>">
					<i class="fa fa-clone bigger-110" aria-hidden="true"></i>
					<?= lang('admin_button_clone'); ?>
				</a>
				&nbsp;&nbsp;
			<?php endif; ?>
			<button class="btn btn-primary" type="submit">
				<i class="fa fa-check bigger-110" aria-hidden="true"></i>
				<?= lang('admin_button_save'); ?>
			</button>
		</div><!-- /.col -->
	</div><!-- /.row -->
</div><!-- /.box-footer -->
