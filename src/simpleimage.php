<?php
/**
 * @package     SimpleImage class
 * @version     2.5.5
 * @author      Cory LaViska for A Beautiful Site, LLC (http://www.abeautifulsite.net/)
 * @author      Nazar Mokrynskyi <nazar@mokrynskyi.com> - merging of forks, namespace support, PhpDoc editing, adaptive_resize() method, other fixes
 * @license     This software is licensed under the MIT license: http://opensource.org/licenses/MIT
 * @copyright   A Beautiful Site, LLC
 *
 *
 * Class SimpleImage
 * This class makes image manipulation in PHP as simple as possible.
 * @package SimpleImage
 *
 */
class SimpleImage {

    /**
     * @var int Default output image quality
     *
     */
    public $quality = 80;

    protected $image, $filename, $original_info, $width, $height, $imagestring;

    /**
     * Create instance and load an image, or create an image from scratch
     *
     * @param null|string   $filename   Path to image file (may be omitted to create image from scratch)
     * @param int           $width      Image width (is used for creating image from scratch)
     * @param int|null      $height     If omitted - assumed equal to $width (is used for creating image from scratch)
     * @param null|string   $color      Hex color string, array(red, green, blue) or array(red, green, blue, alpha).
     *                                  Where red, green, blue - integers 0-255, alpha - integer 0-127<br>
     *                                  (is used for creating image from scratch)
     *
     * @return SimpleImage
     * @throws Exception
     *
     */
    function __construct($filename = null, $width = null, $height = null, $color = null) {
        if ($filename) {
            $this->load($filename);
        } elseif ($width) {
            $this->create($width, $height, $color);
        }
        return $this;
    }

    /**
     * Destroy image resource
     *
     */
    function __destruct() {
        if( @get_resource_type($this->image) === 'gd' ) {
            imagedestroy($this->image);
        }
    }

    /**
     * Adaptive resize
     *
     * This function has been deprecated and will be removed in an upcoming release. Please
     * update your code to use the `thumbnail()` method instead. The arguments for both
     * methods are exactly the same.
     *
     * @param int           $width
     * @param int|null      $height If omitted - assumed equal to $width
     *
     * @return SimpleImage
     *
     */
    function adaptive_resize($width, $height = null) {

        return $this->thumbnail($width, $height);

    }

    /**
     * Rotates and/or flips an image automatically so the orientation will be correct (based on exif 'Orientation')
     *
     * @return SimpleImage
     *
     */
    function auto_orient() {

        switch (@$this->original_info['exif']['Orientation']) {
            case 1:
                // Do nothing
                break;
            case 2:
                // Flip horizontal
                $this->flip('x');
                break;
            case 3:
                // Rotate 180 counterclockwise
                $this->rotate(-180);
                break;
            case 4:
                // vertical flip
                $this->flip('y');
                break;
            case 5:
                // Rotate 90 clockwise and flip vertically
                $this->flip('y');
                $this->rotate(90);
                break;
            case 6:
                // Rotate 90 clockwise
                $this->rotate(90);
                break;
            case 7:
                // Rotate 90 clockwise and flip horizontally
                $this->flip('x');
                $this->rotate(90);
                break;
            case 8:
                // Rotate 90 counterclockwise
                $this->rotate(-90);
                break;
        }

        return $this;

    }

    /**
     * Best fit (proportionally resize to fit in specified width/height)
     *
     * Shrink the image proportionally to fit inside a $width x $height box
     *
     * @param int           $max_width
     * @param int           $max_height
     *
     * @return  SimpleImage
     *
     */
    function best_fit($max_width, $max_height) {

        // If it already fits, there's nothing to do
        if ($this->width <= $max_width && $this->height <= $max_height) {
            return $this;
        }

        // Determine aspect ratio
        $aspect_ratio = $this->height / $this->width;

        // Make width fit into new dimensions
        if ($this->width > $max_width) {
            $width = $max_width;
            $height = $width * $aspect_ratio;
        } else {
            $width = $this->width;
            $height = $this->height;
        }

        // Make height fit into new dimensions
        if ($height > $max_height) {
            $height = $max_height;
            $width = $height / $aspect_ratio;
        }

        return $this->resize($width, $height);

    }

    /**
     * Blur
     *
     * @param string        $type   selective|gaussian
     * @param int           $passes Number of times to apply the filter
     *
     * @return SimpleImage
     *
     */
    function blur($type = 'selective', $passes = 1) {
        switch (strtolower($type)) {
            case 'gaussian':
                $type = IMG_FILTER_GAUSSIAN_BLUR;
                break;
            default:
                $type = IMG_FILTER_SELECTIVE_BLUR;
                break;
        }
        for ($i = 0; $i < $passes; $i++) {
            imagefilter($this->image, $type);
        }
        return $this;
    }

    /**
     * Brightness
     *
     * @param int           $level  Darkest = -255, lightest = 255
     *
     * @return SimpleImage
     *
     */
    function brightness($level) {
        imagefilter($this->image, IMG_FILTER_BRIGHTNESS, $this->keep_within($level, -255, 255));
        return $this;
    }

    /**
     * Contrast
     *
     * @param int           $level  Min = -100, max = 100
     *
     * @return SimpleImage
     *
     *
     */
    function contrast($level) {
        imagefilter($this->image, IMG_FILTER_CONTRAST, $this->keep_within($level, -100, 100));
        return $this;
    }

    /**
     * Colorize
     *
     * @param string        $color      Hex color string, array(red, green, blue) or array(red, green, blue, alpha).
     *                                  Where red, green, blue - integers 0-255, alpha - integer 0-127
     * @param float|int     $opacity    0-1
     *
     * @return SimpleImage
     *
     */
    function colorize($color, $opacity) {
        $rgba = $this->normalize_color($color);
        $alpha = $this->keep_within(127 - (127 * $opacity), 0, 127);
        imagefilter($this->image, IMG_FILTER_COLORIZE, $this->keep_within($rgba['r'], 0, 255), $this->keep_within($rgba['g'], 0, 255), $this->keep_within($rgba['b'], 0, 255), $alpha);
        return $this;
    }

