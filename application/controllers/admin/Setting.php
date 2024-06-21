<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Setting class
 *
 * @property setting_model $setting_model
 */
class Setting extends Admin_Controller {

	/**
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct();

		// load all necessary language files
		$this->lang->load('setting');
	}

	// --------------------------------------------------------------------

	/**
	 * Settings Editor
	 */
	public function index()
	{
		if ($this->acl->get_user('id') <> 1)
		{
			return $this->show_error(HTTP_FORBIDDEN);
		}

		// get settings
		$settings = $this->setting_model->set_orders('sort_order', 'asc')->get_all();

		if ($this->input->method() == 'post')
		{
			$post_data = $this->input->post();

			// validators
			foreach ($settings as $setting)
			{
				if ($setting['validation'])
				{
					$this->validation_rules[] = [
						'field' => $setting['name'], 'label' => lang($setting['label']), 'rules' => $setting['validation']
					];
				}
			}

			$this->form_validation->set_rules($this->validation_rules);
			if ($this->form_validation->run() === TRUE)
			{
				if ($this->setting_model->update(NULL, $post_data))
				{
					$this->session->set_flashdata('success', lang('setting_message_update_success'));
				}
				else
				{
					$this->session->set_flashdata('error', lang('setting_error_update_failed'));
				}

				// redirect to the current url and display message
				redirect($this->uri->uri_string());
			}
		}

		// setup page header data
		$this->theme
			->set_title(lang('setting_title'))
			->set_description(lang('setting_description'));

		// set content data
		$content = [
			'cancel_url' => base_url('admin'),
			'settings'   => $settings,
		];

		$this->theme->render('settings/form', $content);
	}

}
