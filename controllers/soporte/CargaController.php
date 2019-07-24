<?php
namespace app\controllers\soporte;
use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;


use app\controllers\crm\CrmController;

use app\models\UploadForm;
use yii\web\UploadedFile;

use app\models\crm\Prospecto;
use app\models\movistar\ProspectoMovistar;

use app\components\crm\Mensaje;
use app\components\crm\Request;
use app\components\crm\X3html;
use app\components\crm\Email;
use app\components\crm\Ayuda;
use app\components\crm\Listado;

use app\models\crm\xCuota;

use app\models\Aux;

/**
 * Controller de soporte
 */
class CargaController extends CrmController{
    
    public $proyecto = null;
    public $theme = '6';
    public $directorio = 'soporte/';
    public $vct1 = null;    
    public $tFila;
    public $tColumna;
    public $c = -1;

    /*
     * para uso del viz:
     * vct array 
     * vct2 array 
     * vct3 array ir a de la entrada
     */
    public $vct3;
    public $vct;
    public $vct2;
      
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
                        'roles' => ['admin','soporte'],
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
     * Proecsos que se ejecutan antes que el controller carge
     */
    public function init(){
        if( Yii::$app->user->isGuest ){
            Yii::$app->getResponse()->redirect(array('/df/logout',302));
            Yii::$app->end();
        }
        parent::init();
    } // eof #######################################################







    
    public static function JQGrid( $cabecera, $registros, $titulo = '', $json, $subact='', $detail = NULL ){
        return JQGrid::listado( $this->proyecto, $cabecera, $registros, $titulo, $json, $this->proyecto.$subact, $detail );
        
    } // eof #######################################################







    
    public function schema(){
        if( isset($this->proyecto) )
            return $this->proyecto;
        else
            return null;
    } // eof #######################################################








    
    public function actionUpload_deprecate(){
        echo 'asdtjhstjs';
        return $this->render('upload');
        exit(0);
        $model = new UploadForm();
         
        if( Yii::$app->request->isPost ){
            $model->imageFile = UploadedFile::getInstance($model, 'imageFile');
            if( $model->upload() ){
                // file is uploaded successfully
                return;
            }
        }
        return $this->render('upload', ['model' => $model]);
    } // eof #######################################################

    


    /**
     * Despliega las opciones
     */
    public function actionIndex(){
        $cadB = '';
        
        $height = '2.0em';
        $height2 = '3.0em';

        $cadB .= Mensaje::mostrar();
        $cadB .= Ayuda::toHtml('panel');
        
        
        $cadB .= '<div class="row"><hr /><div class="subtitulos col-sm-12"> &nbsp; Herramientas </div><br /><br /></div>';  
        $cadB .= '<div class="btn-group btn-group-justified">';
        
        
        $cadB .= '<a href="' .Yii::$app->params['baseUrl'] .'index.php?r=soporte/proyecto/proyecto" class="btn btn-default" title="Agregar Proyecto"><strong>+</strong> Proyectos</a>';
        $cadB .= '<a href="' .Yii::$app->params['baseUrl'] .'index.php?r=soporte/instrumento/df" class="btn btn-default" title="Agregar Instrumento"><strong>+</strong> Instrumentos</a>';
        $cadB .= '<a href="' .Yii::$app->params['baseUrl'] .'index.php?r=soporte/reporte/df" class="btn btn-default" title="Agregar Reporte"><strong>+</strong> Reportes</a>';
        
        $cadB .= '</div><div class="btn-group btn-group-justified">';
        
        $cadB .= '<a href="' .Yii::$app->params['baseUrl'] .'index.php?r=soporte/usuario/add" class="btn btn-default" title="Agregar Usuario"><strong>+</strong> Usuarios</a>';
        
        $cadB .= '<a href="' .Yii::$app->params['baseUrl'] .'index.php?r=soporte/gerencia/df" class="btn btn-primary" title="Ver Reporte General">Reporte General</a>';       
        $cadB .= '<a href="' .Yii::$app->params['baseUrl'] .'index.php?r=soporte/envios/df" class="btn btn-default" title="Envios"><strong>+</strong> Envios</a>';
        
       
        $cadB .= '</div>';
        
        
        $c1 = '#eeffee';
        $c2 = '#f6f6ff';
        $c3 = '#ffffee';
        $c = $c1;
        $vctSt['1'] = '<span style="color:#000099;">Activo</span>';
        $vctSt['2'] = '<span style="color:#999900;">Pendiente</span>';
        // $proyectos = xProyecto::findAll(['st' => 1]);
        
        $proyectos = Aux::findBySql("select * from a.proyecto where st!='0';")->all();
        
        //    $objInstrumento = new xInstrumento();
        /*      
                $cad = '<table border="0" style="width:100%; padding: 0.2em; text-align: center;"><tbody>';
                $cad .= '<tr class=" ui-state-active" style="font-weight:700;">';
                $cad .= '<td colspan="2">&nbsp;</td>';
                $cad .= '<td style="text-align: left; " colspan="1"> INSTRUMENTO </td>';
                // <a href="' .Yii::$app->params['baseUrl'] .'index.php?r=soporte/add/proyecto" title="Agregar Proyecto" style="color:#009;">(+)</a>
                $cad .= '<td width="10%"> &nbsp;</td>';
                $cad .= '<td width="10%"> &nbsp;</td>';
                //        $cad .= '<td width="10%"> PLANIFICACIÓN</td>';
                $cad .= '<td width="10%"> &nbsp;</td>';
                $cad .= '<td width="10%"> &nbsp;</td>';
                $cad .= '<td width="10%"> &nbsp;</td>';
                $cad .= '</tr>';
        */

        $cad = '<div class="row"><hr /><div class="subtitulos col-sm-12"> &nbsp; Instrumentos </div><br /><br /></div>';



 
        foreach( $proyectos as $proyecto ){
            
            
            
            // $cad .= '<tr><td><img src="'.Yii::$app->params['baseUrl'] .'img/bandera/' .$proyecto->pais .'.png" style="height:1.2em;"/></td><td>' .$proyecto->id .'.</td><td style="text-align: left;"> <span style="font-weight: bold;">' .$proyecto->de .' </span></td><td></td></tr>';
            
            
            $cad .= '<div class="row" style="background-color:#ccc;"><div class="col-sm-12"><img src="'.Yii::$app->params['baseUrl'] .'img/bandera/' .$proyecto->pais .'.png" style="height:1.5em;" title="' .$proyecto->pais .'"/> &nbsp; &nbsp; ' .$proyecto->de .'</div></div>';
            
            
            // (' .$vctSt[$proyecto->st] .')
            // $cad .= '<tr><td colspan="2">&nbsp;</td><td style="text-align: left; colspan="1"> INSTRUMENTOS DE ENCUESTA:  <a href="' .Yii::$app->params['baseUrl'] .'index.php?r=soporte/add/instrumento" title="Agregar Instrumento" style="color:#009;">(+)</a></td><td></td></tr>';   
            $sql1 = "select schema_name from information_schema.schemata where schema_name = '" .$proyecto->codigo ."';";
            //echo $sql1;
            $obj = Aux::findBySql($sql1)->one();
            if( isset($obj->schema_name) && $obj->schema_name != '' ){
                $sql2 = "select id, de, barrida, codigo, st, cuotas from " .$obj->schema_name .".instrumento where st != '0' order by id DESC;";
                // echo $sql2;
                //   $objInstrumento::$schema = $obj->schema_name;
                //     $vctInstrumento = $objInstrumento->findBySql($sql2)->all();
                $vctInstrumento = Aux::findBySql($sql2)->all();
                //$cad .= '<tr><td colspan="2"></td><td>';
                // $cad .= '<tr><td colspan="2"></td><td colspan="2"><table border="0" style="width:100%; padding: 0.4em; text-align: center;"><tbody>';


                foreach( $vctInstrumento as $instrumento ){
                    if( $c == $c1 ) $c = $c2;
                    else if( $c == $c2 ) $c = $c3;
                    else $c = $c1;

                    if( true ){

                        if( file_exists(Yii::$app->params['baseUrl'] .'controller/proyecto/' .ucFirst($instrumento->codigo) .'Controller') )
                            $pathController = Yii::$app->params['baseUrl'] .'index.php?r=proyecto/' .$instrumento->codigo;
                        else if( file_exists(Yii::$app->params['baseUrl'] .'controller/proyecto/' .ucFirst($obj->schema_name) .'Controller') )
                            $pathController = Yii::$app->params['baseUrl'] .'index.php?r=proyecto/' .$obj->schema_name;
                        else
                            $pathController = Yii::$app->params['baseUrl'] .'index.php?r=crm/operacion/encuesta';
     

                        $estilo = ' style="float:left; padding: 0.5em; padding-left: 1.5em; text-align:center;" ';
                        // <div class="row"><label class="control-label col-sm-4  ui-state-focus" for="Fecha de Encuesta">Fecha de Encuesta</label><div class="col-sm-8 ui-widget-content">
                        $cad .= '<div class="row" style="background-color:' .$c .';"><div class="col-sm-5" style="line-height:' .$height2 .';"> '.$proyecto->id.'.' .$instrumento->id .'. ' .$instrumento->de .'</div>';
                        $cad .= '<div class="col-sm-7" style="">';

                        $cad .= '<div ' .$estilo .' ><a href="'.Yii::$app->params['baseUrl'] .'index.php?r=crm/operacion/encuesta/barrida&x3proy=' .$obj->schema_name .'&x3inst=' .$instrumento->id .'" style="color: #0000aa;" title="Barrida"> 
<img src="'.Yii::$app->params['baseUrl'] .'img/icons/barrida2.png" style="height:' .$height .';"/>
</a></div>';

                        $cad .= '<div ' .$estilo .' ><a href="'.Yii::$app->params['baseUrl'] .'index.php?r=crm/operacion/encuesta/ver&x3proy=' .$obj->schema_name .'&x3inst=' .$instrumento->id .'" style="color: #0000aa;" title="Ver Instrumento"> 
<img src="'.Yii::$app->params['baseUrl'] .'img/icons/ver.png" style="height:' .$height .';"/>
</a></div>';

                    
                        $cad .= '<div ' .$estilo .' ><a href="'.Yii::$app->params['baseUrl'] .'index.php?r=soporte/carga/xlsup&x3proy=' .$obj->schema_name .'&x3inst=' .$instrumento->id .'" style="color: #0000aa;" title="Cargar data"> 
<img src="'.Yii::$app->params['baseUrl'] .'img/icons/data.png" style="height:' .$height .';"/>
</a></div>';

                        if( $instrumento->cuotas )
                            $cad .= '<div ' .$estilo .'> <a href="'.Yii::$app->params['baseUrl'] .'index.php?r=soporte/carga/cuotaform&x3proy=' .$obj->schema_name .'&x3inst=' .$instrumento->id .'" style="color: #0000aa;" title="Cargar cuota">
<img src="'.Yii::$app->params['baseUrl'] .'img/icons/cuota.png" style="height:' .$height .';"/>
</a></div>';
                        else
                            $cad .= '<div ' .$estilo .'> <a href="'.Yii::$app->params['baseUrl'] .'index.php?r=soporte/carga/asignarform&x3proy=' .$obj->schema_name .'&x3inst=' .$instrumento->id .'" style="color: #0000aa;" title="Asignar data">
<img src="'.Yii::$app->params['baseUrl'] .'img/icons/asignar.png" style="height:' .$height .';"/>
</a></div>';
                    
                        //          $cad .= '<td><a href="'.Yii::$app->params['baseUrl'] .'index.php?r=soporte/carga/plan1&x3proy=' .$obj->schema_name .'&x3inst=' .$instrumento->id .'" style="color: #0000aa;"> Ver</a></td>';
                        $cad .= '<div ' .$estilo .'><a href="'.Yii::$app->params['baseUrl'] .'index.php?r=soporte/carga/viz&x3proy=' .$obj->schema_name .'&x3inst=' .$instrumento->id .'" style="color: #0000aa;" title="Ruta de las preguntas">
<img src="'.Yii::$app->params['baseUrl'] .'img/icons/ruta.png" style="height:' .$height .';"/>
</a></div>';
                        $cad .= '<div ' .$estilo .'><a href="' .$pathController .'/contactar1&x3proy=' .$obj->schema_name .'&x3inst=' .$instrumento->id .'" style="color: #0000aa;" title="Acceder al instrumento">
<img src="'.Yii::$app->params['baseUrl'] .'img/icons/teleoperar.png" style="height:' .$height .';"/>
</a></div>';
                        $cad .= '<div ' .$estilo .'><a href="' .$pathController .'/reportes&x3proy=' .$obj->schema_name .'&x3inst=' .$instrumento->id .'" style="color: #0000aa;" title="Acceder al lsitado de reportes">
<img src="'.Yii::$app->params['baseUrl'] .'img/icons/reportes.png" style="height:' .$height .';"/>
</a></div>';

                        //          $cad .= '<div ' .$estilo .'><a href="'.Yii::$app->params['baseUrl'] .'index.php?r=' .$obj->schema_name .'/' .$instrumento->codigo .'/edit&x3inst=' .$instrumento->id .'" style="color: #0000aa;" title="Editar"><img src="'.Yii::$app->params['baseUrl'] .'img/icons/edit.png" style="height:' .$height .';"/></a></div>';

                        $cad .= '<div ' .$estilo .'><a href="'.Yii::$app->params['baseUrl'] .'index.php?r=soporte/instrumento/df&x3proyecto=' .$obj->schema_name .'&x3id=' .$instrumento->id .'" style="color: #0000aa;" title="Editar ' .$instrumento->de .'">
<img src="'.Yii::$app->params['baseUrl'] .'img/icons/edit.png" style="height:' .$height .';"/>
</a></div>';


                    
                        $cad .= '<div ' .$estilo .'><a href="javascript:if( confirm(\'Eliminar el instrumento: ' .$instrumento->de .' ?\')) location.href = \'' .Yii::$app->params['baseUrl'] .'index.php?r=soporte/instrumento/eliminar2&x3proyecto=' .$obj->schema_name .'&x3id=' .$instrumento->id .'\';" style="color: #0000aa;" title="Eliminar ' .$instrumento->de .'">
<img src="'.Yii::$app->params['baseUrl'] .'img/icons/del1.png" style="height:' .$height .';"/>
</a></div>';

                    
                    
                        $cad .= '</div></div>';

                    }


                    
                    /*
                      <br />Resumen<br />
                      Data cruda<br />
                      Encuestas por teleoperador<br />
                      Encuestas por teleoperador detallado<br />
                      Efectividad<br />
                      Efectividad Detallado<br />
                      Estadisticas<br />
                    */

                }
                //$cad .= '</tbody></table></td></tr>';
                
                // $cad .= '<tr><td colspan="4">&nbsp;</td></tr>';   
                $cad .= '<div class="row"><div class="col-sm-12">&nbsp;</div></div>';
          
            }
        }

        /*
          $txt = Request::rq('x3txt') .'';
          if( $txt != '' ) $txt = '<div class="alert alert-success"><ul style="position:relative; left:1.0em;">' .$txt .'</ul></div>';
          else $txt = '';
        */

        //        $txt = Mensaje::mostrar();
        
        $cadC = '';
        $cadC .= '<div class="btn-group btn-group-justified">';
        $cadC .= '<a href="' .Yii::$app->params['baseUrl'] .'index.php?r=soporte/perfil/add" class="btn btn-default" title="Agregar Perfil"><strong>+</strong> Perfiles</a>';
        $cadC .= '<a href="' .Yii::$app->params['baseUrl'] .'index.php?r=soporte/dominio/df" class="btn btn-default" title="Dominios"><strong>+</strong> Dominios</a>';
        $cadC .= '<a href="' .Yii::$app->params['baseUrl'] .'index.php?r=soporte/tipificacion/df" class="btn btn-default" title="Tipificaciones"><strong>+</strong> Tipificaciones</a>';
        $cadC .= '</div>';
        

        
        $cadC .= '<div class="btn-group btn-group-justified">';
        $cadC .= '<a href="' .Yii::$app->params['baseUrl'] .'index.php?r=crm/ayuda/df" class="btn btn-default" title="Ayuda"> Ayuda</a>';
        $cadC .= '<a href="' .Yii::$app->params['baseUrl'] .'index.php?r=soporte/mensaje/df" class="btn btn-default" title="Mensajes"><strong>+</strong> Mensajes</a>';
        $cadC .= '<a href="' .Yii::$app->params['baseUrl'] .'index.php?r=soporte/llamada/df" class="btn btn-default" title="Llamadas"> Llamadas Activas</a>';
        $cadC .= '</div>';


        
        return $this->render('@views/soporte/index',array(
            'menu' => '',
            'titulo' => 'Panel - Menú ',
            'txt' => $cadB .$cadC .'<br/>' .$cad ,
            'ayuda' => '',
        ));    
    } // eof #######################################################








