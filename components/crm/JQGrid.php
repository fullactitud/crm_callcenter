<?php
namespace app\components\crm;
use Yii;

use app\models\Proyecto;
use app\models\Usuario;
use app\models\Perfil;

use app\models\xProyecto;

use app\models\xUsuario;
use app\models\xPerfil;
use app\models\Aux;


/**
 * Clase helper para listados jqgrid
 */
class JQGrid{
    
  
    /**
     * Crea un listado JQGrid
     * ej. ctrlFunction::JQGrid( $ctrl, $cabecera, $registros, $titulo, $json, $subact, $detail );
     * @param String $ctrl, 
     * @param Array $cabecera, 
     * @param $registros, 
     * @param String $titulo, Titulo del listado
     * @param $json, 
     * @param $subact, 
     * @param $detail, 
     */
    public static function listado( $ctrl, $cabecera, $registros, $titulo = '', $json, $subact='', $detail = NULL ){
        $c1a = array();
        $c2a = array();
        $ids = array();

        
        foreach( $cabecera as $cad ){
            if( count($cad) > 0 ){
                $ids[] = $cad[1];
                $c1a[] = "'" .$cad[0] ."'";
                $aux = "{name:'" .$cad[1] ."', index:'" .$cad[1] ."'";
                $aux .= (is_null($cad[2]))? ",width:0" : ",width:" .$cad[2] ."";
                $aux .= (is_null($cad[3]))? ",hidden:false" : ",hidden:" .$cad[3];
                $aux .= (is_null($cad[4]))? ",type:'string'" : ",type:'" .$cad[4] ."'";
                $aux .= (is_null($cad[5]))? ",align:'left'" : ",align:'" .$cad[5] ."'";
                $aux .= (is_null($cad[6]))? ",sorttype:'int'" : ",sorttype:'" .$cad[6] ."'";
                $aux .= (is_null($cad[7]))? ",editable:'false'" : ",editable:'" .$cad[7] ."'";
                $aux .= (is_null($cad[8]))? '' : ",edittype:'" .$cad[8] ."'";
                $aux .= (is_null($cad[9]))? '' : ",editoptions:" .$cad[9];
                $aux .= "}";
                $c2a[] = $aux;
            }
        }
        $c1b = implode(',',$c1a);
        $c2b = implode(',',$c2a);
        $n = count($registros);
        $pag = ceil($n / 10);
        $cad = "var lastsel; jQuery('#cyccrmlist1').jqGrid({
               datatype: 'json',
url: 'index.php?r=$ctrl/out&op=$json',
    mtype: 'Get',
 hoverrows:true,
gridview:true,
               viewrecords: true,
               rowNum: 10,
               sortname: 'id',
               sortorder: 'Desc',
               height: 270,
               width:500,
emptyrecords: 'No hay registros',
pager : '#cyccrmpager1',
page: 1,
totalpages: " .$pag .",   
totalrecords:" .$n .", 
pagination:true,
imgpath: 'js/jquery/jquery-ui'+x3theme+'/images',  
              multiselect: false,";
        if( 0 && !is_null($titulo) )
            $cad .= "caption: '" .strtoupper(trim($titulo)) ."',";
           $cad .= "colNames:[" .$c1b ."],
              colModel:[" .$c2b ."],
              onSelectRow : function(id){ 

                   if (id && id !== lastsel) {
                    $('#" .$detail ."').load('" .Yii::$app->params['baseUrl'] ."index.php?r=" .$subact ."&emb=1&id=' +id +'#top2');
                      lastsel = id;
                        }
                    }
               });
jQuery('#cyccrmlist1').jqGrid('navGrid','#cyccrmpager1',{search:false,refresh:false,edit:false,add:false,del:false});";


	return $cad;
} // eof #######################################################









    public static function JQGridVector_deprecate( $cabecera, $registros, $titulo = '', $json, $subact='', $detail = NULL ){
        $c1a = array();
        $c2a = array();
        $ids = array();
        foreach( $cabecera as $cad ){
            $ids[] = $cad[1];
            $c1a[] = "'" .$cad[0] ."'";
            $aux = "{name:'" .$cad[1] ."', index:'" .$cad[1] ."'";
            $aux .= (is_null($cad[2]))? ",width:0" : ",width:" .$cad[2] ."";
            $aux .= (is_null($cad[3]))? ",hidden:false" : ",hidden:" .$cad[3];
            $aux .= (is_null($cad[4]))? ",type:'string'" : ",type:'" .$cad[4] ."'";
            $aux .= (is_null($cad[5]))? ",align:'left'" : ",align:'" .$cad[5] ."'";
            $aux .= (is_null($cad[6]))? ",sorttype:'int'" : ",sorttype:'" .$cad[6] ."'";
            $aux .= (is_null($cad[7]))? ",editable:'false'" : ",editable:'" .$cad[7] ."'";
            $aux .= (is_null($cad[8]))? '' : ",edittype:'" .$cad[8] ."'";
            $aux .= (is_null($cad[9]))? '' : ",editoptions:" .$cad[9];
            $aux .= "}";
            $c2a[] = $aux;
        }
        $c1b = implode(',',$c1a);
        $c2b = implode(',',$c2a);
        $n = count($registros);
        $pag = ceil($n / 10);
        $cad = "var lastsel; jQuery('#cyccrmlist1').jqGrid({
               'datatype': 'json',
url: 'index.php?r=df/df/out&op=$json',
    mtype: 'Get',
 'hoverrows':true,
'gridview':true,
               'viewrecords': true,
               'rowNum': 10,
               'sortname': 'id',
               'sortorder': 'Desc',
               height: 270,
               width:500,
pager : '#cyccrmpager1',

imgpath: 'js/jquery/jquery-ui'+x3theme+'/images',  
              multiselect: false,";
        if( 0 && !is_null($titulo) )
            $cad .= "caption: '" .strtoupper(trim($titulo)) ."',";
           $cad .= "colNames:[" .$c1b ."],
              colModel:[" .$c2b ."],
              onSelectRow : function(id){ 
                   if (id && id !== lastsel) {
                    $('#" .$detail ."').load('" .Yii::$app->params['baseUrl'] ."index.php?r=" .$subact ."&emb=1&id=' +id +'#top2');
                      lastsel = id;
                        }
                    }
               });
jQuery('#cyccrmlist1').jqGrid('navGrid','#cyccrmpager1',{search:false,refresh:false,edit:false,add:false,del:false});
var cadGrid = new Array();";               
   
        for( $i=0; $i < count($registros); $i++ ){
            $cad .= "cadGrid[" .$i ."] = {";
            
            foreach( $ids as $idu )
              $cad .= "'" .$idu ."':'" .$registros[$i]->$idu ."',";
            
            $cad = substr($cad,0,-1) ."};"; 
        }
        $cad .= "for(var i=0;i!=cadGrid.length-1;i++) jQuery('#cyccrmlist1').jqGrid('addRowData',i+1,cadGrid[i],{edit:false,add:false,del:false});";
	return $cad;
} // eof #######################################################






} // class
