<?php
/**
 * Created by PhpStorm.
 * User: Rudenko
 * Date: 20/07/2016
 * Time: 20:53
 */

class MODULE {
    public static $path2mod;
    public static $setting;
    public static $current;

    public static function initialize($p2m,$stng) {
        self::$path2mod = $p2m;
        self::$setting = $stng;

        if ((count(PATH::$p) && !isset(self::$path2mod[PATH::$p[0]])) || !count(PATH::$p)) {
            // PATH[0] not registered as default path (1st in a key list) or PATH is empty
            foreach (self::$path2mod as $firstkey=>$val) break;
            array_unshift(PATH::$p,$firstkey);
        }
        self::$current = self::$path2mod[PATH::$p[0]]; // DateBiz etc.
    }

    public static function getSetting($stng) {
        return isset(self::$setting[self::$current][$stng])
            ? self::$setting[self::$current][$stng]
            : false;
    }
}