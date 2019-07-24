<?php
namespace app\components\crm;
use Yii;

use app\models\Aux;

/**
 * Clase helper para perfiles
 */
class Perfil{
    /**
     * Crea las opciones para un select
     */
    public static function optionSelect( $id_usuario = 0 ){
        $cad = '<option value="0"> &nbsp; --- Seleccione --- </option>';
        $objs = Aux::findBySql("select distinct ai.name, ai.description, aa.item_name from public.auth_item ai left join public.auth_assignment aa on aa.item_name=ai.name and aa.user_id = '" .$id_usuario ."' where ai.type='1' order by ai.name asc; ")->all();
        foreach( $objs as $obj )
            if( $obj->item_name != NULL )
                $cad .= '<option value="' .$obj->name .'" selected="selected"> &nbsp; ' .$obj->description .'</option>';
            else
                $cad .= '<option value="' .$obj->name .'"> &nbsp; ' .$obj->description .'</option>';
        return $cad;
        
    } // eof 
    
    
} // class