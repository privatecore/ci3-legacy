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

if ( ! function_exists('theme_video'))
{
	/**
	 * Get youtube video ID from URL and return embedded URL
	 *
	 * @param  string $url
	 * @return string
	 */
	function theme_video($url)
	{
		$url = urldecode(rawurldecode($url));

		$pattern =
			'%^# Match any youtube URL
			(?:https?://)?    # Optional scheme. Either http or https
			(?:www\.)?        # Optional www subdomain
			(?:               # Group host alternatives
			  youtu\.be/      # Either youtu.be,
			| youtube\.com    # or youtube.com
				(?:           # Group path alternatives
				  /embed/     # Either /embed/
				| /v/         # or /v/
				| /watch\?v=  # or /watch\?v=
				)             # End path alternatives.
			)                 # End host alternatives.
			([\w-]{10,12})    # Allow 10-12 for 11 char youtube id.
			$%x'
		;
		preg_match($pattern, $url, $matches);
		if (isset($matches[1]) && $matches[1])
		{
			$url = "https://www.youtube.com/embed/{$matches[1]}";
		}

		return $url;
	}
}
