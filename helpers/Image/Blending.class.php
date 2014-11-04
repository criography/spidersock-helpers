<?php
/**
	* CGY_Image_Blending
	* ---------------------------------------------------------------------
	* main image blending helpers
	*
	* @package      Davy's: Wine Merchants
	* @subpackage   CGY Helpers
	* @since        2.0
 *
	* @TODO separate error/exception handling
	*/


class CGY_Image_Blending{



/**
	* init
	* ---------------------------------------------------------------------
	* initiate class and expose its methods
	*
	* @return void
	*/
	private static function init() {
		$class = __CLASS__;
		new $class;
	}





/**
	* __construct
	* ---------------------------------------------------------------------
	*/
	public function __construct(){
		require_once( SOCKHELPERS . 'Wordpress/Utils/Url.class.php');
	}


	/**
	 * getColourMultipliedImg
	 * creates an image with colour multiply blending mode. Can be cropped.
	 *
	 * @uses Imagick
	 * @param string $srcURL Path or URL
	 * @param string $colour hex value of a colour to be used for blending
	 * @param int $width destination width
	 * @param int $height destination height
	 * @param string $valign vertical align of the crop ('top' | 'middle' | 'bottom'). Defaults to top.
	 * @param int $jpg_quality Quality of JPEG compression, defaults to 75
	 * @param string $suffix string attached to the filename. Defaults to '-blended'.
	 * @param bool $forceOverwrite should the image be overwritten?
	 * @return string path to the blended image
	 */

	public static function getColourMultipliedImg($srcURL, $colour='#ff0000', $width, $height, $valign, $jpg_quality=75, $suffix='-blended', $forceOverwrite=false){

		$blendedURL   = preg_replace('/(\.jpg|\.jpeg)/i', $suffix.'$1', $srcURL);   /* absolute to the docroot, without the domain name */
		$srcPath      = CGY_WP_Utils_Url::url_to_path($srcURL);                           /* absolute to the server root */
		$blendedPath  = CGY_WP_Utils_Url::url_to_path($blendedURL);                       /* absolute to the server root */


		if(!file_exists($blendedPath) || (file_exists($blendedPath) && $forceOverwrite===true) ){

			if(file_exists($srcPath) && is_numeric($width) && is_numeric($height) && $width*$height>0){

				$img           = new Imagick($srcPath);
				$overlay       = new Imagick();

				/* generate overlay */
				$overlay->newImage($width, $height, new ImagickPixel($colour));
				$overlay->setImageFormat('png');

				/* define cropping coordinates */
				$x = round(($img->getimagewidth() - $width) * .5);
				$y = 0;
				if($valign==='middle'){
					$y = round(($img->getimageheight() - $height) * .5);

				}else if($valign==='bottom'){
					$y = $img->getImageHeight() - $height;

				}


				/* crop image*/
				$img->cropImage($width, $height, $x, $y);

				/* convert to grayscale */
				$img->modulateImage(100, 0, 100);

				/* set levels */
				$img->levelImage(3500, 2, 65535);

				/* multiply image */
				$img->compositeImage($overlay, imagick::COMPOSITE_MULTIPLY, 0, 0);


				/* compress image */
				$img->setImageCompression(Imagick::COMPRESSION_JPEG);
				$img->setImageCompressionQuality($jpg_quality);
				$img->setSamplingFactors(array(2, 1, 1));
				$img->stripImage();
				$img->normalizeImage();
				$img->unsharpMaskImage(0 , 0.75 , 1 , 0.05);
				$img->setInterlaceScheme(Imagick::INTERLACE_PLANE);

				/* save image */
				$img->writeImage($blendedPath);

			}
		}

		return $blendedURL;

	}





}

new CGY_Image_Blending;
//add_action('plugins_loaded', array('CGY_Image_Blending', 'init'));

