<?php
/**
 * Created by PhpStorm.
 * User: Rudenko
 * Date: 18/07/2016
 * Time: 14:51
 */

function alert($msg,$type='danger') { // success, info, warning, danger
    return '<div class="alert alert-'.$type.'" role="alert">'.$msg.'</div>';
}

function p($html) {
    return htmlElement('p',$html);
}

function div($html) {
    return htmlElement('div',$html);
}

function pre($html) {
    return htmlElement('pre',$html);
}

function htmlElement($el,$inner='') {
    return '<'.$el.'>'.$inner.'</'.$el.">\n";
}

// ===========================================================================

function &explode_notnull($delim,$input)
// explodes $input array and returns array with !NULL values only
{
    $ret=array();
    if (is_array($delim))
    {
        $input=str_replace($delim,$delim[0],$input);
        $delim=$delim[0];
    }
    $src=explode($delim,$input);
    foreach ($src as $v) if ($v!=='') $ret[]=$v;
    return $ret;
}

function varExport(&$v,$name=false) {
    return alert(($name?'<strong>'.$name.'</strong> =<br/>':'').pre(var_export($v,true)),'info');
}

function is_obj( &$object, $check=null, $strict=true )
{
    if( $check == null && is_object($object) )
    {
        return true;
    }
    if( is_object($object) )
    {
        $object_name = get_class($object);
        if( $strict === true )
        {
            if( $object_name == $check )
            {
                return true;
            }
        }
        else
        {
            if( strtolower($object_name) == strtolower($check) )
            {
                return true;
            }
        }
    }
}

$LOG=[];
function logMessage($module,$msg,$type='info') {
    global $LOG;
    $LOG[$module][$type][]=$msg;
}

function unlogMessage($module,$type=['danger','warning','success','info']) {
    global $LOG;
    if (!isset($LOG[$module])) return '';
    $pool=[];
    if (!is_array($type)) $type=[$type];
    foreach ($type as $t) {
        if (isset($LOG[$module][$t])) {
            foreach ($LOG[$module][$t] as $msg) {
                $pool[]=alert('<strong>'.$module.'</strong>: '.$msg,$t);
                // echo alert($msg,$t);
            }
            unset($LOG[$module][$t]);
        }
    }
    // echo implode("\n",$pool);
    return implode("\n",$pool);
}