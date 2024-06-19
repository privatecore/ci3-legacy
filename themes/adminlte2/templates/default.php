<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Admin Template
 */
?><!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title><?= $title; ?></title>
	<!-- Tell the browser to be responsive to screen width -->
	<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
	<link rel="shortcut icon" href="<?= $this->theme->get_url('assets/images/favicon.ico'); ?>?t=<?= @filemtime($this->theme->get_path('assets/images/favicon.ico')); ?>" type="image/x-icon" />

	<?php if (isset($css_files_external) && is_array($css_files_external)) : ?>
		<?php foreach ($css_files_external as $file) : ?>
			<link rel="stylesheet" href="<?= $file; ?>" media="all" />
		<?php endforeach; ?>
	<?php endif; ?>
	<?php if (isset($css_files) && is_array($css_files)) : ?>
		<?php foreach ($css_files as $file) : ?>
			<link rel="stylesheet" href="<?= $file; ?>" media="all" />
		<?php endforeach; ?>
	<?php endif; ?>

	<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
	<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
	<!--[if lt IE 9]>
		<script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
		<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
	<![endif]-->

	<!-- Google Font -->
	<link rel="stylesheet"
		href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
</head>
<body class="hold-transition skin-blue sidebar-mini">
<!-- Site wrapper -->
<div class="wrapper">
	<header class="main-header">
		<!-- Logo -->
		<a href="<?= base_url('admin/index') ?>" class="logo">
			<!-- mini logo for sidebar mini 50x50 pixels -->
			<span class="logo-mini"><b>A</b>LT</span>
			<!-- logo for regular state and mobile devices -->
			<span class="logo-lg"><b>Admin</b>LTE</span>
		</a>
		<!-- Header Navbar: style can be found in header.less -->
		<nav class="navbar navbar-static-top">
			<!-- Sidebar toggle button-->
			<a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
				<span class="sr-only">Toggle navigation</span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</a>

			<div class="navbar-custom-menu">
				<ul class="nav navbar-nav">
					<!-- User Account: style can be found in dropdown.less -->
					<li class="dropdown user user-menu">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown">
							<span><?= $this->acl->get_user('first_name'); ?> <?= $this->acl->get_user('last_name'); ?></span>
						</a>
						<ul class="dropdown-menu">
							<!-- User image -->
							<li class="user-header">
								<p>
									<?= $this->acl->get_user('first_name'); ?> <?= $this->acl->get_user('last_name'); ?>
									<small>Registered <?= $this->acl->get_user('date_added'); ?></small>
								</p>
							</li>
							<!-- Menu Footer-->
							<li class="user-footer">
								<div class="pull-left">
									<a href="<?= base_url('admin/auth/profile'); ?>" class="btn btn-default btn-flat">Profile</a>
								</div>
								<div class="pull-right">
									<a href="<?= base_url('admin/auth/logout'); ?>" class="btn btn-default btn-flat">Logout</a>
								</div>
							</li>
						</ul>
					</li>
				</ul>
			</div>
		</nav>
	</header>

	<?php $this->theme->view('_partials/sidebar'); ?>

	<!-- Content Wrapper. Contains page content -->
	<div class="content-wrapper">
		<!-- Content Header (Page header) -->
		<section class="content-header">
			<h1>
				<?= $title; ?>
				<small><?= $description; ?></small>
			</h1>
		</section>

		<!-- Main content -->
		<section class="content">
			<?php $this->theme->view('_partials/alerts'); ?>

			<?= $content; ?>
		</section><!-- /.content -->
	</div><!-- /.content-wrapper -->
</div><!-- ./wrapper -->

<?php if (isset($js_files_external) && is_array($js_files_external)) : ?>
	<?php foreach ($js_files_external as $file) : ?>
		<script src="<?= $file; ?>"></script>
	<?php endforeach; ?>
<?php endif; ?>
<?php if (isset($js_files) && is_array($js_files)) : ?>
	<?php foreach ($js_files as $file) : ?>
		<script src="<?= $file; ?>"></script>
	<?php endforeach; ?>
<?php endif; ?>
<?php if (isset($js_files_i18n) && is_array($js_files_i18n)) : ?>
	<?php foreach ($js_files_i18n as $i18n) : ?>
		<script><?= PHP_EOL.$i18n.PHP_EOL; ?></script>
	<?php endforeach; ?>
<?php endif; ?>

<script>
	$(document).ready(function () {
		$('.sidebar-menu').tree()
	})
</script>
</body>
</html>