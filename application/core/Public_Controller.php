<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Public_Controller class
 *
 * Base parent class which should used for all public pages. All public user
 * data (session, cookies), views and content are generated here.
 */
class Public_Controller extends MY_Controller {

	/**
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct();

		// load the public language file
		$this->lang->load('public');

		// load the theme library
		$this->load->library('theme');

		$this->initialize();
	}

	// --------------------------------------------------------------------

	/**
	 * Initialize the class preferences
	 */
	protected function initialize()
	{
		// prepare public theme
		$this->theme->set_theme($this->config->item('public_theme'));
		$this->theme->set_template($this->config->item('public_template'));

		// set left + right error delimiters
		$this->form_validation->set_error_delimiters(
			$this->config->item('error_delimeter_left'), $this->config->item('error_delimeter_right')
		);

		// enable the profiler?
		$this->output->enable_profiler($this->config->item('profiler'));
	}

	// --------------------------------------------------------------------

	/**
	 * Process page content view based on prepared data and filters
	 *
	 * @return void
	 */
	protected function process_page()
	{
		// trigger all necessary events to prepare the content for output
		$this->page_content = $this->trigger('before_output', $this->page_content);

		$this->theme->render($this->page_view, $this->page_content);
	}

	// --------------------------------------------------------------------

	/**
	 * Show Error Page
	 *
	 * @param  int  $status_code
	 * @param  bool $log_error
	 * @return void
	 */
	protected function show_error($status_code, $log_error = FALSE)
	{
		// load the errors language file
		$this->lang->load('errors');

		// by default we log this, but allow a dev to skip it
		if ($log_error)
		{
			log_message('error', lang("errors_title_{$status_code}").': '.$this->input->server('REQUEST_URI'));
		}

		// setting response header
		set_status_header($status_code);

		$this->theme
			->set_variables([
				'meta_title'       => lang("errors_title_{$status_code}"),
				'meta_description' => $this->settings->meta_description,
				'meta_keywords'    => $this->settings->meta_keywords,
				'breadcrumb'       => [
					['title' => lang('public_title_home'), 'url' => base_url()],
					['title' => lang("errors_title_{$status_code}"), 'url' => NULL],
				],
			]);

		// set content data
		$this->page_content = $this->trigger('before_output', [
			'heading'     => lang("errors_title_{$status_code}"),
			'message'     => lang("errors_message_{$status_code}"),
			'status_code' => $status_code,
			'show_error'  => TRUE,
		]);

		$this->theme->render("errors/error_{$status_code}", $this->page_content);
	}

}