    /**
     * Create an image from scratch
     *
     * @param int           $width  Image width
     * @param int|null      $height If omitted - assumed equal to $width
     * @param null|string   $color  Hex color string, array(red, green, blue) or array(red, green, blue, alpha).
     *                              Where red, green, blue - integers 0-255, alpha - integer 0-127
     *
     * @return SimpleImage
     *
     */
    function create($width, $height = null, $color = null) {

        $height = $height ?: $width;
        $this->width = $width;
        $this->height = $height;
        $this->image = imagecreatetruecolor($width, $height);
        $this->original_info = array(
            'width' => $width,
            'height' => $height,
            'orientation' => $this->get_orientation(),
            'exif' => null,
            'format' => 'png',
            'mime' => 'image/png'
        );

        if ($color) {
            $this->fill($color);
        }

        return $this;

    }

    /**
     * Crop an image
     *
     * @param int           $x1 Left
     * @param int           $y1 Top
     * @param int           $x2 Right
     * @param int           $y2 Bottom
     *
     * @return SimpleImage
     *
     */
    function crop($x1, $y1, $x2, $y2) {

        // Determine crop size
        if ($x2 < $x1) {
            list($x1, $x2) = array($x2, $x1);
        }
        if ($y2 < $y1) {
            list($y1, $y2) = array($y2, $y1);
        }
        $crop_width = $x2 - $x1;
        $crop_height = $y2 - $y1;

        // Perform crop
        $new = imagecreatetruecolor($crop_width, $crop_height);
        imagealphablending($new, false);
        imagesavealpha($new, true);
        imagecopyresampled($new, $this->image, 0, 0, $x1, $y1, $crop_width, $crop_height, $crop_width, $crop_height);

        // Update meta data
        $this->width = $crop_width;
        $this->height = $crop_height;
        $this->image = $new;

        return $this;

    }

    /**
     * Desaturate
     *
     * @param int           $percentage Level of desaturization.
     *
     * @return SimpleImage
     *
     */
    function desaturate($percentage = 100) {

        // Determine percentage
        $percentage = $this->keep_within($percentage, 0, 100);

        if( $percentage === 100 ) {
            imagefilter($this->image, IMG_FILTER_GRAYSCALE);
        } else {
            // Make a desaturated copy of the image
            $new = imagecreatetruecolor($this->width, $this->height);
            imagealphablending($new, false);
            imagesavealpha($new, true);
            imagecopy($new, $this->image, 0, 0, 0, 0, $this->width, $this->height);
            imagefilter($new, IMG_FILTER_GRAYSCALE);

            // Merge with specified percentage
            $this->imagecopymerge_alpha($this->image, $new, 0, 0, 0, 0, $this->width, $this->height, $percentage);
            imagedestroy($new);

        }

        return $this;
    }

    /**
     * Edge Detect
     *
     * @return SimpleImage
     *
     */
    function edges() {
        imagefilter($this->image, IMG_FILTER_EDGEDETECT);
        return $this;
    }

    /**
     * Emboss
     *
     * @return SimpleImage
     *
     */
    function emboss() {
        imagefilter($this->image, IMG_FILTER_EMBOSS);
        return $this;
    }

    /**
     * Fill image with color
     *
     * @param string        $color  Hex color string, array(red, green, blue) or array(red, green, blue, alpha).
     *                              Where red, green, blue - integers 0-255, alpha - integer 0-127
     *
     * @return SimpleImage
     *
     */
    function fill($color = '#000000',$x=0,$y=0,$width=0,$height=0) {

	   if ($width == 0){ $width = $this->width;}
	   if ($height == 0){  $height = $this->height;}
	  
        $rgba = $this->normalize_color($color);
        $fill_color = imagecolorallocatealpha($this->image, $rgba['r'], $rgba['g'], $rgba['b'], $rgba['a']);
        imagealphablending($this->image, false);
        imagesavealpha($this->image, true);
        imagefilledrectangle($this->image, $x, $y, $this->width, $this->height, $fill_color);

        return $this;

    }

    /**
     * Fit to height (proportionally resize to specified height)
     *
     * @param int           $height
     *
     * @return SimpleImage
     *
     */
    function fit_to_height($height) {

        $aspect_ratio = $this->height / $this->width;
        $width = $height / $aspect_ratio;

        return $this->resize($width, $height);

    }

    /**
     * Fit to width (proportionally resize to specified width)
     *
     * @param int           $width
     *
     * @return SimpleImage
     *
     */
    function fit_to_width($width) {

        $aspect_ratio = $this->height / $this->width;
        $height = $width * $aspect_ratio;

        return $this->resize($width, $height);

    }

    /**
     * Flip an image horizontally or vertically
     *
     * @param string        $direction  x|y
     *
     * @return SimpleImage
     *
     */
    function flip($direction) {

        $new = imagecreatetruecolor($this->width, $this->height);
        imagealphablending($new, false);
        imagesavealpha($new, true);

        switch (strtolower($direction)) {
            case 'y':
                for ($y = 0; $y < $this->height; $y++) {
                    imagecopy($new, $this->image, 0, $y, 0, $this->height - $y - 1, $this->width, 1);
                }
                break;
            default:
                for ($x = 0; $x < $this->width; $x++) {
                    imagecopy($new, $this->image, $x, 0, $this->width - $x - 1, 0, 1, $this->height);
                }
                break;
        }

        $this->image = $new;

        return $this;

    }

    /**
     * Get the current height
     *
     * @return int
     *
     */
    function get_height() {
        return $this->height;
    }

    /**
     * Get the current orientation
     *
     * @return string   portrait|landscape|square
     *
     */
    function get_orientation() {

        if (imagesx($this->image) > imagesy($this->image)) {
            return 'landscape';
        }

        if (imagesx($this->image) < imagesy($this->image)) {
            return 'portrait';
        }

        return 'square';

    }

