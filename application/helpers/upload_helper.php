<?php defined('BASEPATH') OR exit('No direct script access allowed');

if ( ! function_exists('do_upload'))
{
	/**
	 * do_upload
	 *
	 * @param  string $key
	 * @param  array  $config
	 * @return mixed
	 */
	function do_upload($key, $config = [])
	{
		$CI =& get_instance();

		// load the upload library
		$CI->load->library('upload');

		$CI->upload->initialize($config);

		$files = $CI->input->files($key);
		if (is_array($files['name']))
		{
			$upload_data = [];
			for ($i = 0; $i < count($files['name']); $i++)
			{
				// overwrite the default $_FILES array with a single file's data
				// to make the $_FILES array consumable by the upload library
				$_FILES['userfile']['name']     = $files['name'][$i];
				$_FILES['userfile']['type']     = $files['type'][$i];
				$_FILES['userfile']['tmp_name'] = $files['tmp_name'][$i];
				$_FILES['userfile']['error']    = $files['error'][$i];
				$_FILES['userfile']['size']     = $files['size'][$i];

				if ($CI->upload->do_upload('userfile') === FALSE)
				{
					continue;
				}

				$upload_data[] = $CI->upload->data();
			}

			return $upload_data ?: $CI->upload->display_errors_plain();
		}
		else
		{
			if ($CI->upload->do_upload($key) === FALSE)
			{
				return $CI->upload->display_errors_plain();
			}

			return $CI->upload->data();
		}
	}
}

// --------------------------------------------------------------------

if ( ! function_exists('prepare_upload'))
{
	/**
	 * prepare_upload
	 *
	 * @param  array $upload_data
	 * @param  array $config
	 * @return array
	 */
	function prepare_upload($upload_data, $config = [])
	{
		$CI =& get_instance();

		// load the image_lib library
		$CI->load->library('image_lib');

		// check for data is multi-dimensional array
		if (isset($upload_data[0]) && count($upload_data) !== count($upload_data, COUNT_RECURSIVE))
		{
			foreach ($upload_data as $idx => $data)
			{
				$CI->image_lib->clear();
				$CI->image_lib->initialize(array_merge($config, [
					'source_image' => $data['full_path'],
					'new_image'    => $data['full_path'],
				]));

				if ($CI->image_lib->resize() !== TRUE)
				{
					@unlink($data['full_path']);
					unset($upload_data[$idx]);
				}
			}
		}
		else
		{
			$CI->image_lib->clear();
			$CI->image_lib->initialize(array_merge($config, [
				'source_image' => $upload_data['full_path'],
				'new_image'    => $upload_data['full_path'],
			]));

			if ($CI->image_lib->resize() !== TRUE)
			{
				@unlink($upload_data['full_path']);
				unset($upload_data);
			}
		}

		return $upload_data;
	}
}

// --------------------------------------------------------------------

if ( ! function_exists('cleanup_upload'))
{
	/**
	 * cleanup_upload
	 *
	 * @param  array $upload_data
	 * @return bool
	 */
	function cleanup_upload($upload_data)
	{
		// do nothing if upload data is empty or not array
		if (empty($upload_data) OR ! is_array($upload_data))
		{
			return FALSE;
		}
		if (count($upload_data) == count($upload_data, COUNT_RECURSIVE))
		{
			$upload_data = [$upload_data];
		}

		$files = array_column($upload_data, 'full_path');
		foreach ($files as $file)
		{
			@unlink($file);
		}

		return TRUE;
	}
}
