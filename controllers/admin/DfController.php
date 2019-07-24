<?php
namespace app\controllers\admin;

use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use app\models\xDbManager;

use app\models\LoginForm;
use app\models\Aux;
use app\controllers\util\Cadena;
use app\controllers\util\JSon;
use app\controllers\util\Formulario;


/**
 * Clase controller para admin
 */
class DfController extends Controller{
    
    /**
     * @inheritdoc
     */
    public function behaviors(){
        return [
            'access' => [
                'class' => AccessControl::className(),
               
                'rules' => [
                                   
                    [
                        'allow' => false, 
                        'roles'=>['?'], 
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
     * Procesos que se ejecutan antes de que el controller carge
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
            

    
} // class
