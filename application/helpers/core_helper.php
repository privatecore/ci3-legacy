<?php defined('BASEPATH') OR exit('No direct script access allowed');

if ( ! function_exists('display_json'))
{
	/**
	 * Outputs an array in a user-readable JSON format
	 *
	 * @param array $array
	 */
	function display_json($array)
	{
		$data = json_indent($array);

		header('Cache-Control: no-cache, must-revalidate');
		header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
		header('Content-type: application/json');

		echo $data;
	}
}

// --------------------------------------------------------------------

if ( ! function_exists('json_indent'))
{
	/**
	 * Convert an array to a user-readable JSON string
	 *
	 * @param  array  $array The original array to convert to JSON
	 * @return string        Friendly formatted JSON string
	 */
	function json_indent($array = [])
	{
		// make sure array is provided
		if (empty($array))
		{
			return null;
		}

		//Encode the string
		$json = json_encode($array);

		$result = '';
		$pos = 0;
		$str_len = strlen($json);
		$indent_str = '  ';
		$new_line = "\n";
		$prev_char = '';
		$out_of_quotes = TRUE;

		for ($i = 0; $i <= $str_len; $i++)
		{
			// grab the next character in the string
			$char = substr($json, $i, 1);

			// are we inside a quoted string?
			if ($char == '"' && $prev_char != '\\')
			{
				$out_of_quotes = ! $out_of_quotes;
			}
			// if this character is the end of an element, output a new line and indent the next line
			elseif (($char == '}' OR $char == ']') && $out_of_quotes)
			{
				$result .= $new_line;
				$pos--;

				for ($j = 0; $j < $pos; $j++)
				{
					$result .= $indent_str;
				}
			}

			// add the character to the result string
			$result .= $char;

			// if the last character was the beginning of an element, output a new line and indent the next line
			if (($char == ',' OR $char == '{' OR $char == '[') && $out_of_quotes)
			{
				$result .= $new_line;

				if ($char == '{' OR $char == '[')
				{
					$pos++;
				}

				for ($j = 0; $j < $pos; $j++)
				{
					$result .= $indent_str;
				}
			}

			$prev_char = $char;
		}

		// return result
		return $result . $new_line;
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('array_to_csv'))
{
	/**
	 * Save array data to a CSV file
	 *
	 * @param  array  $array
	 * @param  string $filename
	 * @return void
	 */
	function array_to_csv($array = [], $filename = 'export.csv')
	{
		// disable the profiler otherwise header errors will occur
		get_instance()->output->enable_profiler(FALSE);

		if ($array)
		{
			try
			{
				// set the headers for file download
				header('Content-type: text/csv');
				header('Content-Description: File Transfer');
				header('Content-Disposition: attachment; filename="'.$filename.'";');
				header('Expires: 0');
				header('Content-Transfer-Encoding: binary');
				header('Cache-Control: private, no-transform, no-store, must-revalidate');

				$output = @fopen('php://output', 'w');

				// used to determine header row
				$header_displayed = FALSE;
				foreach ($array as $row)
				{
					if ( ! $header_displayed)
					{
						// use the array keys as the header row
						fputcsv($output, array_keys($row));
						$header_displayed = TRUE;
					}

					$row = array_map(function($value) {
						is_array($value) && $value = implode(PHP_EOL, $value);
						return $value;
					}, $row);

					// insert the data
					fputcsv($output, $row);
				}

				fclose($output);
			}
			catch (Exception $e) {}
		}

		exit;
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('has_children'))
{
	/**
	 * Check if tree has at least one child with id
	 *
	 * @param  array $tree
	 * @param  int   $id
	 * @return bool
	 */
	function has_children($tree, $id)
	{
		foreach ($tree as $item)
		{
			if ($item['parent_id'] == $id)
			{
				return TRUE;
			}
		}

		return FALSE;
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('format_tree'))
{
	/**
	 * Format tree array to make it usable as options
	 *
	 * @param  array  $tree
	 * @param  string $primary_key
	 * @param  int    $parent
	 * @return array
	 */
	function format_tree($tree, $primary_key, $parent = 0)
	{
		$result = [];

		foreach ($tree as $item)
		{
			if ($item['parent_id'] == $parent)
			{
				$result[$item[$primary_key]] = $item;

				if (has_children($tree, $item[$primary_key]))
				{
					$nodes = format_tree($tree, $primary_key, $item[$primary_key]);
					$result = $result + $nodes;
				}
			}
		}

		return $result;
	}
}
