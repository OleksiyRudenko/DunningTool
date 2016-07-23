<?php
/**
 * Created by PhpStorm.
 * User: Rudenko
 * Date: 18/07/2016
 * Time: 14:35
 */

// =================== Global Functions
require_once('app/helperFn.php');

// =================== Basic Globals


// =================== Classes
require_once('app/DateBiz/DateBiz.class.php');
require_once('app/Path.class.php');
PATH::initialize();
require_once('app/Module.class.php');

// =================== Configs
require_once('app/config.db.php');
require_once('app/config.modules.php');

// =================== Intializations
$DBH = DBconnect($DBconfig);
DateBizCache::initialize($DBH,'dateadjustment');

// =================== User Form Submissions Processing
if (MODULE::getSetting('onSubmit'))
    include_once('app/'.MODULE::$current.'/'.MODULE::$current.'.onSubmit.php');
