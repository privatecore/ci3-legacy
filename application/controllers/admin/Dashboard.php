<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Dashboard class
 */
class Dashboard extends Admin_Controller {

	/**
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct();
	}

	// --------------------------------------------------------------------

	/**
	 * Default
	 */
	function index()
	{
		$this->theme
			->set_title(lang('admin_title_dashboard'))
			->set_description(lang('admin_description_dashboard'));

		$this->theme->render('dashboard');
	}

}