    /**
     * Get info about the original image
     *
     * @return array <pre> array(
     *  width        => 320,
     *  height       => 200,
     *  orientation  => ['portrait', 'landscape', 'square'],
     *  exif         => array(...),
     *  mime         => ['image/jpeg', 'image/gif', 'image/png'],
     *  format       => ['jpeg', 'gif', 'png']
     * )</pre>
     *
     */
    function get_original_info() {
        return $this->original_info;
    }

    /**
     * Get the current width
     *
     * @return int
     *
     */
    function get_width() {
        return $this->width;
    }

    /**
     * Invert
     *
     * @return SimpleImage
     *
     */
    function invert() {
        imagefilter($this->image, IMG_FILTER_NEGATE);
        return $this;
    }

    /**
     * Load an image
     *
     * @param string        $filename   Path to image file
     *
     * @return SimpleImage
     * @throws Exception
     *
     */
    function load($filename) {

        // Require GD library
        if (!extension_loaded('gd')) {
            throw new Exception('Required extension GD is not loaded.');
        }
        $this->filename = $filename;
        return $this->get_meta_data();
    }

    /**
     * Load a base64 string as image
     *
     * @param string        $filename   base64 string
     *
     * @return SimpleImage
     *
     */
    function load_base64($base64string) {
        if (!extension_loaded('gd')) {
            throw new Exception('Required extension GD is not loaded.');
        }
        //remove data URI scheme and spaces from base64 string then decode it
        $this->imagestring = base64_decode(str_replace(' ', '+',preg_replace('#^data:image/[^;]+;base64,#', '', $base64string)));
        $this->image = imagecreatefromstring($this->imagestring);
        return $this->get_meta_data();
    }

    /**
     * Mean Remove
     *
     * @return SimpleImage
     *
     */
    function mean_remove() {
        imagefilter($this->image, IMG_FILTER_MEAN_REMOVAL);
        return $this;
    }

    /**
     * Changes the opacity level of the image
     *
     * @param float|int     $opacity    0-1
     *
     * @throws Exception
     *
     */
    function opacity($opacity) {

        // Determine opacity
        $opacity = $this->keep_within($opacity, 0, 1) * 100;

        // Make a copy of the image
        $copy = imagecreatetruecolor($this->width, $this->height);
        imagealphablending($copy, false);
        imagesavealpha($copy, true);
        imagecopy($copy, $this->image, 0, 0, 0, 0, $this->width, $this->height);

        // Create transparent layer
        $this->create($this->width, $this->height, array(0, 0, 0, 127));

        // Merge with specified opacity
        $this->imagecopymerge_alpha($this->image, $copy, 0, 0, 0, 0, $this->width, $this->height, $opacity);
        imagedestroy($copy);

        return $this;

    }

    /**
     * Outputs image without saving
     *
     * @param null|string   $format     If omitted or null - format of original file will be used, may be gif|jpg|png
     * @param int|null      $quality    Output image quality in percents 0-100
     *
     * @throws Exception
     *
     */
    function output($format = null, $quality = null) {

        // Determine quality
        $quality = $quality ?: $this->quality;

        // Determine mimetype
        switch (strtolower($format)) {
            case 'gif':
                $mimetype = 'image/gif';
                break;
            case 'jpeg':
            case 'jpg':
                imageinterlace($this->image, true);
                $mimetype = 'image/jpeg';
                break;
            case 'png':
                $mimetype = 'image/png';
                break;
            default:
                $info = (empty($this->imagestring)) ? getimagesize($this->filename) : getimagesizefromstring($this->imagestring);
                $mimetype = $info['mime'];
                unset($info);
                break;
        }

        // Output the image
        header('Content-Type: '.$mimetype);
        switch ($mimetype) {
            case 'image/gif':
                imagegif($this->image);
                break;
            case 'image/jpeg':
                imagejpeg($this->image, null, round($quality));
                break;
            case 'image/png':
                imagepng($this->image, null, round(9 * $quality / 100));
                break;
            default:
                throw new Exception('Unsupported image format: '.$this->filename);
                break;
        }
    }

    /**
     * Outputs image as data base64 to use as img src
     *
     * @param null|string   $format     If omitted or null - format of original file will be used, may be gif|jpg|png
     * @param int|null      $quality    Output image quality in percents 0-100
     *
     * @return string
     * @throws Exception
     *
     */
    function output_base64($format = null, $quality = null) {

        // Determine quality
        $quality = $quality ?: $this->quality;

        // Determine mimetype
        switch (strtolower($format)) {
            case 'gif':
                $mimetype = 'image/gif';
                break;
            case 'jpeg':
            case 'jpg':
                imageinterlace($this->image, true);
                $mimetype = 'image/jpeg';
                break;
            case 'png':
                $mimetype = 'image/png';
                break;
            default:
                $info = getimagesize($this->filename);
                $mimetype = $info['mime'];
                unset($info);
                break;
        }

        // Output the image
        ob_start();
        switch ($mimetype) {
            case 'image/gif':
                imagegif($this->image);
                break;
            case 'image/jpeg':
                imagejpeg($this->image, null, round($quality));
                break;
            case 'image/png':
                imagepng($this->image, null, round(9 * $quality / 100));
                break;
            default:
                throw new Exception('Unsupported image format: '.$this->filename);
                break;
        }
        $image_data = ob_get_contents();
        ob_end_clean();

        // Returns formatted string for img src
        return 'data:'.$mimetype.';base64,'.base64_encode($image_data);

    }

