<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * MY_Model class
 */
class MY_Model extends CI_Model {

	/**
	 * This model's default database table. Automatically guessed by pluralising
	 * the model name.
	 *
	 * @var string
	 */
	protected $default_table;

	/**
	 * This model's default primary key or unique identifier. Used by the get(),
	 * update() and delete() functions.
	 *
	 * @var string
	 */
	protected $primary_key = 'id';

	/**
	 * This model's default search keys. Used when processing query filters.
	 *
	 * @var string
	 */
	protected $search_key = 'name';

	/**
	 * This model's default add and update keys. Both of them used by create(),
	 * update() and delete() functions.
	 *
	 * @var string
	 */
	protected $added_key = 'date_added';
	protected $updated_key = 'date_modified';

	/**
	 * Support for soft deletes and this model's 'deleted' key
	 */
	protected $soft_delete = TRUE;
	protected $soft_delete_key = 'deleted';
	protected $_temporary_with_deleted = FALSE;
	protected $_temporary_only_deleted = FALSE;

	/**
	 * The various callbacks available to the model. Each are simple lists of
	 * method names (methods will be run on $this).
	 *
	 * @var array
	 */
	protected $before_total = array();
	protected $before_getall = array();
	protected $after_getall = array();
	protected $before_get = array();
	protected $after_get = array();
	protected $before_create = array();
	protected $after_create = array();
	protected $before_update = array();
	protected $after_update = array();
	protected $before_delete = array();
	protected $after_delete = array();
	protected $before_dropdown = array();
	protected $after_dropdown = array();

	protected $callback_parameters = array();

	/**
	 * Associative array with model related attributes
	 *
	 * @var array
	 */
	protected $attributes = array();

	// --------------------------------------------------------------------

