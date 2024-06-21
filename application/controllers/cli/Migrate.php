<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Migrate class
 */
class Migrate extends Cli_Controller {

	/**
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct();

		// load all necessary configrations
		$this->config->load('migration');

		// load all necessary libraries
		$this->load->library('migration');
	}

	// --------------------------------------------------------------------

	/**
	 * Display the list of current migration scheme
	 *
	 * @return void
	 */
	public function info()
	{
		$this->output_string([
			'Migration is initialised. Make sure this is not accessible in production!',
			'Currently available migrations:',
		]);

		if ($migrations = $this->migration->find_migrations())
		{
			foreach ($migrations as $value)
			{
				$this->output_string($value);
			}
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Set the schema to the latest migration
	 *
	 * @return void
	 */
	public function latest()
	{
		if ( ! $this->migration->latest())
		{
			// do not exist with error, since only case when 'latest' method can
			// fall is - no migration files/scripts found
			$this->exit_error($this->migration->error_string(), EXIT_SUCCESS);
		}

		$this->output_string('Migrated to latest.');
	}

	// --------------------------------------------------------------------

	/**
	 * Set the schema to the migration version set in config
	 *
	 * @return void
	 */
	public function current()
	{
		if ( ! $this->migration->current())
		{
			$this->exit_error($this->migration->error_string());
		}

		$this->output_string('Migrated to current.');
	}

	// --------------------------------------------------------------------

	/**
	 * Migrate to a schema version
	 *
	 * @param  string $target
	 * @return void
	 */
	public function version($target)
	{
		if ( ! $this->migration->version($target))
		{
			$this->exit_error($this->migration->error_string());
		}

		$this->output_string("Migrated to version {$target}.");
	}

	// --------------------------------------------------------------------

	/**
	 * Restart the migration from 0 to the number specified or latest
	 *
	 * @param  string $target
	 * @return void
	 */
	public function restart($target = NULL)
	{
		$this->migration->version(0);

		if ( ! empty($target))
		{
			if ( ! $this->migration->version($target))
			{
				$this->exit_error($this->migration->error_string());
			}

			$this->output_string("Restarted migration to {$target}.");
		}
		else
		{
			if ( ! $this->migration->latest())
			{
				$this->exit_error($this->migration->error_string());
			}

			$this->output_string('Restarted migration to latest.');
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Generate migration placeholder file
	 *
	 * @param  string $name
	 * @return void
	 */
	public function generate($name = NULL)
	{
		if (empty($name))
		{
			$this->exit_error('Migration name is empty.', EXIT_CONFIG);
		}

		// fix errors when multiple migration classes with the same name are called
		$name = $name.'_'.md5(uniqid(mt_rand()));

		$content = "<?php defined('BASEPATH') OR exit('No direct script access allowed');" . "\n\n"
			. '/**' . "\n"
			. " * Migration_{$name} class" . "\n"
			. ' */' . "\n"
			. "class Migration_{$name} extends CI_Migration {" . "\n"
			."\n"
				. "\t" . '/**' . "\n"
				. "\t" . ' * up' . "\n"
				. "\t" . ' *' . "\n"
				. "\t" . ' * @return void' . "\n"
				. "\t" . ' */' . "\n"
				. "\t" . 'public function up()' . "\n"
				. "\t" . '{' . "\n"
				. "\t" . '}' . "\n"
			. "\n"
				. "\t" . '/**' . "\n"
				. "\t" . ' * down' . "\n"
				. "\t" . ' *' . "\n"
				. "\t" . ' * @return void' . "\n"
				. "\t" . ' */' . "\n"
				. "\t" . 'public function down()' . "\n"
				. "\t" . '{' . "\n"
				. "\t" . '}' . "\n"
			. "\n" . '}' . "\n";

		$file_name = $this->security->sanitize_filename(date('YmdHis').'_'.$name.'.php');
		if (file_put_contents($this->config->item('migration_path').$file_name, $content) === FALSE)
		{
			$this->exit_error('Generation of migrate file failed.');
		}

		$this->output_string("Generated migrate file: {$file_name}.");
	}

}
