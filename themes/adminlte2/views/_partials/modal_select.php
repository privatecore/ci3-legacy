<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="modal fade" id="modal-select" tabindex="-1" role="dialog" aria-labelledby="modalSelectLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
				<h4 class="modal-title" id="modalSelectLabel"><?= lang('admin_title_entry_select'); ?></h4>
			</div><!-- /.modal-header -->

			<div class="modal-body">
				<!-- body -->
			</div><!-- /.modal-body -->

			<div class="modal-footer">
				<?= form_button(['class' => 'btn btn-default', 'data-dismiss' => 'modal'], lang('admin_button_close')); ?>
			</div><!-- /.modal-footer -->
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div><!-- /#select -->
