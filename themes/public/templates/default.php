<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Default template
 */
?><!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<link rel="shortcut icon" href="<?= $this->theme->get_url('assets/images/favicon.ico'); ?>?t=<?= @filemtime($this->theme->get_path('assets/images/favicon.ico')); ?>" type="image/x-icon">
	<title><?= $meta_title ?? ''; ?></title>
	<meta name="keywords" content="<?= $meta_keywords ?? ''; ?>">
	<meta name="description" content="<?= $meta_description ?? ''; ?>">

	<?php if (isset($canonical_url) && $canonical_url) : ?><?= PHP_EOL; ?><link rel="canonical" href="<?= $canonical_url; ?>"><?php endif; ?>

	<?php if (isset($css_files_external) && is_array($css_files_external)) : ?>
		<?php foreach ($css_files_external as $file) : ?>
			<link rel="stylesheet" href="<?= $file; ?>" media="all">
		<?php endforeach; ?>
	<?php endif; ?>
	<?php if (isset($css_files) && is_array($css_files)) : ?>
		<?php foreach ($css_files as $file) : ?>
			<link rel="stylesheet" href="<?= $file; ?>" media="all">
		<?php endforeach; ?>
	<?php endif; ?>
</head>

<body>
	<?php $this->theme->view('_partials/default_alert'); ?>

	<main role="main">
		<section class="main-content">
			<?= $content; ?>
		</section>
	</main><!-- /.container -->

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
</body>
</html>