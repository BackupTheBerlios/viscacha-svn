<?php
/**
 * thumbnail.inc.php
 *
 * @author 		Ian Selby (ian@gen-x-design.com)
 * @copyright 	Copyright 2006
 * @version 	1.1 (PHP5)
 * @author 		Modified by Matthias Mohr, 2008
 * @package 	Core
 * @subpackage 	Images
 */

/**
 * PHP class for dynamically resizing, cropping, and rotating images for thumbnail purposes and either displaying them on-the-fly or saving them.
 */
class Thumbnail {
    /**
     * Error message to display, if any
     *
     * @var string
     */
    private $errmsg;
    /**
     * Whether or not there is an error
     *
     * @var boolean
     */
    private $error;
    /**
     * Format of the image file
     *
     * @var string
     */
    private $format;
    /**
     * File name and path of the image file
     *
     * @var string
     */
    private $fileName;
    /**
     * Image meta data if any is available (jpeg/tiff) via the exif library
     *
     * @var array
     */
    public $imageMeta;
    /**
     * Current dimensions of working image
     *
     * @var array
     */
    private $currentDimensions;
    /**
     * New dimensions of working image
     *
     * @var array
     */
    private $newDimensions;
    /**
     * Image resource for newly manipulated image
     *
     * @var resource
     */
    private $newImage;
    /**
     * Image resource for image before previous manipulation
     *
     * @var resource
     */
    private $oldImage;
    /**
     * Image resource for image being currently manipulated
     *
     * @var resource
     */
    private $workingImage;
    /**
     * Percentage to resize image by
     *
     * @var int
     */
    private $percent;
    /**
     * Maximum width of image during resize
     *
     * @var int
     */
    private $maxWidth;
    /**
     * Maximum height of image during resize
     *
     * @var int
     */
    private $maxHeight;
    /**
     * Status of the UnsharpMask usage
     *
     * @var boolean
     */
    private $sharpener;

    /**
     * Class constructor
     *
     * @param string $fileName
     * @return Thumbnail
     */
    public function __construct($fileName) {
        //make sure the GD library is installed
    	if(!function_exists("gd_info")) {
        	echo 'You do not have the GD Library installed.  This class requires the GD library to function properly.' . "\n";
        	echo 'visit http://us2.php.net/manual/en/ref.image.php for more information';
        	exit;
        }
    	//initialize variables
        $this->errmsg               = '';
        $this->error                = false;
        $this->currentDimensions    = array();
        $this->newDimensions        = array();
        $this->fileName             = $fileName;
        $this->imageMeta			= array();
        $this->percent              = 100;
        $this->maxWidth             = 0;
        $this->maxHeight            = 0;
        $this->sharpener			= false;

        //check to see if file exists
        if(!file_exists($this->fileName)) {
            $this->errmsg = 'File not found';
            $this->error = true;
        }
        //check to see if file is readable
        elseif(!is_readable($this->fileName)) {
            $this->errmsg = 'File is not readable';
            $this->error = true;
        }

        //if there are no errors, determine the file format
        if($this->error == false) {
            //check if gif
            if(stristr(strtolower($this->fileName),'.gif')) $this->format = 'GIF';
            //check if jpg
            elseif(stristr(strtolower($this->fileName),'.jpg') || stristr(strtolower($this->fileName),'.jpeg')) $this->format = 'JPG';
            //check if png
            elseif(stristr(strtolower($this->fileName),'.png')) $this->format = 'PNG';
            //unknown file format
            else {
                $this->errmsg = 'Unknown file format';
                $this->error = true;
            }
        }

        //initialize resources if no errors
        if($this->error == false) {
            switch($this->format) {
                case 'GIF':
                    $this->oldImage = ImageCreateFromGif($this->fileName);
                    break;
                case 'JPG':
                    $this->oldImage = ImageCreateFromJpeg($this->fileName);
                    break;
                case 'PNG':
                    $this->oldImage = ImageCreateFromPng($this->fileName);
                    break;
            }

            $size = GetImageSize($this->fileName);
            $this->currentDimensions = array('width'=>$size[0],'height'=>$size[1]);
            $this->newImage = $this->oldImage;
            $this->gatherImageMeta();
        }

        if($this->error == true) {
            $this->showErrorImage();
            break;
        }
    }

