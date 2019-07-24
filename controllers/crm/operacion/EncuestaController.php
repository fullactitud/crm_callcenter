<?php
namespace app\controllers\crm\operacion;


use Yii;
use yii\web\Controller;
use app\controllers\crm\CrmController;

use yii\filters\AccessControl;
use yii\filters\VerbFilter;



use app\components\crm\Request;
use app\components\crm\Formato;
use app\components\crm\Cadena;
use app\components\crm\Reporte;
use app\components\crm\Encuesta;
use app\components\crm\Ayuda;
use app\components\crm\Usuario;
use app\components\crm\Luego;



use app\models\Aux;
use app\models\test\cProyecto;
use app\models\crm\Instrumento;
use app\models\crm\xInstrumento;
use app\models\AuthAssignment;



/**
 * Clase que establece las funciones de Teleoperación
 */
class EncuestaController extends CrmController{


    public $schema = null;
    public $codInstrumento = null;
    
    public $columna_movil = null;
    public $columna_fecha_ref = null;
    public $columna_nombre = null;
    
    public $id_instrumento = null;
    public $id_cuota = null;
    public $id_llamada = null;
    public $prospecto = null;
    public $orden = null;
    public $codigo = null;
   


    // configuracion     
    public $siguiente = 0; // 0:rand, 1:orden por id, 2:orden por telf, 3: orden por documento
    public $desplegar = 0; // 0: agregando uno a uno, 1: todos juntos, 3: por pestañas; 4: uno a uno
    public $back = 0; // 0: no permite regresar; 1: permite leer preguntas anteriores; 2: permite editar preguntas anteriores

    
   
