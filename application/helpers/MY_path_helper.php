<?php defined('BASEPATH') OR exit('No direct script access allowed');

if ( ! function_exists('get_relpath'))
{
	/**
	 * get_relpath
	 *
	 * Return a relative path to a file or directory using base directory.
	 * When you set $base to /website and $path to /website/store/library.php
	 * this function will return /store/library.php
	 *
	 * Remember: All paths have to start from "/" or "\" this is not Windows
	 * compatible.
	 *
	 * @return string
	 */
	function get_relpath($base, $path)
	{
		// detect directory separator
		$separator = substr($base, 0, 1);
		$_base = array_slice(explode($separator, rtrim($base, $separator)), 1);
		$_path = array_slice(explode($separator, rtrim($path, $separator)), 1);

		return $separator.implode($separator, array_slice($_path, count($_base)))
			.(is_dir($path) ? $separator : '');
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('get_abspath'))
{
	/**
	 * get_abspath
	 *
	 * Return an absolute path to a file or directory using base directory.
	 * When you set $base to /var/www/website and $path to /store/library.php
	 * this function will return /var/www/website/store/library.php
	 *
	 * Remember: All paths have to start from "/" or "\" this is not Windows
	 * compatible.
	 *
	 * @return string
	 */
	function get_abspath($base, $path)
	{
		// detect directory separator
		$separator = substr($base, 0, 1);
		$_path = array_slice(explode($separator, rtrim($path, $separator)), 1);

		return rtrim($base, $separator).$separator.implode($separator, $_path)
			.(is_dir($path) ? $separator : '');
	}
}
