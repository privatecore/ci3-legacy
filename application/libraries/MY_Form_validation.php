<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * MY_Form_validation class
 */
class MY_Form_validation extends CI_Form_validation {

	/**
	 * Initialize Form_Validation class
	 *
	 * @param  array $rules
	 * @return void
	 */
	public function __construct($rules = array())
	{
		parent::__construct($rules);
	}

	// --------------------------------------------------------------------

	/**
	 * Valid date
	 *
	 * @param  string $str
	 * @param  string $format https://secure.php.net/manual/en/function.date.php
	 * @return bool
	 */
	public function valid_date($str, $format)
	{
		$date = DateTime::createFromFormat($format, $str);
		return $date && ($date->format($format) === $str);
	}

	// --------------------------------------------------------------------

	/**
	 * Valid phone number
	 *
	 * @param  string $str
	 * @return bool
	 */
	public function valid_phone($str)
	{
		return (bool) preg_match('/^((\+?[1-9])[\- ]?)?(\(?\d{3,4}\)?[\- ]?)?[\d\- ]{5,10}$/', $str);
	}

	// --------------------------------------------------------------------

	/**
	 * Valid price
	 *
	 * @param  string $str
	 * @return bool
	 */
	public function valid_price($str)
	{
		return (bool) preg_match('/^[0-9]+(\.[0-9]{0,2})?$/', $str);
	}

	// --------------------------------------------------------------------

	/**
	 * Valid reCaptcha response
	 *
	 * @param  string $str
	 * @return bool
	 */
	public function valid_recaptcha($str)
	{
		$this->CI->load->library('recaptcha');

		// was there a reCAPTCHA response?
		$response = $this->CI->recaptcha->verify($this->CI->input->ip_address(), $str);

		return (isset($response['success']) && $response['success']);
	}

}
