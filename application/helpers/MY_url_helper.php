<?php defined('BASEPATH') OR exit('No direct script access allowed');

if ( ! function_exists('url_path'))
{
	/**
	 * Get URL Path
	 *
	 * Parse URL string by PHP_URL_PATH and return path array.
	 *
	 * @param  string $str
	 * @return mixed
	 */
	function url_path($str)
	{
		if (empty($str))
		{
			return FALSE;
		}

		if (($path = @parse_url($str, PHP_URL_PATH)) !== FALSE)
		{
			$path = trim($path, '/');
			$path = explode('/', $path);
		}

		return $path;
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('clean_url'))
{
	/**
	 * Clean URL
	 *
	 * Remove query string parameters from an URL.
	 *
	 * @param  string $str
	 * @return string
	 */
	function clean_url($str)
	{
		if (empty($str))
		{
			return '';
		}

		$parts = parse_url($str);
		$uri = isset($parts['path']) ? $parts['path'] : '';

		return $parts['scheme'] . '://' . $parts['host'] . $uri;
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('base_domain'))
{
	/**
	 * Base Domain
	 *
	 * Return the domain name only based on the "base_url" item from your
	 * config file.
	 *
	 * @return string
	 */
	function base_domain()
	{
		$CI =& get_instance();
		return preg_replace('/^[\w]{2,6}:\/\/([\w\d\.\-]+).*$/', '$1', $CI->config->slash_item('base_url'));
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('url_alias'))
{
	/**
	 * Create URL Alias
	 * Takes a "str" string as input and creates a
	 * human-friendly alias string with a "separator" string
	 * as the word separator.
	 *
	 * @param  string $str       Input string
	 * @param  string $separator Word separator (usually '-' or '_')
	 * @param  bool   $ascii     Whether to convert accented foreign characters to ASCII
	 * @return string
	 */
	function url_alias($str, $separator = '-', $ascii = TRUE)
	{
		if ($ascii)
		{
			if ( ! function_exists('convert_accented_characters'))
			{
				// get CI class instance
				$CI = get_instance();

				$CI->load->helper('text');
			}

			$str = convert_accented_characters($str);
		}

		return url_title($str, $separator, TRUE);
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('urlencode_rfc3986'))
{
	/**
	 * Fix Urlencode RFC3986
	 *
	 * Simultaneously, the IETF published the content of RFC 3986 as the full standard
	 * STD 66, reflecting the establishment of the URI generic syntax as an official
	 * Internet protocol.
	 *
	 * @see https://tools.ietf.org/html/rfc3986#section-2.2
	 *
	 * @param  string $str
	 * @return string
	 */
	function urlencode_rfc3986($str)
	{
		$entities = ['%21', '%2A', '%27', '%28', '%29', '%3B', '%3A', '%40', '%26', '%3D', '%2B', '%24', '%2C', '%2F', '%3F', '%23', '%5B', '%5D'];
		$replacements = ["!", "*", "'", "(", ")", ";", ":", "@", "&", "=", "+", "$", ",", "/", "?", "#", "[", "]"];

		return str_replace($entities, $replacements, $str);
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('http_build_query_rfc3986'))
{
	/**
	 * Generate URL-encoded query string RFC3986
	 *
	 * Generates a URL-encoded query string from the associative (or indexed) array
	 * provided. PHP_QUERY_RFC3986 not handles it right and keeps encoding reserved
	 * characters into percent-encoded octet.
	 *
	 * @param  mixed $query_data
	 * @return string
	 */
	function http_build_query_rfc3986($query_data)
	{
		return urlencode_rfc3986(http_build_query($query_data));
	}
}
