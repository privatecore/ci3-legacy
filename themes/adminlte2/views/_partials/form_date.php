<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<?php if (isset($item['id']) && $item['id']) : ?>
	<div class="form-group">
		<?= form_label(lang('admin_input_date_added'), '', ['class' => 'control-label']); ?>
		<div class="form-control-plaintext"><?= $item['date_added']; ?></div>
	</div>

	<div class="form-group">
		<?= form_label(lang('admin_input_date_modified'), '', ['class' => 'control-label']); ?>
		<div class="form-control-plaintext"><?= $item['date_modified']; ?></div>
	</div>
<?php endif; ?>
