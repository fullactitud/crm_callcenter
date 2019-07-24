<?php
namespace app\controllers\soporte;
use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use app\models\xDbManager;
use app\models\LoginForm;

use app\components\crm\Mensaje;
use app\components\crm\Request;
use app\components\crm\Listado;
use app\components\crm\Ayuda;
use app\components\crm\JQGrid;
use app\components\crm\JSon;
use app\components\crm\Formulario;
use app\components\crm\Seccion;
use app\components\EncuestaProspecto1;
use app\components\EncuestaEntrada1;
use app\models\Aux;

use app\models\UploadForm;
use yii\web\UploadedFile;
use yii\widgets\ActiveForm;
use app\vendor\PHPExcel\PHPExcel\IOFactory;
use yii\swiftmailer\Mailer;




/**
 * Controler para manejar el instrumento
 */
class InstrumentoController extends Controller{

    /**
     * Proceso que se ejecuta antes de la carga del controller
     * Verifica que el usuario este validado
     */
    public function init(){
        if( Yii::$app->user->isGuest ){
            Yii::$app->getResponse()->redirect(array('/df/logout',302));
            Yii::$app->end();
        }
        parent::init();
    } // eof 



    /**
     * Valida que el codigo no este repetido
     */
    public function genCodigo( $proyecto, $enc_codigo, $enc_de, $enc_id){
        if( $enc_codigo == null ) // si el codigo no ha sido suministrado
            $enc_codigo = $enc_de;
        $enc_codigo = substr($this->codigo($enc_codigo),0,16);
        if( $enc_id == 0 ){ // si es una encuesta nueva
            $count = 1;
            $sql = "select id from " .$proyecto .".instrumento where codigo='" .$enc_codigo ."';";
            $res = Aux::findBySql($sql)->all();
            $count = count($res);
            while( $count > 0 ){
                $num = rand(0,9);
                $enc_codigo .= $num; 
                $sql = "select id from " .$proyecto .".instrumento where codigo='" .$enc_codigo ."';";
                $res = Aux::findBySql($sql)->all();
                $count = count($res);
            }
        }
        return strtolower($enc_codigo);
    } // eof 
    



    /**
     * Recibe el formulario de encuesta, y salva las entradas en la DB
     */
    public function actionAdd(){
        $txt = '';
        $x3fin = false;   
        $cod_proyecto = Request::rq('proyecto');
        $entradas = (int)Request::rq('jid'); // cantidad de entradas
        
        if( $entradas > 0 && $cod_proyecto != '' ){ // si se selecciono un proyecto, y existen entradas
            $encuesta = $this->insertInstrumento();
            if( isset($encuesta->id) && (int)$encuesta->id > 0 ){            
                $ia = $this->insertCabecera( $cod_proyecto, $encuesta->id );
                
                // Guarda un registro en barrida
                $sql = "with upsert as (update " .$cod_proyecto .".barrida set id_instrumento='" .$encuesta->id ."', barrida='1' where id_instrumento='" .$encuesta->id ."' RETURNING *) insert into " .$cod_proyecto .".barrida (id_instrumento, barrida) select '" .$encuesta->id ."', '1' where not exists (select * from upsert)";
                Aux::findBySql($sql)->one();
                
              
                $this->insertReporte( $cod_proyecto, $encuesta->id );
                for( $i=1;  $i <= $entradas ;$i++ )
                    $txt = $this->insertEntrada( $cod_proyecto, $encuesta->id, $entradas, $i, $ia );
                if( $x3fin == false ) $this->cerrarIntrumento( $cod_proyecto, $encuesta->id );
                $this->modificarIrA( $cod_proyecto, $encuesta->id );
            }else{
                $txt = '<li> Encuesta no guardada.</li>';
                $this->redirect(Yii::$app->params['baseUrl'] .'index.php?r=soporte/carga/index&x3txt=' .$txt, 302);
            }
        }else $txt = '<li> Encuesta no guardada, debe seleccionar un <b>proyecto</b>, y debe poseer <b>entradas</b>.</li>';        
        
        if( $txt == '' ) $txt = '<li> Encuesta guardada correctamente. </li>';
        
        $this->redirect(Yii::$app->params['baseUrl'] .'index.php?r=soporte/carga/index&x3txt=' .$txt, 302);
    } // eof #######################################################


    

    /**
     * Salva el instrumento, creandolo en la DB
     */
    public function insertInstrumento(){
        $proyecto         = Request::rq('proyecto');
        $enc_id           = (int)Request::rq('encuesta_id');
        $enc_tp           = (int)Request::rq('id_instrumento_tp'); 
        $enc_de           = Request::rq('de');
        $enc_codigo       = Request::rq('codigo');
        $enc_id_admin     = (int)Request::rq('id_admin');
        $enc_st           = (int)Request::rq('st');
        
        $enc_id_bootstrap = (int)Request::rq('id_bootstrap');
        $enc_id_font      = (int)Request::rq('id_font');
        $enc_id_jqueryui  = (int)Request::rq('id_jqueryui'); 
        
        $enc_siguiente    = (int)Request::rq('siguiente');
        $enc_desplegar    = (int)Request::rq('desplegar');
        $enc_back         = (int)Request::rq('back');
        $enc_llamar       = (int)Request::rq('llamar_luego');
        $enc_cuotas       = (int)Request::rq('cuotas');
        $enc_dominios     = (int)Request::rq('dominios');
        $enc_inicial      = Request::rq('entrada_inicial');
         
        // No lo estoy tomando encuenta
        $enc_barrida      = (int)Request::rq('barrida');
        $enc_dominio      = (int)Request::rq('columna_dominio');
        //$enc_color       = (int)Request::rq('color');
        $enc_fref         = (int)Request::rq('columna_fecha_ref');
        $enc_movil        = (int)Request::rq('columna_movil'); 
        $enc_nombre       = (int)Request::rq('columna_nombre');
        $enc_agente       = (int)Request::rq('columna_agente');
        
        $enc_codigo = $this->genCodigo( $proyecto, $enc_codigo, $enc_de, $enc_id);
        
       

        $sql = "WITH upsert AS (UPDATE " .$proyecto .".instrumento SET id_instrumento_tp='" .$enc_tp ."', de='" .$enc_de ."', barrida='" .$enc_barrida ."', st='" .$enc_st ."', columna_dominio='" .$enc_dominio ."', id_admin='" .$enc_id_admin ."', id_bootstrap='" .$enc_id_bootstrap ."', id_font='" .$enc_id_font ."', id_jqueryui='" .$enc_id_jqueryui ."', columna_fecha_ref='" .$enc_fref ."', columna_movil='" .$enc_movil ."', columna_nombre='" .$enc_nombre ."', entrada_inicial='" .$enc_inicial ."', columna_agente='" .$enc_agente ."', siguiente='" .$enc_siguiente ."', desplegar='" .$enc_desplegar ."', back='" .$enc_back ."', llamar_luego='" .$enc_llamar ."', cuotas='" .$enc_cuotas ."', dominios='" .$enc_dominios ."' WHERE id='" .$enc_id ."' RETURNING *)
 INSERT INTO " .$proyecto .".instrumento (id_instrumento_tp, de, barrida, st, columna_dominio, codigo, id_admin, id_bootstrap, id_font, id_jqueryui, columna_fecha_ref, columna_movil, columna_nombre, entrada_inicial, columna_agente, siguiente, desplegar, back, llamar_luego, cuotas, dominios) SELECT  '" .$enc_tp ."', '" .$enc_de ."', '" .$enc_barrida ."', '" .$enc_st ."', '" .$enc_dominio ."', '" .$enc_codigo ."', '" .$enc_id_admin ."', '" .$enc_id_bootstrap ."', '" .$enc_id_font ."', '" .$enc_id_jqueryui ."', '" .$enc_fref ."', '" .$enc_movil ."', '" .$enc_nombre ."', '" .$enc_inicial ."', '" .$enc_agente ."', '" .$enc_siguiente ."', '" .$enc_desplegar ."', '" .$enc_back ."', '" .$enc_llamar ."', '" .$enc_cuotas ."', '" .$enc_dominios ."' WHERE NOT EXISTS (SELECT * FROM upsert)";
        Aux::findBySql($sql)->one();
        
        $sql = "select * from " .$proyecto .".instrumento where codigo='" .$enc_codigo ."' limit 1;";
        $encuesta = Aux::findBySql($sql)->one();
        $this->createController( $proyecto, $enc_codigo, $enc_de );
        return $encuesta;
    } // eof 
    
    
    

    /**
     * Crea el controller del instrumento
     * en el directorio /controllers/proyecto/
     */
    public function createController( $proyecto, $enc_codigo, $enc_de ){
       
        $carpeta = Yii::$app->basePath .'/controllers/proyecto/' .$proyecto;
        $controller = file_get_contents( Yii::$app->basePath .'/src/base/controller.php' );
        $controller = str_replace('__codigo__', $proyecto, $controller);
        $controller = str_replace('__codigo0__', ucfirst($enc_codigo), $controller);
        $controller = str_replace('__de__', ucfirst($enc_de), $controller);
        file_put_contents( $carpeta .'/' .ucfirst($enc_codigo) .'Controller.php', $controller );

    } // eof                  
    



    /**
     * Inserta las cabeceras (de archivo) del instrumento en la DB
     */
    public function insertCabecera( $proyecto, $encuesta_id ){
   
        $ia = 1;
        $col_id = (int)Request::rq('dato_id_' .$ia);
        $col_dat = Request::rq('dato_' .$ia);
        $col_col = Request::rq('columna_' .$ia);
        $col_ver = (int)Request::rq('dato_mostrar_' .$ia);
        $col_edi = (int)Request::rq('dato_editar_' .$ia);
        $col_del = (int)Request::rq('delcol_' .$ia);
        if( $col_del == 1 ) $st = 0;
        else $st = 1;
        
        while( $col_dat != '' ){ // si fue suministrado el dato
            $sql = "WITH upsert AS ( ";
            $sql .= " UPDATE " .$proyecto .".cabecera SET id_instrumento='" .$encuesta_id ."', de='" .$col_dat ."', columna='" .$col_col ."', orden='" .$ia ."', tp='0', mostrar='" .$col_ver ."', editar='" .$col_edi ."', st='" .$st ."' WHERE id_instrumento='" .$encuesta_id ."' and id='" .$col_id ."'";
            $sql .= " RETURNING *) ";
            $sql .= " INSERT INTO " .$proyecto .".cabecera (id_instrumento, de, columna, orden, tp, mostrar, editar, st) "; 
            $sql .= " SELECT " .$encuesta_id .", '" .$col_dat ."', '" .$col_col ."', " .$ia .", 0, '" .$col_ver ."', '" .$col_edi ."', '" .$st ."' WHERE NOT EXISTS (SELECT * FROM upsert) ";
            Aux::findBySql($sql)->one();
            
            $ia++;
            $col_id = (int)Request::rq('dato_id_' .$ia);
            $col_dat = Request::rq('dato_' .$ia);
            $col_col = Request::rq('columna_' .$ia);
            $col_ver = (int)Request::rq('dato_mostrar_' .$ia);
            $col_edi = (int)Request::rq('dato_editar_' .$ia);
            $col_del = (int)Request::rq('delcol_' .$ia);
            if( $col_del == 1 ) $st = 0;
            else $st = 1;
        }
        return $ia;

    } // eof 






    

