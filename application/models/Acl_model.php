<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Acl_model class
 *
 * This class enables apply permissions to controllers, controller and models,
 * as well as more fine tuned permissions at code level.
 */
class Acl_model extends CI_Model {

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
	 * Get user related access(controller) by id. Return empty array if no
	 * access(controller) was found.
	 *
	 * @param  int $user_id
	 * @return array
	 */
	public function get_user_access($user_id = NULL)
	{
		$access = [];

		$query = $this->db
			->where('acl_user_access.user_id', $user_id)
			->get('acl_user_access');

		if ($query && $query->num_rows())
		{
			// Add to the list of permissions
			foreach ($query->result_array() as $row)
			{
				$access[$row['controller']] = [
					'user_id'    => (int) $user_id,
					'controller' => xss_clean($row['controller']),
				];
			}
		}

		return $access;
	}

	// --------------------------------------------------------------------

	/**
	 * Get user related permissions by id. Return empty array if no
	 * permissions was found.
	 *
	 * @param  int $user_id
	 * @return array
	 */
	public function get_user_permissions($user_id = NULL)
	{
		$permissions = [];

		$query = $this->db
			->select('acl_permissions.*')
			->join('acl_permissions', "acl_permissions.{$this->primary_key} = acl_user_permissions.permission_id", 'left')
			->where('acl_user_permissions.user_id', $user_id)
			->get('acl_user_permissions');

		if ($query && $query->num_rows())
		{
			// Add to the list of permissions
			foreach ($query->result_array() as $row)
			{
				$permissions[$row['key']] = [
					'id'   => (int) $row[$this->primary_key],
					'name' => xss_clean($row['name']),
					'key'  => xss_clean($row['key']),
					'type' => xss_clean($row['type']),
				];
			}
		}

		return $permissions;
	}

	// --------------------------------------------------------------------

	/**
	 * Get group related access(controller) by id. Return empty array if no
	 * access(controller) was found.
	 *
	 * @param  int $group_id
	 * @return array
	 */
	public function get_group_access($group_id = NULL)
	{
		$access = [];

		$query = $this->db
			->where('acl_group_access.group_id', $group_id)
			->get('acl_group_access');

		if ($query && $query->num_rows())
		{
			// Add to the list of access
			foreach ($query->result_array() as $row)
			{
				$access[$row['controller']] = [
					'group_id'   => (int) $group_id,
					'controller' => xss_clean($row['controller']),
				];
			}
		}

		return $access;
	}

	// --------------------------------------------------------------------

	/**
	 * Get group related permissions by id. Return empty array if no
	 * permissions was found.
	 *
	 * @param  int $group_id
	 * @return array
	 */
	public function get_group_permissions($group_id = NULL)
	{
		$permissions = [];

		$query = $this->db
			->select('acl_permissions.*')
			->join('acl_permissions', "acl_permissions.{$this->primary_key} = acl_group_permissions.permission_id", 'left')
			->where('acl_group_permissions.group_id', $group_id)
			->get('acl_group_permissions');

		if ($query && $query->num_rows())
		{
			// Add to the list of permissions
			foreach ($query->result_array() as $row)
			{
				$permissions[$row['key']] = [
					'id'   => (int) $row[$this->primary_key],
					'name' => xss_clean($row['name']),
					'key'  => xss_clean($row['key']),
					'type' => xss_clean($row['type']),
				];
			}
		}

		return $permissions;
	}

	// --------------------------------------------------------------------

	/**
	 * Get all acl permissions grouped by type. Return empty array if no
	 * permissions was found.
	 *
	 * @return array
	 */
	public function get_all_permissions()
	{
		$permissions = [];

		$query = $this->db
			->order_by('acl_permissions.sort_order', 'asc')
			->get('acl_permissions');

		if ($query && $query->num_rows())
		{
			foreach ($query->result_array() as $row)
			{
				$permissions[$row['type']][] = [
					'id'   => (int) $row[$this->primary_key],
					'name' => xss_clean($row['name']),
					'key'  => xss_clean($row['key']),
					'type' => xss_clean($row['type']),
				];
			}
		}

		return $permissions;
	}

	// --------------------------------------------------------------------

	/**
	 * Disabled methods - prevent query calls and other processing for the
	 * specified methods. Only parent methods from CI_Model should be set
	 * in here.
	 */

	// --------------------------------------------------------------------

	public function get_all() { return NULL; }
	public function get() { return NULL; }
	public function create($data) { return FALSE; }
	public function update($primary_value, $data) { return FALSE; }
	public function delete($primary_value) { return FALSE; }
	public function multiselect($key, $value, $attrs = []) { return NULL; }
	public function dropdown($key, $value, $attrs = []) { return NULL; }

}
