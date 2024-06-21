<?php defined('BASEPATH') OR exit('No direct script access allowed');

if ( ! function_exists('trim_post'))
{
	/**
	 * Trim $_POST input data
	 *
	 * @see https://stackoverflow.com/a/49204642
	 * @return void
	 */
	function trim_post()
	{
		$_POST = filter_var($_POST, FILTER_CALLBACK, ['options' => 'trim']);
	}
}
