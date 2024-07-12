<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * MY_Upload class
 */
class MY_Upload extends CI_Upload {

	/**
	 * Constructor
	 *
	 * @param  array $config
	 * @return void
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);
	}

	// --------------------------------------------------------------------

	/**
	 * Finalized Data Array
	 *
	 * Returns an associative array containing all of the information
	 * related to the upload, allowing the developer easy access in one array.
	 *
	 * @param  string $index
	 * @return mixed
	 */
	public function data($index = NULL)
	{
		$data = array(
			'file_name'      => $this->file_name,
			'file_type'      => $this->file_type,
			'file_path'      => $this->upload_path,
			'full_path'      => $this->upload_path . $this->file_name,
			'raw_name'       => substr($this->file_name, 0, -strlen($this->file_ext)),
			'orig_name'      => $this->orig_name,
			'client_name'    => $this->client_name,
			'file_ext'       => ltrim($this->file_ext, '.'),
			'file_size'      => filesize($this->upload_path . $this->file_name),
			'is_image'       => $this->is_image(),
			'image_width'    => $this->image_width,
			'image_height'   => $this->image_height,
			'image_type'     => $this->image_type,
			'image_size_str' => $this->image_size_str,
		);

		if ( ! empty($index))
		{
			return isset($data[$index]) ? $data[$index] : NULL;
		}

		return $data;
	}

	// --------------------------------------------------------------------

	/**
	 * Display the error messages with separator
	 *
	 * @param  string $separator
	 * @return string
	 */
	public function display_errors_plain($separator = '<br>')
	{
		return (count($this->error_msg) > 0) ? implode($separator, $this->error_msg) : '';
	}

}
