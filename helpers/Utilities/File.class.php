<?php
/**
	* SSK_Utils_File
	* ---------------------------------------------------------------------
	* File Utilities
	*
	* @package      SpiderSock Helpers
	* @subpackage   Utilities
	* @since        2.0
	* @TODO separate error/exception handling
	*/







class SSK_Utils_File{



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
	}






	/**
	 * get_modified_time
	 *
	 * Add suffix to the filename for cache busting
	 *
	 * @param string $path absolute path to the file, starting with '/', no domain.
	 * @param mixed $rewrite in case of WP specific rewrites are in place.
	 * @return string URL with a prefix
	 */
	public static function get_modified_time($path, $rewrite=false){
		$suffix = $path;
		$serverPath = $_SERVER['DOCUMENT_ROOT'];

		if(isset($rewrite)){
			if(is_bool($rewrite) && $rewrite){
				$serverPath = get_template_directory();
			}elseif(is_string($rewrite) && !empty($rewrite)){
				$serverPath .= $rewrite;
			}

		}
		$fullPath = $serverPath.$path;

		if($path && file_exists($fullPath)){
			$suffix.= '?'.filemtime($fullPath);
		}

		return $suffix;

	}







}
new SSK_Utils_File;
//add_action('plugins_loaded', array('SSK_Utils_File', 'init'));
