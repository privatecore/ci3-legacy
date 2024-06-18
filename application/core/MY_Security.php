<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * MY_Security class
 */
class MY_Security extends CI_Security {

	/**
	 * Class constructor
	 *
	 * @return	void
	 */
	public function __construct()
	{
		parent::__construct();
	}

	// --------------------------------------------------------------------

	/**
	 * Sanitize Naughty HTML
	 *
	 * Callback method for xss_clean() to remove naughty HTML elements.
	 *
	 * @used-by	CI_Security::xss_clean()
	 * @param	array	$matches
	 * @return	string
	 */
	protected function _sanitize_naughty_html($matches)
	{
		static $naughty_tags    = array(
			'alert', 'prompt', 'confirm', 'applet', 'audio', 'basefont', 'base', 'behavior', 'bgsound',
			'blink', 'body', 'embed', 'expression', 'form', 'frameset', 'frame', 'head', 'html', 'ilayer',
			'isindex', 'layer', 'link', 'meta', 'keygen', 'object', 'plaintext', 'script', 'title',
			'math', 'xml', 'xss'
		);

		static $evil_attributes = array(
			'on\w+', 'xmlns', 'formaction', 'form', 'xlink:href', 'FSCommand', 'seekSegmentTime'
		);

		// First, escape unclosed tags
		if (empty($matches['closeTag']))
		{
			return '&lt;'.$matches[1];
		}
		// Is the element that we caught naughty? If so, escape it
		elseif (in_array(strtolower($matches['tagName']), $naughty_tags, TRUE))
		{
			return '&lt;'.$matches[1].'&gt;';
		}
		// For other tags, see if their attributes are "evil" and strip those
		elseif (isset($matches['attributes']))
		{
			// We'll store the already filtered attributes here
			$attributes = array();

			// Attribute-catching pattern
			$attributes_pattern = '#'
				.'(?<name>[^\s\042\047>/=]+)' // attribute characters
				// optional attribute-value
				.'(?:\s*=(?<value>[^\s\042\047=><`]+|\s*\042[^\042]*\042|\s*\047[^\047]*\047|\s*(?U:[^\s\042\047=><`]*)))' // attribute-value separator
				.'#i';

			// Blacklist pattern for evil attribute names
			$is_evil_pattern = '#^('.implode('|', $evil_attributes).')$#i';

			// Each iteration filters a single attribute
			do
			{
				// Strip any non-alpha characters that may precede an attribute.
				// Browsers often parse these incorrectly and that has been a
				// of numerous XSS issues we've had.
				$matches['attributes'] = preg_replace('#^[^a-z]+#i', '', $matches['attributes']);

				if ( ! preg_match($attributes_pattern, $matches['attributes'], $attribute, PREG_OFFSET_CAPTURE))
				{
					// No (valid) attribute found? Discard everything else inside the tag
					break;
				}

				if (
					// Is it indeed an "evil" attribute?
					preg_match($is_evil_pattern, $attribute['name'][0])
					// Or does it have an equals sign, but no value and not quoted? Strip that too!
					OR (trim($attribute['value'][0]) === '')
				)
				{
					$attributes[] = 'xss=removed';
				}
				else
				{
					$attributes[] = $attribute[0][0];
				}

				$matches['attributes'] = substr($matches['attributes'], $attribute[0][1] + strlen($attribute[0][0]));
			}
			while ($matches['attributes'] !== '');

			$attributes = empty($attributes)
				? ''
				: ' '.implode(' ', $attributes);
			return '<'.$matches['slash'].$matches['tagName'].$attributes.'>';
		}

		return $matches[0];
	}

}
