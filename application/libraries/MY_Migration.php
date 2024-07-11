<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * MY_Migration class
 */
class MY_Migration extends CI_Migration {

	/**
	 * @var array collect used timestamp
	 */
	protected $_timestamp_set = [];

	/**
	 * Tables meta data array
	 *
	 * @var array
	 */
	protected $table_meta = [];

	/**
	 * @var array skip table name set
	 */
	protected $skip_tables = [];

	// --------------------------------------------------------------------

	/**
	 * Initialize MY_Migration class
	 *
	 * @param  array $config
	 * @return void
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);

		$this->skip_tables[] = $this->config->item('migration_table');

		log_message('debug', 'MY_Migration Class Initialized');
	}

	// --------------------------------------------------------------------

	/**
	 * Create migrations files for tables
	 *
	 * @param  string $tables
	 * @return bool
	 */
	public function create($tables = '*')
	{
		// check tables not empty
		if (empty($tables))
		{
			$this->_error_string = $this->lang->line('migration_none_found');
			return FALSE;
		}

		if ($tables === '*')
		{
			$tables_array = $this->_get_all_tables($this->db->database);

			// collect tables of migration
			$tables_set = [];

			// confirm table num
			if ($tables_array)
			{
				$tables_in_key = "Tables_in_{$this->db->database}";

				foreach ($tables_array as $table_info)
				{
					$table_name = $table_info[$tables_in_key];

					if (isset($table_name) && $table_name !== '')
					{
						// check if table in skip arrays, if so, go next
						if (in_array($table_name, $this->skip_tables))
						{
							continue;
						}

						// skip views
						if (strtolower($table_info['Table_type']) == 'view')
						{
							continue;
						}

						$tables_set[] = $table_name;
					}
				}

				if ($this->db->dbprefix($this->db->database) !== '')
				{
					array_walk($tables_set, [$this, '_remove_database_prefix']);
				}
			}
		}
		else
		{
			$tables_set = is_array($tables) ? $tables : explode(',', $tables);
		}

		if (empty($tables_set))
		{
			$this->_error_string = $this->lang->line('migration_none_found');
			return FALSE;
		}

		// create migration file or override it.
		foreach ($tables_set as $table_name)
		{
			$this->table_meta = $this->_get_table_meta($table_name);

			$file_content = $this->get_file_content($table_name);

			if ( ! empty($file_content))
			{
				$this->write_file($table_name, $file_content);
				continue;
			}
		}

		return TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * get_file_content
	 *
	 * @param  string $table_name
	 * @return string
	 */
	public function get_file_content($table_name)
	{
		$content  = '<?php defined(\'BASEPATH\') OR exit(\'No direct script access allowed\');' . "\n\n";
		$content .= '/**' . "\n";
		$content .= " * Migration_create_{$table_name} class" . "\n";
		$content .= ' */' . "\n";
		$content .= "class Migration_create_{$table_name} extends CI_Migration {" . "\n";
		$content .= $this->get_function_up_content($table_name);
		$content .= "\n\t" . '// --------------------------------------------------------------------' . "\n";
		$content .= $this->get_function_down_content($table_name);
		$content .= "\n" . '}' . "\n";

		return $content;
	}

	// --------------------------------------------------------------------

	/**
	 * write_file
	 *
	 * @param  string $table_name
	 * @param  string $file_content
	 * @return void
	 */
	public function write_file($table_name, $file_content)
	{
		$file = $this->open_file($table_name);
		fwrite($file, $file_content);
		fclose($file);
	}

	// --------------------------------------------------------------------

	/**
	 * open_file
	 *
	 * @param  string $table_name
	 * @return mixed
	 */
	public function open_file($table_name)
	{
		$timestamp = $this->_get_timestamp($table_name);

		$file_path = $this->config->item('migration_path') . $timestamp . '_create_' . $table_name . '.php';

		// Open for reading and writing.
		// Place the file pointer at the beginning of the file and truncate the file to zero length.
		// If the file does not exist, attempt to create it.
		$file = fopen($file_path, 'w+');

		if ( ! $file)
		{
			return FALSE;
		}

		// add this timestamp to timestamp ser
		$this->_timestamp_set[] = $timestamp;

		return $file;
	}

	// --------------------------------------------------------------------

	/**
	 * Base on table name create migration up function
	 *
	 * @param  string $table_name
	 * @return mixed
	 */
	public function get_function_up_content($table_name)
	{
		$content  = "\n\t" . '/**' . "\n";
		$content .= "\t" . ' * up (create table)' . "\n";
		$content .= "\t" . ' *' . "\n";
		$content .= "\t" . ' * @return void' . "\n";
		$content .= "\t" . ' */' . "\n";
		$content .= "\t" . 'public function up()' . "\n";
		$content .= "\t" . '{' . "\n";

		$query = $this->db->query("SHOW FULL FIELDS FROM `{$this->db->dbprefix($table_name)}` FROM `{$this->db->database}`");

		// If there is no result, return directly
		if ( ! $query OR $query->num_rows() == 0)
		{
			return FALSE;
		}

		$columns = $query->result_array();

		$add_key = '';

		$content .= "\t\t" . '$this->dbforge->add_field(array(' . "\n";

		foreach ($columns as $column)
		{
			// field name
			$content .= "\t\t\t'{$column['Field']}' => array(" . "\n";

			// preg_match('/^(\w+)\(([\d]+(?:,[\d]+)*)\)/', $column['Type'], $match);
			preg_match('/^(\w+)\((.+)\)/', $column['Type'], $match);

			if ($match === [])
			{
				preg_match('/^(\w+)/', $column['Type'], $match);
			}

			$content .= "\t\t\t\t'type' => '" . strtoupper($match[1]) . "'," . "\n";

			if (isset($match[2]))
			{
				switch (strtoupper($match[1]))
				{
					// type enum and set need extra handle
					case 'ENUM':
					case 'SET':
						$enum_constraint_str = str_replace(',', ', ', $match[2]);
						$content .= "\t\t\t\t'constraint' => [" . $enum_constraint_str . "],\n";
						break;
					default:
						$content .= "\t\t\t\t'constraint' => '" . strtoupper($match[2]) . "'," . "\n";
						break;
				}
			}

			$content .= (strstr($column['Type'], 'unsigned')) ? "\t\t\t\t'unsigned' => TRUE," . "\n" : '';
			$content .= ((string) $column['Default'] !== '') ? "\t\t\t\t'default' => '" . $column['Default'] . "'," . "\n" : '';
			$content .= ((string) $column['Comment'] !== '') ? "\t\t\t\t'comment' => '" . str_replace("'", "\\'", $column['Comment']) . "',\n" : '';
			$content .= ($column['Null'] !== 'NO') ? "\t\t\t\t'null' => TRUE," . "\n" : '';
			$content .= "\t\t\t)," . "\n";

			if ($column['Key'] == 'PRI')
			{
				$add_key .= "\t\t" . "\$this->dbforge->add_key('{$column['Field']}', TRUE);" . "\n";
			}
		}

		$content .= "\t\t));" . "\n";
		$content .= ($add_key !== '') ? "\n" . $add_key : '';

		// create db

		$query = $this->db->query("SHOW TABLE STATUS WHERE `Name` = '{$table_name}'");

		$engines = $query->row_array();

		$content .= "\n\t\t" . '$attributes = array(' . "\n";;
		$content .= ((string) $engines['Engine'] !== '') ? "\t\t\t'ENGINE' => '" . $engines['Engine'] . "'," . "\n" : '';
		$content .= ((string) $engines['Comment'] !== '') ? "\t\t\t'COMMENT' => '\\'" . str_replace("'", "\\'", $engines['Comment']) . "'\\'',\n" : '';
		$content .= "\t\t" . ');' . "\n\n";
		$content .= "\t\t" . "\$this->dbforge->create_table('{$table_name}', TRUE, \$attributes);";
		$content .= "\n\t" . '}' . "\n";

		return $content;
	}

	// --------------------------------------------------------------------

	/**
	 * Base on table name create migration down function
	 *
	 * @param  string $table_name
	 * @return string
	 */
	public function get_function_down_content($table_name)
	{
		$content  = "\n\t" . '/**' . "\n";
		$content .= "\t" . ' * down (drop table)' . "\n";
		$content .= "\t" . ' *' . "\n";
		$content .= "\t" . ' * @return void' . "\n";
		$content .= "\t" . ' */' . "\n";
		$content .= "\t" . 'public function down()' . "\n";
		$content .= "\t" . '{' . "\n";
		$content .= "\t\t" . "\$this->dbforge->drop_table('{$table_name}', TRUE);" . "\n";
		$content .= "\t" . '}' . "\n";

		return $content;
	}

	// --------------------------------------------------------------------

	/**
	 * _get_full_tables
	 *
	 * @param  string $database_name
	 * @return array
	 */
	private function _get_all_tables($database_name)
	{
		$query = $this->db->query("SHOW FULL TABLES FROM `{$database_name}`");
		return $query->result_array();
	}

	// --------------------------------------------------------------------

	/**
	 * _get_table_status
	 *
	 * @param  string $table_name
	 * @return array|null
	 */
	private function _get_table_meta($table_name)
	{
		$query = $this->db->query("SHOW TABLE STATUS WHERE `Name` = '{$table_name}'");
		return $query->row_array();
	}

	// --------------------------------------------------------------------

	/**
	 * _get_timestamp get
	 *
	 * @param  string $table_name
	 * @return string
	 */
	private function _get_timestamp($table_name)
	{
		$timestamp = date('YmdHis', strtotime($this->table_meta['Create_time']));

		while (in_array($timestamp, $this->_timestamp_set))
		{
			$timestamp += 1;
		}

		return $timestamp;
	}

	// --------------------------------------------------------------------

	/**
	 * _remove_database_prefix
	 *
	 * @param  string $table_name
	 * @return void
	 */
	private function _remove_database_prefix(&$table_name)
	{
		// insensitive replace
		$table_name = str_ireplace($this->db->dbprefix, '', $table_name);
	}

}