    /**
     * Class destructor
     *
     */
    public function __destruct() {
        if(is_resource($this->newImage)) @ImageDestroy($this->newImage);
        if(is_resource($this->oldImage)) @ImageDestroy($this->oldImage);
        if(is_resource($this->workingImage)) @ImageDestroy($this->workingImage);
    }

    /**
     * Returns the current width of the image
     *
     * @return int
     */
    private function getCurrentWidth() {
        return $this->currentDimensions['width'];
    }

    /**
     * Returns the current height of the image
     *
     * @return int
     */
    private function getCurrentHeight() {
        return $this->currentDimensions['height'];
    }

    /**
     * Calculates new image width
     *
     * @param int $width
     * @param int $height
     * @return array
     */
    private function calcWidth($width,$height) {
        $newWp = (100 * $this->maxWidth) / $width;
        $newHeight = ($height * $newWp) / 100;
        return array('newWidth'=>intval($this->maxWidth),'newHeight'=>intval($newHeight));
    }

    /**
     * Calculates new image height
     *
     * @param int $width
     * @param int $height
     * @return array
     */
    private function calcHeight($width,$height) {
        $newHp = (100 * $this->maxHeight) / $height;
        $newWidth = ($width * $newHp) / 100;
        return array('newWidth'=>intval($newWidth),'newHeight'=>intval($this->maxHeight));
    }

    /**
     * Calculates new image size based on percentage
     *
     * @param int $width
     * @param int $height
     * @return array
     */
    private function calcPercent($width,$height) {
        $newWidth = ($width * $this->percent) / 100;
        $newHeight = ($height * $this->percent) / 100;
        return array('newWidth'=>intval($newWidth),'newHeight'=>intval($newHeight));
    }

    /**
     * Calculates new image size based on width and height, while constraining to maxWidth and maxHeight
     *
     * @param int $width
     * @param int $height
     */
    private function calcImageSize($width,$height) {
        $newSize = array('newWidth'=>$width,'newHeight'=>$height);

        if($this->maxWidth > 0) {

            $newSize = $this->calcWidth($width,$height);

            if($this->maxHeight > 0 && $newSize['newHeight'] > $this->maxHeight) {
                $newSize = $this->calcHeight($newSize['newWidth'],$newSize['newHeight']);
            }

            //$this->newDimensions = $newSize;
        }

        if($this->maxHeight > 0) {
            $newSize = $this->calcHeight($width,$height);

            if($this->maxWidth > 0 && $newSize['newWidth'] > $this->maxWidth) {
                $newSize = $this->calcWidth($newSize['newWidth'],$newSize['newHeight']);
            }

            //$this->newDimensions = $newSize;
        }

        $this->newDimensions = $newSize;
    }

    /**
     * Calculates new image size based percentage
     *
     * @param int $width
     * @param int $height
     */
    private function calcImageSizePercent($width,$height) {
        if($this->percent > 0) {
            $this->newDimensions = $this->calcPercent($width,$height);
        }
    }

    /**
     * Displays error image
     *
     */
    private function showErrorImage() {
        header('Content-type: image/png');
        $errImg = ImageCreate(220,25);
        $bgColor = imagecolorallocate($errImg,0,0,0);
        $fgColor1 = imagecolorallocate($errImg,255,255,255);
        $fgColor2 = imagecolorallocate($errImg,255,0,0);
        imagestring($errImg,3,6,6,'Error:',$fgColor2);
        imagestring($errImg,3,55,6,$this->errmsg,$fgColor1);
        imagepng($errImg);
        imagedestroy($errImg);
    }

