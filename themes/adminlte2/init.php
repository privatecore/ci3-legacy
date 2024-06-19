<?php defined('BASEPATH') OR exit('No direct script access allowed');

// admin theme name/folder
$theme = config_item('admin_theme');

/**
 * Initialize theme
 */
get_instance()->theme
	->add_css([
		"/themes/{$theme}/assets/bower_components/bootstrap/dist/css/bootstrap.min.css",
		"/themes/{$theme}/assets/bower_components/font-awesome/css/font-awesome.min.css",
		"/themes/{$theme}/assets/bower_components/Ionicons/css/ionicons.min.css",
		"/themes/{$theme}/assets/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css",
		"/themes/{$theme}/assets/dist/css/AdminLTE.min.css",
		"/themes/{$theme}/assets/dist/css/skins/_all-skins.css",
		"/themes/{$theme}/assets/css/custom.css",
		"/themes/{$theme}/assets/plugins/bootoast/bootoast.min.css",
		"/themes/{$theme}/assets/plugins/iCheck/all.css",
		"/themes/{$theme}/assets/plugins/summernote/summernote.min.css",
	], TRUE)
	->add_js([
		"/themes/{$theme}/assets/bower_components/jquery/dist/jquery.min.js",
		"/themes/{$theme}/assets/bower_components/jquery-ui/jquery-ui.min.js",
		"/themes/{$theme}/assets/bower_components/bootstrap/dist/js/bootstrap.min.js",
		"/themes/{$theme}/assets/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js",
		"/themes/{$theme}/assets/bower_components/jquery-slimscroll/jquery.slimscroll.min.js",
		"/themes/{$theme}/assets/bower_components/fastclick/lib/fastclick.js",
		"/themes/{$theme}/assets/dist/js/adminlte.min.js",
		"/themes/{$theme}/assets/plugins/bootoast/bootoast.min.js",
		"/themes/{$theme}/assets/plugins/iCheck/icheck.min.js",
		"/themes/{$theme}/assets/plugins/summernote/summernote.min.js",
		"/themes/{$theme}/assets/plugins/summernote/lang/summernote-ru-RU.js",
		"/themes/{$theme}/assets/js/custom.js",
		"/themes/{$theme}/assets/js/functions.js",
		"/themes/{$theme}/assets/js/core.js",
	], TRUE);
