<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Auth class
 *
 * @property acl $acl
 * @property user_model $user_model
 */
class Auth extends Admin_Controller {

	/**
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct();

		// load all necessary language files
		$this->lang->load('user');

		// load all necessary models
		$this->load->model('user_model');
	}

	// --------------------------------------------------------------------

	/**
	 * Validate login credentials
	 */
	public function login()
	{
		if ($this->acl->logged_in())
		{
			redirect('admin/index');
		}

		// validators
		$this->validation_rules = [
			['field' => 'email', 'label' => lang('admin_input_email'), 'rules' => 'required|trim|max_length[128]'],
			['field' => 'password', 'label' => lang('admin_input_password'), 'rules' => 'required|trim|max_length[128]'],
			['field' => 'login', 'rules' => 'callback__login'],
		];

		$this->form_validation->set_rules($this->validation_rules);

		if ($this->form_validation->run() === TRUE)
		{
			if ($this->session->userdata('redirect'))
			{
				// redirect to desired page
				$redirect = $this->session->userdata('redirect');
				$this->session->unset_userdata('redirect');

				redirect($redirect);
			}

			redirect('admin/index');
		}

		// setup page header data
		$this->theme->set_title(lang('user_title_login'));

		// declare login template
		$this->theme->set_template('login');
		$this->theme->render('auth/login');
	}

	// --------------------------------------------------------------------

	/**
	 * Profile Editor
	 */
	public function profile()
	{
		// setup page header data
		$this->theme
			->set_title(lang('user_title_profile'))
			->set_description(lang('user_description_profile'));

		// set content data
		$content = [
			'cancel_url'        => base_url(),
			'password_required' => TRUE,
			'item'              => $this->acl->get_user(),
		];

		$this->theme->render('auth/profile', $content);
	}

	// --------------------------------------------------------------------

	/**
	 * Logout
	 */
	public function logout()
	{
		$this->acl->logout();
		redirect('admin/auth/login');
	}

	// --------------------------------------------------------------------

	/**
	 * Prefixing method names with an underscore will also prevent them from being called.
	 * This is a legacy feature that is left for backwards-compatibility.
	 */

	// --------------------------------------------------------------------

	/**
	 * _login
	 *
	 * Check login credentials provided by user. Also check if user has too
	 * many login attempts.
	 *
	 * @return bool
	 */
	public function _login()
	{
		$post_data = $this->input->post();

		// do nothing if email and/or password is not set
		if (empty($post_data['email']) OR empty($post_data['password']))
		{
			return TRUE;
		}

		// restrict login attempts count and if user pass though this limit -
		// show error message and do not let him login
		if ($this->user_model->login_attempt($this->input->ip_address()) === FALSE)
		{
			$this->form_validation->set_message('_login',
				sprintf(lang('user_error_max_login_attempts'), $this->config->item('login_max_time'))
			);
			return FALSE;
		}

		if ( ! ($user = $this->user_model->login($post_data['email'], $post_data['password'])))
		{
			$this->form_validation->set_message('_login', lang('user_error_login_failed'));
			return FALSE;
		}

		$this->acl->login($user);

		return TRUE;
	}

}
