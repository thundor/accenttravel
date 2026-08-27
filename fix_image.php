<?php
$path = $_SERVER['REDIRECT_URL'];
$real_path = preg_replace('~^optimized/~', '', ltrim($path, '/'));
$ext = pathinfo($real_path, PATHINFO_EXTENSION);
if($ext == 'webp'){
    $real_path = preg_replace('~(^[^/]+)/(.*?)\.webp$~', '\2.\1', $real_path);
    // dd($real_path);
}
for(;;){
    $full_path = __DIR__ . '/' . $real_path;
    if(!is_file($full_path)){
        header('Reason: ' . __LINE__);
        break;
    }
    
    $img_size = getimagesize($full_path);
    if(!$img_size){
        header('Reason: ' . __LINE__);
        break;
    }
    list($width, $height) = $img_size;
    if(!($width && $height)){
        header('Reason: ' . __LINE__);
        break;
    }
    try{
        $image = new Image($full_path);
    } catch(\Exception $e){
        header('Reason: ' . __LINE__);
        break;
    }
    
    $optimized_path = __DIR__ . '/' . ltrim($path, '/');
    $optimized_dir = dirname($optimized_path);
    if(!is_dir($optimized_dir)){
        $made_dir = mkdir($optimized_dir, 0755, true);
        if(!$made_dir || !is_dir($optimized_dir)){
            header('Reason: ' . __LINE__);
            break;
        }
    }
    try{
        $image->save($optimized_path);
    } catch(\Exception $e){
        header('Reason: ' . __LINE__);
        break;
    }
    $new_size = filesize($optimized_path);
    if(!$new_size || $new_size > filesize($full_path)){
        // se copiaza cu extensie diferita dar ACELASI TIP DE IMAGINE. deci pot fi png-uri cu extensie webp (ca exemplu)
        $copied = copy($full_path, $optimized_path);
        if(!$copied){
            header('Reason: ' . __LINE__);
            break;
        }
    }
    
    header("Refresh:0; url=" . $path);
    header('Content-Disposition: attachment; filename="' . urlencode(basename($path)) . '"');
    header("Location: " . $path);
    exit;
    break;
}

header("HTTP/1.0 404 Not Found");
echo "Image not found.\n";
die;

function dumpsimple(){ foreach(func_get_args() as $arg) echo htmlspecialchars(print_r($arg, true)); flush();}
function dump(){ echo '<pre style="white-space:pre-wrap">'; foreach(func_get_args() as $arg) echo htmlspecialchars(print_r($arg, true)); echo '</pre>'; flush();}
function dd(){ dump(... func_get_args()); die;}
function simple_trace($trace){ 
	return array_map(function($v){ return array_diff_key($v, ['args' => 1, 'object' => 1]); },$trace);
}
function simple_debug_backtrace(){ 
	return simple_trace(debug_backtrace(...func_get_args()));
}
class Image {
	private $file;
	private $image;
	private $width;
	private $height;
	private $bits;
	private $mime;

	/**
	 * Constructor
	 *
	 * @param	string	$file
	 *
 	*/
	public function __construct($file) {
		if (!extension_loaded('gd')) {
			exit('Error: PHP GD is not installed!');
		}
		
		if (file_exists($file)) {
			$this->file = $file;

			
    # BEGIN - opencart_image_on_demand.ocmod.xml
	$info = $this->getimagesize($file);
    # END - opencart_image_on_demand.ocmod.xml
    

			$this->width  = $info[0];
			$this->height = $info[1];
			$this->bits = isset($info['bits']) ? $info['bits'] : '';
			$this->mime = isset($info['mime']) ? $info['mime'] : '';

			if ($this->mime == 'image/gif') {
				$this->image = imagecreatefromgif($file);
			
    # BEGIN - opencart_image_on_demand.ocmod.xml
	} elseif ($this->mime == 'image/webp') {
		$this->image = imagecreatefromwebp($file);
	} elseif ($this->mime == 'image/png' || $this->mime == 'image/x-png') {
    # END - opencart_image_on_demand.ocmod.xml
    
				$this->image = imagecreatefrompng($file);
			
    # BEGIN - opencart_image_on_demand.ocmod.xml
	} elseif ($this->mime == 'image/jpeg' || $this->mime == 'image/pjpeg') {
    # END - opencart_image_on_demand.ocmod.xml
    
				$this->image = imagecreatefromjpeg($file);
			}

    # BEGIN - opencart_image_on_demand.ocmod.xml
	if($this->image){
		if (function_exists('exif_read_data')) {
		$exif = @exif_read_data($this->file);
		if($exif && isset($exif['Orientation'])) {
		  $orientation = $exif['Orientation'];
		  if($orientation != 1){
			$deg = 0;
			switch ($orientation) {
			  case 3:
				$deg = 180;
				break;
			  case 6:
				$deg = 270;
				$w = $this->width;
				$this->width = $this->height;
				$this->height = $w;
				break;
			  case 8:
				$deg = 90;
				$w = $this->width;
				$this->width = $this->height;
				$this->height = $w;
				break;
			}
			if ($deg) {
			  $this->image = imagerotate($this->image, $deg, 0);        
			}
		  } // if there is some rotation necessary
		} // if have the exif orientation info
	  } // if function exists
	}
    # END - opencart_image_on_demand.ocmod.xml
    
		} else {
			throw new \Exception('Error: Could not load image ' . $file . '!');
		}
	}
	
