<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="row">
	<div class="col-md-12">
		<div class="box box-primary">
			<?= form_open('', ['role' => 'form', 'class' => 'form-horizontal']); ?>
				<div class="box-body">
					<?php foreach ($settings as $setting) : ?>
						<?php $field_data = [];
						if ($setting['options'])
						{
							$field_options = [];
							if ($setting['input_type'] == 'dropdown')
							{
								$field_options[''] = lang('admin_option_default');
							}
							$lines = explode("\n", $setting['options']);
							foreach ($lines as $line)
							{
								$option = explode('|', $line);
								$field_options[$option[0]] = $option[1];
							}
						}

						$field_data['name'] = $setting['name'];
						$field_data['id'] = $setting['name'];
						$field_data['class'] = 'col-xs-10 col-sm-6 form-control' . ($setting['show_editor'] ? ' wysiwyg' : '');
						$field_data['value'] = $setting['value']; ?>
						<div class="form-group<?= form_error($setting['name']) ? ' has-error' : ''; ?>">
							<?= form_label(lang($setting['label']), $setting['name'], ['class' => 'col-sm-3 control-label']); ?>
							<div class="col-sm-5">
								<?php if ($setting['help_text']) : ?>
									<div class="clearfix">
								<?php endif; ?>

								<?php if ($setting['input_type'] == 'input')
								{
									echo form_input($field_data);
								}
								elseif ($setting['input_type'] == 'textarea')
								{
									echo form_textarea($field_data);
								}
								elseif ($setting['input_type'] == 'radio')
								{
									echo '<div>';
									foreach ($field_options as $value => $label)
									{
										echo form_radio(['name' => $field_data['name'], 'id' => $field_data['id'] . '-' . $value], $value, ($value == $field_data['value']));
										echo $label . '&nbsp;&nbsp;';
									}
									echo '</div>';
								}
								elseif ($setting['input_type'] == 'dropdown')
								{
									echo form_dropdown($setting['name'], $field_options, $field_data['value'], ['id' => $field_data['id'], 'class' => $field_data['class']]);
								}
								elseif ($setting['input_type'] == 'timezones')
								{
									echo timezone_menu($field_data['value'], $field_data['class']);
								} ?>

								<?php if ($setting['help_text']) : ?>
									</div>
									<span class="help-block"><?= lang($setting['help_text']); ?></span>
								<?php endif; ?>
							</div>
						</div>
					<?php endforeach; ?>
				</div><!-- /.box-body -->

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
			<?= form_close(); ?>
		</div><!-- /.box -->
	</div><!--/.col -->
</div><!-- /.row -->
