<?php defined('BASEPATH') OR exit('No direct script access allowed');

if ( ! function_exists('compress_html'))
{
	/**
	 * Compress HTML to output
	 *
	 * @see https://stackoverflow.com/a/5324014
	 * @return void
	 */
	function compress_html()
	{
		// set PCRE recursion limit to sane value = STACKSIZE / 500
		// 8MB stack. *nix
		ini_set('pcre.recursion_limit', 16777);

		$CI =& get_instance();

		$buffer = $CI->output->get_output();

		$re = '%            # Collapse whitespace everywhere but in blacklisted elements.
			(?>             # Match all whitespans other than single space.
				[^\S ]\s*   # Either one [\t\r\n\f\v] and zero or more ws,
			| \s{2,}        # or two or more consecutive-any-whitespace.
			)               # Note: The remaining regex consumes no text at all...
			(?=             # Ensure we are not in a blacklist tag.
				[^<]*+      # Either zero or more non-"<" {normal*}
				(?:         # Begin {(special normal*)*} construct
					<       # or a < starting a non-blacklist tag.
					(?!/?(?:textarea|pre|script)\b)
					[^<]*+  # more non-"<" {normal*}
				)*+         # Finish "unrolling-the-loop"
				(?:         # Begin alternation group.
					<       # Either a blacklist start tag.
					(?>textarea|pre|script)\b
				| \z        # or end of file.
				)           # End alternation group.
			)               # If we made it here, we are not in a blacklist tag.
		%Six';

		$new_buffer = preg_replace($re, ' ', $buffer);

		// fallback to show output w/o regex replacement if processing goes wrong
		if ($new_buffer === NULL)
		{
			$new_buffer = $buffer;
		}

		$CI->output->set_output($new_buffer);
		$CI->output->_display();
	}
}
