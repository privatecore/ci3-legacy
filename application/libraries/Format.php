<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Format class
 *
 * Help to convert between various formats such as XML, JSON, CSV, etc.
 *
 * @author Phil Sturgeon, Chris Kacerguis, @softwarespot
 * @license https://dbad-license.org/
 */
class Format {

	/**
	 * CI Singleton
	 *
	 * @var	object
	 */
	protected $_CI;

	/**
	 * Data to parse
	 *
	 * @var mixed
	 */
	protected $_data = [];

	/**
	 * Type to convert from
	 *
	 * @var string
	 */
	protected $_from_type = NULL;

	// --------------------------------------------------------------------

	/**
	 * DO NOT CALL THIS DIRECTLY, USE factory()
	 *
	 * @param  mixed $data
	 * @param  mixed $from_type
	 * @throws Exception
	 */
	public function __construct($data = NULL, $from_type = NULL)
	{
		$this->_CI =& get_instance();

		// if the provided data is already formatted we should probably
		// convert it to an array
		if ( ! is_null($from_type))
		{
			if (method_exists($this, '_from_'.$from_type))
			{
				$data = call_user_func([$this, '_from_'.$from_type], $data);
			}
			else
			{
				throw new Exception("Format class does not support conversion from '{$from_type}'.");
			}
		}

		// set the member variable to the data passed
		$this->_data = $data;
	}

	// --------------------------------------------------------------------

	/**
	 * Create an instance of the format class
	 * e.g: echo $this->format->factory(['foo' => 'bar'])->to_csv();
	 *
	 * @param  mixed  $data      Data to convert/parse
	 * @param  string $from_type Type to convert from e.g. json, csv, html
	 * @return object            Instance of the format class
	 */
	public function factory($data, $from_type = NULL)
	{
		// $class = __CLASS__;
		// return new $class();

		return new static($data, $from_type);
	}

	// FORMATTING OUTPUT --------------------------------------------------

	/**
	 * Format data as an array
	 *
	 * @param  mixed $data Optional data to pass, so as to override the data
	 *                     passed to the constructor
	 * @return array Data parsed as an array; otherwise, an empty array
	 */
	public function to_array($data = NULL)
	{
		// if no data is passed as a parameter, then use the data passed
		// via the constructor
		if (is_null($data))
		{
			$data = $this->_data;
		}

		is_array($data) OR $data = (array) $data;

		$array = [];
		foreach ((array) $data as $key => $value)
		{
			if (is_object($value) OR is_array($value))
			{
				$array[$key] = $this->to_array($value);
				continue;
			}

			$array[$key] = $value;
		}

		return $array;
	}

	// --------------------------------------------------------------------

	/**
	 * Format data as XML
	 *
	 * @param  mixed  $data      Optional data to pass, so as to override the
	 *                           data passed to the constructor
	 * @param  string $basenode
	 * @param  mixed  $structure
	 * @return mixed
	 */
	public function to_xml($data = NULL, $basenode = 'xml', $structure = NULL)
	{
		if (is_null($data))
		{
			$data = $this->_data;
		}

		(is_array($data) OR is_object($data)) OR $data = (array) $data;

		if (is_null($structure))
		{
			$structure = simplexml_load_string('<?xml version="1.0" encoding="UTF-8"?><'.$basenode.' />');
		}

		// load the inflector helper
		$this->_CI->load->helper('inflector');

		foreach ($data as $key => $value)
		{
			// change TRUE/FALSE to 1/0
			if (is_bool($value))
			{
				$value = (int) $value;
			}

			// no numeric keys in our xml please!
			if (is_numeric($key))
			{
				// make string key...
				$key = (singular($basenode) != $basenode) ? singular($basenode) : 'item';
			}

			// replace anything not alpha numeric
			$key = preg_replace('/[^a-z_\-0-9]/i', '', $key);

			if ($key === '_attributes' && (is_array($value) OR is_object($value)))
			{
				$attributes = $value;
				if (is_object($attributes))
				{
					$attributes = get_object_vars($attributes);
				}

				foreach ($attributes as $attribute_name => $attribute_value)
				{
					$structure->addAttribute($attribute_name, $attribute_value);
				}
			}
			// if there is another array found recursively call this function
			elseif (is_array($value) OR is_object($value))
			{
				$node = $structure->addChild($key);

				// recursive call
				$this->to_xml($value, $key, $node);
			}
			else
			{
				// add single node
				$value = htmlspecialchars(html_entity_decode($value, ENT_QUOTES, 'UTF-8'), ENT_QUOTES, 'UTF-8');

				$structure->addChild($key, $value);
			}
		}

		return $structure->asXML();
	}

	// --------------------------------------------------------------------

	/**
	 * Format data as HTML
	 *
	 * @param  mixed $data Optional data to pass, so as to override the data
	 *                     passed to the constructor
	 * @return mixed
	 */
	public function to_html($data = NULL)
	{
		// if no data is passed as a parameter, then use the data passed
		// via the constructor
		if (is_null($data))
		{
			$data = $this->_data;
		}

		// load the array helper
		$this->_CI->load->helper('array');

		is_array($data) OR $data = (array) $data;
		if (count(array_filter($data, 'is_array')) == 0)
		{
			$data = array($data);
		}

		$headings = array_keys($data[0]);

		// load the table library
		$this->_CI->load->library('table');

		$this->_CI->table->set_heading($headings);

		foreach ($data as $row)
		{
			// suppressing the "array to string conversion" notice
			$row = @array_map('strval', $row);

			$this->_CI->table->add_row($row);
		}

		return $this->_CI->table->generate();
	}

	// --------------------------------------------------------------------

