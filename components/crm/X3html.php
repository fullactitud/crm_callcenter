<?php
namespace app\components\crm;

use Yii;

use app\models\Proyecto;
use app\models\Usuario;
use app\models\Perfil;

use app\models\xMenu;
use app\models\xProyecto;
use app\models\xUsuario;
use app\models\xPerfil;
use app\models\Aux;


/**
 * Clase helper para manejar elementso HTML
 */
class X3html{
   


    /**
     * Convierte una cadena normal en una cadena identidicador,
     * que es usada para identificar elementos html
     */
    public static function texto2id( $cad ){

        $cad = str_replace(' ','',strtolower(trim($cad)));
        $cad = str_replace("\t",'',$cad);
        $cad = str_replace("\n",'',$cad);        

        
        return $cad;
    } // eof #######################################################
    



    

    /**
     * Elimina caracteres de escape
     */
    public static function validar( $cad ){
        $cad = str_replace("'", '', $cad);
        $cad = str_replace('\\', '/', $cad);
        
        return $cad;
    } // eof #######################################################

    

    





        



        







    


    /**
     * Decodificación 
     * Se usa para NO pasar el nombre de la tabla y campos por mensajes POST y GET
     * @param integer 
     * @return string Nombre de la tabla o campo 
     */
    public static function dcd( $id ){
        $vct = array();

        $pry = 'test';
        
        // TABLAS ////////////////
        $vct['110'] = 'a.app';
        $vct['111'] = 'a.estatus';
        $vct['112'] = 'a.menu';
        $vct['113'] = 'a.modulo';
        $vct['114'] = 'a.modulo_perfil';
        $vct['115'] = 'a.modulo_tp';
        $vct['116'] = 'a.perfil';
        $vct['117'] = 'a.proyecto';
        $vct['118'] = '';
        $vct['119'] = 'a.usuario';
        $vct['120'] = 'a.usuario_perfil';
        $vct['121'] = 'a.usuario_proyecto';
        
        
        $vct['211'] = $pry .'.agenda';
        $vct['212'] = $pry .'.agenda_cliente';
        $vct['213'] = $pry .'.archivo';
        $vct['214'] = $pry .'.archivo_encuesta';
        $vct['215'] = $pry .'.cabecera';
        $vct['216'] = $pry .'.cliente';
        $vct['217'] = $pry .'.cliente_opcion';
        $vct['218'] = $pry .'.cliente_usuario';
        $vct['219'] = $pry .'.data1';
        $vct['220'] = $pry .'.data2';	
        $vct['221'] = $pry .'.data3';
        $vct['222'] = $pry .'.data4';
        $vct['223'] = $pry .'.data5';
        $vct['224'] = $pry .'.data6';
        $vct['225'] = $pry .'.data7';
        $vct['226'] = $pry .'.data8';
        $vct['227'] = $pry .'.encuesta';
        $vct['228'] = $pry .'.encuesta_barrida';
        $vct['229'] = $pry .'.estatus';
        $vct['230'] = $pry .'.llamada';	
        $vct['231'] = $pry .'.llamada_luego';		
        $vct['234'] = $pry .'.opcion';
        $vct['236'] = $pry .'.plan';
        $vct['237'] = $pry .'.pregunta';
        $vct['239'] = $pry .'.pregunta_opcion';	
        $vct['243'] = $pry .'.tipificacion';
        $vct['244'] = $pry .'.tipificacion_pregunta';
        $vct['245'] = $pry .'.tipificacion_pregunta_opcion';
        $vct['246'] = $pry .'.tipo_encuesta';
        $vct['247'] = $pry .'.tipo_modulo';
        $vct['248'] = $pry .'.tipo_opcion';
        $vct['249'] = $pry .'.tipo_pregunta';
        $vct['250'] = $pry .'.usuario_encuesta';
        
        // CAMPOS ////////////////        
        $vct['501'] = 'id';
        $vct['502'] = 'de';
        $vct['503'] = 'st';
        $vct['504'] = 'reg';
        $vct['505'] = 'id_usuario';
        $vct['506'] = 'id_proyecto';
        $vct['507'] = 'id_encuesta';
        $vct['508'] = 'id_archivo';
        $vct['509'] = '';
        $vct['510'] = '';
        $vct['511'] = '';
        $vct['512'] = '';
        $vct['513'] = '';
        $vct['514'] = '';
        $vct['515'] = '';
        $vct['516'] = '';
        $vct['517'] = '';
        $vct['518'] = '';
        
        
        $vct['601'] = 'c01'; $vct['602'] = 'c02'; $vct['603'] = 'c03'; $vct['604'] = 'c04'; $vct['605'] = 'c05';
        $vct['606'] = 'c06'; $vct['607'] = 'c07'; $vct['608'] = 'c08'; $vct['609'] = 'c09'; $vct['610'] = 'c10';
        $vct['611'] = 'c11'; $vct['612'] = 'c12'; $vct['613'] = 'c13'; $vct['614'] = 'c14'; $vct['615'] = 'c15';
        $vct['616'] = 'c16'; $vct['617'] = 'c17'; $vct['618'] = 'c18'; $vct['619'] = 'c19'; $vct['620'] = 'c20';
        $vct['621'] = 'c21'; $vct['622'] = 'c22'; $vct['623'] = 'c23'; $vct['624'] = 'c24'; $vct['625'] = 'c25';
        $vct['626'] = 'c26'; $vct['627'] = 'c27'; $vct['628'] = 'c28'; $vct['629'] = 'c29'; $vct['630'] = 'c30';
        $vct['631'] = 'c31'; $vct['632'] = 'c32'; $vct['633'] = 'c33'; $vct['634'] = 'c34'; $vct['635'] = 'c35';
        $vct['636'] = 'c36'; $vct['637'] = 'c37'; $vct['638'] = 'c38'; $vct['639'] = 'c39'; $vct['640'] = 'c40';
        $vct['641'] = 'c41'; $vct['642'] = 'c42'; $vct['643'] = 'c43'; $vct['644'] = 'c44'; $vct['645'] = 'c45';
        $vct['646'] = 'c46'; $vct['647'] = 'c47'; $vct['648'] = 'c48'; $vct['649'] = 'c49'; $vct['650'] = 'c50';
        $vct['651'] = 'c51'; $vct['652'] = 'c52'; $vct['653'] = 'c53'; $vct['654'] = 'c54'; $vct['655'] = 'c55';
        $vct['656'] = 'c56'; $vct['657'] = 'c57'; $vct['658'] = 'c58'; $vct['659'] = 'c59'; $vct['660'] = 'c60';
        $vct['661'] = 'c61'; $vct['662'] = 'c62'; $vct['663'] = 'c63'; $vct['664'] = 'c64'; $vct['665'] = 'c65';
        $vct['666'] = 'c66'; $vct['667'] = 'c67'; $vct['668'] = 'c68'; $vct['669'] = 'c69'; $vct['670'] = 'c70';
        $vct['671'] = 'c71'; $vct['672'] = 'c72'; $vct['673'] = 'c73'; $vct['674'] = 'c74'; $vct['675'] = 'c75';
        $vct['676'] = 'c76'; $vct['677'] = 'c77'; $vct['678'] = 'c78'; $vct['679'] = 'c79'; $vct['680'] = 'c80';
        $vct['681'] = 'c81'; $vct['682'] = 'c82'; $vct['683'] = 'c83'; $vct['684'] = 'c84'; $vct['685'] = 'c85';
        $vct['686'] = 'c86'; $vct['687'] = 'c87'; $vct['688'] = 'c88'; $vct['689'] = 'c89'; $vct['690'] = 'c90';
        $vct['691'] = 'c91'; $vct['692'] = 'c92'; $vct['693'] = 'c93'; $vct['694'] = 'c94'; $vct['695'] = 'c95';
        $vct['696'] = 'c96'; $vct['697'] = 'c97'; $vct['698'] = 'c98'; $vct['699'] = 'c99';
        
        
        if( array_key_exists($id,$vct) )
            return $vct[$id];
        else
            return $id;
    } // eof #######################################################







    
    public static function agrupar( $vct, $tp = null ){
        $cad = '';
        switch( $tp ){
        case '1':

            break;
        default:
            $cad = self::agrupar1( $vct );
            break;
        }
        return $cad;
    } // eof #######################################################


    
    /**
     * Agrupacion responsive
     */
    public static function agrupar1( $vct ){
        $cad = '<div class="form-group">';
        foreach( $vct as $v )
            if( $v[0] == 'label' )
                $cad .= '<label class="control-label col-sm-' .$v[2] .'" for="' .$v[3] .'">'. Yii::t('app/crm', $v[1]) .':</label>';
            else if( $v[0] == 'div' )
                $cad .= '<div class="col-sm-' .$v[2] .'">' .$v[1] .'</div>';
        $cad .= '</div>';         
        return $cad;
    } // eof ####################################################
    



