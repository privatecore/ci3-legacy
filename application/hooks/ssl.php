<?php defined('BASEPATH') OR exit('No direct script access allowed');

if ( ! function_exists('force_ssl'))
{
	/**
	 * Force secure SSL connection
	 *
	 * @return void
	 */
	function force_ssl()
	{
		if (is_cli())
		{
			return;
		}

		if ( ! is_https())
		{
			// get CI class instance
			$CI =& get_instance();

			if ( ! function_exists('redirect'))
			{
				// load url helper
				$CI->load->helper('url');
			}

			redirect($CI->uri->uri_string());
		}
	}
}
