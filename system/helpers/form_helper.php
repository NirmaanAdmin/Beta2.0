<?php
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP
 *
 * This content is released under the MIT License (MIT)
 *
 * Copyright (c) 2014 - 2019, British Columbia Institute of Technology
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @package	CodeIgniter
 * @author	EllisLab Dev Team
 * @copyright	Copyright (c) 2008 - 2014, EllisLab, Inc. (https://ellislab.com/)
 * @copyright	Copyright (c) 2014 - 2019, British Columbia Institute of Technology (https://bcit.ca/)
 * @license	https://opensource.org/licenses/MIT	MIT License
 * @link	https://codeigniter.com
 * @since	Version 1.0.0
 * @filesource
 */
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * CodeIgniter Form Helpers
 *
 * @package		CodeIgniter
 * @subpackage	Helpers
 * @category	Helpers
 * @author		EllisLab Dev Team
 * @link		https://codeigniter.com/user_guide/helpers/form_helper.html
 */

// ------------------------------------------------------------------------

if ( ! function_exists('form_open'))
{
	/**
	 * Form Declaration
	 *
	 * Creates the opening portion of the form.
	 *
	 * @param	string	the URI segments of the form destination
	 * @param	array	a key/value pair of attributes
	 * @param	array	a key/value pair hidden data
	 * @return	string
	 */
	function form_open($action = '', $attributes = array(), $hidden = array())
	{
		$CI =& get_instance();

		// If no action is provided then set to the current url
		if ( ! $action)
		{
			$action = $CI->config->site_url($CI->uri->uri_string());
		}
		// If an action is not a full URL then turn it into one
		elseif (strpos($action, '://') === FALSE)
		{
			$action = $CI->config->site_url($action);
		}

		$attributes = _attributes_to_string($attributes);

		if (stripos($attributes, 'method=') === FALSE)
		{
			$attributes .= ' method="post"';
		}

		if (stripos($attributes, 'accept-charset=') === FALSE)
		{
			$attributes .= ' accept-charset="'.strtolower(config_item('charset')).'"';
		}

		$form = '<form action="'.$action.'"'.$attributes.">\n";

		if (is_array($hidden))
		{
			foreach ($hidden as $name => $value)
			{
				$form .= '<input type="hidden" name="'.$name.'" value="'.html_escape($value).'" />'."\n";
			}
		}

		// Add CSRF field if enabled, but leave it out for GET requests and requests to external websites
		if ($CI->config->item('csrf_protection') === TRUE && strpos($action, $CI->config->base_url()) !== FALSE && ! stripos($form, 'method="get"'))
		{
			// Prepend/append random-length "white noise" around the CSRF
			// token input, as a form of protection against BREACH attacks
			if (FALSE !== ($noise = $CI->security->get_random_bytes(1)))
			{
				list(, $noise) = unpack('c', $noise);
			}
			else
			{
				$noise = mt_rand(-128, 127);
			}

			// Prepend if $noise has a negative value, append if positive, do nothing for zero
			$prepend = $append = '';
			if ($noise < 0)
			{
				$prepend = str_repeat(" ", abs($noise));
			}
			elseif ($noise > 0)
			{
				$append  = str_repeat(" ", $noise);
			}

			$form .= sprintf(
				'%s<input type="hidden" name="%s" value="%s" />%s%s',
				$prepend,
				$CI->security->get_csrf_token_name(),
				$CI->security->get_csrf_hash(),
				$append,
				"\n"
			);
		}

		return $form;
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('form_open_multipart'))
{
	/**
	 * Form Declaration - Multipart type
	 *
	 * Creates the opening portion of the form, but with "multipart/form-data".
	 *
	 * @param	string	the URI segments of the form destination
	 * @param	array	a key/value pair of attributes
	 * @param	array	a key/value pair hidden data
	 * @return	string
	 */
	function form_open_multipart($action = '', $attributes = array(), $hidden = array())
	{
		if (is_string($attributes))
		{
			$attributes .= ' enctype="multipart/form-data"';
		}
		else
		{
			$attributes['enctype'] = 'multipart/form-data';
		}

		return form_open($action, $attributes, $hidden);
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('form_hidden'))
{
	/**
	 * Hidden Input Field
	 *
	 * Generates hidden fields. You can pass a simple key/value string or
	 * an associative array with multiple values.
	 *
	 * @param	mixed	$name		Field name
	 * @param	string	$value		Field value
	 * @param	bool	$recursing
	 * @return	string
	 */
	function form_hidden($name, $value = '', $recursing = FALSE)
	{
		static $form;

		if ($recursing === FALSE)
		{
			$form = "\n";
		}

		if (is_array($name))
		{
			foreach ($name as $key => $val)
			{
				form_hidden($key, $val, TRUE);
			}

			return $form;
		}

		if ( ! is_array($value))
		{
			$form .= '<input type="hidden" name="'.$name.'" value="'.html_escape($value)."\" />\n";
		}
		else
		{
			foreach ($value as $k => $v)
			{
				$k = is_int($k) ? '' : $k;
				form_hidden($name.'['.$k.']', $v, TRUE);
			}
		}

		return $form;
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('form_input'))
{
	/**
	 * Text Input Field
	 *
	 * @param	mixed
	 * @param	string
	 * @param	mixed
	 * @return	string
	 */
	function form_input($data = '', $value = '', $extra = '')
	{
		$defaults = array(
			'type' => 'text',
			'name' => is_array($data) ? '' : $data,
			'value' => $value
		);

		return '<input '._parse_form_attributes($data, $defaults)._attributes_to_string($extra)." />\n";
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('form_password'))
{
	/**
	 * Password Field
	 *
	 * Identical to the input function but adds the "password" type
	 *
	 * @param	mixed
	 * @param	string
	 * @param	mixed
	 * @return	string
	 */
	function form_password($data = '', $value = '', $extra = '')
	{
		is_array($data) OR $data = array('name' => $data);
		$data['type'] = 'password';
		return form_input($data, $value, $extra);
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('form_upload'))
{
	/**
	 * Upload Field
	 *
	 * Identical to the input function but adds the "file" type
	 *
	 * @param	mixed
	 * @param	string
	 * @param	mixed
	 * @return	string
	 */
	function form_upload($data = '', $value = '', $extra = '')
	{
		$defaults = array('type' => 'file', 'name' => '');
		is_array($data) OR $data = array('name' => $data);
		$data['type'] = 'file';

		return '<input '._parse_form_attributes($data, $defaults)._attributes_to_string($extra)." />\n";
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('form_textarea'))
{
	/**
	 * Textarea field
	 *
	 * @param	mixed	$data
	 * @param	string	$value
	 * @param	mixed	$extra
	 * @return	string
	 */
	function form_textarea($data = '', $value = '', $extra = '')
	{
		$defaults = array(
			'name' => is_array($data) ? '' : $data,
			'cols' => '40',
			'rows' => '10'
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
			.html_escape($val)
			."</textarea>\n";
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('form_multiselect'))
{
	/**
	 * Multi-select menu
	 *
	 * @param	string
	 * @param	array
	 * @param	mixed
	 * @param	mixed
	 * @return	string
	 */
	function form_multiselect($name = '', $options = array(), $selected = array(), $extra = '')
	{
		$extra = _attributes_to_string($extra);
		if (stripos($extra, 'multiple') === FALSE)
		{
			$extra .= ' multiple="multiple"';
		}

		return form_dropdown($name, $options, $selected, $extra);
	}
}

// --------------------------------------------------------------------

if ( ! function_exists('form_dropdown'))
{
	/**
	 * Drop-down Menu
	 *
	 * @param	mixed	$data
	 * @param	mixed	$options
	 * @param	mixed	$selected
	 * @param	mixed	$extra
	 * @return	string
	 */
	function form_dropdown($data = '', $options = array(), $selected = array(), $extra = '')
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

		$form = '<select '.rtrim(_parse_form_attributes($data, $defaults)).$extra.$multiple.">\n";

		foreach ($options as $key => $val)
		{
			$key = (string) $key;

			if (is_array($val))
			{
				if (empty($val))
				{
					continue;
				}

				$form .= '<optgroup label="'.$key."\">\n";

				foreach ($val as $optgroup_key => $optgroup_val)
				{
					$sel = in_array($optgroup_key, $selected) ? ' selected="selected"' : '';
					$form .= '<option value="'.html_escape($optgroup_key).'"'.$sel.'>'
						.(string) $optgroup_val."</option>\n";
				}

				$form .= "</optgroup>\n";
			}
			else
			{
				$form .= '<option value="'.html_escape($key).'"'
					.(in_array($key, $selected) ? ' selected="selected"' : '').'>'
					.(string) $val."</option>\n";
			}
		}

		return $form."</select>\n";
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('form_checkbox'))
{
	/**
	 * Checkbox Field
	 *
	 * @param	mixed
	 * @param	string
	 * @param	bool
	 * @param	mixed
	 * @return	string
	 */
	function form_checkbox($data = '', $value = '', $checked = FALSE, $extra = '')
	{
		$defaults = array('type' => 'checkbox', 'name' => ( ! is_array($data) ? $data : ''), 'value' => $value);

		if (is_array($data) && array_key_exists('checked', $data))
		{
			$checked = $data['checked'];

			if ($checked == FALSE)
			{
				unset($data['checked']);
			}
			else
			{
				$data['checked'] = 'checked';
			}
		}

		if ($checked == TRUE)
		{
			$defaults['checked'] = 'checked';
		}
		else
		{
			unset($defaults['checked']);
		}

		return '<input '._parse_form_attributes($data, $defaults)._attributes_to_string($extra)." />\n";
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('form_radio'))
{
	/**
	 * Radio Button
	 *
	 * @param	mixed
	 * @param	string
	 * @param	bool
	 * @param	mixed
	 * @return	string
	 */
	function form_radio($data = '', $value = '', $checked = FALSE, $extra = '')
	{
		is_array($data) OR $data = array('name' => $data);
		$data['type'] = 'radio';

		return form_checkbox($data, $value, $checked, $extra);
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('form_submit'))
{
	/**
	 * Submit Button
	 *
	 * @param	mixed
	 * @param	string
	 * @param	mixed
	 * @return	string
	 */
	function form_submit($data = '', $value = '', $extra = '')
	{
		$defaults = array(
			'type' => 'submit',
			'name' => is_array($data) ? '' : $data,
			'value' => $value
		);

		return '<input '._parse_form_attributes($data, $defaults)._attributes_to_string($extra)." />\n";
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('form_reset'))
{
	/**
	 * Reset Button
	 *
	 * @param	mixed
	 * @param	string
	 * @param	mixed
	 * @return	string
	 */
	function form_reset($data = '', $value = '', $extra = '')
	{
		$defaults = array(
			'type' => 'reset',
			'name' => is_array($data) ? '' : $data,
			'value' => $value
		);

		return '<input '._parse_form_attributes($data, $defaults)._attributes_to_string($extra)." />\n";
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('form_button'))
{
	/**
	 * Form Button
	 *
	 * @param	mixed
	 * @param	string
	 * @param	mixed
	 * @return	string
	 */
	function form_button($data = '', $content = '', $extra = '')
	{
		$defaults = array(
			'name' => is_array($data) ? '' : $data,
			'type' => 'button'
		);

		if (is_array($data) && isset($data['content']))
		{
			$content = $data['content'];
			unset($data['content']); // content is not an attribute
		}

		return '<button '._parse_form_attributes($data, $defaults)._attributes_to_string($extra).'>'
			.$content
			."</button>\n";
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('form_label'))
{
	/**
	 * Form Label Tag
	 *
	 * @param	string	The text to appear onscreen
	 * @param	string	The id the label applies to
	 * @param	mixed	Additional attributes
	 * @return	string
	 */
	function form_label($label_text = '', $id = '', $attributes = array())
	{

		$label = '<label';

		if ($id !== '')
		{
			$label .= ' for="'.$id.'"';
		}

		$label .= _attributes_to_string($attributes);

		return $label.'>'.$label_text.'</label>';
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('form_fieldset'))
{
	/**
	 * Fieldset Tag
	 *
	 * Used to produce <fieldset><legend>text</legend>.  To close fieldset
	 * use form_fieldset_close()
	 *
	 * @param	string	The legend text
	 * @param	array	Additional attributes
	 * @return	string
	 */
	function form_fieldset($legend_text = '', $attributes = array())
	{
		$fieldset = '<fieldset'._attributes_to_string($attributes).">\n";
		if ($legend_text !== '')
		{
			return $fieldset.'<legend>'.$legend_text."</legend>\n";
		}

		return $fieldset;
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('form_fieldset_close'))
{
	/**
	 * Fieldset Close Tag
	 *
	 * @param	string
	 * @return	string
	 */
	function form_fieldset_close($extra = '')
	{
		return '</fieldset>'.$extra;
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('form_close'))
{
	/**
	 * Form Close Tag
	 *
	 * @param	string
	 * @return	string
	 */
	function form_close($extra = '')
	{
		return '</form>'.$extra;
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('form_prep'))
{
	/**
	 * Form Prep
	 *
	 * Formats text so that it can be safely placed in a form field in the event it has HTML tags.
	 *
	 * @deprecated	3.0.0	An alias for html_escape()
	 * @param	string|string[]	$str		Value to escape
	 * @return	string|string[]	Escaped values
	 */
	function form_prep($str)
	{
		return html_escape($str, TRUE);
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('set_value'))
{
	/**
	 * Form Value
	 *
	 * Grabs a value from the POST array for the specified field so you can
	 * re-populate an input field or textarea. If Form Validation
	 * is active it retrieves the info from the validation class
	 *
	 * @param	string	$field		Field name
	 * @param	string	$default	Default value
	 * @param	bool	$html_escape	Whether to escape HTML special characters or not
	 * @return	string
	 */
	function set_value($field, $default = '', $html_escape = TRUE)
	{
		$CI =& get_instance();

		$value = (isset($CI->form_validation) && is_object($CI->form_validation) && $CI->form_validation->has_rule($field))
			? $CI->form_validation->set_value($field, $default)
			: $CI->input->post($field, FALSE);

		isset($value) OR $value = $default;
		return ($html_escape) ? html_escape($value) : $value;
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('set_select'))
{
	/**
	 * Set Select
	 *
	 * Let's you set the selected value of a <select> menu via data in the POST array.
	 * If Form Validation is active it retrieves the info from the validation class
	 *
	 * @param	string
	 * @param	string
	 * @param	bool
	 * @return	string
	 */
	function set_select($field, $value = '', $default = FALSE)
	{
		$CI =& get_instance();

		if (isset($CI->form_validation) && is_object($CI->form_validation) && $CI->form_validation->has_rule($field))
		{
			return $CI->form_validation->set_select($field, $value, $default);
		}
		elseif (($input = $CI->input->post($field, FALSE)) === NULL)
		{
			return ($default === TRUE) ? ' selected="selected"' : '';
		}

		$value = (string) $value;
		if (is_array($input))
		{
			// Note: in_array('', array(0)) returns TRUE, do not use it
			foreach ($input as &$v)
			{
				if ($value === $v)
				{
					return ' selected="selected"';
				}
			}

			return '';
		}

		return ($input === $value) ? ' selected="selected"' : '';
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('set_checkbox'))
{
	/**
	 * Set Checkbox
	 *
	 * Let's you set the selected value of a checkbox via the value in the POST array.
	 * If Form Validation is active it retrieves the info from the validation class
	 *
	 * @param	string
	 * @param	string
	 * @param	bool
	 * @return	string
	 */
	function set_checkbox($field, $value = '', $default = FALSE)
	{
		$CI =& get_instance();

		if (isset($CI->form_validation) && is_object($CI->form_validation) && $CI->form_validation->has_rule($field))
		{
			return $CI->form_validation->set_checkbox($field, $value, $default);
		}

		// Form inputs are always strings ...
		$value = (string) $value;
		$input = $CI->input->post($field, FALSE);

		if (is_array($input))
		{
			// Note: in_array('', array(0)) returns TRUE, do not use it
			foreach ($input as &$v)
			{
				if ($value === $v)
				{
					return ' checked="checked"';
				}
			}

			return '';
		}

		// Unchecked checkbox and radio inputs are not even submitted by browsers ...
		if ($CI->input->method() === 'post')
		{
			return ($input === $value) ? ' checked="checked"' : '';
		}

		return ($default === TRUE) ? ' checked="checked"' : '';
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('set_radio'))
{
	/**
	 * Set Radio
	 *
	 * Let's you set the selected value of a radio field via info in the POST array.
	 * If Form Validation is active it retrieves the info from the validation class
	 *
	 * @param	string	$field
	 * @param	string	$value
	 * @param	bool	$default
	 * @return	string
	 */
	function set_radio($field, $value = '', $default = FALSE)
	{
		$CI =& get_instance();

		if (isset($CI->form_validation) && is_object($CI->form_validation) && $CI->form_validation->has_rule($field))
		{
			return $CI->form_validation->set_radio($field, $value, $default);
		}

		// Form inputs are always strings ...
		$value = (string) $value;
		$input = $CI->input->post($field, FALSE);

		if (is_array($input))
		{
			// Note: in_array('', array(0)) returns TRUE, do not use it
			foreach ($input as &$v)
			{
				if ($value === $v)
				{
					return ' checked="checked"';
				}
			}

			return '';
		}

		// Unchecked checkbox and radio inputs are not even submitted by browsers ...
		if ($CI->input->method() === 'post')
		{
			return ($input === $value) ? ' checked="checked"' : '';
		}

		return ($default === TRUE) ? ' checked="checked"' : '';
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('form_error'))
{
	/**
	 * Form Error
	 *
	 * Returns the error for a specific form field. This is a helper for the
	 * form validation class.
	 *
	 * @param	string
	 * @param	string
	 * @param	string
	 * @return	string
	 */
	function form_error($field = '', $prefix = '', $suffix = '')
	{
		if (FALSE === ($OBJ =& _get_validation_object()))
		{
			return '';
		}

		return $OBJ->error($field, $prefix, $suffix);
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('validation_errors'))
{
	/**
	 * Validation Error String
	 *
	 * Returns all the errors associated with a form submission. This is a helper
	 * function for the form validation class.
	 *
	 * @param	string
	 * @param	string
	 * @return	string
	 */
	function validation_errors($prefix = '', $suffix = '')
	{
		if (FALSE === ($OBJ =& _get_validation_object()))
		{
			return '';
		}

		return $OBJ->error_string($prefix, $suffix);
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('_parse_form_attributes'))
{
	/**
	 * Parse the form attributes
	 *
	 * Helper function used by some of the form helpers
	 *
	 * @param	array	$attributes	List of attributes
	 * @param	array	$default	Default values
	 * @return	string
	 */
	function _parse_form_attributes($attributes, $default)
	{
		if (is_array($attributes))
		{
			foreach ($default as $key => $val)
			{
				if (isset($attributes[$key]))
				{
					$default[$key] = $attributes[$key];
					unset($attributes[$key]);
				}
			}

			if (count($attributes) > 0)
			{
				$default = array_merge($default, $attributes);
			}
		}

		$att = '';

		foreach ($default as $key => $val)
		{
			if ($key === 'value')
			{
				$val = html_escape($val);
			}
			elseif ($key === 'name' && ! strlen($default['name']))
			{
				continue;
			}

			$att .= $key.'="'.$val.'" ';
		}

		return $att;
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('_attributes_to_string'))
{
	/**
	 * Attributes To String
	 *
	 * Helper function used by some of the form helpers
	 *
	 * @param	mixed
	 * @return	string
	 */
	function _attributes_to_string($attributes)
	{
		if (empty($attributes))
		{
			return '';
		}

		if (is_object($attributes))
		{
			$attributes = (array) $attributes;
		}

		if (is_array($attributes))
		{
			$atts = '';

			foreach ($attributes as $key => $val)
			{
				$atts .= ' '.$key.'="'.$val.'"';
			}

			return $atts;
		}

		if (is_string($attributes))
		{
			return ' '.$attributes;
		}

		return FALSE;
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('_get_validation_object'))
{
	/**
	 * Validation Object
	 *
	 * Determines what the form validation class was instantiated as, fetches
	 * the object and returns it.
	 *
	 * @return	mixed
	 */
	function &_get_validation_object()
	{
		$CI =& get_instance();

		// We set this as a variable since we're returning by reference.
		$return = FALSE;

		if (FALSE !== ($object = $CI->load->is_loaded('Form_validation')))
		{
			if ( ! isset($CI->$object) OR ! is_object($CI->$object))
			{
				return $return;
			}

			return $CI->$object;
		}

		return $return;
	}
}

function add_drp_activity_log($id, $is_create = true)
{
    $CI = &get_instance();
    if (!empty($id)) {
        $CI->db->where('formid', $id);
        $drp_data = $CI->db->get(db_prefix() . 'forms')->row();
        if (!empty($drp_data)) {
            $is_create_value = $is_create ? 'created' : 'deleted';
            $description = "<b>#" . $id . "DRP-" . date('d M, Y', strtotime($drp_data->date)) . '-' . get_project_name_by_id($drp_data->project_id) . "</b> has been " . $is_create_value . ".";
            $CI->db->insert(db_prefix() . 'module_activity_log', [
                'module_name' => 'forms',
                'rel_id' => $id,
                'description' => $description,
                'date' => date('Y-m-d H:i:s'),
                'staffid' => get_staff_user_id()
            ]);
        }
    }
    return true;
}


function normalize_activity_value($value)
{
    $value = trim((string)$value);

    if (in_array(strtolower($value), ['null', 'none', 'nil', 'n/a', '-', '--'])) {
        return '';
    }

    if ($value === '0000-00-00') {
        return '';
    }

    if (is_numeric($value)) {
        $num = (float)$value;
        return ($num == 0.0) ? '' : $num;
    }

    return strtolower($value);
}
function dpr_activity_log($form_id, $details_html)
{
    $CI = &get_instance();

    if (empty($form_id)) {
        return false;
    }

    $CI->db->where('formid', $form_id);
    $drp_data = $CI->db->get(db_prefix() . 'forms')->row();

    if (empty($drp_data)) {
        return false;
    }

    $header = "<b>#{$form_id} DRP-" .
        date('d M, Y', strtotime($drp_data->date)) .
        "-" . get_project_name_by_id($drp_data->project_id) .
        "</b> has been updated.";

    $description = $header . "<br>" . $details_html;

    $CI->db->insert(db_prefix() . 'module_activity_log', [
        'module_name' => 'forms',
        'rel_id'      => $form_id,
        'description' => $description,
        'date'        => date('Y-m-d H:i:s'),
        'staffid'     => get_staff_user_id(),
    ]);

    return true;
}
function update_dpr_form_activity_log($form_id, $old_data, $new_data)
{
    if (empty($old_data) || empty($new_data)) {
        return false;
    }

    $norm_old = array_map('normalize_activity_value', $old_data);
    $norm_new = array_map('normalize_activity_value', $new_data);

    $changes = array_diff_assoc($norm_new, $norm_old);

    if (empty($changes)) {
        return true;
    }

    $field_map = [
        'client_id'  => 'Client',
        'pmc'        => 'PMC',
        'weather'    => 'Weather',
        'consultant' => 'Consultant',
        'contractor' => 'Contractor',
        'work_stop'  => 'Work Stoppage',
    ];

    /* Value maps */
    $workStopMap = [
        'Y' => 'Yes',
        'N' => 'No',
    ];

    $weatherMap = [
        'Clear'   => 'Clear',
        'Cloudy' => 'Cloudy',
        'Rain'   => 'Rain',
    ];

    $html = "<b>DPR Detail Updated</b><ul>";

    foreach ($changes as $field => $v) {
        if (!isset($field_map[$field])) {
            continue;
        }

        $old_val = $old_data[$field] ?? 'None';
        $new_val = $new_data[$field] ?? 'None';

        /* Field-specific transformations */
        if ($field === 'client_id') {
            $old_val = $old_val ? get_company_by_userid($old_val) : 'None';
            $new_val = $new_val ? get_company_by_userid($new_val) : 'None';
        }

        if ($field === 'work_stop') {
            $old_val = $workStopMap[$old_val] ?? 'None';
            $new_val = $workStopMap[$new_val] ?? 'None';
        }

        if ($field === 'weather') {
            $old_val = $weatherMap[$old_val] ?? $old_val;
            $new_val = $weatherMap[$new_val] ?? $new_val;
        }

        $html .= "<li><b>{$field_map[$field]}</b>: {$old_val} → {$new_val}</li>";
    }

    $html .= "</ul>";

    dpr_activity_log($form_id, $html);
    return true;
}



function update_dpr_detail_activity_log($form_id, $old_data, $new_data)
{
    if (empty($old_data) || empty($new_data)) {
        return false;
    }

    $norm_old = array_map('normalize_activity_value', $old_data);
    $norm_new = array_map('normalize_activity_value', $new_data);

    $changes = array_diff_assoc($norm_new, $norm_old);

    if (empty($changes)) {
        return true;
    }

    $field_map = [
        'location'             => 'Location',
        'agency'               => 'Agency',
        'type'                 => 'Type',
        'sub_type'             => 'Sub Type',
        'work_execute'         => 'Work Executed',
        'material_consumption' => 'Material Consumption',
        'male'                 => 'Male',
        'female'               => 'Female',
        'total'                => 'Total Manpower',
        'machinery'            => 'Machinery',
        'total_machinery'      => 'Total Machinery',
    ];

    $html = "<b>DPR Detail Updated</b><ul>";

    foreach ($changes as $field => $v) {
        if (!isset($field_map[$field])) {
            continue;
        }

        $old_val = $old_data[$field] ?? 'None';
        $new_val = $new_data[$field] ?? 'None';

        if ($field == 'agency') {
            $old_val = get_agency_by_userid($old_val);
            $new_val = get_agency_by_userid($new_val);
        }

        if ($field == 'type') {
            $old_val = get_progress_report_type_listing_byid($old_val);
            $new_val = get_progress_report_type_listing_byid($new_val);
        }

        if ($field == 'sub_type') {
            $old_val = get_progress_report_sub_type_listing_byid($old_val);
            $new_val = get_progress_report_sub_type_listing_byid($new_val);
        }

        if ($field == 'machinery') {
            $old_val = get_progress_report_machinery_listing_byid($old_val);
            $new_val = get_progress_report_machinery_listing_byid($new_val);
        }


        $html .= "<li><b>{$field_map[$field]}</b>: {$old_val} → {$new_val}</li>";
    }

    $html .= "</ul>";

    dpr_activity_log($form_id, $html);
    return true;
}

function dpr_detail_added_log($form_id, $row)
{
    $html = "<b>DPR New Detail Added</b><ul>";

    foreach ($row as $key => $value) {
        if ($key == 'form_id') continue;
        $html .= "<li><b>{$key}</b>: {$value}</li>";
    }

    $html .= "</ul>";

    dpr_activity_log($form_id, $html);
}
function dpr_detail_removed_log($form_id, $row)
{
    $html = "<b>DPR Detail Removed</b><ul>";

    foreach ($row as $key => $value) {
        if ($key == 'form_id' || $key == 'id') continue;
        $html .= "<li><b>{$key}</b>: {$value}</li>";
    }

    $html .= "</ul>";

    dpr_activity_log($form_id, $html);
}

function update_forms_activity_log($form_id, $old_data, $new_data)
{
    if (empty($old_data) || empty($new_data)) {
        return false;
    }

    $norm_old = array_map('normalize_activity_value', $old_data);
    $norm_new = array_map('normalize_activity_value', $new_data);

    $changes = array_diff_assoc($norm_new, $norm_old);

    if (empty($changes)) {
        return true;
    }

    $field_map = [
        'subject'    => 'Subject',
        'project_id' => 'Project',
        'department' => 'Department',
        'assigned'   => 'Assigned To',
        'priority'   => 'Priority',
        'duedate'    => 'Due Date',
        'service'    => 'Service',
    ];

    $html = "<b>DPR Updated</b><ul>";

    foreach ($changes as $field => $v) {
        if (!isset($field_map[$field])) {
            continue;
        }

        $old_val = $old_data[$field] ?? 'None';
        $new_val = $new_data[$field] ?? 'None';

        // Human readable conversions
        if ($field === 'project_id') {
            $old_val = $old_val ? get_project_name_by_id($old_val) : 'None';
            $new_val = $new_val ? get_project_name_by_id($new_val) : 'None';
        }

        if ($field === 'assigned') {
            $old_val = $old_val ? get_staff_full_name($old_val) : 'None';
            $new_val = $new_val ? get_staff_full_name($new_val) : 'None';
        }

        if ($field === 'priority') {
            $map = [1 => 'Low', 2 => 'Medium', 3 => 'High'];
            $old_val = $map[$old_val] ?? $old_val;
            $new_val = $map[$new_val] ?? $new_val;
        }

        $html .= "<li><b>{$field_map[$field]}</b>: {$old_val} → {$new_val}</li>";
    }

    $html .= "</ul>";

    dpr_activity_log($form_id, $html);
    return true;
}

function dept_detail_added_log($form_id, $row)
{
    $html = "<b>New Department Detail Added</b><ul>";

    foreach ($row as $key => $value) {
        if ($key == 'form_id') continue;
        $html .= "<li><b>{$key}</b>: {$value}</li>";
    }

    $html .= "</ul>";

    dpr_activity_log($form_id, $html);
}

function rmc_detail_added_log($form_id, $row)
{
    $html = "<b>New RMC Detail Added</b><ul>";

    foreach ($row as $key => $value) {
        if ($key == 'form_id') continue;
        $html .= "<li><b>{$key}</b>: {$value}</li>";
    }

    $html .= "</ul>";

    dpr_activity_log($form_id, $html);
}
function material_detail_added_log($form_id, $row)
{
    $html = "<b>New Material Detail Added</b><ul>";

    foreach ($row as $key => $value) {
        if ($key == 'form_id') continue;
        $html .= "<li><b>{$key}</b>: {$value}</li>";
    }

    $html .= "</ul>";

    dpr_activity_log($form_id, $html);
}

function update_dept_detail_activity_log($form_id, $old_data, $new_data)
{
    if (empty($old_data) || empty($new_data)) {
        return false;
    }

    $norm_old = array_map('normalize_activity_value', $old_data);
    $norm_new = array_map('normalize_activity_value', $new_data);

    $changes = array_diff_assoc($norm_new, $norm_old);

    if (empty($changes)) {
        return true;
    }

    $field_map = [
        'staff'      => 'Staff',
        'attendance' => 'Attendance',
        'over_time'  => 'Over Time',
        'kharchi'    => 'Kharchi',
    ];

    $html = "<b>Department Detail Updated</b><ul>";

    foreach ($changes as $field => $v) {
        if (!isset($field_map[$field])) {
            continue;
        }

        $old_val = $old_data[$field] ?? 'None';
        $new_val = $new_data[$field] ?? 'None';

        if ($field == 'staff') {
            $old_val = get_staff_members_full_name($old_val);
            $new_val = get_staff_members_full_name($new_val);
        }

        $html .= "<li><b>{$field_map[$field]}</b>: {$old_val} → {$new_val}</li>";
    }

    $html .= "</ul>";

    dpr_activity_log($form_id, $html);
    return true;
}

function update_rmc_detail_activity_log($form_id, $old_data, $new_data)
{
    if (empty($old_data) || empty($new_data)) {
        return false;
    }

    $norm_old = array_map('normalize_activity_value', $old_data);
    $norm_new = array_map('normalize_activity_value', $new_data);

    $changes = array_diff_assoc($norm_new, $norm_old);

    if (empty($changes)) {
        return true;
    }

    $field_map = [
        'challan'    => 'Challan',
        'grade'      => 'Grade',
        'structure'  => 'Structure',
        'quantity'   => 'Quantity',
    ];

    $html = "<b>RMC Detail Updated</b><ul>";

    foreach ($changes as $field => $v) {
        if (!isset($field_map[$field])) {
            continue;
        }

        $old_val = $old_data[$field] ?? 'None';
        $new_val = $new_data[$field] ?? 'None';

        if ($field == 'grade') {
            $old_val = get_rmc_grade_full_name($old_val);
            $new_val = get_rmc_grade_full_name($new_val);
        }

        $html .= "<li><b>{$field_map[$field]}</b>: {$old_val} → {$new_val}</li>";
    }

    $html .= "</ul>";

    dpr_activity_log($form_id, $html);
    return true;
}

function update_material_detail_activity_log($form_id, $old_data, $new_data)
{
    if (empty($old_data) || empty($new_data)) {
        return false;
    }

    $norm_old = array_map('normalize_activity_value', $old_data);
    $norm_new = array_map('normalize_activity_value', $new_data);

    $changes = array_diff_assoc($norm_new, $norm_old);

    if (empty($changes)) {
        return true;
    }

    $field_map = [
        'challan'               => 'Challan',
        'supplier'              => 'Supplier',
        'material_description'  => 'Material Description',
        'total'                 => 'Total',
    ];

    $html = "<b>Material Detail Updated</b><ul>";

    foreach ($changes as $field => $v) {
        if (!isset($field_map[$field])) {
            continue;
        }

        $old_val = $old_data[$field] ?? 'None';
        $new_val = $new_data[$field] ?? 'None';

        $html .= "<li><b>{$field_map[$field]}</b>: {$old_val} → {$new_val}</li>";
    }

    $html .= "</ul>";

    dpr_activity_log($form_id, $html);
    return true;
}

function update_order_cement_activity_log($form_id, $old_data, $new_data)
{
    if (empty($old_data) || empty($new_data)) {
        return false;
    }

    $norm_old = array_map('normalize_activity_value', $old_data);
    $norm_new = array_map('normalize_activity_value', $new_data);

    $changes = array_diff_assoc($norm_new, $norm_old);

    if (empty($changes)) {
        return true;
    }

    $field_map = [
        'inward_inventory'               => 'Inward Inventory',
        'today_usage'              => 'Today Usage',
        'remaining_cement'  => 'Remaining Cement',
        'notes'                 => 'Notes',
    ];

    $html = "<b>Cement Detail Updated</b><ul>";

    foreach ($changes as $field => $v) {
        if (!isset($field_map[$field])) {
            continue;
        }

        $old_val = $old_data[$field] ?? 'None';
        $new_val = $new_data[$field] ?? 'None';

        $html .= "<li><b>{$field_map[$field]}</b>: {$old_val} → {$new_val}</li>";
    }

    $html .= "</ul>";

    dpr_activity_log($form_id, $html);
    return true;
}

function order_cement_added_log($form_id, $row)
{
    $html = "<b>New Cement Detail Added</b><ul>";

    foreach ($row as $key => $value) {
        if ($key == 'form_id') continue;
        $html .= "<li><b>{$key}</b>: {$value}</li>";
    }

    $html .= "</ul>";

    dpr_activity_log($form_id, $html);
}
function update_order_block_activity_log($form_id, $old_data, $new_data)
{
    if (empty($old_data) || empty($new_data)) {
        return false;
    }
    
    $norm_old = array_map('normalize_activity_value', $old_data);
    $norm_new = array_map('normalize_activity_value', $new_data);
    
    $changes = array_diff_assoc($norm_new, $norm_old);
    
    if (empty($changes)) {
        return true;
    }
    
    $field_map = [
        'inward_inventory_bmj' => 'Inward Inventory',
        'today_usage_bmj' => 'Today Usage',
        'remaining_cement_bmj' => 'Remaining Cement',
        'notes_bmj' => 'Notes',
    ];
    
    $html = "<b>Block/Bricks Detail Updated</b><ul>";
    
    foreach ($changes as $field => $v) {
        if (!isset($field_map[$field])) {
            continue;
        }
        
        $old_val = $old_data[$field] ?? 'None';
        $new_val = $new_data[$field] ?? 'None';
        
        $html .= "<li><b>{$field_map[$field]}</b>: {$old_val} → {$new_val}</li>";
    }
    
    $html .= "</ul>";
    dpr_activity_log($form_id, $html);
    return true;
}

function order_block_added_log($form_id, $data)
{
    $html = "<b>Block Detail Added</b><ul>";
    
    foreach ($data as $key => $value) {
        if ($key == 'form_id') continue;
        
        // Make field names human readable
        $field_name = str_replace(['_bmj', '_'], [' ', ' '], $key);
        $field_name = ucwords(str_replace('_', ' ', $field_name));
        
        $html .= "<li><b>{$field_name}</b>: {$value}</li>";
    }
    
    $html .= "</ul>";
    dpr_activity_log($form_id, $html);
}

function update_order_tile_activity_log($form_id, $old_data, $new_data)
{
    if (empty($old_data) || empty($new_data)) {
        return false;
    }
    
    $norm_old = array_map('normalize_activity_value', $old_data);
    $norm_new = array_map('normalize_activity_value', $new_data);
    
    $changes = array_diff_assoc($norm_new, $norm_old);
    
    if (empty($changes)) {
        return true;
    }
    
    $field_map = [
        'inward_inventory_ta' => 'Inward Inventory',
        'today_usage_ta' => 'Today Usage',
        'remaining_cement_ta' => 'Remaining Cement',
        'notes_ta' => 'Notes',
    ];
    
    $html = "<b>Tile Detail Updated</b><ul>";
    
    foreach ($changes as $field => $v) {
        if (!isset($field_map[$field])) {
            continue;
        }
        
        $old_val = $old_data[$field] ?? 'None';
        $new_val = $new_data[$field] ?? 'None';
        
        $html .= "<li><b>{$field_map[$field]}</b>: {$old_val} → {$new_val}</li>";
    }
    
    $html .= "</ul>";
    dpr_activity_log($form_id, $html);
    return true;
}

function order_tile_added_log($form_id, $data)
{
    $html = "<b>Tile Detail Added</b><ul>";
    
    foreach ($data as $key => $value) {
        if ($key == 'form_id') continue;
        
        // Make field names human readable
        $field_name = str_replace('_ta', '', $key);
        $field_name = str_replace('_', ' ', $field_name);
        $field_name = ucwords($field_name);
        
        $html .= "<li><b>{$field_name}</b>: {$value}</li>";
    }
    
    $html .= "</ul>";
    dpr_activity_log($form_id, $html);
}

function update_order_coupler_activity_log($form_id, $old_data, $new_data)
{
    if (empty($old_data) || empty($new_data)) {
        return false;
    }
    
    $norm_old = array_map('normalize_activity_value', $old_data);
    $norm_new = array_map('normalize_activity_value', $new_data);
    
    $changes = array_diff_assoc($norm_new, $norm_old);
    
    if (empty($changes)) {
        return true;
    }
    
    $field_map = [
        'inward_inventory_ca' => 'Inward Inventory',
        'today_usage_ca' => 'Today Usage',
        'remaining_cement_ca' => 'Remaining Cement',
        'notes_ca' => 'Notes',
        'coupler_type' => 'Coupler Type',
    ];
    
    $html = "<b>Coupler Detail Updated</b><ul>";
    
    foreach ($changes as $field => $v) {
        if (!isset($field_map[$field])) {
            continue;
        }
        
        $old_val = $old_data[$field] ?? 'None';
        $new_val = $new_data[$field] ?? 'None';
        
        $html .= "<li><b>{$field_map[$field]}</b>: {$old_val} → {$new_val}</li>";
    }
    
    $html .= "</ul>";
    dpr_activity_log($form_id, $html);
    return true;
}

function order_coupler_added_log($form_id, $data)
{
    $html = "<b>Coupler Detail Added</b><ul>";
    
    foreach ($data as $key => $value) {
        if ($key == 'form_id') continue;
        
        // Make field names human readable
        $field_name = str_replace('_ca', '', $key);
        $field_name = str_replace('_', ' ', $field_name);
        $field_name = ucwords($field_name);
        
        $html .= "<li><b>{$field_name}</b>: {$value}</li>";
    }
    
    $html .= "</ul>";
    dpr_activity_log($form_id, $html);
}
function update_order_wire_coupler_activity_log($form_id, $old_data, $new_data)
{
    if (empty($old_data) || empty($new_data)) {
        return false;
    }
    
    $norm_old = array_map('normalize_activity_value', $old_data);
    $norm_new = array_map('normalize_activity_value', $new_data);
    
    $changes = array_diff_assoc($norm_new, $norm_old);
    
    if (empty($changes)) {
        return true;
    }
    
    $field_map = [
        'inward_inventory_wi' => 'Inward Inventory',
        'today_usage_wi' => 'Today Usage',
        'remaining_cement_wi' => 'Remaining Cement',
        'notes_wi' => 'Notes',
        'wire_type' => 'Wire Type',
    ];
    
    $html = "<b>Wire/Coupler Detail Updated</b><ul>";
    
    foreach ($changes as $field => $v) {
        if (!isset($field_map[$field])) {
            continue;
        }
        
        $old_val = $old_data[$field] ?? 'None';
        $new_val = $new_data[$field] ?? 'None';
        
        $html .= "<li><b>{$field_map[$field]}</b>: {$old_val} → {$new_val}</li>";
    }
    
    $html .= "</ul>";
    dpr_activity_log($form_id, $html);
    return true;
}
function order_wire_coupler_added_log($form_id, $data)
{
    $html = "<b>Wire/Coupler Detail Added</b><ul>";
    
    foreach ($data as $key => $value) {
        if ($key == 'form_id') continue;
        
        // Make field names human readable
        $field_name = str_replace('_wi', '', $key);
        $field_name = str_replace('_', ' ', $field_name);
        $field_name = ucwords($field_name);
        
        $html .= "<li><b>{$field_name}</b>: {$value}</li>";
    }
    
    $html .= "</ul>";
    dpr_activity_log($form_id, $html);
}