    /**
     * Acción Planificación
     */
    public function actionPlan1(){
        $proy = Request::rq('x3proy');
        $inst = Request::rq('x3inst');
        $cad = '';
        $int = null;
        $dom = null;
        $headFecha = '';
        $d = date('Y-m-d');
        $dia = 60 * 60 * 24;
        $diasAntes = -10;
        $diasDespues = 30;
        $this->tFila = array();
        $this->tColumna = array();        
        $cad .= strtoupper('<h3> Proyecto: ' .$proy .' </h3><br />');

        //        $cad .= '<a href="'.Yii::$app->params['baseUrl'] .'index.php?r=soporte/carga/index"> <- Regresar</a> &nbsp; ';
        $cad .= '<a href="'.Yii::$app->params['baseUrl'] .'index.php?r=soporte/carga/cuotaform&x3proy=' .$proy .'&x3inst=' .$inst .'&">Agregar cuota</a>';
        $cad .= '<br />';
        
        $sql1 = 'select i.id from ' .$proy .'.instrumento i where i.st in (1,2,5) order by i.de ASC;';
        $obj1 = new Aux();
        $regs1 = $obj1->findBySql($sql1)->all();

        for( $k=$diasAntes  ; $k <= $diasDespues ; $k++){
            $time = time() + ( $k*$dia);
            $fecha = date('Y-m-d', $time );
            $vct[] = $fecha;
            if( $k == 0 )
                $headFecha .= '<th style="text-align: center; padding: 0.5em; color: #006; width: 6.0em;"> ' .$this->format2($fecha) .'<br>' .$this->format1(date('D',$time)) .'</th>';
            else if( $k < 0 )
                $headFecha .= '<th style="text-align: center; padding: 0.5em; color: #999; width: 6.0em;"> ' .$this->format2($fecha) .'<br>' .$this->format1(date('D',$time)) .'</th>';
            else
                $headFecha .= '<th style="text-align: center; padding: 0.5em; width: 6.0em;"> ' .$this->format2($fecha) .'<br>' .$this->format1(date('D',$time)) .'</th>';
        } // for
        
        foreach( $regs1 as $reg1 ){
            $cad .= $this->cuotasInstrumento( $proy, $reg1->id, $headFecha, $diasAntes, $vct );
            
        } // for

        $regresar = '<div class="regresar"><a href="'
                  .Yii::$app->params['baseUrl'] .'index.php?r=soporte/carga/index'
                  .'"> &lt;- Regresar</a></div>';
        
        return $this->render('@views/soporte/txt',array(
            'regresar' => $regresar,
            'menu' => '',
            'txt' => $cad,
        ));
    } // eof #######################################################



   

    /**
     * Cuotas del instrumento
     */
    public function cuotasInstrumento( $id_proyecto, $id_instrumento, $headFecha, $diasAntes, $vct ){
        $cad = '';
        $cant = count($vct);
        for( $s=1; $s<=$cant ;$s++  )
            $this->tColumna[$s] = 0;
        $sql1 = "select i.*, d.id as id_dominio, d.de as dominio from " .$id_proyecto .".instrumento i inner join " .$id_proyecto .".dominio d on d.id_instrumento=i.id where i.id='" .$id_instrumento ."' and d.st in (1,2,5) order by i.de ASC, d.de ASC;";
        //echo $sql1; exit();
        $obj1 = new Aux();
        $regs1 = $obj1->findBySql($sql1)->all();
        
        if( count($regs1) > 0 ){
            $cad .= '<h4>INSTRUMENTO ' .$id_instrumento .'. ' .$regs1[0]->de .'</h4>';
            $cad .= '<table border="1" style="width:144.0em; text-align: center; padding: 0.5em;">';
            $cad .= '<thead style="font-size: 0.8em;"><tr>';
            $cad .= '<th style="text-align:center; width:6.0em;"> ID</th>';
            $cad .= '<th style="text-align:center; width:36.0em;"> DOMINIO</th>';
            $cad .= '<th style="text-align:center; "> CUOTA</th>';
            $cad .= '<th style="text-align:center; "> TOTAL</th>';
            $cad .= $headFecha;
            $cad .= '</tr></thead>';
            $cad .= '<tbody>';
            
            foreach( $regs1 as $reg ){
                $this->tFila[$reg->id_dominio .'_encuesta'] = 0;
                $this->tFila[$reg->id_dominio .'_ref'] = 0;            
                foreach( $vct as $vctAux  ){
                    $e = 0;
                    $sqlSelect1 = '';
                    $sqlSelect2 = '';
                    $sqlSum = '';
                    $sql2 = '';
                    $sql3 = '';
                    for( $s=1; $s<=$cant ;$s++  ){
                        
                        $sqlSum .= $this->f01($s);
                        $sqlSelect1 .= $this->f02( $id_proyecto, $id_instrumento, $reg->id_dominio, 'fecha_encuesta', $vct[$e], $cant, $s );
                        $sqlSelect2 .= $this->f02( $id_proyecto, $id_instrumento, $reg->id_dominio, 'fecha_ref', $vct[$e++], $cant, $s );
                    } // for
                    $sql2 .= 'select ' .substr($sqlSum,0,-2) .' from (' .$sqlSelect1 .") w;";
                    $sql3 .= 'select ' .substr($sqlSum,0,-2) .' from (' .$sqlSelect2 .") w;";
                } // for
                $obj2 = new Aux();
                $regs2 = $obj2->findBySql($sql2)->all();
                $obj3 = new Aux();
                $regs3 = $obj3->findBySql($sql3)->all();
                
                //                $cad .= '<td>' .substr($reg2->fecha_ref,0,19) .'</td>';
                //  $cad .= '<td>' .substr($reg2->fecha_encuesta,0,19) .'</td>';
                $t = 0;
                foreach( $regs2 as $reg2 ){
                    $cad .= $this->cuotasDominio( $reg->id_dominio, $reg->dominio, $cant, $diasAntes, $reg2, $regs3[$t] );
                    $t++;
                } // for
            }
            
            $total = 0;
            $cad .= '<tr style="text-align:center;">';
            $cad .= '<td colspan="3"> &nbsp;</td>';
            $cadT = '';
            for( $s=1; $s<=$cant ;$s++  ){
                $total += $this->tColumna[$s];
                $cadT .= '<td>' .$this->cero($this->tColumna[$s]) .'</td>';
            } // for
            $cad .= '<td style="text-align: right;"><b>' .$total .' </b>&nbsp;</td>';
            $cad .= $cadT;
            $cad .= '</tr>';
            
            
            $cad .= '</tbody></table><br />';
        }
        return $cad;
    } // eof







    
    /**
     * Establece el color
     */
    public function color(){
        $vct = array();
        $vct[] = '#ffffcf';
        $vct[] = '#ffcfff';
            $vct[] = '#cfffff';
            $vct[] = '#ffffdf';
            $vct[] = '#ffdfff';
            $vct[] = '#dfffff';
            $vct[] = '#ffffef';
            $vct[] = '#ffefff';
            $vct[] = '#efffff';
            $vct[] = '#ffffbf';
            $vct[] = '#ffbfff';
            $vct[] = '#bfffff';
            $vct[] = '#ffcfcf';
            $vct[] = '#cfcfff';
            $vct[] = '#cfffcf';
            $vct[] = '#ffdfdf';
            $vct[] = '#dfdfff';
            $vct[] = '#dfffdf';
            $vct[] = '#ffefef';
            $vct[] = '#efefff';
            $vct[] = '#efffef';
            $vct[] = '#ffbfbf';
            $vct[] = '#bfbfff';
            $vct[] = '#bfffbf';
            $vct[] = '#ffafaf';
            $vct[] = '#afafff';
            $vct[] = '#afffaf';
            $vct[] = '#ffffaf';
            $vct[] = '#ffafff';
            $vct[] = '#afffff';
            $vct[] = '#ffffff';  
            $this->c++;
            if( !array_key_exists($this->c, $vct) )
                $this->c = 1;
            return $vct[$this->c];
    } // eof 
    
    





    /**
     * Cuotas por dominio
     */
    public function cuotasDominio( $id_dominio, $dominio, $cant, $diasAntes, $reg2, $reg3 ){
        $cad = '<tr style="text-align:center;">';
        $cad .= '<td rowspan="2" >' .$id_dominio  .'</td>';
        $cad .= '<td rowspan="2" style="text-align: left;"> &nbsp;' .$dominio .'</td>';
        $cad .= '<td style="text-align: left; width: 36.0em; font-size: 0.8em;"> Según día de encuesta</td>';
        $cad .= '<td style="text-align: right;"> [:' .$id_dominio .'_encuesta:] &nbsp;</td>';        
        for( $s=1; $s<=$cant ;$s++  ){
            $aux01 = 'd' .$this->set0($s,1);
            $this->vct1[$id_dominio]['encuesta'][$aux01] = $this->cero($reg2->$aux01);
            if( ($s - $diasAntes) == 0 )
                $cad .= '<td style="background-color:' .$this->color() .';color:#006;">' .$this->cero($reg2->$aux01) .'</td>';
            else if( ($s - $diasAntes) < 0 )
                $cad .= '<td style="background-color:' .$this->color() .';color:#999;">' .$this->cero($reg2->$aux01) .'</td>';
            else
                $cad .= '<td style="background-color:' .$this->color() .';">' .$this->cero($reg2->$aux01) .'</td>';
            $this->tColumna[$s] += $reg2->$aux01;
            $this->tFila[$id_dominio.'_encuesta'] += $reg2->$aux01;
        } // for
        $cad .= '</tr>';

        $cad .= '<tr style="text-align:center;">';
        $cad .= '<td style="text-align: left; width: 36.0em; font-size: 0.8em;"> Según día de visita</td>';
        $cad .= '<td style="text-align: right; color:#999;"> [:' .$id_dominio .'_ref:] &nbsp;</td>';
        for( $s=1; $s<=$cant ;$s++  ){
            $aux01 = 'd' .$this->set0($s,1);
            $this->vct1[$id_dominio]['ref'][$aux01] = $this->cero($reg2->$aux01);
            if( ($s - $diasAntes) == 0 ) 
                $cad .= '<td style="color:#006;">' .$this->cero($reg3->$aux01) .'</td>';
            else if( ($s - $diasAntes) < 0 )
                $cad .= '<td style="color:#999;">' .$this->cero($reg3->$aux01) .'</td>';
            else
                $cad .= '<td>' .$this->cero($reg3->$aux01) .'</td>';
            $this->tFila[$id_dominio.'_ref'] += $reg3->$aux01;
        } // for
        $cad .= '</tr>';
        foreach( $this->tFila as $k=>$v )
            $cad = str_replace('[:' .$k .':]', $v, $cad);
        return $cad;
    } // eof
    






    /**
     * Retorna día en español
     */
    public function format1( $cad ){
        switch( $cad ){
        case 'Sun': return 'Dom'; break;
        case 'Mon': return 'Lun'; break;
        case 'Tue': return 'Mar'; break;
        case 'Wed': return 'Mie'; break;
        case 'Thu': return 'Jue'; break;
        case 'Fri': return 'Vie'; break;
        case 'Sat': return 'Sab'; break;
        }
        return null;
    } // eof







    /**
     * Retorna mes en español
     */
    public function format2( $cad ){
        $part = explode('-', $cad);
        switch( $part[1] ){
        case '01': return $part[2] .', Ene<br />' .$part[0]; break;
        case '02': return $part[2] .', Feb<br />' .$part[0]; break;
        case '03': return $part[2] .', Mar<br />' .$part[0]; break;
        case '04': return $part[2] .', Abr<br />' .$part[0]; break;
        case '05': return $part[2] .', May<br />' .$part[0]; break;
        case '06': return $part[2] .', Jun<br />' .$part[0]; break;
        case '07': return $part[2] .', Jul<br />' .$part[0]; break;
        case '08': return $part[2] .', Ago<br />' .$part[0]; break;
        case '09': return $part[2] .', Sep<br />' .$part[0]; break;
        case '10': return $part[2] .', Oct<br />' .$part[0]; break;
        case '11': return $part[2] .', Nov<br />' .$part[0]; break;
        case '12': return $part[2] .', Dic<br />' .$part[0]; break;
        }
        return null;
    } // eof

    





