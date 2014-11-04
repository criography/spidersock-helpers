<?php
/**
	* CGY_Image
	* ---------------------------------------------------------------------
	* main image processing helpers
	*
	* @package      Davy's: Wine Merchants
	* @subpackage   CGY Helpers
	* @since        2.0
	*
	* @TODO separate error/exception handling
	*/


class CGY_Image{



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
		require_once( SOCKHELPERS . 'Wordpress/Core/Url.class.php');
		require_once( SOCKHELPERS . 'Utilities/Utils.class.php');
	}







/**
	* base64_encode
	* ---------------------------------------------------------------------
	* Encodes given image using base64.
	* To be used with care, ideally with images under 3-4KB
	*
	* @link http://php.net/manual/en/function.base64-encode.php#105200
	*
	* @param   string    $path       Path or URL (same domain and server) to the file
	* @param   string    $fileType   Forces image type
	*
	* @return  string                base64 encoded image
	*/
	public static function base64_encode ($path, $fileType = '') {
		$output = '';

		if ($path) {
			$path = CGY_WP_Utils_Url::url_to_path($path);
			$data = fread(fopen($path, "r"), filesize($path));

			if(!empty($fileType)){
				$fileType = new finfo(FILEINFO_MIME_TYPE);
				$fileType = $fileType->file($path);

			}else{
				$fileType = 'image/' . $fileType;
			}

			$output = 'data:' . $fileType . ';base64,' . base64_encode($data);
		}

		return $output;
	}







	/**
	 * inlineSVG
	 * ---------------------------------------------------------------------
	 * loads external SVG and inlines it in HTML
	 *
   * Please Note: If SVGs are displaying as text or not at all,
	 * make sure that the server recognises given file extension as XML
	 * and/or add XML declaration to each file:
   * <?xml version="1.0" encoding="utf-8"?>
	 *
   * @param string $path Path to the SVG
	 * @param string $png Path to PNG fallback to be used with SVGeezy.
	 * @param string $title String to be used as <title> and aria-label.
	 * @param array $attributes An optional array of HTML attributes to be added to the <svg> element.
	 * @return mixed SVG string or false
	 */
	public static function inlineSVG($path, $png = '', $title = '', $attributes = ''){
		$svg = '';
		$presentationAttributes = array(
																'aria-hidden' => 'true',
																'role'        => 'presentation',
																'tabindex'    => '-1'
															);


		/* parse the given path and make sure it is in fact absolute server one */
		$path = CGY_WP_Utils_Url::url_to_path($path);



		/* load file specified by $path */
		if(file_exists($path)){
			$doc = new DOMDocument();
			$doc->load($path, LIBXML_COMPACT);
			$svg = (string) $doc->saveXML(null, LIBXML_NOXMLDECL);



			/* remove XML declaration */
			$svg = preg_replace('(<\?xml.*?\?>)', '', $svg);



			/* if title is set make SVG accessible */
			if($title && is_string($title)){
				$svg = preg_replace('/\<svg(.*?)\>/', '<svg$1 aria-label="' . $title . '"><title>'. $title .'</title>', $svg);

			/* otherwise set it as a presentation element */
			}else{
				if(is_array($attributes) && !empty($attributes)){
					$attributes = array_merge($attributes, $presentationAttributes);
				}else{
					$attributes = $presentationAttributes;
				}
			}



			/* add data-png attribute containing png fallback path, and set '.png' class for easier targeting */
			if($png){
				$svg = str_replace('<svg ', '<svg data-png="' . $png . '" ', $svg);
				if(is_array($attributes) && !empty($attributes)){
					if(isset($attributes['class'])){
						$attributes['class'] .= ' svg';
					}else{
						$attributes['class'] = 'svg';
					}
				}else{
					$attributes = array('class' => 'svg');
				}
			}



			/* add any additional attribute if present */
			if(is_array($attributes) && !empty($attributes)){
				$attrHTML = '';
				foreach($attributes as $label => $value){
					$attrHTML .= $label . '="' . addslashes($value) . '" ';
				}

				$svg = str_replace('<svg ', '<svg ' . $attrHTML, $svg);
			}



			/* get rid of self-closing tags */
			$svg = preg_replace("@\\<([A-Za-z]+)([\\w\\s=\\\"\\'\\.\\-\\#:;\\(\\)\\%\\\\\\/]*)\\/\\>@ms", "<$1$2></$1>", $svg);

		}

		return $svg;
	}







/**
	* parse_image_object
	* ---------------------------------------------------------------------
	* Returns first non-empty value using:
	* alt, title, description and caption fields.
	*
	* @param  object    $data       Image Object
	* @param  bool      $alt        Parse Alt Text field?
	* @param  bool      $title      Parse Title field?
	* @param  bool      $desc       Parse Description field?
	* @param  bool      $caption    Parse Caption field?
	* @param  string    $fallback   String to be used as a fallback if nothing else exists
	*
	* @access private
	* @throws Exception             If passed Image ID that doesn't exist
	* @return string                first non-empty string
	*/
	private static function parse_image_object($data, $alt=false, $title=false, $desc=false, $caption=false, $fallback){
	/* Did it return anything? */
	if($data->post_count === 1){

		$image = $data->posts;        /* Image Object */
		$acceptedItems = array();     /* Array of all possible string values */

		if($alt){
			$acceptedItems[] = get_post_meta($image[0]->ID, '_wp_attachment_image_alt', true);
		}

		if($title){
			$acceptedItems[] = $image[0]->post_title;
		}

		if($desc){
			$acceptedItems[] = $image[0]->post_content;
		}

		if($caption){
			$acceptedItems[] = $image[0]->post_excerpt;
		}

		if($fallback){
			$acceptedItems[] = $fallback;
		}

		/* return first non-empty value */
		return $acceptedItems;



		/* Image with that Id doesn';'t seem to exist */
	}else{
		throw new Exception('It seems that image with given ID doesn\'t exist');
	}

}