    /**
     * Agrega ó actualiza la entrada x3fin 
     */ 
    public function cerrarIntrumento( $proyecto, $encuesta_id ){

        $insert = "with upsert as ( ";
        $insert .= " update " .$proyecto .".entrada set id_instrumento='" .$encuesta_id ."', id_pregunta_tp='1', de='Queremos agradecerle su tiempo y disposición.<br />Hasta luego, y que tenga un buen día!', codigo='x3fin', ir_a = NULL where id_instrumento='" .$encuesta_id ."' and codigo='x3fin'";
        $insert .= " returning *) ";
        $insert .= " insert into " .$proyecto .".entrada (id_instrumento, id_pregunta_tp, de, codigo, ir_a) "; 
        $insert .= " select '" .$encuesta_id ."', '1', 'Queremos agradecerle su tiempo y disposición.<br />Hasta luego, y que tenga un buen día!', 'x3fin', NULL WHERE NOT EXISTS (select * from upsert) ";
        Aux::findBySql($insert)->one();
    } // eof 
    




    

    
    /**
     * Inserta los reprotes validos para el instrumento en la DB
     */
    public function insertReporte( $proyecto, $id_instrumento ){
        $objs = Aux::findBySql("select id from a.reporte where st!='0' ;")->all();
        foreach( $objs as $obj ){    
            $rpt = (int)Request::rq('report_' .$obj->id);
            $insert = "with upsert as ( ";
            $insert .= " update a.reporte_instrumento set id_reporte='" .$obj->id ."', proyecto='" .$proyecto ."', id_instrumento='" .$id_instrumento ."', orden='0', st='" .$rpt ."' where id_reporte='" .$obj->id ."' and proyecto='" .$proyecto ."' and id_instrumento='" .$id_instrumento ."' ";
            $insert .= " returning *) ";
            $insert .= " insert into a.reporte_instrumento (id_reporte, proyecto, id_instrumento, orden, st) "; 
            $insert .= " select '" .$obj->id ."', '" .$proyecto ."', '" .$id_instrumento ."', '0', '" .$rpt ."' WHERE NOT EXISTS (select * from upsert) ";
            Aux::findBySql($insert)->one();
        }        
    } // eof 







    

    /**
     * Inserta las entradas del instrumento en la tabla entrada
     */    
    public function insertEntrada( $cod_proyecto = '', $id_instrumento = 0, $entradas, $i = 0, &$ia = 0 ){
        /*
         * $i    Para datos que dependen de la posicion de la entrada
         * $ia   Para datos que dependen de la posicion en la cabecera
         */
        
        $txt = '';
        $texto = Request::rq('text' .$i);
        $delete = (int)Request::rq('delete_' .$i);
        
        
        
        if( $texto != '' && $delete != 1 ){ // si existe el texto, y no quiero borrar esta entrada
            $codigo = strtolower( trim(Request::rq('idcod' .$i).'') );
            if( $codigo != 'x3fin' ){
                            
                $id_nw = (int)Request::rq('id' .$i);
                $codigo = strtolower( trim(Request::rq('idcod' .$i).'') );
                $ir_a = trim(Request::rq('ir_a_' .$i)) .''; // a donde quiero ir
                
                $tipo_entrada = (int)Request::rq('tipo' .$i);
                
                $col_id = (int)Request::rq('dato_id_' .$i);
                $col_dat = Request::rq('dato_' .$i);
                $col_efectiva = (int)Request::rq('efectiva_' .$i);
                
                
                if( $codigo == '' ) $codigo = 'p' .$i;
                if( $codigo == 'x3fin' ) $x3fin = true;
                
                
                $insert = "with upsert as ( ";
                $insert .= " update " .$cod_proyecto .".entrada set id_instrumento='" .$id_instrumento ."', id_pregunta_tp='" .$tipo_entrada ."', de='" .$texto ."', codigo='" .$codigo ."', ir_a='" .$ir_a ."', efectiva='" .$col_efectiva ."' WHERE id_instrumento='" .$id_instrumento ."' and codigo='" .$codigo ."'";
                $insert .= " returning *) ";
                $insert .= " insert into " .$cod_proyecto .".entrada (id_instrumento, id_pregunta_tp, de, codigo, ir_a, efectiva) "; 
                $insert .= " select '" .$id_instrumento ."','" .$tipo_entrada ."','" .$texto ."','" .$codigo ."', '" .$ir_a ."', '" .$col_efectiva ."' WHERE NOT EXISTS (select * from upsert) ";
                Aux::findBySql($insert)->one();
                unset($tipo_entrada);
                
                if( true )
                    $ent = Aux::findBySql("select * from " .$cod_proyecto .".entrada where id_instrumento='" .$id_instrumento ."' and de='" .Request::rq('text' .$i) ."' order by id DESC;")->one();
                
                if( $ent->id > 0 ){
                    // Agrega una cabecera por cada entrada
                    $ia++;
 
                    $col_col = Request::rq('pcolumna_id_' .$ia);
                    if( is_null($col_col) ){
                        $col_col = $this->columna($ia);
                    }

                    if( $codigo != 'x3fin' ){
                        $sql = "with upsert as ( ";
                        $sql .= " update " .$cod_proyecto .".cabecera set id_instrumento='" .$id_instrumento ."', de='" .$codigo ."', columna='" .$col_col ."', orden='" .$ia ."', tp='1', mostrar='0', editar='0', st='1' where id_instrumento='" .$id_instrumento ."' and de='" .$codigo ."'";
                        $sql .= " RETURNING *) ";
                        $sql .= " insert into " .$cod_proyecto .".cabecera (id_instrumento, de, columna, orden, tp, mostrar, editar, st, id_entrada) "; 
                        $sql .= " select " .$id_instrumento .", '" .$codigo ."', '" .$col_col ."', " .$ia .", '1', '0', '0', '1', " .$ent->id ." WHERE NOT EXISTS (SELECT * FROM upsert) ";
                        Aux::findBySql($sql)->one();
                    }
                    
                    
                    $opciones = (int)Request::rq('ops' .$i);
                    for( $j=1;  $j<=$opciones ;$j++ ){
                        $op_id_0 = (int)Request::rq('id_' .$i .'_' .$j);
                        $op_de_0 = Request::rq('text_' .$i .'_' .$j);
                        $op_valor_0 = Request::rq('valor_' .$i .'_' .$j);
                        $op_ir_a_0 = Request::rq('ir_a_' .$i .'_' .$j);
                        
                        $insert = "with upsert as ( ";
                        $insert .= " update " .$cod_proyecto .".entrada_op set id_entrada='" .$ent->id ."', de='" .$op_de_0 ."', valor='" .$op_valor_0 ."', ir_a='" .$op_ir_a_0 ."' WHERE id_entrada='" .$ent->id ."' and id='" .$op_id_0 ."'";
                        $insert .= " returning *) ";
                        $insert .= " insert into " .$cod_proyecto .".entrada_op (id_entrada, de, valor, ir_a) "; 
                        $insert .= " select '" .$ent->id ."', '" .$op_de_0 ."', '" .$op_valor_0 ."', '" .$op_ir_a_0 ."' WHERE NOT EXISTS (select * from upsert) ";
                        if( Aux::findBySql($insert)->one() ) $txt .= '<li> Opción no guardada. </li>';
                        unset( $op_de_0 );
                        unset( $op_valor_0 );
                        unset( $op_ir_a_0 );
                    } // for j
                    
                    

                    for( $u=1;  $u<=$entradas ; $u++ ){
                        $auxVector = Request::rq('requiere_' .$ent->id);
                        if( is_array($auxVector) )
                            foreach( $auxVector as $ux ){
                                $ux = explode('::', $ux);
                                if( count($ux) > 1 ){                                
                                    $sql = "with upsert as (update " .$cod_proyecto .".entrada_requiere set id_entrada='" .$ent->codigo ."', id_entrada_padre='" .$ux[0] ."', id_instrumento='" .$id_instrumento ."', st='" .(int)$ux[1] ."' where id_entrada='" .$ent->codigo ."' and id_entrada_padre='" .$ux[0] ."' and id_instrumento='" .$id_instrumento ."' RETURNING *)
    insert into " .$cod_proyecto .".entrada_requiere (id_entrada, id_entrada_padre, id_instrumento, st) SELECT '" .$ent->codigo ."', '" .$ux[0] ."', '" .$id_instrumento ."', '" .$ux[1] ."' WHERE NOT EXISTS (SELECT * FROM upsert)";
                                    Aux::findBySql($sql)->one();
                                }
                            }
                    }
                    
                }else $txt = '<li> Entrada no guardada.</li>';
            }
        }else if( $delete == 1 ){
            $id_nw = (int)Request::rq('id' .$i);
            if( $id_nw > 0 ){
                Aux::findBySql("delete from " .$cod_proyecto .".entrada where id='" .$id_nw ."' ;")->all();
                Aux::findBySql("delete from " .$cod_proyecto .".cabecera where id_entrada='" .$id_nw ."' ;")->all();
                Aux::findBySql("delete from " .$cod_proyecto .".entrada_op where id_entrada='" .$id_nw ."' ;")->all();
                Aux::findBySql("delete from " .$cod_proyecto .".entrada_requiere where id_entrada='" .$id_nw ."'; ")->all();
                Aux::findBySql("delete from " .$cod_proyecto .".entrada_requiere where id_entrada_padre='" .$id_nw ."'; ")->all();                
            }
        } else
            $txt = '<li> Entrada con texto vacio.</li>';  
        return $txt;
    } //eof 
    
    




    
   