    /**
     * Parte SQL 
     */
    public function f01( $i ){
        return ' sum(d' .$this->set0($i,1) .') as d' .$this->set0($i,1) .', ';
    } // eof

    





    /**
     * Parte SQL
     */
    public function f02( $proy, $inst, $dom, $campo, $valor, $cantidad, $i ){
        if( $i == 1 )
            $cad = ' select ';
        else
            $cad = ' union select ';
        for( $j=1 ; $j<= $cantidad; $j++ )
            if( $i == $j )
                $cad .= ' c.cuota as d' .$this->set0($j,1) .', ';
            else
                $cad .= ' null::integer as d' .$this->set0($j,1) .', ';
        $cad = substr($cad,0,-2);
        $cad .= " from " .$proy .".cuota c where c.id_instrumento='" .$inst ."' and id_dominio = '" .$dom ."' and " .$campo ." = '" .$valor ."' and c.st in (1,2,5) ";
        return $cad;
    } // eof 







    /**
     * Completa los ceros
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
    } // eof 




    
    /**
     * Invisibiliza si es cero
     */    
    public function cero( $valor ){
        if( (int)$valor == 0 ) $valor = '&nbsp;';
        return $valor;
    }// eof 












    /**
     * Reporte de carga
     */
    public function reporteCarga( $proyecto, $idInstrumento ){
        $instrumento = '';
        // CUOTAS
        $sqlXX1 = "select distinct c.fecha_ref, i.de from " .$proyecto .".cuota c inner join " .$proyecto .".instrumento i on i.id = c.id_instrumento where c.id_instrumento = '" .$idInstrumento ."' and c.st in (1,2,5) and c.fecha_encuesta='" .date('Y-m-d'). "' order by c.fecha_ref asc;";
        $regsXX1 = Aux::findBySql($sqlXX1)->all();

        $cad = '';
        $vct = array();
        $it = count($regsXX1);
        $cab2 = array();
        $ik = 0;
        $cuerpo1 = $cuerpo2 = '';
        $cabecera1 = "'Fecha', ";
        foreach( $regsXX1 as $a1 ){ // cuotas del dia
            $instrumento = $a1->de;
            $ik++;
            $cab2[] = "'" .$a1->fecha_ref ."'";
            $cabecera1 .= "'" .$a1->de ."',";
            $sqlXX = "select d.de, c.cuota from " .$proyecto .".dominio d inner join " .$proyecto .".cuota c on c.id_dominio = d.id where d.id_instrumento = '" .$idInstrumento ."' and c.st in (1,2,5) and c.fecha_encuesta='" .date('Y-m-d'). "' order by d.cod ASC;";
            $regsXX = Aux::findBySql($sqlXX)->all();
            foreach( $regsXX as $a2 ){
                if( isset($vct[$a1->de]) ) $vct[$a1->de] += $a2->cuota;
                else $vct[$a1->de] = $a2->cuota;
                $cuerpo2 .= "['" .$a2->de ."', " .$a2->cuota ."],";
                // $cuerpo1 .= "['" .$a1->fecha_ref ."'," .(int)$a2->cuota ."],";
            }
        } // for 
        $cabecera2 = "['Dominio', " .implode(',',$cab2) ."],";

        
        // ULTIMAS 30 CUOTAS
        $sql3 = "select i.de as instrumento, sum(c.cuota) as cuota, fecha_encuesta from " .$proyecto .".cuota c inner join " .$proyecto .".instrumento i on i.id = c.id_instrumento where c.fecha_encuesta::date >= '" .date('Y-m-d'). "'::date - 30 group by instrumento, fecha_encuesta order by c.fecha_encuesta asc;";
        $regs3 = Aux::findBySql($sql3)->all();
        foreach( $regs3 as $reg3 ){
            $cuerpo1 .= "['" .$reg3->fecha_encuesta ."'," .(int)$reg3->cuota ."],";
        }


        $cad .= "
<script type=\"text/javascript\"> 
google.charts.load('current', {'packages':['corechart','bar']});
google.charts.setOnLoadCallback(x3grafico1);

function x3grafico1(){ 
var data1 = new google.visualization.arrayToDataTable([
[" .substr($cabecera1,0,-1) ."], 
 " .substr($cuerpo1,0,-1) ."
]);

        var options1 = {
          title: 'Data cargada, últimos 30 días',
          curveType: 'function',
          legend: { position: 'bottom' }
        };
      

var chart1 = new google.visualization.LineChart(document.getElementById('x3GraficoCuota1'));
chart1.draw(data1, options1);
};

google.charts.load('current', {'packages':['bar']});
google.charts.setOnLoadCallback(drawStuff2);
function drawStuff2(){ 
var data2 = new google.visualization.arrayToDataTable([" .$cabecera2 ."" .$cuerpo2 ."]); 
var options2 = {chart: {title: 'Carga de cuotas solo día de hoy',subtitle: '" .$instrumento ."'},series:{0: { axis: 'cuotas' },},axes:{y: {cuotas: {label: 'Cuotas'}}}};
var chart2 = new google.charts.Bar(document.getElementById('x3GraficoCuota2'));
chart2.draw(data2, options2);
};

</script>";
        $tabla = '<table width="100%"><tbody><tr><th>INSTRUMENTO</th><th>CUOTA</th></tr></tbody><tbody>';
        foreach( $vct as $k => $v )
            $tabla .= '<tr><td>' .$k .'</td><td>' .$v .'</td></tr>';

        $tabla .= '</tbody></table>';
        $cad .= "<hr />" .$tabla ."<hr /><div id=\"x3GraficoCuota2\" style=\"width: 100%; height: 350px;\"></div><br /><div id=\"x3GraficoCuota1\" style=\"width: 100%; height: 350px;\"></div><hr /><button type=\"button\" class=\"btn btn-primary\" onClick=\"\">Enviar por correo electrónico</button><br /><br />";

        // Email::envioSimple( 'develop.callycall@gmail.com', 'prueba', $tabla);
        return $cad;
    } // eof #############################################################
    
    
    





    
    
    /**
     * Formulario Agregar Cuota
     */
    public function actionCuotaform(){
        $model = new UploadForm();
        $proy = Request::rq('x3proy');
        $inst = Request::rq('x3inst');
        $fecha = Request::rq('x3fecha');
        $cad = $cad1 = '';



        $auxFecha = '';
        if( $fecha != null )
            $auxFecha = '&x3fecha=' .$fecha;


        
            
        $sql1 = 'select id,de from ' .$proy .'.instrumento where st in (1,2,5) order by de ASC;';
        $obj1 = new Aux();
        $regs1 = $obj1->findBySql($sql1)->all();
        foreach( $regs1 as $v )
            if( $v->id == $inst )
                $cad1 .= '<option value="' .$v->id .'" selected="selected">' .$v->id .' - ' .$v->de .'</option>';
            else
                $cad1 .= '<option value="' .$v->id .'">' .$v->id .' - ' .$v->de .'</option>';
        /*
          if( $inst != null ){
          $sql2 = "select id,de from " .$proy .".dominio where st in (1,2,5) and (de = '' or id_instrumento='" .$inst ."'  )order by de ASC;";
          $obj2 = new Aux();
          $regs2 = $obj2->findBySql($sql2)->all();
          foreach( $regs2 as $v )
          $cad2 .= '<option value="' .$v->id .'">' .$v->id .' - ' .$v->de .'</option>';
          }

        */
        $cad = '';

            
        $cad .= '<div class="titulos" >Agregar Cuota</div>';
        $cad .= Mensaje::mostrar();

            
        $cad .= '<div class="row" style="text-align:left;">
                    <div class="col-sm-6" style="padding-right: 3.0em;">';

            
              $cad .= '<form id="cuotaform" method="POST" action="' .Yii::$app->params['baseUrl'] .'index.php?r=soporte/carga/in&x3op=1&x3proy=' .$proy .'&x3inst=' .$inst .'&">';


            
              $cad .= '<div class="row">';
              $cad .= '<label class="control-label col-sm-4  ui-corner-top ui-state-default ui-state-active ui-state-focus" for="Instrumento">'. Yii::t('app/crm', 'Instrumento') .'</label>';
              $cad .= '<div class="col-sm-8  ui-widget-content"><select id="id_instrumento" name="id_instrumento" class=""  style="background-color:#ffffff; border: 0px; width:100%;" onChange="carga( \'x3divDominio\', \'x3proy=' .$proy .$auxFecha .'&x3inst=\' +$(\'#id_instrumento\').val() +\'&x3op=1\', \'out\', \'soporte/carga\' );carga( \'x3divData\', \'x3proy=' .$proy .$auxFecha .'&x3inst=\' +$(\'#id_instrumento\').val() +\'&x3op=1\', \'selectdata\', \'soporte/carga\' );" >' .$cad1 .'</select></div>';
            $cad .= '</div>';



        $cad .= '<div class=" row">';
        $cad .= '<label class="control-label col-sm-4 ui-state-default" for="Data">'. Yii::t('app/crm', 'Data') .'</label>';
        $cad .= '<div id="x3divData" class="col-sm-8 ui-widget-content">';
        $cad .= '<img src="' .Yii::$app->params['baseUrl'] .'img/blank.png" style="height:0.1em;" onLoad="carga( \'x3divData\', \'x3proy=' .$proy .'&x3inst=' .$inst .'&x3op=1\', \'selectdata\', \'soporte/carga\' );" />';
        $cad .= '</div>';
        $cad .= '</div>';


        
        
        $cad .= "\n<script>\n
  $( function(){\n
    $( '#fecha_ref' ).datepicker({dateFormat: 'yy-mm-dd'});\n
    $( '#fecha_encuesta' ).datepicker({dateFormat: 'yy-mm-dd'});\n
  } );\n
  </script>\n";
        $cad .= '<div class="row">';
        $cad .= '<label class="control-label col-sm-4  ui-state-focus" for="Fecha de Encuesta">'. Yii::t('app/crm', 'Fecha de Encuesta') .'</label>';
        $cad .= '<div class="col-sm-8 ui-widget-content"><input type="text" id="fecha_encuesta" name="fecha_encuesta" value="' .date('Y-m-d') .'" style="text-align:center; width: 100%; border:0px;margin:0;  padding:0;" /></div>';
        $cad .= '</div>';


            
        $cad .= '<div class=" row">';
        $cad .= '<label class="control-label col-sm-4 ui-state-default" for="Dominio">'. Yii::t('app/crm', 'Dominio') .'</label>';
        $cad .= '<div id="x3divDominio" class="col-sm-8 ui-widget-content">';
        $cad .= '<img src="' .Yii::$app->params['baseUrl'] .'img/blank.png" style="height:0.1em;" onLoad="carga( \'x3divDominio\', \'x3proy=' .$proy .'&x3inst=' .$inst .'&x3op=1\', \'out\', \'soporte/carga\' );" />';
        $cad .= '</div>';
        $cad .= '</div>';
            
        // $cad .= '  <select id="id_dominio" name="id_dominio" class="">' .$cad2 .'</select>



        $cad .= '<div class="row" style=";">';
        $cad .= '<label class="control-label col-sm-4  ui-state-focus" for="Cuota">'. Yii::t('app/crm', 'Cuota') .'</label>';
        $cad .= '<div class="col-sm-8 ui-widget-content"><input type="text" id="cuota" name="cuota" type="number" max="9999" min="0" placeholder="0" style="text-align: center; border:0px;margin:0;  padding:0; width: 100%;" /></div>';
        $cad .= '</div>';


        $cad .= '<div class="row" style="">';
        $cad .= '<label class="control-label col-sm-4 ui-state-default" for="Fecha de Visita">'. Yii::t('app/crm', 'Fecha de Atención') .'</label>';
        $cad .= '<div class="col-sm-8 ui-widget-content"><input type="text" id="fecha_ref" name="fecha_ref" value="" style="text-align:center; width:100%; border:0px; margin:0; padding:0;" /></div>';
        $cad .= '</div>';

        // form-group 
        $cad .= '<div class="row">';
        $cad .= '<label class="control-label col-sm-4" ></label>';
        // $cad .= '<input type="submit" value="   Agregar   "/>';
            
        $cad .= '<button class="ui-button ui-corner-all ui-widget" onClick="submit();" id="button">   Agregar   </button>';
        $cad .= '</div>';
            
        $cad .= '</form>';




        /*            
                      $sqlXX1 = "select distinct c.fecha_ref from " .$proy .".cuota c where c.id_instrumento = '" .$inst ."' and c.st in (1,2,5) and c.fecha_encuesta='" .date('Y-m-d'). "' order by c.fecha_ref asc;";
                      $regsXX1 = Aux::findBySql($sqlXX1)->all();
                      $it = count($regsXX1);
                      $cab2 = array();
                      $ik = 0;
                      $cue = '';
                      foreach( $regsXX1 as $a1 ){
                      $ik++;
                      $cab2[] = "'" .$a1->fecha_ref ."'";
                      $sqlXX = "select d.de, c.cuota from " .$proy .".dominio d inner join " .$proy .".cuota c on c.id_dominio = d.id where d.id_instrumento = '" .$inst ."' and c.st in (1,2,5) and c.fecha_encuesta='" .date('Y-m-d'). "' order by d.cod ASC;";
                      $regsXX = Aux::findBySql($sqlXX)->all();
                      foreach( $regsXX as $a2 )
                      $cue .= "['" .$a2->de ."', " .$a2->cuota ."],";
                      }
                      $cab = "['Dominio', " .implode(',',$cab2) ."],";
            
                      $cad .= "<script type=\"text/javascript\">
                      google.charts.load('current', {'packages':['bar']});
                      google.charts.setOnLoadCallback(drawStuff);
                            function drawStuff() {
                      var data = new google.visualization.arrayToDataTable([
                      ".$cab."".$cue."
                      ]);
                      var options = {
                      // width: 900,
                      chart: {
                      title: 'Carga de Cuotas',
                      subtitle: 'distance on the left, brightness on the right'
                      },
                      series: {
                      0: { axis: 'distance' }, // Bind series 0 to an axis named 'distance'.
                      1: { axis: 'brightness' } // Bind series 1 to an axis named 'brightness'.
                      },
                      axes: {
                      y: {
                      distance: {label: 'parsecs'}, // Left y-axis.
                      brightness: {side: 'right', label: 'apparent magnitude'} // Right y-axis.
                      }
                      }
                      };
                      var chart = new google.charts.Bar(document.getElementById('x3GraficoCuota'));
                      chart.draw(data, options);
                      };
                      </script>
                      <div id=\"x3GraficoCuota\" style=\"width: 100%; height: 500px;\"></div>";
        */


        $cad .= $this->reporteCarga( $proy, $inst ); 
                  
            
        $cad .= '</div>';
        $cad .= '<div id="x3divPlan" class="col-sm-6">';

        $cad .= '<img src="' .Yii::$app->params['baseUrl'] .'img/blank.png" style="height:0.1em;" onLoad="carga( \'x3divPlan\', \'x3proy=' .$proy .'&x3inst=' .$inst .$auxFecha .'\', \'dominiocuota\', \'soporte/carga\' );" />';
        $cad .= '</div>';
        $cad .= '</div>';

        //        $cad .= '<script> $(function(){$("#id_instrumento").selectmenu();}); </script>';

        /*
          $regresar = '<div class="regresar"><a href="'
          . Yii::$app->params['baseUrl'] .'index.php?r=soporte/carga/index'
          .'"> &lt;- Regresar</a></div>';
        */
            
        return $this->render('@views/soporte/txt',array(
            'regresar' => '',
            'menu' => '',
            'txt' => $cad,
        ));
            
        
            
    } // eof #######################################################
    
    






