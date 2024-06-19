<?php defined('BASEPATH') OR exit('No direct script access allowed');

if ( ! function_exists('jsi18n'))
{
	/**
	 * Parse through a JS file and replace language keys with language text
	 * values
	 *
	 * @param  string $file
	 * @return mixed
	 */
	function jsi18n($file)
	{
		if ( ! ($contents = @file_get_contents($file)))
		{
			return NULL;
		}

		// find all double braces {{...}}
		preg_match_all("/\{\{(.*?)\}\}/", $contents, $matches, PREG_PATTERN_ORDER);

		if ($matches)
		{
			foreach ($matches[1] as $match)
			{
				$lang_value = lang($match);

				// replace double braces with language text
				$contents = str_replace("{{{$match}}}", $lang_value, $contents);
			}
		}

		return $contents;
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('link_active'))
{
	/**
	 * Link Active
	 *
	 * Checks if link is active - current url partially or completely contains
	 * provided link - and set proper CSS style. Use predefined symbols for
	 * negative '!' and strict '@' comparison.
	 *
	 * @param  string $uri
	 * @param  string $style
	 * @return string
	 */
	function link_active($uri, $style = 'active')
	{
		$found = FALSE;

		// get CI class instance
		$CI =& get_instance();

		// get current uri string
		$current = $CI->uri->uri_string();
		if ( ! $current)
		{
			// default router method name
			$current = 'index';
		}

		foreach ((array) $uri as $value)
		{
			// remove leading and trailing slashes
			$value = trim($value, '/');

			// support for negative string comparison to exclude values from checks,
			// those string should be added at the begining of the array ($uri)
			if (strpos($value, '!') === 0)
			{
				if (strcmp($current, substr($value, 1)) === 0)
				{
					break;
				}
			}
			// support for strict string comparison checks use strcmp (binary safe)
			elseif (strpos($value, '@') === 0)
			{
				if (strcmp($current, substr($value, 1)) === 0)
				{
					$found = TRUE;
					break;
				}
			}
			elseif (strpos($current, $value) === 0)
			{
				$found = TRUE;
				break;
			}
		}

		return $found ? $style : '';
	}
}
