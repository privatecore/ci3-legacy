<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Admin_Controller class
 *
 * Base Admin class which should be used for all administration panel pages.
 * Every request here is checked if user is logged in.
 *
 * @property acl $acl
 */
class Admin_Controller extends MY_Controller {

	/**
	 * User defaults: limit, sort and dir. Used to save parameters for the
	 * current user (session).
	 *
	 * @var array
	 */
	protected $user_defaults = ['limit', 'sort', 'dir'];

	/**
	 * Page view file
	 *
	 * @var string
	 */
	protected $page_view = '';

	/**
	 * Page related content
	 *
	 * @var array
	 */
	protected $page_content = [];

	/**
	 * Page related sorts
	 *
	 * @var array
	 */
	protected $page_sorts = ['status', 'name', 'date_added', 'date_modified'];

	/**
	 * Validation rules for add/edit actions
	 *
	 * @var array
	 */
	protected $validation_rules = [];

	/**
	 * Base url for the current controller
	 *
	 * @var string
	 */
	protected $this_url = '';

	/**
	 * The url is used in session (if available) to return to the previous
	 * filter/sorted/paginated list.
	 *
	 * @var string
	 */
	protected $redirect_url = '';

	/**
	 * The data is used during upload callback function and contains all
	 * information about uploaded files.
	 *
	 * @var array
	 */
	protected $upload_data = [];

	/**
	 * The folder name is used to generate media absolute and relative
	 * paths. Should be set for all controllers with upload callbacks,
	 * otherwise files will be saved to the wrong path.
	 *
	 * @var string
	 */
	protected $upload_folder = '';

	/**
	 * Undocumented variable
	 *
	 * @var array
	 */
	protected $upload_keys = [];

	// --------------------------------------------------------------------

	/**
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct();

		// load the admin configuration file
		$this->config->load('admin');

		// load the admin language file
		$this->lang->load('admin');

		// user must be logged in
		if ($this->acl->logged_in() === FALSE)
		{
			if ($this->uri->uri_string() !== 'admin/auth/login')
			{
				$current_url = current_url();
				if ($current_url !== base_url())
				{
					// store requested URL to session - will load once logged in
					$this->session->set_userdata(['redirect' => $current_url]);
				}

				redirect('admin/auth/login');
			}
		}

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
		// prepare theme name
		$this->theme->set_theme($this->config->item('admin_theme'));

		// set left + right error delimiters
		$this->form_validation->set_error_delimiters(
			$this->config->item('error_delimeter_left'), $this->config->item('error_delimeter_right')
		);

		// enable the profiler?
		$this->output->enable_profiler($this->config->item('profiler'));
	}

	// --------------------------------------------------------------------

	/**
	 * Show list of all entries from the db. This method can also use filters
	 * to select only certain entries. By default, filters passed with POST
	 * data and then generates GET parameters.
	 *
	 * @return void
	 */
	protected function process_method_index()
	{
		// get user defined page defaults
		$this->get_user_defaults();

		// get parameters
		$this->request_defaults = $this->get_defaults();

		// set user defined page defaults
		$this->set_user_defaults();

		// get db and url filters from the get request
		$this->request_filters = $this->get_db_filters();
		$this->url_filters = $this->get_url_filters();

		// save the current url to session for returning
		$this->session->set_userdata('referrer', $this->this_url.'?'.http_build_query_rfc3986(array_merge($this->request_defaults, $this->url_filters)));

		// set filters on form submition
		$this->form_filters();

		// get the data
		$data['total'] = $this->default_model->set_filters($this->request_filters)->total_entries();
		$data['results'] = $data['total'] > 0
			? $this->default_model
				->set_orders($this->request_defaults['sort'], $this->request_defaults['dir'])
				->set_limits($this->request_defaults['limit'], $this->request_defaults['offset'])
				->set_filters($this->request_filters)
				->get_all()
			: NULL;

		// remove offset key to build proper pagination and filters urls, in other
		// case it will fail with additional offset parameter and invalid filters
		$diff_offset = array_diff_key($this->request_defaults, array_flip(['offset']));

		// build pagination
		$this->pagination->initialize(array_merge((array) $this->config->item('pagination'), [
			'base_url'   => $this->this_url.'?'.http_build_query_rfc3986(array_merge($diff_offset, $this->url_filters)),
			'total_rows' => $data['total'],
			'per_page'   => $this->request_defaults['limit'],
			'page_sorts' => $this->page_sorts,
		]));

		// setup page header data
		if ( ! $this->theme->get_variable('description'))
		{
			$this->theme->set_description(lang('admin_description_entry_list'));
		}

		// prepare page content data before output
		$this->page_content = $this->trigger('before_output', array_merge($this->page_content, [
			'this_url'    => $this->this_url,
			'results'     => $data['results'],
			'total'       => $data['total'],
			'filters_url' => $this->this_url.'?'.http_build_query_rfc3986($diff_offset),
			'filters'     => $this->url_filters,
			'pagination'  => $this->pagination->create_links(),
			'limit'       => $this->pagination->create_limits(),
			'order'       => $this->pagination->create_orders(),
		]));

		$this->theme->render($this->page_view, $this->page_content);
	}