    /**
     * Activa la data para el instrumento
     */
    public function actionAsignarform(){
        $cad = '';
        $auxFecha = '';      
        $cod_proyecto = Request::rq('x3proy');
        $id_instrumento = Request::rq('x3inst');
        
        
        
        
        // id 	de 	url 	acumulado 	reg 	st 	id_instrumento 	desde 	hasta 	cantidad 	reg_ini 	reg_fin
        
        
        $sql1 = "select id,de from " .$cod_proyecto .".instrumento where st in (1,2,5) and id='" .$id_instrumento ."' order by de ASC;";
        $reg1 = Aux::findBySql($sql1)->one();        
        
        $cad .= '<div class="titulos" > &nbsp; Data para el instrumento: &nbsp; ' .$reg1->de .'</div>';
        $cad .= Mensaje::mostrar();
        
        
        if( false ){   
            $cad .= '<div class="row" style="text-align:left;">
                    <div class="col-sm-6" style="padding-right: 3.0em;">';
                  
                  $cad .= '<form id="form1" method="POST" action="' .Yii::$app->params['baseUrl'] .'index.php?r=soporte/carga/in&x3op=1&x3proy=' .$cod_proyecto .'&x3inst=' .$id_instrumento .'&">';
                  
                  $cad .= '<div class="row">';
                  $cad .= '<label class="control-label col-sm-4  ui-corner-top ui-state-default ui-state-active ui-state-focus" for="Instrumento">'. Yii::t('app/crm', 'Instrumento') .'</label>';
                  $cad .= '<div class="col-sm-8  ui-widget-content"><select id="id_instrumento" name="id_instrumento" class=""  style="background-color:#ffffff; border: 0px; width:100%;" onChange="carga( \'x3divDominio\', \'x3proy=' .$cod_proyecto .$auxFecha .'&x3inst=\' +$(\'#id_instrumento\').val() +\'&x3op=1\', \'out\', \'soporte/carga\' );carga( \'x3divData\', \'x3proy=' .$cod_proyecto .$auxFecha .'&x3inst=\' +$(\'#id_instrumento\').val() +\'&x3op=1\', \'selectdata\', \'soporte/carga\' );" >' .$cad1 .'</select></div>';
                $cad .= '</div>';
            
            $cad .= '<div class=" row">';
            $cad .= '<label class="control-label col-sm-4 ui-state-default" for="Data">'. Yii::t('app/crm', 'Data') .'</label>';
            $cad .= '<div id="x3divData" class="col-sm-8 ui-widget-content">';
            $cad .= '<img src="' .Yii::$app->params['baseUrl'] .'img/blank.png" style="height:0.1em;" onLoad="carga( \'x3divData\', \'x3proy=' .$cod_proyecto .'&x3inst=' .$id_instrumento .'&x3op=1\', \'selectdata\', \'soporte/carga\' );" />';
            $cad .= '</div>';
            $cad .= '</div>';
            
            $cad .= "\n<script>\n
  $( function(){\n
    $( '#fecha_ref' ).datepicker({dateFormat: 'yy-mm-dd'});\n
    $( '#fecha_encuesta' ).datepicker({dateFormat: 'yy-mm-dd'});\n
  } );\n
  </script>\n";
            $cad .= '<div class="row">';
            $cad .= '<label class="control-label col-sm-4  ui-state-focus" for="Fecha de Encuesta">'. Yii::t('app/crm', 'Fecha de Encuesta') .'</label>';
            $cad .= '<div class="col-sm-8 ui-widget-content"><input type="text" id="fecha_encuesta" name="fecha_encuesta" value="' .date('Y-m-d') .'" style="text-align:center; width: 100%; border:0px;margin:0;  padding:0;" /></div>';
            $cad .= '</div>';


            
            $cad .= '<div class=" row">';
            $cad .= '<label class="control-label col-sm-4 ui-state-default" for="Dominio">'. Yii::t('app/crm', 'Dominio') .'</label>';
            $cad .= '<div id="x3divDominio" class="col-sm-8 ui-widget-content">';
            $cad .= '<img src="' .Yii::$app->params['baseUrl'] .'img/blank.png" style="height:0.1em;" onLoad="carga( \'x3divDominio\', \'x3proy=' .$cod_proyecto .'&x3inst=' .$id_instrumento .'&x3op=1\', \'out\', \'soporte/carga\' );" />';
            $cad .= '</div>';
            $cad .= '</div>';


            $cad .= '<div class="row" style=";">';
            $cad .= '<label class="control-label col-sm-4  ui-state-focus" for="Cuota">'. Yii::t('app/crm', 'Data') .'</label>';
            $cad .= '<div class="col-sm-8 ui-widget-content"><input type="text" id="cuota" name="cuota" type="number" max="9999" min="0" placeholder="0" style="text-align: center; border:0px;margin:0;  padding:0; width: 100%;" /></div>';
            $cad .= '</div>';


            $cad .= '<div class="row" style="">';
            $cad .= '<label class="control-label col-sm-4 ui-state-default" for="Fecha de Visita">'. Yii::t('app/crm', 'Fecha de Atención') .'</label>';
            $cad .= '<div class="col-sm-8 ui-widget-content"><input type="text" id="fecha_ref" name="fecha_ref" value="" style="text-align:center; width:100%; border:0px; margin:0; padding:0;" /></div>';
            $cad .= '</div>';

            $cad .= '<div class="row">';
            $cad .= '<label class="control-label col-sm-4" ></label>';
            
            $cad .= '<button class="ui-button ui-corner-all ui-widget" onClick="submit();" id="button">   Agregar   </button>';
            $cad .= '</div>';
            
            $cad .= '</form>';

            
            $cad .= '</div>';
            
            $cad .= '</div>';
        }


            

        if( false ){               
            // CUOTAS
            $sqlXX1 = "select distinct c.fecha_ref, i.de from " .$proyecto .".cuota c inner join " .$proyecto .".instrumento i on i.id = c.id_instrumento where c.id_instrumento = '" .$idInstrumento ."' and c.st in (1,2,5) and c.fecha_encuesta='" .date('Y-m-d'). "' order by c.fecha_ref asc;";
            $regsXX1 = Aux::findBySql($sqlXX1)->all();
            
            $cad = '';
            $vct = array();
            $it = count($regsXX1);
            $cab2 = array();
            $ik = 0;
            $cuerpo1 = $cuerpo2 = '';
            $cabecera1 = "'Fecha', ";
            foreach( $regsXX1 as $a1 ){ // cuotas del dia
                $instrumento = $a1->de;
                $ik++;
                $cab2[] = "'" .$a1->fecha_ref ."'";
                $cabecera1 .= "'" .$a1->de ."',";
                $sqlXX = "select d.de, c.cuota from " .$proyecto .".dominio d inner join " .$proyecto .".cuota c on c.id_dominio = d.id where d.id_instrumento = '" .$idInstrumento ."' and c.st in (1,2,5) and c.fecha_encuesta='" .date('Y-m-d'). "' order by d.cod ASC;";
                $regsXX = Aux::findBySql($sqlXX)->all();
                foreach( $regsXX as $a2 ){
                    if( isset($vct[$a1->de]) ) $vct[$a1->de] += $a2->cuota;
                    else $vct[$a1->de] = $a2->cuota;
                    $cuerpo2 .= "['" .$a2->de ."', " .$a2->cuota ."],";
                    // $cuerpo1 .= "['" .$a1->fecha_ref ."'," .(int)$a2->cuota ."],";
                }
            } // for 
            $cabecera2 = "['Dominio', " .implode(',',$cab2) ."],";

            
            
            // GRAFICO DE ULTIMAS 30 CARGAS
            $sql3 = "select i.de as instrumento, sum(c.cuota) as cuota, fecha_encuesta from " .$proyecto .".cuota c inner join " .$proyecto .".instrumento i on i.id = c.id_instrumento where c.fecha_encuesta::date >= '" .date('Y-m-d'). "'::date - 30 group by instrumento, fecha_encuesta order by c.fecha_encuesta asc;";
            $regs3 = Aux::findBySql($sql3)->all();
            foreach( $regs3 as $reg3 ){
                $cuerpo1 .= "['" .$reg3->fecha_encuesta ."'," .(int)$reg3->cuota ."],";
            }
            $cad .= "<script type=\"text/javascript\"> 
google.charts.load('current', {'packages':['corechart','bar']});
google.charts.setOnLoadCallback(x3grafico1);
function x3grafico1(){ 
var data1 = new google.visualization.arrayToDataTable([
[" .substr($cabecera1,0,-1) ."], 
 " .substr($cuerpo1,0,-1) ."
]);
        var options1 = {
          title: 'Data cargada, últimos 30 días',
          curveType: 'function',
          legend: { position: 'bottom' }
        };
var chart1 = new google.visualization.LineChart(document.getElementById('x3Grafico1'));
chart1.draw(data1, options1);
};
google.charts.load('current', {'packages':['bar']});
google.charts.setOnLoadCallback(drawStuff2);
function drawStuff2(){ 
var data2 = new google.visualization.arrayToDataTable([" .$cabecera2 ."" .$cuerpo2 ."]); 
var options2 = {chart: {title: 'Carga de cuotas solo día de hoy',subtitle: '" .$instrumento ."'},series:{0: { axis: 'cuotas' },},axes:{y: {cuotas: {label: 'Cuotas'}}}};
var chart2 = new google.charts.Bar(document.getElementById('x3Grafico2'));
chart2.draw(data2, options2);
};
</script>";
            $cad .= "<div id=\"x3Grafico2\" style=\"width: 100%; height: 350px;\"></div><br /><div id=\"x3Grafico1\" style=\"width: 100%; height: 350px;\"></div>";
        }
        

        
        
        $cad .= $this->cargaListado( $cod_proyecto, $id_instrumento );    
        return $this->render('@views/crm/txt',array('txt' => $cad));
    } // eof #######################################################
    
  


    /**
     * Listado de data según dominio
     */
    public function cargaListado( $cod_proyecto, $id_instrumento ){
        $sql0 = "select * from " .$cod_proyecto .".dominio where st='1' and id_instrumento='" .$id_instrumento ."' order by de asc;";
        $regs0 = Aux::findBySql($sql0)->all();
        
        $dato[] = 'ID';
        $dato[] = 'Archivo';
        $dato[] = 'Periodo';
        $dato[] = 'Prospectos';
        $dato[] = 'Dominio';
        $dato[] = 'Estatus';
        $data[] = $dato; unset($dato);
        $sql1 = "select dt.*,d.de as dominio, i.de as instrumento from " .$cod_proyecto .".data dt inner join " .$cod_proyecto .".instrumento i on i.id = dt.id_instrumento left join " .$cod_proyecto .".dominio d on dt.id_dominio = d.id where dt.id_instrumento='" .$id_instrumento ."' order by dt.id DESC ;";
        $regs1 = Aux::findBySql($sql1)->all();    
        foreach( $regs1 as $reg ){
            $dato[] = $reg->id;
            $dato[] = $reg->de;
            $dato[] = 'de ' .$reg->desde .' &nbsp; a ' .$reg->hasta;
            $dato[] = $reg->cantidad;
            $dom = '';
            $carga = "carga( 'x3divIdDominioData', 'cod_proyecto=" .$cod_proyecto ."&id_dominio=' +$(this).val() +'&id=" .$reg->id ."', 'updominiodata', 'soporte/carga' );";
            if( count($regs0) > 0 ){
                $dom .= '<select id="id_dominio" name="id_dominio" class="" onChange="' .$carga .'"><option value="0"> &nbsp; --- Seleccione --- </option>';
                foreach( $regs0 as $dominio )
                    if( $reg->dominio == $dominio->id ) 
                        $dom .= '<option value="' .$dominio->id .'" selected="selected"> &nbsp; ' .$dominio->de .'</option>';
                    else
                        $dom .= '<option value="' .$dominio->id .'"> &nbsp; ' .$dominio->de .'</option>';
                $dom .= '</select>';
            }
            $dato[] = $dom;
            $carga = "carga( 'x3divIdData', 'cod_proyecto=" .$cod_proyecto ."&st=' +$(this).val() +'&id=" .$reg->id ."', 'upstdata', 'soporte/carga' );";
            $aux = '<select id="st_dominio" name="st_dominio" class="" onChange="' .$carga .'">';
            if( $reg->st == 1 ){
                $aux .= '<option value="1" selected="selected"> &nbsp; Activo</option>';
                $aux .= '<option value="0"> &nbsp; No activo</option>';
            }else{
                $aux .= '<option value="1"> &nbsp; Activo</option>';
                $aux .= '<option value="0" selected="selected"> &nbsp; No activo</option>';
            }
            $aux .= '</select>';
            
            $dato[] = $aux;
            $data[] = $dato; unset($dato);
        }
        return Listado::listado($data);;
    } // eof
    


    
    /**
     * actualiza el estado de la data
     */
    public function actionUpstdata(){
        Yii::$app->layout = 'embebido';
        $cod_proyecto = Request::rq('cod_proyecto');
        // $id_instrumento = (int)Request::rq('id_instrumento');
        $st = (int)Request::rq('st');
        $id = (int)Request::rq('id');
        Aux::findBySql("update " .$cod_proyecto .".data set st='" .$st ."' where id='" .$id ."'")->one();
        return;
    }


    
    /**
     * actualiza el dominio de la data
     */
    public function actionUpdominiodata(){
        Yii::$app->layout = 'embebido';
        $cod_proyecto = Request::rq('cod_proyecto');
        // $id_instrumento = (int)Request::rq('id_instrumento');
        $id_dominio = (int)Request::rq('id_dominio');
        $id = (int)Request::rq('id');
        Aux::findBySql("update " .$cod_proyecto .".data set id_dominio='" .$id_dominio ."' where id='" .$id ."'")->one();
        return;
    }