	/**
	 * @link http://www.metashock.de/2014/02/create-csv-file-in-memory-php/
	 * @param  mixed  $data      Optional data to pass, so as to override the
	 *                           data passed to the constructor
	 * @param  string $delimiter The optional delimiter parameter sets the field
	 *                           delimiter (one character only). NULL will use
	 *                           the default value (,)
	 * @param  string $enclosure The optional enclosure parameter sets the field
	 *                           enclosure (one character only). NULL will use
	 *                           the default value (")
	 * @return string A csv string
	 */
	public function to_csv($data = NULL, $delimiter = ',', $enclosure = '"')
	{
		// use a threshold of 1 MB (1024 * 1024)
		if (($handle = fopen('php://temp/maxmemory:1048576', 'w')) === FALSE)
		{
			return NULL;
		}

		// if no data is passed as a parameter, then use the data passed
		// via the constructor
		if (is_null($data))
		{
			$data = $this->_data;
		}

		// load the array helper
		$this->_CI->load->helper('array');

		is_array($data) OR $data = (array) $data;
		if (count(array_filter($data, 'is_array')) == 0)
		{
			$data = array($data);
		}

		$headings = array_keys($data[0]);

		is_null($delimiter) && $delimiter = ',';
		is_null($enclosure) && $enclosure = '"';

		// apply the headings
		fputcsv($handle, $headings, $delimiter, $enclosure);

		foreach ($data as $record)
		{
			// if the record is not an array, then break. This is because the
			// 2nd param of fputcsv() should be an array.
			if ( ! is_array($record))
			{
				break;
			}

			// suppressing the "array to string conversion" notice
			$record = @array_map('strval', $record);

			// returns the length of the string written or FALSE
			fputcsv($handle, $record, $delimiter, $enclosure);
		}

		// reset the file pointer
		rewind($handle);

		// retrieve the csv contents
		$csv = stream_get_contents($handle);

		// close the handle
		fclose($handle);

		return $csv;
	}

	// --------------------------------------------------------------------

	/**
	 * Encode data as JSON
	 *
	 * @param  mixed $data Optional data to pass, so as to override the data
	 *                     passed to the constructorbasenode
	 * @return string JSON representation of a value
	 */
	public function to_json($data = NULL)
	{
		// if no data is passed as a parameter, then use the data passed
		// via the constructor
		if (is_null($data))
		{
			$data = $this->_data;
		}

		// get the callback parameter (if set)
		$callback = $this->_CI->input->get('callback');

		if (empty($callback))
		{
			return json_encode($data);
		}
		// we only honour a jsonp callback which are valid javascript identifiers
		elseif (preg_match('/^[a-z_\$][a-z0-9\$_]*(\.[a-z_\$][a-z0-9\$_]*)*$/i', $callback))
		{
			// return the data as encoded json with a callback
			return $callback.'('.json_encode($data).');';
		}

		// an invalid jsonp callback function provided, though I don't believe
		// this should be hardcoded here
		$data['warning'] = 'INVALID JSONP CALLBACK: '.$callback;

		return json_encode($data);
	}

	// --------------------------------------------------------------------

	/**
	 * Encode data as a serialized array
	 *
	 * @param  mixed $data Optional data to pass, so as to override the data
	 *                     passed to the constructor
	 * @return string Serialized data
	 */
	public function to_serialized($data = NULL)
	{
		// if no data is passed as a parameter, then use the data passed
		// via the constructor
		if (is_null($data))
		{
			$data = $this->_data;
		}

		return serialize($data);
	}

	// --------------------------------------------------------------------

	/**
	 * Format data using a PHP structure
	 *
	 * @param  mixed $data Optional data to pass, so as to override the data
	 *                     passed to the constructor
	 * @return mixed String representation of a variable
	 */
	public function to_php($data = NULL)
	{
		// if no data is passed as a parameter, then use the data passed
		// via the constructor
		if (is_null($data))
		{
			$data = $this->_data;
		}

		return var_export($data, TRUE);
	}

	// INTERNAL FUNCTIONS -------------------------------------------------

	/**
	 * @param  string $data XML string
	 * @return array XML element object; otherwise, empty array
	 */
	protected function _from_xml($data)
	{
		return $data ? (array) simplexml_load_string($data, 'SimpleXMLElement', LIBXML_NOCDATA) : [];
	}

	// --------------------------------------------------------------------

	/**
	 * @param  string $data      CSV string
	 * @param  string $delimiter The optional delimiter parameter sets the field
	 *                           delimiter (one character only). NULL will use
	 *                           the default value (,)
	 * @param  string $enclosure The optional enclosure parameter sets the field
	 *                           enclosure (one character only). NULL will use
	 *                           the default value (")
	 * @return array A multi-dimensional array with the outer array being the
	 *               number of rows and the inner arrays the individual fields
	 */
	protected function _from_csv($data, $delimiter = ',', $enclosure = '"')
	{
		is_null($delimiter) && $delimiter = ',';
		is_null($enclosure) && $enclosure = '"';

		return str_getcsv($data, $delimiter, $enclosure);
	}

	// --------------------------------------------------------------------

	/**
	 * @param  string $data Encoded json string
	 * @return mixed Decoded json string with leading and trailing whitespace removed
	 */
	protected function _from_json($data)
	{
		return json_decode(trim($data), TRUE);
	}

	// --------------------------------------------------------------------

	/**
	 * @param  string $data Data to unserialize
	 * @return mixed Unserialized data
	 */
	protected function _from_serialize($data)
	{
		return unserialize(trim($data));
	}

	// --------------------------------------------------------------------

	/**
	 * @param  string $data Data to trim leading and trailing whitespace
	 * @return string Data with leading and trailing whitespace removed
	 */
	protected function _from_php($data)
	{
		return trim($data);
	}

}
