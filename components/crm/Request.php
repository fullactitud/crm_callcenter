<?php
namespace app\components\crm;

use Yii;

/**
 * Clase helper para los REQUEST
 */
class Request{
    /**
     * Recibe un valor
     */ 
    public static function rq( $p ){
        if( array_key_exists($p, $_REQUEST) ){
            $cad = str_replace( "'", '', $_REQUEST[$p] );
            return $cad;
        }
        return null;
       
    } // eof #######################################################
        
    
} // class
