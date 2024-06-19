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
<body class="hold-transition login-page">
<div class="login-box">
	<div class="login-logo">
		<?= $title; ?>
	</div>
	<!-- /.login-logo -->
	<div class="login-box-body">
		<?php $this->theme->view('_partials/alerts'); ?>

		<?= $content; ?>
	</div>
	<!-- /.login-box-body -->
</div>
<!-- /.login-box -->

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
	$(function () {
		$('input').iCheck({
			checkboxClass: 'icheckbox_square-blue',
			radioClass: 'iradio_square-blue',
			increaseArea: '20%' /* optional */
		});
	});
</script>
</body>
</html>