    /**
     * Overlay
     *
     * Overlay an image on top of another, works with 24-bit PNG alpha-transparency
     *
     * @param string        $overlay        An image filename or a SimpleImage object
     * @param string        $position       center|top|left|bottom|right|top left|top right|bottom left|bottom right
     * @param float|int     $opacity        Overlay opacity 0-1
     * @param int           $x_offset       Horizontal offset in pixels
     * @param int           $y_offset       Vertical offset in pixels
     *
     * @return SimpleImage
     *
     */
    function overlay($overlay, $position = 'center', $opacity = 1, $x_offset = 0, $y_offset = 0) {

        // Load overlay image
        if( !($overlay instanceof SimpleImage) ) {
            $overlay = new SimpleImage($overlay);
        }

        // Convert opacity
        $opacity = $opacity * 100;

        // Determine position
        switch (strtolower($position)) {
            case 'top left':
                $x = 0 + $x_offset;
                $y = 0 + $y_offset;
                break;
            case 'top right':
                $x = $this->width - $overlay->width + $x_offset;
                $y = 0 + $y_offset;
                break;
            case 'top':
                $x = ($this->width / 2) - ($overlay->width / 2) + $x_offset;
                $y = 0 + $y_offset;
                break;
            case 'bottom left':
                $x = 0 + $x_offset;
                $y = $this->height - $overlay->height + $y_offset;
                break;
            case 'bottom right':
                $x = $this->width - $overlay->width + $x_offset;
                $y = $this->height - $overlay->height + $y_offset;
                break;
            case 'bottom':
                $x = ($this->width / 2) - ($overlay->width / 2) + $x_offset;
                $y = $this->height - $overlay->height + $y_offset;
                break;
            case 'left':
                $x = 0 + $x_offset;
                $y = ($this->height / 2) - ($overlay->height / 2) + $y_offset;
                break;
			case 'bottom right up':
			$x = $this->width - $overlay->width + $x_offset;
			$y = $this->height - $overlay->height + $y_offset;
			break;
            case 'right':
                $x = $this->width - $overlay->width + $x_offset;
                $y = ($this->height / 2) - ($overlay->height / 2) + $y_offset;
                break;
            case 'center':
            default:
                $x = ($this->width / 2) - ($overlay->width / 2) + $x_offset;
                $y = ($this->height / 2) - ($overlay->height / 2) + $y_offset;
                break;
        }

        // Perform the overlay
        $this->imagecopymerge_alpha($this->image, $overlay->image, $x, $y, 0, 0, $overlay->width, $overlay->height, $opacity);

        return $this;

    }

    /**
     * Pixelate
     *
     * @param int           $block_size Size in pixels of each resulting block
     *
     * @return SimpleImage
     *
     */
    function pixelate($block_size = 10) {
        imagefilter($this->image, IMG_FILTER_PIXELATE, $block_size, true);
        return $this;
    }

    /**
     * Resize an image to the specified dimensions
     *
     * @param int   $width
     * @param int   $height
     *
     * @return SimpleImage
     *
     */
    function resize($width, $height) {

        // Generate new GD image
        $new = imagecreatetruecolor($width, $height);

        if( $this->original_info['format'] === 'gif' ) {
            // Preserve transparency in GIFs
            $transparent_index = imagecolortransparent($this->image);
            $palletsize = imagecolorstotal($this->image);
            if ($transparent_index >= 0 && $transparent_index < $palletsize) {
                $transparent_color = imagecolorsforindex($this->image, $transparent_index);
                $transparent_index = imagecolorallocate($new, $transparent_color['red'], $transparent_color['green'], $transparent_color['blue']);
                imagefill($new, 0, 0, $transparent_index);
                imagecolortransparent($new, $transparent_index);
            }
        } else {
            // Preserve transparency in PNGs (benign for JPEGs)
            imagealphablending($new, false);
            imagesavealpha($new, true);
        }

        // Resize
        imagecopyresampled($new, $this->image, 0, 0, 0, 0, $width, $height, $this->width, $this->height);

        // Update meta data
        $this->width = $width;
        $this->height = $height;
        $this->image = $new;

        return $this;

    }

    /**
     * Rotate an image
     *
     * @param int           $angle      0-360
     * @param string        $bg_color   Hex color string, array(red, green, blue) or array(red, green, blue, alpha).
     *                                  Where red, green, blue - integers 0-255, alpha - integer 0-127
     *
     * @return SimpleImage
     *
     */
    function rotate($angle, $bg_color = '#000000') {

        // Perform the rotation
        $rgba = $this->normalize_color($bg_color);
        $bg_color = imagecolorallocatealpha($this->image, $rgba['r'], $rgba['g'], $rgba['b'], $rgba['a']);
        $new = imagerotate($this->image, -($this->keep_within($angle, -360, 360)), $bg_color);
        imagesavealpha($new, true);
        imagealphablending($new, true);

        // Update meta data
        $this->width = imagesx($new);
        $this->height = imagesy($new);
        $this->image = $new;

        return $this;

    }

    /**
     * Save an image
     *
     * The resulting format will be determined by the file extension.
     *
     * @param null|string   $filename   If omitted - original file will be overwritten
     * @param null|int      $quality    Output image quality in percents 0-100
     * @param null|string   $format     The format to use; determined by file extension if null
     *
     * @return SimpleImage
     * @throws Exception
     *
     */
    function save($filename = null, $quality = null, $format = null) {

        // Determine quality, filename, and format
        $quality = $quality ?: $this->quality;
        $filename = $filename ?: $this->filename;
        if( !$format ) {
            $format = $this->file_ext($filename) ?: $this->original_info['format'];
        }

        // Create the image
        switch (strtolower($format)) {
            case 'gif':
                $result = imagegif($this->image, $filename);
                break;
            case 'jpg':
            case 'jpeg':
                imageinterlace($this->image, true);
                $result = imagejpeg($this->image, $filename, round($quality));
                break;
            case 'png':
                $result = imagepng($this->image, $filename, round(9 * $quality / 100));
                break;
            default:
                throw new Exception('Unsupported format');
        }

        if (!$result) {
            throw new Exception('Unable to save image: ' . $filename);
        }

        return $this;

    }

    /**
     * Sepia
     *
     * @return SimpleImage
     *
     */
    function sepia() {
        imagefilter($this->image, IMG_FILTER_GRAYSCALE);
        imagefilter($this->image, IMG_FILTER_COLORIZE, 100, 50, 0);
        return $this;
    }

    /**
     * Sketch
     *
     * @return SimpleImage
     *
     */
    function sketch() {
        imagefilter($this->image, IMG_FILTER_MEAN_REMOVAL);
        return $this;
    }

