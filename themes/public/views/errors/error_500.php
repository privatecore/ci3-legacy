<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="error-page">
	<h2 class="headline text-red"><?= $status_code; ?></h2>

	<div class="error-content">
		<h3>
			<i class="fa fa-warning text-red"></i>
			<?= $heading; ?>
		</h3>

		<p><?= $message; ?></p>
	</div><!-- /.error-content -->
</div><!-- /.error-page -->