    /**
     * Resizes image to maxWidth x maxHeight
     *
     * @param int $maxWidth
     * @param int $maxHeight
     */
    public function resize($maxWidth = 0, $maxHeight = 0) {
        $this->maxWidth = $maxWidth;
        $this->maxHeight = $maxHeight;

        $this->calcImageSize($this->currentDimensions['width'],$this->currentDimensions['height']);

		if(function_exists("ImageCreateTrueColor")) {
			$this->workingImage = ImageCreateTrueColor($this->newDimensions['newWidth'],$this->newDimensions['newHeight']);
		}
		else {
			$this->workingImage = ImageCreate($this->newDimensions['newWidth'],$this->newDimensions['newHeight']);
		}

		ImageCopyResampled(
			$this->workingImage,
			$this->oldImage,
			0,
			0,
			0,
			0,
			$this->newDimensions['newWidth'],
			$this->newDimensions['newHeight'],
			$this->currentDimensions['width'],
			$this->currentDimensions['height']
		);

		$this->oldImage = $this->workingImage;
		$this->newImage = $this->workingImage;
		$this->currentDimensions['width'] = $this->newDimensions['newWidth'];
		$this->currentDimensions['height'] = $this->newDimensions['newHeight'];
	}

	/**
	 * Resizes the image by $percent percent
	 *
	 * @param int $percent
	 */
	public function resizePercent($percent = 0) {
	    $this->percent = $percent;

	    $this->calcImageSizePercent($this->currentDimensions['width'],$this->currentDimensions['height']);

		if(function_exists("ImageCreateTrueColor")) {
			$this->workingImage = ImageCreateTrueColor($this->newDimensions['newWidth'],$this->newDimensions['newHeight']);
		}
		else {
			$this->workingImage = ImageCreate($this->newDimensions['newWidth'],$this->newDimensions['newHeight']);
		}

		ImageCopyResampled(
			$this->workingImage,
			$this->oldImage,
			0,
			0,
			0,
			0,
			$this->newDimensions['newWidth'],
			$this->newDimensions['newHeight'],
			$this->currentDimensions['width'],
			$this->currentDimensions['height']
		);

		$this->oldImage = $this->workingImage;
		$this->newImage = $this->workingImage;
		$this->currentDimensions['width'] = $this->newDimensions['newWidth'];
		$this->currentDimensions['height'] = $this->newDimensions['newHeight'];
	}

	/**
	 * Crops the image from calculated center in a square of $cropSize pixels
	 *
	 * @param int $cropSize
	 */
	public function cropFromCenter($cropSize) {
	    if($cropSize > $this->currentDimensions['width']) $cropSize = $this->currentDimensions['width'];
	    if($cropSize > $this->currentDimensions['height']) $cropSize = $this->currentDimensions['height'];

	    $cropX = intval(($this->currentDimensions['width'] - $cropSize) / 2);
	    $cropY = intval(($this->currentDimensions['height'] - $cropSize) / 2);

	    if(function_exists("ImageCreateTrueColor")) {
			$this->workingImage = ImageCreateTrueColor($cropSize,$cropSize);
		}
		else {
			$this->workingImage = ImageCreate($cropSize,$cropSize);
		}

		imagecopyresampled(
            $this->workingImage,
            $this->oldImage,
            0,
            0,
            $cropX,
            $cropY,
            $cropSize,
            $cropSize,
            $cropSize,
            $cropSize
		);

		$this->oldImage = $this->workingImage;
		$this->newImage = $this->workingImage;
		$this->currentDimensions['width'] = $cropSize;
		$this->currentDimensions['height'] = $cropSize;
	}

