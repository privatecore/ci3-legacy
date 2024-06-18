<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * MY_Router class
 */
class MY_Router extends CI_Router {

	/**
	 * Class constructor
	 *
	 * Runs the route mapping function.
	 *
	 * @param  array $routing
	 * @return void
	 */
	public function __construct($routing = NULL)
	{
		parent::__construct($routing);
	}

	// --------------------------------------------------------------------

	/**
	 * Set default controller
	 *
	 * @return	void
	 */
	protected function _set_default_controller()
	{
		if (empty($this->default_controller))
		{
			show_error('Unable to determine what should be displayed. A default route has not been specified in the routing file.');
		}

		$default_controller = explode('/', trim($this->default_controller, '/'));

		// If sub-folder specified in the default controller
		if (count($default_controller) > 2)
		{
			// Method should be specified
			$method = array_pop($default_controller);
			$class = array_pop($default_controller);

			// Update default controller with values
			$this->default_controller = $class.'/'.$method;

			$this->set_directory(implode(DIRECTORY_SEPARATOR, $default_controller));
		}

		// Is the method being specified?
		if (sscanf($this->default_controller, '%[^/]/%s', $class, $method) !== 2)
		{
			$method = 'index';
		}

		if ( ! file_exists(APPPATH.'controllers/'.$this->directory.ucfirst($class).'.php'))
		{
			// This will trigger 404 later
			return;
		}

		$this->set_class($class);
		$this->set_method($method);

		// Assign routed segments, index starting from 1
		$this->uri->rsegments = array(
			1 => $class,
			2 => $method
		);

		log_message('debug', 'No URI present. Default controller set.');
	}

}