    /**
     * Smooth
     *
     * @param int           $level  Min = -10, max = 10
     *
     * @return SimpleImage
     *
     */
    function smooth($level) {
        imagefilter($this->image, IMG_FILTER_SMOOTH, $this->keep_within($level, -10, 10));
        return $this;
    }

    /**
     * Add text to an image
     *
     * @param string        $text
     * @param string        $font_file
     * @param float|int     $font_size
     * @param string        $color
     * @param string        $position
     * @param int           $x_offset
     * @param int           $y_offset
     *
     * @return SimpleImage
     * @throws Exception
     *
     */
    function text($text, $font_file, $font_size = 12, $color = '#000000', $position = 'center', $x_offset = 0, $y_offset = 0, $strokecolor = '#000000') {

        // todo - this method could be improved to support the text angle
        $angle = 0;

        // Determine text color
        $rgba = $this->normalize_color($color);
        $color = imagecolorallocatealpha($this->image, $rgba['r'], $rgba['g'], $rgba['b'], $rgba['a']);

		// Determine strokecolor
        $srgba = $this->normalize_color($strokecolor);
        $strokecolor = imagecolorallocatealpha($this->image, $srgba['r'], $srgba['g'], $srgba['b'], $srgba['a']);
		
        // Determine textbox size
        $box = imagettfbbox($font_size, $angle, $font_file, $text);
        if (!$box) {
            throw new Exception('Unable to load font: '.$font_file);
        }
        $box_width = abs($box[6] - $box[2]);
        $box_height = abs($box[7] - $box[1]);

        // Determine position
        switch (strtolower($position)) {
            case 'top left':
                $x = 0 + $x_offset;
                $y = 0 + $y_offset + $box_height;
                break;
            case 'top right':
                $x = $this->width - $box_width + $x_offset;
                $y = 0 + $y_offset + $box_height;
                break;
            case 'top':
                $x = ($this->width / 2) - ($box_width / 2) + $x_offset;
                $y = 0 + $y_offset + $box_height;
                break;
            case 'bottom left':
                $x = 0 + $x_offset;
                $y = $this->height - $box_height + $y_offset + $box_height;
                break;
            case 'bottom right':
                $x = $this->width - $box_width + $x_offset;
                $y = $this->height - $box_height + $y_offset + $box_height;
                break;
            case 'bottom':
                $x = ($this->width / 2) - ($box_width / 2) + $x_offset;
                $y = $this->height - $box_height + $y_offset + $box_height;
                break;
            case 'left':
                $x = 0 + $x_offset;
                $y = ($this->height / 2) - (($box_height / 2) - $box_height) + $y_offset;
                break;
            case 'right';
                $x = $this->width - $box_width + $x_offset;
                $y = ($this->height / 2) - (($box_height / 2) - $box_height) + $y_offset;
                break;
            case 'center':
            default:
                $x = ($this->width / 2) - ($box_width / 2) + $x_offset;
                $y = ($this->height / 2) - (($box_height / 2) - $box_height) + $y_offset;
                break;
        }

        // Add the text
        imagesavealpha($this->image, true);
        imagealphablending($this->image, true);
		$px = 2; 
		  for($c1 = ($x-abs($px)); $c1 <= ($x+abs($px)); $c1++)
        for($c2 = ($y-abs($px)); $c2 <= ($y+abs($px)); $c2++)
		  $bg = imagettftext($this->image, $font_size, $angle, $c1, $c2,$strokecolor, $font_file, $text);
		  
        imagettftext($this->image, $font_size, $angle, $x, $y, $color, $font_file, $text);

        return $this;

    }
	
// v1.9.1 (small changes from original version v1.9)
// Added emphasys "..." when text is truncated (no option).
function imagettftextboxopt($size, $angle, $left, $top, $color, $fontfile, $text,$finalimage,$boxwidth,$boxheight,$line_height)
{
	$image = $this->image;
	
	// Determine text color
	$rgba = $this->normalize_color($color);
	$color = imagecolorallocatealpha($image, $rgba['r'], $rgba['g'], $rgba['b'], $rgba['a']);
	
	$opt = array(
	'width' => $boxwidth,
	'line_height' => $line_height,
	'orientation' => array(ORIENTATION_LEFT, ORIENTATION_LEFT),
	'align' => ALIGN_LEFT,
	'v_align' => VALIGN_MIDDLE,
	'outlines' => array(
	array(1, imagecolorallocate($image, 0, 0, 0)),
	array(0,imagecolorallocate($image, 0, 0, 0)),
	)
	);
	
    // Default options
    $default_opt = array(
        // bbox/position
        'width' =>$boxwidth, // Required
        'height' =>700, // Optional height
        'orientation' => array(ORIENTATION_CENTER, ORIENTATION_LEFT), // Orientation for placement
                                                                   // Can be a constant or a nubmer
                                                                   // relative to 0, 0 of the box
        'indent_for_angle' => true, // For angled text, have the left x of each line be translated too
        // General
        'space_char' => ' ', // Space character
        'use_mbstring' => false, // Uses the mbstring extension for string functions (Unicode, etc. support)
        // Text
        'align' => ALIGN_LEFT, // ALIGN_LEFT, ALIGN_MIDDLE, ALIGN_RIGHT
        'line_height' => 0, // Set to 0 to use the largest line height (not a good idea)
        'v_align' => VALIGN_MIDDLE, // VALIGN_TOP, VALIGN_CENTER, VALIGN_BOTTOM
        'leading' => 1,
        'show_3dots' => false,
        // Features
        'outlines' => array(), // Outlines: array(weight, color)
        // Word wrap
        'force_word_wrap' => true, // If a word is too long, you can force it to wrap
                                   // However, to do this, it will imagettfbbox() every character
        'cutoff_word_wrap' => false, // Instead of wrapping, cut off the text
        'word_wrap_hyphen' => '-', // When a word is force wrapped, this character will be appended
        'aggressive_word_wrap' => false, // Aggressive word wrapping will force a wrap if too much of a line is
                                         // left blank (aggressive_word_wrap_amt) - great for Chinese and other
                                         // languages where words are characters, but not for other languages
        'aggressive_word_wrap_amt' => .03, // If aggressive forced word wrapping is enabled, this is the 
                                           // maximum percent width of a line that may be left blank
        );
    
    // "Named arguments"
    extract($default_opt);
    extract($opt);
    
    // Use mb_string?
    if ($use_mbstring) {
        if (!extension_loaded("mbstring")) {
            throw new Exception("The mbstring module is not loaded");
        }
        
        $F_strlen = "mb_strlen";
        $F_substr = "mb_substr";
    } else {
        $F_strlen = "strlen";
        $F_substr = "substr";
    }
    
    // Check input
    if ($v_align != VALIGN_TOP && $v_align != VALIGN_MIDDLE && $v_align != VALIGN_BOTTOM) {
        throw new Exception("Unknown vertical alignment passed");
    }
    if ($align != ALIGN_LEFT && $align != ALIGN_CENTER && $align != ALIGN_RIGHT) {
        throw new Exception("Unknown horizontal alignment passed");
    }
    if (count($outlines) > 0 && !is_array($outlines[0])) {
        throw new Exception("The outline argument uses nested arrays");
    }
        
    // Adds support for manual line breaks
    $text_lines = explode("\n", $text); 
    
    $lines = array(); // Stores each line
    $line_widths = array(); // Stores how wide each line is, for alignment calculation
    $line_heights = array(); // Stores how tall each line is, for alignment calculation
    
    $largest_line_height = 0; // Auto-detected line height
    
    // First, go through all the lines
    for ($l = 0; $l < count($text_lines); $l++) {
        $block = $text_lines[$l];
        $words = explode($space_char, $block);
        
        $buffer = ''; // Stores the current line that's been checked to be <= $width
        $buffer_width = 0; // Width corresponding to the buffer
        $buffer_height = 0; // Height corresponding to the buffer
        $test_word = ''; // Current word being 'tested'
        $test_buffer = ''; // Contains the current line that's being tested, that's not guaranteed to be <= $width
        $test_width = 0;
        $test_height = 0;
        
        // Iterate through all the words
        for ($w = 0; $w <= count($words); $w++) {
            @$test_word = $words[$w];
            $test_buffer = $buffer.($buffer == "" ? "" : " ").$test_word;
            $bbox = imagettfbbox($size, 0, $fontfile, $test_buffer);
            $test_width = $bbox[2]-$bbox[0];
            $test_height = $bbox[1]-$bbox[7];
            
            // For auto line height detection
            if ($test_height > $largest_line_height) {
                $largest_line_height = $test_height;
            }
            
            // Tested line is too long, meaning the buffer is what we want as a new line
            // OR we're on the last word
            if ($test_width > $width || $w == count($words)) {
                // Slightly (but not much) smarter word wrapping
                if ($aggressive_word_wrap && !empty($buffer) && $w != count($words)) {
                    if ($buffer_width < $width-($aggressive_forced_wrap_amt*$width)) {
                        $buffer = ""; // Forces word wrapping
                    }
                }
                
                // This single word line is already too long by itself
                // We can force a word wrap
                if (empty($buffer)) {
                    // Cut this word into two chunks to force it to wrap
                    if ($force_word_wrap) {
                        $test_length = $F_strlen($test_buffer);
                        
                        $c_buffer_width = 0;
                        $c_buffer_height = 0;
                        $c_test_buffer = '';
                        $c_test_width = 0;
                        $c_test_height = 0;
                        
                        // Iterate through all the characters
                        for ($c = 0; $c < $test_length; $c++) {     
                            $c_test_buffer = trim($F_substr($test_buffer, 0, $c+1), $space_char).$word_wrap_hyphen;
                            
                            $bbox = imagettfbbox($size, 0, $fontfile, $c_test_buffer);
                            $c_test_width = $bbox[2]-$bbox[0];
                            $c_test_height = $bbox[1]-$bbox[7];
            
                            // For auto line height detection
                            if ($c_test_height > $largest_line_height) {
                                $largest_line_height = $c_test_height;
                            }
                            
                            if ($c_test_width > $width) {
                                $c_buffer = $F_substr($test_buffer, 0, $c).$word_wrap_hyphen;
                                if (empty($c_buffer)) { // $width too small
                                    throw new Exception("Width $width is too small; increase it or disable forced word wrapping");
                                } else {
                                    $lines[] = $c_buffer; // Add the line
                        
                                    // Needed for alignment
                                    $line_widths[] = $c_buffer_width;
                                    $line_heights[] = $c_buffer_height;
                                    
                                    if (!$cutoff_word_wrap) {
                                        // Add the rest as a new word
                                        $words[$w--] = $F_substr($test_buffer, $c);
                                    }
                                    
                                    // Stop calculating lines if we've already reached max height
                                    if ($height > 0 && ($line_height > 0 ? count($lines)*$line_height : array_sum($line_heights)) > $height) {
                                        break 2;
                                    }
                                    
                                    break;
                                }
                            } else {
                                $c_buffer_width = $c_test_width;
                                $c_buffer_height = $c_test_height;
                            }
                        }
                    // Or we don't
                    } else {
                        $lines[] = $test_buffer; // Add the buffer to the list of lines
                        $buffer = "";
                        
                        // Needed for alignment
                        $line_widths[] = $test_width;
                        $line_heights[] = $test_height;
                                    
                        // Stop calculating lines if we've already reached max height
                        if ($height > 0 && ($line_height > 0 ? count($lines)*$line_height : array_sum($line_heights)) > $height) {
                            break;   
                        }
                    }
                // Line fits well
                } else {
                    $lines[] = $buffer; // Add the buffer to the list of lines
                    $buffer = "";
                    $w--;
                    
                    // Needed for alignment
                    $line_widths[] = $buffer_width;
                    $line_heights[] = $buffer_height;
                                    
                    // Stop calculating lines if we've already reached max height
                    if ($height > 0 && ($line_height > 0 ? count($lines)*$line_height : array_sum($line_heights)) > $height) {
                        break;   
                    }
                }
            // Still not long enough... let's try again
            } else {
                $buffer = $test_buffer;
                $buffer_width = $test_width;
                $buffer_height = $test_height;
            }
        }
    }
    
    // No line height given, so we will use our auto-detected line height for it
    if ($line_height == 0) {
        $line_height = $largest_line_height;
    }
    
    // Add leading to the line height
    $line_height += $leading;
    
    // Number of lines and total height used for vertical
    // alignment and for the returned value
    $max_lines = $height > 0 ? floor($height/$line_height) : count($lines);
    $drawn_lines = count($lines) < $max_lines ? count($lines) : $max_lines;
    $total_height = $drawn_lines * $line_height;
	
    if (($show_3dots) && ($drawn_lines != count($lines))) {
      $lines[$max_lines - 1] = trim($lines[$max_lines - 1], " .\n\r")."...";
    }
	
    $top_shift = 0;
    $left_shift = 0;
    
    // Slightly adjust position based on the first line
    $first_line_bbox = imagettfbbox($size, 0, $fontfile, $lines[0]);
    
    // Vertical alignment
    if ($height != 0) {
        // Make sure the first line fits correctly;
        $first_line_offset = $line_height-$line_heights[0];
        
        if ($v_align == VALIGN_TOP) {
            $top_shift += abs($first_line_bbox[7])-$line_height;
        } else if ($v_align == VALIGN_MIDDLE) {
            $top_shift += ($height-$total_height-$first_line_offset)/2;
        } else if ($v_align == VALIGN_BOTTOM) {
            $top_shift += ($height-$total_height-$first_line_offset);
        }
    }
    
    // Draw iterations, used for outlines
    $draw_iterations = array_merge($outlines, array(array(0, $color)));
    
    // Orientation
    if ($orientation[1] == ORIENTATION_LEFT) {
        $orient_left = 0;
    } else if ($orientation[1] == ORIENTATION_RIGHT) {
        $orient_left = $width;
    } else if ($orientation[1] == ORIENTATION_CENTER) {
        $orient_left = $width/2;
    } else {
        $orient_left = intval($orientation[1]);
    }
    
    if ($orientation[0] == ORIENTATION_TOP) {
        $orient_top = 0;
    } else if ($orientation[0] == ORIENTATION_BOTTOM) {
        $orient_top = $height;
    } else if ($orientation[0] == ORIENTATION_CENTER) {
        $orient_top = $height/2;
    } else {
        $orient_top = intval($orientation[0]);
    }
    
    // Go through each draw iteration
    foreach ($draw_iterations as $draw) {
        list($weight, $color) = $draw;
        
        // Go through each X and Y to draw the outline :/
        // Not the world's most efficient method...
        for ($leftc = $left-$weight; $leftc <= $left+$weight; $leftc++) {
            for ($topc = $top-$weight; $topc <= $top+$weight; $topc++) {
                // Now go through and print all the lines
                for ($l = 0; $l < $drawn_lines; $l++) {
                    $line = $lines[$l];
                    
                    $top_offset = 0;
                    $left_offset = 0;
                    
                    // Horizontal alignment, more accurate than the vertical
                    // alignment we have above
                    if ($align == ALIGN_LEFT) {
                        $left_offset = -$first_line_bbox[0];
                    } else if ($align == ALIGN_CENTER) {
                        $left_offset = ($width-$line_widths[$l])/2;
                    } else if ($align == ALIGN_RIGHT) {
                        $left_offset = ($width-$line_widths[$l]);
                    }
                    
                    // Where we'll be drawing it
                    $x = $leftc+$left_offset+$left_shift;
                    $y = $topc+$top_offset+$top_shift+$line_height+($line_height*$l);
                    
                    // We need to figure the position if the angle is not zero
                    if ($indent_for_angle && ($angle != 0 || $orient_left != 0 || $orient_top != 0)) {
                        // Translate the point
                        $x -= $left+$orient_left;
                        $y -= $top+$orient_top;
                        $new_x = $x*cos(-$angle*M_PI/180)-$y*sin(-$angle*M_PI/180);
                        $new_y = $x*sin(-$angle*M_PI/180)+$y*cos(-$angle*M_PI/180);
                        $x = $new_x+$left;
                        $y = $new_y+$top;
                    }
                    imagettftext($image, $size, $angle, $x, $y, $color, $fontfile, $line);
                }
            }
        }
    }
	$this->save($finalimage);
}
   /**
     * Thumbnail
     *
     * This function attempts to get the image to as close to the provided dimensions as possible, and then crops the
     * remaining overflow (from the center) to get the image to be the size specified. Useful for generating thumbnails.
     *
     * @param int           $width
     * @param int|null      $height If omitted - assumed equal to $width
     *
     * @return SimpleImage
     *
     */
    function thumbnail($width, $height = null) {

        // Determine height
        $height = $height ?: $width;

        // Determine aspect ratios
        $current_aspect_ratio = $this->height / $this->width;
        $new_aspect_ratio = $height / $width;

        // Fit to height/width
        if ($new_aspect_ratio > $current_aspect_ratio) {
            $this->fit_to_height($height);
        } else {
            $this->fit_to_width($width);
        }
        $left = floor(($this->width / 2) - ($width / 2));
        $top = floor(($this->height / 2) - ($height / 2));

        // Return trimmed image
        return $this->crop($left, $top, $width + $left, $height + $top);

    }

