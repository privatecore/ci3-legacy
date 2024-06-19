<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<?php if ($this->session->flashdata('success')) : ?>
	<div class="alert alert-success alert-dismissable fade show" role="alert">
		<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		<?= $this->session->flashdata('success'); ?>
	</div>
<?php endif; ?>
<?php if ($this->session->flashdata('warning')) : ?>
	<div class="alert alert-warning alert-dismissable fade show" role="alert">
		<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		<?= $this->session->flashdata('warning'); ?>
	</div>
<?php endif; ?>
<?php if ($this->session->flashdata('info')) : ?>
	<div class="alert alert-info alert-dismissable fade show" role="alert">
		<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		<?= $this->session->flashdata('info'); ?>
	</div>
<?php endif; ?>
<?php if ($this->session->flashdata('error')) : ?>
	<div class="alert alert-danger alert-dismissable fade show" role="alert">
		<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		<?= $this->session->flashdata('error'); ?>
	</div>
<?php elseif (validation_errors()) : ?>
	<div class="alert alert-danger alert-dismissable fade show" role="alert">
		<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		<?= validation_errors(); ?>
	</div>
<?php elseif ($this->theme->get_error()) : ?>
	<div class="alert alert-danger alert-dismissable fade show mb-0" role="alert">
		<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		<?= $this->theme->get_error(); ?>
	</div>
<?php endif; ?>
