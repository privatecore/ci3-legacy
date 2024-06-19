<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * ACL class
 *
 * This class enables you to apply permissions to controllers, controller and models,
 * as well as more fine tuned permissions at code level.
 */
class Acl {

	/**
	 * CI Singleton
	 *
	 * @var	object
	 */
	protected $_CI;

	/**
	 * Session field with current user data
	 *
	 * @var string
	 */
	protected $acl_key = 'logged_in';

	/**
	 * Current user data
	 *
	 * @var object
	 */
	protected $acl_user = NULL;

	/**
	 * Current user permissions
	 *
	 * @var array
	 */
	protected $acl_permissions = [];

	// --------------------------------------------------------------------

	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->_CI =& get_instance();

		// load the session library
		$this->_CI->load->library('session');

		if ($this->logged_in())
		{
			$this->acl_user = $this->get_user();
		}

		log_message('debug', 'ACL Class Initialized');
	}

	// --------------------------------------------------------------------

	/**
	 * login
	 *
	 * @param  array $data
	 * @return void
	 */
	public function login($data = [])
	{
		if ($data)
		{
			// regenerate the session (for security purpose: to avoid session fixation)
			$this->_CI->session->sess_regenerate(FALSE);
			$this->_CI->session->set_userdata($this->acl_key, $data);
		}
	}

	// --------------------------------------------------------------------

	/**
	 * logout
	 *
	 * @return void
	 */
	public function logout()
	{
		$this->_CI->session->unset_userdata($this->acl_key);
		$this->_CI->session->sess_destroy();
	}

	// --------------------------------------------------------------------

	/**
	 * Check whenever user is logged in
	 *
	 * @return bool
	 */
	public function logged_in()
	{
		return $this->_CI->session->has_userdata($this->acl_key);
	}

	// --------------------------------------------------------------------

	/**
	 * Return the data of user from the session
	 *
	 * @param  string $key
	 * @return object
	 */
	public function get_user($key = NULL)
	{
		$user = $this->_CI->session->userdata($this->acl_key);
		if (isset($key))
		{
			return isset($user[$key]) ? $user[$key] : NULL;
		}

		return $user;
	}

}
