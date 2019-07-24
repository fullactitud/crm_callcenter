<?php
namespace app\components\crm;

use Yii;

use app\models\Proyecto;
use app\models\xProyecto;
use app\models\xUsuario;
use app\models\xPerfil;
use app\models\Aux;


/**
 * Clase helper para objetos JSon
 */
class JSon{
           
        
        
    /**
     * Retorna un objeto JSon
     * @param string $tabla, Tabla a consultar en la Base de Datos
     * @param string $select, Campos a consultar en la Tabla
     * @return string Objeto JSon
     */ 
    public static function toJson( $tabla, $select ){       
        $paginacion = in_array('rows',$_REQUEST)?(int)$_REQUEST['rows']:10;
        $paginacion = (int)$paginacion > 0 ? $paginacion : 10;
        $page = in_array('page',$_REQUEST)?(int)$_REQUEST['page']:1;
        $offset = ($page > 0) ? (($page - 1)  * $paginacion) : 0;
        $limit = $paginacion;
        $sord = in_array('sord',$_REQUEST)?$_REQUEST['sord']:'asc';
        $sidx = in_array('sidx',$_REQUEST)?$_REQUEST['sidx']:'id';    
        $sord = ($sord != '') ? $sord : 'asc' ;
        $order = (($sidx != '') ? $sidx : 'id') .' '. $sord ;
        
        $sql = "select count(id) as id from $tabla;";
        $count =  Aux::findBySql($sql)->one();
        
        if( $paginacion > 0 )
            $total_page = ceil($count->id/$paginacion);
        else
            $total_page = 1;
        $sql = "select $select from $tabla order by $sidx $sord limit $limit offset $offset;";
       
        $model = Aux::findBySql($sql)->all(); 
        $cad = '{"page":'.$page.',"total":'.$total_page.',"records":'.$count->id.',"rows":[';
        $vct = explode(',',$select);
        $i = 0;
        foreach( $model as $reg ){
            $i++;
            if( isset($reg->id) )
                $id = $reg->id;
            else
                $id = $i;
            $cad .= '{"id":' .$id .',"cell":[';
            foreach( $vct as $camp )
                $cad .= '"' .$reg->$camp .'",';
            
            $cad = substr($cad, 0, -1);
            $cad .= ']},';
        }
        $cad = substr($cad, 0, -1);
        $cad .= ']}';
        return $cad;
        
        
        
        
        
    } // eof #######################################################
    
    
   


} // class