    /**
     * MODIFICAR EL CODIGO EN EL CAMPO IR_A
     */
    public function modificarIrA( $proyecto, $encuesta_id ){
        $vectorId = array();
        $vectorCodigo = array();
        $vectorIr_a = array();
        $vectorId_pregunta_tp = array();
        
        $select = "select id, codigo, ir_a, id_pregunta_tp from " .$proyecto .".entrada where id_instrumento = '" .$encuesta_id ."';";
        $objs = Aux::findBySql($select)->all();
        foreach( $objs as $obj ){
            $vectorId[] = $obj->id;
            $vectorCodigo[] = $obj->codigo;
            $vectorIr_a[] = $obj->ir_a;
            $vectorId_pregunta_tp[] = $obj->id_pregunta_tp;
        }
        $vectorCodigo[-1] = '';
        foreach( $objs as $obj ){
            if( $obj->codigo != 'x3fin' && (int)substr($obj->ir_a,0,1) != 0 ){
                if( $obj->ir_a == 'x3fin' || $obj->ir_a == '' )
                    $auxIr = 'x3fin';
                else if( isset($vectorCodigo[$obj->ir_a -1]) )
                    $auxIr = $vectorCodigo[$obj->ir_a -1];
                else
                    $auxIr = '';
                $update = "update " .$proyecto .".entrada set ir_a = '" .$auxIr ."' where id='" .$obj->id ."';";
                Aux::findBySql($update)->one();
                if( false && ($obj->id_pregunta_tp == 3 || $obj->id_pregunta_tp == 4 || $obj->id_pregunta_tp == 5) ){
                    $select2 = "select id, ir_a from " .$proyecto .".entrada_op where id_entrada = '" .$obj->id ."';";
                    $objs2 = Aux::findBySql($select2)->all();
                    foreach( $objs2 as $obj2 ){           
                        if( $obj2->ir_a == 'x3fin' )
                            $auxIr2 = 'x3fin';
                        else if( $obj2->ir_a == '' && $obj->ir_a == '' )
                            $auxIr2 = 'x3fin';
                        else if( $obj2->ir_a == '' )
                            $auxIr2 = $obj->ir_a;
                        else if( isset($vectorCodigo[$obj2->ir_a -1]) )
                            $auxIr2 = $vectorCodigo[$obj2->ir_a -1];
                        else
                            $auxIr2 = '';
                        Aux::findBySql( "update " .$proyecto .".entrada_op set ir_a = '" .$auxIr2 ."' where id='" .$obj2->id ."' ;" )->one();
                    } // foreach
                } // if
            } // if
        } // for 
    } // eof 
    


    
    /**
     * Establece la plantilla para las Entradas
     */
    public function s1(){
        $res = <<<EOT
<div class="lisort1" id="__id__">
<br />
  <div style="position:relative; clear:both; margin: 0.5em; width:100%; height: auto;" >
	<div style="width:100%; height: auto;margin: none;"> 
      &nbsp; <span style="font-size:1.2em;font-weight:700;">__id__</span>° &nbsp; Entrada &nbsp; (pregunta ó solo texto) 
    </div>

	<div id="lisort1s__id__" style="position:relative;height: auto; display: block;">
	  
<!--  //////////////////////////////////////////////////////////////////////////////////////////////////////  -->
	  <div style="clear:both;">
		<div class="control-label col-sm-2" for="tipo__id__">
		  Tipo de campo &nbsp;
		</div>
		<div class="col-sm-3">
	      <select class="form-control" id="tipo__id__" name="tipo__id__" onChange="x3fun1(\'__id__\');" style="width:100%;">
			<option value="1" __selected_tipo_1__> Texto sin pregunta </option>
			<option value="2" __selected_tipo_2__> Pregunta para obtener un texto como respuesta </option>
			<option value="3" __selected_tipo_3__> Lista desplegable </option>
			<option value="4" __selected_tipo_4__> Lisŧa para hacer chequear un sola una opción </option>
			<option value="5" __selected_tipo_5__> Lista para hacer chequear una o varias opciones </option>
		  </select>
		  	<!-- option value="dia"> Fecha </option>
			<option value="hora"> Hora </option -->
		</div>
		<div class="col-sm-1"></div>	 
<!--  //////////////////////////////////////////////////////////////////////////////////////////////////////  -->

		<div class="control-label col-sm-2" for="id__id__"> Identificador &nbsp;
		</div>
		<div class="col-sm-3">\n
          <input type="hidden" id="id__id__" name="id__id__" value="__iden_id__" />\n
		  <input type="text" id="idcod__id__" name="idcod__id__" placeholder="Identificador en la base de datos" value="__identificador_id__"  style="width:100%;"/> \n
		</div>
		<div class="col-sm-1"></div>
</div>
<!--  //////////////////////////////////////////////////////////////////////////////////////////////////////  -->

	  <div style="clear:both; height: 2.5em;">
		<div class="control-label col-sm-2" for="text__id__"> Pregunta ó texto &nbsp;
		</div>
		<div class="col-sm-9">
		  <textarea id="text__id__" name="text__id__" placeholder="Texto de la Pregunta" defaultValue="" cols="80%"  style="width:100%;">__cuerpo_id__</textarea>
		</div>
</div>
<!--  //////////////////////////////////////////////////////////////////////////////////////////////////////  -->
	  <div style="clear:both; height: 2.2em;">
	
		<div class="control-label col-sm-2" for="efectiva___id__">Hace efectiva la encuesta &nbsp;
		</div>
		<div class="col-sm-3">
          <input type="checkbox" id="efectiva___id__" name="efectiva___id__" __checked_efectiva_1__ value="1"/> Si &nbsp; &nbsp;
          <!-- input type="radio" id="efectiva___id___0" name="efectiva___id__" __checked_efectiva_1__ value="1"/> Si &nbsp; &nbsp;
          <input type="radio" id="efectiva___id___0" name="efectiva___id__" __checked_efectiva_0__ value="0"/> No -->
		</div>
	 	<div class="col-sm-1"></div>
	  
<!--  //////////////////////////////////////////////////////////////////////////////////////////////////////  -->	
	  <div class="control-label col-sm-2" for="ir_a___id__">Entrada siguiente por defecto &nbsp;
	  </div>
		<div class="col-sm-3">
		  <select class="form-control" id="ir_a___id__" name="ir_a___id__" title="Ir a"></select>
		</div>
	 		<div class="col-sm-1"></div>
</div>

<!--  //////////////////////////////////////////////////////////////////////////////////////////////////////  -->
	  <div style="clear:both; height: 2.2em;">


	  
		<div class="control-label col-sm-2"> Requiere </div>
		<div id="div_requiere___id__" class="col-sm-9">
          requieres___iden_id__
		</div>
	  <div class="col-sm-1"></div>
</div>
<!--  //////////////////////////////////////////////////////////////////////////////////////////////////////  -->


	    <input type="hidden" id="ops__id__" name="ops__id__" value="0" />

<!--  //////////////////////////////////////////////////////////////////////////////////////////////////////  -->
	  <div id="x3div_opciones___id__" style="clear:both; height: auto; display:none;">
		<div class="control-label col-sm-2" for="addop"> Opciones &nbsp;</div>
		<div class="col-sm-10" style="height: auto;">
		  <div class="widget">
			<div id="addop" onClick="agregaropcion(\'sortable___id__\',\'__id__\');" style="text-transform:underline; color:#0000ff; cursor:pointer; cursor:hand;"> Agregar Opción </div>
		  </div>
		  <div style="position:relative; height:auto;">
	  		<ul id="sortable___id__" class="sortable___id__"></ul>
		  </div>
		</div>
	    <div class="col-sm-1"></div>
      </div>
<!--  //////////////////////////////////////////////////////////////////////////////////////////////////////  -->

	  <div style="clear:both; height: 2.2em;">
		<div class="control-label col-sm-2" for="delete___id__"> Eliminar &nbsp; </div>
		<div class="col-sm-7">
          <input type="checkbox" id="delete___id__" name="delete___id__" __delete_checked___id__ value="1"/> Marcar esta entrada para ser eliminada al salvar.
		</div>
	 	<div class="col-sm-1"></div>
	</div>
  </div>
</div>
<br /><br />
EOT;
        return $res;
    } // eof ##########################################################
    
    


