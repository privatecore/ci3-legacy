<?php defined('BASEPATH') OR exit('No direct script access allowed');

if ( ! function_exists('nice_date'))
{
	/**
	 * Turns many "reasonably-date-like" strings into something
	 * that is actually useful. This only works for dates after unix epoch.
	 *
	 * @param  string The terribly formatted date-like string
	 * @param  string Date format to return (same as php date function)
	 * @return string
	 */
	function nice_date($bad_date = '', $format = FALSE)
	{
		if (empty($bad_date))
		{
			return 'Unknown';
		}
		elseif (empty($format))
		{
			$format = 'U';
		}

		// Date like: YYYYMM
		if (preg_match('/^\d{6}$/i', $bad_date))
		{
			if (in_array(substr($bad_date, 0, 2), array('19', '20')))
			{
				$year = substr($bad_date, 0, 4);
				$month = substr($bad_date, 4, 2);
			}
			else
			{
				$month = substr($bad_date, 0, 2);
				$year = substr($bad_date, 2, 4);
			}

			return date($format, strtotime($year . '-' . $month . '-01'));
		}

		// Date Like: YYYYMMDD
		if (preg_match('/^\d{8}$/i', $bad_date, $matches))
		{
			return DateTime::createFromFormat('Ymd', $bad_date)->format($format);
		}

		// Date Like: MM-DD-YYYY __or__ M-D-YYYY (or anything in between)
		if (preg_match('/^(\d{1,2})-(\d{1,2})-(\d{4})$/i', $bad_date, $matches))
		{
			return date($format, strtotime($matches[3] . '-' . $matches[1] . '-' . $matches[2]));
		}

		// Any other kind of string, when converted into UNIX time,
		// produces "0 seconds after epoc..." is probably bad...
		// return "Invalid Date".
		if (date('U', strtotime($bad_date)) === '0')
		{
			return 'Invalid Date';
		}

		// It's probably a valid-ish date format already
		return date($format, strtotime($bad_date));
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('human_to_mysql'))
{
	/**
	 * Converts human readable to MySQL's datetime format.
	 *
	 * @param  string $str
	 * @return mixed
	 */
	function human_to_mysql($str = '')
	{
		if (empty($str))
		{
			return '0000-00-00 00:00:00';
		}

		// We don't check specified format for compatibility
		// Result: YYYY-MM-DD HH:MM:SS
		return date('Y-m-d H:i:s', strtotime($str));
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('mysql_to_human'))
{
	/**
	 * Converts MySQL's datetime format to "human".
	 *
	 * @param  string $str
	 * @param  bool   $format
	 * @return mixed
	 */
	function mysql_to_human($str = '', $format = FALSE)
	{
		if (empty($str))
		{
			return 'Unknown';
		}
		elseif (empty($format))
		{
			$format = 'U';
		}

		return date($format, strtotime($str));
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('unix_to_mysql'))
{
	/**
	 * Converts UNIX to MySQL's datetime format.
	 *
	 * @param  string $str
	 * @return mixed
	 */
	function unix_to_mysql($str = '')
	{
		if (empty($str))
		{
			return '0000-00-00 00:00:00';
		}

		return date('Y-m-d H:i:s', $str);
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('zero_date'))
{
	/**
	 * Check Date
	 *
	 * Checks for the 'zero date' (0000-00-00 00:00:00). Return TRUE if
	 * specified date is 'zero date', FALSE - otherwise.
	 *
	 * @param  string  $str
	 * @return bool
	 */
	function zero_date($str = '')
	{
		if (empty($str))
		{
			return TRUE;
		}

		$time = strtotime($str);

		return ( ! $time OR $time < 0);
	}
}
