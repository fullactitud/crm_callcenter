<?php
namespace app\controllers\soporte;

use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

use app\models\xDbManager;
use app\models\LoginForm;



use app\components\crm\Mensaje;
use app\components\crm\Reporte;
use app\components\crm\Grafico;
use app\components\crm\Formato;
use app\components\crm\Menu;
use app\components\crm\Ayuda;


use app\models\Aux;

use app\models\AuthAssignment;


use app\controllers\ctrlFunction;

/**
 * Clase controler para manejar Gerencia
 */
class GerenciaController extends Controller{

    
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
     * Accioón por defecto de Gerencia
     */
    public function actionDf(){
        $cad = '';
        $cad .= '<div class="titulos"> Reporte General </div>';
        $cad .= Mensaje::mostrar();
        $cad .= Ayuda::toHtml('reportegeneral');


        
        $cad .= Reporte::resumenes();


        
        return $this->render('@views/crm/txt',array('txt'=>$cad));
    } // eof #######################################################

                
} // class