    /**
     * Returns the file extension of the specified file
     *
     * @param string    $filename
     *
     * @return string
     *
     */
    protected function file_ext($filename) {

        if (!preg_match('/\./', $filename)) {
            return '';
        }

        return preg_replace('/^.*\./', '', $filename);

    }

    /**
     * Get meta data of image or base64 string
     *
     * @param string|null       $imagestring    If omitted treat as a normal image
     *
     * @return SimpleImage
     * @throws Exception
     *
     */
    protected function get_meta_data() {
        //gather meta data
        if(empty($this->imagestring)) {
            $info = getimagesize($this->filename);

            switch ($info['mime']) {
                case 'image/gif':
                    $this->image = imagecreatefromgif($this->filename);
                    break;
                case 'image/jpeg':
                    $this->image = imagecreatefromjpeg($this->filename);
                    break;
                case 'image/png':
                    $this->image = imagecreatefrompng($this->filename);
                    break;
                default:
                    throw new Exception('Invalid image: '.$this->filename);
                    break;
            }
        } elseif (function_exists('getimagesizefromstring')) {
            $info = getimagesizefromstring($this->imagestring);
        } else {
            throw new Exception('PHP 5.4 is required to use method getimagesizefromstring');
        }

        $this->original_info = array(
            'width' => $info[0],
            'height' => $info[1],
            'orientation' => $this->get_orientation(),
            'exif' => function_exists('exif_read_data') && $info['mime'] === 'image/jpeg' && $this->imagestring === null ? $this->exif = @exif_read_data($this->filename) : null,
            'format' => preg_replace('/^image\//', '', $info['mime']),
            'mime' => $info['mime']
        );
        $this->width = $info[0];
        $this->height = $info[1];

        imagesavealpha($this->image, true);
        imagealphablending($this->image, true);

        return $this;

    }

