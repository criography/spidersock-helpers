<?php

    /**
     * SSK_Utils_File
     * ---------------------------------------------------------------------
     * URL Utilities
     *
     * @package      SpiderSock Helpers
     * @subpackage   Utilities
     * @since        2.0
     */
    class SSK_Utils_Url
    {


        /**
         * init
         * ---------------------------------------------------------------------
         * initiate class and expose its methods
         *
         * @return void
         */
        private static function init()
        {
            $class = __CLASS__;
            new $class;
        }






        /**
         * __construct
         * ---------------------------------------------------------------------
         */
        public function __construct()
        {
        }






        /**
         * parse_url
         *
         * Temporary fix for protocol relative URLs, not supported by native PHP function
         *
         * @param     string $url URL
         *
         * @return    array                   should be identical as he output of parse_url
         */
        public static function parse_url( $url )
        {
            if (!empty( $url ) && is_string($url) && strpos($url, '//') === 0) {
                $url = ( (!empty($_SERVER[ 'HTTPS' ]) && $_SERVER['HTTPS']!=='off' || $_SERVER['SERVER_PORT']==443) ? "https:" : "http:" ) . $url;
            }

            $_url = parse_url($url);

            return $_url;
        }


    }

    new SSK_Utils_Url;
    //add_action('plugins_loaded', array('SSK_Utils_Url', 'init'));
