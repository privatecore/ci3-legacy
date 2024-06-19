<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<!-- Left side column. contains the sidebar -->
<aside class="main-sidebar">
	<!-- sidebar: style can be found in sidebar.less -->
	<section class="sidebar">
		<!-- sidebar menu: : style can be found in sidebar.less -->
		<ul class="sidebar-menu" data-widget="tree">
			<li class="header">NAVIGATION</li>
			<li class="<?= link_active(['@admin', '@admin/index']); ?>">
				<a href="<?= base_url('admin/index') ?>">
					<i class="fa fa-dashboard"></i>
					<span><?= lang('admin_title_dashboard'); ?></span>
				</a>
			</li>
			<li class="treeview <?= link_active('admin/category', 'active menu-open'); ?>">
				<a href="#">
					<i class="fa fa-list"></i>
					<span><?= lang('admin_title_category'); ?></span>
					<span class="pull-right-container">
						<i class="fa fa-angle-left pull-right"></i>
					</span>
				</a>
				<ul class="treeview-menu" <?= link_active('admin/category/item', 'style="display: block;"'); ?>>
					<li class="<?= link_active('admin/category/item'); ?>">
						<a href="<?= base_url('admin/category/item') ?>">
							<i class="fa fa-circle-o"></i>
							<?= lang('admin_title_category_item'); ?>
						</a>
					</li>
					<li class="<?= link_active('admin/category/item'); ?>">
						<a href="<?= base_url('admin/category/item') ?>">
							<i class="fa fa-circle-o"></i>
							<?= lang('admin_title_category_item'); ?>
						</a>
					</li>
					<li class="<?= link_active('admin/category/item'); ?>">
						<a href="<?= base_url('admin/category/item') ?>">
							<i class="fa fa-circle-o"></i>
							<?= lang('admin_title_category_item'); ?>
						</a>
					</li>
				</ul>
			</li>
			<li class="<?= link_active('admin/user'); ?>">
				<a href="<?= base_url('admin/user') ?>">
					<i class="fa fa-users"></i>
					<span><?= lang('admin_title_user'); ?></span>
				</a>
			</li>
			<li class="<?= link_active('admin/setting'); ?>">
				<a href="<?= base_url('admin/setting') ?>">
					<i class="fa fa-cog"></i>
					<span><?= lang('admin_title_setting'); ?></span>
				</a>
			</li>
		</ul>
	</section><!-- /.sidebar -->
</aside>
