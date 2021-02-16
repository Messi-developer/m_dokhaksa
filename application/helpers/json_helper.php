<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
function ToEucKr( $val ){
    return iconv("UTF-8","euc-kr//IGNORE",$val );
}
function ToUTF8( $val ){
    //return iconv("euc-kr","UTF-8",$val );
    #? ? ?.euc-kr 로 사용시 문제됨.
    return iconv("cp949","UTF-8",$val );
}

//euc-kr용
function jsonencode($val) {
    $tmp = obj_change_charset($val, 'utf-8', true);
    $tmp = json_encode( $tmp );
    return $tmp;
}
//euc-kr용
function jsondecode($val) {
    $tmp = obj_change_charset($val, 'utf-8', true);
    $tmp = json_decode( $tmp );
    obj_change_charset( $tmp, 'euc-kr' );
    return $tmp;
}

function &obj_change_charset( &$obj, $tocharset='utf-8', $clone=false ) {
    $obj_tmp = NULL;
    if ($clone) {
        $obj_tmp = is_object($obj) ? clone $obj : $obj;
    }else{
        $obj_tmp =& $obj;
    }

    switch ( gettype($obj_tmp) ) {
        case 'string' :
            //$obj_tmp = mb_convert_encoding( $obj_tmp, $tocharset, mb_detect_encoding( $obj_tmp ) ); //짧은 값은 제대로 인식하지 못하더군요.
            if ('euc-kr'==$tocharset) {
                $obj_tmp = ToEucKr($obj_tmp);
            }else{
                $obj_tmp = ToUTF8($obj_tmp);
            }
            break;
        case 'array':
            foreach ($obj_tmp as &$val) {
                $val = obj_change_charset( $val, $tocharset, $clone );
            }
            break;
        case 'object':
            $object_vars = get_object_vars( $obj_tmp );
            foreach ($object_vars as $key=>$val) {
                $obj_tmp->$key = obj_change_charset( $val, $tocharset, $clone );
            }
            break;
    }

    return $obj_tmp;
}
?>