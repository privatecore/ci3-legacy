<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Theme Class
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Theme
 */
class Theme {

	/**
	 * CI Singleton
	 *
	 * @var	object
	 */
	protected $_CI;

	/**
	 * Theme folder name
	 *
	 * @var string
	 */
	protected $theme = 'default';

	/**
	 * Template file name
	 *
	 * @var string
	 */
	protected $template = 'default';

	/**
	 * Theme additional css, js and jsi18n data
	 *
	 * @var array
	 */
	protected $css_files = array();
	protected $js_files = array();
	protected $js_i18n = array();

	/**
	 * Associative array of data to pass to view
	 *
	 * @var array
	 */
	protected $data = array();

	/**
	 * Global rrror message to display
	 *
	 * @var string
	 */
	protected $error = '';

	// --------------------------------------------------------------------

	/**
	 * Class constructor
	 *
	 * @return void
	 */
	public function __construct()
	{
		$this->_CI =& get_instance();

		log_message('debug', 'Theme Class Initialized');
	}

	// --------------------------------------------------------------------

	/**
	 * Set Current Theme
	 *
	 * @param  string $theme
	 * @return CI_Theme
	 */
	public function set_theme($theme = 'default')
	{
		$this->theme = $this->_CI->security->sanitize_filename($theme);

		// prepend a parent path to the theme path arrays, this should be set
		// before render call, otherwise error will be thrown
		$this->_CI->load->add_package_path($this->get_path());

		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Set Template
	 *
	 * Sometimes we want to use different template for different page for example,
	 * 404 template, login template, full-width template, sidebar template, etc.
	 * so, use this function.
	 *
	 * @param  string $template
	 * @return CI_Theme
	 */
	public function set_template($template = 'default')
	{
		// strip file extension if set - default extension is .php
		$template = pathinfo($template, PATHINFO_FILENAME);
		$this->template = $this->_CI->security->sanitize_filename($template);

		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Get Theme Path
	 *
	 * Return the absolute pathname for the current theme
	 *
	 * @param  string $uri
	 * @return string
	 */
	public function get_path($uri = '')
	{
		if ($uri)
		{
			$extenstion = pathinfo($uri, PATHINFO_EXTENSION);
			$uri = ($extenstion === '') ? $uri.'.php' : $uri;
		}

		return realpath(FCPATH."themes/{$this->theme}/".ltrim($uri, '/'));
	}

	// --------------------------------------------------------------------

	/**
	 * Get Theme Url
	 *
	 * Return the URL for the current theme
	 *
	 * @param  string $uri
	 * @return string
	 */
	public function get_url($uri = '')
	{
		return base_url("themes/{$this->theme}/".ltrim($uri, '/'));
	}

	// --------------------------------------------------------------------

	/**
	 * Set Theme Error
	 *
	 * @param  string $error
	 * @return CI_Theme
	 */
	public function set_error($error)
	{
		$this->error = $error;
		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Get Theme Error
	 *
	 * @return string
	 */
	public function get_error()
	{
		return $this->error;
	}

	// --------------------------------------------------------------------

	/**
	 * Set Page Title
	 *
	 * @param  string $title
	 * @return CI_Theme
	 */
	public function set_title($title)
	{
		$this->data['title'] = $title;

		// check wether page heading has been set or has a value if not, then
		// set page title as heading
		$this->data['heading'] = isset($this->data['heading'])
			? $this->data['heading']
			: $title;

		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Set Page Heading
	 *
	 * Sometime, we want to have page header different from page title so, use
	 * this method instead.
	 *
	 * @param  string $heading
	 * @return CI_Theme
	 */
	public function set_heading($heading)
	{
		$this->data['heading'] = $heading;
		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Set Page Description
	 *
	 * @param  string $description
	 * @return CI_Theme
	 */
	public function set_description($description)
	{
		$this->data['description'] = $description;
		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Set Page Keywords
	 *
	 * @param  string $keywords
	 * @return CI_Theme
	 */
	public function set_keywords($keywords)
	{
		$this->data['keywords'] = $keywords;
		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Set Variables
	 *
	 * Once variables are set they become available within template and views.
	 * Global set variables can be accessed from the controller class.
	 *
	 * @param  array  $vars
	 * @param  string $val
	 * @param  bool   $global
	 * @return CI_Theme
	 */
	public function set_variables($vars, $val = '', $global = FALSE)
	{
		is_string($vars) && $vars = [$vars => $val];

		foreach ($vars as $key => $val)
		{
			// set property global
			if ($global === TRUE)
			{
				$this->_CI->load->vars($key, $val);
			}
			else
			{
				$this->data[$key] = $val;
			}
		}

		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Get Variable
	 *
	 * Check if a variable is set and retrieve it from local data array or
	 * global variables.
	 *
	 * @param  string $key
	 * @param  bool   $global
	 * @return void
	 */
	public function get_variable($key, $global = FALSE)
	{
		if ($global === TRUE)
		{
			return $this->_CI->load->get_var($key);
		}

		return isset($this->data[$key]) ? $this->data[$key] : NULL;
	}

	// --------------------------------------------------------------------

	/**
	 * Add CSS Files
	 *
	 * This function used to easily add css files to be included in a template.
	 * With this function, we can just pass css file name as parameter and it
	 * will use default css path for the current theme.
	 *
	 * @param  mixed $css_files
	 * @param  bool  $prepend
	 * @return CI_Theme
	 */
	public function add_css($css_files, $prepend = FALSE)
	{
		$result = array();
		foreach (array_filter((array) $css_files) as $file)
		{
			if ($file = $this->_prepare_include_file($file))
			{
				$result[sha1($file)] = $file;
			}
		}

		if ($result)
		{
			$this->css_files = ($prepend == FALSE)
				? array_merge($this->css_files, $result)
				: array_merge($result, $this->css_files);
		}

		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Add JS Files
	 *
	 * This function used to easily add js files to be included in a template.
	 * With this function, we can just pass js file name as parameter and it
	 * will use default js path for the current theme.
	 *
	 * @param  mixed $js_files
	 * @param  bool  $prepend
	 * @return CI_Theme
	 */
	public function add_js($js_files, $prepend = FALSE)
	{
		$result = array();
		foreach (array_filter((array) $js_files) as $file)
		{
			if ($file = $this->_prepare_include_file($file))
			{
				$result[sha1($file)] = $file;
			}
		}

		if ($result)
		{
			$this->js_files = ($prepend == FALSE)
				? array_merge($this->js_files, $result)
				: array_merge($result, $this->js_files);
		}

		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Add JSi18n Files
	 *
	 * This function used to easily add jsi18n files to be included in a template.
	 * With this function, we can just pass jsi18n file name as parameter and it
	 * will use default js path for the current theme.
	 *
	 * @param  mixed $js_files
	 * @return CI_Theme
	 */
	public function add_jsi18n($js_files)
	{
		// load the html helper
		$this->_CI->load->helper('html');

		foreach (array_filter((array) $js_files) as $file)
		{
			if ($file = $this->_prepare_include_file($file, TRUE))
			{
				$this->js_i18n[sha1($file)] = jsi18n($file);
			}
		}

		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Remove CSS Files
	 *
	 * This function used to easily remove css files from included in a template.
	 * Work the same way as add_css method, but instead of append/prepend files,
	 * unset them.
	 *
	 * @param  mixed $css_files
	 * @return CI_Theme
	 */
	public function remove_css($css_files)
	{
		foreach (array_filter((array) $css_files) as $file)
		{
			if ($file = $this->_prepare_include_file($file))
			{
				unset($this->css_files[sha1($file)]);
			}
		}

		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Remove JS Files
	 *
	 * This function used to easily remove js files from included in a template.
	 * Work the same way as add_js method, but instead of append/prepend files,
	 * unset them.
	 *
	 * @param  mixed $js_files
	 * @return CI_Theme
	 */
	public function remove_js($js_files)
	{
		foreach (array_filter((array) $js_files) as $file)
		{
			if ($file = $this->_prepare_include_file($file))
			{
				unset($this->js_files[sha1($file)]);
			}
		}

		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Theme View
	 *
	 * Load theme view file with provided variables data
	 *
	 * @param  string $view
	 * @param  array  $vars
	 * @param  bool   $return
	 * @return object|string
	 */
	public function view($view, $vars = array(), $return = FALSE)
	{
		return $this->_CI->load->view($view, $vars, $return);
	}

	// --------------------------------------------------------------------

	/**
	 * Render Output
	 *
	 * This method builds everything and returns the final output
	 *
	 * @param  string $view    View to load
	 * @param  array  $content Array of content data to pass to view
	 * @param  bool   $return  Return the current output string
	 * @return mixed
	 */
	public function render($view, $content = array(), $return = FALSE)
	{
		$file_path = $this->get_path("templates/{$this->template}");
		if ( ! file_exists($file_path))
		{
			throw new Exception('The requested template file "'.$file_path.'" could not be found.');
		}

		// try to include initialization and theme related functions files
		if (file_exists($this->get_path('init')))
		{
			include($this->get_path('init'));
		}
		if (file_exists($this->get_path('functions')))
		{
			include($this->get_path('functions'));
		}

		// prepare view data
		$this->data = array_merge($this->data, array(
			'content'   => $this->view($view, $content, TRUE),
			'css_files' => $this->css_files,
			'js_files'  => $this->js_files,
			'js_i18n'   => $this->js_i18n,
		));

		$this->_CI->load->vars($this->data);

		$output = $this->_CI->load->file($file_path, TRUE);

		if ($return === TRUE)
		{
			return $output;
		}

		$this->_CI->output->set_output($output);
	}

	// --------------------------------------------------------------------

	/**
	 * Private methods - typically, some kind of helpers, used inside
	 * other public methods in the class.
	 */

	// --------------------------------------------------------------------

	/**
	 * _prepare_include_file
	 *
	 * @param  string $file
	 * @param  bool   $is_i18n
	 * @return mixed
	 */
	private function _prepare_include_file($file, $is_i18n = FALSE)
	{
		$result = NULL;

		// process only if provided file value is not url, otherwise, return
		// file w/o any modifications
		if ( ! preg_match('#^(\w+:)?//#i', $file))
		{
			$file = trim($file, '/');

			// check if provided file is external and can be included
			if (is_file(FCPATH.$file))
			{
				if ($filemtime = @filemtime(FCPATH.$file))
				{
					$result = ($is_i18n === FALSE)
						? base_url("{$file}?t={$filemtime}")
						: realpath(FCPATH.$file);
				}
			}
			else
			{
				$extension = strtolower(substr(strrchr($file, '.'), 1));
				$uri = "assets/{$extension}/{$file}";

				if ($filemtime = @filemtime($this->get_path($uri)))
				{
					$result = ($is_i18n === FALSE)
						? $this->get_url("{$uri}?t={$filemtime}")
						: $this->get_path($uri);
				}
			}
		}
		else
		{
			$result = $file;
		}

		return $result;
	}

}