    public $cab_dominio = 'ID ZONA';
    public $cab_telf = 'MOVIL';
    public $cab_fecha_ref = 'Fecha de Atención';
    public $cab_nombre = 'NOMBRE';


    
    /**
     * Configuración de acceso
     */
    public function behaviors(){
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [                
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    } // eof #######################################################
    


    /**
     * Procesos que ee ejecuta antes que el controller sea cargado
     */
    function init(){
        parent::init();
       
        $this->orden = array();
        $this->codigo = array();
    } // eof #######################################################




    /**
     * Genera un Listado JQGrid
     */    
    public static function JQGrid( $cabecera, $registros, $titulo = '', $json, $subact='', $detail = NULL ){
        return JQGrid::listado( $this->schema(), $cabecera, $registros, $titulo, $json, $this->schema().$subact, $detail );
    } // eof #######################################################




    /**
     * Retorna el código del proyecto
     */
    public function schema(){
        if( isset($this->schema) ) return $this->schema;
        else return null;
    } // eof #######################################################





    /**
     * Establece el tipo de usario
     */
    function es( $vct ){
        foreach( $vct as $reg ) if( $reg->item_name == 'admin' ) return 'admin';
        foreach( $vct as $reg ) if( $reg->item_name == 'cliente' ) return 'cliente';
        foreach( $vct as $reg ) if( $reg->item_name == 'soporte' ) return 'soporte';
        foreach( $vct as $reg ) if( $reg->item_name == 'tlo' ) return 'tlo';
        foreach( $vct as $reg ) if( $reg->item_name == 'supervisor' ) return 'supervisor';
        return 'guest';
    } // eof #######################################################
    




    /**
     * Retorna una instancia de prospecto
     */
    public function prospecto2( $cod_proyecto = null , $id_instrumento = null , $p = null ){
        if( (int)$p != 0 )
            $sql = "select p.* from " .$cod_proyecto .".prospecto p where p.id = '" .$p ."' limit 1;";
        else{
           
            $sql = "select de,columna from " .$cod_proyecto .".cabecera c where st in (1,2,5) and c.id_instrumento='" .$id_instrumento ."' and c.de='ID ZONA' limit 1;";
            $cab = Aux::findBySql($sql)->one();

            
            $fecha = (Request::rq('x3fecha'))?Request::rq('x3fecha'):date('Y-m-d');
            $sql = "select p.* from " .$cod_proyecto .".prospecto p inner join " .$cod_proyecto .".dominio d on d.cod = p." .$cab->columna ." inner join " .$cod_proyecto .".cuota c on c.id_dominio = d.id where p.st in (1,2,1201) and p.id_instrumento='" .$id_instrumento ."' and c.fecha_encuesta='" .$fecha ."' order by de ASC limit 1;";
        }
        return Aux::findBySql($sql)->one();
    } // eof #######################################################





    /**
     * Actualiza la Barrida
     */
    public function actionBarridaup(){
        Encuesta::upBarrida();
    } // eof ##################################################



    /**
     * Muestra la Barrida actual
     */
    public function actionBarrida(){
        return $this->render('@views/crm/txt',array( 'txt' => Encuesta::verBarrida() ));
    } // eof ##################################################





    

    /**
     * Listado de Reportes disponibles para el instrumento
     */
    public function actionReportes(){
        $id_instrumento = (int)Request::rq('x3inst');
        if( $id_instrumento == 0 ) $id_instrumento = (int)Request::rq('id_instrumento');
        $cod_proyecto = Request::rq('x3proy');
        if( $cod_proyecto.'' == '' ) $cod_proyecto = Request::rq('cod_proyecto');
        $config = Encuesta::configuracion( $cod_proyecto, $id_instrumento );
        $cad = '<div class="titulos"> &nbsp; Reportes </div>';
        $cad .= Ayuda::toHtml('reportes_panel');
        return $this->render('@views/crm/txt',array( 'txt' => $cad .Reporte::index( $cod_proyecto, $id_instrumento, $config->cod_instrumento ) ));
    } // eof ##################################################



    /**
     * Inmplementaun enlace de regresar
     */  
    public function regresar(){
        if( $this->soporte() ){
            return '<div class="regresar"><a href="'
                              .Yii::$app->params['baseUrl'] .'index.php?r=soporte/carga/index'
                              .'"> &lt;- Regresar</a></div>';
        }else 
            return '<div class="regresar"><a href="'
                              .Yii::$app->params['baseUrl'] .'index.php?r=df/index'
                              .'"> &lt;- Regresar</a></div>';
    } // eof ##################################################
    



    /**
     * Presenta el formulario de filtro de reportes
     */
    public function actionFiltro1(){
        $cod_proyecto = Request::rq('cod_proyecto');
        $id_instrumento = Request::rq('x3inst');
        $reporte = Request::rq('x3rpt');
        $cod_instrumento = Request::rq('x3codInst');
        return $this->render('@views/crm/txt2',array(
            'regresar' => '',
            'txt' => Reporte::filtro1( $cod_proyecto, $cod_instrumento, $id_instrumento, $reporte, $accion ),
            'title' => '',
        ));
    } // eof ##################################################



    /**
     * Reporte tipo resumen
     */
    public function actionResumen(){
        return $this->render('@views/crm/txt2',array(
            'regresar' => '',
      
            'txt' => Reporte::resumen(),
            'title' => '',
        ));
    } // eof ##################################################
    


    /**
     * Reporte tipo data cruda
     */
    public function actionDc(){    
        $titulo = '';
        return $this->render('@views/rpt/index',array(
           
            'titulo' => $titulo,
            'regresar' => '',
            'txt' => Reporte::dc($this->schema, $this->codInstrumento, $this->idInstrumento),
            'ayuda' => '',
        ));        
    } // eof ##################################################   
    
    

    /**
     * Reporte de estadisticas
     */
    public function actionEstadisticas(){
        $titulo = '';
        $config = Encuesta::configuracion();
        return $this->render('@views/rpt/index',array(
           
            'titulo' => $titulo,
            'regresar' => '',
            'txt' => Reporte::estadisticas($this->schema, $this->codInstrumento, $this->idInstrumento, $this->columna_fecha_ref, $config->columna_cod, $config->columna_agente),
            'ayuda' => '',
        ));
    } // eof ##################################################  



    /**
     * Reporte de Supervisor
     */
    public function actionSupervisor(){
        $titulo = '';
        $config = Encuesta::configuracion();
        return $this->render('@views/rpt/index',array(
          
            'titulo' => $titulo,
            'regresar' => '',
            'txt' => Reporte::supervisor($this->schema, $this->codInstrumento, $this->idInstrumento, $this->columna_fecha_ref, $config->columna_cod, $config->columna_agente),
            'ayuda' => '',
        ));
    } // eof ##################################################  




    /**
     * Reporte de efectividad
     */
    public function actionEfectividad(){
        $titulo = '';
        $config = Encuesta::configuracion();
        return $this->render('@views/rpt/index',array(
          
            'titulo' => $titulo,
            'regresar' => '',
            'txt' => Reporte::efectividad($this->schema, $this->codInstrumento, $this->idInstrumento, $this->columna_fecha_ref, $config->columna_cod, $config->columna_agente),
            'ayuda' => '',
        ));
    } // eof ################################################## 




    /**
     * reporte de Detalle de efectividad
     */
    public function actionEfectividad_detalle(){
        $titulo = '';
        $config = Encuesta::configuracion();
        return $this->render('@views/rpt/index',array(
            'titulo' => $titulo,
            'regresar' => '',
            'txt' => Reporte::efectividadDetalle($config->cod_proyecto, $config->cod_instrumento, $config->id_instrumento, $config->columna_fecha_ref, $config->columna_cod, $config->columna_agente),
            'ayuda' => '',
        ));
    } // eof ################################################## 

    


    /**
     * Reporte de detalle de Supervisor
     */
    public function actionSupervisor_detalle(){
        $titulo = '';
        $config = Encuesta::configuracion();
        return $this->render('@views/rpt/index',array(
          
            'titulo' => $titulo,
            'regresar' => '',
            'txt' => Reporte::supervisorDetalle($this->schema, $this->codInstrumento, $this->idInstrumento, $this->columna_fecha_ref, $config->columna_cod, $config->columna_agente),
            'ayuda' => '',
        ));
    } // eof ################################################## 
    


    /**
     * Salva la encuesta
     */
    public function actionSalvar(){
        $this->redirect( Yii::$app->params['baseUrl'] .'index.php?r=' .Encuesta::salvar() );
    } // eof ##################################################




    /** 
     * Retorna el número de llamadas y llamadas efectivas del día
     */
    public function actionTop(){
        Yii::$app->layout = 'embebido';
        $idUsuario = Usuario::id();
        $proyecto = Request::rq('x3proy');
        $codInstrumento = Request::rq('x3codInstrumento');
        $fecha = date('Y-m-d');
        $sql = "select sum(llamadas) as llamadas, sum(efectivas) as efectivas from ( select count(l.*) as llamadas, 0 as efectivas from " .$this->schema .".llamada l where id_usuario='" .$idUsuario ."' and l.reg >= '" .$fecha ." 01:01:01'::timestamp and l.st!= '0' and l.st!= '6' union select 0 as llamadas, count(p.*) as efectivas from " .$this->schema .".prospecto p where p.id_tipificacion='15' and tlo='" .$idUsuario ."' and reg >= '" .$fecha ." 01:01:01'::timestamp) alia;";
        $obj = Aux::findBySql($sql)->one();
        $top = '<div class="ui-corner" style="position:relative; top:0; width: 100%;"> <span title="Proyecto">' .strtoupper($proyecto) .'</span> - <span title="Instrumento">' .$this->deInstrumento .'</span> &nbsp; <span style="color:#008888;" title="Efectivas">' .$obj->efectivas .'</span> &nbsp; <span style="color:#660088;" title="Llamadas">' .$obj->llamadas .'</span></div>';
        echo $top;
    } // eof ##################################################
    
    
 
           


    
    /**
     * Despliega el listado de dominios
     */    
    public function actionContactar1(){
        $msn = Request::rq('mns');
        $inst = Request::rq('x3inst');
        $idUser = Usuario::id();
        $cad = '';
       
        if( strlen($msn) > 0 )
            $cad .= '<div class="alert alert-info">' .$msn .'</div>';
       
        return $this->render('@views/crm/x3cnt',array( 'txt' => $cad .Encuesta::contactar1(0), ));
    } // eof #######################################################



    
    /**
     * Despliega el instrumento
     */    
    public function actionVer(){
        $msn = Request::rq('mns');
        $inst = Request::rq('x3inst');
        $idUser = Usuario::id();
        $cad = '';
       
        if( strlen($msn) > 0 )
            $cad .= '<div class="alert alert-info">' .$msn .'</div>';
       
        return $this->render('@views/crm/x3cnt',array( 'txt' => $cad .Encuesta::contactar1(1), ));
    } // eof #######################################################
   




    /**
     * Determina si puede ver lo de soporte
     */
    public function soporte(){
        $idUser = \Yii::$app->user->identity->id;
        $sql = "select item_name from public.auth_assignment where user_id='$idUser';";
        $aux = AuthAssignment::findBySql($sql)->all();
        $rolMayor = $this->es($aux);
        if( $rolMayor == 'admin' || $rolMayor == 'soporte' )
            return true;
        else
            return false;
    } // eof #######################################################


   
    
    
    /**
     * Reinicia un prospecto que previamente ha sido utilizado para pruebas
     */    
    public static function actionClearprospecto(){
        $idProspecto = Request::rq('id');        
        Encuesta::clearProspecto( $schema, $idProspecto );
    } // eof #######################################################
    



    /**
     * Reinicia los prospectos usados por el usuario "admin"
     * Todos los usuarios utilizados por "admin" se consideran pruebas
     */
    public static function actionClearadmin(){
        Encuesta::clearAdmin();
    } // eof #######################################################



  
    /**
     * Rellena un select con información ampliada de los dominios
     */
    public function actionSelectdominiosampliados(){
        Yii::$app->layout = 'embebido';
     
        echo Encuesta::selectDominiosAmpliados();
    } // eof #######################################################
    
    



    /**
     * Selecciona el prospecto siguiente
     */
    public function actionSelectsiguiente(){
        Yii::$app->layout = 'embebido';
        echo Encuesta::selectSiguiente();
    } // eof #######################################################

    




    /**
     * lista de dominios para select, NO es el usado en contacto1
     */
    public function actionSelectDominios_deprecate(){
        Yii::$app->layout = 'embebido';
        echo Encuesta::selectDominios( $this->schema, $this->idInstrumento );
    } // eof #######################################################
     


    


    /**
     * Despliega un contactable y sus acciones
     */
    public function actionGetcontactable(){
        $idCuota = (int)Request::rq('x3cuota');
        $fecha = date('Y-m-d');
        $vct[] = $this->cab_dominio;
        $vct[] = $this->cab_telf;
        $vct[] = $this->cab_fecha_ref;
        $vct[] = $this->cab_nombre;    
        echo Encuesta::getContactable( $idCuota, $fecha, $vct );
    } // eof #######################################################
    
    
   


    
    /**
     * Despliega la encuesta, con el prospecto
     * ej. index.php?r=proyecto/[schema]/[codgigo]/contacto1
     */
    public function actionContacto1(){
        $id_proyecto = Request::rq('x3proy');        
        $id_prospecto = Request::rq('x3prosp');
        $id_cuota = Request::rq('x3cuota');
        return $this->render('@views/crm/txt',array(
            'txt' => Encuesta::despliega( $id_prospecto, $id_cuota, $this->javascript() ),
        ));
    } // eof #######################################################
    
    

    /** 
     * Función abstracta
     * Se debe sobrescribir en la clase hija
     * Parametros:
     * 1:entrada actual, 2:entrada proxima, 3:tipo de entrada, 4:valor de la opcion
     * ( act, next, tp, valorOpcion )
     */
    public function javascript(){ return ''; }

   

    
    /**
     * Cancela una llamada
     */
    public function actionColgar(){
        Yii::$app->layout = 'embebido';
        $codProyecto = Request::rq('x3proy');
        $idInstrumento = Request::rq('x3inst');
        $idProspecto = Request::rq('x3prosp');
        $idLlamada = Request::rq('x3llamada');
        $idTipificacion = Request::rq('x3res');

        Encuesta::colgar( $codProyecto, $idInstrumento, $idProspecto, $idLlamada, $idTipificacion );

        // precarga otro prospecto
        $this->redirect( Yii::$app->params['baseUrl'] .'index.php?r=crm/operacion/encuesta/contactar1&x3proy=' .$codProyecto .'&x3inst=' .$idInstrumento .'&mns=LLAMADA CANCELADA'); 
    } // eof #######################################################

    
    



    /**
     * Formulario Llamar luego
     */
    public function actionLlamar_luego(){
        return $this->render('@views/crm/txt',array( 'txt' => Luego::df() ));
    } // eof 

    
    
    
    /**
     * Despliega las opciones para colgar una llamada
     */
    public function actionEsperar(){
        $cod_proyecto = Request::rq('x3proy');
        $id_instrumento = (int)Request::rq('x3inst');
        $id_cuota = (int)Request::rq('x3cuota');
        $id_prospecto = (int)Request::rq('x3prosp');
        $id_llamada = (int)Request::rq('x3llamada');
        $id_usuario = \Yii::$app->user->identity->id;
        
        $prospecto = $this->prospecto2( $cod_proyecto, $id_instrumento, $id_prospecto );
        if( $id_prospecto == 0 ) $id_prospecto = $prospecto->id;
        if( $id_llamada == null ){
            $sql = "select id from " .$cod_proyecto .".llamada where id_prospecto='" .$id_prospecto ."' and id_usuario='" .$id_usuario ."' and st='4';";
            $obj = Aux::findBySql($sql)->one();
            $id_llamada = $obj->id;
        }
        Yii::$app->layout = 'embebido';
        return $this->render('@views/crm/txt',array( 'txt' => Encuesta::esperar($id_cuota, $id_prospecto, $id_llamada) ));
    } // eof ##################################################





    /**
     * lista de dominios, para select, NO usado en contacto1
     */
    public function actionSelectdominio(){
        Yii::$app->layout = 'embebido';
     
        echo Encuesta::selectDominio();
    } // eof #######################################################
    


    
} // class
