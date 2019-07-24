<?php
namespace app\components\crm;
use Yii;

use app\models\Aux;


/**
 * Clase helper para listados
 */
class Listado{
    

    /**
     * Retorna el listado según el formao selecionado para el sistema
     */
    public static function listado( $registros, $titulo = '', $ctrl = null, $cabecera = null, $json = null, $subact='', $detail = null ){    
        return self::listado2( $registros, $titulo );
    }



    /**
     * Listado tipo 1
     */
    public static function listado1( $registros ){
        $i = 0;
        $cad = '<table style="width:100%;"><tbody>';
        foreach( $registros as $reg ){
            $i++;
            $cad .= '<tr>';
            foreach( $reg as $celda ){
                if( $i == 1 ){
                    $cad .= '<th>' .$celda .'</th>';
                }else{
                    $cad .= '<td>' .$celda .'</td>';
                }
            }
            $cad .= '</tr>';
        }
        $cad .= '</body></table>';
        return $cad;
    }




    /**
     * Listado tipo 2
     */
    public static function listado2( $registros, $titulo = null, $style = null ){
        $i = 0;
        $cad = '<table border="1" style="width:100%; padding: 0.2em; text-align: center; min-width:800px;">';
        $cad .= '<tbody style="font-size: 0.7em;">';
        if( !is_null($titulo) )
            $cad .= '<tr><th colspan="'.count($registros[0]).'" style="text-align:center; padding: 0.5em;"> ' .$titulo .'</th></tr>';
        foreach( $registros as $reg ){
            $i++;
            $cad .= '<tr>';
            foreach( $reg as $celda ){
                if( $i == 1 ){
                    $cad .= '<th class="ui-state-active" style="text-align: center; font-weight: 700;">' .$celda .'</th>';
                }else{
                    $cad .= '<td class="ui-state-content">' .$celda .'</td>';
                }
            }
            $cad .= '</tr>';
        }            
        $cad .= '</tbody></table>';
        return $cad;
    }
    

    /**
     * Listado tipo 3
     */
    public static function listado3( $data, $titulo = '', $style = null, $size = '0.8em' ){
        $c = '';
        $c1 = '#fffff6';
        $c2 = '#f6f6ff';
        $c3 = '#fff6ff';
        $c1 = '#aa0000';
        $c2 = '#00aaaa';
        $c3 = '#0000aa';
        $cad = '<div style="font-size: 0.8em;"><table border="0" style="width:100%;">';        
        $cad .= '<tbody>';
        $aux = $aux2 = '';
        $j = 0;
        foreach( $data as $v1 ){
            if( $j == 0 ){
                $cad .= '<tr>';
                foreach( $v1 as $v )
                    $cad .= '<td style="font-size:' .$size .'; background-color:#6666aa; color: #ffffff; text-align:center; padding: 0.2em; font-weight:700;"> ' .strtoupper($v) .' </td>';
                $cad .= '</tr>';
            }else{
                if( $c == $c1 ) $c = $c2;
                else if( $c == $c2 ) $c = $c3;
                else $c = $c1;
              
                $cad .= '<tr>';
                $i = 0;
                foreach( $v1 as $v2 ){
                    if( $style != null ) $s = ' style="' .$style[$i] .'; border-bottom: solid 1px '.$c.'; font-size:' .$size .';" ';
                   
                    else $s = '';
                    if( $i == 0 ){
                        if( $aux == $v2 ){
                            $cad .= '<td' .$s .'>  </td>';
                        $aux2 = $v2;
                        }else
                            $cad .= '<td' .$s .'> ' .$v2 .' </td>';
                        $aux = $v2;
                    }else
                        $cad .= '<td' .$s .'> ' .$v2 .' </td>';
                    $i++;
                } // for 
                $cad .= '</tr>';
            }
            $j++;
        } // for 
        return $cad .'</tbody></table></div>';
    } // eof ##################################################
    

    /**
     * Listado tipo 4
     */
    public static function listado4( $registros, $titulo = '' ){
        $i = 0;
        $cad = '';
        
        return $cad;
    }
    



    
    /**
     * Listado tipo 5: Botones que apuntan a reportes
     */
    public static function listado5( $schema, $codInstrumento, $idInstrumento, $registros ){
        $cad = '<table style="width:100%;" border="0"><tbody>';
        $clase = ' class="ui-button ui-corner-all ui-widget" ';  
        $style2 = ' valign="middle" style="width: 100%; color: #773333; line-height: 1.2em; height: 4.8em; font-weight:700; vertical-align: middle;" ';
        $aux2 = $clase .$style2;
        $h = 0;
        foreach( $registros as $reg ){
            if( $reg[2] == 1 )
                $c3 = Yii::$app->params['baseUrl'] .'index.php?r=proyecto/' .$schema .'/' .$codInstrumento .'/' .$reg[1] .'&cod_proyecto=' .$schema .'&id_instrumento=' .(int)$idInstrumento .'&x3inst=' .(int)$idInstrumento;
            else
                $c3 = Yii::$app->params['baseUrl'] .'index.php?r=soporte/reporte/report&x3proy=' .$schema .'&x3id=' .$reg[1] .'&x3inst=' .$idInstrumento;
            if( $h == 0 ){
                $cad .= '<tr><td width="24%"><a href="' .$c3 .'"><div ' .$aux2 .'  >' .Cadena::acortar($reg[0]) .'</div></a></td>';
                $cad .= '<td width="1%">&nbsp;</td>';
                $h = 1;
            }else if( $h == 1 ){
                $cad .= '<td width="24%"><a href="' .$c3 .'"><div ' .$aux2 .'  ">' .Cadena::acortar($reg[0]) .'</div></a></td>';
                $cad .= '<td width="1%">&nbsp;</td>';
                $h = 2;
            }else if( $h == 2 ){
                $cad .= '<td width="24%"><a href="' .$c3 .'"><div ' .$aux2 .'  >' .Cadena::acortar($reg[0]) .'</div></a></td>';
                $cad .= '<td width="1%">&nbsp;</td>';
                $h = 3;
            }else{
                $cad .= '<td width="24%"><a href="' .$c3 .'"><div ' .$aux2 .' >' .Cadena::acortar($reg[0]) .'</div></a></td></tr>';
                $cad .= '<tr><td colspan="7" style="height: 0.4em;">&nbsp;</td></tr>';
                $h = 0;
            }
        } // for 
        $cad .= '</body></table>';
        return $cad;
    }






