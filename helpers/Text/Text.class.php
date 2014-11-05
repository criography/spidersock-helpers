<?php
/**
	* SSK_Text
	* ---------------------------------------------------------------------
	* Main Text Processing Helpers
	*
	* @package      SpiderSock Helpers
	* @subpackage   Text
	* @since        2.0
	* @TODO separate error/exception handling
	* @TODO Add as many wp-typgraphy methods as possible.
	*/


class SSK_Text{





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
 * strtocamel
 * --------------------------------------------------
 * transforms a string into a camel case with optionally 1st character as lowercase
 *
 * @param string $str String to be parsed
 * @param bool $firstLower Should first character be lowercase?
 * @return string camelCased string
 */

	public static function strtocamel($str, $firstLower=true){
		$output = '';

		if(is_string($str) && !empty($str)){
			$output = str_replace('-', ' ', $str);
			$output = preg_replace( '/[[:space:][:cntrl:][:punct:]£$€]+/', '', ucwords(strtolower($output)) );
			if($firstLower){
				$output[0] = strtolower($output[0]);
			}
		}

		return $output;
	}









/**
 * wrap_words_in_spans
 * --------------------------------------------------
 * Wraps each word in span and optionally gives it a class
 *
 * @param   string      $str                  Text to process
 * @param   bool        $addClass             Should class attribute be added?
 * @param   bool        $addTrailingSpace     Should there be a space after each word?
 *
 * @return  string                       processed string
 */

	public static function wrap_words_in_spans($str, $addClass=false, $addTrailingSpace=true){
		$output = false;

		if(!empty($str)){
			$str      = explode(' ', preg_replace('/(\s+?)/m', ' ', trim($str)));
			$output   = '';

			foreach($str as $n=>$word){
				$output.= '<span'.($addClass ? ' class="word-'.($n+1).'"' : '').'>'. $word . ($addTrailingSpace ? ' ' : ''). '</span>';
			}


		}

		return $output;

	}









/**
 * wrap_chars_in_spans
 * --------------------------------------------------
 * parses string and wraps each letter in span with iterated class
 *
 * @param string $string Text to be parsed
 * @return string wrapped string
 */
	public static function wrap_chars_in_spans($str=''){
		$output = false;

		if($str){
			$output = '';
			$str = str_split($str);
			foreach($str as $k=>$v){
				$output.='<span class="chr-'. ($k+1) .'">'. $v .'</span>';
			}
		}

		return $output;
	}










/**
 * remove_widows
 * --------------------------------------------------
 * parses string and replaces last space with non-breaking
 *
 * @param string $string Text to be parsed
 * @return string dewidowed string
 */

	public static function remove_widows($string){
		$string = trim($string);

		$pos = strrpos($string, ' ');

		return ($pos !== false) ? substr_replace($string, '&nbsp;', $pos, 1) : $string;
	}








}

new SSK_Text;
//add_action('plugins_loaded', array('SSK_Text', 'init'));
