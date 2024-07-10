<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * MY_Image_lib class
 */
class MY_Image_lib extends CI_Image_lib {

	/**
	 * Sets the current gravity suggestion for various other settings and options
	 * Can be: northwest, north, northeast, west, center, east, southwest, south, southeast
	 *
	 * @var string
	 */
	public $gravity = 'undefined';

	/**
	 * Background image color
	 *
	 * @var string
	 */
	public $bg_color = '#ffffff';

	/**
	 * Initialize Image Library
	 *
	 * @param  array $props
	 * @return void
	 */
	public function __construct($props = array())
	{
		parent::__construct($props);
	}

	// --------------------------------------------------------------------

	/**
	 * Initialize image properties
	 *
	 * Resets values in case this class is used in a loop
	 *
	 * @return	void
	 */
	public function clear()
	{
		$props = array(
			'thumb_marker', 'library_path', 'source_image', 'new_image', 'width', 'height', 'rotation_angle', 'x_axis',
			'y_axis', 'wm_text', 'wm_overlay_path', 'wm_font_path', 'wm_shadow_color', 'source_folder', 'dest_folder',
			'mime_type', 'orig_width', 'orig_height', 'image_type', 'size_str', 'full_src_path', 'full_dst_path', 'gravity'
		);

		foreach ($props as $val)
		{
			$this->$val = '';
		}

		$this->image_library      = 'gd2';
		$this->dynamic_output     = FALSE;
		$this->quality            = 90;
		$this->create_thumb       = FALSE;
		$this->thumb_marker       = '_thumb';
		$this->maintain_ratio     = TRUE;
		$this->master_dim         = 'auto';
		$this->wm_type            = 'text';
		$this->wm_x_transp        = 4;
		$this->wm_y_transp        = 4;
		$this->wm_font_size       = 17;
		$this->wm_vrt_alignment   = 'B';
		$this->wm_hor_alignment   = 'C';
		$this->wm_padding         = 0;
		$this->wm_hor_offset      = 0;
		$this->wm_vrt_offset      = 0;
		$this->wm_font_color      = '#ffffff';
		$this->wm_shadow_distance = 2;
		$this->wm_opacity         = 50;
		$this->create_fnc         = 'imagecreatetruecolor';
		$this->copy_fnc           = 'imagecopyresampled';
		$this->error_msg          = array();
		$this->wm_use_drop_shadow = FALSE;
		$this->wm_use_truetype    = FALSE;
		$this->gravity            = 'undefined';
		$this->bg_color           = '#ffffff';
	}

	// --------------------------------------------------------------------

	/**
	 * Image Extent
	 *
	 * This is a wrapper function that chooses the proper
	 * extent function based on the protocol specified
	 *
	 * @return bool
	 */
	public function extent()
	{
		$protocol = ($this->image_library === 'gd2') ? 'image_process_gd' : 'image_process_' . $this->image_library;
		return $this->$protocol('extent');
	}

	// --------------------------------------------------------------------

