<?php

/**
 *
 * @Get atomic time
 *
 * @throws Exception of failure
 *
 * @return int
 *
 */
function atomicTime()
{
    /*** connect to the atomic clock ***/
    #$fp = @fsockopen( "time-a.nist.gov", 37, $errno, $errstr, 10 );
    $fp = @fsockopen( "ntp.emt.ee", 37, $errno, $errstr, 10 );
    if ( !$fp )
    {
        throw new Exception( "$errno: $errstr" );
    }
    else
    { 
        fputs($fp, "\n"); 
        $time_info = fread($fp, 49);
        fclose($fp);
    }
    /*** create the timestamp ***/
    $atomic_time = (abs(hexdec('7fffffff') - hexdec(bin2hex($time_info)) - hexdec('7fffffff')) - 2208988800); 
    echo $errstr;
    return $atomic_time;
}

?> 

