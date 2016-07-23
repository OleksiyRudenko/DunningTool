<?php
/**
 * Created by PhpStorm.
 * User: Rudenko
 * Date: 18/07/2016
 * Time: 12:11
 */

// Do ui.form action
if (isset($_POST['submitDateBiz'])) {
    // echo alert($_POST['submitDateBiz'],'info');
    // query db
    switch ($_POST['submitDateBiz']) {
        case 'SelectPeriod':
            setcookie('DateBizListFrom',$_POST['from'],time()+60*60*24*180); // 180 days
            setcookie('DateBizListTill',$_POST['till'],time()+60*60*24*180);
            $_COOKIE['DateBizListFrom']=$_POST['from'];
            $_COOKIE['DateBizListTill']=$_POST['till'];
            break;
        case 'Update':
            // logMessage('DateBiz','_POST:<br/><pre>'.var_export($_POST,true).'</pre>','info');

            $pool = [];
            if (isset($_POST['oldTouched']))
                foreach ($_POST['oldTouched'] as $k=>$touched)
                    if ($touched && ($_POST['oldValue'][$k]>=-1 && $_POST['oldValue'][$k]<=1) && strlen($_POST['oldDate'][$k])==10)
                        $pool[$_POST['oldDate'][$k]] = $_POST['oldValue'][$k];
            if (isset($_POST['newTouched']))
                foreach ($_POST['newTouched'] as $k=>$touched)
                    if ($touched && ($_POST['newValue'][$k]==1 || $_POST['newValue'][$k]==-1)  && strlen($_POST['newDate'][$k])==10)
                        $pool[$_POST['newDate'][$k]] = $_POST['newValue'][$k];

            //#log
            /* $str=[];
            foreach ($pool as $d=>$v)
                $str[]=p($d.'='.$v);
            if (count($str))
                logMessage('DateBiz','DateBiz submission filtered:'.implode("\n ",$str),'info'); */

            // process pool
            if (count($pool))  DateBizCache::setAdjustment($pool);

            break;
    }

}