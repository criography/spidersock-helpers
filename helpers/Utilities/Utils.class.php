<?php
/**
	* SSK_Utils
	* ---------------------------------------------------------------------
	* Misc Utilities
	*
	* @package      SpiderSock Helpers
	* @subpackage   Utilities
	* @since        2.0
	*/






class SSK_Utils{



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
 * first
 * --------------------------------------------------
 * equivalent of JS's a||b||c returns first nonempty value
 *
 * @param mixed $arg array or comma separated argument list: all arguments
 * @return mixed
 */

	public static function first(){
		$args = func_get_args();
		return current(array_filter( is_array($args[0]) ? $args[0] : $args ));
	}







/**
 * print_a
 * --------------------------------------------------
 * shows pre-formatted object or array
 *
 * @param {mixed} $data object or array
 * @return void
 */

	public static function print_a($data){
		echo    '<pre style="font:100 14px/1.375 Courier New,Courier,Lucida Sans Typewriter,Lucida Typewriter,monospace; color:#272624; background:#f5f2eb; padding:20px;">',
                print_r($data, 1),
            '</pre>';
	}







}
new SSK_Utils;
//add_action('plugins_loaded', array('SSK_Utils', 'init'));