    /** 
     * Establece la plantilla para las Opciones de las entradas 
     */
    public function s2(){
        return '<div class="lisort2" id="lisort2___id_____id2__" style="width:100%;height:2.0em;">
  <table style="width:100%;"><tbody><tr>
<input type="hidden" name="id___id_____id2__" value="0" />
<td><input type="text" name="text___id_____id2__" placeholder="Texto de la opción" defaultValue="" style="width:300px;"/></td>
<td><input type="text" name="valor___id_____id2__" placeholder="Valor de la opción" style="width:150px;"/></td>
<td><select class="form-control" id="ir_a___id_____id2__" name="ir_a___id_____id2__" title="Al marcar esta opción ir a" style="width:200px;"></select></td>

<td style="text-align:right;" title="Eliminar al salvar"> <input type="checkbox" id="delete___id___0" name="delete___id__" __delete_checked__id_____id2__ value="1"/> &nbsp; </td>
<td title="Eliminar al salvar"> Eliminar opción</td>

</tr></tbody></table>
</div>';
    } // eof ##########################################################
    


    
    /**
     * Consulta las cabeceras validas del instrumento
     */
    public function pCargaData1( $x3Proyecto, $idInstrumento, $opciones ){
        $cad = '';
        $i = 0;
        if( $x3Proyecto != '' && (int)$idInstrumento != 0 ){
            $cabeceras = Aux::findBySql( "select * from " .$x3Proyecto .".cabecera where id_instrumento ='" .$idInstrumento ."' and tp in ('0','1') and st!='0' order by id asc;" )->all();
            foreach( $cabeceras as $cabecera ){
                $i++;
                $cad .= $this->pCargaData2( $i, $opciones, $cabecera->id, $cabecera->de, $cabecera->columna, $cabecera->mostrar, $cabecera->editar, $cabecera->st );
            }
        }
        return $cad;
    } // eof 

        

    
    /**
     * Carga data (cabeceras)
     */
    public function pCargaData2( $num, $opciones, $id, $cabecera, $columna, $mostrar = 0, $editar = 0, $borrar = 0 ){
        $df_mostrar = ((int)$mostrar)?' checked="checked" ':'';
        $df_editar = ((int)$editar)?' checked="checked" ':'';
        $df_ignorar = (!(int)$borrar)?' checked="checked" ':'';
        $cad = '
<div class="form-group" style="clear:both; font-size: 0.8em; height: 2.0em;">
  <div class="col-sm-1"> Data:</div>
  <div class="col-sm-2">
    <input type="hidden" id="dato_id_' .$num .'" name="dato_id_' .$num .'" value="' .$id .'"/>
    <input type="text" id="dato_' .$num .'" name="dato_' .$num .'" placeholder="Dato" class="form-control" style="width:100%;" value="' .$cabecera .'"/>
  </div>                                                               
  <div class="col-sm-1"> Columna:</div>
  <div class="col-sm-2">
    <select id="columna_' .$num .'" name="columna_' .$num .'" placeholder="Seleccione" class="form-control" style="width:100%;">';
        foreach( $opciones as $k=>$v )
            if( $k == $columna )
                $cad .= '<option value="'.$k.'" selected="selected"> &nbsp; ' .$v .'</option>';
            else
                $cad .= '<option value="'.$k.'"> &nbsp; ' .$v .'</option>';
        $cad .= '</select>
  </div>
  <div class="col-sm-2">
    <input type="checkbox" id="dato_mostrar_' .$num .'" name="dato_mostrar_' .$num .'" value="1" ' .$df_mostrar .'/> &nbsp; Mostrar
  </div>
  <div class="col-sm-2">
    <input type="checkbox" id="dato_editar_' .$num .'" name="dato_editar_' .$num .'" value="1" ' .$df_editar .'/> &nbsp; Editar
  </div>
  <div class="col-sm-2">
    <input type="checkbox" id="delcol_' .$num .'" name="delcol_' .$num .'" value="1" ' .$df_ignorar .'/> &nbsp; Ignorar
  </div>
</div>';
        return $cad;
    } // eof 
        




    /**
     * Carga data (cabeceras)
     */
    public function pCargaData3( $cabecera ){
        $cad = '<div class="form-group" style="clear:both; font-size: 0.8em; height: 2.0em;">
  <div class="col-sm-1"> Data:</div>
  <div class="col-sm-2">' .$cabecera .'</div>
  <div class="col-sm-9"></div>
</div>';
        return $cad;
    } // eof 



    

    /**
     * Lista los reportes existente
     */
    public function pReportes( $x3Proyecto, $encuesta_id ){
        $sql = "select r.*, ri.id as activo from a.reporte r left join a.reporte_instrumento ri on ri.id_reporte = r.id and ri.proyecto='".$x3Proyecto ."' and ri.id_instrumento='" .(int)$encuesta_id ."' and ri.st='1' where r.st='1';";
        $reportes = '<div class="row">';
        $rps = Aux::findBySql($sql)->all();
        $i5 = 0;
        foreach( $rps as $rp ){
            $i5++;
            if( $i5 > 3 ){
                $i5 = 1;
                $reportes .= '</div><div class="row">';
            }
            if( (int)$rp->activo != 0 )
                $reportes .= '<div class="col-sm-1" style="text-align:right;"><input type="checkbox" id="report_' .$rp->id .'" name="report_' .$rp->id .'" value="' .$rp->st .'" checked="checked"/></div><div class="col-sm-3" style="text-align:left;"> ' .$rp->id .'. ' .$rp->de .' </div>';
            else
                $reportes .= '<div class="col-sm-1" style="text-align:right;"><input type="checkbox" id="report_' .$rp->id .'" name="report_' .$rp->id .'" value="' .$rp->st .'" /></div><div class="col-sm-3" style="text-align:left;"> ' .$rp->id .'. ' .$rp->de .' </div>';            
        }
        $reportes .= '</div>';
        $cad = $reportes;
        return $cad;
        
    } // eof #######################################################
    
    
    
    
    public function actionOrdenar_deprecate(){
        return $this->render('ordenar');
    } // eof #######################################################
    



    
    public function add_deprecate($p1, $p2 = null, $p3 = 50, $p4 = 'false', $p5 = 'string', $p6 = 'left', $p7 = null, $p8 = null, $p9 = null, $p10 = null){
        if( $p2 == null )
            $p2 = strtoupper($p1);
        $aux = array();
        $aux[] = $p2;
        $aux[] = $p1;
        $aux[] = $p3;
        $aux[] = $p4;
        $aux[] = $p5;
        $aux[] = $p6;
        $aux[] = $p7;
        $aux[] = $p8;
        $aux[] = $p9;
        $aux[] = $p10;
        return $aux;
    } // eof ########################################################
  
              
   

   
    public function setSchema_deprecate( $p ){
        $this->schema = $p;
        return $this->schema;
    } // eof #######################################################
    
    

    
    public function listado_deprecate( $cabecera, $registros, $titulo = '', $json, $subact='', $detail = NULL ){
        $aux = $this->schema .'' .$subact;
        return Listado::JQGrid( $this->schema, $cabecera, $registros, $titulo, $json, $aux, $detail );
    } // eof #######################################################



    
    public static function JQGrid_deprecate( $cabecera, $registros, $titulo = '', $json, $subact='', $detail = NULL ){
        return Listado::JQGrid( $this->schema(), $cabecera, $registros, $titulo, $json, $this->schema().$subact, $detail );
    } // eof #######################################################
    
    

    
    /**
     * Crea el nombre que debe llevar la siguiente columna 
     */    
    public function columna($i){
        $cad = 'c' .$this->set0( $i, '2' );
        return $cad;
    } // eof #######################################################




    /**
     * Coloca ceros delante de un número, para ser usado como un codigo o nombre
     */
    public function set0( $valor, $ceros = '2' ){
        switch( $ceros ){
        case '1':
            if( (int)$valor < 10 )
                $valor = '0' .$valor;
            break;
        case '3':
            if( (int)$valor < 10 )
                $valor = '000' .$valor;
            else if( (int)$valor < 100 )
                $valor = '00' .$valor;
            else if( (int)$valor < 1000 )
                $valor = '0' .$valor;
            break;            
        default:
            if( (int)$valor < 10 )
                $valor = '00' .$valor;
            else if( (int)$valor < 100 )
                $valor = '0' .$valor;
            break;
        }
        return $valor;
 
    } // eof #######################################################




    /**
     * Sustituye caracteres estraños en una cadena que será usada como codigo
     */
    public function codigo( $codigo ){        
        $codigo = str_replace(' ', '', $codigo);
        $codigo = str_replace("\t", '', $codigo);
        $codigo = str_replace("\n", '', $codigo);
        $codigo = str_replace('á', '', $codigo);
        $codigo = str_replace('ó', '', $codigo);
        $codigo = str_replace('í', '', $codigo);
        $codigo = str_replace('é', '', $codigo);
        $codigo = str_replace('ú', '', $codigo);
        $codigo = str_replace('ñ', '', $codigo);
        $codigo = str_replace('Á', '', $codigo);
        $codigo = str_replace('Ó', '', $codigo);
        $codigo = str_replace('Í', '', $codigo);
        $codigo = str_replace('É', '', $codigo);
        $codigo = str_replace('Ú', '', $codigo);
        $codigo = str_replace('Ñ', '', $codigo);
        $codigo = str_replace('-', '_', $codigo);
        $codigo = str_replace('&', '_', $codigo);
        $codigo = str_replace('#', '_', $codigo);
        $codigo = str_replace('%', '_', $codigo);
        $codigo = str_replace('$', '_', $codigo);
        $codigo = str_replace('/', '_', $codigo);
        $codigo = str_replace('(', '_', $codigo);
        $codigo = str_replace(')', '_', $codigo);
        $codigo = str_replace('=', '_', $codigo);
        $codigo = str_replace("'", '', $codigo);
        $codigo = str_replace('.', '_', $codigo);
        $codigo = str_replace(',', '_', $codigo);
        $codigo = str_replace(':', '_', $codigo);
        $codigo = str_replace(';', '_', $codigo);
        return $codigo;
    } // eof 




    /**
     * Elimina un instrumento 
     */
    public function actionEliminar2(){
        $cod_proyecto = Request::rq('x3proyecto');
        $id_instrumento = Request::rq('x3id');
        $obj = Aux::findBySql("select codigo from " .$cod_proyecto .".instrumento where id= '" .$id_instrumento ."';")->one();
        unlink( Yii::$app->basePath .'/controllers/proyecto/' .$cod_proyecto .'/' .ucfirst($obj->codigo) .'Controller.php' );
        Aux::findBySql("delete from " .$cod_proyecto .".instrumento where id= '" .$id_instrumento ."';")->one();
        $this->redirect(Yii::$app->params['baseUrl'] .'index.php?r=soporte/carga/index&x3txt=Instrumento eliminado.', 302);
    } // eof 

    

    
    public function actionForm1_deprecate(){
      
        $id = 0;
        $aux = null;
        $menu = '<div style="width: 500px; padding: 1.0em; padding-top: 0px;">';
        $formulario = ''; 
        if( isset($_REQUEST['id']) ) $id = (int)$_REQUEST['id'];
        if( isset($_REQUEST['aux']) ) $aux = $_REQUEST['aux'];


        
        if( !is_null($aux) ){
            
            switch( $aux ){
            case 'perfil':
                $form[] = array('Id','id','hidden','center');
                $form[] = array('Descripción','de','text','left');
                $form[] = array('Estatus','st','radio','center',array('0'=>'No activo','1'=>'Activo'));
                $titulo = 'Características del Perfil';
                $obj = 'xPerfil';
                $reg = xPerfil::findAll(['id' => $id]);
                break;
                
            case 'usuario': //  df/from1?aux=usuario
                $form[] = array('Id','id','hidden','center');
                $form[] = array('Foto','foto','file','center'); 
                $form[] = array('Nombre de usuario','username','text','center');
                $form[] = array('Nombres','nombres','text','left');
                $form[] = array('Apellidos','apellidos','text','left');
                $form[] = array('Contraseña','password','password','left');
                $form[] = array('Correo Electrónico','email','text','left');
                $form[] = array('Empresa','ente','text','left');
                $form[] = array('Estatus','st','radio','center',array('3'=>'No activo','1'=>'Activo'));
                
                $titulo = 'Características del Usuario';
                $obj = 'xUsuario';
                $reg = xUsuario::findAll(['id' => $id]);
                break;


            case 'proyecto': //  df/from1?aux=proyecto
                $form[] = array('Id','id','hidden','center');
                $form[] = array('Logo','imagen','file','center'); 
                $form[] = array('Descripción','de','text','center');
                $form[] = array('Contacto','contacto','text','left');
                $form[] = array('Correo Electrónico','email','text','left');
                $form[] = array('Código','codigo','text','left');
                $form[] = array('País','pais','text','left',array('ve'=>'Venezuela','ec'=>'Ecuador','ec'=>'Colombia'));
                $form[] = array('Estatus','st','radio','center',array('3'=>'No activo','1'=>'Activo'));

                $titulo = 'Datos del Proyecto';
                $obj = 'xProyecto';
                $reg = Aux::findBySql("select * from a.proyecto where id='" .$id ."'")->one();
                break;


                
            default:
                break;
            }

            $reg = (is_array($reg) && isset($reg[0]))?$reg[0]:$reg;
            $formulario = Formulario::formulario1( $form, $obj, $reg, $titulo );
        }
        return $this->render('@views/df/formulario',array(
            'form'=>$formulario,
            'menu'=>$menu,
        ));
    } // eof #######################################################
    



