<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Setting_model class
 */
class Setting_model extends MY_Model {

	/**
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct();

		// do not allow 'soft-delete' method here
		$this->soft_delete = FALSE;
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
		$query = $this->db->get($this->default_table);
		if ($query && $query->num_rows())
		{
			return $query->result_array();
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
		$query = $this->db->get($this->default_table, 1);
		if ($query && $query->num_rows())
		{
			return $query->row_array();
		}

		return NULL;
	}

	// --------------------------------------------------------------------

	/**
	 * Update all existing entries in the db. Return TRUE, if even single
	 * entry was updated successfully or FALSE in case of errors, empty
	 * data provided.
	 *
	 * @param  int|array $primary_value
	 * @param  array     $data
	 * @return bool
	 */
	public function update($primary_value, $data)
	{
		$result = FALSE;

		if ($data)
		{
			foreach ($data as $key => $value)
			{
				$this->db
					->set('value', is_array($value) ? serialize($value) : $value)
					->set($this->updated_key, unix_to_mysql(time()))
					->where('name', $key)
					->update($this->default_table);

				if ($this->db->affected_rows())
				{
					$result = TRUE;
				}
			}
		}

		return $result;
	}

	// --------------------------------------------------------------------

	/**
	 * Disabled methods - prevent query calls and other processing for the
	 * specified methods. Only parent methods from CI_Model should be set
	 * in here.
	 */

	// --------------------------------------------------------------------

	public function create($data) { return FALSE; }
	public function delete($primary_value) { return FALSE; }
	public function multiselect($key, $value, $attrs = []) { return NULL; }

}