	/**
     * 
	 * 
	 * @return	string
     */
	public function getFile() {
		return $this->file;
	}

	/**
     * 
	 * 
	 * @return	array
     */
	public function getImage() {
		return $this->image;
	}
	
	/**
     * 
	 * 
	 * @return	string
     */
	public function getWidth() {
		return $this->width;
	}
	
	/**
     * 
	 * 
	 * @return	string
     */
	public function getHeight() {
		return $this->height;
	}
	
	/**
     * 
	 * 
	 * @return	string
     */
	public function getBits() {
		return $this->bits;
	}
	
	/**
     * 
	 * 
	 * @return	string
     */
	public function getMime() {
		return $this->mime;
	}
	
	/**
     * 
     *
     * @param	string	$file
	 * @param	int		$quality
     */
	public function save($file, $quality = 90) {
		$info = pathinfo($file);

		$extension = strtolower($info['extension']);

		if (is_resource($this->image)) {
			if ($extension == 'jpeg' || $extension == 'jpg') {
				imagejpeg($this->image, $file, $quality);
			
    # BEGIN - opencart_image_on_demand.ocmod.xml
	} elseif ($extension == 'webp') {
		imagepalettetotruecolor($this->image);
		imagewebp($this->image, $file);
	} elseif ($extension == 'png') {
    # END - opencart_image_on_demand.ocmod.xml
    
				imagepng($this->image, $file);
			} elseif ($extension == 'gif') {
				imagegif($this->image, $file);
			}

			imagedestroy($this->image);
		}
	}
	
	/**
     * 
     *
     * @param	int	$width
	 * @param	int	$height
	 * @param	string	$default
     */
	public function resize($width = 0, $height = 0, $default = '') {
		if (!$this->width || !$this->height) {
			return;
		}

		$xpos = 0;
		$ypos = 0;
		$scale = 1;

		$scale_w = $width / $this->width;
		$scale_h = $height / $this->height;

		if ($default == 'w') {
			$scale = $scale_w;
		} elseif ($default == 'h') {
			$scale = $scale_h;
		} else {
			$scale = min($scale_w, $scale_h);
		}

		if ($scale == 1 && $scale_h == $scale_w && $this->mime != 'image/png' && $this->mime != 'image/webp') {
			return;
		}

		$new_width = (int)($this->width * $scale);
		$new_height = (int)($this->height * $scale);
		$xpos = (int)(($width - $new_width) / 2);
		$ypos = (int)(($height - $new_height) / 2);

		$image_old = $this->image;
		$this->image = imagecreatetruecolor($width, $height);

		if ($this->mime == 'image/png' || $this->mime == 'image/webp') {
			imagealphablending($this->image, false);
			imagesavealpha($this->image, true);
			$background = imagecolorallocatealpha($this->image, 255, 255, 255, 127);
			imagecolortransparent($this->image, $background);
		} else {
			$background = imagecolorallocate($this->image, 255, 255, 255);
		}

		imagefilledrectangle($this->image, 0, 0, $width, $height, $background);

		imagecopyresampled($this->image, $image_old, $xpos, $ypos, 0, 0, $new_width, $new_height, $this->width, $this->height);
		imagedestroy($image_old);

		$this->width = $width;
		$this->height = $height;
	}
	
