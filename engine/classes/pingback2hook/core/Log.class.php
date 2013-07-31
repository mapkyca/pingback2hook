<?php

/**
 * @file
 * 
 * Logging
 * 
 * @package core
 * @copyright Marcus Povey 2013
 * @license The MIT License (see LICENCE.txt), other licenses available.
 * @author Marcus Povey <marcus@marcus-povey.co.uk>
 * @link http://www.marcus-povey.co.uk
 */

namespace pingback2hook\core {

    /**
     * Logging
     */
    class Log {

        /**
         * Write a message to the system log.
         * @param type $message
         * @param type $level
         * @return type 
         */
        public static function log_echo($message, $level = 'notice') {
            $level = strtoupper($level);

            error_log("$level: $message");

            return false;
        }

        public static function notice($message) {
            self::log_echo($message);
        }

        public static function warning($message) {
            self::log_echo($message, 'warning');
        }

        public static function error($message) {
            self::log_echo($message, 'error');
        }

        public static function debug($message) {
            self::log_echo($message, 'debug');
        }

        /**
         * Output to the syslog (which isn't the same as error_log). 
         * Handy for hooking into server level facilities like fail2ban
         * @param type $message
         */
        public static function syslog($message, $log = LOG_SYSLOG, $level = LOG_NOTICE) {
            openlog("Pingback2Hook.API({$_SERVER['HTTP_HOST']})", LOG_PID, $log);

            syslog($level, $message);

            closelog();
        }

    }

}