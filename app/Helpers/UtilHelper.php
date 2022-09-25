<?php

namespace App\Helpers;

use Auth;
class UtilHelper
{
    /**
     * @param bool $onlyNumber
     * @return string
     */
    public static function generateString($onlyNumber = false)
    {
        $timestamp = (microtime(true) * 10000);
        $timestamp = str_replace("0", "8", $timestamp);
        $string = "ABCDEFGHIJKLMNOPQRSTUVWXYZ123456789";
        $number = "123456789";

        $random_string = ($onlyNumber == true) ? str_shuffle($number) : str_shuffle($string);
        $unique_code = str_shuffle(substr($timestamp, 0, 4)) . substr($timestamp, 5, 8) . substr($random_string, 0, 4);

        return $unique_code;
    }
    
    /**
     * 
     * @return boolean
     */
    public static function checkIfAdmin(){
        if(\Arr::exists(Auth::user(), 'admin_reference_number')){
            return 1;
        }else{
            return 0;
        }
    }

}