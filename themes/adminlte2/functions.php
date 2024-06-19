<?php defined('BASEPATH') OR exit('No direct script access allowed');

if ( ! function_exists('theme_is_home'))
{
	/**
	 * Theme Home Page
	 *
	 * Checks whenever current page is home (default_controller).
	 *
	 * @return bool
	 */
	function theme_is_home()
	{
		$CI =& get_instance();

		$class = strtolower($CI->router->class);
		$method = strtolower($CI->router->method);

		return ($class === $CI->router->default_controller && $method === 'index');
	}
}

// --------------------------------------------------------------------

if ( ! function_exists('theme_declension'))
{
	/**
	 * Theme Declension
	 *
	 * Theme declination of nouns by numerical basis
	 *
	 * @param  int   $number
	 * @param  array $words
	 * @return string
	 */
	function theme_declension($number, $words)
	{
		$number = abs($number) % 100;

		if ($number > 20) $number %= 10;
		if ($number == 1) return $words[0];
		if ($number >= 2 && $number <= 4) return $words[1];

		return $words[2];
	}
}

// --------------------------------------------------------------------

if ( ! function_exists('theme_noimage'))
{
	/**
	 * Return 'noimage' file url if exists
	 *
	 * @return mixed
	 */
	function theme_noimage()
	{
		$CI =& get_instance();

		$uri = 'assets/images/noimage.jpg';
		if (is_file($CI->theme->get_path($uri)))
		{
			return $CI->theme->get_url($uri);
		}

		return NULL;
	}
}