	/**
     * 
     *
     * @param	string	$watermark
	 * @param	string	$position
     */
	public function watermark($watermark, $position = 'bottomright') {
		switch($position) {
			case 'topleft':
				$watermark_pos_x = 0;
				$watermark_pos_y = 0;
				break;
			case 'topcenter':
				$watermark_pos_x = intval(($this->width - $watermark->getWidth()) / 2);
				$watermark_pos_y = 0;
				break;
			case 'topright':
				$watermark_pos_x = $this->width - $watermark->getWidth();
				$watermark_pos_y = 0;
				break;
			case 'middleleft':
				$watermark_pos_x = 0;
				$watermark_pos_y = intval(($this->height - $watermark->getHeight()) / 2);
				break;
			case 'middlecenter':
				$watermark_pos_x = intval(($this->width - $watermark->getWidth()) / 2);
				$watermark_pos_y = intval(($this->height - $watermark->getHeight()) / 2);
				break;
			case 'middleright':
				$watermark_pos_x = $this->width - $watermark->getWidth();
				$watermark_pos_y = intval(($this->height - $watermark->getHeight()) / 2);
				break;
			case 'bottomleft':
				$watermark_pos_x = 0;
				$watermark_pos_y = $this->height - $watermark->getHeight();
				break;
			case 'bottomcenter':
				$watermark_pos_x = intval(($this->width - $watermark->getWidth()) / 2);
				$watermark_pos_y = $this->height - $watermark->getHeight();
				break;
			case 'bottomright':
				$watermark_pos_x = $this->width - $watermark->getWidth();
				$watermark_pos_y = $this->height - $watermark->getHeight();
				break;
		}
		
		imagealphablending( $this->image, true );
		imagesavealpha( $this->image, true );
		imagecopy($this->image, $watermark->getImage(), $watermark_pos_x, $watermark_pos_y, 0, 0, $watermark->getWidth(), $watermark->getHeight());

		imagedestroy($watermark->getImage());
	}
	
	/**
     * 
     *
     * @param	int		$top_x
	 * @param	int		$top_y
	 * @param	int		$bottom_x
	 * @param	int		$bottom_y
     */
	public function crop($top_x, $top_y, $bottom_x, $bottom_y) {
		$image_old = $this->image;
		$this->image = imagecreatetruecolor($bottom_x - $top_x, $bottom_y - $top_y);

		imagecopy($this->image, $image_old, 0, 0, $top_x, $top_y, $this->width, $this->height);
		imagedestroy($image_old);

		$this->width = $bottom_x - $top_x;
		$this->height = $bottom_y - $top_y;
	}
	
	/**
     * 
     *
     * @param	int		$degree
	 * @param	string	$color
     */
	public function rotate($degree, $color = 'FFFFFF') {
		$rgb = $this->html2rgb($color);

		$this->image = imagerotate($this->image, $degree, imagecolorallocate($this->image, $rgb[0], $rgb[1], $rgb[2]));

		$this->width = imagesx($this->image);
		$this->height = imagesy($this->image);
	}
	
	/**
     * 
     *
     */
	private function filter() {
        $args = func_get_args();

        call_user_func_array('imagefilter', $args);
	}
	
	/**
     * 
     *
     * @param	string	$text
	 * @param	int		$x
	 * @param	int		$y 
	 * @param	int		$size
	 * @param	string	$color
     */
	private function text($text, $x = 0, $y = 0, $size = 5, $color = '000000') {
		$rgb = $this->html2rgb($color);

		imagestring($this->image, $size, $x, $y, $text, imagecolorallocate($this->image, $rgb[0], $rgb[1], $rgb[2]));
	}
	
	/**
     * 
     *
     * @param	object	$merge
	 * @param	object	$x
	 * @param	object	$y
	 * @param	object	$opacity
     */
	private function merge($merge, $x = 0, $y = 0, $opacity = 100) {
		imagecopymerge($this->image, $merge->getImage(), $x, $y, 0, 0, $merge->getWidth(), $merge->getHeight(), $opacity);
	}
	
	/**
     * 
     *
     * @param	string	$color
	 * 
	 * @return	array
     */

	# BEGIN - opencart_image_on_demand.ocmod.xml
	private function getimagesize($file){
		$info = getimagesize($file);
		if($info){
			return $info;
		}
		$ext = pathinfo($file, PATHINFO_EXTENSION);
		switch($ext){
			case 'webp':
				$img = imagecreatefromwebp($file);
				if($img){
					$info = array(
						imagesx($img),
						imagesy($img),
						'bits' => filesize($file),
						'mime' => 'image/webp',
					);
					if(imagesx($img) && imagesy($img)){
						$another_acceptable_image_type = true;
					}
					imagedestroy($img);
				}
			break;
		}
		return $info;
	}
	# END - opencart_image_on_demand.ocmod.xml 
	private function html2rgb($color) {
		if ($color[0] == '#') {
			$color = substr($color, 1);
		}

		if (strlen($color) == 6) {
			list($r, $g, $b) = array($color[0] . $color[1], $color[2] . $color[3], $color[4] . $color[5]);
		} elseif (strlen($color) == 3) {
			list($r, $g, $b) = array($color[0] . $color[0], $color[1] . $color[1], $color[2] . $color[2]);
		} else {
			return false;
		}

		$r = hexdec($r);
		$g = hexdec($g);
		$b = hexdec($b);

		return array($r, $g, $b);
	}
}