    /**
     * Listado de instrumentos
     */
    public function actionInstrumentos(){ 
        Yii::$app->layout = 'proyectos';
 
        $sql = "select * from " .$this->schema .".instrumento where st in (1) order by id DESC;";
 
        $objs = xInstrumento::findBySql($sql)->all();
        return $this->render('@views/df/instrumentos',array(
            'objs'=>$objs,
            'schema'=>$this->schema,
        ));
    } // eof #######################################################
    


    
    /**
     * Listado de instrumentos para un campo select
     */
    public function optionInstrumentos( $p = '' ){ 
        $sql = "select id,de from " .$this->schema .".instrumento where st in (1) order by id DESC;";
        $objs = xInstrumento::findBySql($sql)->all();
        $cad = '<option value=""> Seleccione </option>';
        foreach( $objs as $obj ){
            if( $obj->id == $p ) $checked = ' checked="checked" ';
            else $checked = '';
            $cad .= '<option value="' .$obj->id .'" ' .$checked .'>' .$obj->de .'</option>';
        }
        return $cad;
    } // eof #######################################################    


    
    
    public function actionIn_deprecate(){

        Yii::$app->layout = 'embebido';
        $id = (int)$_REQUEST['a'];
        $tabla = Cedena::dcd(ctrlFunction::validar($_REQUEST['b']));
        $campo = Cadena::dcd(ctrlFunction::validar($_REQUEST['c']));
        $valor = Cadena::validar($_REQUEST['d']);
        if( false ){
            // $modelo = 'x'.ucfirst($tabla);
            switch( $tabla ){
            case 'usuario':
                $obj = xUsuario::findAll(['id' => $id]);
                if( is_array($obj) ) $obj = $obj[0];
                break;
            }
            if( (strtolower($campo) == 'password' || strtolower($campo) == 'repassword') && strlen($valor) < 3 ){
                // no haga nada
            }else{ 
                $obj->$campo = $valor;
                $obj->update();
            }
        }else{
            
            if( strtolower($campo) == 'foto' ){
                // file
            }else if( strtolower($campo) == 'password' || strtolower($campo) == 'repassword' ){
                if( strlen(trim($valor)) >= 3 ){
                    $sql = "update $tabla set $campo = '" .md5($valor) ."' where id='$id';";
                    Aux::findBySql($sql)->one();
                }
            }else{
                $sql = "update $tabla set $campo = '$valor' where id='$id';";
                Aux::findBySql($sql)->one();
            }   
        }
        $txt = '';
        return $this->render('txt',array(
            'txt'=>$txt,     
        ));    
    } // eof #######################################################
 

    
    
