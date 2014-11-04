<?php
/**
	* URL Helper Methods
	* -----------------------------------------------------
	* Contains all helpers transforming, traversing or parsing URLs
	*
	* @package      SpiderSock Helpers
	* @subpackage   WP URL
	* @since        2.0
	*
	*/





class SSK_WP_Utils_Url{



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
	* safe_separators
	* ---------------------------------------------------------------------
	* replaces / and \ with PHP's global var DIRECTORY_SEPARATOR
	*
	* @param   string    $path   Potentially unsafe path
	*
	* @access  private
	* @return  string            Safe path
	*/
	private static function safe_separators($path){
		return preg_replace('%[\\/]%', DIRECTORY_SEPARATOR, $path );
	}








/**
	* url_to_path
	* ---------------------------------------------------------------------
	* converts absolute (with or without protocol declaration) URL to a full server path
	*
	* @param  string    $url                URL string to be parsed
	* @param  bool      $ignoreRewrites     if parsing an URL that is most definitely not following any custom rewrites (like /_incs) set this to true for increased performance.
	* @param  bool      $verifyFileExists   run file_exists check too?
	*
	* @access public
	* @throws Exception                     If given URL fails to return 'path' element after initial parsing
	* @return string                        Full server path
	*/
	public static	function url_to_path($url, $ignoreRewrites = false, $verifyFileExists = false){
		$server_root  = self::safe_separators($_SERVER['DOCUMENT_ROOT']);
		$parsed_url   = parse_url($url);
		$path         = '';


		/* attempt getting the path out of the URL:
				http://domain.com/xxx/yyy/zzz/      => /xxx/yyy/zzz/
				http://domain.com/xxx/yyy/zzz       => /xxx/yyy/zzz
				http://domain.com/xxx/yyy/zzz.xxx   => /xxx/yyy/zzz.xxx
				/xxx/yyy/zzz                        => /xxx/yyy/zzz
				//domain.com/xxx/yyy/zzz            => /xxx/yyy/zzz
		*/
		if(!empty($parsed_url['path'])){
			$parsed_url = $parsed_url['path'];

			/* is it actually a server path already? */
			if( strpos($parsed_url, $_SERVER['DOCUMENT_ROOT']) === 0 ){
				return $parsed_url;
			}


		/* This doesn't look like a valid URL and cannot be parsed. */
		} else {
			return false;
		}


		/* if not specifically requested to ignore this bit, check for all custom URL rewrites */
		if(!$ignoreRewrites){
			global $wp_rewrite;

			/* if any exist loop through them */
			if(!empty($wp_rewrite->non_wp_rules)){
				foreach($wp_rewrite->non_wp_rules as $rule => $destination){

					$pattern = '~'. $rule .'~';

					/* if match found, run regex and break the loop */
					if(preg_match('~'. $rule .'~', $parsed_url )){
						$parsed_url = preg_replace($pattern, $destination, $parsed_url );
						break;
					}

				}
			}
		}


		/* make sure that both server path and $parsed url use same type of directory separator and return the final path */
		$path = $server_root . self::safe_separators($parsed_url);


		/* check if file or directory exists, if it's required, otherwise return the path */
		if($verifyFileExists){
			if(file_exists( $path )){
				return $path;

			}else{
				return false;
			}

		}else{
			return $path;

		}

	}







}

new SSK_WP_Utils_Url;
//add_action('plugins_loaded', array('SSK_WP_Utils_Url', 'init'));


