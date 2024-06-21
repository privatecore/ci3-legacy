<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * User class
 *
 * @property user_model $default_model
 */
class User extends Admin_Controller {

	/**
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct();

		// load all necessary language files
		$this->lang->load('user');

		// set default model
		$this->load->model('user_model', 'default_model');

		$this->this_url = base_url('admin/user/index');
		$this->redirect_url = $this->get_redirect($this->this_url);

		// setup page header data
		$this->theme->set_title(lang('admin_title_user'));

		// set triggers to process
		array_push($this->before_validate, '_trigger_before_validate');
		array_push($this->before_update, '_trigger_before_update');
	}

	// --------------------------------------------------------------------

	/**
	 * Show list of all entries from the db. This method can also use filters
	 * to select only certain entries. By default, filters passed with POST
	 * data and then generates GET parameters.
	 *
	 * @return void
	 */
	public function index()
	{
		if ($this->acl->get_user('id') <> 1)
		{
			return $this->show_error(HTTP_FORBIDDEN);
		}

		// set page sortings
		$this->page_sorts = ['status', 'email', 'date_added', 'date_modified'];

		// set page sort and dir
		$this->default_sort = 'date_added';
		$this->default_dir = 'desc';

		// set page data to process
		$this->page_view = 'user/list';
		$this->page_content = [
			'this_url'   => $this->this_url,
			'add_url'    => base_url('admin/user/add'),
			'edit_url'   => base_url('admin/user/edit'),
			'delete_url' => base_url('admin/user/delete'),
		];

		$this->process_method_index();
	}

	// --------------------------------------------------------------------

	/**
	 * Add/Create new entry to the db. By default show form with empty fields,
	 * on submit, if validation passes - display message and redirect.
	 *
	 * @return void
	 */
	public function add()
	{
		// set page data to process
		$this->page_view = 'user/form';
		$this->page_content = [
			'cancel_url'        => $this->redirect_url,
			'password_required' => TRUE,
		];

		$this->process_method_add();
	}

	// --------------------------------------------------------------------

	/**
	 * Edit/Update an existing entry in the db. If no POST data was submitted,
	 * show form with current entry's data. On submit redirect to the same page
	 * and display message with result (success or failure).
	 *
	 * @param  int $id
	 * @return void
	 */
	public function edit($id = NULL)
	{
		if ($this->acl->get_user('id') <> 1)
		{
			return $this->show_error(HTTP_FORBIDDEN);
		}

		// set page data to process
		$this->page_view = 'user/form';
		$this->page_content = [
			'cancel_url'        => $this->redirect_url,
			'password_required' => FALSE,
		];

		$this->process_method_edit($id);
	}

	// --------------------------------------------------------------------

	/**
	 * Delete an existing entry from the db. By default show nothing and allow
	 * only post requests. Only one parameter to get entry is id.
	 *
	 * @return void
	 */
	public function delete()
	{
		if ($this->acl->get_user('id') <> 1)
		{
			return $this->show_error(HTTP_FORBIDDEN);
		}

		$this->process_method_delete();
	}

	// --------------------------------------------------------------------

	/**
	 * Prefixing method names with an underscore will also prevent them from being called.
	 * This is a legacy feature that is left for backwards-compatibility.
	 */

	// --------------------------------------------------------------------

	/**
	 * _validate_email
	 *
	 * @param  string $value
	 * @return bool
	 */
	public function _validate_email($value)
	{
		// 'required' case is checked with other rule
		if (empty($value))
		{
			return TRUE;
		}

		$id = (int) $this->input->post('id');

		$entry_exists = $this->default_model->set_filters(['id <>' => $id, 'email' => $value])->entry_exists();
		if ($entry_exists)
		{
			$this->form_validation->set_message('_validate_email', lang('admin_error_email_exists'));
			return FALSE;
		}

		return TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * _trigger_before_validate
	 *
	 * @param  array $data
	 * @return array
	 */
	protected function _trigger_before_validate($data)
	{
		// validators
		$this->validation_rules = [
			['field' => 'email', 'label' => lang('admin_input_email'), 'rules' => 'required|trim|max_length[128]|valid_email|callback__validate_email'],
			['field' => 'first_name', 'label' => lang('admin_input_first_name'), 'rules' => 'required|trim|min_length[2]|max_length[32]'],
			['field' => 'last_name', 'label' => lang('admin_input_last_name'), 'rules' => 'trim|min_length[2]|max_length[32]'],
			['field' => 'password_repeat', 'label' => lang('admin_input_password_repeat'), 'rules' => 'required|trim|matches[password]'],
			['field' => 'password', 'label' => lang('admin_input_password'), 'rules' => 'required|trim|min_length[5]'],
			['field' => 'status', 'label' => lang('admin_input_status'), 'rules' => 'required|in_list[0,1]'],
		];

		if (isset($data['id']) && $data['id'])
		{
			// do not require password & password_repeat when edit existing entry,
			// but if one is set -> validate submited data
			$this->validation_rules = array_merge($this->validation_rules, [
				['field' => 'password', 'label' => lang('user_label_password'), 'rules' => 'trim|min_length[5]'],
				['field' => 'password_repeat', 'label' => lang('user_label_password_repeat'), 'rules' => 'trim|matches[password]'],
			]);
		}

		return $data;
	}

	// --------------------------------------------------------------------

	/**
	 * _trigger_before_update
	 *
	 * @param  array $data
	 * @return array
	 */
	protected function _trigger_before_update($data)
	{
		// do not update password when updating user related data, cuz
		// password is not required for edit
		if (isset($data['password']) && empty($data['password']))
		{
			unset($data['password'], $data['password_repeat']);
		}

		return $data;
	}

}
