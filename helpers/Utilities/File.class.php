<?php
/**
	* CGY_Utils_File
	* ---------------------------------------------------------------------
	* File Utilities
	*
	* @package      Davy's: Wine Merchants
	* @subpackage   CGY Helpers
	* @since        2.0
	* @TODO separate error/exception handling
	*/







class CGY_Utils_File{



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

add_action('plugins_loaded', array('CGY_Utils_File', 'init'));