    /**
     * Cambia números por letras 
     */
    public static function letra($i){
        $v = array('1'=>'a','2'=>'b','3'=>'c','4'=>'d','5'=>'e','6'=>'f','7'=>'g','8'=>'h','9'=>'i','10'=>'j','11'=>'k','12'=>'l','13'=>'m','14'=>'n','15'=>'o','16'=>'p','17'=>'q','18'=>'r','19'=>'s','20'=>'t','21'=>'u','22'=>'v','23'=>'w','24'=>'x','25'=>'y','26'=>'z','27'=>'aa','28'=>'ab','29'=>'ac','30'=>'ad','31'=>'ae','32'=>'af','33'=>'ag','34'=>'ah','35'=>'ai','36'=>'aj','37'=>'ak','38'=>'al','39'=>'am','40'=>'an','41'=>'ao','42'=>'ap','43'=>'aq','44'=>'ar','45'=>'as','46'=>'at','47'=>'au','48'=>'av','49'=>'aw','50'=>'ax','51'=>'ay','52'=>'az','53'=>'ba','54'=>'bb','55'=>'bc','56'=>'bd','57'=>'be','58'=>'bf','59'=>'bg','60'=>'bh','61'=>'bi','62'=>'bj','63'=>'bk','64'=>'bl','65'=>'bm','66'=>'bn','67'=>'bo','68'=>'bp','69'=>'bq','70'=>'br','71'=>'bs','72'=>'bt','73'=>'bu','74'=>'bv','75'=>'bw','76'=>'bx','77'=>'by','78'=>'bz','79'=>'ca','80'=>'cb','81'=>'cc','82'=>'cd','83'=>'ce','84'=>'cf','85'=>'cg','86'=>'ch','87'=>'ci','88'=>'cj','89'=>'ck','90'=>'cl','91'=>'cm','92'=>'cn','93'=>'co','94'=>'cp','95'=>'cq','96'=>'cr','97'=>'cs','98'=>'ct','99'=>'cu','100'=>'cv','101'=>'cw','102'=>'cx','103'=>'cy','104'=>'cz');
        return $v[$i];
    } // eof #######################################################

    
 
