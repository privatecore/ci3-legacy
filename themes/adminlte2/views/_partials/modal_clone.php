<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<?php if (isset($clone_url) && $clone_url) : ?>
	<div class="modal modal-info fade" id="modal-clone" tabindex="-1" role="dialog" aria-labelledby="modalCloneLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
					<h4 class="modal-title" id="modalCloneLabel"><?= lang('admin_title_entry_clone'); ?></h4>
				</div>

				<div class="modal-body">
					<?= form_open($clone_url, array('role' => 'form', 'id' => 'modal-clone-form')); ?>
						<p><?= lang('admin_message_entry_clone_confirm'); ?></p>
						<?= form_input(['type' => 'hidden', 'name' => 'id', 'id' => 'clone-field-id', 'class' => 'modal-field-id']); ?>
					<?= form_close(); ?>
				</div>

				<div class="modal-footer">
					<?= form_button(['class' => 'btn btn-outline', 'data-dismiss' => 'modal'], lang('admin_button_close')); ?>
					<?= form_submit(['class' => 'btn btn-outline modal-submit'], lang('admin_button_clone')); ?>
				</div>
			</div>
		</div>
	</div>
<?php endif; ?>
