<?php
namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use app\models\xDbManager;

use app\models\LoginForm;
use app\components\crm\Usuario;
use app\models\Proyecto;
use app\models\xUsuario;
use app\models\Aux;
use app\models\AuthAssignment;


/**
 * Controller por defecto
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
     * Proceso que se ejecuta antes de cargar el controller
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
     * Accción por defecto
     */
    public function actionIndex(){
        $id_usuario = Usuario::id(); // \Yii::$app->user->identity->id;
        $rol = null;
        $vector[] = 'admin';
        $vector[] = 'soporte';
        $vector[] = 'supervisor';
        $vector[] = 'tlo';
        $vector[] = 'activador';    
        $vector[] = 'cliente';
        $i = 0;
        $perfiles = Aux::findBySql("select item_name from public.auth_assignment where user_id ='" .(int)$id_usuario ."';")->all();
       
        $rol = $this->es($perfiles);
            
        switch( $rol ){
        case 'admin':
        case 'soporte':
            $this->redirect( Yii::$app->params['baseUrl'] .'index.php?r=soporte/carga/index');
            break;
        case 'supervisor':
            $this->redirect(Yii::$app->params['baseUrl'] .'index.php?r=soporte/tlo/supervisor&', 302);
            break;
        case 'activador':
            $this->redirect(Yii::$app->params['baseUrl'] .'index.php?r=soporte/tlo/activador&', 302);
            break;
        case 'cliente':
            $this->redirect(Yii::$app->params['baseUrl'] .'index.php?r=soporte/tlo/cliente&', 302);
            break;
        case 'tlo':
            $this->redirect(Yii::$app->params['baseUrl'] .'index.php?r=soporte/tlo/tlo&', 302);
            break;
        default:
            $this->redirect(Yii::$app->params['baseUrl'] .'index.php?r=df/logout&', 302);
            break;
        }
    } // eof
    
    
    
    /**
     * Establece el rol
     */
    function es( $vct ){
        $tp = 'guest';
        if( count($vct) > 0 )
            foreach( $vct as $reg ){
                if( $reg->item_name == 'admin' ){
                    $tp = 'admin';
                    break;
                }
                if( $tp != 'admin' ){
                    if( $reg->item_name == 'soporte' ){
                        $tp = 'soporte';
                        break;
                    }
                }
                if( $tp != 'admin' && $tp != 'soporte'  ){
                    if( $reg->item_name == 'supervidor' ){
                        $tp = 'supervisor';
                        break;
                    }
                }
                if( $tp != 'admin' && $tp != 'soporte' && $tp != 'supervisor' ){
                    if( $reg->item_name == 'tlo' ){
                        $tp = 'tlo';
                        break;
                    }
                }

                if( $tp != 'admin' && $tp != 'soporte' && $tp != 'supervisor' && $tp != 'tlo' ){
                    if( $reg->item_name == 'cliente' ){
                        $tp = 'cliente';
                        break;
                    }
                }
                
            }
        return $tp;
    } // eof #######################################################

  


    /**
     * Cuelga todas las llamadas antes de salir de la aplicacion
     */
    public static function colgar(){
        $id_usuario = \Yii::$app->user->identity->id;
        $objs0 = Aux::findBySql("select * from a.proyecto where st = '1';")->all();
        foreach( $objs0 as $proyecto ){
            $objs2 = Aux::findBySql("select id_prospecto from " .$proyecto->codigo .".llamada where st != '5' and st != '6' and id_usuario='" .$id_usuario ."' ;")->all();
            foreach( $objs2 as $llamada ){
                Aux::findBySql("update " .$proyecto->codigo .".prospecto set id_tipificacion='3', st='2' where id='" .$llamada->id_prospecto ."' ;")->one();
            }
            Aux::findBySql("update " .$proyecto->codigo .".llamada set id_tipificacion='3' where st != '5' and st != '6' and id_usuario='" .$id_usuario ."' ;")->one();
            Aux::findBySql("delete from " .$proyecto->codigo .".usuario_prospecto where id_usuario='" .$id_usuario ."' ;")->one();
        }
    } // eof
    

    

    /**
     * Logout
     *
     * @return string
     */
    public function actionLogout(){
        self::colgar();
        Yii::$app->user->logout();
        return $this->redirect(['site/login']);
    } // eof #######################################################

    
} // class
