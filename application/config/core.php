<?php defined('BASEPATH') OR exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| Default Themes
|--------------------------------------------------------------------------
|
| Folder's name, which containing default themes.
|
*/
$config['admin_theme'] = '<admin_theme>';
$config['public_theme'] = '<public_theme>';
$config['public_template'] = '<public_template>';

/*
|--------------------------------------------------------------------------
| Default Limits
|--------------------------------------------------------------------------
|
| The default per page limit to process data results.
|
*/
$config['cli_default_limit'] = 100;
$config['ajax_default_limit'] = 25;

/*
|--------------------------------------------------------------------------
| Base Directory Path
|--------------------------------------------------------------------------
|
| Base directory path to manipulate with relative urls. Use a full server
| path with trailing slash.
|
*/
$config['base_path'] = '/var/www/<domain>/current/';

/*
|--------------------------------------------------------------------------
| Upload Directory Path
|--------------------------------------------------------------------------
|
| The location to save temporary files. Do not leave this BLANK or upload
| processing functions will not work. Use a full server path with trailing
| slash.
|
*/
$config['upload_path'] = '/var/www/<domain>/current/tmp/';

/*
|--------------------------------------------------------------------------
| Image Directory Path
|--------------------------------------------------------------------------
|
| Do not leave this BLANK or image upload processing functions will not
| work. Use a relative path with trailing slash.
|
*/
$config['assets_images'] = '/var/www/<domain>/current/assets/images/';
$config['assets_cache'] = '/var/www/<domain>/current/assets/cache/';

/*
|--------------------------------------------------------------------------
| Upload Maximum SIze
|--------------------------------------------------------------------------
|
| The maximum size (in kilobytes) that the file can be. Set to zero for no
| limit. Note: Most PHP installations have their own limit, as specified
| in the php.ini file. Usually 2 MB (or 2048 KB) by default.
|
*/
$config['upload_max_size'] = 2 * 1024;

/*
|--------------------------------------------------------------------------
| Image Allowed Types
|--------------------------------------------------------------------------
|
| The mime types corresponding to the types of files you allow to be
| uploaded. Usually the file extension can be used as the mime type. Can
| be either an array or a pipe-separated string.
|
*/
$config['upload_allowed_types'] = [
	'bmp',
	'gif',
	'jpg',
	'jpeg',
	'png',
	'svg',
	'webp',
];

/*
|--------------------------------------------------------------------------
| Reserved Words
|--------------------------------------------------------------------------
|
| Words reserved for the controllers, method names, application folders,
| other actions that can be interpreted in wrong way by the system. Usually,
| used as stop-words for url 'alias'.
|
*/
$config['reserved_words'] = [
	'admin',
	'ajax',
	'application',
	'cache',
	'assets',
	'errors',
	'logs',
	'public',
	'system',
	'tmp',
	'themes',
];

/*
|--------------------------------------------------------------------------
| Login Attempts
|--------------------------------------------------------------------------
|
| These options allow you to control amount of login attempts in a period
| of time (seconds). Make sure you set here proper values.
|
*/
$config['login_max_time'] = 10;
$config['login_max_attempts'] = 3;

/*
|--------------------------------------------------------------------------
| Profiler
|--------------------------------------------------------------------------
|
| Permits you to enable/disable the Profiler, which will display benchmark
| and other data at the bottom of your pages for debugging and optimization
| purposes. When enabled a report will be generated and inserted at the
| bottom of your pages.
|
*/
$config['profiler'] = FALSE;

/*
|--------------------------------------------------------------------------
| Miscellaneous
|--------------------------------------------------------------------------
|
| Miscellaneos options not needed to explain.
|
*/
$config['error_delimeter_left'] = '';
$config['error_delimeter_right'] = '<br>';
