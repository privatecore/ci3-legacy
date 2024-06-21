<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Cli_Controller class
 *
 * Should be used as parent class for all CLI related classes.
 */
class Cli_Controller extends MY_Controller {

	/**
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct();

		if (is_cli() === FALSE)
		{
			$this->output_string(lang('core_error_cli_only'));
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Output one or more strings
	 *
	 * @param  mixed $message
	 * @return void
	 */
	protected function output_string($message)
	{
		if (is_array($message))
		{
			foreach ($message as $value)
			{
				echo $value.PHP_EOL;
			}
		}
		else
		{
			echo $message.PHP_EOL;
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Error Handler
	 *
	 * @param  string $message
	 * @param  int    $exit_status
	 * @return void
	 */
	protected function exit_error($message, $exit_status = 1)
	{
		$this->output_string($message);

		$exit_status = abs($exit_status);
		if ($exit_status > EXIT__AUTO_MAX)
		{
			$exit_status = EXIT__AUTO_MAX;
		}

		exit($exit_status);
	}

}