    /**
     * Same as PHP's imagecopymerge() function, except preserves alpha-transparency in 24-bit PNGs
     *
     * @param $dst_im
     * @param $src_im
     * @param $dst_x
     * @param $dst_y
     * @param $src_x
     * @param $src_y
     * @param $src_w
     * @param $src_h
     * @param $pct
     *
     * @link http://www.php.net/manual/en/function.imagecopymerge.php#88456
     *
     */
    protected function imagecopymerge_alpha($dst_im, $src_im, $dst_x, $dst_y, $src_x, $src_y, $src_w, $src_h, $pct) {

        // Get image width and height and percentage
        $pct /= 100;
        $w = imagesx($src_im);
        $h = imagesy($src_im);

        // Turn alpha blending off
        imagealphablending($src_im, false);

        // Find the most opaque pixel in the image (the one with the smallest alpha value)
        $minalpha = 127;
        for ($x = 0; $x < $w; $x++) {
            for ($y = 0; $y < $h; $y++) {
                $alpha = (imagecolorat($src_im, $x, $y) >> 24) & 0xFF;
                if ($alpha < $minalpha) {
                    $minalpha = $alpha;
                }
            }
        }

        // Loop through image pixels and modify alpha for each
        for ($x = 0; $x < $w; $x++) {
            for ($y = 0; $y < $h; $y++) {
                // Get current alpha value (represents the TANSPARENCY!)
                $colorxy = imagecolorat($src_im, $x, $y);
                $alpha = ($colorxy >> 24) & 0xFF;
                // Calculate new alpha
                if ($minalpha !== 127) {
                    $alpha = 127 + 127 * $pct * ($alpha - 127) / (127 - $minalpha);
                } else {
                    $alpha += 127 * $pct;
                }
                // Get the color index with new alpha
                $alphacolorxy = imagecolorallocatealpha($src_im, ($colorxy >> 16) & 0xFF, ($colorxy >> 8) & 0xFF, $colorxy & 0xFF, $alpha);
                // Set pixel with the new color + opacity
                if (!imagesetpixel($src_im, $x, $y, $alphacolorxy)) {
                    return;
                }
            }
        }

        // Copy it
        imagesavealpha($dst_im, true);
        imagealphablending($dst_im, true);
        imagesavealpha($src_im, true);
        imagealphablending($src_im, true);
        imagecopy($dst_im, $src_im, $dst_x, $dst_y, $src_x, $src_y, $src_w, $src_h);

    }

