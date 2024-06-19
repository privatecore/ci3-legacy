<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<?= form_open('', ['role' => 'form', 'class' => 'form-horizontal']); ?>
	<div class="row">
		<div class="col-md-12">
			<div class="box box-primary">
				<div class="box-body">
					<div class="form-group<?= form_error('email') ? ' has-error' : ''; ?>">
						<?= form_label(lang('admin_input_email'), 'form-field-email', ['class' => 'col-sm-3 control-label required']); ?>
						<div class="col-sm-5">
							<div class="input-group">
								<?= form_input([
									'name'     => 'email',
									'id'       => 'form-field-email',
									'class'    => 'form-control',
									'disabled' => 'disabled',
								], set_value('email', $item['email'])); ?>
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
							], set_value('first_name', $item['first_name'])); ?>
						</div>
					</div>

					<div class="form-group<?= form_error('last_name') ? ' has-error' : ''; ?>">
						<?= form_label(lang('admin_input_last_name'), 'form-field-lastname', ['class' => 'col-sm-3 control-label required']); ?>
						<div class="col-sm-5">
							<?= form_input([
								'name'  => 'last_name',
								'id'    => 'form-field-lastname',
								'class' => 'form-control',
							], set_value('last_name', $item['last_name'])); ?>
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
							<span class="help-block"><?= lang('admin_help_passwords'); ?></span>
						</div>
					</div>
				</div><!-- /.box-body -->

				<?php if (isset($item_id)) : ?>
					<?= form_hidden('id', $item_id); ?>
				<?php endif; ?>

				<div class="box-footer">
					<div class="form-actions">
						<div class="col-md-offset-3 col-md-9">
							<a href="<?= $cancel_url; ?>" class="btn btn-warning">
								<i class="fa fa-reply bigger-110" aria-hidden="true"></i>
								<?= lang('admin_button_cancel'); ?>
							</a>
							&nbsp;&nbsp;
							<button type="submit" class="btn btn-info">
								<i class="fa fa-check bigger-110" aria-hidden="true"></i>
								<?= lang('admin_button_save'); ?>
							</button>
						</div>
					</div><!-- /.form-actions -->
				</div><!-- /.box-footer -->
			</div><!-- /.box -->
		</div><!-- /.col -->
	</div><!-- /.row -->
<?= form_close(); ?>
