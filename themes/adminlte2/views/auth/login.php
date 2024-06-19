<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<p class="login-box-msg"><?= lang('user_message_login_welcome'); ?></p>

<?= form_open() ?>
	<div class="form-group has-feedback<?= (form_error('email') OR form_error('login')) ? ' has-error' : ''; ?>">
		<?= form_input([
			'name'        => 'email',
			'type'        => 'email',
			'class'       => 'form-control',
			'placeholder' => lang('user_placeholder_email'),
		], set_value('email')); ?>
		<span class="glyphicon glyphicon-envelope form-control-feedback"></span>
	</div>

	<div class="form-group has-feedback<?= (form_error('password') OR form_error('login')) ? ' has-error' : ''; ?>">
		<?= form_password([
			'name'        => 'password',
			'class'       => 'form-control',
			'placeholder' => lang('user_placeholder_password'),
		]); ?>
		<span class="glyphicon glyphicon-lock form-control-feedback"></span>
	</div>

	<div class="row">
		<div class="col-xs-4 col-xs-offset-8">
			<?= form_submit([
				'name'  => 'login',
				'class' => 'btn btn-primary btn-block btn-flat',
			], lang('user_button_login')); ?>
		</div><!-- /.col -->
	</div><!-- /.row -->
<?= form_close(); ?>

<a href="<?= base_url('admin/remind') ?>"><?= lang('user_button_forgot_password'); ?></a>
