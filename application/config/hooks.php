<?php defined('BASEPATH') OR exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| Hooks
|--------------------------------------------------------------------------
|
| This file lets you define "hooks" to extend CI without hacking the core
| files.  Please see the user guide for info:
|
|	https://codeigniter.com/userguide3/general/hooks.html
|
*/

$hook['pre_system'] = array('function' => 'trim_post', 'filename' => 'input.php', 'filepath' => 'hooks');
$hook['display_override'] = array('function' => 'compress_html', 'filename' => 'output.php', 'filepath' => 'hooks');
