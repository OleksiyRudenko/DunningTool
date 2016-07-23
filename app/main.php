<h1><?=MODULE::getSetting('heading')?></h1>
<div class="container">
    <div class="col-lg-8">
<?php
/**
 * Created by PhpStorm.
 * User: Rudenko
 * Date: 18/07/2016
 * Time: 14:08
 */

// =================== Major UI
echo unlogMessage('DBinit');

// echo varExport(PATH::$p,'PATH');
include_once('app/'.MODULE::$current.'/'.MODULE::$current.'.view.php');
?>
    </div>
</div>