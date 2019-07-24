<?php
namespace app\controllers\crm;
use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use app\models\xDbManager;
use app\models\LoginForm;
use app\models\Proyecto;
use app\models\Usuario;
use app\models\Perfil;
use app\models\crm\Encuesta;
use app\models\crm\Entrada;
use app\models\crm\Opcion;



use app\models\xMenu;
use app\models\xProyecto;
use app\models\xUsuario;
use app\models\xPerfil;
use app\models\crm\xCabecera;

use app\models\crm\xInstrumento;
use app\models\crm\xEntrada;
use app\models\crm\xOpcion;
use app\models\crm\xInstrumentoCabecera;



use app\models\Aux;
use app\components\util\JQGrid;
use app\components\util\Menu;
use app\components\util\JSon;
use app\components\util\Formulario;
use app\components\EncuestaProspecto1;
use app\components\EncuestaEntrada1;

use app\models\test\cProspecto;


use app\models\UploadForm;
use yii\web\UploadedFile;


use yii\widgets\ActiveForm;

use app\vendor\PHPExcel\PHPExcel\IOFactory;

use yii\swiftmailer\Mailer;


/**
 * Clase abstracta controller, de donde heredean los controller de los proyectos
 */
abstract class CrmController extends Controller{    


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

    
    
    /*
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
     * Proecsos que se ejecutan antes que el controller carge
     */
    function init(){     
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
     * Determina si tiene acceso a soporte
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
     * Detremina que tipo de rol posee
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
     * Crea el nombre de la columna
     */    
    public function columna($i){
        $cad = 'c' .$this->set0( $i, '2' );
        return $cad;
    } // eof #######################################################




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
    } // eof #######################################################







    /**
     * Proceso salir del sistema
     */  
    public function actionLogout(){
        Yii::$app->user->logout();
        return $this->redirect(['site/login']);
    } // eof #######################################################



        
        
} // class