    /**
     * Ensures $value is always within $min and $max range.
     *
     * If lower, $min is returned. If higher, $max is returned.
     *
     * @param int|float     $value
     * @param int|float     $min
     * @param int|float     $max
     *
     * @return int|float
     *
     */
    protected function keep_within($value, $min, $max) {

        if ($value < $min) {
            return $min;
        }

        if ($value > $max) {
            return $max;
        }

        return $value;

    }

    /**
     * Converts a hex color value to its RGB equivalent
     *
     * @param string        $color  Hex color string, array(red, green, blue) or array(red, green, blue, alpha).
     *                              Where red, green, blue - integers 0-255, alpha - integer 0-127
     *
     * @return array|bool
     *
     */
    protected function normalize_color($color) {

        if (is_string($color)) {

            $color = trim($color, '#');

            if (strlen($color) == 6) {
                list($r, $g, $b) = array(
                    $color[0].$color[1],
                    $color[2].$color[3],
                    $color[4].$color[5]
                );
            } elseif (strlen($color) == 3) {
                list($r, $g, $b) = array(
                    $color[0].$color[0],
                    $color[1].$color[1],
                    $color[2].$color[2]
                );
            } else {
                return false;
            }
            return array(
                'r' => hexdec($r),
                'g' => hexdec($g),
                'b' => hexdec($b),
                'a' => 0
            );

        } elseif (is_array($color) && (count($color) == 3 || count($color) == 4)) {

            if (isset($color['r'], $color['g'], $color['b'])) {
                return array(
                    'r' => $this->keep_within($color['r'], 0, 255),
                    'g' => $this->keep_within($color['g'], 0, 255),
                    'b' => $this->keep_within($color['b'], 0, 255),
                    'a' => $this->keep_within(isset($color['a']) ? $color['a'] : 0, 0, 127)
                );
            } elseif (isset($color[0], $color[1], $color[2])) {
                return array(
                    'r' => $this->keep_within($color[0], 0, 255),
                    'g' => $this->keep_within($color[1], 0, 255),
                    'b' => $this->keep_within($color[2], 0, 255),
                    'a' => $this->keep_within(isset($color[3]) ? $color[3] : 0, 0, 127)
                );
            }

        }
        return false;
    }

}
