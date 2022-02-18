<?php
/**
*    The functions, required for class phpMultiLang ver.2.0
*    @package phpMultiLang
*    @author Konsantin S. Budylov
*/

/**
*    Function checks, is the file expired, or not.
*
*    @param $fname Filename.
*    @param $expire Value of expire(seconds) for check.
*    @return bool TRUE if not expired, or FALSE -if expired.
*/
function check_file_expire($fname="",$expire=0)
{
    return (file_exists($fname))?((time()<(filemtime($fname)+$expire))?true:false):false;
}

?>