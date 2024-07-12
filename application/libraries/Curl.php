<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Curl class
 *
 * CodeIgniter library which makes it easy to do simple cURL requests and
 * makes more complicated cURL requests easier too.
 *
 * @author Philip Sturgeon
 * @license https://dbad-license.org/
 */
class Curl {

	/**
	 * CI Singleton
	 *
	 * @var	object
	 */
	protected $_CI;

	/**
	 * Error code returned as an int
	 *
	 * @var int
	 */
	public $error_code;

	/**
	 * Error message returned as a string
	 *
	 * @var string
	 */
	public $error_string;

	/**
	 * Returned after request (elapsed time, etc)
	 *
	 * @var mixed
	 */
	public $info;

	/**
	 * Contains the cURL response for debug
	 *
	 * @var string|bool
	 */
	public $response;

	// --------------------------------------------------------------------

	/**
	 * Populates extra HTTP headers
	 *
	 * @var array
	 */
	protected $_headers = [];

	/**
	 * Populates curl_setopt_array
	 *
	 * @var array
	 */
	protected $_options = [];

	/**
	 * List all supported methods, the first will be the default format
	 *
	 * @var array
	 */
	protected $_supported_formats = [
		'json'       => ['application/json', 'application/hal+json', 'application/problem+json'],
		'array'      => 'application/json',
		'csv'        => 'text/csv',
		'html'       => 'text/html',
		'jsonp'      => 'application/javascript',
		'php'        => 'text/plain',
		'serialized' => 'application/vnd.php.serialized',
		'xml'        => ['text/xml', 'application/xml'],
	];

	// --------------------------------------------------------------------

	/**
	 * Constructor
	 */
	public function __construct()
	{
		if ( ! function_exists('curl_init'))
		{
			show_error('PHP was not built with cURL enabled. Rebuild PHP with --with-curl flag.');
		}

		$this->_CI =& get_instance();

		log_message('debug', 'Curl Class Initialized');
	}

	// --------------------------------------------------------------------

	/**
	 * COMMON METHODS
	 *
	 * Use these methods to build up common request queries: get, post, put,
	 * patch, delete.
	 */

	// --------------------------------------------------------------------

	/**
	 * get
	 *
	 * @param  array|string $params
	 * @return $this
	 */
	public function get($url, $params = [])
	{
		// if its an array (instead of a query string) then format it correctly
		if (is_array($params))
		{
			$params = http_build_query($params, '', '&');
		}

		return $this->request($url.'?'.$params);
	}

	// --------------------------------------------------------------------

	/**
	 * post
	 *
	 * @param  array|string $params
	 * @return $this
	 */
	public function post($url, $params = [])
	{
		// if its an array (instead of a query string) then format it correctly
		if (is_array($params))
		{
			$params = json_encode($params);
		}

		// add in the specific options provided
		$this->set_options(CURLOPT_POST, TRUE);
		$this->set_options(CURLOPT_POSTFIELDS, $params);
		$this->set_options(CURLOPT_CUSTOMREQUEST, 'POST');

		return $this->request($url);
	}

	// --------------------------------------------------------------------

	/**
	 * put
	 *
	 * @param  array|string $params
	 * @return $this
	 */
	public function put($url, $params = [])
	{
		// if its an array (instead of a query string) then format it correctly
		if (is_array($params))
		{
			$params = json_encode($params);
		}

		// add in the specific options provided
		$this->set_options(CURLOPT_POSTFIELDS, $params);
		$this->set_options(CURLOPT_CUSTOMREQUEST, 'PUT');

		// override method, I think this overrides $_POST with PUT data but... we'll see eh?
		$this->set_headers('X-HTTP-Method-Override', 'PUT');

		return $this->request($url);
	}

	// --------------------------------------------------------------------

	/**
	 * patch
	 *
	 * @param  array|string $params
	 * @return $this
	 */
	public function patch($url, $params = [])
	{
		// if its an array (instead of a query string) then format it correctly
		if (is_array($params))
		{
			$params = json_encode($params);
		}

		// add in the specific options provided
		$this->set_options(CURLOPT_POSTFIELDS, $params);
		$this->set_options(CURLOPT_CUSTOMREQUEST, 'PATCH');

		// override method, I think this overrides $_POST with PATCH data but... we'll see eh?
		$this->set_headers('X-HTTP-Method-Override', 'PATCH');

		return $this->request($url);
	}

	// --------------------------------------------------------------------

	/**
	 * delete
	 *
	 * @param  array|string $params
	 * @return $this
	 */
	public function delete($url, $params = [])
	{
		// if its an array (instead of a query string) then format it correctly
		if (is_array($params))
		{
			$params = json_encode($params);
		}

		// add in the specific options provided
		$this->set_options(CURLOPT_POSTFIELDS, $params);
		$this->set_options(CURLOPT_CUSTOMREQUEST, 'DELETE');

		// override method, I think this overrides $_POST with DELETE data but... we'll see eh?
		$this->set_headers('X-HTTP-Method-Override', 'DELETE');

		return $this->request($url);
	}

	// --------------------------------------------------------------------

	/**
	 * COMMON METHODS
	 *
	 * Used to customize cURL requests, set additional parameters, execute
	 * and debug queries.
	 */

	// --------------------------------------------------------------------

