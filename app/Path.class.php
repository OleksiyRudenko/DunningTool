<?php
/**
 * Created by PhpStorm.
 * User: Rudenko
 * Date: 20/07/2016
 * Time: 20:50
 */

class PATH {
    public static $p;

    public static function initialize() {
        self::$p=array_slice(explode_notnull('/',$_SERVER['SCRIPT_URI']),2);
    }
}