	/**
	 * Class constructor
	 *
	 * @link https://github.com/bcit-ci/CodeIgniter/issues/5332
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();

		$this->load->helper('inflector');
		if (version_compare(PHP_VERSION, '8.1', '<'))
		{
			// array_is_list is only implemented in PHP >= 8.1
			$this->load->helper('array');
		}

		// reset table prefix to default value
		$this->set_dbtable();

		array_unshift($this->after_getall, 'prepare_attributes');
		array_unshift($this->after_get, 'prepare_attributes');
		array_unshift($this->before_create, 'protect_attributes');
		array_unshift($this->before_update, 'protect_attributes');
	}

	// --------------------------------------------------------------------

	/**
	 * reconnect
	 *
	 * Simple wrapper for the reconnect method CI_DB_driver. Keep/Reestablish
	 * the db connection if no queries have been sent for a length of time
	 * exceeding the server's idle timeout.
	 *
	 * @return $this
	 */
	public function reconnect()
	{
		$this->db->reconnect();
		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * DB Prefix
	 *
	 * Simple wrapper for the dbprefix method CI_DB_query_builder. It prepend
	 * a database prefix if one exists in configuration or return prefix.
	 *
	 * @param  string $table
	 * @return string
	 */
	public function get_dbprefix($table = '')
	{
		return $this->db->dbprefix.$table;
	}

	// --------------------------------------------------------------------

	/**
	 * set_dbtable
	 *
	 * Overwrite model's default database table
	 *
	 * @param  string $table
	 * @return $this
	 */
	public function set_dbtable($table = '')
	{
		$this->default_table = $table ?: $this->_fetch_dbtable();
		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * set_orders
	 *
	 * Set default orders (ORDER BY) for the current query
	 *
	 * @param  string|array $sort
	 * @param  string       $dir
	 * @return $this
	 */
	public function set_orders($sort, $dir = 'asc')
	{
		// format orderby field and direction into associative array where keys
		// are orderby fields and values - directions
		if ( ! is_array($sort))
		{
			$sort = array($sort => $dir);
		}

		foreach ($sort as $key => $value)
		{
			if (isset($this->attributes[$key]))
			{
				if ( ! isset($this->attributes[$key]['sortable']) OR $this->attributes[$key]['sortable'] === TRUE)
				{
					$key = $this->prepend_table($key);
				}
			}

			$this->db->order_by($key, $value);
		}

		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * set_limits
	 *
	 * Set default limits (LIMIT) for the current query
	 *
	 * @param  int $limit
	 * @param  int $offset
	 * @return $this
	 */
	public function set_limits($limit, $offset = 0)
	{
		$this->db->limit($limit, $offset);
		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * set_filters
	 *
	 * Set default filters for all the current query
	 *
	 * @param  int|array $params
	 * @return $this
	 */
	public function set_filters($params)
	{
		// allow to pass primary_key value as parameter w/o associative array,
		// also flatten array can be provided as parameter
		if ( ! is_array($params) OR ($params && array_is_list($params)))
		{
			$params = array($this->primary_key => $params);
		}

		foreach ($params as $key => $value)
		{
			switch ($key)
			{
				case 'group':
				case 'or_group':
				case 'not_group':
				case 'or_not_group':
					if (is_array($value))
					{
						$this->db->{$key.'_start'}();
						$this->set_filters($value);
						$this->db->group_end();
					}
					break;
				case 'like':
				case 'not_like':
				case 'or_like':
				case 'or_not_like':
					if (is_array($value))
					{
						foreach ($value as $k => $v)
						{
							$_value = is_array($v) ? $v['value'] : $v;
							$_side = is_array($v) ? $v['side'] : 'both';
							$this->set_like($key, $this->prepend_table($k), $_value, $_side);
						}
					}
					break;
				case 'or_where_not_in':
				case 'or_where':
				case 'where_in':
				case 'where_not_in':
					if (is_array($value))
					{
						foreach ($value as $k => $v)
						{
							$this->set_where($key, $this->prepend_table($k), $v);
						}
					}
					break;
				case 'id':
					$this->db->where("{$this->default_table}.{$this->primary_key}", $value);
					break;
				case 'q':
					$field = ctype_digit($value) ? $this->primary_key : $this->search_key;
					$method = ctype_digit($value) ? 'where' : 'like';
					if (is_array($field))
					{
						$this->db->group_start();
						foreach ($field as $idx => $item)
						{
							$method = ($idx == 0) ? $method : "or_{$method}";
							$this->db->{$method}($this->prepend_table($item), $value);
						}
						$this->db->group_end();
					}
					else
					{
						$this->db->{$method}($this->prepend_table($field), $value);
					}
					break;
				case 'before':
				case 'after':
					$operator = ($key == 'before') ? '<' : '>';
					$this->set_where('where', $this->prepend_table($this->updated_key).' '.$operator, $value);
					break;
				default:
					if (method_exists($this, 'custom_filters') && is_callable(array($this, 'custom_filters')))
					{
						call_user_func_array(array($this, 'custom_filters'), array($key, $value));
					}
					else
					{
						$this->set_where('where', $this->prepend_table($key), $value);
					}
					break;
			}
		}

		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * total_entries
	 *
	 * Return total amount of entries from the table with provided filters.
	 *
	 * @return int
	 */
	public function total_entries()
	{
		$this->trigger('before_total');

		if ($this->soft_delete && $this->_temporary_with_deleted !== TRUE)
		{
			$this->set_where('where', $this->prepend_table($this->soft_delete_key), (bool) $this->_temporary_only_deleted);
		}

		$this->db
			->select($this->prepend_table('*'))
			->group_by($this->prepend_table($this->primary_key));

		return $this->db->from($this->default_table)->count_all_results();
	}

	// --------------------------------------------------------------------

	/**
	 * entry_exists
	 *
	 * Check if entry with provided filters exists in the table.
	 *
	 * @return bool
	 */
	public function entry_exists()
	{
		return $this->total_entries() > 0;
	}

	// --------------------------------------------------------------------

	/**
	 * field_exists
	 *
	 * Simple wrapper for the field_exists method CI_DB_driver. Check if
	 * field with provided name exists in the table.
	 *
	 * @param  string $field
	 * @param  string $table
	 * @return bool
	 */
	public function field_exists($field, $table)
	{
		return $this->db->field_exists($field, $table);
	}

	// --------------------------------------------------------------------

	/**
	 * field_value
	 *
	 * Return single field value from the table with provided filters.
	 *
	 * @param  string $field
	 * @return mixed
	 */
	public function field_value($field)
	{
		$query = $this->db->select($field)->get($this->default_table, 1);
		if ($query && $query->num_rows())
		{
			return xss_clean($query->row()->{$field});
		}

		return NULL;
	}

	// --------------------------------------------------------------------

	/**
	 * Get sorted entries from the db with limit/offset and specified filters.
	 * If limit eq. zero - get all entries w/o limit.
	 *
	 * @return array|null
	 */
	public function get_all()
	{
		$this->trigger('before_getall');

		if ($this->soft_delete && $this->_temporary_with_deleted !== TRUE)
		{
			$this->set_where('where', $this->prepend_table($this->soft_delete_key), (bool) $this->_temporary_only_deleted);
		}

		$query = $this->db
			->select($this->prepend_table('*'))
			->group_by($this->prepend_table($this->primary_key))
			->get($this->default_table);

		if ($query && $query->num_rows())
		{
			$result = $query->result_array();

			foreach ($result as $key => &$row)
			{
				$row = $this->trigger('after_getall', $row, ($key == count($result) - 1));
			}

			return $result;
		}

		return NULL;
	}

	// --------------------------------------------------------------------

	/**
	 * Get single entry from the db with specified filters. If no filters
	 * was set - return empty result.
	 *
	 * @return array|null
	 */
	public function get()
	{
		$this->trigger('before_get');

		if ($this->soft_delete && $this->_temporary_with_deleted !== TRUE)
		{
			$this->set_where('where', $this->prepend_table($this->soft_delete_key), (bool) $this->_temporary_only_deleted);
		}

		$query = $this->db
			->select($this->prepend_table('*'))
			->group_by($this->prepend_table($this->primary_key))
			->get($this->default_table, 1);

		if ($query && $query->num_rows())
		{
			$row = $query->row_array();

			return $this->trigger('after_get', $row);
		}

		return NULL;
	}

	// --------------------------------------------------------------------

	/**
	 * Create new entry in the db. Return id of newly created entry or false
	 * in case of errors, empty data provided.
	 *
	 * @param  array $data
	 * @return int|bool
	 */
	public function create($data)
	{
		if ($data)
		{
			$data = $this->trigger('before_create', $data);

			$list_fields = $this->db->list_fields($this->default_table);

			foreach ($data as $key => $value)
			{
				if (is_array($value))
				{
					continue;
				}
				elseif ( ! in_array($key, $list_fields))
				{
					continue;
				}

				$this->db->set($key, $value);
			}

			if ($this->soft_delete && $this->_temporary_with_deleted !== TRUE)
			{
				$this->db->set($this->soft_delete_key, FALSE);
			}

			$this->db
				->set($this->added_key, unix_to_mysql(time()))
				->set($this->updated_key, unix_to_mysql(time()))
				->insert($this->default_table);

			if ($insert_id = $this->db->insert_id())
			{
				$this->trigger('after_create', array_merge($data, array($this->primary_key => $insert_id)));

				return $insert_id;
			}
		}

		return FALSE;
	}

	// --------------------------------------------------------------------

	/**
	 * Update an existing entry in the db. Return true, if entry was updated
	 * successfully or false in case of errors, empty data provided.
	 *
	 * @param  int|array $primary_value
	 * @param  array     $data
	 * @return bool
	 */
	public function update($primary_value, $data)
	{
		if ($data)
		{
			$data = $this->trigger('before_update', $data);

			$list_fields = $this->db->list_fields($this->default_table);

			foreach ($data as $key => $value)
			{
				if (is_array($value))
				{
					continue;
				}
				elseif ( ! in_array($key, $list_fields))
				{
					continue;
				}

				$this->db->set($key, $value);
			}

			$this->db
				->set($this->updated_key, unix_to_mysql(time()))
				->where_in($this->primary_key, (array) $primary_value)
				->update($this->default_table);

			if ($this->db->affected_rows())
			{
				$this->trigger('after_update', array_merge($data, array($this->primary_key => $primary_value)));

				return TRUE;
			}
		}

		return FALSE;
	}

	// --------------------------------------------------------------------

	/**
	 * "Soft-delete" entry from the db. Return true, if entry was deleted
	 * successfully or false in case of errors, empty data provided.
	 *
	 * @param  int|array $primary_value
	 * @return bool
	 */
	public function delete($primary_value)
	{
		if ($primary_value)
		{
			$this->trigger('before_delete', $primary_value);

			$this->db->where_in($this->primary_key, (array) $primary_value);

			if ($this->soft_delete)
			{
				$this->db
					->set($this->soft_delete_key, TRUE)
					->set($this->updated_key, unix_to_mysql(time()))
					->update($this->default_table);

				$result = $this->db->affected_rows();
			}
			else
			{
				$result = $this->db->delete($this->default_table);
			}

			if ($result)
			{
				$this->trigger('after_delete', $primary_value);

				return TRUE;
			}
		}

		return FALSE;
	}

	// --------------------------------------------------------------------

	/**
	 * Get filtered entries from the db to use em for multiselect list. By
	 * default, resulted array contains options and attributes. Last one
	 * is used for custom data-* attributes for every related option.
	 *
	 * @return array|null
	 */
	public function multiselect($key, $value, $attrs = array())
	{
		$this->trigger('before_dropdown', array($key, $value));

		if ($this->soft_delete && $this->_temporary_with_deleted !== TRUE)
		{
			$this->set_where('where', $this->prepend_table($this->soft_delete_key), FALSE);
		}

		$query = $this->db
			->select($this->prepend_table('*'))
			->group_by($this->prepend_table($this->primary_key))
			->get($this->default_table);

		if ($query && $query->num_rows())
		{
			foreach ($query->result_array() as $row)
			{
				$result['options'][$row[$key]] = xss_clean($row[$value]);

				is_array($attrs) OR $attrs = (array) $attrs;
				foreach ($attrs as $attr)
				{
					if (isset($row[$attr]))
					{
						$result['attrs'][$row[$key]]["data-{$attr}"] = xss_clean($row[$attr]);
					}
				}
			}

			return $this->trigger('after_dropdown', $result);
		}

		return NULL;
	}

	// --------------------------------------------------------------------

	/**
	 * Get filtered entries from the db to use em for dropdown list. By
	 * default, resulted array contains options and attributes. Last one
	 * is used for custom data-* attributes for every related option.
	 *
	 * @return array|null
	 */
	public function dropdown($key = NULL, $value = NULL, $attrs = array())
	{
		$key OR $key = $this->primary_key;
		$value OR $value = $this->search_key;

		$result = $this->multiselect($key, $value, $attrs);
		$result['options'] = array('' => '------') + ($result['options'] ?? array());

		return $result;
	}

	// --------------------------------------------------------------------

	/**
	 * Don't care about soft deleted rows on the next call
	 *
	 * @return $this
	 */
	public function with_deleted()
	{
		$this->_temporary_with_deleted = TRUE;
		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Only get deleted rows on the next call
	 *
	 * @return $this
	 */
	public function only_deleted()
	{
		$this->_temporary_only_deleted = TRUE;
		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Protected methods - used for manipulation data to get needed format
	 * or data representation, for ex. format/protect attributes.
	 */

	// --------------------------------------------------------------------

	/**
	 * Protect attributes by removing them from $data array
	 *
	 * @param  array $data
	 * @return array
	 */
	protected function protect_attributes($data)
	{
		$data = array_intersect_key($data, $this->attributes);

		foreach ($this->attributes as $key => $value)
		{
			if (isset($value['protected']) && $value['protected'])
			{
				unset($data[$key]);
			}
		}

		return $data;
	}

	// --------------------------------------------------------------------

	/**
	 * Prepare attributes by format values from $data array
	 *
	 * @param  array  $data
	 * @param  string $event
	 * @return array
	 */
	protected function prepare_attributes($data, $event)
	{
		$data = array_intersect_key($data, $this->attributes);

		foreach ($this->attributes as $key => $value)
		{
			if (isset($value['hidden']) && $value['hidden'])
			{
				unset($data[$key]);
				continue;
			}

			switch ($value['type'])
			{
				case 'boolean': $data[$key] = boolval($data[$key]); break;
				case 'callback':
					if (isset($this->callback_parameters[$event]) && in_array($key, $this->callback_parameters[$event]))
					{
						if (method_exists($this, $value['method']) && is_callable(array($this, $value['method'])))
						{
							$data[$key] = $this->{$value['method']}($data);
						}
					}
					break;
				case 'datetime':
					if (isset($value['format']) && $value['format'])
					{
						$data[$key] = mysql_to_human($data[$key], $value['format']);
					}
					break;
				case 'float': $data[$key] = floatval($data[$key]); break;
				case 'generic':
					if (isset($value['key']) && $value['key'])
					{
						$parameter = isset($data[$value['key']]) ? $data[$value['key']] : $data[$key];
					}
					else
					{
						$parameter = $data[$key];
					}
					$data[$key] = call_user_func($value['method'], $parameter);
					break;
				case 'json': $data[$key] = json_decode(strval($data[$key]), TRUE); break;
				case 'integer': $data[$key] = intval($data[$key]); break;
				case 'string': $data[$key] = strval($data[$key]); break;
				default: unset($data[$key]); break;
			}

			if (isset($value['xss_clean']) && $value['xss_clean'])
			{
				$data[$key] = xss_clean($data[$key]);
			}
		}

		// get sorted array of keys from attributes & data, then sort result
		$sorted_array = array_intersect(array_keys($this->attributes), array_keys($data));
		$data = array_replace(array_flip($sorted_array), $data);

		return $data;
	}

	// --------------------------------------------------------------------

	/**
	 * Prepend table's name to the specified field if need
	 *
	 * @param  string $field
	 * @param  string $table
	 * @return string
	 */
	protected function prepend_table($field, $table = '')
	{
		$table = empty($table) ? $this->default_table : $table;

		if (is_string($field))
		{
			// return current field if functions or operators are found
			if (strpos($field, '(') !== FALSE)
			{
				return $field;
			}

			$field = explode(',', $field);
		}

		$result = array();
		foreach ($field as $value)
		{
			$value = trim($value);
			if ($value !== '')
			{
				$result[] = (strpos($value, '.') === FALSE) ? "{$table}.{$value}" : $value;
			}
		}

		return $result ? implode(',', $result) : $field;
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
	protected function trigger($event, $data = array(), $last = TRUE)
	{
		if (isset($this->$event) && is_array($this->$event))
		{
			foreach ($this->$event as $method)
			{
				if (strpos($method, '('))
				{
					preg_match('/([a-zA-Z0-9\_\-]+)(\(([a-zA-Z0-9\_\-\., ]+)\))?/', $method, $matches);

					$method = $matches[1];
					$this->callback_parameters[$event] = explode(',', $matches[3]);
				}

				$data = call_user_func_array(array($this, $method), array($data, $event, $last));
			}
		}

		return $data;
	}

	// --------------------------------------------------------------------

	/**
	 * Generates WHERE clause by value.
	 *
	 * @param  string $method Query building method for where clause
	 * @param  string $key    Key to check value
	 * @param  mixed  $value  Value of the key
	 * @param  bool   $escape
	 * @return void
	 */
	public function set_where($method, $key, $value, $escape = NULL)
	{
		switch ($method)
		{
			case 'where_in':
			case 'where_not_in':
			case 'or_where_in':
			case 'or_where_not_in':
				if ( ! is_array($value))
				{
					$value = (array) $value;
				}
			case 'where':
			case 'or_where':
				if (is_array($value))
				{
					if (substr($method, -strlen('_in')) !== '_in')
					{
						$method = $method.'_in';
					}

					// Trying to get rid of warning message:
					//   preg_match(): Compilation failed: regular expression is too large at offset ...
					// @see https://stackoverflow.com/q/34912377
					if (count($value) > 25)
					{
						$group_start = 'group_start';
						if (strpos($method, 'not') !== FALSE)
						{
							$group_start = 'not_'.$group_start;
						}
						if (strpos($method, 'or') !== FALSE)
						{
							$group_start = 'or_'.$group_start;
						}

						$this->db->{$group_start}();
						$value_chunk = array_chunk($value, 25);
						foreach ($value_chunk as $chunk)
						{
							$this->db->or_where_in($key, $chunk, $escape);
						}
						$this->db->group_end();
						break;
					}
				}

				$this->db->{$method}($key, $value, $escape);
				break;
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Generates LIKE clause by value.
	 *
	 * @param  string $method Query building method for like clause
	 * @param  string $key    Key to check value
	 * @param  mixed  $value  Value of the key
	 * @param  string $side
	 * @param  bool   $escape
	 * @return void
	 */
	public function set_like($method, $key, $value, $side = 'both', $escape = NULL)
	{
		switch ($method)
		{
			case 'like':
			case 'or_like':
			case 'not_like':
			case 'or_not_like':
				$this->db->{$method}($key, $value, $side, $escape);
				break;
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Private methods - typically, some kind of helpers, used inside
	 * other public methods in the class.
	 */

	// --------------------------------------------------------------------

	/**
	 * Guess the default table name by singularising the model name
	 *
	 * @return string
	 */
	private function _fetch_dbtable()
	{
		return singular(preg_replace('/(_m|_model)?$/', '', strtolower(get_class($this))));
	}

}