	/**
	 * Image Process Using GD/GD2
	 *
	 * This function will resize or crop
	 *
	 * @param	string
	 * @return	bool
	 */
	public function image_process_gd($action = 'resize')
	{
		// If the target width/height match the source, AND if the new file name is not equal to the old file name
		// we'll simply make a copy of the original with the new name... assuming dynamic rendering is off.
		if ($this->dynamic_output === FALSE && $this->orig_width === $this->width && $this->orig_height === $this->height)
		{
			if ($this->source_image !== $this->new_image && @copy($this->full_src_path, $this->full_dst_path))
			{
				chmod($this->full_dst_path, $this->file_permissions);
			}

			return TRUE;
		}

		// Let's set up our values based on the action
		if ($action === 'crop')
		{
			// Reassign the source width/height if cropping
			$this->orig_width  = $this->width;
			$this->orig_height = $this->height;
		}
		else
		{
			// If resizing the x/y axis must be zero
			$this->x_axis = 0;
			$this->y_axis = 0;
		}

		// Create the image handle
		if ( ! ($src_img = $this->image_create_gd()))
		{
			return FALSE;
		}

		/* Create the image
		 *
		 * Old conditional which users report cause problems with shared GD libs who report themselves as "2.0 or greater"
		 * it appears that this is no longer the issue that it was in 2004, so we've removed it, retaining it in the comment
		 * below should that ever prove inaccurate.
		 *
		 * if ($this->image_library === 'gd2' && function_exists('imagecreatetruecolor') && $v2_override === FALSE)
		 */
		if ($this->image_library === 'gd2' && function_exists('imagecreatetruecolor'))
		{
			$create	= 'imagecreatetruecolor';
			$copy	= 'imagecopyresampled';
		}
		else
		{
			$create	= 'imagecreate';
			$copy	= 'imagecopyresized';
		}

		$dst_img = $create($this->width, $this->height);

		if ($this->image_type === 3) // png we can actually preserve transparency
		{
			imagealphablending($dst_img, FALSE);
			imagesavealpha($dst_img, TRUE);
		}

		if ($action === 'extent')
		{
			list($r, $g, $b) = sscanf($this->bg_color, '#%02x%02x%02x');
			$bg_color = imagecolorallocate($dst_img, $r, $g, $b);
			imagefill($dst_img, 0, 0, $bg_color);

			$ratio = min(array($this->width / $this->orig_width, $this->height / $this->orig_height));
			$dst_width = $this->orig_width * $ratio;
			$dst_height = $this->orig_height * $ratio;
			$dst_x = $ratio > 0 ? floor(($this->width - $dst_width) / 2) : 0;
			$dst_y = $ratio > 0 ? floor(($this->height - $dst_height) / 2) : 0;
		}
		else
		{
			$dst_width = $this->width;
			$dst_height = $this->height;
			$dst_x = 0;
			$dst_y = 0;
		}

		$copy($dst_img, $src_img, $dst_x, $dst_y, $this->x_axis, $this->y_axis, $dst_width, $dst_height, $this->orig_width, $this->orig_height);

		// Show the image
		if ($this->dynamic_output === TRUE)
		{
			$this->image_display_gd($dst_img);
		}
		elseif ( ! $this->image_save_gd($dst_img)) // Or save it
		{
			return FALSE;
		}

		// Kill the file handles
		imagedestroy($dst_img);
		imagedestroy($src_img);

		if ($this->dynamic_output !== TRUE)
		{
			chmod($this->full_dst_path, $this->file_permissions);
		}

		return TRUE;
	}

	// --------------------------------------------------------------------

	/**
	 * Image Process Using ImageMagick
	 *
	 * This function will resize, crop or rotate
	 *
	 * @param	string
	 * @return	bool
	 */
	public function image_process_imagemagick($action = 'resize')
	{
		// Do we have a vaild library path?
		if ($this->library_path === '')
		{
			$this->set_error('imglib_libpath_invalid');
			return FALSE;
		}

		if ( ! preg_match('/convert$/i', $this->library_path))
		{
			$this->library_path = rtrim($this->library_path, '/') . '/convert';
		}

		// if TRUE - image sequence is important
		$sequence = FALSE;

		// Execute the command
		$cmd = $this->library_path . ' -quality ' . $this->quality;

		if ($action === 'crop')
		{
			if ($this->gravity !== 'undefined')
			{
				$cmd .= ' -gravity ' . $this->gravity;
			}

			$this->x_axis = (($this->x_axis >= 0) ? '+' : '-') . abs($this->x_axis);
			$this->y_axis = (($this->y_axis >= 0) ? '+' : '-') . abs($this->y_axis);
			$cmd .= ' -crop ' . $this->width . 'x' . $this->height . $this->x_axis . $this->y_axis;
		}
		elseif ($action === 'rotate')
		{
			$cmd .= ($this->rotation_angle === 'hor' OR $this->rotation_angle === 'vrt')
				? ' -flop'
				: ' -rotate ' . $this->rotation_angle;
		}
		// Set the image size and offset. If the image is enlarged,
		// unfilled areas are set to the background color.
		elseif ($action === 'extent')
		{
			// Do not use maintain_ratio with extent or image will be resized with wrong width & height
			$cmd .= ' -unsharp 0x1 -resize ' . $this->width . 'x' . $this->height;
			$cmd .= ' -background white -gravity center -extent ' . $this->width . 'x' . $this->height;
		}
		else // Resize
		{
			$cmd .= ' -unsharp 0x1 -resize ' . $this->width . 'x' . $this->height . (($this->maintain_ratio !== TRUE) ? '\!' : '');
		}

		$cmd .= (( ! $sequence) ? ' ' . escapeshellarg($this->full_src_path) : '') .
			' ' . escapeshellarg($this->full_dst_path) . ' 2>&1';

		$retval = 1;
		// exec() might be disabled
		if (function_usable('exec'))
		{
			@exec($cmd, $output, $retval);
		}

		// Did it work?
		if ($retval > 0)
		{
			$this->set_error('imglib_image_process_failed');
			return FALSE;
		}

		@chmod($this->full_dst_path, $this->file_permissions);

		return TRUE;
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