    /**
     * Lista de dominio para select
     */
    public function actionOut(){
        Yii::$app->layout = 'embebido';
        $op = Request::rq('x3op');

        switch( $op ){    
        case 1: // lista de dominios para select
            $proy = Request::rq('x3proy');
            $inst = Request::rq('x3inst');
            $cad = '<select id="id_dominio" name="id_dominio"  style="background-color:#ffffff; border:none; width:100%;">';
            if( $inst != null ){
                //  $sql = "select id,de,cod from " .$proy .".dominio where st in (1,2,5) and (de = '' or id_instrumento='" .$inst ."'  ) order by de ASC;";
                $sql = "select id, de, cod from " .$proy .".dominio where st in (1,2,5) and id_instrumento='" .$inst ."' order by cod ASC;";
                $obj = new Aux();
                $regs = $obj->findBySql($sql)->all();
                foreach( $regs as $v )
                    $cad .= '<option value="' .$v->id .'">' .$v->cod .' - ' .$v->de .'</option>';
            }else
                $cad .= '<option value="">&nbsp;</option>';
            $cad .= '</select>';
            // $cad .= '<script> $(function(){$("#id_dominio").selectmenu();}); </script>';
            echo $cad;
            break;            
        }
        
    } // eof #######################################################









    /**
     * lista de data para select
     */
    public function actionSelectdata(){
        Yii::$app->layout = 'embebido';
        $proy = Request::rq('x3proy');
        $inst = Request::rq('x3inst');

        $fecha = date('Y-m-d H:i:s',strtotime('-3 day', strtotime(date('Y-m-d'))));
        $cad = '<select id="id_data" name="id_data"  style="background-color:#ffffff; border:none; width:100%;">';
        if( $inst != null ){
            $sql = "select id, de, cantidad from " .$proy .".data where st in (1,2) and id_instrumento='" .$inst ."' and cantidad > 0 and reg >= '" .$fecha ."'::timestamp order by id DESC;";
            $regs = Aux::findBySql($sql)->all();
            foreach( $regs as $v )
                $cad .= '<option value="' .$v->id .'">' .$v->id .' - ' .$v->de .' (' .$v->cantidad .' reg.)</option>';
            if( count($regs) == 0 ) $cad .= '<option value="">&nbsp; No hay data reciente </option>';
        }else
            $cad .= '<option value="">&nbsp; Instrumento no seleccionado </option>';
        $cad .= '</select>';
        echo $cad;
    } // eof #######################################################




    /**
     * Gráfico VIZ de opciones 
     */
    public function vizOpcion( $entrada, $opcion ){
        $cad = "\n";
        $cOption = '[style=filled, color=lightsteelblue1]';
        
        $this->vct3[$entrada->codigo .'.' .$opcion->valor] = $opcion->ir_a;
        
        $this->vct[(string)$opcion->valor] = $opcion->de;
        if( substr($opcion->valor,0,1) != '_' ){    
            if( true ) $ux = $opcion->valor;
            else $ux = $opcion->de;
            $cad .= '"' .$entrada->codigo .'. ' .$ux .'" ' .$cOption .';' ."\n"; // presentacion
            $cad .= '"' .$entrada->codigo .'" -> "' .$entrada->codigo .'. '.$ux .'";' ."\n"; // padre -> hijo
            
            if( isset($opcion->ir_a) && ( trim($opcion->ir_a) != '' || (int)$opcion->ir_a != '0' ) )
                $ir_a = $opcion->ir_a;
            else $ir_a = $entrada->ir_a;
            
            // escribe en el ovalo
            if( $ir_a == 'x3fin' )
                $cad .= '"' .$entrada->codigo .'. '.$ux .'" -> "FIN";' ."\n"; // hijo -> fin
            else
                $cad .= '"' .$entrada->codigo .'. '.$ux .'" -> "' .$ir_a .'";' ."\n"; // hijo -> padre
        }
        return $cad;
    } // eof 
    
    


    /**
     * Gráfico VIZ de entrada
     */
    public function vizEntrada( $cod_proyecto, $entrada ){
        $cad = '';
        // $this->vct = array();
        $cInputText = '[style=filled, color=thistle2]';
        $cInputRadio = '[style=filled, color=cadetblue1]';
        $cTexto = '[color=lightgrey, fontcolor=lightgrey]';
        $cFin = '[color="#000000"]';
            
            
        $this->vct3[$entrada->codigo] = $entrada->ir_a;
        $i = $entrada->id;
        if( $entrada->id_pregunta_tp == 1 ){
            if( $entrada->codigo == 'x3fin' ){
                $cad .= '"FIN" ' .$cFin .';';
                $this->vct['0'] = '';
            }else{
                $aux = $entrada->codigo;
                $cad .= '"' .$entrada->codigo .'" ' .$cTexto .';';
                $this->vct['0'] = $entrada->de;
                if( $entrada->ir_a == 'x3fin' )
                    $cad .= '"' .$entrada->codigo .'" -> "FIN";';
                else
                    $cad .= '"' .$entrada->codigo .'" -> "' .$entrada->ir_a .'";';
            }
        }else if( $entrada->id_pregunta_tp == 2  ){
            $this->vct['0'] = $entrada->de;
            $cad .= '"' .$entrada->codigo .'" ' .$cInputText .';';
            if( $entrada->ir_a == 'x3fin' )
                $cad .= '"' .$entrada->codigo .'" -> "FIN";';
            else
                $cad .= '"' .$entrada->codigo .'" -> "' .$entrada->ir_a .'";';     
        }else{
            $this->vct['0'] = $entrada->de;
            $cad .= '"' .$entrada->codigo .'" ' .$cInputRadio .';';
            $sql2 = "select de, valor, orden, ir_a from " .$cod_proyecto .".entrada_op where st in (1,2,5) and id_entrada='" .$entrada->id ."' order by id ASC;";                //  and tp != '1'
            $regs2 = Aux::findBySql( $sql2 )->all();
            foreach( $regs2 as $opcion ){ // por cada opcion
                $cad .= $this->vizOpcion( $entrada, $opcion );
            }
        }
        $this->vct2[$entrada->codigo] = $this->vct;
        $this->vct = null ;
        return $cad;
    } // eof 
    
    

    
    /**
     * Genera el gráfico de las rutas de las preguntas y respuestas
     */
    public function viz(){ 
        $aux = null;
        $cod_proyecto = Request::rq('x3proy');
        $id_instrumento = Request::rq('x3inst');
        $cad2 = '';
      
        $cad = '<script src="' .Yii::$app->params['baseUrl'] .'js/viz.js"></script>';    
        $cad .= '<script type="text/vnd.graphviz" id="x3ruta"> digraph "unix" { graph [	fontname = "Helvetica-Oblique",	bgcolor="white", fontcolor="black", size = "32,0" ];
 node [ shape = ellipse, fontsize = 24, sides = 8, allowedToMoveX = true, allowedToMoveY = true, distortion = "0.0", orientation = "0.0", skew = "0.0" color = "#000066", colorborder = "#ffffdd", fontname = "Helvetica-Outline" ];';
        
        $sql1 = "select e.id, e.id_pregunta_tp, e.de, e.codigo, e.ir_a from " .$cod_proyecto .".entrada e where e.st in (1,2,5) and e.id_instrumento='" .$id_instrumento ."' order by e.id ASC;";
        $regs1 = Aux::findBySql($sql1)->all();        
        foreach( $regs1 as $entrada )
            $cad .= $this->vizEntrada( $cod_proyecto, $entrada );
        
        $cad .= ' } </script><script> function inspect(s){ return "<pre>" + s.replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/\"/g, "&quot;") + "</pre>";} function src(id){ return document.getElementById(id).innerHTML; } function example(id, format, engine){ var result; try{ result = Viz(src(id), format, engine); if(format === "svg") return result; else return inspect(result); }catch(e){ return inspect(e.toString()); } }; </script>';

        
        if( false ) $cad2 = $this->vizTabla();
        else $cad2 = '';
        
        
        $cad .= '<div class="titulos"> Grafico de las Entradas del Instrumento </div><br />';
        $cad .= Mensaje::mostrar();
        $cad .= Ayuda::toHtml('ruta');
        $cad .= '<script>document.body.innerHTML += example("x3ruta", "svg");</script>';
        return array( $cad, $cad2 );
    } // eof #######################################################


    
    /**
     * Tabla de relaciones VIZ
     */
    public function vizTabla(){
        $cad2 .= '<div id="x3p1"><table style="min-width: 1200px;"><tbody>';
        $vctAux = null;
        foreach( $this->vct2 as $k => $v ){
            $cad2 .= '<tr><td colspan="4"><hr /></td></tr>';
            if( $vctAux != $k ){    
                $cad2 .= '<tr><td> ' .$k .'</td>';
                if( array_key_exists('0',$v) )
                    $cad2 .= '<td> ' .$v['0'] .'</td>';
                $vctAux = $k;
            }
            if( count($v) > 1){
                $cad2 .= '<td style="width: 50%;"><table><tbody>';
                foreach( $v as $k2 => $v2 )
                    if( $k2 != '0' && isset($this->vct3[$k .'.' .$k2]) )
                        $cad2 .= '<tr><td> ' .$k2 .'</td><td> &nbsp; &nbsp; </td><td> ' .$v2 .'</td><td> -> &nbsp; </td><td><input type="text" id="" name="" value="' .$this->vct3[$k .'.' .$k2] .'"  style="border:0;"/></td></tr>';
                $cad2 .= '</tbody></table></td>';
            }else
                $cad2 .= '<td><input type="text" id="" name="" value="' .$this->vct3[$k] .'" style="border:0;"/></td>';
            $cad2 .= '</tr>';
        }
        $cad2 .= '</tbody></table></div>';
        return $cad2;
    } // eof
    



   
    /**
     * Acción VIZ
     */
    public function actionViz(){
        $cads = $this->viz();
        return $this->render('@views/soporte/txt',array( 'txt' => $cads[0], ));
    } // eof #######################################################
        



    /**
     * Acciones vía AJAX
     */
    public function actionIn(){
        Yii::$app->layout = 'embebido';
        $op = Request::rq('x3op');
        switch( $op ){     
            
        case 1: // agregar cuota
            $proy = Request::rq('x3proy');
            $inst = Request::rq('x3inst');
            $id_dominio = Request::rq('id_dominio');
            $id_data = (int)Request::rq('id_data');
            $fecha_ref = Request::rq('fecha_ref');
            $fecha_encuesta = Request::rq('fecha_encuesta');
            $cuota = Request::rq('cuota');
            if( $proy == null || $inst == null || $id_dominio == null || $fecha_ref == null || $fecha_encuesta == null || $cuota == null || (int)$cuota < 1 || $id_data == 0 )
                break;
            $id_usuario = \Yii::$app->user->identity->id;

            $sql00 = "select * from " .$proy .".cuota where id_dominio = '" .$id_dominio ."' and fecha_encuesta='" .$fecha_encuesta ."' and fecha_ref='" .$fecha_ref ."';";
            $obj = Aux::findBySql($sql00)->one();
            // $idd = $obj->id;
            if( ! $obj instanceof Aux ){
                $obj = new xCuota();
                $obj::$schema = $proy;
                $obj->id_instrumento = $inst;
                $obj->id_dominio = $id_dominio;
                $obj->fecha_ref = $fecha_ref;
                $obj->fecha_encuesta = $fecha_encuesta;
                $obj->cuota = $cuota;
                $obj->id_data = $id_data;
                $obj->id_usuario = $id_usuario;
                $obj->save();
            }else{
                $sql00 = "update " .$proy .".cuota set cuota = '" .$cuota ."', id_data = '" .$id_data ."', id_usuario = '" .$id_usuario ."' where id_instrumento='" .$inst ."' and id_dominio = '" .$id_dominio ."' and fecha_encuesta='" .$fecha_encuesta ."' and fecha_ref = '" .$fecha_ref ."';";
                $obj = Aux::findBySql($sql00)->one();
            }
            
            $sql0 = "select cod from " .$proy .".dominio where id = '" .$id_dominio ."';";
            $objDominio = Aux::findBySql($sql0)->one();
            $dominio = $objDominio->cod;
            $sql1 = "select id from " .$proy .".cuota where id_usuario = '" .$id_usuario ."' order by id DESC;";
            $objCuota = Aux::findBySql($sql1)->one();
            $id_cuota = $objCuota->id;
            $sql3 = "select columna_dominio from " .$proy .".instrumento where id = '" .$inst ."';";
            $objColumnaDominio = Aux::findBySql($sql3)->one();
            $columna_dominio = $objColumnaDominio->columna_dominio;

            if( ! $obj instanceof Aux ){
                $sql2 = "insert into " .$proy .".cuota_prospecto select nextval('" .$proy .".cuota_prospecto_seq'::regclass), '" .$id_cuota ."',id,'" .$id_data ."' from " .$proy .".prospecto where id_instrumento='" .$inst ."' and id_data='" .$id_data ."' and " .$columna_dominio ."='" .$dominio ."';";
                Aux::findBySql($sql2)->one();
            }
            $this->redirect(Yii::$app->params['baseUrl'] .'index.php?r=soporte/carga/cuotaform&x3proy=' .$proy .'&x3inst=' .$inst .'&x3fecha=' .$fecha_encuesta, 302);
            break;

            
            
        case 2: // eliminar cuota
            $id = Request::rq('x3id');
            $proy = Request::rq('x3proy');
            $inst = Request::rq('x3inst');
            if( $proy == null || $inst == null || $id == null ) break;
            $obj = new xCuota();
            $obj::$schema = $proy;
            $obj->delete();
            $this->redirect(Yii::$app->params['baseUrl'] .'index.php?r=soporte/carga/dominiocuota&x3proy=' .$proy .'&x3inst=' .$inst .'&x3fecha=' .$obj->fecha_encuesta, 302);            
            break;

            
        case 3: // actualizar cuota
            $id = Request::rq('x3id');
            $proy = Request::rq('x3proy');
            $inst = Request::rq('x3inst');
            if( $proy == null || $inst == null || $id == null ) break;
            $obj = new xCuota();
            $obj::$schema = $proy;
            $id_dominio = Request::rq('id_dominio');
            $fecha_ref = Request::rq('fecha_ref');
            $fecha_encuesta = Request::rq('fecha_encuesta');
            $cuota = Request::rq('cuota');
            if( $id_dominio != null )
                $obj->id_dominio = $id_dominio;
            if( $fecha_ref != null )
                $obj->fecha_ref = $fecha_ref;
            if( $fecha_encuesta != null )
                $obj->fecha_encuesta = $fecha_encuesta;
            if( $cuota != null )
                $obj->cuota = $cuota;
            $obj->update();
            $this->redirect(Yii::$app->params['baseUrl'] .'index.php?r=soporte/carga/dominiocuota&x3proy=' .$proy .'&x3inst=' .$inst .'&x3fecha=' .$obj->fecha_encuesta, 302);
            break;
            
        }
        
        // no hace nada
        // $this->redirect(Yii::$app->params['baseUrl'] .'index.php?r=soporte/carga/index', 302);

    } // eof #######################################################