    /**
     * Desplega el formulario para crear un instrumento
     */
    public function actionDf(){
        //   INICIALIZACION
        $cad = '';
        $opciones = array();
        $jid = 0;
        $instrumento_de = '';
        $cargado = '';
        
        $option_cuotas = '';
        $option_dominios = '';
        $option_usuarios = '';
        $option_font = '';
        $option_jquery = '';
        $option_bootstrap = '';
        $option_instr_tp = '';
        
        
        $encuesta_codigo = '';
        $entrada_inicial = '';
        
        $option_desplegar = $option_back = $option_siguiente = '';
        $option_cdominio = $option_fecha_ref = $option_movil = $option_nombre = $option_agente = '';
        $option_llamar_luego = '';
        
        $encuesta_id = 0;
        $encuesta_st = 1;
        $encuesta_de = '';
        $encuesta_id_admin = 1;
        $encuesta_id_bootstrap = 0;
        $encuesta_columna_dominio = '';
        $encuesta_columna_fecha_ref = '';
        $encuesta_columna_movil = '';
        $encuesta_columna_nombre = '';
        $encuesta_columna_agente = '';
        $encuesta_siguiente = '';
        $encuesta_back = '';
        $encuesta_desplegar = '';
        $encuesta_id_jqueryui = 0;
        $encuesta_id_instrumento_tp = 0;
        $encuesta_id_font = 0;
        $encuesta_llamar_luego = 0;
        $encuesta_cuotas = 0;
        $encuesta_dominios = 0;
        
        $entradas = array();
        
        $cdom = array('c001', 'c002', 'c003', 'c004', 'c005', 'c006', 'c007', 'c008', 'c009', 'c010', 'c011', 'c012', 'c013', 'c014', 'c015', 'c016', 'c017', 'c018', 'c019', 'c020', 'c021', 'c022', 'c023', 'c024', 'c025', 'c026', 'c027', 'c028', 'c029', 'c030', 'c031', 'c032', 'c033', 'c034', 'c035', 'c036', 'c037', 'c038', 'c039', 'c040', 'c041', 'c042', 'c043', 'c044', 'c045', 'c046', 'c047', 'c048', 'c049', 'c050', 'c051', 'c052', 'c053', 'c054', 'c055', 'c056', 'c057', 'c058', 'c059', 'c060');

        $col = array('c001'=>'Columna 1', 'c002'=>'Columna 2', 'c003'=>'Columna 3', 'c004'=>'Columna 4', 'c005'=>'Columna 5', 'c006'=>'Columna 6', 'c007'=>'Columna 7', 'c008'=>'Columna 8', 'c009'=>'Columna 9', 'c010'=>'Columna 10', 'c011'=>'Columna 11', 'c012'=>'Columna 12', 'c013'=>'Columna 13', 'c014'=>'Columna 14', 'c015'=>'Columna 15', 'c016'=>'Columna 16', 'c017'=>'Columna 17', 'c018'=>'Columna 18', 'c019'=>'Columna 19', 'c020'=>'Columna 20', 'c021'=>'Columna 21', 'c022'=>'Columna 22', 'c023'=>'Columna 23', 'c024'=>'Columna 24', 'c025'=>'Columna 25', 'c026'=>'Columna 26', 'c027'=>'Columna 27', 'c028'=>'Columna 28', 'c029'=>'Columna 29', 'c030'=>'Columna 30', 'c031'=>'Columna 31', 'c032'=>'Columna 32', 'c033'=>'Columna 33', 'c034'=>'Columna 34', 'c035'=>'Columna 35', 'c036'=>'Columna 36', 'c037'=>'Columna 37', 'c038'=>'Columna 38', 'c039'=>'Columna 39', 'c040'=>'Columna 40', 'c041'=>'Columna 41', 'c042'=>'Columna 42', 'c043'=>'Columna 43', 'c044'=>'Columna 44', 'c045'=>'Columna 45', 'c046'=>'Columna 46', 'c047'=>'Columna 47', 'c048'=>'Columna 48', 'c049'=>'Columna 49', 'c050'=>'Columna 50', 'c051'=>'Columna 51', 'c052'=>'Columna 52', 'c053'=>'Columna 53', 'c054'=>'Columna 54', 'c055'=>'Columna 55', 'c056'=>'Columna 56', 'c057'=>'Columna 57', 'c058'=>'Columna 58', 'c059'=>'Columna 59', 'c060'=>'Columna 60');

        $df_requiere = array();
            
        $siguientes[0] = 'Prospecto Aleatorio';
        $siguientes[1] = 'Prospecto ordenado por ID';

        $backs[2] = 'Permite editar preguntas anteriores';
        $desplegars[0] = 'Agregando uno a uno';
        $desplegars[1] = 'Todos juntos';

        //   PARAMETROS
        $cod_proyecto = $x3Proyecto = Request::rq('x3proyecto') .'';
        $x3Id = (int)Request::rq('x3id');
        $s1 = $this->s1();
        $s2 = $this->s2();
        
        // OPERACION
        
        $s1 = mberegi_replace("[\n|\r|\n\r|\t||\x0B]", '', $s1);
        $s1a = str_replace('__checked_delete_1__', '', $s1);
        $s1a = str_replace('__cuerpo_id__', '', $s1a);
        $s1a = str_replace('__iden_id__', '', $s1a);
        $s1a = str_replace('__identificador_id__', '', $s1a);


        $cad0 = "\n entradas = '" .$s1a ."';\n";
        $s2 = mberegi_replace("[\n|\r|\n\r|\t||\x0B]", '', $s2);
        $cad0 .= "opciones = '" .$s2 ."';\n";
        
        
        $objProyectos = Aux::findBySql("select * from a.proyecto where st != '0';")->all();
        $proyectos = '<select class="form-control" id="proyecto" name="proyecto" placeholder="Seleccione" onSelect="#"/><option value=""> => Seleccione <= </option>';
        foreach( $objProyectos as $objProyecto ){
            if( $x3Proyecto == $objProyecto->codigo )
                $proyectos .= '<option value="' .$objProyecto->codigo .'" selected="selected">' .$objProyecto->de .'</option>';
            else $proyectos .= '<option value="' .$objProyecto->codigo .'">' .$objProyecto->de .'</option>';
        }
        $proyectos .= '</select>';
        

        $encuesta_admin = 1;
        
        if( !is_null($x3Proyecto) && $x3Id != 0 ){
            $encuesta = Aux::findBySql("select * from " .$x3Proyecto .".instrumento where id='" .$x3Id ."';")->one();
            $instrumento_de             = $encuesta->de;
            $entrada_inicial            = $encuesta->entrada_inicial;

            $encuesta_id                = $encuesta->id;
            $encuesta_entrada_inicial   = $encuesta->entrada_inicial;
            $encuesta_de                = $encuesta->de;
            $encuesta_st                = $encuesta->st;
            $encuesta_codigo            = $encuesta->codigo;
            $encuesta_id_admin          = $encuesta->id_admin;
            $encuesta_id_bootstrap      = $encuesta->id_bootstrap;
            $encuesta_columna_dominio   = $encuesta->columna_dominio;
            $encuesta_columna_fecha_ref = $encuesta->columna_fecha_ref;
            $encuesta_columna_movil     = $encuesta->columna_movil;
            $encuesta_columna_nombre    = $encuesta->columna_nombre;
            $encuesta_columna_agente    = $encuesta->columna_agente;
            $encuesta_siguiente         = $encuesta->siguiente;
            $encuesta_back              = $encuesta->back;
            $encuesta_desplegar         = $encuesta->desplegar;
            $encuesta_id_jqueryui       = $encuesta->id_jqueryui;
            $encuesta_id_instrumento_tp = $encuesta->id_instrumento_tp;
            $encuesta_id_font           = $encuesta->id_font;

            $encuesta_llamar_luego      = $encuesta->llamar_luego;
            $encuesta_cuotas            = $encuesta->cuotas;
            $encuesta_dominios          = $encuesta->dominios;

            $entradas = Aux::findBySql("select * from " .$x3Proyecto .".entrada where id_instrumento='" .$x3Id ."' order by id asc;")->all();






  
            $auxVector = '';
            foreach( $entradas as $entrada3 )
                if( $entrada3->codigo != 'x3fin' && $entrada3->st > 0 ) 
                    $auxVector .= '<input type="checkbox" id="requiere_{{ID}}_' .$entrada3->codigo .'" name="requiere_{{ID}}[]" value="' .$entrada3->codigo .'::{{ST' .$entrada3->codigo .'}}" {{CHCK' .$entrada3->codigo .'}} title="' .$entrada3->codigo .'"/> Entrada <b>' .$entrada3->codigo .'</b> &nbsp; &nbsp; ';            
            foreach( $entradas as $entrada2 ){
                if( $entrada2->codigo != 'x3fin' ){
                    $df_requiere[$entrada2->id] = str_replace('{{ID}}', $entrada2->id, $auxVector);
                    $sq = "select id_entrada_padre, st from " .$cod_proyecto .".entrada_requiere where id_entrada = '" .$entrada2->codigo ."' and id_instrumento='" .$x3Id ."' order by id asc;";
                   
                    $ent_reqs = Aux::findBySql($sq)->all();
                    foreach( $ent_reqs as $ent_req ){  
                        if( $ent_req->st > 0 ){
                            $df_requiere[$entrada2->id] = str_replace('{{ST' .$ent_req->id_entrada_padre .'}}', '1', $df_requiere[$entrada2->id]);
                            $df_requiere[$entrada2->id] = str_replace('{{CHCK' .$ent_req->id_entrada_padre .'}}', ' checked="checked" ', $df_requiere[$entrada2->id]);
                            
                        }else{
                            $df_requiere[$entrada2->id] = str_replace('{{ST' .$ent_req->id_entrada_padre .'}}', '0', $df_requiere[$entrada2->id]);
                            $df_requiere[$entrada2->id] = str_replace('{{CHCK' .$ent_req->id_entrada_padre .'}}', '', $df_requiere[$entrada2->id]);
                        }
                    }
                    foreach( $entradas as $entrada4 ){
                        $df_requiere[$entrada2->id] = str_replace('{{ST' .$entrada2->codigo .'}}', '0', $df_requiere[$entrada2->id]);
                        $df_requiere[$entrada2->id] = str_replace('{{CHCK' .$entrada2->codigo .'}}', '', $df_requiere[$entrada2->id]);
            }
                    
                }else{
                    $df_requiere[$entrada2->id] = '';
                }
            }

                
                
        } // if 




        
        
        $usuarios = Aux::findBySql("select * from a.usuario where st='1' order by nombres ASC, apellidos ASC")->all();
        foreach( $usuarios as $usuario )
            if( $usuario->id == $encuesta_id_admin )
                $option_usuarios .= '<option value="' .$usuario->id .'" selected="selected">' .$usuario->nombres .' ' .$usuario->apellidos .'</option>';
            else $option_usuarios .= '<option value="' .$usuario->id .'">' .$usuario->nombres .' ' .$usuario->apellidos .'</option>';
        
        
        
        $bootstraps = Aux::findBySql("select * from a.bootstrap where st='1' order by de ASC")->all();
        foreach( $bootstraps as $bootstrap )
            if( $bootstrap->id == $encuesta_id_bootstrap )
                $option_bootstrap .= '<option value="' .$bootstrap->id .'" selected="selected">' .$bootstrap->de .'</option>';
            else $option_bootstrap .= '<option value="' .$bootstrap->id .'">' .$bootstrap->de .'</option>';
        
        
        
        foreach( $cdom as $k => $cdom2 ){
            if( $cdom2 == $encuesta_columna_dominio )
                $option_cdominio .= '<option value="' .$cdom2 .'" selected="selected"> Columna ' .($k+1) .'</option>';
            else $option_cdominio .= '<option value="' .$cdom2 .'"> Columna ' .($k+1) .'</option>';
            
            if( $cdom2 == $encuesta_columna_fecha_ref )
                $option_fecha_ref .= '<option value="' .$cdom2 .'" selected="selected"> Columna ' .($k+1) .'</option>';
            else $option_fecha_ref .= '<option value="' .$cdom2 .'"> Columna ' .($k+1) .'</option>';
            
            if( $cdom2 == $encuesta_columna_movil )
                $option_movil .= '<option value="' .$cdom2 .'" selected="selected"> Columna ' .($k+1) .'</option>';
            else $option_movil .= '<option value="' .$cdom2 .'"> Columna ' .($k+1) .'</option>';
            
            if( $cdom2 == $encuesta_columna_nombre )
                $option_nombre .= '<option value="' .$cdom2 .'" selected="selected"> Columna ' .($k+1) .'</option>';
            else $option_nombre .= '<option value="' .$cdom2 .'"> Columna ' .($k+1) .'</option>';
            
            if( $cdom2 == $encuesta_columna_agente )
                $option_agente.= '<option value="' .$cdom2 .'" selected="selected"> Columna ' .($k+1) .'</option>';
            else $option_agente .= '<option value="' .$cdom2 .'"> Columna ' .($k+1) .'</option>';
        } // foreach
        
           
            
        foreach( $siguientes as $k => $v )
            if( $k == $encuesta_siguiente )
                $option_siguiente .= '<option value="' .$k .'" selected="selected"> ' .$v .'</option>';
            else $option_siguiente .= '<option value="' .$k .'"> ' .$v .'</option>';

        
        
        foreach( $backs as $k => $v )
            if( $k == $encuesta_back )
                $option_back .= '<option value="' .$k .'" selected="selected"> ' .$v .'</option>';
            else $option_back .= '<option value="' .$k .'"> ' .$v .'</option>';
        
                
        
        foreach( $desplegars as $k => $v )
            if( $k == $encuesta_desplegar )
                $option_desplegar .= '<option value="' .$k .'" selected="selected"> ' .$v .'</option>';
            else $option_desplegar .= '<option value="' .$k .'"> ' .$v .'</option>';
        


        $option_llamar_luego = $this->optionLlamarLuego($encuesta_llamar_luego );
        $option_cuotas       = $this->optionCuotas($encuesta_cuotas);
        $option_dominios     = $this->optionDominios($encuesta_dominios);
        $option_jquery       = $this->optionJquery( $encuesta_id_jqueryui );
        $option_instr_tp     = $this->optionInstrTp( $encuesta_id_instrumento_tp );
        $option_font         = $this->optionFont( $encuesta_id_font );
        
        
        
        if( count($entradas) > 0 ){
            
            $ii = 0;            
            foreach( $entradas as $entrada ){ // recorrer todas las entradas para llenar sus caracteristicas
                
              
                $jid++;
                $x3aux = str_replace('__id__', $jid, $s1);
                $x3aux = str_replace('\\', '', $x3aux);
                
                $ii++;
                $df_ir_a = '<select class="form-control" id="ir_a_' .$ii .'" name="ir_a_' .$ii .'" title="Ir a"><option value=""> Al seleccionar ir a </option>';
                $df_al_sel = '';
                
                for( $g=1; $g <= count($entradas) ;$g++ ){ // la relaciono con las demas entradas
                    if( $entrada->id == $entradas[$g-1]->id ) continue;
                    if( $entrada->ir_a == $entradas[$g-1]->codigo ){
                        $df_ir_a .= '<option value="' .$entradas[$g-1]->codigo .'" selected="selected"> &nbsp; Entrada ' .$g .' (' .$entradas[$g-1]->codigo .') </option>';
                    }else{
                        $df_ir_a .= '<option value="' .$entradas[$g-1]->codigo .'"> &nbsp; Entrada ' .$g .' (' .$entradas[$g-1]->codigo .') </option>';
                    }
                  





                    
                    
                } // for 
                $df_ir_a .= '</select>';



                
                $x3aux = str_replace('<select class="form-control" id="ir_a_' .$ii .'" name="ir_a_' .$ii .'" title="Ir a"></select>', $df_ir_a, $x3aux);
               




                   
                


                  
                $x3aux = str_replace('__iden_id__', $entrada->id, $x3aux); 
                $x3aux = str_replace( 'requieres_' .$entrada->id, $df_requiere[$entrada->id], $x3aux );
               

                $x3aux = str_replace('__identificador_id__', $entrada->codigo, $x3aux);
                $x3aux = str_replace('__idcod_id__', $entrada->codigo, $x3aux);
               
                $x3aux = str_replace('__cuerpo_id__', $entrada->de, $x3aux);
                
                $x3aux = str_replace('__selected_tipo_' .$entrada->id_pregunta_tp .'__', ' selected = "selected" ', $x3aux);
                $x3aux = str_replace('__selected_tipo_1__', '', $x3aux);
                $x3aux = str_replace('__selected_tipo_2__', '', $x3aux);
                $x3aux = str_replace('__selected_tipo_3__', '', $x3aux);
                $x3aux = str_replace('__selected_tipo_4__', '', $x3aux);
                $x3aux = str_replace('__selected_tipo_5__', '', $x3aux);

                if( $entrada->id_pregunta_tp == 3 || $entrada->id_pregunta_tp == 4 || $entrada->id_pregunta_tp == 5 )
                    $x3aux = str_replace('<div id="x3div_opciones_' .$ii .'" style="clear:both; height: auto; display:none;">', '<div id="x3div_opciones_' .$ii .'" style="clear:both; height: auto; display:block;">', $x3aux);
                
                if( $entrada->efectiva == 0 ){
                    $x3aux = str_replace('__checked_efectiva_0__', ' checked="checked" ', $x3aux);
                    $x3aux = str_replace('__checked_efectiva_1__', '', $x3aux);
                }else{
                    $x3aux = str_replace('__checked_efectiva__', ' checked="checked" ', $x3aux);
                    $x3aux = str_replace('__checked_efectiva_0__', '', $x3aux);
                    $x3aux = str_replace('__checked_efectiva_1__', ' checked="checked" ', $x3aux);
                }
            
                
                
                $select3 = "select * from " .$x3Proyecto .".entrada_op where id_entrada='" .$entrada->id ."' order by id asc;";
                $opciones[$entrada->id] = Aux::findBySql($select3)->all();
                
                
                $df_op = '<div class="lisort2" id="lisort2_' .$ii .'_1" style="width:100%;height:auto;"><table style="width:100%;"><tbody>';
                $t = 0;
                foreach( $opciones[$entrada->id] as $op0 ){
                    $t++;
                    $df_op .= '<script>jid2['.$t .']=' .count($opciones[$entrada->id]) .';</script><tr>
<input name="id_' .$ii .'_' .$t .'" value="' .$op0->id .'" type="hidden" />
                <td><input name="text_' .$ii .'_' .$t .'" placeholder="Texto de la opción" value="' .$op0->de .'" type="text" style="width:300px;"></td>
                <td><input name="valor_' .$ii .'_' .$t .'" placeholder="Valor de la opción" value="' .$op0->valor .'" type="text" style="width:150px;"></td>
                <td><select class="form-control" id="ir_a_' .$ii .'_' .$t .'" name="ir_a_' .$ii .'_' .$t .'" title="Al marcar esta opción ir a" style="width:200px;">
                <option value=""> Al seleccionar ir a </option>';
                   
                       
                    
                    for( $g=1; $g <= count($entradas) ;$g++ ){
                        if( $entrada->id == $entradas[$g-1]->id ) continue;
                        if( $op0->ir_a == $entradas[$g-1]->codigo ){
                            $df_al_sel .= '<option value="' .$entradas[$g-1]->codigo .'" selected="selected"> &nbsp; Entrada ' .$g .' (' .$entradas[$g-1]->codigo .') </option>';
                        }else{
                            $df_al_sel .= '<option value="' .$entradas[$g-1]->codigo .'"> &nbsp; Entrada ' .$g .' (' .$entradas[$g-1]->codigo .') </option>';
                        }
                    }
                    $df_op .= $df_al_sel;

          
                    $df_op .= '</select></td>
                <td style="text-align:right;" title="Eliminar al salvar"> 
<input id="delete_' .$ii .'_' .$t .'" name="delete_' .$ii .'_' .$t .'" __delete_checked' .$ii .'_' .$t .' value="1" type="checkbox"/> &nbsp; 
</td>
                <td title="Eliminar al salvar"> No tomar en cuenta</td>
                </tr>';
                }
                $df_op .= '</tbody></table></div>';
                $x3aux = str_replace('<ul id="sortable_' .$ii .'" class="sortable_' .$ii .'"></ul>', '<ul id="sortable_' .$ii .'" class="sortable_' .$ii .'">' .$df_op .'</ul>', $x3aux);
                
                $cargado .= $x3aux;
            } // foreach 
        }else if( false ){
            $jid++;
            $cargado .= str_replace('__id__', $jid, $s1a);

        }
        
               
        
        $cargado = str_replace('__id__', '', $cargado);
        $cargado = str_replace('__identificador_id__', '', $cargado);
        $cargado = str_replace('__cuerpo_id__', '', $cargado);
        $cargado = str_replace('__checked_efectiva_0__', '', $cargado);
        $cargado = str_replace('__checked_efectiva_1__', '', $cargado);
       
  
        $reportes = $this->pReportes( $x3Proyecto, $encuesta_id );

        $carga_data = $this->pCargaData1( $x3Proyecto, $encuesta_id, $col );
        

        $cad .= '<style>
#contentLeft li:hover {
    cursor: pointer;
}
#contentLeft li.ui-sortable-helper{
    cursor: move;
}
</style>
<script type="text/javascript" src="js/encuesta1.js"></script>
<script type="text/javascript">
var jid = \'' .$jid .'\';
' .$cad0 .'
</script>';
        

        $cad .= '<div class="titulos"> Instrumento </div>';
        $cad .= Mensaje::mostrar();
        $cad .= Ayuda::toHtml('instrumento');
        
        $cad .= Seccion::titulo('Configuración');
        
        $cad .= '<form id="enc1" name="enc1" action="' .Yii::$app->params['baseUrl'] .'index.php?r=soporte/instrumento/add" method="POST">
      <input type="hidden" id="encuesta_id" name="encuesta_id" value="' .$encuesta_id .'"/>
      <input type="hidden" id="jid" name="jid" value="0"/>
      <input type="hidden" id="x3sortedIDs" name="x3sortedIDs" value="0"/>';
              
              
        $tp1_selected = '';
        $tp3_selected = '';
        if( $encuesta_st == 1 ){
            $tp1_selected = ' selected="selected" ';
            $tp3_selected = '';
        }else if( $encuesta_st == 3 ){
            $tp1_selected = '';
            $tp3_selected = ' selected="selected" ';
        }
            
              
        $cad .= '<div class="form-group" style="clear:both; font-size: 0.8em; height: 2.0em;"> 
  <div class="control-label col-sm-1" for="de">Proyecto:</div>
  <div class="col-sm-2">' .$proyectos .'</div>
  <div class="col-sm-1">  </div>
  <div class="control-label col-sm-1" for="admin">Administrador:</div>
  <div class="col-sm-2">
    <select id="id_admin" name="id_admin" class="form-control" style="width:100%;"><option="0"> => Seleccione <= </option>' .$option_usuarios .'</select>
  </div>
  <div class="col-sm-1">  </div>
  <div class="control-label col-sm-1" for="st">Estatus:</div>
  <div class="col-sm-2">
        <select id="st" name="st" class="form-control" style="width:100%;">
          <option value="1" ' .$tp1_selected .'>Activo</option>
          <option value="3" ' .$tp3_selected .'>En espera</option>
          <option value="0">Eliminado</option>
        </select>
  </div>
