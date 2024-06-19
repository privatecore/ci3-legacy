<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<?= form_open('', ['class' => 'form-horizontal']); ?>
	<div class="row">
		<div class="col-md-12">
			<div class="box box-primary">
				<div class="box-body">
					<div class="form-group<?= form_error('status') ? ' has-error' : ''; ?>">
						<?= form_label(lang('admin_input_status'), 'form-field-status', ['class' => 'col-sm-3 control-label required']); ?>
						<div class="col-sm-5">
							<?= form_dropdown([
								'name'  => 'status',
								'id'    => 'form-field-status',
								'class' => 'form-control',
							], [
								0 => lang('admin_option_disabled'),
								1 => lang('admin_option_enabled'),
							], set_value('status', $item['status'] ?? 0)); ?>
						</div>
					</div>

					<div class="form-group<?= form_error('email') ? ' has-error' : ''; ?>">
						<?= form_label(lang('admin_input_email'), 'form-field-email', ['class' => 'col-sm-3 control-label required']); ?>
						<div class="col-sm-5">
							<div class="input-group">
								<?= form_input([
									'name'  => 'email',
									'id'    => 'form-field-email',
									'class' => 'form-control',
								], set_value('email', $item['email'] ?? '')); ?>
								<span class="input-group-addon"><i class="fa fa-envelope"></i></span>
							</div>
						</div>
					</div>

					<div class="form-group<?= form_error('first_name') ? ' has-error' : ''; ?>">
						<?= form_label(lang('admin_input_first_name'), 'form-field-firstname', ['class' => 'col-sm-3 control-label required']); ?>
						<div class="col-sm-5">
							<?= form_input([
								'name'  => 'first_name',
								'id'    => 'form-field-firstname',
								'class' => 'form-control',
							], set_value('first_name', $item['first_name'] ?? '')); ?>
						</div>
					</div>

					<div class="form-group<?= form_error('last_name') ? ' has-error' : ''; ?>">
						<?= form_label(lang('admin_input_last_name'), 'form-field-lastname', ['class' => 'col-sm-3 control-label']); ?>
						<div class="col-sm-5">
							<?= form_input([
								'name'  => 'last_name',
								'id'    => 'form-field-lastname',
								'class' => 'form-control',
							], set_value('last_name', $item['last_name'] ?? '')); ?>
						</div>
					</div>

					<div class="form-group<?= form_error('password') ? ' has-error' : ''; ?>">
						<?= form_label(lang('admin_input_password'), 'form-field-pass1', ['class' => 'col-sm-3 control-label required']); ?>
						<div class="col-sm-5">
							<div class="input-group">
								<?= form_password([
									'name'         => 'password',
									'id'           => 'form-field-pass1',
									'class'        => 'form-control',
									'autocomplete' => 'off',
								]); ?>
								<span class="input-group-addon"><i class="fa fa-lock"></i></span>
							</div>
						</div>
					</div>

					<div class="form-group<?= form_error('password_repeat') ? ' has-error' : ''; ?>">
						<?= form_label(lang('admin_input_password_repeat'), 'form-field-pass2', ['class' => 'col-sm-3 control-label required']); ?>
						<div class="col-sm-5">
							<div class="clearfix">
								<div class="input-group">
									<?= form_password([
										'name'         => 'password_repeat',
										'id'           => 'form-field-pass2',
										'class'        => 'form-control',
										'autocomplete' => 'off',
									]); ?>
									<span class="input-group-addon"><i class="fa fa-lock"></i></span>
								</div>
							</div>
							<?php if (empty($password_required)) : ?>
								<span class="help-block"><?= lang('admin_help_passwords'); ?></span>
							<?php endif; ?>
						</div>
					</div>

					<?php if (isset($item['id']) && $item['id']) : ?>
						<div class="form-group">
							<div class="clearfix">
								<?= form_label(lang('admin_input_date_added'), '', ['class' => 'col-sm-3 control-label']); ?>
								<div class="col-sm-9">
									<div class="form-control-static"><?= $item['date_added']; ?></div>
								</div>
							</div>

							<div class="clearfix">
								<?= form_label(lang('admin_input_date_modified'), '', ['class' => 'col-sm-3 control-label']); ?>
								<div class="col-sm-9">
									<div class="form-control-static"><?= $item['date_modified']; ?></div>
								</div>
							</div>
						</div>
					<?php endif; ?>
				</div><!-- /.box-body -->

				<?php $this->theme->view('_partials/form_action'); ?>
			</div><!-- /.box -->
		</div><!-- /.col -->
	</div><!-- /.row -->

	<?php if (isset($item['id']) && $item['id']) : ?>
		<?= form_hidden('id', $item['id']); ?>
	<?php endif; ?>
<?= form_close(); ?>