	/**
	 * Advanced cropping function that crops an image using $startX and $startY as the upper-left hand corner.
	 *
	 * @param int $startX
	 * @param int $startY
	 * @param int $width
	 * @param int $height
	 */
	public function crop($startX,$startY,$width,$height) {
	    //make sure the cropped area is not greater than the size of the image
	    if($width > $this->currentDimensions['width']) $width = $this->currentDimensions['width'];
	    if($height > $this->currentDimensions['height']) $height = $this->currentDimensions['height'];
	    //make sure not starting outside the image
	    if(($startX + $width) > $this->currentDimensions['width']) $startX = ($this->currentDimensions['width'] - $width);
	    if(($startY + $height) > $this->currentDimensions['height']) $startY = ($this->currentDimensions['height'] - $height);
	    if($startX < 0) $startX = 0;
	    if($startY < 0) $startY = 0;

	    if(function_exists("ImageCreateTrueColor")) {
			$this->workingImage = ImageCreateTrueColor($width,$height);
		}
		else {
			$this->workingImage = ImageCreate($width,$height);
		}

		imagecopyresampled(
            $this->workingImage,
            $this->oldImage,
            0,
            0,
            $startX,
            $startY,
            $width,
            $height,
            $width,
            $height
		);

		$this->oldImage = $this->workingImage;
		$this->newImage = $this->workingImage;
		$this->currentDimensions['width'] = $width;
		$this->currentDimensions['height'] = $height;
	}

	public function addWatermarkText($textString, $textFont, $textSize = "12", $shadow = false) {
		$imageWidth = $this->currentDimensions['width'];
		$imageHeight = $this->currentDimensions['height'];

		// Create some colors
		$white = imagecolorallocate($this->newImage, 255, 255, 255);
		$black = imagecolorallocate($this->newImage, 0, 0, 0);

		// Get box info
		$box = imagettfbbox($textSize, 0, $textFont, $textString);

		//Find out the width and height of the text box
		$textW = $box[2] - $box[0];
		$textH = $box[5] - $box[3];

		// Calculate the positions
		$positionLeft = ($imageWidth - $textW)/2;
		$positionTop = (($imageHeight - $textH)/2);

		// Add some text
		imagettftext($this->newImage, $textSize, 0, $positionLeft+1, $positionTop+1, $black, $textFont, $textString);
		imagettftext($this->newImage, $textSize, 0, $positionLeft, $positionTop, $white, $textFont, $textString);
	}

	/**
	 * Outputs the image to the screen, or saves to $name if supplied.  Quality of JPEG images can be controlled with the $quality variable
	 *
	 * @param int $quality
	 * @param string $name
	 */
	public function show($quality=100,$name = '') {
		if ($this->sharpener == true) {
			$this->newImage = $this->UnsharpMask($this->newImage);
		}
	    switch($this->format) {
	        case 'GIF':
	            if($name != '') {
	                ImageGif($this->newImage,$name);
	            }
	            else {
	               header('Content-type: image/gif');
	               ImageGif($this->newImage);
	            }
	            break;
	        case 'JPG':
	            if($name != '') {
	                ImageJpeg($this->newImage,$name,$quality);
	            }
	            else {
	               header('Content-type: image/jpeg');
	               ImageJpeg($this->newImage,'',$quality);
	            }
	            break;
	        case 'PNG':
	            if($name != '') {
	                ImagePng($this->newImage,$name);
	            }
	            else {
	               header('Content-type: image/png');
	               ImagePng($this->newImage);
	            }
	            break;
	    }
	}

	/**
	 * Saves image as $name (can include file path), with quality of # percent if file is a jpeg
	 *
	 * @param string $name
	 * @param int $quality
	 */
	public function save($name,$quality=100) {
	    $this->show($quality,$name);
	}

	/**
	 * Enables the unsharp mask.
	 *
	 * @param boolean true to enable, false to disable
	 */
	public function setUnsharpMask($status = true) {
		$this->sharpener = (bool) $status;
	}