</div>';


        $cad .= '<div class="form-group" style="clear:both; font-size: 0.8em; height: 2.0em;">
  <div class="control-label col-sm-2" for="de">Descripción del Instrumento:</div>
  <div class="col-sm-5">
    <input type="text" class="form-control" id="de" name="de" placeholder="Coloque el nombre o la descripción del instrumento de la encuesta" style="width:100%;" value="' .$encuesta_de .'"/>
  </div>
  <div class="col-sm-1">  </div>
  <div class="control-label col-sm-1" for="codigo">Código:</div>
  <div class="col-sm-2">
      <input type="text" class="form-control" id="codigo" name="codigo" placeholder="Código del instrumento (alfanúmerico sin espacios)" value="' .$encuesta_codigo .'" style="width:100%;"/>
  </div>
</div>';


        $cad .= '<div class="form-group" style="clear:both; font-size: 0.8em; height: 2.0em;"> 
      <div class="control-label col-sm-1" for="jquery"> &nbsp; Tipo de Instrumento:</div>
  <div class="col-sm-2">
    <select id="id_instrumento_tp" name="id_instrumento_tp" placeholder="Seleccione" class="form-control" style="width:100%;">' .$option_instr_tp .'</select>
  </div>
  <div class="col-sm-1">  </div>
</div>';


        $cad .= '<div style="clear:both;"><br /><hr /></div>';

        $cad .= Seccion::titulo('Presentación');

      
        $cad .= '<div class="form-group" style="clear:both; font-size: 0.8em; height: 2.0em;"> 
  <div class="control-label col-sm-1" for="jquery">Tema JQuery:</div>
  <div class="col-sm-2">
     <select id="id_jqueryui" name="id_jqueryui" placeholder="Seleccione" class="form-control" style="width:100%;">' .$option_jquery .'</select>
  </div>
  <div class="col-sm-1">  </div>
  <div class="control-label col-sm-1" for="bootstrap">Tema Bootstrap:</div>
  <div class="col-sm-2">
    <select id="id_bootstrap" name="id_bootstrap" placeholder="Seleccione" class="form-control" style="width:100%;">' .$option_bootstrap .'</select>
  </div>
  <div class="col-sm-1">  </div>
  <div class="control-label col-sm-1" for="font">Tipo de Letra:</div>
  <div class="col-sm-2">
    <select id="id_font" name="id_font" placeholder="Seleccione" class="form-control" style="width:100%;">' .$option_font .'</select>
  </div>
</div>';



        $cad .= '<div style="clear:both;"><br /><hr /></div>';



        $cad .= Seccion::titulo('Comportamiento del instrumento');
 

        $cad .= '<div class="form-group" style="clear:both; font-size: 0.8em; height: 2.0em;">
  <div class="control-label col-sm-1" for="desplegar">Desplegar:</div>
  <div class="col-sm-2">
      <select id="desplegar" name="desplegar" class="form-control" style="width:100%;">' .$option_desplegar .'</select>
    </div>
  <div class="col-sm-1">  </div>
  <div class="control-label col-sm-1" for="siguiente">Siguiente:</div>
  <div class="col-sm-2">
      <select id="siguiente" name="siguiente" class="form-control" style="width:100%;">' .$option_siguiente .'</select>
    </div>
  <div class="col-sm-1">  </div>
  <div class="control-label col-sm-1" for="back">Retroceder:</div>
  <div class="col-sm-2">
    <select id="back" name="back" class="form-control" style="width:100%;">' .$option_back .'</select>
  </div>
  <div class="col-sm-1">  </div>
