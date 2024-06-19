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
				if (is_null($value) OR is_array($value) OR is_object($value))
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

if ( ! function_exists('array_key_first'))
{
	/**
	 * Array Key First
	 *
	 * Get the first key of the given array without affecting the internal array
	 * pointer. Fallback implementation for PHP < 7.3.0.
	 *
	 * @param  array $array
	 * @return mixed
	 */
	function array_key_first(array $array)
	{
		if ($array)
		{
			foreach ($array as $key => $unused)
			{
				return $key;
			}
		}

		return NULL;
	}
}

// --------------------------------------------------------------------

if ( ! function_exists('array_key_last'))
{
	/**
	 * Array Key Last
	 *
	 * Get the last key of the given array without affecting the internal array
	 * pointer. Fallback implementation for PHP < 7.3.0.
	 *
	 * @param  array $array
	 * @return mixed
	 */
	function array_key_last(array $array)
	{
		if ($array)
		{
			return key(array_slice($array, -1, 1, TRUE));
		}

		return NULL;
	}
}

// --------------------------------------------------------------------

if ( ! function_exists('array_is_list'))
{
	/**
	 * Determines if the given array is a list. An array is considered a list
	 * if its keys consist of consecutive numbers from 0 to count($array)-1.
	 * Returns true if array is a list, false otherwise.
	 *
	 * @param  array $array
	 * @return bool
	 */
	function array_is_list(array $array)
	{
		$i = 0;
		foreach ($array as $key => $value)
		{
			if ($key !== $i++) return FALSE;
		}

		return TRUE;
	}
}