/**
	* parse_acf_image_array
	* ---------------------------------------------------------------------
	* Returns first non-empty value using:
	* alt, title, description and caption fields from ACF Image Field.
	*
	* @param  object    $data       ACF Image Array
	* @param  bool      $alt        Parse Alt Text field?
	* @param  bool      $title      Parse Title field?
	* @param  bool      $desc       Parse Description field?
	* @param  bool      $caption    Parse Caption field?
	* @param  string    $fallback   String to be used as a fallback if nothing else exists
	*
	* @access private
	* @throws Exception             If passed Image ID that doesn't exist
	* @return string                first non-empty string
	*/
	private static function parse_acf_image_array($data, $alt=false, $title=false, $desc=false, $caption=false, $fallback){

	/* Did it return anything? */
	if(!empty($data)){

		$acceptedItems = array();     /* Array of all possible string values */

		if($alt){
			$acceptedItems[] = $data['alt'];
		}

		if($title){
			$acceptedItems[] = $data['title'];
		}

		if($desc){
			$acceptedItems[] = $data['caption'];
		}

		if($caption){
			$acceptedItems[] = $data['description'];
		}

		if($fallback){
			$acceptedItems[] = $fallback;
		}

		/* return first non-empty value */
		return $acceptedItems;



		/* Image with that Id doesn';'t seem to exist */
	}else{
		throw new Exception('It seems that image with given ID doesn\'t exist');
	}

}









/**
	* get_image_text
	* ---------------------------------------------------------------------
	* Retrieves image textual meta data
	*
	* @param  mixed     $img        Image Object, Array [ACF] or ID
	* @param  bool      $alt        Parse Alt Text field?
	* @param  bool      $title      Parse Title field?
	* @param  bool      $desc       Parse Description field?
	* @param  bool      $caption    Parse Caption field?
	* @param  string    $fallback   String to be used as a fallback if nothing else exists
	*
	* @access private
	* @throws Exception             If no field or fallback is allowed
	* @return string                base64 encoded image
	*/
	private static function get_image_text($img=null, $alt=false, $title=false, $desc=false, $caption=false, $fallback){

		$stringArray = array();

		/* is any of the fields even accepted?*/
		if($alt || $title || $desc || $caption || $fallback){


			/* have we received image's ID? */
			if(is_int($img) && $img>0){

				$object  = new WP_Query( array(
					'post_type'      => 'attachment',
					'posts_per_page' => 1,
					'no_found_rows'  => true,
					'p'              => $img
				));

				$stringArray = self::parse_image_object($object, $alt, $title, $desc, $caption, $fallback);


			/* is it an object? */
			}elseif( is_object($img) ){
				$stringArray = self::parse_image_object($img, $alt, $title, $desc, $caption, $fallback);


			}elseif( is_array($img) ){
				$stringArray = self::parse_acf_image_array($img, $alt, $title, $desc, $caption, $fallback);

			}


			/* finally return the string */
			return CGY_Utils::first($stringArray);


		/* end early */
		}else{
			throw new Exception('You must allow for at least one field to be parsed');
		}

	}








/**
	* get_alt
	* ---------------------------------------------------------------------
	* Returns first non-empty value for alt attribute using:
	* alt, title as defaults, and description and caption fields as optional.
	* To be used with care, ideally with images under 3-4KB
	*
	* @param  mixed   $img        Image Object, Array [ACF] or ID
	* @param  bool    $alt        Parse Alt Text field?
	* @param  bool    $title      Parse Title field?
	* @param  bool    $desc       Parse Description field?
	* @param  bool    $caption    Parse Caption field?
	* @param  string  $fallback   String to be used as a fallback if nothing else exists
	*
	* @return  string                base64 encoded image
	 */
	public static function get_alt($img=null, $alt=true, $title=true, $desc=false, $caption=false, $fallback = ''){
		try{
			return self::get_image_text($img, $alt, $title, $desc, $caption, $fallback);

		}catch(Exception $e){

			if(WP_DEBUG){
				echo  '<strong>Caught exception: </strong>',  $e->getMessage(),
							' [ <em style="opacity:.6">', 'Line ', $e->getLine(), ' in ', $e->getFile(), '</em> ]';
			}

			return false;

		}
	}







/**
	* get_caption
	* ---------------------------------------------------------------------
	* Returns first non-empty value for image's caption using:
	* description and caption fields as defaults, with optional alt and title.
	* To be used with care, ideally with images under 3-4KB
	*
	* @param  mixed   $img        Image Object, Array [ACF] or ID
	* @param  bool    $alt        Parse Alt Text field?
	* @param  bool    $title      Parse Title field?
	* @param  bool    $desc       Parse Description field?
	* @param  bool    $caption    Parse Caption field?
	* @param  string  $fallback   String to be used as a fallback if nothing else exists
	*
	* @return  string                base64 encoded image
	 */
	public static function get_caption($img=null, $alt=false, $title=false, $desc=true, $caption=true, $fallback = ''){

		try {
			return self::get_image_text($img, $alt, $title, $desc, $caption, $fallback);

		}catch(Exception $e){

			if(WP_DEBUG){
				echo  '<strong>Caught exception: </strong>',  $e->getMessage(),
							' [ <em style="opacity:.6">', 'Line ', $e->getLine(), ' in ', $e->getFile(), '</em> ]';
			}

			return false;

		}

	}







}

new CGY_Image;
//add_action('plugins_loaded', array('CGY_Image', 'init'));
