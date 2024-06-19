<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<?php $this->theme->view('_partials/control_top'); ?>

<div class="row">
	<div class="col-sm-12">
		<div class="box box-primary">
			<div class="box-body">
				<table id="simple-table" class="table table-bordered table-hover">
					<thead>
					<tr>
						<th class="col-md-1 col-sm-1 text-center">#</th>
						<th class="col-md-1 col-sm-1 text-center"><?= lang('admin_input_status'); ?></th>
						<th><?= lang('admin_input_name'); ?></th>
						<th class="hidden-xs"><?= lang('admin_input_email'); ?></th>
						<th class="col-md-2 col-sm-1 hidden-xs">
							<i class="fa fa-clock-o" aria-hidden="true"></i>
							<?= lang('admin_input_date_added'); ?>
						</th>
						<th class="col-md-2 col-sm-1 hidden-xs">
							<i class="fa fa-clock-o" aria-hidden="true"></i>
							<?= lang('admin_input_date_modified'); ?>
						</th>
						<th class="col-md-1 col-sm-2 col-xs-2"></th>
					</tr>
					</thead>

					<tbody>
					<?php if (isset($results) && $results) : ?>
						<?php foreach ($results as $item) : ?>
							<tr class="click-row" data-id="<?= $item['id']; ?>" <?php if (isset($edit_url) && $edit_url) : ?>data-href="<?= "{$edit_url}/{$item['id']}"; ?>"<?php endif; ?>>
								<td class="text-center modal-append"><?= $item['id']; ?></td>
								<td class="text-center">
									<?php if ($item['status']) : ?>
										<span rel="tooltip" title="<?= htmlentities(lang('admin_tooltip_enabled')); ?>">
											<i class="fa fa-check text-green" aria-hidden="true"></i>
										</span>
									<?php else : ?>
										<span rel="tooltip" title="<?= htmlentities(lang('admin_tooltip_disabled')); ?>">
											<i class="fa fa-times text-red" aria-hidden="true"></i>
										</span>
									<?php endif; ?>
								</td>
								<td><?= $item['first_name']; ?> <?= $item['last_name']; ?></td>
								<td class="hidden-xs"><?= $item['email']; ?></td>
								<td class="hidden-xs"><?= $item['date_added']; ?></td>
								<td class="hidden-xs"><?= $item['date_modified']; ?></td>
								<td class="text-center">
									<div class="btn-group">
										<?php if (isset($edit_url) && $edit_url) : ?>
											<a href="<?= "{$edit_url}/{$item['id']}"; ?>" class="btn btn-xs btn-primary">
												<i class="fa fa-pencil icon-only" aria-hidden="true"></i>
											</a>
										<?php endif; ?>

										<?php if (isset($delete_url) && $delete_url) : ?>
											<a href="#" class="btn btn-xs btn-danger" data-toggle="modal" data-target="#modal-delete">
												<i class="fa fa-trash-o icon-only" aria-hidden="true"></i>
											</a>
										<?php endif; ?>
									</div>
								</td>
							</tr>
						<?php endforeach; ?>
					<?php else : ?>
						<tr>
							<td colspan="100"><?= lang('admin_error_no_results_found'); ?></td>
						</tr>
					<?php endif; ?>
					</tbody>
				</table>
			</div><!-- /.box-body -->
		</div>
	</div><!-- /.col -->
</div><!-- /.row -->

<?php $this->theme->view('_partials/control_bottom'); ?>
<?php $this->theme->view('_partials/modal_delete'); ?>
