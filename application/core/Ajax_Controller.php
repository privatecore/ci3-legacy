<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Ajax_Controller class
 *
 * Should be used as parent class for all AJAX related classes.
 */
class Ajax_Controller extends MY_Controller {

	/**
	 * Status and Message field names used for JSON response
	 *
	 * @var string
	 */
	protected $status_field = 'status';
	protected $message_field = 'message';

	// --------------------------------------------------------------------

	/**
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct();

		// must use an AJAX request
		if ($this->input->is_ajax_request() === FALSE)
		{
			$this->json_response(lang('core_error_ajax_only'), HTTP_METHOD_NOT_ALLOWED);
		}

		// load the public language file
		$this->lang->load('public');
	}

	// --------------------------------------------------------------------

	/**
	 * JSON Encoded Response
	 *
	 * @param  mixed $data
	 * @param  int   $status_code
	 * @return void
	 */
	public function json_response($data, $status_code = 200)
	{
		$this->output->set_header('Cache-Control: no-cache, must-revalidate');
		$this->output->set_header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
		$this->output->set_content_type('application/json', 'utf-8');
		$this->output->set_status_header($status_code);

		if (is_string($data))
		{
			$data = [$this->message_field => $data];
		}

		// return status FALSE only for: Client and Server error responses,
		// HTTP status codes: 400 - 499 and 500 - 599, respectively
		$data[$this->status_field] = $status_code < 400;

		// display the data and exit execution
		$this->output->_display(json_encode($data));
		exit;
	}

}