    /**
     * Cuota por dominio
     */
    public function actionDominiocuota(){
        Yii::$app->layout = 'embebido';
        $cad = '';
        $proy = Request::rq('x3proy');
        $inst = Request::rq('x3inst');
        $fecha = Request::rq('x3fecha');
        $auxFecha = '';
        $txtFecha = 'Todos los días';
        if( $fecha != null && 0 ){
            $auxFecha = " and fecha_encuesta='" .$fecha ."' ";
            $txtFecha = $fecha;
        }  
        $cad .= '<div style="font-size:0.8em;">
 <div class="row" style="font-size:0.6em;">
  <div class="col-sm-1  ui-state-active" title="ID de Instrumento">INST</div>
  <div class="col-sm-1  ui-state-active" title="ID Data">DATA</div>
  <div class="col-sm-3  ui-state-active" title="Dominio">DOMINIO</div>
  <div class="col-sm-2  ui-state-active" title="Fecha de Visita">VISITA</div>
  <div class="col-sm-2  ui-state-active" title="Fecha de Encuesta">ENCUESTA</div>
  <div class="col-sm-1  ui-state-active" title="Cuota">CUOTA</div>
  <div class="col-sm-1  ui-state-active" title="Conteo">CONTEO</div>
  <div class="col-sm-1  ui-state-active" title="Prospectos Disponibles">DISPONIBLE</div>
</div>';
        $sql1 = "select d.id, d.de, c.id_instrumento, c.cuota, c.conteo, c.fecha_ref, c.fecha_encuesta, c.id_data, count(cp.*) as disponible from " .$proy .".dominio d inner join " .$proy .".cuota c on c.id_dominio = d.id left join " .$proy .".cuota_prospecto cp on cp.id_cuota=c.id where d.st = '1' and d.id_instrumento='" .$inst ."' " .$auxFecha ." and c.fecha_encuesta::date >= (now()::date - 7)  group by d.id, d.de, cuota, conteo, fecha_ref, fecha_encuesta, c.id_data,  c.id_instrumento, c.id order by c.id DESC ;";
        // echo $sql1; exit();
        $regs1 = Aux::findBySql($sql1)->all();    
        foreach( $regs1 as $reg ){
            $cad .= '
<div class="row">
      <div class="col-sm-1 ui-widget-content" style="border-top:0px;"> ' .$reg->id_instrumento .'</div>
      <div class="col-sm-1 ui-widget-content" style="border-top:0px;"> ' .$reg->id_data .'</div>
      <div class="col-sm-3 ui-widget-content" style="border-top:0px;"> ' .$reg->de .'</div>
      <div class="col-sm-2 ui-widget-content" style="border-top:0px;"> ' .$reg->fecha_ref .'</div>
      <div class="col-sm-2 ui-widget-content" style="border-top:0px;"> ' .$reg->fecha_encuesta .'</div>
      <div class="col-sm-1 ui-widget-content" style="border-top:0px;"> ' .$reg->cuota .'</div>
      <div class="col-sm-1 ui-widget-content" style="border-top:0px;"> ' .$reg->conteo .'</div>
      <div class="col-sm-1 ui-widget-content" style="border-top:0px;"> ' .$reg->disponible .'</div>
</div>
';
        }
        $cad .= '</div>';
        $regresar = '<div class="regresar"><a href="'
                  . Yii::$app->params['baseUrl'] .'index.php?r=soporte/carga/index' 
                  .'"> &lt;- Regresar</a></div>';
        $regresar = '';
        return $this->render('@views/soporte/txt',array(
            'regresar' => $regresar,
            'menu' => '',
            'txt' => $cad,
        ));
    } // eof
    


    
    /**
     * Sube un archivo EXCEL a el servidor
     */
    public function actionXlsup(){
        $cod_proyecto = Request::rq('x3proy');
        $id_instrumento = (int)Request::rq('x3inst');

        $model = new UploadForm();
        if( Yii::$app->request->isPost ){
            $model->xls = UploadedFile::getInstance($model, 'xls');
        }
         
        if( Yii::$app->request->isPost && isset($model->xls) && $model->uploadXLS() ){
            //   echo 'sdfghj6666666666k'; exit();
            $this->redirect(Yii::$app->params['baseUrl'] .'index.php?r=soporte/carga/xls2html&x3proy=' .$cod_proyecto .'&x3inst=' .$id_instrumento .'&x3file=' .$model->nombre, 302);
        }else{
            $cad = '<div class="titulos"> Subir Archivo </div>';
            $cad .= Mensaje::mostrar();
            $cad .= Ayuda::toHtml('data');
            $cad .= $this->cabeceraData( $cod_proyecto, $id_instrumento );
            $cad .= $this->formData( $cod_proyecto, $id_instrumento );
            $cad .= '<br /><br />';
            $cad .= $this->graficoData( $cod_proyecto );
            $cad .= $this->listadoData( $cod_proyecto );              
            return $this->render('@views/soporte/txt',array( 'txt' => $cad ));
        }
    } // eof #######################################################
    


    /**
     * Cabecera de la DATA
     */    
    public function cabeceraData( $cod_proyecto, $id_instrumento ){
        $cad = '';
        $sqlCb = "select de from " .$cod_proyecto .".cabecera where id_instrumento='" .$id_instrumento ."' and st = '1' and tp = '0' order by orden asc;";
        $dataCb = Aux::findBySql( $sqlCb )->all();
        $cad .= '<table border="1" style="width:100%;text-align:center;"><tbody><tr><th colspan="' .count($dataCb) .'"> &nbsp; ORDEN DE LOS DATOS</th></tr><tr>';
        for( $ib = 1; $ib<=count($dataCb) ; $ib++ )
            $cad .= '<td style="min-width:80px;"> Columna ' .$ib .'</td>';
        $cad .= '</tr><tr>';
        foreach( $dataCb as $dt )
            $cad .= '<td style="min-width:80px;">' .ucfirst(strtolower($dt->de)) .'</td>';
        $cad .= '</tr></tbody></table>';
        return $cad;
    } // eof


