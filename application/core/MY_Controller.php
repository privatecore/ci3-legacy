<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * MY_Controller class
 *
 * Base parent class which should used for all pages. All user's data (session),
 * views and content generated here.
 */
class MY_Controller extends CI_Controller {

	/**
	 * Application settings stored in the db
	 *
	 * @var object
	 */
	public $settings;

	/**
	 * Default controller model
	 *
	 * @var object
	 */
	public $default_model;

	// --------------------------------------------------------------------

	/**
	 * Default sort parameter
	 *
	 * @var string
	 */
	protected $default_sort;

	/**
	 * Default dir parameter
	 *
	 * @var string
	 */
	protected $default_dir;

	/**
	 * Default limit parameter
	 *
	 * @var int
	 */
	protected $default_limit;

	/**
	 * Default offset parameter
	 *
	 * @var int
	 */
	protected $default_offset;

	/**
	 * Default filters parameter
	 *
	 * @var array
	 */
	protected $default_filters = ['q', 'status', 'before', 'after'];

	/**
	 * Request defaults: sort, dir, limit and offset
	 *
	 * @var array
	 */
	protected $request_defaults = ['limit', 'offset', 'sort', 'dir'];

	/**
	 * Request related filters
	 *
	 * @var array
	 */
	protected $request_filters = [];

	/**
	 * URL related filters
	 *
	 * @var array
	 */
	protected $url_filters = [];

	/**
	 * The various callbacks available to the model. Each are simple lists of
	 * method names (methods will be run on $this).
	 *
	 * @var array
	 */
	protected $before_create = [];
	protected $after_create = [];
	protected $before_update = [];
	protected $after_update = [];
	protected $before_delete = [];
	protected $after_delete = [];
	protected $before_validate = [];
	protected $before_output = [];

	protected $callback_parameters = [];

	// --------------------------------------------------------------------

	/**
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct();

		// get settings
		$this->settings = new stdClass();
		$settings = $this->setting_model->set_orders('id')->get_all();
		foreach ((array) $settings as $setting)
		{
			$this->settings->{$setting['name']} = (@unserialize($setting['value']) !== FALSE)
				? unserialize($setting['value'])
				: $setting['value'];
		}

		if (isset($this->settings->timezones))
		{
			// set the time zone
			$timezones = $this->config->item('timezones');
			if (function_exists('date_default_timezone_set'))
			{
				date_default_timezone_set($timezones[$this->settings->timezones]);
			}
		}

		// set constants
		$this->default_limit = $this->settings->per_page_limit ?? 0;
		$this->default_offset = 0;
		$this->default_sort = 'date_added';
		$this->default_dir = 'desc';

		// force disable profiler
		$this->output->enable_profiler(FALSE);
	}

	// --------------------------------------------------------------------

	/**
	 * get_db_filters
	 *
	 * Return db filters used to filter entries
	 *
	 * @param  string $separator
	 * @return array
	 */
	protected function get_db_filters($separator = ',')
	{
		$filters = [];

		if ($this->default_filters)
		{
			// get default filters
			foreach ($this->default_filters as $key)
			{
				if (($value = $this->input->get($key)) === NULL)
				{
					continue;
				}

				$value = urldecode($value);

				// before and after filters key should be passed as unix
				// timestamp if not, try to get proper mysql data
				if ($key === 'before' OR $key === 'after')
				{
					$value = (is_numeric($value) && intval($value) == $value)
						? unix_to_mysql($value)
						: human_to_mysql($value);
				}
				elseif (strpos($key, 'phone') !== FALSE)
				{
					$value = phone_clean($value);
				}

				$filters[$key] = (strpos($value, $separator) !== FALSE)
					? array_unique(explode($separator, $value))
					: $value;
			}
		}

		return $filters;
	}

	// --------------------------------------------------------------------

	/**
	 * get_url_filters
	 *
	 * Return url filters to build correct url with parameters
	 *
	 * @param  string $separator
	 * @return array
	 */
	protected function get_url_filters($separator = ',')
	{
		$filters = [];

		if ($this->request_filters)
		{
			// get request filters
			foreach ($this->request_filters as $key => $value)
			{
				$filters[$key] = is_array($value) ? implode($separator, $value) : $value;
			}
		}

		return $filters;
	}

	// --------------------------------------------------------------------

	/**
	 * get_user_data
	 *
	 * @return array
	 */
	protected function get_user_data()
	{
		return [
			'ip'         => $this->input->ip_address(),
			'referer'    => $this->agent->referrer(),
			'user_agent' => $this->agent->agent_string(),
		];
	}


	// --------------------------------------------------------------------

	/**
	 * get_defaults
	 *
	 * @return array
	 */
	protected function get_defaults()
	{
		return [
			'sort'   => $this->input->get('sort') ? $this->input->get('sort', TRUE) : $this->default_sort,
			'dir'    => $this->input->get('dir') ? $this->input->get('dir', TRUE) : $this->default_dir,
			'limit'  => $this->input->get('limit') ? $this->input->get('limit', TRUE) : $this->default_limit,
			'offset' => $this->input->get('offset') ? $this->input->get('offset', TRUE) : $this->default_offset,
		];
	}

	// --------------------------------------------------------------------

	/**
	 * Trigger an event and call its observers. Pass through the event name
	 * (which looks for an instance variable $this->event_name), an array of
	 * parameters to pass through and an optional 'last in iteration'
	 * boolean.
	 *
	 * @param  string $event
	 * @param  array  $data
	 * @param  bool   $last
	 * @return array
	 */
	protected function trigger($event, $data = [], $last = TRUE)
	{
		if (isset($this->$event) && is_array($this->$event))
		{
			foreach ($this->$event as $method)
			{
				if (strpos($method, '(') !== FALSE)
				{
					preg_match('/([a-zA-Z0-9\_\-]+)(\(([a-zA-Z0-9\_\-\., ]+)\))?/', $method, $matches);
					$method = $matches[1];
					$this->callback_parameters = explode(',', $matches[3]);
				}

				$data = call_user_func_array([$this, $method], [$data, $last]);
			}
		}

		return $data;
	}

}
