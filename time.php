<?php

// Include log app php sdk
include ('LogAppSDK.php');

// Generate a long error message
$message = 'abcdefghjl';
for ($i=0;$i<5000;$i++) {
    $message .= 'abcdefghjl';
}

echo strlen($message).'<br>';

// Execution start time
$time_start = microtime(true); 

// Log message
LogApp::log($message, 'error');

$time_end = microtime(true);

// Execution finish
$execution_time = ($time_end - $time_start);

echo '<b>Total Execution Time:</b> '.$execution_time.' ms';

