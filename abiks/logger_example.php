#!/usr/local/bin/php -q
<?php

include '/home/webapp/test/logger.php';
try
{
    /*** a new logger instance ***/
    $log = logger::getInstance();
    /*** the file to write to ***/
    $log->logfile = '/home/webapp/test/logger_errors.log';
    /*** write an error message with filename and line number ***/
    $log->write('An error has occured', __FILE__, __LINE__);
}
catch(Exception $e)
{
    echo $e->getMessage();
}
?>

