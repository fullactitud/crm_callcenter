<?php
namespace app\components\crm;

use Yii;

/**
 * Clase helper para los formatos
 */
class Formato{
    
    /**
     * Despliega una fecha formateada
     */
    public static function date( $origen, $format = 'd,m Y' ){
        $cad = $origen;
        switch( $format ){
        case 'd,m Y':
            $cad = substr($origen,8,2) .' de ' .self::mes( substr($origen,5,2), 'Mes') .' del '. substr($origen,0,4);
            break;
        case 'd,m':
            $cad = substr($origen,8,2) .' de ' .self::mes( substr($origen,5,2), 'Mes');
            break;
        case 'Ymd':
            $cad = str_replace('-','',$origen); 
            break;
        default:
            $cad = $origen;
            break;
        }
        return $cad;
    } // eof #######################################################
    




    
    /**
     * Regresa el mes en español
     */
    public static function mes( $param, $format = 'Mes' ){
        $cad = $param;
        switch( $format ){
        case 'Mes':
            switch( $param ){
            case 1: $cad = 'Enero'; break;
            case 2: $cad = 'Febrero'; break;
            case 3: $cad = 'Marzo'; break;
            case 4: $cad = 'Abril'; break;
            case 5: $cad = 'Mayo'; break;
            case 6: $cad = 'Junio'; break;
            case 7: $cad = 'Julio'; break;
            case 8: $cad = 'Agosto'; break;
            case 9: $cad = 'Septiembre'; break;
            case 10: $cad = 'Octubre'; break;
            case 11: $cad = 'Noviembre'; break;
            case 12: $cad = 'Diciembre'; break;
            }
            break;
        default:
            $cad = $param;
            break;
        }
        return $cad;
    } // eof #######################################################    
    
    
    /**
     * Retorna el telefono con formato
     */
    public static function telf( $cad ){
        $cant = strlen($cad);
        $sp = ' &nbsp;';
        $g = ')  ';
        $p = ' (';
        switch( $cant ){
        case 4:
            $cad = substr($cad,0,1) .$sp .substr($cad,-3);
            break;
        case 5:
            $cad = substr($cad,0,2) .$sp .substr($cad,-3);
            break;
        case 6:
            $cad = substr($cad,0,3) .$sp .substr($cad,-3);
            break;
            
        case 7:
            $cad = substr($cad,0,1) .$sp .substr($cad,-6,3) .$sp .substr($cad,-3);
            break;
            
        case 8:
            $cad = $p .substr($cad,0,1) .$g .substr($cad,-7,1) .$sp .substr($cad,-6,3) .$sp .substr($cad,-3);
            break;
        case 9:
            $cad = $p .substr($cad,0,2) .$g .substr($cad,-7,1) .$sp .substr($cad,-6,3) .$sp .substr($cad,-3);
            break;
        case 10:
            $cad = $p .substr($cad,0,1) .substr($cad,-9,2) .$g .substr($cad,-7,1) .$sp .substr($cad,-6,3) .$sp .substr($cad,-3);
            break;
        case 11:
            $cad = substr($cad,0,2) .substr($cad,-9,2) .$g .substr($cad,-7,1) .$sp .substr($cad,-6,3) .$sp .substr($cad,-3);
            break;
            
        case 12:
            $cad = substr($cad,0,3) .$sp .substr($cad,-9,2) .$g .substr($cad,-7,1) .$sp .substr($cad,-6,3) .$sp .substr($cad,-3);
            break;
        }
        return $cad;
    } // eof #######################################################
    
    
    
    
    
    
} // class
