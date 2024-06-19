<?php defined('BASEPATH') OR exit('No direct script access allowed');

// admin theme name/folder
$theme = config_item('public_theme');

/**
 * Initialize theme
 */
get_instance()->theme
	->add_css([ /* javascript files */ ], TRUE)
	->add_js([ /* css files */ ], TRUE);