    /**
     * Gráfico de la carga de la DATA
     */
    public function graficoData( $cod_proyecto ){
        $cad = '';
        $cad3 = '';
        $bagInst = array();
        $sql1o = 'select d.*,i.codigo as instrumento, i.id as idInstrumento from ' .$cod_proyecto .'.data d inner join ' .$cod_proyecto .'.instrumento i on i.id=d.id_instrumento where d.st in (1,2,5) order by d.id desc limit 30';
        $sql1 = 'select * from (' .$sql1o .') alia order by id asc;';
        $datas = Aux::findBySql($sql1)->all();
        $cad .= "<script type=\"text/javascript\">
      google.charts.load('current', {'packages':['corechart']});
      google.charts.setOnLoadCallback(drawChart);
      function drawChart() {
        var data = google.visualization.arrayToDataTable([";
        foreach( $datas as $dat ){
            if( !in_array("'" .$dat->instrumento ."'",$bagInst) && $dat->instrumento != '' )
                $bagInst[] = "'" .$dat->instrumento ."'";
            $cad3 .= "['" .substr($dat->reg,0,-16) ."',  " .$dat->cantidad ."],";
        }
        $cab1 = "['Fecha',". implode(',',$bagInst) .'],';        
        $cad .= $cab1 .substr( $cad3, 0, -1 ) ." ]);
        var options = {
          title: 'Últimas 30 cargas de Data',
          curveType: 'function',
          legend: { position: 'bottom' }
        };
        var chart = new google.visualization.LineChart(document.getElementById('x3GraficoData'));
        chart.draw(data, options);
      }
    </script>
  <div id=\"x3GraficoData\" style=\"width: 900px; height: 500px\"></div>";
              return $cad;
    } // eof
    

    /**
     * Formulario para cargar DATA
     */
    public function formData( $cod_proyecto, $id_instrumento ){
        $cad = '<form id="w0" enctype="multipart/form-data" method="POST" action="' .Yii::$app->params['baseUrl'] .'index.php?r=soporte/carga/xlsup">';
        $cad .= '<input type="hidden" id="x3proy" name="x3proy" value="' .$cod_proyecto .'"/>';
        $cad .= '<input type="hidden" id="x3inst" name="x3inst" value="' .$id_instrumento .'"/>';
        $cad .= '<input type="hidden" id="cod_proyecto" name="cod_proyecto" value="' .$cod_proyecto .'"/>';
        $cad .= '<input type="hidden" id="id_instrumento" name="id_instrumento" value="' .$id_instrumento .'"/>';
         
        if( false )
            $cad .= $form->field($model, 'xls')->fileInput(['multiple' => true, 'accept' => 'image/*']);
        else if( false )
            $cad .= $form->field($model, 'xls')->fileInput(['multiple' => false, 'accept' => '*']);
        
        $cad .= '<input type="hidden" name="UploadForm[xls]" value="">';
        $cad .= '<input type="file" id="uploadform-xls" name="UploadForm[xls]" accept="*">'; 
        
        $cad .= '<center><button> Subir Archivo </button></center>';
        $cad .= '</form>';
        return $cad;    
    } // eof

    

    /**
     * Listado de DATA cargada
     */
    public function listadoData( $cod_proyecto ){
        $cad = '';
        $sql = 'select d.*,i.codigo as instrumento, i.id as idInstrumento from ' .$cod_proyecto .'.data d inner join ' .$cod_proyecto .'.instrumento i on i.id = d.id_instrumento where d.st in (1,2,5) order by d.id desc limit 50;';
        $datas = Aux::findBySql( $sql )->all();
        
        $celda[] = 'ID';
        $celda[] = 'FECHA';
        $celda[] = 'USUARIO';
        $celda[] = 'SOPORTE';
        $celda[] = 'INSTRUMENTO';
        $celda[] = 'REG. INICIAL';
        $celda[] = 'REG. FINAL';
        $celda[] = 'CANTIDAD';
        $celda[] = 'ACUMULAR';
        $celda[] = 'ESTADO';
        $data[] = $celda; unset($celda);
        foreach( $datas as $dato ){
            $celda[] = $dato->id;
            $celda[] = substr($dato->reg,0,19);
            $celda[] = $dato->id_usuario;
            $celda[] = $dato->url;
            $celda[] = $dato->id_instrumento;
            $celda[] = $dato->reg_ini;
            $celda[] = $dato->reg_fin;
            $celda[] = $dato->cantidad;
            $celda[] = $dato->acumulado;
            $celda[] = $dato->st;
            $data[] = $celda; unset($celda);
        }
        $cad .= Listado::listado2( $data, strtoupper('Últimos archivos cargados en el proyecto <i>' .$cod_proyecto .'</i>') );
        unset($data);
        return $cad;
    } // eof
    
    


    
    /**
     * Lee la data en excel y la despliega en html
     */
    public function actionXls2html(){
        $proy = null;
        if( true ){

            // &&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&  INICIALIZA VARIABLES 
            $proy = Request::rq('x3proy');
            $inst = Request::rq('x3inst');
            $archivo = Request::rq('x3file');
            if( true ){
                error_reporting(E_ALL);
                ini_set('display_errors', TRUE);
                ini_set('display_startup_errors', TRUE);
            }
            define('EOL',(PHP_SAPI == 'cli') ? PHP_EOL : '<br />');
            date_default_timezone_set('America/Caracas');



            // &&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&   LEER EXCEL A VECTOR
            set_include_path('../vendor/PHPExcel/');
            include_once('PHPExcel/IOFactory.php');
            include_once('PHPExcel.php');
            // $objPHPExcel = new \PHPExcel();
            $inputFileName = Yii::$app->basePath .'/web/uploads/data/' .$archivo;
            $objPHPExcel = \PHPExcel_IOFactory::load($inputFileName);
            $sheetData = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);
            // &&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&

            
            
            // &&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&  SALIDA HTML
            $titulo = 'Datos suministrados desde el archivo';
            $cad3 = '';
            $cad3 .= "\n<script>\n
  $( function(){\n
    $( '#desde' ).datepicker({dateFormat: 'yy-mm-dd'});\n
    $( '#hasta' ).datepicker({dateFormat: 'yy-mm-dd'});\n
  } );\n
  </script>\n
 ";
            $cad3 .= '<div class="titulos">' .$titulo .'</div>';
            $cad3 .= '<form class="form-horizontal" id="f001" name="f001" action="' .Yii::$app->params['baseUrl'] .'index.php?r=soporte/carga/html2db" method="POST" >';
            $cad3 .= '<input type="hidden" id="x3proy" name="x3proy" value="' .$proy .'" />';
            $cad3 .= '<input type="hidden" id="x3inst" name="x3inst" value="' .$inst .'" />';
            $cad3 .= '<input type="hidden" id="x3file" name="x3file" value="' .$archivo .'" />';
            $cad3 .= '<br />';


            $vct1[] = 'label';
            $vct1[] = 'Descripción';
            $vct1[] = 4;
            $vct1[] = 'Descripcion';
            $vct[] = $vct1;
            $vct2[] = 'div';
            $vct2[] = '<input type="text" id="de" name="de" value="' .$archivo .'" style="width:350px;"/>';
            $vct2[] = 8;
            $vct[] = $vct2;
            $cad3 .= X3html::agrupar( $vct );
            unset($vct1);  unset($vct2); unset($vct);
            

            /*             
                           $cad3 .= '<div class="form-group">';
                           $cad3 .= '<label class="control-label col-sm-4" for="Instrumento">'. Yii::t('app/crm', 'Instrumento') .':</label>';
                           $cad3 .= '<div class="col-sm-8">';
                           $cad3 .= '<select id="instrumento" name="instrumento">' .$this->optionInstrumentos() .'</select>';
                           $cad3 .= '</div></div>';
            */


            $vct1[] = 'label';
            $vct1[] = 'Acumulado';
            $vct1[] = 4;
            $vct1[] = 'Acumulado';
            $vct[] = $vct1;
            $vct2[] = 'div';
            $vct2[] = '<input type="checkbox" id="acumulado" name="acumulado" value="0"/>';
            $vct2[] = 8;
            $vct[] = $vct2;
            $cad3 .= X3html::agrupar( $vct );
            unset($vct1); unset($vct2); unset($vct);            
            
            

            // $.datepicker.formatDate( "yy-mm-dd", new Date( 2007, 1 - 1, 26 ) );
            $vct1[] = 'label';
            $vct1[] = 'Desde';
            $vct1[] = 1;
            $vct1[] = 'Desde';
            $vct[] = $vct1;
            $vct2[] = 'div';
            $vct2[] = '<input type="text" id="desde" name="desde" value="' .date('Y-m-d') .'" style="width:100px;"/>';
            $vct2[] = 2;
            $vct[] = $vct2;

            $vct3[] = 'div';
            $vct3[] = '&nbsp;';
            $vct3[] = 2;
            $vct[] = $vct3;
            
            $vct4[] = 'label';
            $vct4[] = 'Hasta';
            $vct4[] = 2;
            $vct4[] = 'Hasta';
            $vct[] = $vct4;

            $vct5[] = 'div';
            $vct5[] = '<input type="text" id="hasta" name="hasta" value="' .date('Y-m-d') .'" style="width:100px;"/>';
            $vct5[] = 2;
            $vct[] = $vct5;
            
            $cad3 .= X3html::agrupar( $vct );
            unset($vct1); unset($vct2); unset($vct3); unset($vct4); unset($vct5); unset($vct);   
        

            
            $cad3 .= '<br/>';
            $cad3 .= '<button class="ui-button ui-corner-all ui-widget" id="button1"> Salvar en la Base de Datos</button>';
            $cad3 .= ' &nbsp; ';
            $cad3 .= '<button class="ui-button ui-corner-all ui-widget" id="button2"> Cargar otro Archivo </button>';
            
            /*
              $cad3 .= '<select class="ui-corner-all ui-widget" id="select1">
              <option value="BIG5">BIG5</option>
              <option value="ISO-8859-1">ISO-8859-1</option>
              <option value="ISO-8859-2">ISO-8859-2</option>
              <option value="ISO-8859-3">ISO-8859-3</option>
              <option value="ISO-8859-4">ISO-8859-4</option>
              <option value="ISO-8859-5">ISO-8859-5</option>
              <option value="ISO-8859-6">ISO-8859-6</option>
              <option value="ISO-8859-7">ISO-8859-7</option>
              <option value="ISO-8859-8">ISO-8859-8</option>
              <option value="ISO-8859-9">ISO-8859-9</option>
              <option value="ISO-8859-10">ISO-8859-10</option>
              <option value="ISO-8859-13">ISO-8859-13</option>
              <option value="ISO-8859-14">ISO-8859-14</option>
              <option value="ISO-8859-15">ISO-8859-15</option>
              <option value="ISO-2022-JP">ISO-2022-JP</option>
              <option value="US-ASCII">US-ASCII</option>
              <option value="UTF-7">UTF-7</option>
              <option value="UTF-8" selected="selected">UTF-8</option>
              <option value="UTF-16">UTF-16</option>
              <option value="Windows-1251">Windows-1251</option>
              <option value="Windows-1252">Windows-1252</option>
              <option value="ARMSCII-8">ARMSCII-8</option>
              <option value="ISO-8859-16">ISO-8859-16</option>
              </select>';
            */
            
            $cad3 .= '<hr />';
           

            //            print_r($sheetData);
            $tam = (isset($sheetData[1]))?count($sheetData[1]):0;
            
            $sql = "select * from " .$proy .".cabecera where id_instrumento='" .$inst ."' and st='1' order by orden ASC;";
            $objs = Aux::findBySql($sql)->all();    
            //$cabecera[] = '';
            for( $i=0 ; $i < $tam ; $i++ )
                if( isset($objs[$i]) )
                    $cabecera[] = $objs[$i]->de ;
       
            $cad3 .= '<hr />';
            
            $cad3 .= X3html::grid( $proy, $cabecera, 1, $sheetData, null, 'auto', '0.75em',true);
                
            $cad3 .= '</form>';     
            
            
            
            return $this->render('@views/soporte/form2',array(
                'regresar' => '',
                'data' => $cad3,
                'menu' => '',
                'titulo' => $titulo,
                'txt' => '<div class="alert alert-info">texto</div>',
                'ayuda' => '<div class="alert alert-info"><hr />' .'' .'<hr /></div>',
            ));
        }else{
            $this->redirect(Yii::$app->params['baseUrl'] .'index.php?r=soporte/carga/xlsup&p=', 302);
        }
    } // eof #######################################################
    
    

    
    public function addProspecto_deprecate($proyecto, $instrumento, $data){
        if( true ){
            switch( $proyecto ){
            case 'movistar':
                $obj = new Prospecto(); //Movistar();
                // $obj = new ProspectoMovistar();
                // $obj::$schema = $this->schema;
                // print_r($obj); exit();        
                break;
            default:
                $obj = new ProspectoTest();
                break;
            }
        }else{
            $obj = new cProspecto();
        }
        $obj::$schema = $this->schema;
        $obj->id_instrumento = $instrumento;
        $obj->id_data = $data;
        
        for( $j=1; $j <= $columnas ;$j++){
            if( $j > 0 ) break;
            if( $j < 10 )
                $pos = 'c00' .$j;
            else if( $j < 100 )
                $pos = 'c0' .$j;
            else
                $pos = 'c' .$j;
            $obj->$pos = $_REQUEST['campo_' .$i .'_' .$j];
        }
        $obj->save();
        /*
          $datas2 = new xData();
          $datas2::$schema = $proy;
          $datas = $datas2->findBySql($sql1)->all();
        */
        $id = $obj->getPrimaryKey();
        return $id;
        // print_r($id); exit();
    } // eof ##########################################################
    
           

    /**
     * Agrega la DATA
     */
    public function addData( $proyecto, $instrumento, $user, $url, $de, $acumulado, $desde, $hasta ){
        $key = array();
        $value = array();
        $id = null;
        $sql1 = 'insert into ' .$proyecto .'.data ';
        $key[] = 'id_instrumento';
        $value[] = "'" .(int)$instrumento ."'";
        $key[] = 'id_usuario';
        $value[] = "'" .(int)$user ."'";

        //    $key[] = 'schema';
        // $value[] = "'" .$proyecto ."'";

        $key[] = 'de';
        $value[] = "'" .$de ."'";

        $key[] = 'url';
        $value[] = "'" .$url ."'";

        $key[] = 'acumulado';
        $value[] = "'" .(int)$acumulado ."'";
        
        $key[] = 'desde';
        $value[] = "'" .$desde ."'";

        $key[] = 'hasta';
        $value[] = "'" .$hasta ."'";
        
        $keys = ' (' .implode(',',$key) .') ';
        $values = ' (' .implode(',',$value) .') ';
        $sql1 .= $keys .' values ' .$values.'; ';
        $sql2 = 'select id from ' .$proyecto .'.data order by id DESC limit 1; ';

        $connection = Yii::$app->getDb();
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $connection->createCommand($sql1)->execute();
            $id = $connection->createCommand($sql2)->queryScalar();
            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        } catch (\Throwable $e) {
            $transaction->rollBack();
            throw $e;
        }        
        return $id;
    } // eof ##########################################################


    
    public function addProspecto2_OLD( $proyecto, $instrumento, $data, $columnas, $i ){
        $key = array();
        $value = array();
        $id = null;
        $sql = '';
        $sql .= 'insert into ' .$proyecto .'.prospecto ';
        $key[] = 'id_instrumento';
        $value[] = "'" .$instrumento ."'";
        $key[] = 'id_data';
        $value[] = "'" .$data ."'";
        // $where = " where id_instrumento = '$instrumento' and id_data = '$data'";
        for( $j=1; $j <= $columnas ;$j++){
            // echo ' .-. <br>';
            if( $j > 100 ) break;
            if( $j < 10 )
                $pos = 'c00' .$j;
            else if( $j < 100 )
                $pos = 'c0' .$j;
            else
                $pos = 'c' .$j;
            $key[] = $pos;
            $auxi = 'campo_' .$i .'_' .$j;
            if( array_key_exists($auxi, $_REQUEST) )
                $ux = $_REQUEST[$auxi];
            else{
                $ux = '';
            }
            // echo $pos.' = '.$ux.'<br>';
            $value[] = "'" .$ux ."'";
            // $where .= " and $pos = '$ux' ";
        }
        $keys = ' (' .implode(',', $key) .') ';
        $values = ' (' .implode(',', $value) .') ';
        $sql .= $keys .' values ' .$values.'; ';
        $sql2 = 'select id from ' .$proyecto .'.prospecto order by id DESC limit 1; ';

        $connection = Yii::$app->getDb();
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $connection->createCommand($sql)->execute();
            $id = $connection->createCommand($sql2)->queryScalar();
            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        } catch (\Throwable $e) {
            $transaction->rollBack();
            throw $e;
        }
        
        