	/**
	 * set_cookie
	 *
	 * @param  mixed $data
	 * @return $this
	 */
	public function set_cookie($data = [])
	{
		if (is_array($data))
		{
			$data = http_build_query($data, '', '&');
		}

		$this->set_options(CURLOPT_COOKIE, $data);

		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * set_cookie_file
	 *
	 * @param  string $file
	 * @return $this
	 */
	public function set_cookie_file($file = '')
	{
		if ( ! is_writable(dirname($file)))
		{
			throw new Exception('Cookie file directory is not writable or not exists. Aborting.');
		}

		$this->set_options(CURLOPT_COOKIEFILE, $file);
		$this->set_options(CURLOPT_COOKIEJAR, $file);

		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * set_auth
	 *
	 * @param  string $username
	 * @param  string $password
	 * @param  string $type
	 * @return $this
	 */
	public function set_auth($username = '', $password = '', $type = 'any')
	{
		$this->set_options(CURLOPT_HTTPAUTH, constant('CURLAUTH_'.strtoupper($type)));
		$this->set_options(CURLOPT_USERPWD, $username.':'.$password);

		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * set_ssl
	 *
	 * @param  bool   $verify_peer
	 * @param  int    $verify_host
	 * @param  string $cainfo
	 * @return $this
	 */
	public function set_ssl($verify_peer = TRUE, $verify_host = 2, $cainfo = '')
	{
		$this->set_options(CURLOPT_SSL_VERIFYPEER, $verify_peer);
		$this->set_options(CURLOPT_SSL_VERIFYHOST, $verify_host);

		if ($verify_peer && $cainfo)
		{
			$this->set_options(CURLOPT_CAINFO, realpath($cainfo));
		}

		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * set_mime
	 *
	 * @param  string $format
	 * @return $this
	 */
	public function set_mime($format = 'json')
	{
		$mime_type = array_key_exists($format, $this->_supported_formats)
			? (is_array($this->_supported_formats[$format]) ? $this->_supported_formats[$format][0] : $this->_supported_formats[$format])
			: $format;

		$this->set_headers('Accept', $mime_type);
		$this->set_headers('Content-type', $mime_type);

		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * set_headers
	 *
	 * @param  string $header
	 * @param  string $content
	 * @return $this
	 */
	public function set_headers($header, $content = '')
	{
		$this->_headers[] = $content ? $header.': '.$content : $header;

		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * options
	 *
	 * @param  mixed $data
	 * @param  mixed $value
	 * @return $this
	 */
	public function set_options($data, $value = NULL)
	{
		if (is_array($data))
		{
			// merge options in with the rest - done as array_merge() does not
			// overwrite numeric keys
			foreach ($data as $option_code => $option_value)
			{
				$this->_options[$option_code] = $option_value;
			}
		}
		else
		{
			if (is_string($data) && ! is_numeric($data))
			{
				$data = constant('CURLOPT_'.strtoupper($data));
			}

			$this->_options[$data] = $value;
		}

		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Execute the cURL query and return the response
	 *
	 * @param  string $url
	 * @return $this
	 */
	public function request($url)
	{
		$ch = curl_init($url);

		if ( ! isset($this->_options[CURLOPT_TIMEOUT]))
		{
			$this->_options[CURLOPT_TIMEOUT] = 30;
		}
		if ( ! isset($this->_options[CURLOPT_RETURNTRANSFER]))
		{
			$this->_options[CURLOPT_RETURNTRANSFER] = TRUE;
		}
		if ( ! isset($this->_options[CURLOPT_FAILONERROR]))
		{
			$this->_options[CURLOPT_FAILONERROR] = FALSE;
		}

		if ($this->_headers)
		{
			$this->_options[CURLOPT_HTTPHEADER] = $this->_headers;
		}

		// set all options provided
		curl_setopt_array($ch, $this->_options);

		// execute the request & and hide all output
		if (($this->response = curl_exec($ch)) === FALSE)
		{
			$this->error_code = curl_errno($ch);
			$this->error_string = curl_error($ch);
		}

		$this->info = curl_getinfo($ch);

		curl_close($ch);
		$this->_reset();

		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * response
	 *
	 * @param  string $type
	 * @return mixed
	 */
	public function response($type = 'array')
	{
		if (is_string($this->response))
		{
			try
			{
				// load the format library
				$this->_CI->load->library('format');

				$format = $this->_detect_output_format();
				$output = $this->_CI->format->factory($this->response, $format)->{'to_'.$type}();
			}
			catch (Exception $e)
			{
				// format is not supported, so output the raw data as is
				$output = $this->response;
			}
		}
		else
		{
			$output = $this->response;
		}

		return $output;
	}

	// --------------------------------------------------------------------

	/**
	 * _detect_output_format
	 *
	 * @return mixed
	 */
	protected function _detect_output_format()
	{
		// find out what format the data was returned in
		$content_type = $this->info['content_type'] ?? NULL;

		if ( ! empty($content_type))
		{
			// If a semi-colon exists in the string, then explode by ; and get
			// the value of where the current array pointer resides. This will
			// generally be the first element of the array.
			if (strpos($content_type, ';') !== FALSE)
			{
				$content_type = current(explode(';', $content_type));
			}

			// check all formats against the CONTENT-TYPE header
			foreach ($this->_supported_formats as $type => $mime)
			{
				// $type = format e.g. csv
				// $mime = mime type e.g. application/csv

				// if both the mime types match, then return the format
				if ((is_array($mime) && in_array($content_type, $mime)) OR $content_type === $mime)
				{
					return $type;
				}
			}
		}

		return NULL;
	}

	// --------------------------------------------------------------------

	/**
	 * _reset
	 *
	 * @return void
	 */
	protected function _reset()
	{
		$this->_headers = $this->_options = [];
		$this->error_code = $this->error_string = NULL;
	}

}