	/**
	 * Creates Apple-style reflection under image, optionally adding a border to main image
	 *
	 * @param int $percent
	 * @param int $reflection
	 * @param int $white
	 * @param bool $border
	 * @param string $borderColor
	 */
	public function createReflection($percent,$reflection,$white,$border = true,$borderColor = '#a4a4a4') {
        $width = $this->currentDimensions['width'];
        $height = $this->currentDimensions['height'];

        $reflectionHeight = intval($height * ($reflection / 100));
        $newHeight = $height + $reflectionHeight;
        $reflectedPart = $height * ($percent / 100);

        $this->workingImage = ImageCreateTrueColor($width,$newHeight);

        ImageAlphaBlending($this->workingImage,true);

        $colorToPaint = ImageColorAllocateAlpha($this->workingImage,255,255,255,0);
        ImageFilledRectangle($this->workingImage,0,0,$width,$newHeight,$colorToPaint);

        imagecopyresampled(
                            $this->workingImage,
                            $this->newImage,
                            0,
                            0,
                            0,
                            $reflectedPart,
                            $width,
                            $reflectionHeight,
                            $width,
                            ($height - $reflectedPart));
        $this->imageFlipVertical();

        imagecopy($this->workingImage,$this->newImage,0,0,0,0,$width,$height);

        imagealphablending($this->workingImage,true);

        for($i=0;$i<$reflectionHeight;$i++) {
            $colorToPaint = imagecolorallocatealpha($this->workingImage,255,255,255,($i/$reflectionHeight*-1+1)*$white);
            imagefilledrectangle($this->workingImage,0,$height+$i,$width,$height+$i,$colorToPaint);
        }

        if($border == true) {
            $rgb = $this->hex2rgb($borderColor,false);
            $colorToPaint = imagecolorallocate($this->workingImage,$rgb[0],$rgb[1],$rgb[2]);
            imageline($this->workingImage,0,0,$width,0,$colorToPaint); //top line
            imageline($this->workingImage,0,$height,$width,$height,$colorToPaint); //bottom line
            imageline($this->workingImage,0,0,0,$height,$colorToPaint); //left line
            imageline($this->workingImage,$width-1,0,$width-1,$height,$colorToPaint); //right line
        }

        $this->oldImage = $this->workingImage;
		$this->newImage = $this->workingImage;
		$this->currentDimensions['width'] = $width;
		$this->currentDimensions['height'] = $newHeight;
	}

	/**
	 * Inverts working image, used by reflection function
	 *
	 */
	private function imageFlipVertical() {
	    $x_i = imagesx($this->workingImage);
	    $y_i = imagesy($this->workingImage);

	    for($x = 0; $x < $x_i; $x++) {
	        for($y = 0; $y < $y_i; $y++) {
	            imagecopy($this->workingImage,$this->workingImage,$x,$y_i - $y - 1, $x, $y, 1, 1);
	        }
	    }
	}

	/**
	 * Converts hexidecimal color value to rgb values and returns as array/string
	 *
	 * @param string $hex
	 * @param bool $asString
	 * @return array|string
	 */
	private function hex2rgb($hex, $asString = false) {
        // strip off any leading #
        if (0 === strpos($hex, '#')) {
           $hex = substr($hex, 1);
        } else if (0 === strpos($hex, '&H')) {
           $hex = substr($hex, 2);
        }

        // break into hex 3-tuple
        $cutpoint = ceil(strlen($hex) / 2)-1;
        $rgb = explode(':', wordwrap($hex, $cutpoint, ':', $cutpoint), 3);

        // convert each tuple to decimal
        $rgb[0] = (isset($rgb[0]) ? hexdec($rgb[0]) : 0);
        $rgb[1] = (isset($rgb[1]) ? hexdec($rgb[1]) : 0);
        $rgb[2] = (isset($rgb[2]) ? hexdec($rgb[2]) : 0);

        return ($asString ? "{$rgb[0]} {$rgb[1]} {$rgb[2]}" : $rgb);
    }

