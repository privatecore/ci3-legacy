<?php defined('BASEPATH') OR exit('No direct script access allowed');

if ( ! function_exists('cookie_utm'))
{
	/**
	 * Save utm parameters from URL into cookies with defined lifetime
	 *
	 * @see https://support.google.com/analytics/answer/1033863
	 * @return void
	 */
	function cookie_utm()
	{
		if (is_cli())
		{
			return;
		}

		// get CI class instance
		$CI =& get_instance();

		$input_get = (array) $CI->input->get();
		$input_get = array_intersect_key($input_get, array_flip(['utm_source', 'utm_medium', 'utm_campaign', 'utm_content']));

		$cookie_prefix = $CI->config->item('cookie_prefix');
		foreach ($input_get as $key => $value)
		{
			if ($CI->input->cookie($cookie_prefix.$key))
			{
				continue;
			}

			$CI->input->set_cookie($cookie_prefix.$key, $value, $CI->config->item('sess_expiration'));
		}
	}
}