    /**
     * Listado tipo 6: Botones para TLO
     */
    public static function listado6( $registros ){
        $cad = '<table style="width:100%;" border="0"><tbody>';
        $clase = ' class="ui-button ui-corner-all ui-widget" ';  
        $style2 = ' valign="middle" style="position:relative; vertical-align: middle; width:100%; line-height: 1.2em; height: 3.6em;" ';
        $aux2 = $clase .$style2;
        $h = 0;
        foreach( $registros as $reg ){
            if( $h == 0 ){
                $cad .= '<tr><td width="32%" style="height: 4.8em;"><a href="' .$reg[1] .'"><div ' .$aux2 .'  ><span style="font-weight:700;" >' .Cadena::acortar($reg[0]) .'</span></div></a></td>';
                $cad .= '<td width="1%">&nbsp;</td>';
                $h = 1;
            }else if( $h == 1 ){
                $cad .= '<td width="32%" style="height: 4.8em;"><a href="' .$reg[1] .'"><div ' .$aux2 .'  "><span style="font-weight:700;" >' .Cadena::acortar($reg[0]) .'</div></div></a></td>';
                $cad .= '<td width="1%">&nbsp;</td>';
                $h = 2;
            }else{
                $cad .= '<td width="32%" style="height: 4.8em;"><a href="' .$reg[1] .'"><div ' .$aux2 .' ><span style="font-weight:700;" >' .Cadena::acortar($reg[0]) .'</div></div></a></td></tr>';
                $cad .= '<tr><td colspan="7" style="height: 0.4em;">&nbsp;</td></tr>';
                $h = 0;
            }
        } // for
                $cad .= '<tr><td width="32%">&nbsp;</td><td width="1%">&nbsp;</td><td width="32%">&nbsp;</td><td width="1%">&nbsp;</td><td width="32%">&nbsp;</td></tr>';
        $cad .= '</body></table>';
        return $cad;
    } // eof 
    

    /*
     * Botones para TLO
     */
    public static function listado6_old( $registros ){
        $cad = '<table style="width:100%;" border="0"><tbody>';
        $clase = ' class="ui-button ui-corner-all ui-widget" ';  
        $style2 = ' valign="middle" style="width: 100%; color: #773333; line-height: 1.2em; height: 4.8em; font-weight:700; vertical-align: middle;" ';
        $aux2 = $clase .$style2;
        $h = 0;
        foreach( $registros as $reg ){
            if( $h == 0 ){
                $cad .= '<tr><td width="24%"><a href="' .$reg[1] .'"><div ' .$aux2 .'  >' .Cadena::acortar($reg[0]) .'</div></a></td>';
                $cad .= '<td width="1%">&nbsp;</td>';
                $h = 1;
            }else if( $h == 1 ){
                $cad .= '<td width="24%"><a href="' .$reg[1] .'"><div ' .$aux2 .'  ">' .Cadena::acortar($reg[0]) .'</div></a></td>';
                $cad .= '<td width="1%">&nbsp;</td>';
                $h = 2;
            }else if( $h == 2 ){
                $cad .= '<td width="24%"><a href="' .$reg[1] .'"><div ' .$aux2 .'  >' .Cadena::acortar($reg[0]) .'</div></a></td>';
                $cad .= '<td width="1%">&nbsp;</td>';
                $h = 3;
            }else{
                $cad .= '<td width="24%"><a href="' .$reg[1] .'"><div ' .$aux2 .' >' .Cadena::acortar($reg[0]) .'</div></a></td></tr>';
                $cad .= '<tr><td colspan="7" style="height: 0.4em;">&nbsp;</td></tr>';
                $h = 0;
            }
        } // for
                $cad .= '<tr><td width="24%">&nbsp;</td><td width="1%">&nbsp;</td><td width="24%">&nbsp;</td><td width="1%">&nbsp;</td><td width="24%">&nbsp;</td><td width="1%">&nbsp;</td><td width="24%">&nbsp;</td></tr>';
        $cad .= '</body></table>';
        return $cad;
    } // eof 



    

    
    /*
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
    public static function JQGrid( $ctrl, $cabecera, $registros, $titulo = '', $json, $subact='', $detail = NULL ){
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
