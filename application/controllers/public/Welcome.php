<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Welcome class
 */
class Welcome extends Public_Controller {

	/**
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct();
	}

	// --------------------------------------------------------------------

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 *      http://example.com/index.php/welcome
	 *  - or -
	 *      http://example.com/index.php/welcome/index
	 *  - or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/userguide3/general/urls.html
	 */
	public function index()
	{
		// setup theme related variables - can be used inside template,
		// these variables also can be set in public controller to stick
		// to DRY principle
		$this->theme
			->set_variables([
				'meta_title'       => 'meta_title',
				'meta_description' => 'meta_description',
				'meta_keywords'    => 'meta_keywords',
			]);

		// set page data to process
		$this->page_view = 'welcome';
		$this->page_content = [
			// every array's key can be accessed as variable inside view
		];

		$this->process_page();
	}

}