	// --------------------------------------------------------------------

	/**
	 * Add/Create new entry to the db. By default show form with empty fields,
	 * on submit, if validation passes - display message and redirect.
	 *
	 * @return void
	 */
	protected function process_method_add()
	{
		if ($this->input->method() == 'post')
		{
			$this->trigger('before_validate', $this->input->post());
			$this->form_validation->set_rules($this->validation_rules);

			if ($this->form_validation->run() === TRUE)
			{
				$post_data = $this->trigger('before_create', $this->input->post());
				if ($post_data['id'] = $this->default_model->create($post_data))
				{
					$this->trigger('after_create', $post_data);
					$this->session->set_flashdata('success', lang('admin_message_entry_add_success'));
				}
				else
				{
					$this->session->set_flashdata('error', lang('admin_error_entry_add_failed'));
				}

				// return to list and display message
				redirect($this->redirect_url);
			}
		}

		// setup page header data
		if ( ! $this->theme->get_variable('description'))
		{
			$this->theme->set_description(lang('admin_description_entry_add'));
		}

		// prepare page content data before output
		$this->page_content = $this->trigger('before_output', array_merge($this->page_content, [
			'cancel_url' => $this->redirect_url,
			'item'       => NULL,
		]));

		$this->theme->render($this->page_view, $this->page_content);
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
	protected function process_method_edit($id)
	{
		if (is_null($id) OR ! is_numeric($id))
		{
			redirect($this->redirect_url);
		}

		$item = $this->default_model->set_filters($id)->get();
		if (empty($item))
		{
			$this->session->set_flashdata('error', lang('admin_error_entry_not_exists'));
			redirect($this->redirect_url);
		}

		if ($this->input->method() == 'post')
		{
			$this->trigger('before_validate', $this->input->post());
			$this->form_validation->set_rules($this->validation_rules);

			if ($this->form_validation->run() === TRUE)
			{
				$post_data = $this->trigger('before_update', array_merge($this->input->post(), ['_current' => $item]));
				if ($this->default_model->update($post_data['id'], $post_data))
				{
					$this->trigger('after_update', $post_data);
					$this->session->set_flashdata('success', lang('admin_message_entry_edit_success'));
				}
				else
				{
					$this->session->set_flashdata('error', lang('admin_error_entry_edit_failed'));
				}

				// redirect to current page and display message
				redirect($this->uri->uri_string());
			}
		}

		// setup page header data
		if ( ! $this->theme->get_variable('description'))
		{
			$this->theme->set_description(sprintf(lang('admin_description_entry_edit'), $id));
		}

		// prepare page content data before output
		$this->page_content = $this->trigger('before_output', array_merge($this->page_content, [
			'cancel_url' => $this->redirect_url,
			'item'       => $item,
		]));

		$this->theme->render($this->page_view, $this->page_content);
	}

	// --------------------------------------------------------------------

	/**
	 * Delete an existing entry from the db. By default show nothing and allow
	 * only post requests. Only one parameter to get entry is id.
	 *
	 * @return void
	 */
	protected function process_method_delete()
	{
		if ($this->input->method() == 'post')
		{
			$id = (int) $this->input->post('id');

			$item = $this->default_model->set_filters($id)->get();
			if (empty($item))
			{
				$this->session->set_flashdata('error', lang('admin_error_entry_not_exists'));
				redirect($this->redirect_url);
			}

			$item = $this->trigger('before_delete', $item);
			if ($this->default_model->delete($id))
			{
				$this->trigger('after_delete', $item);
				$this->session->set_flashdata('success', lang('admin_message_entry_delete_success'));
			}
			else
			{
				$this->session->set_flashdata('error', lang('admin_error_entry_delete_failed'));
			}
		}

		// return to list and display message
		redirect($this->redirect_url);
	}

	// --------------------------------------------------------------------

	/**
	 * Clone/Duplicate entry to the db. By default show nothing and allow only
	 * post requests. Only one parameter to get entry is id.
	 *
	 * @return void
	 */
	protected function process_method_clone()
	{
		if ($this->input->method() == 'post')
		{
			$id = (int) $this->input->post('id');

			$item = $this->default_model->set_filters($id)->get();
			if (empty($item))
			{
				$this->session->set_flashdata('error', lang('admin_error_entry_not_exists'));
				redirect($this->redirect_url);
			}

			$item = $this->trigger('before_create', $item);
			if ($item['id'] = $this->default_model->create($item))
			{
				$this->trigger('after_create', $item);
				$this->session->set_flashdata('success', lang('admin_message_entry_clone_success'));
			}
			else
			{
				$this->session->set_flashdata('error', lang('admin_error_entry_clone_failed'));
			}
		}

		// return to list and display message
		redirect($this->redirect_url);
	}

	// --------------------------------------------------------------------

	/**
	 * get_user_defaults
	 *
	 * @return void
	 */
	protected function get_user_defaults()
	{
		// directory + class + method prefix used to store parameters
		$prefix = ($this->router->directory ? (rtrim(strtolower($this->router->directory), '/\\').'.') : '')
			.strtolower($this->router->class).'.'.strtolower($this->router->method).'.';

		foreach (array_values($this->user_defaults) as $key)
		{
			if (property_exists($this, 'default_'.$key))
			{
				if ($value = $this->session->userdata($prefix.$key))
				{
					$this->{'default_'.$key} = $value;
				}
			}
		}
	}

	// --------------------------------------------------------------------

	/**
	 * set_user_defaults
	 *
	 * @return void
	 */
	protected function set_user_defaults()
	{
		// directory + class + method prefix used to store parameters
		$prefix = ($this->router->directory ? (rtrim(strtolower($this->router->directory), '/\\').'.') : '')
			.strtolower($this->router->class).'.'.strtolower($this->router->method).'.';

		foreach (array_keys($this->request_defaults) as $key)
		{
			if (in_array($key, $this->user_defaults))
			{
				$this->session->set_userdata($prefix.$key, $this->request_defaults[$key]);
			}
		}
	}

	// --------------------------------------------------------------------

	/**
	 * get_redirect
	 *
	 * @param  string $url
	 * @return void
	 */
	protected function get_redirect($url)
	{
		return ($this->session->userdata('referrer'))
			? $this->session->userdata('referrer')
			: $url;
	}

	// --------------------------------------------------------------------

	/**
	 * set_filters
	 *
	 * @param  string $separator
	 * @return void
	 */
	protected function form_filters($separator = ',')
	{
		// are filters being submitted?
		if ($this->input->method() == 'post')
		{
			if ($this->input->post('clear'))
			{
				// reset button clicked
				redirect($this->this_url);
			}
			else
			{
				if ($this->default_filters)
				{
					// apply the filters
					$filters = [];
					foreach ($this->default_filters as $key)
					{
						// by default, do not allow to pass 0 (zero) or empty strings
						// as post data values to filter entries
						$value = $this->input->post($key);
						if ( ! is_null($value) && $value !== '')
						{
							if (strpos($key, 'phone') !== FALSE)
							{
								// reference to RFC 2396, which states that + is reserved
								$value = phone_clean($value);
							}

							$filters[$key] = is_array($value) ? implode($separator, $value) : $value;
						}
					}

					// redirect using new url filters
					redirect($this->this_url.'?'.http_build_query_rfc3986(array_merge($this->request_defaults, $filters)));
				}
			}
		}
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

		$this->theme->set_title(lang("errors_title_{$status_code}"));

		// set content data
		$content = [
			'heading'     => lang("errors_title_{$status_code}"),
			'message'     => lang("errors_message_{$status_code}"),
			'status_code' => $status_code,
			'show_error'  => TRUE,
		];

		$this->theme->render("errors/error_{$status_code}", $content);
	}

	// --------------------------------------------------------------------

	/**
	 * Prefixing method names with an underscore will also prevent them from being called.
	 * This is a legacy feature that is left for backwards-compatibility.
	 */

	// --------------------------------------------------------------------

	/**
	 * _callable_do_upload
	 *
	 * @param  string $null
	 * @param  string $key
	 * @param  string $postfix
	 * @return bool
	 */
	public function _callable_do_upload($null, $key, $postfix = '_upload')
	{
		$files = $this->input->files($key);

		// pass validation if input with specified key is not set or upload error
		// is not UPLOAD_ERR_OK (Value: 0; There is no error, the file uploaded with success)
		if ( ! $files OR ! in_array(UPLOAD_ERR_OK, (array) $files['error']))
		{
			return TRUE;
		}

		// load the upload helper
		$this->load->helper('upload');

		$upload_data = do_upload($key, [
			'upload_path'      => $this->config->item('upload_path'),
			'max_size'         => $this->config->item('upload_max_size'),
			'allowed_types'    => $this->config->item('upload_allowed_types'),
			'encrypt_name'     => TRUE,
			'file_ext_tolower' => TRUE,
		]);

		if ( ! is_array($upload_data))
		{
			$this->form_validation->set_message('_callback_do_upload', $upload_data);
			return FALSE;
		}

		// update upload input file name -> remove '_upload' postfix if set
		// only usable for multiple items upload
		if (substr($key,-strlen($postfix)) === $postfix)
		{
			$key = rtrim($key, $postfix);
		}

		$this->upload_data[$key] = prepare_upload($upload_data);

		return TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * _trigger_prepare_images
	 *
	 * @param  array $data
	 * @return array
	 */
	protected function _trigger_prepare_images($data)
	{
		if ($this->upload_data && is_array($this->upload_data))
		{
			$assets_relpath = get_relpath(
				$this->config->item('base_path'),
				$this->config->item('assets_images').$this->upload_folder.DIRECTORY_SEPARATOR
			);

			// prepare post input with keys from upload data - store only relative
			// path to the uploaded file name
			foreach ($this->upload_data as $key => $value)
			{
				if (empty($value) OR ! is_array($value))
				{
					unset($this->upload_data[$key]);
					continue;
				}

				if ( ! isset($value['file_name']))
				{
					foreach ($value as $item)
					{
						if ( ! isset($item['file_name']) OR empty($item['file_name']))
						{
							continue;
						}

						$data[$key][] = $assets_relpath.$item['file_name'];
					}
				}
				elseif ($value['file_name'])
				{
					$data[$key] = $assets_relpath.$value['file_name'];
				}
				else
				{
					unset($this->upload_data[$key]);
					continue;
				}
			}
		}

		return $data;
	}

	// --------------------------------------------------------------------

	/**
	 * _trigger_process_images
	 *
	 * @param  array $data
	 * @return array
	 */
	protected function _trigger_process_images($data)
	{
		$assets_path = $this->config->item('assets_images').$this->upload_folder.DIRECTORY_SEPARATOR;

		// delete images which was deleted during form submition - loop through
		// upload key and unlink everything, which was changed
		if (isset($this->upload_keys) && is_array($this->upload_keys))
		{
			foreach ($this->upload_keys as $key)
			{
				// both current item key and post input key should be set
				if ( ! isset($data['_current'][$key]) OR ! isset($data[$key]))
				{
					continue;
				}

				if (is_array($data['_current'][$key]))
				{
					$to_delete = array_diff($data['_current'][$key], (array) $data[$key]);
					if ($to_delete)
					{
						foreach ($to_delete as $image)
						{
							@unlink($assets_path.basename($image));
						}
					}
				}
				elseif ($data['_current'][$key] !== $data[$key])
				{
					@unlink($assets_path.basename($data['_current'][$key]));
				}
			}
		}

		// replace newly uploaded images with new ones - upload data can be flat
		// array (single input) or multi-dimensional array, where each key contains
		// array of upload data
		if ($this->upload_data && is_array($this->upload_data))
		{
			foreach ($this->upload_data as $key => $value)
			{
				if ( ! isset($value['file_name']))
				{
					foreach ($value as $item)
					{
						@rename($item['full_path'], $assets_path.$item['file_name']);
					}
				}
				else
				{
					if (isset($data['_current'][$key]) && $data['_current'][$key])
					{
						@unlink($assets_path.basename($data['_current'][$key]));
					}

					@rename($value['full_path'], $assets_path.$value['file_name']);
				}
			}
		}

		return $data;
	}

	// --------------------------------------------------------------------

	/**
	 * _trigger_cleanup_images
	 *
	 * @param  array $data
	 * @return array
	 */
	protected function _trigger_cleanup_images($data)
	{
		$assets_path = $this->config->item('assets_images').$this->upload_folder.DIRECTORY_SEPARATOR;

		if (isset($this->upload_keys) && is_array($this->upload_keys))
		{
			foreach ($this->upload_keys as $key)
			{
				if ( ! isset($data[$key]) OR empty($data[$key]))
				{
					continue;
				}

				if (is_array($data[$key]))
				{
					foreach ($data[$key] as $image)
					{
						@unlink($assets_path.basename($image));
					}
				}
				else
				{
					@unlink($assets_path.basename($data[$key]));
				}
			}
		}

		return $data;
	}

}