    /**
     * Reads selected exif meta data from jpg images and populates $this->imageMeta with appropriate values if found
     *
     */
    private function gatherImageMeta() {
    	//only attempt to retrieve info if exif exists
    	if(function_exists("exif_read_data") && $this->format == 'JPG') {
			$imageData = exif_read_data($this->fileName);
			if(isset($imageData['Make']))
				$this->imageMeta['make'] = ucwords(strtolower($imageData['Make']));
			if(isset($imageData['Model']))
				$this->imageMeta['model'] = $imageData['Model'];
			if(isset($imageData['COMPUTED']['ApertureFNumber'])) {
				$this->imageMeta['aperture'] = $imageData['COMPUTED']['ApertureFNumber'];
				$this->imageMeta['aperture'] = str_replace('/','',$this->imageMeta['aperture']);
			}
			if(isset($imageData['ExposureTime'])) {
				$exposure = explode('/',$imageData['ExposureTime']);
				$exposure = round($exposure[1]/$exposure[0],-1);
				$this->imageMeta['exposure'] = '1/' . $exposure . ' second';
			}
			if(isset($imageData['Flash'])) {
				if($imageData['Flash'] > 0) {
					$this->imageMeta['flash'] = 'Yes';
				}
				else {
					$this->imageMeta['flash'] = 'No';
				}
			}
			if(isset($imageData['FocalLength'])) {
				$focus = explode('/',$imageData['FocalLength']);
				$this->imageMeta['focalLength'] = round($focus[0]/$focus[1],2) . ' mm';
			}
			if(isset($imageData['DateTime'])) {
				$date = $imageData['DateTime'];
				$date = explode(' ',$date);
				$date = str_replace(':','-',$date[0]) . ' ' . $date[1];
				$this->imageMeta['dateTaken'] = date('m/d/Y g:i A',strtotime($date));
			}
    	}
    }

    /**
     * Rotates image either 90 degrees clockwise or counter-clockwise
     *
     * @param string $direction
     */
    public function rotateImage($direction = 'CW') {
    	if($direction == 'CW') {
    		$this->workingImage = imagerotate($this->workingImage,-90,0);
    	}
    	else {
    		$this->workingImage = imagerotate($this->workingImage,90,0);
    	}
    	$newWidth = $this->currentDimensions['height'];
    	$newHeight = $this->currentDimensions['width'];
		$this->oldImage = $this->workingImage;
		$this->newImage = $this->workingImage;
		$this->currentDimensions['width'] = $newWidth;
		$this->currentDimensions['height'] = $newHeight;
    }

