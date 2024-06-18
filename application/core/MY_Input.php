<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * MY_Input class
 */
class MY_Input extends CI_Input {

	/**
	 * Class constructor
	 *
	 * Determines whether to globally enable the XSS processing
	 * and whether to allow the $_GET array.
	 *
	 * @return	void
	 */
	public function __construct()
	{
		parent::__construct();
	}

	// --------------------------------------------------------------------

	/**
	 * Fetch an item from FILES data with fallback to POST
	 *
	 * @param	string	$index		Index for item to be fetched from $_FILES
	 * @param	bool	$xss_clean	Whether to apply XSS filtering
	 * @return	mixed
	 */
	public function files($index, $xss_clean = NULL)
	{
		return $this->_fetch_from_array($_FILES, $index, $xss_clean);
	}

}
