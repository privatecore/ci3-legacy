<?php defined('BASEPATH') OR exit('No direct script access allowed');

if ( ! function_exists('sprintf_assoc'))
{
	/**
	 * Functions vsprintf, sprintf, and printf do not allow for associative arrays
	 * to perform replacements, `sprintf_assoc` resolves this by using the key
	 * of the array in the lookup for string replacement.
	 * http://php.net/manual/en/function.vsprintf.php
	 *
	 * @param  string $str     Value of the string needs to be formatted
	 * @param  array  $vars    Associative array with values needs to be replaced
	 * @param  string $prefix  Prefix character for pseudo vars
	 * @param  string $postfix Postfix character for pseudo vars
	 * @return string
	 */
	function sprintf_assoc($str = '', $vars = [], $prefix = '{', $postfix = '}')
	{
		if (empty($str))
		{
			return '';
		}

		if (count($vars))
		{
			foreach ($vars as $key => $value)
			{
				if (is_array($value) OR is_object($value))
				{
					continue;
				}

				$str = str_replace($prefix.$key.$postfix, $value, $str);
			}
		}

		return $str;
	}
}

// --------------------------------------------------------------------

if ( ! function_exists('str_contains'))
{
	/**
	 * Determine if a string contains a given substring. Fallback implementation
	 * for PHP < 8.0.0.
	 *
	 * @param  string $haystack
	 * @param  string $needle
	 * @return bool
	 */
	function str_contains($haystack, $needle)
	{
		return (strpos($haystack, $needle) !== FALSE);
	}
}

// --------------------------------------------------------------------

if ( ! function_exists('phone_clean'))
{
	/**
	 * Cleanup telephone number from all symbols except numbers and +
	 * ex.: +7 ### ###-##-## -> 7##########
	 *
	 * @param  string $str
	 * @return string
	 */
	function phone_clean($str)
	{
		return preg_replace('/[^0-9]/', '', $str);
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('phone_format'))
{
	/**
	 * Formats telephone number to fit desired format. By default is used
	 * mixed format: +7 ### ###-##-## and 8 ### ###-##-##.
	 *
	 * @param  string $str
	 * @param  string $format
	 * @return string
	 */
	function phone_format($str, $format = 'mixed')
	{
		$str = phone_clean($str);
		if (empty($str))
		{
			return '';
		}

		switch ($format)
		{
			case 'e164':
				$str = strpos($str, '8') === 0 ? '7'.substr($str, 1, -1) : $str;
				$result = '+'.$str;
				break;
			case 'international':
				$str = strpos($str, '8') === 0 ? '7'.substr($str, 1, -1) : $str;
				$result = preg_replace('/(\d{1})(\d{3})(\d{3})(\d{2})(\d{2})/', '+$1 $2 $3-$4-$5', $str);
				break;
			break;
			case 'national':
				$str = strpos($str, '8') !== 0 ? '8'.substr($str, 1, -1) : $str;
				$result = preg_replace('/(\d{1})(\d{3})(\d{3})(\d{2})(\d{2})/', '$1 $2 $3-$4-$5', $str);
				break;
			default:
				$prefix = strpos($str, '8') !== 0 ? '+' : '';
				$result = $prefix.preg_replace('/(\d{1})(\d{3})(\d{3})(\d{2})(\d{2})/', '$1 $2 $3-$4-$5', $str);
				break;
		}

		return $result;
	}
}
