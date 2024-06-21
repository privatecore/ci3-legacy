<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Errors class
 */
class Errors extends Public_Controller {

	/**
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct();
	}

	// --------------------------------------------------------------------

	/**
	 * Output 404 error
	 */
	public function error_404()
	{
		$this->show_error(HTTP_NOT_FOUND);
	}

}
