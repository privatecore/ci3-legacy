<?php defined('BASEPATH') OR exit('No direct script access allowed');

if ( ! function_exists('form_multiselect'))
{
	/**
	 * Multi-Select Menu
	 *
	 * @param	string
	 * @param	array
	 * @param	mixed
	 * @param	mixed
	 * @return	string
	 */
	function form_multiselect($name = '', $options = array(), $selected = array(), $extra = '', $attr = array())
	{
		$extra = _attributes_to_string($extra);
		if (stripos($extra, 'multiple') === FALSE)
		{
			$extra .= ' multiple="multiple"';
		}

		return form_dropdown($name, $options, $selected, $extra, $attr);
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('form_dropdown'))
{
	/**
	 * Dropdown Menu
	 *
	 * @param  mixed $data
	 * @param  mixed $options
	 * @param  mixed $selected
	 * @param  mixed $extra
	 * @param  mixed $attr
	 * @return string
	 */
	function form_dropdown($data = '', $options = array(), $selected = array(), $extra = '', $attr = array())
	{
		$defaults = array();

		if (is_array($data))
		{
			if (isset($data['selected']))
			{
				$selected = $data['selected'];
				unset($data['selected']); // select tags don't have a selected attribute
			}

			if (isset($data['options']))
			{
				$options = $data['options'];
				unset($data['options']); // select tags don't use an options attribute
			}
		}
		else
		{
			$defaults = array('name' => $data);
		}

		is_array($selected) OR $selected = array($selected);
		is_array($options) OR $options = array($options);

		// If no selected state was submitted we will attempt to set it automatically
		if (empty($selected))
		{
			if (is_array($data))
			{
				if (isset($data['name'], $_POST[$data['name']]))
				{
					$selected = array($_POST[$data['name']]);
				}
			}
			elseif (isset($_POST[$data]))
			{
				$selected = array($_POST[$data]);
			}
		}

		$extra = _attributes_to_string($extra);

		$multiple = (count($selected) > 1 && stripos($extra, 'multiple') === FALSE) ? ' multiple="multiple"' : '';

		$form = '<select ' . rtrim(_parse_form_attributes($data, $defaults)) . $extra . $multiple . ">\n";

		foreach ($options as $key => $val)
		{
			$key = (string)$key;

			if (is_array($val))
			{
				if (empty($val))
				{
					continue;
				}

				$form .= '<optgroup label="' . $key . "\">\n";

				foreach ($val as $optgroup_key => $optgroup_val)
				{
					$sel = in_array($optgroup_key, $selected) ? ' selected="selected"' : '';
					$form .= '<option value="' . html_escape($optgroup_key) . '"' . $sel . '>'
						. (string)$optgroup_val . "</option>\n";
				}

				$form .= "</optgroup>\n";
			}
			else
			{
				$attr_html = '';

				// manage options extra attributes
				if (array_key_exists($key, $attr))
				{
					if (is_array($attr[$key]))
					{
						foreach ($attr[$key] as $attr_name => $attr_value)
						{
							$attr_html .= ' ' . html_escape($attr_name) . '="' . (string)$attr_value . '"' . ' ';
						}
					}
					else
					{
						$attr_html = $attr[$key];
					}
				}

				$form .= '<option value="' . html_escape($key) . '"'
					. (in_array($key, $selected) ? ' selected="selected"' : '')
					. $attr_html . '>'
					. (string)$val . "</option>\n";
			}
		}

		return $form . "</select>\n";
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('form_wysiwyg'))
{
	/**
	 * Textarea Field
	 *
	 * @param  mixed  $data
	 * @param  string $value
	 * @param  mixed  $extra
	 * @return string
	 */
	function form_wysiwyg($data = '', $value = '', $extra = '')
	{
		$defaults = array(
			'name' => is_array($data) ? '' : $data,
			'cols' => '40',
			'rows' => '10',
		);

		if ( ! is_array($data) OR ! isset($data['value']))
		{
			$val = $value;
		}
		else
		{
			$val = $data['value'];
			unset($data['value']); // textareas don't use the value attribute
		}

		return '<textarea '._parse_form_attributes($data, $defaults)._attributes_to_string($extra).'>'
			.$val
			."</textarea>\n";
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('set_multiselect'))
{
	/**
	 * Set Multi-Select
	 *
	 * Let's you set the selected value of a <select> menu via data in the POST array.
	 * If Form Validation is active it retrieves the info from the validation class
	 *
	 * @param	string	$field		Field name
	 * @param	string	$default	Default value
	 * @param	bool	$html_escape	Whether to escape HTML special characters or not
	 * @return	array
	 */
	function set_multiselect($field, $default = '', $html_escape = TRUE)
	{
		$CI =& get_instance();

		$value = $CI->input->post($field, FALSE);

		isset($value) OR $value = $default;
		return ($html_escape) ? html_escape($value) : $value;
	}
}