        /*
         * 
         * $ctrl, schema
$cabecera, cabecera
$registros, vector de registros
$titulo, string
$json, action donde leer en json
$subact, action al haecr click
$detail, div 
         */
    public static function grid( $ctrl, $cabecera = null, $reg_inicio = 1, $vct, $tp = null, $width = '100%', $size = '0.75em', $numerar = true ){
 
        $cad = '';
        if( is_null($size) ) $size = '0.75em';
        switch( $tp ){
        case '1':
         
            $cad = self::listado( $ctrl, $cabecera, $vct, $titulo = '', $json, $subact='', $detail = NULL );
            break;
        default:
            $cad = self::grid1( $ctrl, $cabecera, $reg_inicio, $vct, $width, $size, $numerar );
            break;
        }
        return $cad;    
    } // eof ####################################################  table table-striped table-bordered







    
    public static function grid1( $ctrl, $cabecera, $reg_inicio = 1, $vct, $width = '100%', $size = '1.0em', $numerar = true ){
        $style =' style="padding: 0.3em; text-align: center; font-size: ' .$size .';" ';
        $style1 =' style="padding: 0.3em; text-align: right; font-size: ' .$size .';" ';
        $styleH =' style="padding: 0.3em; text-align: center; background-color: #eeeeee; font-size: ' .$size .';" ';
        $cad = '<table class="table-bordered" style="width:' .$width .';" ><tbody>';
        $columna = 0;
        $fila = 0;
        foreach( $vct as $linea ){
            $columna = 0;
            $contiene = 0;
            $cadAux = '';
            foreach( $linea as $campo ){
                // PRIMERA COLUMNA
                if( $numerar && $columna == 0 )
                    if( $reg_inicio == 1 && $fila == 0 )
                        $cadAux .= '<th' .$styleH .'>#</th>';
                    else $cadAux .= '<td' .$style1 .'>' .$fila .'</td>';
                if( trim($campo) != '' )
                    if( $reg_inicio == 1 && $fila == 0 )
                        if( $cabecera != null && isset($cabecera[$columna]) )
                            $cadAux .= '<th' .$styleH .'>' .strtoupper($cabecera[$columna]) .'</th>';
                        else
                            $cadAux .= '<th' .$styleH .'>' .strtoupper(trim($campo)) .'</th>';
                    else
                        $cadAux .= '<td' .$style .'>' .trim($campo) .'</td>';
                if( trim($campo) != '' ) $contiene++;
                $columna++;
            } // for
            if( $contiene > 0 ){
                $cad .= '<tr>' .$cadAux .'</tr>';
                $fila++;
            }
            $fila;
        } // for linea
        return '<p>' .($fila -1) .' registros</p>' .$cad .'</tbody></table>';
    } // eof ####################################################
    



    

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
url: 'index.php?r=$ctrl/df/out&op=$json',
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



    
    
} // class