	/**
	 * phpUnsharpMask - Unsharp mask algorithm by Torstein Hønsi 2003.
	 *
	 * thoensi_at_netcom_dot_no - Please leave this notice.
	 *
	 * @param resource $img
	 * @param int $amount
	 * @param floor $radius
	 * @param int $threshold
	 * @return resource
	 */
	private function UnsharpMask($img, $amount=80, $radius=0.5, $threshold=3)	{
		// $img is an image that is already created within php using
		// imgcreatetruecolor. No url! $img must be a truecolor image.

		// Attempt to calibrate the parameters to Photoshop:
		if ($amount > 500)	$amount = 500;
		$amount = $amount * 0.016;
		if ($radius > 50)	$radius = 50;
		$radius = $radius * 2;
		if ($threshold > 255)	$threshold = 255;

		$radius = abs(round($radius)); 	// Only integers make sense.
		if ($radius == 0) {
			return $img; imagedestroy($img); break;		}
		$w = imagesx($img); $h = imagesy($img);
		$imgCanvas = imagecreatetruecolor($w, $h);
		$imgCanvas2 = imagecreatetruecolor($w, $h);
		$imgBlur = imagecreatetruecolor($w, $h);
		$imgBlur2 = imagecreatetruecolor($w, $h);
		imagecopy ($imgCanvas, $img, 0, 0, 0, 0, $w, $h);
		imagecopy ($imgCanvas2, $img, 0, 0, 0, 0, $w, $h);


		// Gaussian blur matrix:
		//
		//	1	2	1
		//	2	4	2
		//	1	2	1
		//
		//////////////////////////////////////////////////

		// Move copies of the image around one pixel at the time and merge them with weight
		// according to the matrix. The same matrix is simply repeated for higher radii.
		for ($i = 0; $i < $radius; $i++)	{
			imagecopy ($imgBlur, $imgCanvas, 0, 0, 1, 1, $w - 1, $h - 1); // up left
			imagecopymerge ($imgBlur, $imgCanvas, 1, 1, 0, 0, $w, $h, 50); // down right
			imagecopymerge ($imgBlur, $imgCanvas, 0, 1, 1, 0, $w - 1, $h, 33.33333); // down left
			imagecopymerge ($imgBlur, $imgCanvas, 1, 0, 0, 1, $w, $h - 1, 25); // up right
			imagecopymerge ($imgBlur, $imgCanvas, 0, 0, 1, 0, $w - 1, $h, 33.33333); // left
			imagecopymerge ($imgBlur, $imgCanvas, 1, 0, 0, 0, $w, $h, 25); // right
			imagecopymerge ($imgBlur, $imgCanvas, 0, 0, 0, 1, $w, $h - 1, 20 ); // up
			imagecopymerge ($imgBlur, $imgCanvas, 0, 1, 0, 0, $w, $h, 16.666667); // down
			imagecopymerge ($imgBlur, $imgCanvas, 0, 0, 0, 0, $w, $h, 50); // center
			imagecopy ($imgCanvas, $imgBlur, 0, 0, 0, 0, $w, $h);

			// During the loop above the blurred copy darkens, possibly due to a roundoff
			// error. Therefore the sharp picture has to go through the same loop to
			// produce a similar image for comparison. This is not a good thing, as processing
			// time increases heavily.
			imagecopy ($imgBlur2, $imgCanvas2, 0, 0, 0, 0, $w, $h);
			imagecopymerge ($imgBlur2, $imgCanvas2, 0, 0, 0, 0, $w, $h, 50);
			imagecopymerge ($imgBlur2, $imgCanvas2, 0, 0, 0, 0, $w, $h, 33.33333);
			imagecopymerge ($imgBlur2, $imgCanvas2, 0, 0, 0, 0, $w, $h, 25);
			imagecopymerge ($imgBlur2, $imgCanvas2, 0, 0, 0, 0, $w, $h, 33.33333);
			imagecopymerge ($imgBlur2, $imgCanvas2, 0, 0, 0, 0, $w, $h, 25);
			imagecopymerge ($imgBlur2, $imgCanvas2, 0, 0, 0, 0, $w, $h, 20 );
			imagecopymerge ($imgBlur2, $imgCanvas2, 0, 0, 0, 0, $w, $h, 16.666667);
			imagecopymerge ($imgBlur2, $imgCanvas2, 0, 0, 0, 0, $w, $h, 50);
			imagecopy ($imgCanvas2, $imgBlur2, 0, 0, 0, 0, $w, $h);

		}

		// Calculate the difference between the blurred pixels and the original
		// and set the pixels
		for ($x = 0; $x < $w; $x++)	{ // each row
			for ($y = 0; $y < $h; $y++)	{ // each pixel

				$rgbOrig = ImageColorAt($imgCanvas2, $x, $y);
				$rOrig = (($rgbOrig >> 16) & 0xFF);
				$gOrig = (($rgbOrig >> 8) & 0xFF);
				$bOrig = ($rgbOrig & 0xFF);

				$rgbBlur = ImageColorAt($imgCanvas, $x, $y);

				$rBlur = (($rgbBlur >> 16) & 0xFF);
				$gBlur = (($rgbBlur >> 8) & 0xFF);
				$bBlur = ($rgbBlur & 0xFF);

				// When the masked pixels differ less from the original
				// than the threshold specifies, they are set to their original value.
				$rNew = (abs($rOrig - $rBlur) >= $threshold)
					? max(0, min(255, ($amount * ($rOrig - $rBlur)) + $rOrig))
					: $rOrig;
				$gNew = (abs($gOrig - $gBlur) >= $threshold)
					? max(0, min(255, ($amount * ($gOrig - $gBlur)) + $gOrig))
					: $gOrig;
				$bNew = (abs($bOrig - $bBlur) >= $threshold)
					? max(0, min(255, ($amount * ($bOrig - $bBlur)) + $bOrig))
					: $bOrig;



				if (($rOrig != $rNew) || ($gOrig != $gNew) || ($bOrig != $bNew)) {
    				$pixCol = ImageColorAllocate($img, $rNew, $gNew, $bNew);
    				ImageSetPixel($img, $x, $y, $pixCol);
				}
			}
		}

		imagedestroy($imgCanvas);
		imagedestroy($imgCanvas2);
		imagedestroy($imgBlur);
		imagedestroy($imgBlur2);

		return $img;

	}

}
?>