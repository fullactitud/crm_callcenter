<?php
namespace app\controllers\soporte;

use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use app\models\xDbManager;

use app\components\crm\Mensaje;
use app\components\crm\JSon;
use app\components\crm\JQGrid;
use app\components\crm\Menu;
use app\components\crm\Request;
use app\components\crm\Listado;
use app\components\crm\Reporte;
use app\components\crm\Ayuda;
use app\models\Aux;


/**
 * Controler para manejar Reportes
 */
class ReporteController extends Controller{

    /**
     * @inheritdoc
     */
    public function behaviors(){
        return [
            'access' => [
                'class' => AccessControl::className(),

                'rules' => [                
                    [
                        'allow' => false, // Do not have access
                        'roles'=>['?'], // Guests '?'
                    ],
                    [
                        'allow' => true,
                        'actions' => ['login', 'signup', 'error'],
                        'roles' => ['?'],
                    ],

                    [
                        'allow' => true,
                        'roles' => ['admin'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['index','form1','in','out','logout'],
                        'roles' => ['tlo'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['index','in','out','logout'],
                        'roles' => ['@'],
                    ],

                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                    'logout' => ['get'],
                ],
            ],
        ];
    } // eof #######################################################

    
    /**
     * @inheritdoc
     */
    public function actions(){
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    } // eof #######################################################




    /**
     * Procesos que se ejecutan antes que el controller carge
     */    
    function init(){
        if( Yii::$app->user->isGuest ){
            Yii::$app->getResponse()->redirect(array('/df/logout',302));
            Yii::$app->end();
        }
        parent::init();
        if( isset($_REQUEST['_lang']) ){
            Yii::$app->language = $_REQUEST['_lang'];
            Yii::$app->session['_lang'] = Yii::$app->language;
        }else if( isset(Yii::$app->session['_lang']) ){
            Yii::$app->language = Yii::$app->session['_lang'];
        }else{
            Yii::$app->language = 'es_ve';
            Yii::$app->session['_lang'] = Yii::$app->language;
        }
    } // eof #######################################################
            



    /**
     * Generar reporte generico
     */
    public function actionReport(){

        $cod_proyecto = Request::rq('x3proy');
        $id_instrumento = Request::rq('3xint');
        $id = Request::rq('x3id');
        
        if( $cod_proyecto != '' && !is_null($id) ){
            $sql = "select * from a.reporte where st ='1' and id='" .(int)$id ."' limit 1;";
            $obj = Aux::findBySql($sql)->all();
            if( count($obj) > 0 ){
                $cad = Reporte::reportGeneric($cod_proyecto, $id);            
                return $this->render('@views/crm/txt',array('txt'=>$cad));                
            }else{
                $sql = "select ri.proyecto, i.codigo 
from a.reporte_instrumento ri 
inner join a.reporte r on r.id=ri.id_reporte 
inner join " .$cod_proyecto .".instrumento i on ri.id_instrumento=i.id 
where r.st ='1' and ri.st='1' and ri.proyecto='" .$cod_proyecto ."' and r.codigo='" .$id ."' limit 1;";
                $obj = Aux::findBySql($sql)->all();
                $this->redirect( Yii::$app->params['baseUrl'] .'index.php?r=proyecto/' .$obj[0]->proyecto .'/' .$obj[0]->codigo .'/' .$id .'&cod_proyecto=' .$cod_proyecto .'&id_instrumento='.$id_instrumento );
            }
        }else{ 
            $this->redirect( Yii::$app->params['baseUrl'] .'index.php');

        }
    } // eof #######################################################
    


    public function actionDf_deprecate(){
      
        $proyectos = xProyecto::findAll(['st' => 1]);
        if( count($proyectos) > 1 ){ // sia accesa a mas de un proyecto, muestre menu
            return $this->render('proyectos',array(
                'proyectos'=>$proyectos,
                'menu'=>ctrlFunction::menu(),
            ));
        }else{ // si solo accesa a un proyecto, redireccione al proyecto
            $this->redirect( Yii::$app->params['baseUrl'] .'index.php?r=' .$proyectos[0]->codigo .'/df');
        }
    } // eof #######################################################



    /**
     * Genera un código valido
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
    }


    /**
     * Agrega un Reporte
     */   
    public function actionAdd(){
        $vctT[0] = '';
        $vctT[1] = 'cuota';
        $vctT[2] = 'instrumento';
        $vctT[3] = 'llamada';
        $vctT[4] = 'prospecto';
        $vctT[5] = 'tipificacion';
        $de = trim(Request::rq('de')); // Nombre del reporte
        if( $de == '' ){
            $this->redirect( Yii::$app->params['baseUrl'] .'index.php?r=soporte/reporte/df&', 302 );
        }else{ 
            $x3id = (int)Request::rq('x3id');
            $codigo = Request::rq('codigo');
            $query = Request::rq('query');
            $rol = Request::rq('rol');
            $fechas_iguales = (int)Request::rq('fechas_iguales');
            $filtrar_proyecto = (int)Request::rq('filtrar_proyecto');
            $filtrar_desde = (int)Request::rq('filtrar_desde');
            $filtrar_hasta = (int)Request::rq('filtrar_hasta');
            $filtrar_tlo = (int)Request::rq('filtrar_tlo');
            $filtrar_instrumento = (int)Request::rq('filtrar_instrumento');
            $filtrar_data_tp = (int)Request::rq('filtrar_data_tp');
            $filtrar_fecha_tp = (int)Request::rq('filtrar_fecha_tp');
            $filtrar_dominio = (int)Request::rq('filtrar_dominio');
            $filtrar_agente = (int)Request::rq('filtrar_agente');
            $st = 1; // (int)Request::rq('st');
            // 	imagen 
            $objs = Aux::findBySql("select * from a.reporte where id = '" .$x3id ."' and tp='0';")->all();
            if( count($objs) > 0 ){ // ACTUALIZAR
                $obj = $objs[0];
                $sql = "update a.reporte set ";
                $sql .= " codigo = '" .$codigo ."', ";
                $sql .= " de = '" .$de ."', ";
                $sql .= " query = '" .$query ."', ";
                $sql .= " rol = '" .$rol ."', ";
                $sql .= " fechas_iguales = '" .$fechas_iguales ."', ";
                $sql .= " filtrar_proyecto = '" .$filtrar_proyecto ."', ";
                $sql .= " filtrar_desde = '" .$filtrar_desde ."', ";
                $sql .= " filtrar_hasta = '" .$filtrar_hasta ."', ";
                $sql .= " filtrar_tlo = '" .$filtrar_tlo ."', ";
                $sql .= " filtrar_instrumento = '" .$filtrar_instrumento ."', ";
                $sql .= " filtrar_data_tp = '" .$filtrar_data_tp ."', ";
                $sql .= " filtrar_fecha_tp = '" .$filtrar_fecha_tp ."', ";
                $sql .= " filtrar_dominio = '" .$filtrar_dominio ."', ";
                $sql .= " filtrar_agente = '" .$filtrar_agente ."', ";
                $sql .= " st = '" .$st ."' ";
                $sql .= " where tp='0' and id='" .$x3id ."'";
                Aux::findBySql($sql)->one();

                // actualizar campos
                $i6 = 1;
                $de_ = Request::rq('de_'.$i6);
                while( $de_ != '' ){
                    if( (int)Request::rq('delCol_'.$i6) != 1 ){
                        if( (int)Request::rq('id_'.$i6) != 0 ){ // update
                            $sql = "update a.reporte_campo set de= '" .$de_ ."', tabla= '" .$vctT[Request::rq('tabla_'.$i6)] ."', campo = '" .Request::rq('xcampo_'.$i6) ."', orden= '" .$i6 ."', st = '" .(int)Request::rq('st_'.$i6) ."' where id='" .(int)Request::rq('id_'.$i6) ."';";
                            Aux::findBySql($sql)->one();
                        }else{ // insert
                            $sql = "insert into a.reporte_campo (id_reporte, de, tabla, campo, orden, st) values ('" .$obj->id ."', '" .$de_ ."', '" .$vctT[Request::rq('tabla_'.$i6)] ."', '" .Request::rq('xcampo_'.$i6) ."', '" .$i6 ."', '" .(int)Request::rq('st_'.$i6) ."')";
                            Aux::findBySql($sql)->one();
                        }
                    }else{
                        if( (int)Request::rq('id_'.$i6) != 0 ){ // delete
                            $sql = "delete from a.reporte_campo where id='" .(int)Request::rq('id_'.$i6) ."';";
                            Aux::findBySql($sql)->one();
                        }
                    }
                    $i6++;
                    $de_ = Request::rq('de_'.$i6);                    
                }
                $this->redirect(Yii::$app->params['baseUrl'] .'index.php?r=soporte/reporte/df&x3id=' .$x3id, 302);   
            }else{ // INSERT
                
                // garantizar codigo unico
                if( $codigo == '' )
                    $codigo = substr($this->codigo($de),0,8);
                $sql = "select id from a.reporte where codigo='" .$codigo ."';";
                $objCod = Aux::findBySql($sql)->all();
                while( count($objCod) > 0 ){
                    $codigo .= rand(0, 9);
                    $sql = "select id from a.reporte where codigo='" .$codigo ."';";
                    $objCod = Aux::findBySql($sql)->all();
                }
                
                $sql = "insert into a.reporte (codigo, de, query, rol, fechas_iguales, filtrar_proyecto, filtrar_desde, filtrar_hasta, filtrar_tlo, filtrar_instrumento,filtrar_data_tp, filtrar_fecha_tp, filtrar_dominio, filtrar_agente, st, tp) values ('" .$codigo ."', '" .$de ."', '" .$query ."', '" .$rol ."', '" .$fechas_iguales ."', '" .$filtrar_proyecto ."', '" .$filtrar_desde ."', '" .$filtrar_hasta ."', '" .$filtrar_tlo ."', '" .$filtrar_instrumento ."', '" .$filtrar_data_tp ."', '" .$filtrar_fecha_tp ."', '" .$filtrar_dominio ."', '" .$filtrar_agente ."', '" .$st ."', '0')";
                Aux::findBySql($sql)->one();
                $sql = "select id from a.reporte where codigo = '" .$codigo ."' order by id desc;";
                $obj = Aux::findBySql($sql)->one();

                // insertar campos      
                $i6 = 1;
                $de_ = Request::rq('de_'.$i6);
                
                while( $de_ != '' ){
                    if( (int)Request::rq('delCol_'.$i6) != 1 ){
                        $sql = "insert into a.reporte_campo (id_reporte, de, tabla, campo, orden, st) values ('" .$obj->id ."', '" .$de_ ."', '" .$vctT[Request::rq('tabla_'.$i6)] ."', '" .Request::rq('xcampo_'.$i6) ."', '" .$i6 ."', '" .(int)Request::rq('st_'.$i6) ."')";
                        Aux::findBySql($sql)->one();
                    }
                    $i6++;
                    $de_ = Request::rq('de_'.$i6);
                }
                $this->redirect(Yii::$app->params['baseUrl'] .'index.php?r=soporte/reporte/df&x3id=' .$obj->id, 302);
            }
        }
    } // eof #######################################################

    

    /**
     * Elimina un reporte
     */
    public function actionEliminar1(){
        $x3id = Request::rq('x3id');

        
        // eliminar campos
        
        $sql = "delete from a.reporte where id= '" .$x3id ."' and tp='0';";
        Aux::findBySql($sql)->one();        
        $txt = 'Reporte eliminado.';
        $this->redirect(Yii::$app->params['baseUrl'] .'index.php?r=soporte/reporte/df&x3txt=' .$txt, 302);
    }


    /**
     * Acción por defecto de Reportes
     */    
    public function actionDf(){
        $x3Id = (int)Request::rq('x3id');
        $x3Proyecto = (int)Request::rq('x3proyecto');
        $x3Proyecto = Request::rq('x3proy');
        
        $rpt_id	= 0;
        $rpt_codigo = '';
        $rpt_de = '';
        $rpt_query = '';
        $rpt_imagen = '';
        $rpt_rol = '';
        
        $rpt_fechas_iguales = 0;
        $rpt_filtrar_proyecto = 0;
        $rpt_filtrar_desde = 0;
        $rpt_filtrar_hasta = 0;
        $rpt_filtrar_tlo = 0;
        $rpt_filtrar_instrumento = 0;
        $rpt_filtrar_data_tp = 0;
        $rpt_filtrar_fecha_tp = 0;
        $rpt_filtrar_dominio = 0;
        $rpt_filtrar_agente = 0;
        
        $selectRol = "select distinct name from auth_item where type = '1' order by name asc;";
        $roles = Aux::findBySql($selectRol)->all();
        
        if( !is_null($x3Proyecto) && $x3Id != 0 ){
            $select1 = "select * from a.reporte where id='" .$x3Id ."' and st != '0';";
            $reportes = Aux::findBySql($select1)->all();

            if( count($reportes) > 0 ){
                $reporte = $reportes[0];
                $select2 = "select * from a.reporte_campo where id_reporte='" .$x3Id ."' and st != '0' order by orden ASC, id ASC;";
                $campos = Aux::findBySql($select2)->all();
                
                $rpt_id	= (int)$reporte->id;
                $rpt_codigo = $reporte->codigo;
                $rpt_de = $reporte->de;
                $rpt_query = $reporte->query;
                $rpt_imagen = $reporte->imagen;
                $rpt_rol = $reporte->rol;
                
                $rpt_fechas_iguales = (int)$reporte->fechas_iguales;
                $rpt_filtrar_proyecto = (int)$reporte->filtrar_proyecto;
                $rpt_filtrar_desde = (int)$reporte->filtrar_desde;
                $rpt_filtrar_hasta = (int)$reporte->filtrar_hasta;
                $rpt_filtrar_tlo = (int)$reporte->filtrar_tlo;
                $rpt_filtrar_instrumento = (int)$reporte->filtrar_instrumento;
                $rpt_filtrar_data_tp = (int)$reporte->filtrar_data_tp;
                $rpt_filtrar_fecha_tp = (int)$reporte->filtrar_fecha_tp;
                $rpt_filtrar_dominio = (int)$reporte->filtrar_dominio;
                $rpt_filtrar_agente = (int)$reporte->filtrar_agente;
                
                foreach( $campos as $campo ){
                    
                }
            }
        }

        $cad = '';
        $cad .= '<script type="text/javascript" src="js/reporte1.js"></script>';
        
        $cad .= '<div class="titulos"> Configuración del Reporte </div>';
        $cad .= Mensaje::mostrar();
        $cad .= Ayuda::toHtml('reporte');

        $cad .= '<div style="position:relative; font-size: 0.85em;"/>';

       
        $cad .= '<form class="form-horizontal" method="POST" action="' .Yii::$app->params['baseUrl'] .'index.php?r=soporte/reporte/add" name="formReporte" id="formReporte">';
        $cad .= '<input type="hidden" name="x3id" id="x3id" value="' .$rpt_id .'"/>';
        

        $cad .= '<div style="clear:both; line-height: 2.0em; ">';
        
        $cad .= '<div class="col-sm-1">';
        $cad .= 'Descripción: ';
        $cad .= '</div>';
        $cad .= '<div class="col-sm-3">';
        $cad .= '<input type="text" name="de" id="de" value="' .$rpt_de .'" style="width:100%;"/>';
        $cad .= '</div>';
        $cad .= '<div class="col-sm-1">  </div>';        
        

	   
        
        
        $cad .= '<div class="col-sm-1">';
        $cad .= ' Código: ';
        $cad .= '</div>';
        $cad .= '<div class="col-sm-2">';
        $cad .= '<input type="text" name="codigo" id="codigo" value="' .$rpt_codigo .'" style="width:100%;"/>';
        $cad .= '</div>';
        $cad .= '<div class="col-sm-1">  </div>';
        
        
        $cad .= '<div class="col-sm-1"> &nbsp; Rol: </div>';
        $cad .= '<div class="col-sm-2">';
        $cad .= '<select class="form-control" name="rol" id="rol" style="width:100%;">';
        foreach( $roles as $rol )
            if( $rol->name == $rpt_rol )
                $cad .= '<option value="' .$rol->name .'" selected="selected"> &nbsp; ' .ucfirst($rol->name) .'</option>';
            else
                $cad .= '<option value="' .$rol->name .'"> &nbsp; ' .ucfirst($rol->name) .'</option>';
        $cad .= '</select>';
        $cad .= '</div>';
       
        
        $cad .= '</div><div style="clear:both; line-height: 3.0em; ">';        
        
        $cad .= '<div class="col-sm-1">';
        $cad .= ' Consulta SQL: ';
        $cad .= '</div>';
        $cad .= '<div class="col-sm-11">';
        $cad .= '<textarea name="query" id="query" style="width:100%;"/>' .$rpt_query .'</textarea>';
        $cad .= '</div>';

        $cad .= '</div><div class="subtitulos" style="clear:both; line-height: 1.5em; width:100%; background-color:#cccccc;"> &nbsp; Filtrar por </div>';
        
        $cad .= '<div style="clear:both;  line-height: 2.0em; ">';
                
        $cad .= '<div class="col-sm-2">';
        if( $rpt_filtrar_proyecto == 0 )
            $cad .= '<input type="checkbox" name="filtrar_proyecto" id="filtrar_proyecto" value="1"/> ';
        else
            $cad .= '<input type="checkbox" name="filtrar_proyecto" id="filtrar_proyecto" value="1" checked="checked"/> ';
        $cad .= ' Proyecto</div><div class="col-sm-1">  </div>';

        $cad .= '<div class="col-sm-2">';
        if( $rpt_filtrar_tlo == 0 )
            $cad .= '<input type="checkbox" name="filtrar_tlo" id="filtrar_tlo" value="1"/> ';
        else
            $cad .= '<input type="checkbox" name="filtrar_tlo" id="filtrar_tlo" value="1" checked="checked"/> ';
        $cad .= ' TLO</div><div class="col-sm-1">  </div>';

        $cad .= '<div class="col-sm-2">';
        if( $rpt_filtrar_instrumento == 0 )
            $cad .= '<input type="checkbox" name="filtrar_instrumento" id="filtrar_instrumento" value="1"/> ';
        else
            $cad .= '<input type="checkbox" name="filtrar_instrumento" id="filtrar_instrumento" value="1" checked="checked"/> ';
        $cad .= ' Instrumento</div><div class="col-sm-1">  </div>';
        
        $cad .= '<div class="col-sm-2">';    
        if( $rpt_filtrar_dominio == 0 )
            $cad .= '<input type="checkbox" name="filtrar_dominio" id="filtrar_dominio" value="1"/> ';
        else
            $cad .= '<input type="checkbox" name="filtrar_dominio" id="filtrar_dominio" value="1" checked="checked"/> ';
        $cad .= ' Dominio</div><div class="col-sm-1">  </div>';

        $cad .= '</div><div style="clear:both; line-height: 2.0em; ">';

        $cad .= '<div class="col-sm-2">';
        if( $rpt_filtrar_desde == 0 )
            $cad .= '<input type="checkbox" name="filtrar_desde" id="filtrar_desde" value="1"/> ';
        else
            $cad .= '<input type="checkbox" name="filtrar_desde" id="filtrar_desde" value="1" checked="checked"/> ';
        $cad .= ' Fecha inicial</div><div class="col-sm-1">  </div>';

        $cad .= '<div class="col-sm-2">';
        if( $rpt_filtrar_hasta == 0 )
            $cad .= '<input type="checkbox" name="filtrar_hasta" id="filtrar_hasta" value="1"/> ';
        else
            $cad .= '<input type="checkbox" name="filtrar_hasta" id="filtrar_hasta" value="1" checked="checked"/> ';
        $cad .= ' Fecha final</div><div class="col-sm-1">  </div>';
        
        $cad .= '<div class="col-sm-2">';
        if( $rpt_fechas_iguales == 0 )
            $cad .= '<input type="checkbox" name="fechas_iguales" id="fechas_iguales" value="1"/> ';
        else
            $cad .= '<input type="checkbox" name="fechas_iguales" id="fechas_iguales" value="1" checked="checked"/> ';
        $cad .= ' Única fecha</div><div class="col-sm-1">  </div>';

        $cad .= '<div class="col-sm-3">';
        if( $rpt_filtrar_fecha_tp == 0 )
            $cad .= '<input type="checkbox" name="filtrar_fecha_tp" id="filtrar_fecha_tp" value="1"/> ';
        else
            $cad .= '<input type="checkbox" name="filtrar_fecha_tp" id="filtrar_fecha_tp" value="1" checked="checked"/> ';
        $cad .= ' Tipo de fecha</div>';
        
        $cad .= '</div><div style="clear:both; line-height: 2.0em; ">';
  
        $cad .= '<div class="col-sm-2">';
        if( $rpt_filtrar_data_tp == 0 )
            $cad .= '<input type="checkbox" name="filtrar_data_tp" id="filtrar_data_tp" value="1"/> ';
        else
            $cad .= '<input type="checkbox" name="filtrar_data_tp" id="filtrar_data_tp" value="1" checked="checked"/> ';
        $cad .= ' Tipo de data</div><div class="col-sm-1">  </div>';                

        $cad .= '<div class="col-sm-2">';     
        if( $rpt_filtrar_agente == 0 )
            $cad .= '<input type="checkbox" name="filtrar_agente" id="filtrar_agente" value="1"/> ';
        else
            $cad .= '<input type="checkbox" name="filtrar_agente" id="filtrar_agente" value="1" checked="checked"/> ';
        $cad .= ' Agente</div><div class="col-sm-1">  </div>';
        
        $cad .= '</div>';
        
        $cad .= '<div style="clear:both;"><br /><hr /></div>
<div style="background-color:#cccccc; line-height: 2.0em;">
  <div class="subtitulos" title="Campos"> &nbsp; Campos</div>
</div>
<br />
<div id="div_campo_reporte" style="clear: both;height: 2.0em;">';


        $vctT[0] = ' => Seleccione <= ';
        $vctT[1] = 'Cuota';
        $vctT[2] = 'Instrumento';
        $vctT[3] = 'Llamada';
        $vctT[4] = 'Prospecto';
        $vctT[5] = 'Tipificacion';



        $auxCuotaK[0] = 'fecha_encuesta';
        $auxCuotaK[1] = 'fecha_ref';
        $auxCuotaK[2] = 'cuota';
        $auxCuotaK[3] = 'reg';
        $auxCuotaK[4] = 'conteo';
        $auxCuotaK[5] = 'cod_dominio';

        $auxCuotaV[0] = 'Fecha de la encuesta';
        $auxCuotaV[1] = 'Fecha de referencia';
        $auxCuotaV[2] = 'Cuota';
        $auxCuotaV[3] = 'Fecha de registro';
        $auxCuotaV[4] = 'Conteo';
        $auxCuotaV[5] = 'Codigo del dominio';


        // PROSPECTO
        $auxProspectoK[1] = 'id';
        $auxProspectoK[2] = 'id_instrumento';
        $auxProspectoK[3] = 'id_data';
        $auxProspectoK[4] = 'barrida';
        $auxProspectoK[5] = 'llamar';
        $auxProspectoK[6] = 'reg';
        $auxProspectoK[7] = 'up';
        $auxProspectoK[8] = 'st';
        $auxProspectoK[9] = 'tlo';
        $auxProspectoK[10] = 'inicio';
        $auxProspectoK[11] = 'fin';
        $auxProspectoK[12] = 'id_tipificacion';
        $auxProspectoK[13] = 'encuesta';

        $auxProspectoV[1] = 'Número ID';
        $auxProspectoV[2] = 'Número ID del instrumento';
        $auxProspectoV[3] = 'id_data';
        $auxProspectoV[4] = 'Barrida';
        $auxProspectoV[5] = 'llamar';
        $auxProspectoV[6] = 'Fecha de registro';
        $auxProspectoV[7] = 'Fecha de última actualización';
        $auxProspectoV[8] = 'Estatus';
        $auxProspectoV[9] = 'ID del Teleoperador';
        $auxProspectoV[10] = 'Fecha de inicio';
        $auxProspectoV[11] = 'Fecha de fin';
        $auxProspectoV[12] = 'ID de tipificacion';
        $auxProspectoV[13] = 'Número de encuesta';


        $auxProspectoK[101] = 'c001'; $auxProspectoK[102] = 'c002'; $auxProspectoK[103] = 'c003';
        $auxProspectoK[104] = 'c004'; $auxProspectoK[105] = 'c005'; $auxProspectoK[106] = 'c006';
        $auxProspectoK[107] = 'c007'; $auxProspectoK[108] = 'c008'; $auxProspectoK[109] = 'c009';
        $auxProspectoK[110] = 'c010'; $auxProspectoK[111] = 'c011'; $auxProspectoK[112] = 'c012';
        $auxProspectoK[113] = 'c013'; $auxProspectoK[114] = 'c014'; $auxProspectoK[115] = 'c015';
        $auxProspectoK[116] = 'c016'; $auxProspectoK[117] = 'c017'; $auxProspectoK[118] = 'c018';
        $auxProspectoK[119] = 'c019'; $auxProspectoK[120] = 'c020'; $auxProspectoK[121] = 'c021';
        $auxProspectoK[122] = 'c022'; $auxProspectoK[123] = 'c023'; $auxProspectoK[124] = 'c024';
        $auxProspectoK[125] = 'c025'; $auxProspectoK[126] = 'c026'; $auxProspectoK[127] = 'c027';
        $auxProspectoK[128] = 'c028'; $auxProspectoK[129] = 'c029'; $auxProspectoK[130] = 'c030';
        $auxProspectoK[131] = 'c031'; $auxProspectoK[132] = 'c032'; $auxProspectoK[133] = 'c033';
        $auxProspectoK[134] = 'c034'; $auxProspectoK[135] = 'c035'; $auxProspectoK[136] = 'c036';
        $auxProspectoK[137] = 'c037'; $auxProspectoK[138] = 'c038'; $auxProspectoK[139] = 'c039';
        $auxProspectoK[140] = 'c040'; $auxProspectoK[141] = 'c041'; $auxProspectoK[142] = 'c042';
        $auxProspectoK[143] = 'c043'; $auxProspectoK[144] = 'c044'; $auxProspectoK[145] = 'c045';
        $auxProspectoK[146] = 'c046'; $auxProspectoK[147] = 'c047'; $auxProspectoK[148] = 'c048';
        $auxProspectoK[149] = 'c049'; $auxProspectoK[150] = 'c050'; $auxProspectoK[151] = 'c051';
        $auxProspectoK[152] = 'c052'; $auxProspectoK[153] = 'c053'; $auxProspectoK[154] = 'c054';
        $auxProspectoK[155] = 'c055'; $auxProspectoK[156] = 'c056'; $auxProspectoK[157] = 'c057';
        $auxProspectoK[158] = 'c058'; $auxProspectoK[159] = 'c059'; $auxProspectoK[160] = 'c060';
        $auxProspectoK[161] = 'c061'; $auxProspectoK[162] = 'c062'; $auxProspectoK[163] = 'c063';
        $auxProspectoK[164] = 'c064'; $auxProspectoK[165] = 'c065'; $auxProspectoK[166] = 'c066';
        $auxProspectoK[167] = 'c067'; $auxProspectoK[168] = 'c068'; $auxProspectoK[169] = 'c069';
        $auxProspectoK[170] = 'c070'; $auxProspectoK[171] = 'c071'; $auxProspectoK[172] = 'c072';
        $auxProspectoK[173] = 'c073'; $auxProspectoK[174] = 'c074'; $auxProspectoK[175] = 'c075';
        $auxProspectoK[176] = 'c076'; $auxProspectoK[177] = 'c077'; $auxProspectoK[178] = 'c078';
        $auxProspectoK[179] = 'c079'; $auxProspectoK[180] = 'c080'; $auxProspectoK[181] = 'c081';
        $auxProspectoK[182] = 'c082'; $auxProspectoK[183] = 'c083'; $auxProspectoK[184] = 'c084';
        $auxProspectoK[185] = 'c085'; $auxProspectoK[186] = 'c086'; $auxProspectoK[187] = 'c087';
        $auxProspectoK[188] = 'c088'; $auxProspectoK[189] = 'c089'; $auxProspectoK[190] = 'c090';
        $auxProspectoK[191] = 'c091'; $auxProspectoK[192] = 'c092'; $auxProspectoK[193] = 'c093';
        $auxProspectoK[194] = 'c094'; $auxProspectoK[195] = 'c095'; $auxProspectoK[196] = 'c096';
        $auxProspectoK[197] = 'c097'; $auxProspectoK[198] = 'c098'; $auxProspectoK[199] = 'c099';

        $auxProspectoV[101] = 'Columna 1'; $auxProspectoV[102] = 'Columna 2'; $auxProspectoV[103] = 'Columna 3';
        $auxProspectoV[104] = 'Columna 4'; $auxProspectoV[105] = 'Columna 5'; $auxProspectoV[106] = 'Columna 6';
        $auxProspectoV[107] = 'Columna 7'; $auxProspectoV[108] = 'Columna 8'; $auxProspectoV[109] = 'Columna 9';
        $auxProspectoV[110] = 'Columna 10'; $auxProspectoV[111] = 'Columna 11'; $auxProspectoV[112] = 'Columna 12';
        $auxProspectoV[113] = 'Columna 13'; $auxProspectoV[114] = 'Columna 14'; $auxProspectoV[115] = 'Columna 15';
        $auxProspectoV[116] = 'Columna 16'; $auxProspectoV[117] = 'Columna 17'; $auxProspectoV[118] = 'Columna 18';
        $auxProspectoV[119] = 'Columna 19'; $auxProspectoV[120] = 'Columna 20'; $auxProspectoV[121] = 'Columna 21';
        $auxProspectoV[122] = 'Columna 22'; $auxProspectoV[123] = 'Columna 23'; $auxProspectoV[124] = 'Columna 24';
        $auxProspectoV[125] = 'Columna 25'; $auxProspectoV[126] = 'Columna 26'; $auxProspectoV[127] = 'Columna 27';
        $auxProspectoV[128] = 'Columna 28'; $auxProspectoV[129] = 'Columna 29'; $auxProspectoV[130] = 'Columna 30';
        $auxProspectoV[131] = 'Columna 31'; $auxProspectoV[132] = 'Columna 32'; $auxProspectoV[133] = 'Columna 33';
        $auxProspectoV[134] = 'Columna 34'; $auxProspectoV[135] = 'Columna 35'; $auxProspectoV[136] = 'Columna 36';
        $auxProspectoV[137] = 'Columna 37'; $auxProspectoV[138] = 'Columna 38'; $auxProspectoV[139] = 'Columna 39';
        $auxProspectoV[140] = 'Columna 40'; $auxProspectoV[141] = 'Columna 41'; $auxProspectoV[142] = 'Columna 42';
        $auxProspectoV[143] = 'Columna 43'; $auxProspectoV[144] = 'Columna 44'; $auxProspectoV[145] = 'Columna 45';
        $auxProspectoV[146] = 'Columna 46'; $auxProspectoV[147] = 'Columna 47'; $auxProspectoV[148] = 'Columna 48';
        $auxProspectoV[149] = 'Columna 49'; $auxProspectoV[150] = 'Columna 50'; $auxProspectoV[151] = 'Columna 51';
        $auxProspectoV[152] = 'Columna 52'; $auxProspectoV[153] = 'Columna 53'; $auxProspectoV[154] = 'Columna 54';
        $auxProspectoV[155] = 'Columna 55'; $auxProspectoV[156] = 'Columna 56'; $auxProspectoV[157] = 'Columna 57';
        $auxProspectoV[158] = 'Columna 58'; $auxProspectoV[159] = 'Columna 59'; $auxProspectoV[160] = 'Columna 60';
        $auxProspectoV[161] = 'Columna 61'; $auxProspectoV[162] = 'Columna 62'; $auxProspectoV[163] = 'Columna 63';
        $auxProspectoV[164] = 'Columna 64'; $auxProspectoV[165] = 'Columna 65'; $auxProspectoV[166] = 'Columna 66';
        $auxProspectoV[167] = 'Columna 67'; $auxProspectoV[168] = 'Columna 68'; $auxProspectoV[169] = 'Columna 69';
        $auxProspectoV[170] = 'Columna 70'; $auxProspectoV[171] = 'Columna 71'; $auxProspectoV[172] = 'Columna 72';
        $auxProspectoV[173] = 'Columna 73'; $auxProspectoV[174] = 'Columna 74'; $auxProspectoV[175] = 'Columna 75';
        $auxProspectoV[176] = 'Columna 76'; $auxProspectoV[177] = 'Columna 77'; $auxProspectoV[178] = 'Columna 78';
        $auxProspectoV[179] = 'Columna 79'; $auxProspectoV[180] = 'Columna 80'; $auxProspectoV[181] = 'Columna 81';
        $auxProspectoV[182] = 'Columna 82'; $auxProspectoV[183] = 'Columna 83'; $auxProspectoV[184] = 'Columna 84';
        $auxProspectoV[185] = 'Columna 85'; $auxProspectoV[186] = 'Columna 86'; $auxProspectoV[187] = 'Columna 87';
        $auxProspectoV[188] = 'Columna 88'; $auxProspectoV[189] = 'Columna 89'; $auxProspectoV[190] = 'Columna 90';
        $auxProspectoV[191] = 'Columna 91'; $auxProspectoV[192] = 'Columna 92'; $auxProspectoV[193] = 'Columna 93';
        $auxProspectoV[194] = 'Columna 94'; $auxProspectoV[195] = 'Columna 95'; $auxProspectoV[196] = 'Columna 96';
        $auxProspectoV[197] = 'Columna 97'; $auxProspectoV[198] = 'Columna 98'; $auxProspectoV[199] = 'Columna 99';


        // LLAMADA 
        $auxLlamadaK[0] = 'id';
        $auxLlamadaK[1] = 'id_tipificacion';
        $auxLlamadaK[2] = 'barrida';
        $auxLlamadaK[3] = 'observacion';
        $auxLlamadaK[4] = 'reg';
        $auxLlamadaK[5] = 'fin';
        $auxLlamadaK[6] = 'st';

        $auxLlamadaV[0] = 'Número ID de llamada'; 	 	 	
        $auxLlamadaV[1] = 'Número ID de Tipificacion';
        $auxLlamadaV[2] = 'Barrida';
        $auxLlamadaV[3] = 'Observaciones';
        $auxLlamadaV[4] = 'Fecha de registro';
        $auxLlamadaV[5] = 'Fecah de finalización de llamada';
        $auxLlamadaV[6] = 'Estatus';


        // INSTRUMENTO
        $auxInstrumentoK[0] = 'barrida';
        $auxInstrumentoK[1] = 'codigo';
        $auxInstrumentoK[2] = 'de';
        $auxInstrumentoK[3] = 'st';
        $auxInstrumentoK[4] = 'id_admin';
        $auxInstrumentoK[5] = 'id';

        $auxInstrumentoV[0] = 'Barrida';
        $auxInstrumentoV[1] = 'Codigo';
        $auxInstrumentoV[2] = 'Descripción';
        $auxInstrumentoV[3] = 'Estatus';
        $auxInstrumentoV[4] = 'ID Administrador';
        $auxInstrumentoV[5] = 'ID Instrumento';

        // TIPIFICACION
        $auxTipificacionK[0] = 'id';
        $auxTipificacionK[1] = 'de';
        $auxTipificacionV[0] = 'Número ID';
        $auxTipificacionV[1] = 'Descripción';



        // ////////////////////////////////////

        $nCampoRpt = 0;
        $_cad = '';
        if( isset($campos) )
            foreach( $campos as $campo ){
                $nCampoRpt++;
                $_cad .= '<div class="form-group" style="clear:both; height: 2.0em;">';
                $_cad .= '<input type="hidden" name="id_' .$nCampoRpt .'" id="id_' .$nCampoRpt .'" value="' .$campo->id .'" />';
                $_cad .= '<div class="col-sm-3">Nombre: <br /><input type="text" name="de_' .$nCampoRpt .'" id="de_' .$nCampoRpt .'" value="' .$campo->de .'" style="width:200px;"/></div>';
                $_cad .= '<div class="col-sm-3"> Tabla:<br />';
                $_cad .= '<select name="tabla_' .$nCampoRpt .'" id="tabla_' .$nCampoRpt .'" class="form-control" style="width:200px;" onChange="upTabla( $(this).val(),\'' .$nCampoRpt .'\' );">';
                for( $i7 = 0 ; $i7 < count($vctT) ; $i7++ )
                    if( strtolower($vctT[$i7]) == strtolower($campo->tabla) )
                        $_cad .= '<option value="' .$i7 .'" selected="selected"> &nbsp; ' .$vctT[$i7] .'</option>';
                    else
                        $_cad .= '<option value="' .$i7 .'"> &nbsp; ' .$vctT[$i7] .'</option>';
                $_cad .='</select></div>';
                $_cad .= '<div class="col-sm-3"> Campo: <br />';
                $_cad .= '<select name="xcampo_' .$nCampoRpt .'" id="xcampo_' .$nCampoRpt .'" class="form-control" style="width:200px;">';
                $vt1 = array();
                $vt2 = array();
                switch( strtolower($campo->tabla) ){
                case 'instrumento':
                    $vt1 = $auxInstrumentoK;
                    $vt2 = $auxInstrumentoV;
                    break;
                case 'cuota':
                    $vt1 = $auxCuotaK;
                    $vt2 = $auxCuotaV;
                    break;
                case 'prospecto':
                    $vt1 = $auxProspectoK;
                    $vt2 = $auxProspectoV;
                    break;
                case 'tipificacion':
                    $vt1 = $auxTipificacionK;
                    $vt2 = $auxTipificacionV;
                    break;
                case 'llamada':
                    $vt1 = $auxLlamadaK;
                    $vt2 = $auxLlamadaV;
                    break;        
                }

                foreach( $vt1 as $k => $v ){
                    if( strtolower($vt1[$k]) == strtolower($campo->campo) )
                        $_cad .= '<option value="' .$vt1[$k] .'" selected="selected">' .$vt2[$k] .'</option>';
                    else
                        $_cad .= '<option value="' .$vt1[$k] .'">' .$vt2[$k] .'</option>';
                }
    
                $_cad .= '</select>';
                $_cad .= '</div>';
                $_cad .= '<div class="col-sm-1">';
                $_cad .= ' Activo:<br /> ';
                if( $campo->st == 1 )
                    $_cad .= ' <input type="checkbox" name="st_' .$nCampoRpt .'" id="st_' .$nCampoRpt .'" value="1" checked="checked"/> Sí';
                else
                    $_cad .= ' <input type="checkbox" name="st_' .$nCampoRpt .'" id="st_' .$nCampoRpt .'" value="1"/> Sí';        
                $_cad .= '</div>';
                $_cad .= '<div class="col-sm-2">';
                $_cad .= ' Salvar<br />';
                $_cad .= '<input type="checkbox" id="delCol_' .$nCampoRpt .'" name="delCol[]" value="1"/> No';
                $_cad .= '</div>';
                $_cad .= '</div>';
    
            }
        $cad .= $_cad;
        $cad .= '</div>
   <div class="" style="clear: both;">
      <button type="button" class="btn btn-primary" style="width:150px;" onClick="nCampoRpt=addCampoRpt(nCampoRpt);" > Agregar Campo </button>
      </div>';
        
        $cad .= '<hr /><br />';

        $cad .= '<button type="button" class="btn btn-danger" style="width:150px;" onClick="submit();" >   Salvar   </button>';
        
        
        $cad .= '</form>';
        $cad .= '</div>';        

        $cad .= '<br /><hr /><br />';

       
        $dato[] = 'ID';
        $dato[] = 'Código';
        $dato[] = 'Nombre';
        $data[] = $dato;
        unset($dato);

       
        $select1 = "select * from a.reporte where st != '0' order by tp asc, id asc;";
        $reportes = Aux::findBySql($select1)->all();
        foreach( $reportes as $reporte ){
            $select2 = "select * from a.reporte_campo where id_reporte='" .$reporte->id ."' and st != '0' order by orden ASC, id ASC;";
            $campos = Aux::findBySql($select2)->all();
            
            $rpt_id	= (int)$reporte->id;
            $rpt_codigo = $reporte->codigo;
            $rpt_de = $reporte->de;
            $rpt_query = $reporte->query;
            $rpt_imagen = $reporte->imagen;
            $rpt_rol = $reporte->rol;
            

            $rpt_fechas_iguales = (int)$reporte->fechas_iguales;
            $rpt_filtrar_proyecto = (int)$reporte->filtrar_proyecto;
            $rpt_filtrar_desde = (int)$reporte->desde;
            $rpt_filtrar_hasta = (int)$reporte->hasta;
            $rpt_filtrar_tlo = (int)$reporte->tlo;
            $rpt_filtrar_instrumento = (int)$reporte->instrumento;
            $rpt_filtrar_data_tp = (int)$reporte->data_tp;
            $rpt_filtrar_fecha_tp = (int)$reporte->fecha_tp;
            $rpt_filtrar_dominio = (int)$reporte->dominio;
            $rpt_filtrar_agente = (int)$reporte->agente;
            
            foreach( $campos as $campo ){
                
            }
            
            
            $dato[] = $reporte->id;
            $dato[] = $reporte->codigo;
            $dato[] = $reporte->de;
            if( $reporte->tp == 0 ){
                $dato[] = '<a href="' .Yii::$app->params['baseUrl'] .'index.php?r=soporte/reporte/df&x3id=' .$reporte->id .'"><img src="' .Yii::$app->params['baseUrl'] .'img/icons/edit.png" style="width:1.5em;"  title="Editar ' .$reporte->de .'"/></a>';
                $dato[] = '<a href="javascript:if( confirm(\'Eliminar el reporte: ' .$reporte->de .' ?\')) location.href = \'' .Yii::$app->params['baseUrl'] .'index.php?r=soporte/reporte/eliminar1&x3id=' .$reporte->id .'\' ;"><img src="' .Yii::$app->params['baseUrl'] .'img/icons/del1.png" style="width:1.5em;"  title="Eliminar ' .$reporte->de .'"/></a>';
            }
            $data[] = $dato;
            unset($dato);
            
        }
                
       



        $cad .= Listado::listado( $data );

        
        return $this->render('@views/df/txt', array(
            'titulo' => 'Configuración del Reporte',
            'txt' => $cad,
            
            'ayuda' => '<div class="alert alert-info"><hr /> h <hr /></div>',
        ));
        
    } // eof
    
    
} // class
