<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Ajax class
 *
 * All AJAX requests should go in here. CSRF protection has been disabled
 * for this controller in the config file. IMPORTANT! For retrieving data
 * only.
 */
class Ajax extends Ajax_Controller {

	/**
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct();

		// must be logged in
		if ($this->acl->logged_in() === FALSE)
		{
			$this->json_response(lang('core_error_auth_required'), HTTP_FORBIDDEN);
		}

		// load the admin configuration file
		$this->config->load('admin');

		// load the admin language file
		$this->lang->load('admin');

		// prepare theme name
		$this->theme->set_theme($this->config->item('admin_theme'));
	}

}
