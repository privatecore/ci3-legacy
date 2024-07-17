<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * User_model class
 */
class User_model extends MY_Model {

	/**
	 * Associative array with model related attributes
	 *
	 * @var array
	 */
	protected $attributes = [
		'id'            => ['type' => 'integer', 'protected' => TRUE],
		'email'         => ['type' => 'string', 'xss_clean' => TRUE],
		'password'      => ['type' => 'string', 'hidden' => TRUE],
		'salt'          => ['type' => 'string', 'hidden' => TRUE],
		'first_name'    => ['type' => 'string', 'xss_clean' => TRUE],
		'last_name'     => ['type' => 'string', 'xss_clean' => TRUE],
		'status'        => ['type' => 'integer'],
		'deleted'       => ['type' => 'integer'],
		'date_added'    => ['type' => 'datetime', 'format' => 'd.m.Y H:i', 'protected' => TRUE],
		'date_modified' => ['type' => 'datetime', 'format' => 'd.m.Y H:i', 'protected' => TRUE],
		'time_modified' => ['type' => 'generic', 'key' => 'date_modified', 'method' => 'strtotime', 'protected' => TRUE, 'sortable' => FALSE],
	];

	// --------------------------------------------------------------------

	/**
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct();

		array_push($this->before_create, '_trigger_create_password');
		array_push($this->before_update, '_trigger_update_password');
	}

	// --------------------------------------------------------------------

	/**
	 * Check for valid login credentials. Used pairs of: email + password,
	 * email + password.
	 *
	 * @param  string $email
	 * @param  string $password
	 * @return mixed
	 */
	public function login($email = NULL, $password = NULL)
	{
		if ($email && $password)
		{
			$this->trigger('before_get');

			$query = $this->db
				->select("{$this->default_table}.*")
				->where("{$this->default_table}.status", 1)
				->where("{$this->default_table}.email", $email)
				->get($this->default_table, 1);

			if ($query && $query->num_rows())
			{
				$row = $query->row_array();

				if ($row['password'] === hash('sha512', $password.$row['salt']))
				{
					return $this->trigger('after_get', $row);
				}
			}
		}

		return NULL;
	}

	// --------------------------------------------------------------------

	/**
	 * Process login attempts for users. Return FALSE if user has too many
	 * login attempts, TRUE - otherwise.
	 *
	 * @param  string $ip
	 * @return bool
	 */
	public function login_attempt($ip)
	{
		// delete old entries for login attempts
		$last_attempt = date('Y-m-d H:i:s', strtotime("-{$this->config->item('login_max_time')} seconds"));
		$this->db->where('attempt <', $last_attempt)->delete('login_attempt');

		// add new entry for login attempt
		$this->db
			->set('ip', $ip)
			->set('attempt', unix_to_mysql(time()))
			->insert('login_attempt');

		// count attempts for the current user ip
		$total = $this->db->from('login_attempt')->where('ip', $ip)->count_all_results();
		if ($total > $this->config->item('login_max_attempts'))
		{
			return FALSE;
		}

		return TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Common methods - typically, kind of helpers, which are used by triggers
	 * or other methods in the current class.
	 */

	// --------------------------------------------------------------------

	/**
	 * _trigger_create_password
	 *
	 * @param  array $data
	 * @return array
	 */
	protected function _trigger_create_password($data)
	{
		// secure password
		$data['salt'] = hash('sha512', uniqid(mt_rand(1, mt_getrandmax()), TRUE));
		$data['password'] = hash('sha512', $data['password'].$data['salt']);

		return $data;
	}

	// --------------------------------------------------------------------

	/**
	 * _trigger_update_password
	 *
	 * @param  array $data
	 * @return array
	 */
	protected function _trigger_update_password($data)
	{
		if (isset($data['password']) && $data['password'])
		{
			// secure password
			$data['salt'] = hash('sha512', uniqid(mt_rand(1, mt_getrandmax()), TRUE));
			$data['password'] = hash('sha512', $data['password'].$data['salt']);
		}

		return $data;
	}

}
