<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * This is a PHP library that handles calling reCAPTCHA.
 *    - Documentation and latest version
 *          https://developers.google.com/recaptcha/docs/php
 *    - Get a reCAPTCHA API Key
 *          https://www.google.com/recaptcha/admin/create
 *    - Discussion group
 *          http://groups.google.com/group/recaptcha
 *
 * @copyright Copyright (c) 2014, Google Inc.
 * @link      http://www.google.com/recaptcha
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

/**
 * Recaptcha class
 */
class Recaptcha {

	/**
	 * CI Singleton
	 *
	 * @var	object
	 */
	protected $_CI;

	/**
	 * The secret key authorizes communication between your application backend
	 * and the reCAPTCHA server to verify the user's response
	 *
	 * @var string
	 */
	protected $secret;

	/**
	 * reCAPTCHA verify and api url
	 *
	 * @var string
	 */
	protected $verify_url = 'https://www.google.com/recaptcha/api/siteverify';

	// --------------------------------------------------------------------

	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->_CI = &get_instance();

		// load the recaptcha config
		$this->_CI->load->config('recaptcha');

		$this->secret = $this->_CI->config->item('recaptcha_secret_key');

		if (empty($this->secret))
		{
			show_error('To use reCAPTCHA get an API key from: https://www.google.com/recaptcha/admin.');
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Calls the reCAPTCHA siteverify API to verify whether the user passes
	 * CAPTCHA test
	 *
	 * @param string $remote_ip IP address of end user
	 * @param string $response  Response string from recaptcha verification
	 * @return object
	 */
	public function verify($remote_ip, $response)
	{
		$result = [];

		// discard empty solution submissions
		if (empty($response))
		{

			$result['success'] = FALSE;
			$result['error-codes'] = 'missing-input-response';
		}
		else
		{
			$verify = json_decode($this->request(
				[
					'secret'   => $this->secret,
					'remoteip' => $remote_ip,
					'response' => $response
				]
			), TRUE);

			if (isset($verify['success']) && $verify['success'] == TRUE)
			{
				$result['success'] = TRUE;
			}
			else
			{
				$result['success'] = FALSE;
				$result['error-codes'] = isset($verify['error-codes'])
					? $verify['error-codes']
					: 'connection-failed';
			}
		}

		return $result;
	}

	// --------------------------------------------------------------------

	/**
	 * Submit the POST request with the specified parameters
	 *
	 * @param array $params Request parameters to be sent
	 * @return string
	 */
	protected function request($params)
	{
		$options = [
			'http' => [
				'header' => "Content-type: application/x-www-form-urlencoded\r\n",
				'method' => 'POST',
				'content' => http_build_query($params, '', '&'),
			],
		];

		$context = stream_context_create($options);
		$response = @file_get_contents($this->verify_url, FALSE, $context);

		return $response;
	}

}
