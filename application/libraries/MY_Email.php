<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * MY_Email class
 */
class MY_Email extends CI_Email {

	/**
	 * Constructor - Sets Email Preferences
	 *
	 * The constructor can be passed an array of config values
	 *
	 * @param	array	$config = array()
	 * @return	void
	 */
	public function __construct(array $config = array())
	{
		parent::__construct($config);
	}

	// --------------------------------------------------------------------

	/**
	 * Build final headers
	 *
	 * @return	void
	 */
	protected function _build_headers()
	{
		$this->set_header('X-Sender', $this->clean_email($this->_headers['From']));
		$this->set_header('X-Priority', $this->_priorities[$this->priority]);
		$this->set_header('Message-ID', $this->_get_message_id());
		$this->set_header('Mime-Version', '1.0');
	}

}