</div>';



        $cad .= '<div class="form-group" style="clear:both; font-size: 0.8em; height: 2.0em;">
  <div class="control-label col-sm-1" title="Habilitar llamar luego">LLamar Luego:</div>
  <div class="col-sm-2">' .$option_llamar_luego .'</div>
  <div class="col-sm-1">  </div>
  <div class="control-label col-sm-1"> Uso de dominios:</div>
  <div class="col-sm-2">' .$option_dominios .'</div>
  <div class="col-sm-1">  </div>
  <div class="control-label col-sm-1"> Uso de cuotas:</div>
  <div class="col-sm-2">' .$option_cuotas .'</div>
  <div class="col-sm-1">  </div>
</div>';

        $cad .= '<div class="form-group" style="clear:both; font-size: 0.8em; height: 2.0em;">
  <div class="control-label col-sm-1" for="entrada_inicial">Entrada Inicial:</div>
  <div class="col-sm-2">
    <input placeholder="Primera pregunta" class="form-control" style="width:100%;" type="text" id="entrada_inicial" name="entrada_inicial" value="' .$entrada_inicial .'"/>
  </div>
  <div class="col-sm-1">  </div>                                                                  
</div>';



        if( false ){
            $cad .= '<div style="clear:both;"><br /><hr /></div>';
            $cad .= Seccion::titulo('Origen de los datos, para uso interno del sistema');
    
    
            $cad .= '<div class="form-group" style="clear:both; font-size: 0.8em; height: 2.0em;">
  <div class="control-label col-sm-1" for="columna_fecha_ref">Fecha de REF:</div>
  <div class="col-sm-2">
    <select id="columna_fecha_ref" name="columna_fecha_ref" placeholder="Seleccione" class="form-control" style="width:100%;">' .$option_fecha_ref .'</select>
  </div>
  <div class="col-sm-1">  </div>
  <div class="control-label col-sm-1" for="columna_movil">Telefono:</div>
  <div class="col-sm-2">
      <select id="columna_movil" name="columna_movil" placeholder="Seleccione" class="form-control" style="width:100%;">' .$option_movil .'</select>
  </div>
  <div class="col-sm-1">  </div>
  <div class="control-label col-sm-1" for="columna_nombre">Nombre del Prospecto:</div>
  <div class="col-sm-2">
    <select id="columna_nombre" name="columna_nombre" placeholder="Seleccione" class="form-control" style="width:100%;">' .$option_nombre .'</select>
  </div>
  <div class="col-sm-1">  </div>
</div>';
    
     
    
            $cad .= '<div class="form-group" style="clear:both; font-size: 0.8em; height: 2.0em;">
  <div class="control-label col-sm-1" for="columna_agente">Agente:</div>
  <div class="col-sm-2">
    <select id="columna_agente" name="columna_agente" placeholder="Seleccione" class="form-control" style="width:100%;">' .$option_agente .'</select>
  </div>
  <div class="col-sm-1">  </div>
  <div class="control-label col-sm-1" for="cdominio">Columna de dominio:</div>
  <div class="col-sm-2">
    <select id="columna_dominio" name="columna_dominio" placeholder="Seleccione" class="form-control" style="width:100%;">
      ' .$option_cdominio .'
    </select>
  </div>
  <div class="col-sm-1">  </div>
</div>';
    
    
        }

        $cad .= '<div style="clear:both;"><br /><hr /></div>';
        $cad .= Seccion::titulo('Carga de data');
        $cad .= '<div id="div_carga_data" style="clear:both;">';
        $i9 = 0;
        if( $x3Proyecto != '' && (int)$encuesta_id != 0 ){

            $sql = "select * from " .$x3Proyecto .".cabecera where id_instrumento ='" .$encuesta_id ."' and tp in ('0') order by orden asc;";
            $cabeceras0 = Aux::findBySql($sql)->all();    
            foreach( $cabeceras0 as $cabecera ){
                $i9++;
                $cad .= $this->pCargaData2( $i9, $col, $cabecera->id, $cabecera->de, $cabecera->columna, $cabecera->mostrar, $cabecera->editar, $cabecera->st );
            }
        }else{
            $i9++;
            $cad .= $this->pCargaData2( $i9, $col, '0', 'Prospecto', 'c001', '1', '0' );
            $i9++;
            $cad .= $this->pCargaData2( $i9, $col, '0', 'Teléfono', 'c002', '1', '0' );
            $i9++;
            $cad .= $this->pCargaData2( $i9, $col, '0', 'Fecha REF', 'c003', '0', '0' );
            $i9++;
            $cad .= $this->pCargaData2( $i9, $col, '0', 'Agente', 'c004', '0', '0' );
            $i9++;
            $cad .= $this->pCargaData2( $i9, $col, '0', 'Dominio', 'c005', '0', '0' );
            $i9--;
        }
        $cad .= "<script> nCabecera = '" .$i9 ."'; </script>";
        $cad .= '</div>';



        if( $x3Proyecto != '' && (int)$encuesta_id != 0 ){
            $cad .= '<div style="clear:both;">';
            $cabeceras0 = Aux::findBySql("select * from " .$x3Proyecto .".cabecera where id_instrumento ='" .$encuesta_id ."' and tp in ('1') and st='1' order by orden asc;")->all();
            foreach( $cabeceras0 as $cabecera )
                $cad .= $this->pCargaData3( $cabecera->de );
            $cad .= '</div>';    
        }
        
        
        

        
        $cad .= '<div class="" style="clear: both;">
      <button type="button" class="btn btn-primary" style="width:150px;" onClick="nCabecera=addcolumna(nCabecera);" > Agregar Columna </button>
      </div>';
        $cad .= '<div style="clear:both;"><br /><hr /></div>';

        
        $cad .= Seccion::titulo('Reportes permitidos'); 
        $cad .= $reportes;
        $cad .= '<div style="clear:both;"><br /></div>';

        
        $cad .= Seccion::titulo('Preguntas o Textos');
        $cad .= '<ul id="sortable" class="sortable">' .$cargado .'</ul>';


        $cad .= '<div cl555ass="btn-group" style="clear: both;">
      <hr/>
      <button type="button" class="btn btn-primary" style="width:150px;" onClick="jid=entrada(\'sortable\');" > Agregar Entrada </button>
      <!-- button type="button" class="btn btn-primary" style="width:150px;" onClick="edit1=x3sortable();" > Ordenar / Editar </button -->
      <button type="button" class="btn btn-danger" style="width:150px;" onClick="x3funSubmit(\'enc1\');" > Salvar </button>
      </div>';



        $cad .= '</form>';

        $cad .= '<script> $(document).ready(function(){$(\':input[type="submit"]\').prop(\'disabled\', true);}); </script>';


        
        return $this->render('@views/crm/txt',array('txt' => $cad));
    } // eof #######################################################

   


    /** 
     * Opciones para decidir si se usa "Llamar Luego"
     */
    public function optionLlamarLuego( $default = null){
        $cad = '';
        if( $default == '1' )
            $cad .= '<input type="radio" id="llamar_luego0" name="llamar_luego" value="0"/> No &nbsp; <input type="radio" id="llamar_luego1" name="llamar_luego" value="1" checked="checked"/> Si';
        else
            $cad .= '<input type="radio" id="llamar_luego0" name="llamar_luego" value="0" checked="checked"/> No &nbsp; <input type="radio" id="llamar_luego1" name="llamar_luego" value="1"/> Si';
        return $cad;
    } // eof 

    
    
    /**
     * Opciones para decidir si se usan "Cuotas"
     */
    public function optionCuotas( $default = null){
        $cad = '';
        if( $default == '1' )
            $cad .= '<input type="radio" id="cuotas0" name="cuotas" value="0"/> No &nbsp; <input type="radio" id="cuotas1" name="cuotas" value="1" checked="checked"/> Si';
        else
            $cad .= '<input type="radio" id="cuotas0" name="cuotas" value="0" checked="checked"/> No &nbsp; <input type="radio" id="cuotas1" name="cuotas" value="1"/> Si';
        return $cad;
    } // eof 

    
    
    /**
     * Opciones para decidir si se usan "Dominios"
     */
    public function optionDominios( $default = null ){
        $cad = '';
        if( $default == '1' )
            $cad .= '<input type="radio" id="dominios0" name="dominios" value="0"/> No &nbsp; <input type="radio" id="dominios1" name="dominios" value="1" checked="checked"/> Si';
        else
            $cad .= '<input type="radio" id="dominios0" name="dominios" value="0" checked="checked"/> No &nbsp; <input type="radio" id="dominios1" name="dominios" value="1"/> Si';
        return $cad;
    } // eof 

    

    /**
     * Opciones para decidir que tema de JQuery se usará
     */
    public function optionJquery( $default = null ){
        $cad = '';
        $objs = Aux::findBySql("select * from a.jqueryui where st='1' order by de ASC")->all();
        foreach( $objs as $obj )
            if( $obj->id == $default && $default != null )
                $cad .= '<option value="' .$obj->id .'" selected="selected"> &nbsp; ' .$obj->de .'</option>';
            else
                $cad .= '<option value="' .$obj->id .'"> &nbsp; ' .$obj->de .'</option>';
        return $cad;
    } // eof 



    /**
     * Opciones para decidir que tipo de instrumento es
     */
    public function optionInstrTp( $default = null ){
        $cad = '';
        $objs = Aux::findBySql("select * from a.instrumento_tp where st='1' order by de ASC")->all();
        foreach( $objs as $obj )
            if( $obj->id == $default && $default != null )
                $cad .= '<option value="' .$obj->id .'" selected="selected">' .$obj->de .'</option>';
            else
                $cad .= '<option value="' .$obj->id .'">' .$obj->de .'</option>';
        return $cad;
    } // eof 

    
        
    /**
     * Opciones para decidir que Fuente de Letras se usará
     */
    public function optionFont( $default = null ){
        $cad = '';
        $objs = Aux::findBySql("select * from a.font where st='1' order by de ASC")->all();
        foreach( $objs as $obj )
            if( $obj->id == $default && $default != null )
                $cad .= '<option value="' .$obj->id .'" selected="selected">' .$obj->de .'</option>';
            else
                $cad .= '<option value="' .$obj->id .'">' .$obj->de .'</option>';
        return $cad;
    } // eof 

   
} // class