        /*
          $aux = new Aux();
          //$aux::$schema = $proyecto;
          $id = $aux->findBySql($sql)->one();
        */

        
        return $id;
    } // eof ##########################################################



    /**
     * Optiene la descripción del instrumento
     */
    public function getInstDe( $proyecto = 'test', $id = 0 ){
        $sql = "select de from " .$proyecto .".instrumento where id='" .$id ."' limit 1;";
        $cad = Yii::$app->getDb()->createCommand($sql)->queryScalar();
        return $cad .'';
    } // eof ##########################################################




    /**
     * Optiene la descripción de la tabla
     */
    public function getDe( $proyecto = null, $tabla = null, $id = null ){
        if( $proyecto == null || $tabla == null || $id == null ) return '';
        $sql = "select de from " .$proyecto ."." .$tabla ." where id='" .$id ."' limit 1;";
        return Yii::$app->getDb()->createCommand($sql)->queryScalar() .'';
    } // eof ##########################################################



    
    /**
     * Introduce la DATA HTML a la Base de Datos
     */
    public function actionHtml2db(){
        if( Yii::$app->request->isPost ){
            $t_inicio = microtime(true);
            
            // INICIALIZACION DE VARIABLES
            $titulo = '';
            $cad = '';
            $idIni = 0;
            $idFin = 0;
            $idUser = \Yii::$app->user->identity->id;
            $proyecto = Request::rq('x3proy');
            $inst = Request::rq('x3inst');            
            $archivo = Request::rq('x3file');
            
            
            // CARGAR ARCHIVO
            $inputFileName = Yii::$app->basePath .'/web/uploads/data/' .$archivo;

            // CONFIGURACION
            if( false ){
                error_reporting(E_ALL);
                ini_set('display_errors', TRUE);
                ini_set('display_startup_errors', TRUE);
            }
            define('EOL',(PHP_SAPI == 'cli') ? PHP_EOL : '<br />');
            date_default_timezone_set('America/Caracas');

            
            // LIBRERIAS NECESARIAS
            set_include_path('../vendor/PHPExcel/');
            include_once('PHPExcel/IOFactory.php');
            include_once('PHPExcel.php');
            


            

            // REGISTRA LA CARGA EN LA BASE DE DATOS
            $id_data = $this->addData($proyecto, $inst, $idUser, Request::rq('x3file'), Request::rq('de'), Request::rq('acumulado'), Request::rq('desde'), Request::rq('hasta'));            
            

            // INICIALIZACION DE OBJETO HOJA DE CALCULO
            // $objPHPExcel = new \PHPExcel();    
            $objPHPExcel = \PHPExcel_IOFactory::load($inputFileName);          
            $sheetData = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);
      
            
            // RECORRER CADA LINEA DE LA HOJA DE CALCULO
            $fila = 0;
            foreach( $sheetData as $linea ){
                $fila++;
                if( $fila == 1 ) continue;


                // INICIALIZACION DE VARIABLES A USAR POR CADA LINEA
                $columna = 0;
                $key = array();
                $value = array();
                $id = null;
                $sql = 'insert into ' .$proyecto .'.prospecto ';
                $key[] = 'id_instrumento';
                $value[] = "'" .$inst ."'";
                $key[] = 'id_data';
                $value[] = "'" .$id_data ."'";

                $contiene = false;
                // RECORRER CADA CELDA DE LA HOJA DE CALCULO
                foreach( $linea as $campo ){
                    $columna++;
                    if( $columna > 100 ) break;
                    if( $columna < 10 )
                        $pos = 'c00' .$columna;
                    else if( $columna < 100 )
                        $pos = 'c0' .$columna;
                    else
                        $pos = 'c' .$columna;
                    $key[] = $pos;
                    $value[] = "'" .str_replace("'",'&#39;',$campo) ."'";
                    if( trim($campo) != '' ) $contiene = true;
                } // for
                $keys = ' (' .implode(',', $key) .') ';
                $values = ' (' .implode(',', $value) .') ';
                $sql .= $keys .' values ' .$values.'; ';
                $sql2 = 'select id from ' .$proyecto .'.prospecto order by id DESC limit 1; ';

                if( $contiene ){
                    $connection = Yii::$app->getDb();
                    $transaction = Yii::$app->db->beginTransaction();
                    try {
                        $connection->createCommand($sql)->execute();
                        $id = $connection->createCommand($sql2)->queryScalar();
                        $transaction->commit();
                    } catch (\Exception $e) {
                        $transaction->rollBack();
                        throw $e;
                    } catch (\Throwable $e) {
                        $transaction->rollBack();
                        throw $e;
                    }   
                    
                    if( $idIni == 0 )
                        $idIni = $id;
                    $idFin = $id;
                    $total = $fila;
                }
                
            } // for linea
            
            
            $total--; // no se toma en cuenta la primera fila
            
            $sqlUp = "update " .$proyecto .".data set cantidad = '" .$total ."', reg_ini='" .$idIni ."', reg_fin='" .$idFin ."' where id='" .$id_data ."';";
            Aux::findBySql($sqlUp)->one();
            $t_final = microtime(true) - $t_inicio;
            $cad = '<hr />';
            $cad .= 'Carga número <b>' .$id_data .'</b>:<br /> Fueron registradas ' .$total .' entradas para el instrumento "<b>' .$this->getInstDe( $proyecto, $inst ) .'</b>" del proyecto <b>' .strtoupper($proyecto) .'</b>.<br />';
            $cad .= ' Registros del número '.$idIni .' al número ' .$idFin .'.';
            $cad .= '<br />';
            $cad .= ' Tiempo empleado: ' .round($t_final,0) .' segundos.';
            $cad .= '<hr />';
            $msn = $cad;
            
            
            $regresar = '<div class="regresar"><a href="'
                      .Yii::$app->params['baseUrl'] .'index.php?r=soporte/carga/index' 
                      .'"> &lt;- Regresar</a></div>';
            return $this->render('@views/soporte/form3',array(
                'regresar' => $regresar,
                'data' => $cad,
                'menu' => '',
                'titulo' => $titulo,
                'txt' => '<div class="alert alert-info">texto</div>',
                'ayuda' => '<div class="alert alert-info"><hr />' .'' .'<hr /></div>',
            ));
        }else{
            $this->redirect(Yii::$app->params['baseUrl'] .'index.php?r=soporte/carga/index&msn=' .$msn, 302);
        }
    } // eof #######################################################




    /**
     * microtime en formato float
     */
    function microtime_float(){
        list($useg,$seg) = explode(' ',microtime());
        return((float)$useg+(float)$seg);
    } // eof 



   
    /**
     * Introduce la DATA HTML a la Base de Datos
     */
    public function actionHtml2db_OLD(){
        if( false ){
            error_reporting(E_ALL);
            ini_set('display_errors', TRUE);
            ini_set('display_startup_errors', TRUE);
        }
        define('EOL',(PHP_SAPI == 'cli') ? PHP_EOL : '<br />');
        date_default_timezone_set('America/Caracas');

        /*      
                include_once('PHPExcel/IOFactory.php');
                include_once('PHPExcel.php');
                $objPHPExcel = new \PHPExcel();
        */
        
        $titulo = '';
        $cad = '';
        $idIni = 0;
        $idFin = 0;
        $idUser = \Yii::$app->user->identity->id;
        $proy = Request::rq('x3proy');
        $inst = Request::rq('x3inst');
        //   $archivo = $this->rq('x3file');
        if( Yii::$app->request->isPost ){
            $id_data = $this->addData($proy, $inst, $idUser, Request::rq('x3file'), Request::rq('de'), Request::rq('acumulado'), Request::rq('desde'), Request::rq('hasta'));
            foreach( $_REQUEST as $k=>$v ){
                if( substr($k,0,9) == 'posicion_' ) $auxPos = $k;
                if( substr($k,0,6) == 'campo_' ) $auxReg = $k;
                // echo $k.' = '.$v.'<br>';
            }
            $columnas = substr($auxPos,9);
            $len = -1 * (strlen($columnas) +1);
            $registros = substr(substr($auxReg,6),0,$len);
            for( $i=2; $i <= $registros ;$i++ ){
                $id = $this->addProspecto2($proy, $inst, $id_data, $columnas, $i);
                if( $idIni == 0 )
                    $idIni = $id;
                $idFin = $id;
                $total = $i;
            } // for
            $sqlUp = "update " .$proy .".data set cantidad = '" .$total ."', reg_ini='" .$idIni ."', reg_fin='" .$idFin ."' where id='" .$id_data ."';";
            Aux::findBySql($sqlUp)->one();            
            $cad = 'Fueron registradas ' .$total .' entradas para el instrumento "<b>' .$this->getInstDe( $proy, $inst ) .'</b>" del proyecto <b>' .strtoupper($proy) .'</b>.<br /> Del registro '.$idIni .' al registro ' .$idFin.'.';






            
            $regresar = '<div class="regresar"><a href="'
                      .Yii::$app->params['baseUrl'] .'index.php?r=soporte/carga/index' 
                      .'"> &lt;- Regresar</a></div>';
            return $this->render('@views/soporte/form3',array(
                'regresar' => $regresar,
                'data' => $cad,
                'menu' => '',
                'titulo' => $titulo,
                'txt' => '<div class="alert alert-info">texto</div>',
                'ayuda' => '<div class="alert alert-info"><hr />' .'' .'<hr /></div>',
            ));
       
        }else{
            $this->redirect(Yii::$app->params['baseUrl'] .'index.php?r=soporte/carga/index&', 302);
        }
    } // eof #######################################################

    





    /**
     * Introduce la DATA HTML a la Base de Datos
     */
    public function actionHtml2db_OLD_OOLD(){
        if( false ){
            error_reporting(E_ALL);
            ini_set('display_errors', TRUE);
            ini_set('display_startup_errors', TRUE);
        }
        define('EOL',(PHP_SAPI == 'cli') ? PHP_EOL : '<br />');
        date_default_timezone_set('America/Caracas');

        
     

        
        $titulo = '';
        $cad = '';
        $idIni = 0;
        $idFin = 0;
        $idUser = \Yii::$app->user->identity->id;
        $proy = Request::rq('x3proy');
        $inst = Request::rq('x3inst');
        //   $archivo = $this->rq('x3file');
        if( Yii::$app->request->isPost ){
            $id_data = $this->addData($proy, $inst, $idUser, Request::rq('x3file'), Request::rq('de'), Request::rq('acumulado'), Request::rq('desde'), Request::rq('hasta'));
            foreach( $_REQUEST as $k=>$v ){
                if( substr($k,0,9) == 'posicion_' ) $auxPos = $k;
                if( substr($k,0,6) == 'campo_' ) $auxReg = $k;
                // echo $k.' = '.$v.'<br>';
            }
            $columnas = substr($auxPos,9);
            $len = -1 * (strlen($columnas) +1);
            $registros = substr(substr($auxReg,6),0,$len);
            for( $i=2; $i <= $registros ;$i++ ){
                $id = $this->addProspecto2($proy, $inst, $id_data, $columnas, $i);
                if( $idIni == 0 )
                    $idIni = $id;
                $idFin = $id;
                $total = $i;
            } // for
            $sqlUp = "update " .$proy .".data set cantidad = '" .$total ."', reg_ini='" .$idIni ."', reg_fin='" .$idFin ."' where id='" .$id_data ."';";
            Aux::findBySql($sqlUp)->one();            
            $cad = 'Fueron registradas ' .$total .' entradas para el instrumento "<b>' .$this->getInstDe( $proy, $inst ) .'</b>" del proyecto <b>' .strtoupper($proy) .'</b>.<br /> Del registro '.$idIni .' al registro ' .$idFin.'.';

            $regresar = '<div class="regresar"><a href="'
                      .Yii::$app->params['baseUrl'] .'index.php?r=soporte/carga/index' 
                      .'"> &lt;- Regresar</a></div>';
            
            return $this->render('@views/soporte/form3',array(
                'regresar' => $regresar,
                'data' => $cad,
                'menu' => '',
                'titulo' => $titulo,
                'txt' => '<div class="alert alert-info">texto</div>',
                'ayuda' => '<div class="alert alert-info"><hr />' .'' .'<hr /></div>',
            ));
       
        }else{
            $this->redirect(Yii::$app->params['baseUrl'] .'index.php?r=soporte/carga/index&', 302);
        }
    } // eof #######################################################


    

    
    /**
     * Carga de cabecera de la encuesta desde un vector
     */
    public function actionAddcabeceras(){
        include('/home/sas/public_html/crm/modules/cyc/controllers/movistar/vector.php');
        $i = 0;
        //        $vctAux[] = 'ID_Encuesta ';
        $vctAux[] = 'Fecha de Atención';
        $vctAux[] = 'AGENTE';
        $vctAux[] = 'MOVIL';
        $vctAux[] = 'NOMBRE';
        $vctAux[] = 'Codigo de Vendedor';
        $vctAux[] = 'ZONA';
        $vctAux[] = 'ID ZONA';
        $vctAux[] = 'FECHA';
        $vctAux[] = 'HORA_INICIO';
        $vctAux[] = 'HORA_FIN';
        foreach( $vctAux as $v ) 
            $sqls[$i++] = "insert into movistar.cabecera (id_instrumento, de, columna, orden) values ('2', '" .$v ."', '" .$this->columna($i) ."', '" .$i ."');";
        foreach( $vct as $k=>$v )
            if( is_array($v) ){
                $sqls[$i++] = "insert into movistar.cabecera (id_instrumento, de, columna, orden) values ('2', '" .$k ."', '" .$this->columna($i) ."', '" .$i ."');";
                echo $sqls[$i -1] .'<br>';
            }
        $sqls[$i++] = "insert into movistar.cabecera (id_instrumento, de, columna, orden) values ('2', 'Teleoperador', '" .$this->columna($i) ."', '" .$i ."');";
        echo '<br>Cantidad: ' .$i .'<br>';
        foreach( $sqls as $sql ) Aux::findBySql($sql)->one();
    } // eof #######################################################




    /**
     *  Carga entradas de la encuesta desde un vector
     */
    public function actionVectorcreaencuesta(){

        include('/var/www/html/crm_dev/controllers/proyecto/movistar/vectorAA.php');
        $idInstrumento = '1';
        $cad = '';

        $i = 0;
        $vctAux[] = 'Fecha de Atención';
        $vctAux[] = 'AGENTE';
        $vctAux[] = 'MOVIL';
        $vctAux[] = 'NOMBRE';
        $vctAux[] = 'Codigo de Vendedor';
        $vctAux[] = 'ZONA';
        $vctAux[] = 'ID ZONA';
        $vctAux[] = 'FECHA';
        $vctAux[] = 'HORA_INICIO';
        $vctAux[] = 'HORA_FIN';
        foreach( $vctAux as $v ) 
            $sqls[$i++] = "insert into movistar.cabecera (id_instrumento, de, columna, orden) values ('" .$idInstrumento ."', '" .$v ."', '" .$this->columna($i) ."', '" .$i ."');";
        foreach( $vct as $k=>$v )
            if( is_array($v) ){
                $sqls[$i++] = "insert into movistar.cabecera (id_instrumento, de, columna, orden) values ('" .$idInstrumento ."', '" .$k ."', '" .$this->columna($i) ."', '" .$i ."');";
                $cad .= $sqls[$i -1] .'<br>';
            }
        $sqls[$i++] = "insert into movistar.cabecera (id_instrumento, de, columna, orden) values ('" .$idInstrumento ."', 'Teleoperador', '" .$this->columna($i) ."', '" .$i ."');";
        $cad .= '<br>Cantidad: ' .$i .'<br>';
        foreach( $sqls as $sql ) Aux::findBySql($sql)->one();


        
        $pregunta_cod = array();
        $pregunta_txt = array();
        $opcion = array();
        $opcion_next = array();
        $pregunta_tp = array();

        $cad .= '<br>Cantidad: '.count($vct).'<br>';
        foreach( $vct as $k=>$v ){
            $pregunta_cod[] = $k;
            //            $pregunta_txt[] = $v['_0'];
            if( is_array($v) && count($v) > 1 ){
                $aux = explode('::', $v['_0']);
                if( count($aux) > 1 ) $link = "'" .$aux[1] ."'";
                else $link = 'null';
                $sql = "insert into movistar.entrada (id_instrumento, id_pregunta_tp, de, codigo, ir_a) values ('" .$idInstrumento ."','4','".$aux[0]."','".$k."'," .$link .");";
                $cad .= $sql .'<br>';
                Aux::findBySql($sql)->one();
                $sql = "select id from movistar.entrada where id_instrumento='" .$idInstrumento ."' and id_pregunta_tp='4' and de='".$aux[0]."' and codigo='".$k."' order by id DESC limit 1;";
                $e = Aux::findBySql($sql)->one();
                
                $cad .= '<br><br><br><br><br>' .count($v) ;
                foreach($v as $kk => $vv){
                    if( $kk != '_0' ){
                        $cad .= $kk.' = '.$vv.'<br>' ;
                        $aux = explode('::', $vv);
                        if( count($aux) > 1 ) $link = "'" .$aux[1] ."'";
                        else $link = 'null';
                        $sql = "insert into movistar.entrada_op (id_entrada, de, valor, ir_a) values ('" .$e->id ."','" .$aux[0] ."','" .$kk ."'," .$link .");";
                        Aux::findBySql($sql)->one();
                        $cad .= $sql .'<br>';
                    }
                }
            }else if( is_array($v) &&  count($v) == 1 ){
                $aux = explode('::', $v['_0']);
                if( count($aux) > 1 ) $link = "'" .$aux[1] ."'";
                else $link = 'null';
                $sql = "insert into movistar.entrada (id_instrumento, id_pregunta_tp, de, codigo, ir_a) values ('" .$idInstrumento ."','2','".$aux[0]."','".$k."'," .$link .");";
                Aux::findBySql($sql)->one();
                $cad .= $sql .'<br>';
            }else{
                $aux = explode('::', $v);
                if( count($aux) > 1 ) $link = "'" .$aux[1] ."'";
                else $link = 'null';
                $sql = "insert into movistar.entrada (id_instrumento, id_pregunta_tp, de, codigo, ir_a) values ('" .$idInstrumento ."','1','" .$aux[0] ."','" .$k ."'," .$link .");";
                Aux::findBySql($sql)->one();
                $cad .= $sql .'<br>';
            }
        }
        //   id_entrada,  de , valor ,orden
        echo $cad;
    } // eof #######################################################

    
} // class
