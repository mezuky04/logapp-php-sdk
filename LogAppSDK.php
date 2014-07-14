<?php

/**
 * Class LogApp
 *
 * LogApp PHP SDK
 *
 * @author Alexandru Bugarin <alexandru.bugarin@gmail.com>
 */
class LogApp {

    /**
     * @var string Api key
     */
    private static $_apiKey = 'abc';

    /**
     * API endpoint
     */
    private static $_apiEndpoint = 'http://logapp-dev.co/loggerAPI/log';

    /**
     * Logger constants
     */
    const LOG_LEVEL_INFO = 'info';
    const LOG_LEVEL_DEBUG = 'debug';
    const LOG_LEVEL_WARNING = 'warning';
    const LOG_LEVEL_ERROR = 'error';
    const LOG_LEVEL_EMERGENCY = 'emergency';

    /**
     * JSON response keys and values
     */
    const RESPONSE_KEY_STATUS = 'status';
    const RESPONSE_VALUE_SUCCESS = 'success';
    const RESPONSE_VALUE_FAILURE = 'failure';
    const RESPONSE_KEY_SUCCESS_MESSAGE = 'message';
    const RESPONSE_KEY_ERROR_MESSAGE = 'error_message';
    const RESPONSE_KEY_ERROR_CODE = 'error_code';

    /**
     * POST fields names
     */
    const FIELD_API_KEY = 'api-key';
    const FIELD_MESSAGE = 'message';
    const FIELD_LOG_FILE = 'log-file';
    const FIELD_LOG_LINE = 'log-line';
    const FIELD_LOG_LEVEL = 'log-level';


    /**
     * Log a message
     *
     * @param string $message To log
     * @param string $logLevel Can be 'info', 'debug', 'warning', 'error' or 'emergency'
     */
    public static function log($message, $logLevel = self::LOG_LEVEL_WARNING) {

        // Check if curl is enabled
        self::_curlEnabled();

        $fields = array(
            self::FIELD_API_KEY => self::$_apiKey,
            self::FIELD_MESSAGE => $message,
            self::FIELD_LOG_FILE => self::_getLogFile(),
            self::FIELD_LOG_LINE => self::_getLogLine(),
            self::FIELD_LOG_LEVEL => $logLevel
        );

        // Make api request
        $response = self::_doRequest($fields);

        // Process response errors
        self::_processResponseErrors($response);
    }


    /**
     * Make api request
     *
     * @param array $fields
     * @return array
     * @throws Exception
     */
    private function _doRequest($fields = array()) {

        // Initialize curl
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, self::$_apiEndpoint);
        curl_setopt($ch, CURLOPT_POST, count($fields));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        // Make request
        $result = curl_exec($ch);

        // Check for errors
        if (curl_error($ch)) {
            throw new Exception("LogApp curl error: ".curl_error($ch));
        }

        curl_close($ch);

        return json_decode($result, true);
    }


    /**
     * Outputs error code and message
     *
     * @param array $response
     * @throws Exception
     */
    private function _processResponseErrors($response) {

        if ($response[self::RESPONSE_KEY_STATUS] == self::RESPONSE_VALUE_FAILURE) {
            throw new Exception("LogApp returned error code ".$response[self::RESPONSE_KEY_ERROR_CODE]." with the following message: ".$response[self::RESPONSE_KEY_ERROR_MESSAGE]);
        }
    }


    /**
     * Get log file path
     *
     * @return string Log file path
     */
    private function _getLogFile() {
        $backtrace = debug_backtrace();
        $caller = array_shift($backtrace);

        return $caller['file'];
    }


    /**
     * Get log line
     *
     * @return int Log line
     */
    private function _getLogLine() {
        $backtrace = debug_backtrace();
        $caller = array_shift($backtrace);

        return $caller['line'];
    }


    /**
     * Check if curl is enabled
     *
     * @throws Exception
     */
    private function _curlEnabled() {
        if (!function_exists('curl_init')) {
            throw new Exception("Curl is required to use LogApp");
        }
    }
}